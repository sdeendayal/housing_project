<?php

namespace App\Http\Controllers\MMGAY\Bdo;

use App\Http\Controllers\Controller;
use App\Models\MmgayPossessionApplication;
use App\Models\MmgayPossessionStatusLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MMGAYBdoPossessionController extends Controller
{
    /**
     * Show BDO Login Form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->scheme === 'MMGAY' && $user->role === 'mmgay_bdo') {
                return redirect()->route('mmgay.bdo.dashboard');
            }
        }

        $captcha = rand(1000, 9999);
        session(['bdo_captcha' => $captcha]);

        return view('mmgay.bdo.login', compact('captcha'));
    }

    /**
     * Refresh BDO Captcha.
     */
    public function refreshCaptcha()
    {
        $captcha = rand(1000, 9999);
        session(['bdo_captcha' => $captcha]);
        return response()->json(['captcha' => $captcha]);
    }

    /**
     * Handle BDO Login Submit.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required',
        ]);

        if ($request->captcha != session('bdo_captcha')) {
            return back()
                ->withInput()
                ->with('error', 'Invalid captcha.');
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'scheme' => 'MMGAY',
            'role' => 'mmgay_bdo',
            'Is_Active' => '1',
            'Is_Deleted' => '0',
        ];

        if (!Auth::attempt($credentials)) {
            return back()
                ->withInput()
                ->with('error', 'Invalid BDO login credentials.');
        }

        $request->session()->regenerate();

        return redirect()
            ->route('mmgay.bdo.dashboard')
            ->with('success', 'Welcome BDO Officer.');
    }

    /**
     * Handle BDO Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mmgay.bdo.login')
            ->with('success', 'Logged out successfully.');
    }

    /**
     * Show BDO Dashboard.
     */
    public function dashboard(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        // 1. Total Eligible (All owners in BDO block who have paid)
        $totalEligibleQuery = DB::table('ownermaster')->where('IsPaid', 1);
        if ($blockMasterId) {
            $totalEligibleQuery->where('BlockId', $blockMasterId);
        }
        $totalEligibleCount = $totalEligibleQuery->count();

        // 2. Not Scheduled (No app or status is Eligible for Physical Possession)
        $notScheduledQuery = DB::table('ownermaster as o')
            ->leftJoin('mmgay_possession_applications as ppa', function ($join) {
                $join->on('o.OwnerId', '=', 'ppa.owner_id');
            })
            ->where('o.IsPaid', 1)
            ->where(function($q) {
                $q->whereNull('ppa.id')
                  ->orWhere('ppa.physical_possession_status', 'Eligible for Physical Possession');
            });
        if ($blockMasterId) {
            $notScheduledQuery->where('o.BlockId', $blockMasterId);
        }
        $notScheduledCount = $notScheduledQuery->count();

        // Base query for physical possession applications
        $ppaQuery = DB::table('mmgay_possession_applications');
        if ($blockMasterId) {
            $ppaQuery->where('block_id', $blockMasterId);
        }

        $stats = [
            'total_eligible' => $totalEligibleCount,
            'not_scheduled' => $notScheduledCount,
            'awaiting_citizen' => (clone $ppaQuery)->where('physical_possession_status', 'Visit Scheduled')->count(),
            'awaiting_coordinates' => (clone $ppaQuery)->where('physical_possession_status', 'Slot Selected')->count(),
            'awaiting_bdo_doc' => (clone $ppaQuery)->where('physical_possession_status', 'Site Verified')->count(),
            'verified' => (clone $ppaQuery)->where('physical_possession_status', 'Verified')->count(),
        ];

        $recentApplications = (clone $ppaQuery)->latest()->take(6)->get();

        $activeMenu = 'dashboard';
        return view('mmgay.bdo.dashboard', compact('bdo', 'stats', 'recentApplications', 'activeMenu'));
    }

    /**
     * Show BDO Eligibility List of Applicants.
     */
    public function eligibilityList(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        $query = DB::table('ownermaster as o')
            ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
            ->leftJoin('mmgay_possession_applications as ppa', function ($join) {
                $join->on('o.OwnerId', '=', 'ppa.owner_id');
            })
            ->where('o.IsPaid', 1);

        if (!$request->has('all')) {
            $query->where(function($q) {
                $q->whereNull('ppa.id')
                  ->orWhereIn('ppa.physical_possession_status', ['Eligible for Physical Possession', 'Visit Scheduled']);
            });
        }

        if ($blockMasterId) {
            $query->where('o.BlockId', $blockMasterId);
        }

        // Search filter
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('o.OwnerName', 'like', "%{$search}%")
                  ->orWhere('o.MobileNo', 'like', "%{$search}%")
                  ->orWhere('o.RegistrationNo', 'like', "%{$search}%");
            });
        }

        $applications = $query->select(
            'o.OwnerId as id',
            'o.secure_id',
            'o.OwnerName as applicant_name',
            'o.FatherHusbandName as father_name',
            'o.MobileNo as mobile',
            'd.DistrictName as district_name',
            'b.BlockName as block_name',
            'ppa.application_number',
            DB::raw("COALESCE(ppa.physical_possession_status, 'Eligible for Physical Possession') as physical_possession_status")
        )->paginate(25)->withQueryString();

        $activeMenu = 'schedule_pending';
        return view('mmgay.bdo.eligibility', compact('applications', 'search', 'bdo', 'activeMenu'));
    }

    /**
     * Show BDO Schedule Form - Dynamically creates the application row on-demand if missing.
     */
    public function scheduleForm($secureId)
    {
        $bdo = Auth::user();

        // 1. Fetch the owner details by secure_id
        $owner = DB::table('ownermaster as o')
            ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
            ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->where('o.secure_id', $secureId)
            ->select('o.*', 'b.BlockName', 'v.VillageName', 'd.DistrictName', 'f.FlatNo')
            ->first();

        if (!$owner) {
            abort(404, 'Beneficiary record not found.');
        }

        if ($bdo->block_id && $owner->BlockId !== $bdo->block_id) {
            abort(403, 'Unauthorized access to beneficiary in another block.');
        }

        if ($owner->IsPaid != 1) {
            abort(400, 'Physical Possession is only available for beneficiaries who have completed their payment.');
        }

        // 2. Find or dynamically create the physical possession application row
        $application = MmgayPossessionApplication::where('owner_id', $owner->OwnerId)
            ->first();

        if (!$application) {
            // Find or create villager user in users table
            $user = User::where('mobile', $owner->MobileNo)
                ->where('scheme', 'MMGAY')
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $owner->OwnerName,
                    'mobile' => $owner->MobileNo,
                    'role' => 'villager',
                    'scheme' => 'MMGAY',
                    'Is_Active' => '1',
                    'Is_Deleted' => '0',
                    'district_id' => $owner->DistrictId,
                    'district_name' => $owner->DistrictName,
                    'block_id' => $owner->BlockId,
                    'block_name' => $owner->BlockName,
                ]);

                // Seed role type for this new user
                $villagerRole = DB::table('roles')->where('slug', 'villager')->first();
                $villagerGroup = DB::table('role_groups')->where('slug', 'villager')->first();
                if ($villagerRole && $villagerGroup) {
                    DB::table('role_types')->insert([
                        'user_id' => $user->id,
                        'role_id' => $villagerRole->id,
                        'role_group_id' => $villagerGroup->id,
                        'Is_Active' => '1',
                        'Is_Deleted' => '0',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $application = MmgayPossessionApplication::create([
                'user_id' => $user->id,
                'owner_id' => $owner->OwnerId,
                'scheme' => 'MMGAY',
                'application_number' => 'PP-MMGAY-' . now()->format('Y') . '-' . ($owner->RegistrationNo ?? rand(1000, 9999)),
                'secure_id' => $owner->secure_id, // Match owner's unique random 32-character secure_id
                'slip_id' => 'SLIP-MMGAY-' . uniqid(),
                'district_id' => $owner->DistrictId,
                'district_name' => $owner->DistrictName,
                'block_id' => $owner->BlockId,
                'block_name' => $owner->BlockName,
                'mobile' => $owner->MobileNo,
                'applicant_name' => $owner->OwnerName,
                'father_name' => $owner->FatherHusbandName ?? '',
                'address' => $owner->OwnerAddress ?? '',
                'flat_cost' => 0,
                'received_amount' => 0,
                'balance_amount' => 0,
                'physical_possession_status' => 'Eligible for Physical Possession',
                'status' => 'pending',
            ]);
        }

        if (in_array($application->physical_possession_status, ['Slot Selected', 'Verified', 'Rejected'])) {
            return redirect()->route('mmgay.bdo.possession-applications')->with('error', 'Cannot schedule or update schedule after slot is confirmed by citizen or verified.');
        }

        $activeMenu = 'schedule_pending';
        return view('mmgay.bdo.schedule', compact('application', 'owner', 'bdo', 'secureId', 'activeMenu'));
    }

    /**
     * Save BDO Schedule visits.
     */
    public function scheduleSave(Request $request, $secureId)
    {
        $bdo = Auth::user();

        $application = MmgayPossessionApplication::where('secure_id', $secureId)
            ->firstOrFail();

        if ($bdo->block_id && $application->block_id !== $bdo->block_id) {
            abort(403, 'Unauthorized.');
        }

        if (in_array($application->physical_possession_status, ['Slot Selected', 'Verified', 'Rejected'])) {
            return redirect()->route('mmgay.bdo.possession-applications')->with('error', 'Cannot schedule or update schedule after slot is confirmed.');
        }

        $request->validate([
            'slot_date_1' => 'required|date|after:today',
            'slot_time_1' => 'required|string',
            'slot_date_2' => 'required|date|after:today',
            'slot_time_2' => 'required|string',
            'slot_date_3' => 'required|date|after:today',
            'slot_time_3' => 'required|string',
            'visit_instructions' => 'nullable|string|max:1000',
        ]);

        $dateTime1 = Carbon::parse($request->slot_date_1 . ' ' . $request->slot_time_1);
        $dateTime2 = Carbon::parse($request->slot_date_2 . ' ' . $request->slot_time_2);
        $dateTime3 = Carbon::parse($request->slot_date_3 . ' ' . $request->slot_time_3);

        $now = now();
        $todayStr = $now->toDateString();
        $currentHour = $now->hour;

        foreach ([$dateTime1, $dateTime2, $dateTime3] as $idx => $dt) {
            $slotNum = $idx + 1;
            
            if ($dt->isPast()) {
                return back()->withErrors(["slot_time_{$slotNum}" => "Slot {$slotNum} time cannot be in the past."])->withInput();
            }
            
            if ($dt->toDateString() === $todayStr && $dt->hour <= $currentHour) {
                return back()->withErrors(["slot_time_{$slotNum}" => "Slot {$slotNum} time cannot be in the past."])->withInput();
            }
            
            if ($dt->hour < 9 || $dt->hour > 16) {
                return back()->withErrors(["slot_time_{$slotNum}" => "Slot {$slotNum} must be between 09:00 AM and 05:00 PM."])->withInput();
            }
        }

        if (
            ($dateTime1->toDateString() === $dateTime2->toDateString() && $dateTime1->format('H:i') === $dateTime2->format('H:i')) ||
            ($dateTime1->toDateString() === $dateTime3->toDateString() && $dateTime1->format('H:i') === $dateTime3->format('H:i')) ||
            ($dateTime2->toDateString() === $dateTime3->toDateString() && $dateTime2->format('H:i') === $dateTime3->format('H:i'))
        ) {
            return back()->withErrors(['slot_date_1' => 'You cannot select the same date and time for more than one slot.'])->withInput();
        }

        // Capacity slot check (max 10 people per 1-hour window per block)
        $blockId = $application->block_id;
        foreach ([$dateTime1, $dateTime2, $dateTime3] as $idx => $dt) {
            $slotStart = $dt->copy()->startOfHour();
            $slotEnd = $slotStart->copy()->addHour();

            $existingCount = DB::table('mmgay_possession_applications')
                ->where('block_id', $blockId)
                ->where('id', '!=', $application->id)
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
                        $sub->whereIn('physical_possession_status', ['Slot Selected', 'Verified'])
                            ->whereBetween('citizen_visit_date', [$slotStart, $slotEnd]);
                    });
                })
                ->count();

            if ($existingCount >= 10) {
                $slotNum = $idx + 1;
                return back()->withErrors([
                    "slot_time_{$slotNum}" => "Slot {$slotNum} (" . $dt->format('d M Y, h:i A') . ") has {$existingCount} visits scheduled. Maximum 10 visits allowed per hour."
                ])->withInput();
            }
        }

        $oldStatus = $application->physical_possession_status;

        $application->update([
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

        MmgayPossessionStatusLog::create([
            'application_id' => $application->id,
            'asset_id' => $application->asset_id ?? 0,
            'old_status' => $oldStatus,
            'new_status' => 'Visit Scheduled',
            'remarks' => 'Visit scheduled by BDO. Offered slots: Slot 1: ' . $dateTime1->format('d M Y - h:i A') . ', Slot 2: ' . $dateTime2->format('d M Y - h:i A') . ', Slot 3: ' . $dateTime3->format('d M Y - h:i A'),
            'changed_by_type' => 'officer',
            'changed_by_id' => $bdo->id,
        ]);

        \App\Models\MmgayPossessionBdoStatus::create([
            'application_id' => $application->id,
            'application_number' => $application->application_number,
            'bdo_user_id' => $bdo->id,
            'bdo_name' => $bdo->name,
            'bdo_email' => $bdo->email,
            'bdo_mobile' => $bdo->mobile ?? null,
            'status' => 'Visit Scheduled',
            'remarks' => $request->visit_instructions ?? 'Visit scheduled by BDO.',
        ]);

        Log::info("MMGAY SMS Mock: Physical Possession slots scheduled for {$application->applicant_name} (Mobile: {$application->mobile}).");

        return redirect()->route('mmgay.bdo.eligibility-list')->with('success', 'Physical Possession visit has been successfully scheduled.');
    }

    /**
     * Show BDO Possession Applications.
     */
    public function applications(Request $request)
    {
        $bdo = Auth::user();
        $blockMasterId = $bdo->block_id;

        $query = MmgayPossessionApplication::query()
            ->where('physical_possession_status', '!=', 'Eligible for Physical Possession');

        if ($blockMasterId) {
            $query->where('block_id', $blockMasterId);
        }

        $status = $request->input('status');
        if ($status) {
            $query->where('physical_possession_status', $status);
        }

        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('applicant_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('application_number', 'like', "%{$search}%");
            });
        }
        $applications = $query->latest()->paginate(25)->withQueryString();

        $activeMenu = '';
        if ($status === 'Visit Scheduled') {
            $activeMenu = 'awaiting_citizen';
        } elseif ($status === 'Slot Selected') {
            $activeMenu = 'field_visit_pending';
        } elseif ($status === 'Site Verified') {
            $activeMenu = 'epossession_pending';
        } elseif ($status === 'Verified') {
            $activeMenu = 'verified';
        }

        return view('mmgay.bdo.applications', compact('applications', 'search', 'status', 'bdo', 'activeMenu'));
    }

    /**
     * Show BDO Verify Form.
     */
    public function verifyForm($secureId)
    {
        $bdo = Auth::user();

        $application = MmgayPossessionApplication::where('secure_id', $secureId)
            ->firstOrFail();

        if ($bdo->block_id && $application->block_id !== $bdo->block_id) {
            abort(403, 'Unauthorized.');
        }

        // Get owner details from ownermaster
        $owner = DB::table('ownermaster as o')
            ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
            ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->where('o.OwnerId', $application->owner_id)
            ->select('o.*', 'b.BlockName', 'v.VillageName', 'd.DistrictName', 'f.FlatNo')
            ->first();
        $logs = MmgayPossessionStatusLog::where('application_id', $application->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $activeMenu = '';
        if ($application->physical_possession_status === 'Visit Scheduled') {
            $activeMenu = 'awaiting_citizen';
        } elseif ($application->physical_possession_status === 'Slot Selected') {
            $activeMenu = 'field_visit_pending';
        } elseif ($application->physical_possession_status === 'Site Verified') {
            $activeMenu = 'epossession_pending';
        } elseif ($application->physical_possession_status === 'Verified') {
            $activeMenu = 'verified';
        }

        return view('mmgay.bdo.verify', compact('application', 'owner', 'bdo', 'logs', 'activeMenu'));
    }

    public function verifySave(Request $request, $secureId)
    {
        $bdo = Auth::user();

        $application = MmgayPossessionApplication::where('secure_id', $secureId)
            ->firstOrFail();

        if ($bdo->block_id && $application->block_id !== $bdo->block_id) {
            abort(403, 'Unauthorized.');
        }

        $currentStatus = $application->physical_possession_status;

        if ($currentStatus === 'Slot Selected') {
            // Stage 1: Coordinates and Plot image capture
            $request->validate([
                'remarks' => 'required|string|max:1000',
                'latitude' => 'required|string',
                'longitude' => 'required|string',
                'plot_image' => 'required|image|mimes:jpeg,jpg,png|max:500',
            ], [
                'remarks.required' => 'Please provide verification remarks/comments.',
                'latitude.required' => 'GPS Latitude is required. Please capture location.',
                'longitude.required' => 'GPS Longitude is required. Please capture location.',
                'plot_image.required' => 'Plot site photo with applicant is required.',
                'plot_image.max' => 'The plot site photo must not exceed 500 KB.',
            ]);

            if ($request->hasFile('plot_image')) {
                $plotImage = $request->file('plot_image');
                $plotImageName = 'plot_' . $application->id . '_' . time() . '.' . $plotImage->getClientOriginalExtension();
                $owner = DB::table('ownermaster')->where('OwnerId', $application->owner_id)->first();
                $memberId = $owner ? trim($owner->MemberId) : '';
                $memberFolder = $memberId ? preg_replace('/[^A-Za-z0-9_-]/', '', $memberId) : 'member_' . $application->id;
                $plotImagePath = $plotImage->storeAs($memberFolder . '/plot_images', $plotImageName, 'public');
                $application->plot_image = $plotImagePath;
            }

            $application->latitude = $request->latitude;
            $application->longitude = $request->longitude;
            $application->image_capture_datetime = now();
            $application->remarks = $request->input('remarks');
            $application->physical_possession_status = 'Site Verified';
            $application->save();

            MmgayPossessionStatusLog::create([
                'application_id' => $application->id,
                'asset_id' => $application->asset_id ?? 0,
                'old_status' => $currentStatus,
                'new_status' => 'Site Verified',
                'remarks' => 'Site verification details (GPS coordinates and site photo) submitted by BDO: ' . $request->remarks,
                'changed_by_type' => 'officer',
                'changed_by_id' => $bdo->id,
            ]);

            \App\Models\MmgayPossessionBdoStatus::create([
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'bdo_user_id' => $bdo->id,
                'bdo_name' => $bdo->name,
                'bdo_email' => $bdo->email,
                'bdo_mobile' => $bdo->mobile ?? null,
                'status' => 'Site Verified',
                'remarks' => $request->remarks,
            ]);

            return redirect()->route('mmgay.bdo.verify-form', $application->secure_id)->with('success', 'Site coordinates captured successfully. Now proceed to E-Possession step.');

        } else {
            // Stage 2: Signed report upload & BDO Official Document (PDF only)
            $request->validate([
                'site_engineer_file' => 'required|file|mimes:pdf|max:500',
                'possession_certificate' => 'required|file|mimes:pdf|max:500',
            ], [
                'site_engineer_file.required' => 'BDO signed report (PDF) is required.',
                'site_engineer_file.mimes' => 'The signed report must be a PDF file.',
                'site_engineer_file.max' => 'The signed report must not exceed 500 KB.',
                'possession_certificate.required' => 'BDO official verification document (PDF) is required.',
                'possession_certificate.mimes' => 'The official verification document must be a PDF file.',
                'possession_certificate.max' => 'The official verification document must not exceed 500 KB.',
            ]);

            if ($request->hasFile('site_engineer_file')) {
                $siteFile = $request->file('site_engineer_file');
                $siteFileName = 'bdo_verify_' . $application->id . '_' . time() . '.' . $siteFile->getClientOriginalExtension();
                $owner = DB::table('ownermaster')->where('OwnerId', $application->owner_id)->first();
                $memberId = $owner ? trim($owner->MemberId) : '';
                $memberFolder = $memberId ? preg_replace('/[^A-Za-z0-9_-]/', '', $memberId) : 'member_' . $application->id;
                $siteFilePath = $siteFile->storeAs($memberFolder . '/bdo_verify', $siteFileName, 'public');
                $application->site_engineer_file = $siteFilePath;
            }

            if ($request->hasFile('possession_certificate')) {
                $certFile = $request->file('possession_certificate');
                $certFileName = 'bdo_cert_' . $application->id . '_' . time() . '.' . $certFile->getClientOriginalExtension();
                $owner = DB::table('ownermaster')->where('OwnerId', $application->owner_id)->first();
                $memberId = $owner ? trim($owner->MemberId) : '';
                $memberFolder = $memberId ? preg_replace('/[^A-Za-z0-9_-]/', '', $memberId) : 'member_' . $application->id;
                $certFilePath = $certFile->storeAs($memberFolder . '/bdo_certs', $certFileName, 'public');
                $application->possession_certificate = $certFilePath;
            }

            $oldStatus = $application->physical_possession_status;

            $application->update([
                'physical_possession_status' => 'Verified',
                'status' => 'approved',
                'verified_by' => $bdo->id,
                'verified_at' => now(),
            ]);

            MmgayPossessionStatusLog::create([
                'application_id' => $application->id,
                'asset_id' => $application->asset_id ?? 0,
                'old_status' => $oldStatus,
                'new_status' => 'Verified',
                'remarks' => 'Physical Possession verified and completed by BDO Officer.',
                'changed_by_type' => 'officer',
                'changed_by_id' => $bdo->id,
            ]);

            \App\Models\MmgayPossessionBdoStatus::create([
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'bdo_user_id' => $bdo->id,
                'bdo_name' => $bdo->name,
                'bdo_email' => $bdo->email,
                'bdo_mobile' => $bdo->mobile ?? null,
                'status' => 'Verified',
                'remarks' => 'Physical Possession verified and completed by BDO Officer.',
            ]);

            return redirect()->route('mmgay.bdo.dashboard')->with('success', 'Application verified and approved successfully.');
        }
    }

    /**
     * Download Prefilled BDO/Citizen Possession Report PDF.
     */
    public function downloadCertificate(Request $request, $secureId)
    {
        $bdo = Auth::user();

        $application = MmgayPossessionApplication::where('secure_id', $secureId)
            ->firstOrFail();

        if ($bdo && $bdo->block_id && $application->block_id !== $bdo->block_id) {
            abort(403, 'Unauthorized.');
        }

        // Get owner details from ownermaster
        $owner = DB::table('ownermaster as o')
            ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
            ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->where('o.OwnerId', $application->owner_id)
            ->select('o.*', 'b.BlockName', 'v.VillageName', 'd.DistrictName', 'f.FlatNo')
            ->first();

        if (!$owner) {
            abort(404, 'Owner details not found.');
        }

        $bdoName = '—';
        if ($application->verified_by) {
            $verifyingUser = User::find($application->verified_by);
            if ($verifyingUser) {
                $bdoName = $verifyingUser->name;
            }
        }

        $pdfData = [
            'application' => $application,
            'owner' => $owner,
            'bdoName' => $bdoName,
            'verified_at' => $application->verified_at ? $application->verified_at->format('d M Y, h:i A') : '—',
        ];

        $pdf = Pdf::loadView('mmgay.bdo.pdf.prefilled-form', $pdfData)
            ->setPaper('a4');

        $safeAppNo = str_replace(['/', '\\'], '-', $application->application_number);

        if ($request->has('inline')) {
            return $pdf->stream('Possession-Report-MMGAY-'.$safeAppNo.'.pdf');
        }

        return $pdf->download('Possession-Report-MMGAY-'.$safeAppNo.'.pdf');
    }

    /**
     * AJAX slot capacity check.
     */
    public function getSlotCapacityCheck(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'exclude_id' => 'nullable|integer',
        ]);

        $date = $request->input('date');
        $excludeId = $request->input('exclude_id', 0);
        $bdo = Auth::user();
        
        $blockId = $bdo->block_id;
        if (!$blockId) {
            return response()->json(['success' => false, 'message' => 'BDO block not defined.']);
        }

        $counts = [];
        for ($hour = 9; $hour <= 16; $hour++) {
            $slotStart = Carbon::parse($date . ' ' . sprintf('%02d:00:00', $hour));
            $slotEnd = $slotStart->copy()->addHour();
            
            $count = DB::table('mmgay_possession_applications')
                ->where('block_id', $blockId)
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
                        $sub->whereIn('physical_possession_status', ['Slot Selected', 'Verified'])
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
     * Show Citizen Submission Form to select visit slot.
     */
    public function submitPossessionForm()
    {
        $user = Auth::user();

        $application = MmgayPossessionApplication::where('user_id', $user->id)
            ->whereIn('physical_possession_status', ['Visit Scheduled', 'Rejected'])
            ->latest()
            ->first();

        if (!$application) {
            return redirect()->route('mmgav.villager.dashboard')->with('error', 'No active scheduled visit found for your application.');
        }

        $owner = DB::table('ownermaster')->where('OwnerId', $application->owner_id)->first();
        if (!$owner || $owner->IsPaid != 1) {
            return redirect()->route('mmgav.villager.dashboard')->with('error', 'Physical Possession is only available after completing payment.');
        }

        $logs = MmgayPossessionStatusLog::where('application_id', $application->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mmgay.citizen.submit-possession', compact('application', 'user', 'logs'));
    }

    /**
     * Save Citizen Slot selection.
     */
    public function submitPossession(Request $request)
    {
        $user = Auth::user();

        $application = MmgayPossessionApplication::where('user_id', $user->id)
            ->whereIn('physical_possession_status', ['Visit Scheduled', 'Rejected'])
            ->latest()
            ->first();

        if (!$application) {
            return redirect()->route('mmgav.villager.dashboard')->with('error', 'No active scheduled visit found for your application.');
        }

        $owner = DB::table('ownermaster')->where('OwnerId', $application->owner_id)->first();
        if (!$owner || $owner->IsPaid != 1) {
            return redirect()->route('mmgav.villager.dashboard')->with('error', 'Physical Possession is only available after completing payment.');
        }

        $request->validate([
            'selected_slot' => 'required|string',
        ], [
            'selected_slot.required' => 'Please select the scheduled meeting slot.',
        ]);

        $oldStatus = $application->physical_possession_status;
        $selectedSlot = $request->input('selected_slot');
        $dateTime = Carbon::parse($selectedSlot);

        $application->update([
            'meeting_slot' => $selectedSlot,
            'citizen_visit_date' => $dateTime,
            'possession_date' => $dateTime->toDateString(),
            'physical_possession_status' => 'Slot Selected',
        ]);

        MmgayPossessionStatusLog::create([
            'application_id' => $application->id,
            'asset_id' => $application->asset_id ?? 0,
            'old_status' => $oldStatus,
            'new_status' => 'Slot Selected',
            'remarks' => 'Visit slot selected by Citizen: ' . $dateTime->format('d M Y - h:i A'),
            'changed_by_type' => 'user',
            'changed_by_id' => $user->id,
        ]);

        return redirect()->route('mmgav.villager.dashboard')->with('success', 'You have successfully selected the visit slot: ' . $dateTime->format('d M Y - h:i A') . ' and submitted your request.');
    }

    /**
     * Download Citizen/Villager Physical Possession Appointment Slip PDF.
     */
    public function downloadSlip(Request $request)
    {
        $user = Auth::user();

        $application = MmgayPossessionApplication::where('user_id', $user->id)
            ->whereIn('physical_possession_status', ['Slot Selected', 'Site Verified', 'Verified'])
            ->latest()
            ->firstOrFail();

        // Get owner details from ownermaster
        $owner = DB::table('ownermaster as o')
            ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
            ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->where('o.OwnerId', $application->owner_id)
            ->select('o.*', 'b.BlockName', 'v.VillageName', 'd.DistrictName', 'f.FlatNo')
            ->first();

        if (!$owner) {
            abort(404, 'Owner details not found.');
        }

        $pdfData = [
            'application' => $application,
            'owner' => $owner,
        ];

        $pdf = Pdf::loadView('mmgay.citizen.pdf.appointment-slip', $pdfData)
            ->setPaper('a4');

        $safeAppNo = str_replace(['/', '\\'], '-', $application->application_number);

        return $pdf->download('Possession-Slip-MMGAY-'.$safeAppNo.'.pdf');
    }
}
