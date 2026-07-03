<?php

namespace App\Http\Controllers\MMGAY\DistrictCEO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistrictCEOController extends Controller
{
    public function dashboard(Request $request, $phase = 1)
    {
        $user = auth()->user();

        $districtId = DB::table('DistrictMaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        if (!$districtId) {
            abort(404, 'District not found.');
        }

        // Phase Wise Plot Column
        switch ($phase) {
            case 2:
                $plotColumn = 'v.totalPlotsPhase2';
                break;

            case 3:
                $plotColumn = 'v.totalPlotsPhase3';
                break;

            default:
                $plotColumn = 'v.TotalPlots';
                break;
        }

        $query = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->join('DistrictMaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('FlatMaster as f', 'o.FlatId', '=', 'f.FlatId')

            ->where('o.DistrictId', $districtId)
            ->where('o.Phase', $phase);

        // NULL Plot wale villages hide
        if ($phase == 1) {
            $query->whereNotNull('v.TotalPlots');
        } elseif ($phase == 2) {
            $query->whereNotNull('v.totalPlotsPhase2');
        } else {
            $query->whereNotNull('v.totalPlotsPhase3');
        }

        $villageData = $query
            ->groupBy(
                'd.DistrictName',
                'v.VillageId',
                'v.VillageName',
                DB::raw($plotColumn)
            )

            ->orderBy('v.VillageName')

            ->selectRaw("
    d.DistrictName,
    v.VillageId,
    v.VillageName,

    $plotColumn AS TotalPlots,

    COUNT(o.OwnerId) AS TotalApplicants,

    SUM(
        CASE
            WHEN o.IsApproved = 1
             AND o.IsPaid = 1
            THEN 1 ELSE 0
        END
    ) AS Paid,

    SUM(
        CASE
            WHEN o.IsApproved = 1
             AND o.IsPaid = 1
             AND o.Caste = 'SC'
            THEN 1 ELSE 0
        END
    ) AS SC,

    SUM(
        CASE
            WHEN o.IsApproved = 1
             AND o.IsPaid = 1
             AND o.Caste = 'Ghumantu'
            THEN 1 ELSE 0
        END
    ) AS Ghumantu,

    SUM(
        CASE
            WHEN o.IsApproved = 1
             AND o.IsPaid = 1
             AND o.Caste = 'Widow'
            THEN 1 ELSE 0
        END
    ) AS Widow,

    SUM(
        CASE
            WHEN o.IsApproved = 1
             AND o.IsPaid = 1
             AND (o.Caste = 'General' OR o.Caste = 'Others')
            THEN 1 ELSE 0
        END
    ) AS Others,

    (
    SUM(CASE WHEN o.IsApproved=1 AND o.IsPaid=1 AND o.Caste='SC' THEN 1 ELSE 0 END)
    +
    SUM(CASE WHEN o.IsApproved=1 AND o.IsPaid=1 AND o.Caste='Ghumantu' THEN 1 ELSE 0 END)
    +
    SUM(CASE WHEN o.IsApproved=1 AND o.IsPaid=1 AND o.Caste='Widow' THEN 1 ELSE 0 END)
    +
    SUM(CASE WHEN o.IsApproved=1 AND o.IsPaid=1
        AND (o.Caste='General' OR o.Caste='Others')
        THEN 1 ELSE 0 END)
) AS TotalAllotment,

    SUM(
        CASE
            WHEN o.IsPaymentApproved = 1
            THEN 1 ELSE 0
        END
    ) AS Possession
")
            ->get();

        $totals = [
            'totalVillages' => $villageData->count(),
            'totalPlots' => $villageData->sum('TotalPlots'),
            'totalApplicants' => $villageData->sum('TotalApplicants'),
            'totalPaid' => $villageData->sum('Paid'),
            'totalAllotment' => $villageData->sum('TotalAllotment'),
            'totalPossession' => $villageData->sum('Possession'),
            'totalSC' => $villageData->sum('SC'),
            'totalGhumantu' => $villageData->sum('Ghumantu'),
            'totalWidow' => $villageData->sum('Widow'),
            'totalOthers' => $villageData->sum('Others'),
        ];

        if ($request->ajax()) {
            return response()->json([
                'phase' => $phase,
                'totals' => $totals,
                'villageData' => $villageData
            ]);
        }

        return view('mmgay.district-ceo.dashboard', compact(
            'phase',
            'totals',
            'villageData'
        ));
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

    public function viewOwner($id)
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

        $owner = DB::table('OwnerMaster as o')
            ->leftJoin('DistrictMaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('BlockMaster as b', 'o.BlockId', '=', 'b.BlockId')
            ->leftJoin('VillageMaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->leftJoin('FlatMaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->leftJoin('SocialCategoryMaster as sc', 'o.Caste', '=', 'sc.CategoryId')
            ->where('o.OwnerId', $id)
            ->where('o.DistrictId', $districtId)
            ->select(
                'o.*',
                'd.DistrictName',
                'b.BlockName',
                'v.VillageName',
                'f.FlatNo',
                'sc.CategoryName'
            )
            ->first();

        if (!$owner) {
            abort(404, 'Owner not found');
        }

        return view('mmgay.district-ceo.owner-view', compact('owner'));
    }



    public function submitGrievance(Request $request, $id)
    {
        $request->validate([
            'grievance' => 'required'
        ]);

        DB::table('OwnerMaster')
            ->where('OwnerId', $id)
            ->update([
                'IsRejected' => 0, // 🔥 back to pending
                'Remarks' => $request->grievance,
                'UpdatedBy' => auth()->id(),
                'UpdatedDate' => now()
            ]);

        return redirect()
            ->back()
            ->with('success', 'Grievance submitted successfully. Application moved to Pending.');
    }

    public function ownerAction(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000',
            'action' => 'required|in:approve,reject',
        ]);

        $data = [
            'Remarks' => $request->remarks,
            'UpdatedBy' => auth()->id(),
            'UpdatedDate' => now(),
        ];

        if ($request->action == 'approve') {

            $data['IsApproved'] = 1;
            $data['IsRejected'] = 0;

        } else {

            $data['IsApproved'] = 0;
            $data['IsRejected'] = 1;

        }

        DB::table('OwnerMaster')
            ->where('OwnerId', $id)
            ->update($data);

        return back()->with('success', 'Application updated successfully.');
    }
}