<?php

namespace App\Http\Controllers\PhysicalPossession;

use App\Http\Controllers\Controller;
use App\Models\ApplicationStatusLog;
use App\Models\OfficerApplicationAction;
use App\Models\PhysicalPossessionApplication;
use App\Models\PhysicalPossessionDocument;
use App\Models\User;
use App\Services\PpApplicationStatusSmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PpOfficerController extends Controller
{
    public function __construct(
        private PpApplicationStatusSmsService $statusSmsService
    ) {}

    // Officer dashboard with stats
    public function dashboard(Request $request)
    {
        $officer = Auth::user();
        // $this->ensureDistrictApplications($officer);
        $query = $this->districtApplicationsQuery($officer);

        $phase = $request->input('phase');
        if ($phase) {
            $query->whereIn('physical_possession_applications.private_purchaser_id', function ($q) use ($phase) {
                $q->select('PrivatePurchaserId')
                  ->from('property_private_purchasers')
                  ->where('phase', $phase);
            });
        }

        // Fetch eligibleCount first because we use it in stats
        // Calculate count of eligible applicants who are not yet scheduled/initiated
        $receiptsQuery = DB::table('cash_receipt_details')
            ->select('asset_number')
            ->selectRaw('SUM(total_paid_amount) as receipt_total')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->groupBy('asset_number');

        $eligibleQuery = DB::table('property_auction_detail as pad')
            ->join('property_private_purchasers as ppp', 'pad.PurchaserID', '=', 'ppp.PrivatePurchaserId')
            ->join('mmsay_eligible_beneficiaries as meb', 'ppp.ApplicationNo', '=', 'meb.application_number')
            ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('physical_possession_applications as ppa', function ($join) {
                $join->on('pad.PurchaserID', '=', 'ppa.private_purchaser_id')
                     ->on('pad.AssetId', '=', 'ppa.asset_id');
            })
            ->leftJoinSub($receiptsQuery, 'crd', 'pad.AssetId', '=', 'crd.asset_number')
            ->where('pad.IsActive', 1)
            ->where('pad.IsDeleted', 0)
            ->where(function ($q) {
                $q->whereNull('ppa.id')
                  ->orWhere('ppa.physical_possession_status', 'Eligible for Physical Possession');
            })
            ->whereRaw("COALESCE(pad.ReceivedAmount, 0) + COALESCE(crd.receipt_total, 0) >= 60000");

        if ($officer->district_id) {
            $eligibleQuery->where('ppp.DistrictId', $officer->district_id);
        } elseif ($officer->district_name) {
            $eligibleQuery->where('d.DistrictName', 'like', '%' . $officer->district_name . '%');
        }

        if ($phase) {
            $eligibleQuery->where('ppp.phase', $phase);
        }

        $eligibleCount = $eligibleQuery->count();

        // Aggregate stats in a single database query
        $statsData = (clone $query)
            ->select('physical_possession_status', DB::raw('count(*) as cnt'))
            ->groupBy('physical_possession_status')
            ->pluck('cnt', 'physical_possession_status')
            ->toArray();

        $stats = [
            'awaiting_schedule' => $eligibleCount,
            'scheduled' => $statsData['Visit Scheduled'] ?? 0,
            'submitted' => ($statsData['Slot Selected'] ?? 0) + ($statsData['Physical Possession Submitted'] ?? 0),
            'site_verified' => $statsData['Site Verified'] ?? 0,
            'verified' => $statsData['Verified'] ?? 0,
            'rejected' => $statsData['Rejected'] ?? 0,
        ];
        $stats['total'] = 0; // Calculated dynamically from the database later

        // Aggregate last 7 days chart data in a single database query
        $sevenDaysAgo = now()->subDays(6)->startOfDay();
        $chartDataGrouped = (clone $query)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->select(DB::raw('DATE(created_at) as date_key'), DB::raw('count(*) as cnt'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('cnt', 'date_key')
            ->toArray();

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $dateKey = $date->toDateString();
            $chartData[] = $chartDataGrouped[$dateKey] ?? 0;
        }

        $recentApplications = (clone $query)->latest()->take(6)->get();
        $pendingApplications = (clone $query)->where('status', 'pending')->latest()->take(4)->get();
        $userCount = (clone $query)->distinct()->count('user_id');
        $decided = $stats['verified'] + $stats['rejected'];
        $approvalRate = $decided > 0
            ? (int) round(($stats['verified'] / $decided) * 100)
            : 0;
        $weekTotal = array_sum($chartData);

        // Fetch paginated eligible purchasers list for dashboard display (25 per page)
        $purchasersQuery = DB::table('property_auction_detail as pad')
            ->join('property_private_purchasers as ppp', 'pad.PurchaserID', '=', 'ppp.PrivatePurchaserId')
            ->join('mmsay_eligible_beneficiaries as meb', 'ppp.ApplicationNo', '=', 'meb.application_number')
            ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('property_registration as pr', 'pad.AssetId', '=', 'pr.AssetId')
            ->leftJoin('physical_possession_applications as ppa', function ($join) {
                $join->on('pad.PurchaserID', '=', 'ppa.private_purchaser_id')
                     ->on('pad.AssetId', '=', 'ppa.asset_id');
            })
            ->leftJoinSub($receiptsQuery, 'crd', 'pad.AssetId', '=', 'crd.asset_number')
            ->where('pad.IsActive', 1)
            ->where('pad.IsDeleted', 0);

        if ($officer->district_id) {
            $purchasersQuery->where('ppp.DistrictId', $officer->district_id);
        } elseif ($officer->district_name) {
            $purchasersQuery->where('d.DistrictName', 'like', '%' . $officer->district_name . '%');
        }

        if ($phase) {
            $purchasersQuery->where('ppp.phase', $phase);
        }

        $purchasersQuery->select([
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
            DB::raw("COALESCE(pad.ReceivedAmount, 0) + COALESCE(crd.receipt_total, 0) as total_paid")
        ])
        ->whereRaw("COALESCE(pad.ReceivedAmount, 0) + COALESCE(crd.receipt_total, 0) >= 60000");

        // Calculate total eligible count directly from the database table (with district/phase checks)
        $stats['total'] = (clone $purchasersQuery)->count();

        $search = $request->input('search');
        if ($search) {
            $purchasersQuery->where(function($q) use ($search) {
                $q->where('ppp.PrivatePurchaserName', 'like', "%{$search}%")
                  ->orWhere('ppp.MobileNo', 'like', "%{$search}%")
                  ->orWhere('ppp.PPPId', 'like', "%{$search}%")
                  ->orWhere('ppp.ApplicationNo', 'like', "%{$search}%");
            });
        }

        $purchasers = $purchasersQuery->paginate(25)->withQueryString();

        return view('physical-possession.officer.dashboard', compact(
            'officer',
            'stats',
            'chartLabels',
            'chartData',
            'recentApplications',
            'pendingApplications',
            'userCount',
            'approvalRate',
            'weekTotal',
            'eligibleCount',
            'purchasers',
            'search',
            'phase'
        ));
    }

    // Get count of approved bookings for the officer's district on a selected date
    public function getSlotCapacity(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->input('date');
        $officer = Auth::user();

        $query = PhysicalPossessionApplication::query()
            ->where('status', 'approved')
            ->whereNotNull('citizen_visit_date')
            ->whereDate('citizen_visit_date', $date);

        if ($officer->district_id) {
            $query->where('district_id', $officer->district_id);
        } elseif ($officer->district_name) {
            $query->where('district_name', 'like', '%'.$officer->district_name.'%');
        }

        $bookings = $query->selectRaw('HOUR(citizen_visit_date) as hour, COUNT(*) as count')
            ->groupByRaw('HOUR(citizen_visit_date)')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();

        return response()->json([
            'success' => true,
            'bookings' => $bookings,
        ]);
    }

    // Saari applications - sirf apne district ki
    public function applications(Request $request)
    {
        $officer = Auth::user();
        $status = $request->get('status', 'all');

        $query = $this->districtApplicationsQuery($officer);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $applications = $query->latest()->get();

        return view('physical-possession.officer.applications.index', compact('applications', 'status'));
    }

    // Pending applications
    public function pendingApplications()
    {
        return $this->applicationsByStatus('pending');
    }

    // Approved applications
    public function approvedApplications()
    {
        return $this->applicationsByStatus('approved');
    }

    // Rejected applications
    public function rejectedApplications()
    {
        return $this->applicationsByStatus('rejected');
    }

    private function applicationsByStatus(string $status)
    {
        $officer = Auth::user();
        $applications = $this->districtApplicationsQuery($officer)
            ->where('status', $status)
            ->latest()
            ->get();

        return view('physical-possession.officer.applications.index', [
            'applications' => $applications,
            'status' => $status,
        ]);
    }

    // Application detail dekhna
    public function showApplication(PhysicalPossessionApplication $application)
    {
        $officer = Auth::user();
        $application = $this->findOfficerApplication($officer, $application);
        $application->load(['documents', 'statusLogs', 'user', 'propertyRegistration', 'officerActions.officer']);

        $purchaser = DB::table('property_private_purchasers as ppp')
            ->join('property_auction_detail as pad', 'ppp.PrivatePurchaserId', '=', 'pad.PurchaserID')
            ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('property_registration as pr', 'pad.AssetId', '=', 'pr.AssetId')
            ->leftJoin('cities as c', 'pr.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'pr.SectorId', '=', 's.SectorId')
            ->where('ppp.PrivatePurchaserId', $application->private_purchaser_id)
            ->where('pad.AssetId', $application->asset_id)
            ->select([
                'ppp.PrivatePurchaserName',
                'ppp.PurchaserFatherName',
                'ppp.MobileNo',
                'ppp.Address',
                'ppp.PPPId',
                'ppp.MemberID',
                'ppp.ApplicationNo as purchaser_app_no',
                'ppp.CasteCategoryName as purchaser_category',
                'ppp.MaritalStatus as purchaser_marital_status',
                'ppp.CreateDate as purchaser_reg_date',
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
            ])
            ->first();

        $initialDeposit = 0.0;
        $installmentPaid = 0.0;
        if ($purchaser) {
            $initialDeposit = (float) ($purchaser->ReceivedAmount ?? 0);
            $assetId = $purchaser->AssetId;
            if ($assetId) {
                $cashReceiptPaid = (float) DB::table('cash_receipt_details')
                    ->where('asset_number', $assetId)
                    ->where('IsDeleted', 0)
                    ->where('IsActive', 1)
                    ->sum('total_paid_amount');

                $installmentPaid = $cashReceiptPaid;
            }
        }
        $totalReceived = $initialDeposit + $installmentPaid;
        $balanceAmount = $purchaser ? (float) ($purchaser->FlatCost ?? 0) - $totalReceived : 0.0;

        return view('physical-possession.officer.applications.show', compact('application', 'officer', 'purchaser', 'totalReceived', 'balanceAmount'));
    }

    // Document download
    public function downloadDocument(PhysicalPossessionApplication $application, PhysicalPossessionDocument $document)
    {
        $officer = Auth::user();
        $application = $this->findOfficerApplication($officer, $application);

        if ($document->application_id !== $application->id) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($document->file_path, $document->original_name);
    }

    // Approve, reject, or send back documents for correction
    public function decide(Request $request, PhysicalPossessionApplication $application)
    {
        $request->validate([
            'decision' => 'required|in:approved,rejected,sent_back',
            'remarks' => 'required|string|max:1000',
            'visit_slot_1' => 'required_if:decision,approved|nullable|date|after_or_equal:now',
            'visit_slot_2' => 'required_if:decision,approved|nullable|date|after_or_equal:now',
            'visit_slot_3' => 'required_if:decision,approved|nullable|date|after_or_equal:now',
            'visit_instructions' => 'nullable|string|max:500',
            'returned_documents' => 'required_if:decision,sent_back|array|min:1',
            'returned_documents.*' => 'integer',
            'document_remarks' => 'nullable|array',
            'document_remarks.*' => 'nullable|string|max:500',
        ], [
            'decision.required' => 'Please select Approve, Reject, or Send Back.',
            'decision.in' => 'Invalid decision selected.',
            'remarks.required' => 'Remarks are required.',
            'visit_slot_1.required_if' => 'Meeting slot 1 is required for approval.',
            'visit_slot_1.after_or_equal' => 'Meeting slot 1 cannot be in the past.',
            'visit_slot_2.required_if' => 'Meeting slot 2 is required for approval.',
            'visit_slot_2.after_or_equal' => 'Meeting slot 2 cannot be in the past.',
            'visit_slot_3.required_if' => 'Meeting slot 3 is required for approval.',
            'visit_slot_3.after_or_equal' => 'Meeting slot 3 cannot be in the past.',
            'returned_documents.required_if' => 'Select at least one document to send back.',
            'returned_documents.min' => 'Select at least one document to send back.',
        ]);

        if ($request->decision === 'approved') {
            $slots = [
                Carbon::parse($request->visit_slot_1)->toDateTimeString(),
                Carbon::parse($request->visit_slot_2)->toDateTimeString(),
                Carbon::parse($request->visit_slot_3)->toDateTimeString(),
            ];

            if (count(array_unique($slots)) !== 3) {
                return back()->withErrors(['visit_slot_1' => 'All three meeting slots must be distinct dates/times.'])->withInput();
            }

            foreach (['visit_slot_1', 'visit_slot_2', 'visit_slot_3'] as $slotField) {
                $scheduleError = $this->citizenVisitScheduleError($request->$slotField, Auth::user());
                if ($scheduleError) {
                    return back()->withErrors([$slotField => $scheduleError])->withInput();
                }
            }
        }

        if ($request->decision === 'sent_back') {
            return $this->sendBackDocuments(
                $application,
                $request->remarks,
                $request->input('returned_documents', []),
                $request->input('document_remarks', [])
            );
        }

        return $this->updateStatus(
            $application,
            $request->decision,
            $request->remarks,
            $request->decision === 'approved' ? $request->visit_slot_1 : null,
            $request->decision === 'approved' ? $request->visit_slot_2 : null,
            $request->decision === 'approved' ? $request->visit_slot_3 : null,
            $request->decision === 'approved' ? $request->visit_instructions : null
        );
    }

    // Approve karna (legacy route)
    public function approve(Request $request, PhysicalPossessionApplication $application)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000',
            'visit_slot_1' => 'required|date|after_or_equal:now',
            'visit_slot_2' => 'required|date|after_or_equal:now',
            'visit_slot_3' => 'required|date|after_or_equal:now',
            'visit_instructions' => 'nullable|string|max:500',
        ], [
            'remarks.required' => 'Remarks are required.',
            'visit_slot_1.required' => 'Meeting slot 1 is required.',
            'visit_slot_1.after_or_equal' => 'Meeting slot 1 cannot be in the past.',
            'visit_slot_2.required' => 'Meeting slot 2 is required.',
            'visit_slot_2.after_or_equal' => 'Meeting slot 2 cannot be in the past.',
            'visit_slot_3.required' => 'Meeting slot 3 is required.',
            'visit_slot_3.after_or_equal' => 'Meeting slot 3 cannot be in the past.',
        ]);

        $slots = [
            Carbon::parse($request->visit_slot_1)->toDateTimeString(),
            Carbon::parse($request->visit_slot_2)->toDateTimeString(),
            Carbon::parse($request->visit_slot_3)->toDateTimeString(),
        ];

        if (count(array_unique($slots)) !== 3) {
            return back()->withErrors(['visit_slot_1' => 'All three meeting slots must be distinct dates/times.'])->withInput();
        }

        foreach (['visit_slot_1', 'visit_slot_2', 'visit_slot_3'] as $slotField) {
            $scheduleError = $this->citizenVisitScheduleError($request->$slotField, Auth::user());
            if ($scheduleError) {
                return back()->withErrors([$slotField => $scheduleError])->withInput();
            }
        }

        return $this->updateStatus(
            $application,
            'approved',
            $request->remarks,
            $request->visit_slot_1,
            $request->visit_slot_2,
            $request->visit_slot_3,
            $request->visit_instructions
        );
    }

    // Reject karna (legacy route)
    public function reject(Request $request, PhysicalPossessionApplication $application)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ], [
            'remarks.required' => 'Remarks are required.',
        ]);

        return $this->updateStatus($application, 'rejected', $request->remarks);
    }

    // Status update - approve ya reject (single action per application, audit table)
    private function updateStatus(
        PhysicalPossessionApplication $application,
        string $newStatus,
        ?string $remarks,
        ?string $visitSlot1 = null,
        ?string $visitSlot2 = null,
        ?string $visitSlot3 = null,
        ?string $visitInstructions = null
    ) {
        $officer = Auth::user();
        $this->findOfficerApplication($officer, $application);

        if ($newStatus !== 'approved') {
            $visitSlot1 = null;
            $visitSlot2 = null;
            $visitSlot3 = null;
            $visitInstructions = null;
        }

        try {
            DB::transaction(function () use ($officer, $application, $newStatus, $remarks, $visitSlot1, $visitSlot2, $visitSlot3, $visitInstructions) {
                $locked = PhysicalPossessionApplication::query()
                    ->where('id', $application->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($locked->status !== 'pending') {
                    throw new \RuntimeException('Only pending applications can be updated.');
                }

                if ($newStatus === 'approved') {
                    foreach ([$visitSlot1, $visitSlot2, $visitSlot3] as $slot) {
                        if ($slot) {
                            $scheduleError = $this->citizenVisitScheduleError(
                                $slot,
                                $officer,
                                $locked->id,
                                true
                            );
                            if ($scheduleError) {
                                throw new \RuntimeException($scheduleError);
                            }
                        }
                    }
                }

                $oldStatus = $locked->status;

                $locked->update([
                    'status' => $newStatus,
                    'remarks' => $remarks,
                    'approved_by' => $officer->id,
                    'approved_at' => now(),
                    'citizen_visit_date' => null,
                    'visit_slot_1' => $visitSlot1,
                    'visit_slot_2' => $visitSlot2,
                    'visit_slot_3' => $visitSlot3,
                    'visit_instructions' => $visitInstructions,
                ]);

                if ($newStatus === 'approved') {
                    PhysicalPossessionDocument::where('application_id', $locked->id)
                        ->update(['review_status' => PhysicalPossessionDocument::REVIEW_ACCEPTED]);
                }

                OfficerApplicationAction::create([
                    'application_id' => $locked->id,
                    'asset_id' => $locked->asset_id,
                    'private_purchaser_id' => $locked->private_purchaser_id,
                    'user_id' => $locked->user_id,
                    'secure_id' => $locked->secure_id,
                    'officer_id' => $officer->id,
                    'action' => $newStatus,
                    'remarks' => $remarks,
                    'previous_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'application_number' => $locked->application_number,
                    'district_id' => $locked->district_id,
                    'district_name' => $locked->district_name,
                    'citizen_visit_date' => null,
                    'visit_slot_1' => $visitSlot1,
                    'visit_slot_2' => $visitSlot2,
                    'visit_slot_3' => $visitSlot3,
                    'visit_instructions' => $visitInstructions,
                ]);

                ApplicationStatusLog::create([
                    'application_id' => $locked->id,
                    'asset_id' => $locked->asset_id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'remarks' => $remarks,
                    'changed_by_type' => 'officer',
                    'changed_by_id' => $officer->id,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            throw $e;
        }

        $this->statusSmsService->notifyCitizen($application->fresh(), $newStatus);

        $message = $newStatus === 'approved'
            ? 'Application has been approved successfully.'
            : 'Application has been rejected.';

        return back()->with('success', $message);
    }

    // Officer sends back selected documents — citizen must re-upload and resubmit
    private function sendBackDocuments(
        PhysicalPossessionApplication $application,
        string $remarks,
        array $returnedDocumentIds,
        array $documentRemarks
    ) {
        $officer = Auth::user();
        $application = $this->findOfficerApplication($officer, $application);

        $returnedDocumentIds = array_map('intval', $returnedDocumentIds);
        $validDocuments = PhysicalPossessionDocument::where('application_id', $application->id)
            ->whereIn('id', $returnedDocumentIds)
            ->get();

        if ($validDocuments->count() !== count($returnedDocumentIds)) {
            return back()->withErrors(['returned_documents' => 'Invalid document selection.'])->withInput();
        }

        try {
            DB::transaction(function () use ($officer, $application, $remarks, $validDocuments, $documentRemarks) {
                $locked = PhysicalPossessionApplication::query()
                    ->where('id', $application->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($locked->status !== 'pending') {
                    throw new \RuntimeException('Only pending applications can be sent back for correction.');
                }

                $oldStatus = $locked->status;
                $returnedIds = $validDocuments->pluck('id')->all();

                PhysicalPossessionDocument::where('application_id', $locked->id)
                    ->whereIn('id', $returnedIds)
                    ->get()
                    ->each(function (PhysicalPossessionDocument $doc) use ($officer, $documentRemarks) {
                        $doc->update([
                            'review_status' => PhysicalPossessionDocument::REVIEW_RETURNED,
                            'officer_remarks' => trim((string) ($documentRemarks[$doc->id] ?? '')) ?: null,
                            'returned_at' => now(),
                            'returned_by' => $officer->id,
                        ]);
                    });

                PhysicalPossessionDocument::where('application_id', $locked->id)
                    ->whereNotIn('id', $returnedIds)
                    ->update(['review_status' => PhysicalPossessionDocument::REVIEW_ACCEPTED]);

                $locked->update([
                    'status' => 'returned',
                    'remarks' => $remarks,
                    'approved_by' => null,
                    'approved_at' => null,
                    'citizen_visit_date' => null,
                    'visit_instructions' => null,
                ]);

                OfficerApplicationAction::create([
                    'application_id' => $locked->id,
                    'asset_id' => $locked->asset_id,
                    'private_purchaser_id' => $locked->private_purchaser_id,
                    'user_id' => $locked->user_id,
                    'secure_id' => $locked->secure_id,
                    'officer_id' => $officer->id,
                    'action' => 'sent_back',
                    'remarks' => $remarks,
                    'previous_status' => $oldStatus,
                    'new_status' => 'returned',
                    'application_number' => $locked->application_number,
                    'district_id' => $locked->district_id,
                    'district_name' => $locked->district_name,
                ]);

                ApplicationStatusLog::create([
                    'application_id' => $locked->id,
                    'asset_id' => $locked->asset_id,
                    'old_status' => $oldStatus,
                    'new_status' => 'returned',
                    'remarks' => $remarks,
                    'changed_by_type' => 'officer',
                    'changed_by_id' => $officer->id,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        $this->statusSmsService->notifyCitizen($application->fresh(), 'sent_back');

        return back()->with('success', 'Application sent back to citizen for document correction.');
    }

    // Users list - jo users ne apply kiya apne district me
    public function users()
    {
        $officer = Auth::user();

        $userIds = $this->districtApplicationsQuery($officer)
            ->pluck('user_id')
            ->unique();

        $users = User::whereIn('id', $userIds)->get();

        return view('physical-possession.officer.users', compact('users', 'officer'));
    }

    // Reports page
    public function reports()
    {
        $officer = Auth::user();
        $query = $this->districtApplicationsQuery($officer);

        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyStats[] = [
                'label' => $month->format('M Y'),
                'total' => (clone $query)->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)->count(),
                'approved' => (clone $query)->where('status', 'approved')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)->count(),
                'rejected' => (clone $query)->where('status', 'rejected')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)->count(),
            ];
        }

        return view('physical-possession.officer.reports', compact('officer', 'monthlyStats'));
    }

    // Sirf apne district ki applications - yahi main filtering hai
    private function districtApplicationsQuery(User $officer)
    {
        $query = PhysicalPossessionApplication::query()
            ->where('status', '!=', 'draft');

        if ($officer->district_id) {
            $query->where('district_id', $officer->district_id);
        } elseif ($officer->district_name) {
            $query->where('district_name', 'like', '%'.$officer->district_name.'%');
        }

        return $query;
    }

    private function findOfficerApplication(User $officer, PhysicalPossessionApplication $application): PhysicalPossessionApplication
    {
        return $this->districtApplicationsQuery($officer)
            ->where('secure_id', $application->secure_id)
            ->firstOrFail();
    }

    /** Meeting only hourly slots 09:00 AM–05:00 PM; max 10 approved visits per district per 1-hour slot. */
    private function citizenVisitScheduleError(
        string $citizenVisitDate,
        User $officer,
        ?int $excludeApplicationId = null,
        bool $forUpdate = false
    ): ?string {
        $visitAt = Carbon::parse($citizenVisitDate);

        if ($visitAt->second !== 0 || $visitAt->minute !== 0) {
            return 'Meeting time must be on the hour (e.g. 09:00, 10:00).';
        }

        if ($visitAt->hour < 9 || $visitAt->hour > 16) {
            return 'Meeting time must be between 09:00 AM and 05:00 PM.';
        }

        $slotStart = $visitAt->copy()->startOfHour();
        $slotEnd = $slotStart->copy()->addHour();

        $query = PhysicalPossessionApplication::query()
            ->where('status', 'approved')
            ->whereNotNull('citizen_visit_date')
            ->where('citizen_visit_date', '>=', $slotStart)
            ->where('citizen_visit_date', '<', $slotEnd);

        if ($excludeApplicationId) {
            $query->where('id', '!=', $excludeApplicationId);
        }

        if ($officer->district_id) {
            $query->where('district_id', $officer->district_id);
        } elseif ($officer->district_name) {
            $query->where('district_name', 'like', '%'.$officer->district_name.'%');
        }

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        if ($query->count() >= 10) {
            return sprintf(
                'This time slot (%s – %s on %s) is full in your district. Maximum 10 citizens per hour per district.',
                $slotStart->format('h:i A'),
                $slotEnd->format('h:i A'),
                $slotStart->format('d M Y')
            );
        }

        return null;
    }

    /**
     * Ensure all eligible district purchasers have physical possession application rows.
     */
    private function ensureDistrictApplications($officer)
    {
        $receiptsQuery = DB::table('cash_receipt_details')
            ->select('asset_number')
            ->selectRaw('SUM(total_paid_amount) as receipt_total')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->groupBy('asset_number');

        $query = DB::table('property_auction_detail as pad')
            ->join('property_private_purchasers as ppp', 'pad.PurchaserID', '=', 'ppp.PrivatePurchaserId')
            ->join('mmsay_eligible_beneficiaries as meb', 'ppp.ApplicationNo', '=', 'meb.application_number')
            ->leftJoin('districts as d', 'ppp.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('physical_possession_applications as ppa', function ($join) {
                $join->on('pad.PurchaserID', '=', 'ppa.private_purchaser_id')
                     ->on('pad.AssetId', '=', 'ppa.asset_id');
            })
            ->leftJoinSub($receiptsQuery, 'crd', 'pad.AssetId', '=', 'crd.asset_number')
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
            DB::raw("COALESCE(pad.ReceivedAmount, 0) + COALESCE(crd.receipt_total, 0) as total_paid")
        ])
        ->whereRaw("COALESCE(pad.ReceivedAmount, 0) + COALESCE(crd.receipt_total, 0) >= 60000");

        $missing = $query->limit(15)->get();

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

    /**
     * Web view for Site Engineer to view draw maps/documents filtered by their district.
     */
    public function drawDocuments(Request $request)
    {
        $officer = Auth::user();
        // $this->ensureDistrictApplications($officer);
        
        $districtId = $officer->district_id;
        
        if (!$districtId) {
            return redirect()->route('pp.officer.dashboard')
                ->with('error', 'District is not assigned to your officer account.');
        }

        $documents = \App\Models\PropertyDrawDocument::where('district_id', $districtId)
            ->where('IsActive', true)
            ->where('IsDeleted', false)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('physical-possession.officer.draw-documents', compact('officer', 'documents'));
    }

    /**
     * DTP Officer Dashboard (MMGAY DTP role)
     */
    public function dtpDashboard(Request $request)
    {
        $officer = Auth::user();

        return view('physical-possession.dtp_dashboard', compact('officer'));
    }
}

