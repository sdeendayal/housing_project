<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MmgayPossessionApplication;
use App\Models\MmgayPossessionStatusLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MmgayVillagerApiController extends Controller
{
    /**
     * Show MMGAV Villager dashboard details.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        $ownerInfo = null;
        if (Schema::hasTable('ownermaster')) {
            $ownerInfo = DB::table('ownermaster')
                ->leftJoin('blockmaster', 'ownermaster.BlockId', '=', 'blockmaster.BlockId')
                ->leftJoin('villagemaster', 'ownermaster.VillageId', '=', 'villagemaster.VillageId')
                ->leftJoin('districtmaster', 'ownermaster.DistrictId', '=', 'districtmaster.DistrictId')
                ->leftJoin('flatmaster', 'ownermaster.FlatId', '=', 'flatmaster.FlatId')
                ->select(
                    'ownermaster.*',
                    'blockmaster.BlockName',
                    'villagemaster.VillageName',
                    'districtmaster.DistrictName',
                    'flatmaster.FlatNo'
                )
                ->where('ownermaster.MobileNo', $user->mobile)
                ->first();
        }

        $possessionApplication = null;
        $logs = [];
        if ($ownerInfo) {
            $possessionApplication = DB::table('mmgay_possession_applications')
                ->where('owner_id', $ownerInfo->OwnerId)
                ->first();

            if ($possessionApplication) {
                $logs = DB::table('mmgay_possession_status_logs')
                    ->where('application_id', $possessionApplication->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'owner_info' => $ownerInfo,
            'possession_application' => $possessionApplication,
            'logs' => $logs
        ]);
    }

    /**
     * Get scheduled visit slot options for the citizen.
     */
    public function submitPossessionForm()
    {
        $user = Auth::user();

        $application = MmgayPossessionApplication::where('user_id', $user->id)
            ->whereIn('physical_possession_status', ['Visit Scheduled', 'Rejected'])
            ->latest()
            ->first();

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'No active scheduled visit found for your application.'], 404);
        }

        $owner = DB::table('ownermaster')->where('OwnerId', $application->owner_id)->first();
        if (!$owner || !\App\Models\MmgayPossessionApplication::isWhitelistedForPossession($owner->RegistrationNo)) {
            return response()->json(['success' => false, 'message' => 'Physical Possession is only available for verified HFA land registration entries.'], 400);
        }

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
     * Save Citizen slot selection.
     */
    public function submitPossession(Request $request)
    {
        $user = Auth::user();

        $application = MmgayPossessionApplication::where('user_id', $user->id)
            ->whereIn('physical_possession_status', ['Visit Scheduled', 'Rejected'])
            ->latest()
            ->first();

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'No active scheduled visit found for your application.'], 404);
        }

        $owner = DB::table('ownermaster')->where('OwnerId', $application->owner_id)->first();
        if (!$owner || !\App\Models\MmgayPossessionApplication::isWhitelistedForPossession($owner->RegistrationNo)) {
            return response()->json(['success' => false, 'message' => 'Physical Possession is only available for verified HFA land registration entries.'], 400);
        }

        $request->validate([
            'selected_slot' => 'required|string',
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

        return response()->json([
            'success' => true,
            'message' => 'You have successfully selected the visit slot: ' . $dateTime->format('d M Y - h:i A') . ' and submitted your request.',
            'application' => $application
        ]);
    }

    /**
     * Download Citizen appointment slip.
     */
    public function downloadSlip(Request $request)
    {
        $user = Auth::user();

        $application = MmgayPossessionApplication::where('user_id', $user->id)
            ->whereIn('physical_possession_status', ['Slot Selected', 'Site Verified', 'Verified'])
            ->latest()
            ->first();

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'No active possession application found.'], 404);
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

        $pdfData = [
            'application' => $application,
            'owner' => $owner,
        ];

        $pdf = Pdf::loadView('mmgay.citizen.pdf.appointment-slip', $pdfData)->setPaper('a4');
        $safeAppNo = str_replace(['/', '\\'], '-', $application->application_number);

        return $pdf->download('Possession-Slip-MMGAY-'.$safeAppNo.'.pdf');
    }
}
