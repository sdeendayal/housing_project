<?php

namespace App\Http\Controllers\Api;

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

class MmgayBdoApiController extends Controller
{
    /**
     * Show BDO Dashboard stats and recent applications.
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

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'recent_applications' => $recentApplications,
            'bdo' => [
                'id' => $bdo->id,
                'name' => $bdo->name,
                'email' => $bdo->email,
                'block_id' => $bdo->block_id,
                'block_name' => $bdo->block_name,
            ]
        ]);
    }

    /**
     * Get BDO Eligibility List.
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

        return response()->json([
            'success' => true,
            'applications' => $applications
        ]);
    }

    /**
     * Show BDO Schedule Form - Auto-creates the application row if it doesn't exist yet.
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
            return response()->json(['success' => false, 'message' => 'Beneficiary record not found.'], 404);
        }

        if ($bdo->block_id && $owner->BlockId !== $bdo->block_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access to beneficiary in another block.'], 403);
        }

        if ($owner->IsPaid != 1) {
            return response()->json(['success' => false, 'message' => 'Physical Possession is only available for beneficiaries who have completed their payment.'], 400);
        }

        // 2. Find or dynamically create the physical possession application row
        $application = MmgayPossessionApplication::where('owner_id', $owner->OwnerId)->first();

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
                'secure_id' => $owner->secure_id,
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
            return response()->json(['success' => false, 'message' => 'Cannot schedule or update schedule after slot is confirmed by citizen or verified.'], 400);
        }

        return response()->json([
            'success' => true,
            'application' => $application,
            'owner' => $owner
        ]);
    }

    /**
     * Save BDO Schedule details.
     */
    public function scheduleSave(Request $request, $secureId)
    {
        $bdo = Auth::user();

        $application = MmgayPossessionApplication::where('secure_id', $secureId)->first();
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application record not found.'], 404);
        }

        if ($bdo->block_id && $application->block_id !== $bdo->block_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized block access.'], 403);
        }

        if (in_array($application->physical_possession_status, ['Slot Selected', 'Verified', 'Rejected'])) {
            return response()->json(['success' => false, 'message' => 'Cannot schedule or update schedule after slot is confirmed.'], 400);
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
                return response()->json(['success' => false, 'message' => "Slot {$slotNum} time cannot be in the past."], 422);
            }
            
            if ($dt->toDateString() === $todayStr && $dt->hour <= $currentHour) {
                return response()->json(['success' => false, 'message' => "Slot {$slotNum} time cannot be in the past."], 422);
            }
            
            if ($dt->hour < 9 || $dt->hour > 16) {
                return response()->json(['success' => false, 'message' => "Slot {$slotNum} must be between 09:00 AM and 05:00 PM."], 422);
            }
        }

        if (
            ($dateTime1->toDateString() === $dateTime2->toDateString() && $dateTime1->format('H:i') === $dateTime2->format('H:i')) ||
            ($dateTime1->toDateString() === $dateTime3->toDateString() && $dateTime1->format('H:i') === $dateTime3->format('H:i')) ||
            ($dateTime2->toDateString() === $dateTime3->toDateString() && $dateTime2->format('H:i') === $dateTime3->format('H:i'))
        ) {
            return response()->json(['success' => false, 'message' => 'You cannot select the same date and time for more than one slot.'], 422);
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
                return response()->json(['success' => false, 'message' => "Slot {$slotNum} (" . $dt->format('d M Y, h:i A') . ") has {$existingCount} visits scheduled. Maximum 10 visits allowed per hour."], 422);
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

        Log::info("MMGAY SMS Mock: Physical Possession slots scheduled for {$application->applicant_name} via API.");

        return response()->json([
            'success' => true,
            'message' => 'Physical Possession visit has been successfully scheduled.',
            'application' => $application
        ]);
    }

    /**
     * Get BDO Possession Applications.
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
            $mappedStatus = match ($status) {
                'awaiting_citizen' => 'Visit Scheduled',
                'awaiting_coordinates' => 'Slot Selected',
                'awaiting_bdo_doc' => 'Site Verified',
                'verified' => 'Verified',
                default => $status
            };

            $query->where('physical_possession_status', $mappedStatus);
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

        return response()->json([
            'success' => true,
            'applications' => $applications
        ]);
    }

    /**
     * Show BDO Verify Form.
     */
    public function verifyForm($secureId)
    {
        $bdo = Auth::user();

        $application = MmgayPossessionApplication::where('secure_id', $secureId)->first();
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found.'], 404);
        }

        if ($bdo->block_id && $application->block_id !== $bdo->block_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

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

        return response()->json([
            'success' => true,
            'application' => $application,
            'owner' => $owner,
            'logs' => $logs
        ]);
    }

    /**
     * Save BDO Verification Action (Stage 1 / Stage 2).
     */
    public function verifySave(Request $request, $secureId)
    {
        $bdo = Auth::user();

        $application = MmgayPossessionApplication::where('secure_id', $secureId)->first();
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found.'], 404);
        }

        if ($bdo->block_id && $application->block_id !== $bdo->block_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized block access.'], 403);
        }

        $currentStatus = $application->physical_possession_status;

        if ($request->input('action') === 'reschedule') {
            $oldStatus = $application->physical_possession_status;

            // Capture the previous slot time before resetting
            $prevSlotInfo = "N/A";
            if ($application->possession_date) {
                $dateFormatted = date('d M Y', strtotime($application->possession_date));
                $prevSlotInfo = $dateFormatted . " (" . ($application->meeting_slot ?? 'N/A') . ")";
            }

            $application->physical_possession_status = 'Eligible for Physical Possession';
            $application->possession_date = null;
            $application->meeting_slot = null;
            $application->citizen_visit_date = null;
            $application->visit_slot_1 = null;
            $application->visit_slot_2 = null;
            $application->visit_slot_3 = null;
            $application->visit_instructions = null;
            $application->save();

            MmgayPossessionStatusLog::create([
                'application_id' => $application->id,
                'asset_id' => $application->asset_id ?? 0,
                'old_status' => $oldStatus,
                'new_status' => 'Eligible for Physical Possession',
                'remarks' => "Applicant was absent / did not attend the scheduled visit slot: {$prevSlotInfo}. Visit slot has been reset for rescheduling by BDPO.",
                'changed_by_type' => 'officer',
                'changed_by_id' => $bdo->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Visit slot has been reset for rescheduling successfully.',
                'application' => $application
            ]);
        }

        if ($currentStatus === 'Slot Selected') {
            // Stage 1: Coordinates and Plot image capture
            $request->validate([
                'remarks' => 'required|string|max:1000',
                'latitude' => 'required|string',
                'longitude' => 'required|string',
                'plot_image' => 'required|image|mimes:jpeg,jpg,png|max:500',
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

            return response()->json([
                'success' => true,
                'message' => 'Site coordinates captured successfully. Now proceed to E-Possession step.',
                'application' => $application
            ]);

        } else {
            // Stage 2: Signed report upload & BDO Official Document (PDF only)
            $request->validate([
                'site_engineer_file' => 'required|file|mimes:pdf|max:500',
                'possession_certificate' => 'required|file|mimes:pdf|max:500',
            ], [
                'site_engineer_file.required' => 'BDO signed report (PDF) is required.',
                'site_engineer_file.mimes' => 'The signed report must be a PDF file.',
                'site_engineer_file.max' => 'The signed report must not exceed 500 KB.',
                'possession_certificate.required' => 'Final Possession Letter (PDF) is required.',
                'possession_certificate.mimes' => 'The Final Possession Letter must be a PDF file.',
                'possession_certificate.max' => 'The Final Possession Letter must not exceed 500 KB.',
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

            return response()->json([
                'success' => true,
                'message' => 'Application verified and approved successfully.',
                'application' => $application
            ]);
        }
    }

    /**
     * Download Prefilled BDO/Citizen Possession Report PDF.
     */
    public function downloadCertificate(Request $request, $secureId)
    {
        $bdo = Auth::user();

        $application = MmgayPossessionApplication::where('secure_id', $secureId)->first();
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found.'], 404);
        }

        if ($bdo && $bdo->block_id && $application->block_id !== $bdo->block_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized block access.'], 403);
        }

        $owner = DB::table('ownermaster as o')
            ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
            ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->where('o.OwnerId', $application->owner_id)
            ->select('o.*', 'b.BlockName', 'v.VillageName', 'd.DistrictName', 'f.FlatNo')
            ->first();

        if (!$owner) {
            return response()->json(['success' => false, 'message' => 'Owner details not found.'], 404);
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

        $pdf = Pdf::loadView('mmgay.bdo.pdf.prefilled-form', $pdfData)->setPaper('a4');
        $safeAppNo = str_replace(['/', '\\'], '-', $application->application_number);

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
}
