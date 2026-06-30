<?php

namespace App\Http\Controllers\PhysicalPossession;

use App\Http\Controllers\Controller;
use App\Models\PhysicalPossessionApplication;
use App\Models\ApplicationStatusLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PhysicalPossessionWorkflowController extends Controller
{
    /**
     * Display a list of eligible applicants who have paid >= 40,000.
     */
    public function officerEligibilityList(Request $request)
    {
        $officer = Auth::user();

        $this->ensureDistrictApplications($officer);

        $query = DB::table('property_auction_detail as pad')
            ->join('property_private_purchasers as ppp', 'pad.PurchaserID', '=', 'ppp.PrivatePurchaserId')
            ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('property_registration as pr', 'pad.AssetId', '=', 'pr.AssetId')
            ->leftJoin('physical_possession_applications as ppa', function ($join) {
                $join->on('pad.PurchaserID', '=', 'ppa.private_purchaser_id')
                     ->on('pad.AssetId', '=', 'ppa.asset_id');
            })
            ->where('pad.IsActive', 1)
            ->where('pad.IsDeleted', 0);

        // Filter by officer district
        if ($officer->district_id) {
            $query->where('ppp.DistrictId', $officer->district_id);
        } elseif ($officer->district_name) {
            $query->where('d.DistrictName', 'like', '%' . $officer->district_name . '%');
        }

        $query->select([
            'pad.PropertyAuctionId',
            'pad.AssetId',
            'pad.PurchaserID',
            'pad.FlatCost',
            'pad.ReceivedAmount',
            'pad.BalanceAmount',
            'ppp.PrivatePurchaserName',
            'ppp.PurchaserFatherName',
            'ppp.Address',
            'ppp.MobileNo',
            'ppp.ApplicationNo',
            'ppp.PPPId',
            'ppp.MemberID',
            'ppp.DistrictId',
            'd.DistrictName',
            'pr.AssetName',
            'pr.AssetSize',
            'pr.Unit',
            'ppa.id as application_id',
            'ppa.secure_id as application_secure_id',
            'ppa.physical_possession_status',
        ])
        ->selectRaw("
            COALESCE(pad.ReceivedAmount, 0) + COALESCE(
                (SELECT SUM(total_paid_amount) FROM cash_receipt_details WHERE asset_number = pad.AssetId AND IsDeleted = 0 AND IsActive = 1),
                (SELECT SUM(Payment) FROM ledger WHERE AssetId = pad.AssetId AND Is_Deleted = 0 AND Is_Active = 1),
                0
            ) as total_paid
        ")
        ->having('total_paid', '>=', 40000);

        // Search filter
        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('ppp.PrivatePurchaserName', 'like', "%{$search}%")
                  ->orWhere('ppp.MobileNo', 'like', "%{$search}%")
                  ->orWhere('ppp.PPPId', 'like', "%{$search}%")
                  ->orWhere('ppp.ApplicationNo', 'like', "%{$search}%");
            });
        }

        $purchasers = $query->paginate(25)->withQueryString();

        return view('physical-possession.workflow.officer-eligibility', compact('purchasers', 'search', 'officer'));
    }

    /**
     * Get count of active bookings for the officer's district on a selected date
     */
    public function getSlotCapacityCheck(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'exclude_id' => 'nullable|integer',
        ]);

        $date = $request->input('date');
        $excludeId = $request->input('exclude_id', 0);
        $officer = Auth::user();
        
        $districtId = $officer->district_id;
        if (!$districtId) {
            return response()->json(['success' => false, 'message' => 'Officer district not defined.']);
        }

        // Return count of bookings for each hour (9 to 16) on this date
        $counts = [];
        for ($hour = 9; $hour <= 16; $hour++) {
            $slotStart = Carbon::parse($date . ' ' . sprintf('%02d:00:00', $hour));
            $slotEnd = $slotStart->copy()->addHour();
            
            $count = DB::table('physical_possession_applications')
                ->where('district_id', $districtId)
                ->where('id', '!=', $excludeId)
                ->where(function($q) use ($slotStart, $slotEnd) {
                    $q->where(function($sub) use ($slotStart, $slotEnd) {
                        $sub->whereIn('physical_possession_status', ['Visit Scheduled', 'Rejected'])
                            ->where(function($three) use ($slotStart, $slotEnd) {
                                $three->whereBetween('visit_slot_1', [$slotStart, $slotEnd])
                                      ->orWhereBetween('visit_slot_2', [$slotStart, $slotEnd])
                                      ->orWhereBetween('visit_slot_3', [$slotStart, $slotEnd]);
                            });
                    })
                    ->orWhere(function($sub) use ($slotStart, $slotEnd) {
                        $sub->whereIn('physical_possession_status', ['Physical Possession Submitted', 'Verified'])
                            ->whereBetween('citizen_visit_date', [$slotStart, $slotEnd]);
                    });
                })
                ->count();
            
            $counts[$hour] = $count;
        }

        return response()->json([
            'success' => true,
            'counts' => $counts
        ]);
    }

    /**
     * Show scheduling form for a purchaser.
     */
    public function officerScheduleForm(PhysicalPossessionApplication $application)
    {
        if (in_array($application->physical_possession_status, ['Slot Selected', 'Verified', 'Rejected'])) {
            return redirect()->route('pp.officer.possession-applications')->with('error', 'Cannot schedule or update schedule after slot is confirmed by citizen or verified.');
        }

        $officer = Auth::user();

        // Fetch comprehensive property and allotment details
        $property = DB::table('property_registration as pr')
            ->leftJoin('cities as c', 'pr.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'pr.SectorId', '=', 's.SectorId')
            ->leftJoin('districts as d', 'pr.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('property_auction_detail as pad', 'pr.AssetId', '=', 'pad.AssetId')
            ->leftJoin('property_private_purchasers as ppp', 'pad.PurchaserID', '=', 'ppp.PrivatePurchaserId')
            ->where('pr.AssetId', $application->asset_id)
            ->select([
                'pr.AssetId',
                'pr.AssetName',
                'pr.AssetSize',
                'pr.Unit',
                'c.CityName',
                's.SectorName',
                'd.DistrictName',
                'pad.FlatCost',
                'pad.ReceivedAmount',
                'pad.BalanceAmount',
                'ppp.PrivatePurchaserName',
                'ppp.PurchaserFatherName',
                'ppp.MobileNo',
                'ppp.Address',
                'ppp.ApplicationNo as purchaser_app_no',
                'ppp.PPPId as purchaser_ppp_id',
                'ppp.MemberID as purchaser_member_id',
                'ppp.CasteCategoryName as purchaser_category',
                'ppp.MaritalStatus as purchaser_marital_status',
                'ppp.CreateDate as purchaser_reg_date',
            ])
            ->first();

        if (!$property) {
            return redirect()->route('pp.officer.eligibility-list')->with('error', 'Applicant and property details not found.');
        }

        $initialDeposit = 0.0;
        $installmentPaid = 0.0;
        if ($property) {
            $initialDeposit = (float) ($property->ReceivedAmount ?? 0);
            $assetId = $property->AssetId;
            if ($assetId) {
                $ledgerPaid = (float) DB::table('ledger')
                    ->where('AssetId', $assetId)
                    ->where('Is_Deleted', 0)
                    ->where('Is_Active', 1)
                    ->sum('Payment');

                $cashReceiptPaid = (float) DB::table('cash_receipt_details')
                    ->where('asset_number', $assetId)
                    ->where('IsDeleted', 0)
                    ->where('IsActive', 1)
                    ->sum('total_paid_amount');

                $installmentPaid = $ledgerPaid > 0 ? $ledgerPaid : $cashReceiptPaid;
            }
        }
        $totalReceived = $initialDeposit + $installmentPaid;
        $balanceAmount = $property ? (float) ($property->FlatCost ?? 0) - $totalReceived : 0.0;

        return view('physical-possession.workflow.officer-schedule', compact(
            'property',
            'application',
            'officer',
            'initialDeposit',
            'installmentPaid',
            'totalReceived',
            'balanceAmount'
        ));
    }

    /**
     * Save meeting schedule and mark as "Visit Scheduled".
     */
    public function officerScheduleSave(Request $request, PhysicalPossessionApplication $application)
    {
        if (in_array($application->physical_possession_status, ['Slot Selected', 'Verified', 'Rejected'])) {
            return redirect()->route('pp.officer.possession-applications')->with('error', 'Cannot schedule or update schedule after slot is confirmed by citizen or verified.');
        }

        $request->validate([
            'slot_date_1' => 'required|date|after_or_equal:today',
            'slot_time_1' => 'required|string',
            'slot_date_2' => 'required|date|after_or_equal:today',
            'slot_time_2' => 'required|string',
            'slot_date_3' => 'required|date|after_or_equal:today',
            'slot_time_3' => 'required|string',
            'visit_instructions' => 'nullable|string|max:1000',
        ]);

        $purchaserId = $application->private_purchaser_id;

        $purchaser = DB::table('property_private_purchasers as ppp')
            ->join('property_auction_detail as pad', 'ppp.PrivatePurchaserId', '=', 'pad.PurchaserID')
            ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
            ->where('ppp.PrivatePurchaserId', $purchaserId)
            ->select('ppp.*', 'pad.AssetId', 'pad.FlatCost', 'pad.ReceivedAmount', 'pad.BalanceAmount', 'd.DistrictName')
            ->first();

        if (!$purchaser) {
            return redirect()->route('pp.officer.eligibility-list')->with('error', 'Applicant details not found.');
        }

        $dateTime1 = Carbon::parse($request->slot_date_1 . ' ' . $request->slot_time_1);
        $dateTime2 = Carbon::parse($request->slot_date_2 . ' ' . $request->slot_time_2);
        $dateTime3 = Carbon::parse($request->slot_date_3 . ' ' . $request->slot_time_3);

        $now = now();
        $todayStr = $now->toDateString();
        $currentHour = $now->hour;

        foreach ([$dateTime1, $dateTime2, $dateTime3] as $idx => $dt) {
            $slotNum = $idx + 1;
            
            // 1. Ensure the date-time is not in the past
            if ($dt->isPast()) {
                return back()->withErrors([
                    "slot_time_{$slotNum}" => "Slot {$slotNum} time cannot be in the past."
                ])->withInput();
            }
            
            // 2. Or specifically check if it's today and the hour is less than or equal to current hour
            if ($dt->toDateString() === $todayStr && $dt->hour <= $currentHour) {
                return back()->withErrors([
                    "slot_time_{$slotNum}" => "Slot {$slotNum} time cannot be in the past."
                ])->withInput();
            }
            
            // 3. Ensure it's between 09:00 AM and 05:00 PM (hour 9 to 16)
            if ($dt->hour < 9 || $dt->hour > 16) {
                return back()->withErrors([
                    "slot_time_{$slotNum}" => "Slot {$slotNum} must be between 09:00 AM and 05:00 PM."
                ])->withInput();
            }
        }

        if ($dateTime1->equalTo($dateTime2) || $dateTime1->equalTo($dateTime3) || $dateTime2->equalTo($dateTime3)) {
            return back()->withErrors(['slot_date_1' => 'All three scheduled slots must be distinct dates and times.'])->withInput();
        }

        $excludeId = $application->id;
        $districtId = $purchaser->DistrictId;

        // Capacity slot check (max 10 people per 1-hour window per district)
        foreach ([$dateTime1, $dateTime2, $dateTime3] as $idx => $dt) {
            $slotStart = $dt->copy()->startOfHour();
            $slotEnd = $slotStart->copy()->addHour();

            $existingCount = DB::table('physical_possession_applications')
                ->where('district_id', $districtId)
                ->where('id', '!=', $excludeId)
                ->where(function($q) use ($slotStart, $slotEnd) {
                    $q->where(function($sub) use ($slotStart, $slotEnd) {
                        $sub->whereIn('physical_possession_status', ['Visit Scheduled', 'Rejected'])
                            ->where(function($three) use ($slotStart, $slotEnd) {
                                $three->whereBetween('visit_slot_1', [$slotStart, $slotEnd])
                                      ->orWhereBetween('visit_slot_2', [$slotStart, $slotEnd])
                                      ->orWhereBetween('visit_slot_3', [$slotStart, $slotEnd]);
                            });
                    })
                    ->orWhere(function($sub) use ($slotStart, $slotEnd) {
                        $sub->whereIn('physical_possession_status', ['Physical Possession Submitted', 'Verified'])
                            ->whereBetween('citizen_visit_date', [$slotStart, $slotEnd]);
                    });
                })
                ->count();

            if ($existingCount >= 10) {
                $slotNum = $idx + 1;
                return back()->withErrors([
                    "slot_time_{$slotNum}" => "Slot {$slotNum} (" . $dt->format('d M Y, h:i A') . ") has {$existingCount} visits scheduled. Maximum 10 visits allowed per hour in your district."
                ])->withInput();
            }
        }

        // Link/create citizen user account so they can log in via OTP
        $user = User::where('private_purchaser_id', $purchaserId)
            ->orWhere('mobile', $purchaser->MobileNo)
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $purchaser->PrivatePurchaserName,
                'mobile' => $purchaser->MobileNo,
                'role' => 'citizen',
                'private_purchaser_id' => $purchaserId,
            ]);
        } else {
            if (empty($user->private_purchaser_id)) {
                $user->private_purchaser_id = $purchaserId;
                $user->save();
            }
        }

        $oldStatus = $application->physical_possession_status;
        // Save application data
        $application->update([
            'user_id' => $user->id,
            'district_id' => $purchaser->DistrictId,
            'district_name' => $purchaser->DistrictName,
            'mobile' => $purchaser->MobileNo,
            'applicant_name' => $purchaser->PrivatePurchaserName,
            'father_name' => $purchaser->PurchaserFatherName,
            'address' => $purchaser->Address,
            'flat_cost' => $purchaser->FlatCost,
            'received_amount' => $purchaser->ReceivedAmount,
            'balance_amount' => $purchaser->BalanceAmount,
            
            'possession_date' => $request->slot_date_1,
            'meeting_slot' => $dateTime1->format('Y-m-d H:i:s') . ' | ' . $dateTime2->format('Y-m-d H:i:s') . ' | ' . $dateTime3->format('Y-m-d H:i:s'),
            
            'citizen_visit_date' => $dateTime1,
            'visit_slot_1' => $dateTime1,
            'visit_slot_2' => $dateTime2,
            'visit_slot_3' => $dateTime3,
            'visit_instructions' => $request->visit_instructions,
            
            'status' => 'pending',
            'physical_possession_status' => 'Visit Scheduled',
        ]);

        ApplicationStatusLog::create([
            'application_id' => $application->id,
            'asset_id' => $application->asset_id,
            'old_status' => $oldStatus,
            'new_status' => 'Visit Scheduled',
            'remarks' => 'Visit scheduled by Site Engineer. Offered slots: Slot 1: ' . $dateTime1->format('d M Y - h:i A') . ', Slot 2: ' . $dateTime2->format('d M Y - h:i A') . ', Slot 3: ' . $dateTime3->format('d M Y - h:i A'),
            'changed_by_type' => 'officer',
            'changed_by_id' => Auth::id(),
        ]);

        // Mock/Log Citizen notification
        Log::info("SMS Notification: Physical Possession visit scheduled for applicant {$application->applicant_name} (Mobile: {$application->mobile}) with slots: {$dateTime1}, {$dateTime2}, {$dateTime3}. Status: Visit Scheduled.");

        return redirect()->route('pp.officer.eligibility-list')->with('success', 'Physical Possession visit has been successfully scheduled.');
    }



    /**
     * Citizen Submission form.
     */
    public function citizenSubmitForm(Request $request)
    {
        $user = Auth::user();

        $application = PhysicalPossessionApplication::where('user_id', $user->id)
            ->whereIn('physical_possession_status', ['Visit Scheduled', 'Rejected'])
            ->latest()
            ->first();

        if (!$application) {
            return redirect()->route('citizen.dashboard')->with('error', 'No active scheduled visit found for your application.');
        }

        return view('physical-possession.workflow.citizen-submit', compact('application', 'user'));
    }

    /**
     * Citizen Submission POST save.
     */
    public function citizenSubmit(Request $request)
    {
        $user = Auth::user();

        $application = PhysicalPossessionApplication::where('user_id', $user->id)
            ->whereIn('physical_possession_status', ['Visit Scheduled', 'Rejected'])
            ->latest()
            ->first();

        if (!$application) {
            return redirect()->route('citizen.dashboard')->with('error', 'No active scheduled visit found for your application.');
        }

        $request->validate([
            'selected_slot' => 'required|string',
        ], [
            'selected_slot.required' => 'Please select the scheduled meeting slot.',
        ]);

        $oldStatus = $application->physical_possession_status;
        $selectedSlot = $request->input('selected_slot');
        $dateTime = Carbon::parse($selectedSlot);

        $application->meeting_slot = $selectedSlot;
        $application->citizen_visit_date = $dateTime;
        $application->possession_date = $dateTime->toDateString();
        $application->physical_possession_status = 'Slot Selected';
        $application->save();

        ApplicationStatusLog::create([
            'application_id' => $application->id,
            'asset_id' => $application->asset_id,
            'old_status' => $oldStatus,
            'new_status' => 'Slot Selected',
            'remarks' => 'Visit slot selected by Citizen: ' . $dateTime->format('d M Y - h:i A'),
            'changed_by_type' => 'user',
            'changed_by_id' => $user->id,
        ]);

        return redirect()->route('citizen.dashboard')->with('success', 'Visit slot selected successfully. The Site Engineer will meet you at the site.');
    }

    /**
     * Display list of all physical possession applications.
     */
    public function officerApplications(Request $request)
    {
        $officer = Auth::user();

        $this->ensureDistrictApplications($officer);

        $query = PhysicalPossessionApplication::query()
            ->whereNotNull('physical_possession_status');

        if ($officer->district_id) {
            $query->where('district_id', $officer->district_id);
        } elseif ($officer->district_name) {
            $query->where('district_name', 'like', '%' . $officer->district_name . '%');
        }

        // Status filter
        $status = $request->input('status');
        if ($status) {
            if ($status === 'Physical Possession Submitted') {
                $query->whereIn('physical_possession_status', ['Slot Selected', 'Physical Possession Submitted']);
            } else {
                $query->where('physical_possession_status', $status);
            }
        }

        // Search filter
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('applicant_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('application_number', 'like', "%{$search}%");
            });
        }

        $applications = $query->latest()->paginate(25)->withQueryString();

        return view('physical-possession.workflow.officer-applications', compact('applications', 'search', 'officer'));
    }

    /**
     * Officer Verification detail view.
     */
    public function officerVerifyForm(PhysicalPossessionApplication $application)
    {
        $officer = Auth::user();

        // Check if officer is allowed to view (belongs to same district)
        if ($officer->district_id && $application->district_id !== $officer->district_id) {
            abort(403, 'Unauthorized access to application in another district.');
        }

        // Fetch comprehensive property and allotment details
        $property = DB::table('property_registration as pr')
            ->leftJoin('cities as c', 'pr.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'pr.SectorId', '=', 's.SectorId')
            ->leftJoin('districts as d', 'pr.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('property_auction_detail as pad', 'pr.AssetId', '=', 'pad.AssetId')
            ->leftJoin('property_private_purchasers as ppp', 'pad.PurchaserID', '=', 'ppp.PrivatePurchaserId')
            ->where('pr.AssetId', $application->asset_id)
            ->select([
                'pr.AssetId',
                'pr.AssetName',
                'pr.AssetSize',
                'pr.Unit',
                'c.CityName',
                's.SectorName',
                'd.DistrictName',
                'pad.FlatCost',
                'pad.ReceivedAmount',
                'pad.BalanceAmount',
                'ppp.ApplicationNo as purchaser_app_no',
                'ppp.PPPId as purchaser_ppp_id',
                'ppp.MemberID as purchaser_member_id',
                'ppp.CasteCategoryName as purchaser_category',
                'ppp.MaritalStatus as purchaser_marital_status',
                'ppp.CreateDate as purchaser_reg_date',
            ])
            ->first();

        $initialDeposit = 0.0;
        $installmentPaid = 0.0;
        if ($property) {
            $initialDeposit = (float) ($property->ReceivedAmount ?? 0);
            $assetId = $property->AssetId;
            if ($assetId) {
                $ledgerPaid = (float) DB::table('ledger')
                    ->where('AssetId', $assetId)
                    ->where('Is_Deleted', 0)
                    ->where('Is_Active', 1)
                    ->sum('Payment');

                $cashReceiptPaid = (float) DB::table('cash_receipt_details')
                    ->where('asset_number', $assetId)
                    ->where('IsDeleted', 0)
                    ->where('IsActive', 1)
                    ->sum('total_paid_amount');

                $installmentPaid = $ledgerPaid > 0 ? $ledgerPaid : $cashReceiptPaid;
            }
        }
        $totalReceived = $initialDeposit + $installmentPaid;
        $balanceAmount = $property ? (float) ($property->FlatCost ?? 0) - $totalReceived : 0.0;

        return view('physical-possession.workflow.officer-verify', compact(
            'application', 
            'officer', 
            'property',
            'initialDeposit',
            'installmentPaid',
            'totalReceived',
            'balanceAmount'
        ));
    }

    /**
     * Save verification decision (Approve/Reject).
     */
    public function officerVerifySave(Request $request, PhysicalPossessionApplication $application)
    {
        $officer = Auth::user();

        // Check district authorization
        if ($officer->district_id && $application->district_id !== $officer->district_id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:Verified,Rejected',
            'remarks' => 'required|string|max:1000',
            'latitude' => 'required_if:status,Verified|string',
            'longitude' => 'required_if:status,Verified|string',
            'plot_image' => 'required_if:status,Verified|image|mimes:jpeg,jpg,png|max:500',
            'possession_certificate' => 'required_if:status,Verified|file|mimes:pdf|max:500',
        ], [
            'remarks.required' => 'Please provide verification remarks/comments.',
            'latitude.required_if' => 'GPS Latitude is required. Please capture location.',
            'longitude.required_if' => 'GPS Longitude is required. Please capture location.',
            'plot_image.required_if' => 'Plot site photo is required for verification.',
            'plot_image.max' => 'The plot site photo must not exceed 500 KB.',
            'possession_certificate.required_if' => 'Physical Possession Application (Citizen Signed) is required for verification.',
            'possession_certificate.mimes' => 'The Physical Possession Application (Citizen Signed) must be a PDF file.',
            'possession_certificate.max' => 'The Physical Possession Application (Citizen Signed) must not exceed 500 KB.',
        ]);

        $status = $request->input('status');
        $oldStatus = $application->physical_possession_status;

        if ($status === 'Verified') {
            if ($request->hasFile('plot_image')) {
                $plotImage = $request->file('plot_image');
                $plotImageName = 'plot_' . $application->id . '_' . time() . '.' . $plotImage->getClientOriginalExtension();
                $plotImagePath = $plotImage->storeAs('possession_uploads/images', $plotImageName, 'public');
                $application->plot_image = $plotImagePath;
            }

            if ($request->hasFile('possession_certificate')) {
                $certificate = $request->file('possession_certificate');
                $certificateName = 'cert_' . $application->id . '_' . time() . '.' . $certificate->getClientOriginalExtension();
                $certificatePath = $certificate->storeAs('possession_uploads/certificates', $certificateName, 'public');
                $application->possession_certificate = $certificatePath;
            }

            $application->latitude = $request->latitude;
            $application->longitude = $request->longitude;
            $application->image_capture_datetime = now();
        }

        $application->physical_possession_status = $status;
        $application->status = $status === 'Verified' ? 'approved' : 'rejected';
        $application->verified_by = $officer->id;
        $application->verified_at = now();
        $application->remarks = $request->input('remarks');
        $application->save();

        ApplicationStatusLog::create([
            'application_id' => $application->id,
            'asset_id' => $application->asset_id,
            'old_status' => $oldStatus,
            'new_status' => $status,
            'remarks' => $status === 'Verified' 
                ? 'Physical possession verified and approved on site by Site Engineer.' 
                : 'Physical possession rejected by Site Engineer. Remarks: ' . $request->input('remarks'),
            'changed_by_type' => 'officer',
            'changed_by_id' => $officer->id,
        ]);

        $statusMessage = $status === 'Verified' ? 'verified' : 'rejected';
        return redirect()->route('pp.officer.possession-applications')->with('success', "Physical Possession application has been successfully {$statusMessage}.");
    }

    /**
     * Download the prefilled Possession Certificate PDF for an application.
     */
    public function officerDownloadCertificate(Request $request, PhysicalPossessionApplication $application)
    {
        $officer = Auth::user();

        // Check if officer is allowed to view (belongs to same district)
        if ($officer->district_id && $application->district_id !== $officer->district_id) {
            abort(403, 'Unauthorized access to application in another district.');
        }

        // Query property purchaser details to build the profile dictionary
        $purchaser = DB::table('property_private_purchasers as ppp')
            ->leftJoin('property_auction_detail as pad', 'ppp.PrivatePurchaserId', '=', 'pad.PurchaserID')
            ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'ppp.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'ppp.SectorId', '=', 's.SectorId')
            ->leftJoin('property_registration as pr', 'pad.AssetId', '=', 'pr.AssetId')
            ->where('ppp.PrivatePurchaserId', $application->private_purchaser_id)
            ->select([
                'ppp.*',
                'd.DistrictName',
                'c.CityName',
                's.SectorName',
                'pad.AssetId',
                'pr.AssetName',
            ])
            ->first();

        $name = $application->applicant_name;
        $district = $application->district_name;

        $plotNo = '—';
        if ($purchaser) {
            if (!empty($purchaser->Flat_Id)) {
                $plotNo = (string) $purchaser->Flat_Id;
            } elseif (!empty($purchaser->AssetId)) {
                $plotNo = (string) $purchaser->AssetId;
            } elseif (!empty($purchaser->AssetName)) {
                $plotNo = $purchaser->AssetName;
            }
        }

        $sectorName = $purchaser?->SectorName ?? '—';
        $cityName = $purchaser?->CityName ?? '—';
        $urbanEstate = strtoupper(trim($cityName !== '—' ? $cityName : ($district !== '—' ? $district : '—')));
        $officeLocation = $urbanEstate !== '—' ? $urbanEstate : strtoupper(trim($district));

        $profile = [
            'name' => $name,
            'mobile' => $application->mobile,
            'application_no' => $purchaser?->ApplicationNo ?? '',
            'plot_no' => $plotNo,
            'sector' => $sectorName,
            'urban_estate' => $urbanEstate,
            'office_location' => $officeLocation,
        ];

        $pdf = Pdf::loadView('physical-possession.user.pdf.prefilled-form', compact('profile'))
            ->setPaper('a4');

        if ($request->has('inline')) {
            return $pdf->stream('Possession-Certificate-Request-'.$application->application_number.'.pdf');
        }

        return $pdf->download('Possession-Certificate-Request-'.$application->application_number.'.pdf');
    }

    /**
     * Ensure all eligible district purchasers have physical possession application rows.
     */
    private function ensureDistrictApplications($officer)
    {
        $query = DB::table('property_auction_detail as pad')
            ->join('property_private_purchasers as ppp', 'pad.PurchaserID', '=', 'ppp.PrivatePurchaserId')
            ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('physical_possession_applications as ppa', function ($join) {
                $join->on('pad.PurchaserID', '=', 'ppa.private_purchaser_id')
                     ->on('pad.AssetId', '=', 'ppa.asset_id');
            })
            ->where('pad.IsActive', 1)
            ->where('pad.IsDeleted', 0)
            ->whereNull('ppa.id');

        if ($officer->district_id) {
            $query->where('ppp.DistrictId', $officer->district_id);
        } elseif ($officer->district_name) {
            $query->where('d.DistrictName', 'like', '%' . $officer->district_name . '%');
        }

        $query->select([
            'pad.PropertyAuctionId',
            'pad.AssetId',
            'pad.PurchaserID',
            'pad.FlatCost',
            'pad.ReceivedAmount',
            'pad.BalanceAmount',
            'ppp.PrivatePurchaserName',
            'ppp.PurchaserFatherName',
            'ppp.Address',
            'ppp.MobileNo',
            'ppp.ApplicationNo',
            'ppp.PPPId',
            'ppp.MemberID',
            'ppp.DistrictId',
            'd.DistrictName',
        ])
        ->selectRaw("
            COALESCE(pad.ReceivedAmount, 0) + COALESCE(
                (SELECT SUM(total_paid_amount) FROM cash_receipt_details WHERE asset_number = pad.AssetId AND IsDeleted = 0 AND IsActive = 1),
                (SELECT SUM(Payment) FROM ledger WHERE AssetId = pad.AssetId AND Is_Deleted = 0 AND Is_Active = 1),
                0
            ) as total_paid
        ")
        ->having('total_paid', '>=', 40000);

        $missing = $query->get();

        foreach ($missing as $p) {
            $user = User::where('private_purchaser_id', $p->PurchaserID)
                ->orWhere('mobile', $p->MobileNo)
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $p->PrivatePurchaserName,
                    'mobile' => $p->MobileNo,
                    'role' => 'citizen',
                    'private_purchaser_id' => $p->PurchaserID,
                ]);
            } else {
                if (empty($user->private_purchaser_id)) {
                    $user->private_purchaser_id = $p->PurchaserID;
                    $user->save();
                }
            }

            PhysicalPossessionApplication::create([
                'user_id' => $user->id,
                'private_purchaser_id' => $p->PurchaserID,
                'asset_id' => $p->AssetId,
                'application_number' => 'PP-' . now()->format('Y') . '-' . ($p->ApplicationNo ?? rand(1000, 9999)),
                'slip_id' => 'SLIP-' . uniqid(),
                'district_id' => $p->DistrictId,
                'district_name' => $p->DistrictName,
                'mobile' => $p->MobileNo,
                'applicant_name' => $p->PrivatePurchaserName,
                'father_name' => $p->PurchaserFatherName ?? '',
                'address' => $p->Address ?? '',
                'flat_cost' => $p->FlatCost,
                'received_amount' => $p->ReceivedAmount,
                'balance_amount' => $p->BalanceAmount,
                'physical_possession_status' => 'Eligible for Physical Possession',
                'status' => 'pending',
            ]);
        }
    }
}
