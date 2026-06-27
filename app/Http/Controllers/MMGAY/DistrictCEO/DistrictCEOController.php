<?php

namespace App\Http\Controllers\MMGAY\DistrictCEO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistrictCEOController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('mmgay.login');
        }

        $districtId = DB::table('DistrictMaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        if (!$districtId) {
            abort(404, 'District not found.');
        }

        // Default Phase
        $phase = 1;

        $summary = "
            COUNT(*) AS Total,
            COALESCE(SUM(CASE WHEN IsPaid = 1 THEN 1 ELSE 0 END),0) AS Paid,
            COALESCE(SUM(CASE WHEN IsApproved = 1 AND IFNULL(IsPaid,0)=0 THEN 1 ELSE 0 END),0) AS Approved,
            COALESCE(SUM(CASE WHEN IsRejected = 1 THEN 1 ELSE 0 END),0) AS Rejected,
            COALESCE(SUM(CASE WHEN IsDcReconsidered = 1 THEN 1 ELSE 0 END),0) AS InProcess,
            COALESCE(SUM(
                CASE
                    WHEN IFNULL(IsApproved,0)=0
                     AND IFNULL(IsRejected,0)=0
                     AND IFNULL(IsPaid,0)=0
                     AND IFNULL(IsDcReconsidered,0)=0
                    THEN 1 ELSE 0
                END
            ),0) AS Pending
        ";

        $data = DB::table('OwnerMaster')
            ->where('DistrictId', $districtId)
            ->where('Phase', $phase)
            ->selectRaw($summary)
            ->first();

        return view('mmgay.district-ceo.dashboard', compact('data'));
    }

    public function getPhaseData($phase)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $districtId = DB::table('DistrictMaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        if (!$districtId) {
            return response()->json([
                'message' => 'District not found.'
            ], 404);
        }

        $summary = "
            COUNT(*) AS Total,
            COALESCE(SUM(CASE WHEN IsPaid = 1 THEN 1 ELSE 0 END),0) AS Paid,
            COALESCE(SUM(CASE WHEN IsApproved = 1 AND IFNULL(IsPaid,0)=0 THEN 1 ELSE 0 END),0) AS Approved,
            COALESCE(SUM(CASE WHEN IsRejected = 1 THEN 1 ELSE 0 END),0) AS Rejected,
            COALESCE(SUM(CASE WHEN IsDcReconsidered = 1 THEN 1 ELSE 0 END),0) AS InProcess,
            COALESCE(SUM(
                CASE
                    WHEN IFNULL(IsApproved,0)=0
                     AND IFNULL(IsRejected,0)=0
                     AND IFNULL(IsPaid,0)=0
                     AND IFNULL(IsDcReconsidered,0)=0
                    THEN 1 ELSE 0
                END
            ),0) AS Pending
        ";

        $data = DB::table('OwnerMaster')
            ->where('DistrictId', $districtId)
            ->where('Phase', $phase)
            ->selectRaw($summary)
            ->first();

        return response()->json($data);
    }
}