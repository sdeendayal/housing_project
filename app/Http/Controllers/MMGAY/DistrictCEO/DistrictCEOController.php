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

    public function list($phase, $status)
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

        $query = DB::table('OwnerMaster as o')
            ->leftJoin('DistrictMaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('BlockMaster as b', 'o.BlockId', '=', 'b.BlockId')
            ->leftJoin('VillageMaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->leftJoin('FlatMaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->leftJoin('SocialCategoryMaster as sc', 'o.Caste', '=', 'sc.CategoryId')

            ->select(
                'o.OwnerId',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.Relation',
                'o.Gender',
                'o.MobileNo',
                'o.RegistrationNo',
                'o.PPPId',
                'o.Phase',

                'o.Remarks',
                'o.DCRemarks',
                'o.UpdatedBy',
                'o.Caste', 
                'o.IsPaid',
                'o.IsApproved',
                'o.IsRejected',
                'o.IsDcReconsidered',
                'o.IsPaymentApproved',

                'd.DistrictName',
                'b.BlockName',
                'v.VillageName',

                'f.FlatNo',

                'sc.CategoryName'
            )

            ->where('o.DistrictId', $districtId)
            ->where('o.Phase', $phase);

        switch ($status) {

            case 'paid':
                $query->where('o.IsPaid', 1);
                break;

            case 'approved':
                $query->where('o.IsApproved', 1)
                    ->where(function ($q) {
                        $q->whereNull('o.IsPaid')
                            ->orWhere('o.IsPaid', 0);
                    });
                break;

            case 'rejected':
                $query->where('o.IsRejected', 1);
                break;

            case 'inprocess':
                $query->where('o.IsDcReconsidered', 1);
                break;

            case 'pending':
                $query->where(function ($q) {

                    $q->where(function ($qq) {
                        $qq->whereNull('o.IsApproved')
                            ->orWhere('o.IsApproved', 0);
                    })
                        ->where(function ($qq) {
                            $qq->whereNull('o.IsRejected')
                                ->orWhere('o.IsRejected', 0);
                        })
                        ->where(function ($qq) {
                            $qq->whereNull('o.IsPaid')
                                ->orWhere('o.IsPaid', 0);
                        })
                        ->where(function ($qq) {
                            $qq->whereNull('o.IsDcReconsidered')
                                ->orWhere('o.IsDcReconsidered', 0);
                        });

                });
                break;

            case 'total':
            default:
                break;
        }

        $owners = $query
            ->orderByDesc('o.OwnerId')
            ->paginate(20);

        return view('mmgay.district-ceo.owner-list', compact(
            'owners',
            'phase',
            'status'
        ));
    }
}