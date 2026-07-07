<?php

namespace App\Http\Controllers\MMGAY\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        // =========================
        // OVERALL SUMMARY
        // =========================
        $summary = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->where(function ($q) {
                $q->where('v.TotalPlots', '>', 0)
                    ->orWhere('v.totalPlotsPhase2', '>', 0)
                    ->orWhere('v.totalPlotsPhase3', '>', 0);
            })
            ->selectRaw("
            (SELECT COUNT(*) FROM DistrictMaster) AS TotalDistricts,

            (
                SELECT COUNT(*)
                FROM VillageMaster
                WHERE COALESCE(TotalPlots,0) > 0
                   OR COALESCE(totalPlotsPhase2,0) > 0
                   OR COALESCE(totalPlotsPhase3,0) > 0
            ) AS TotalVillages,

            COUNT(DISTINCT o.OwnerId) AS TotalBeneficiaries,

            SUM(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 THEN 1 ELSE 0 END) AS TotalPaid,

            SUM(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 0 THEN 1 ELSE 0 END) AS TotalNotPaid,

            COUNT(DISTINCT o.FlatId) AS TotalAllotment,

            COUNT(DISTINCT CASE
                WHEN o.IsPaid = 1 THEN o.FlatId
            END) AS TotalAssignedFlats
        ")
            ->first();

        // =========================
        // BASE QUERY
        // =========================
        $baseQuery = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->where(function ($q) {
                $q->where('v.TotalPlots', '>', 0)
                    ->orWhere('v.totalPlotsPhase2', '>', 0)
                    ->orWhere('v.totalPlotsPhase3', '>', 0);
            });

        // =========================
        // PHASE WISE DATA
        // =========================
        $phaseData = (clone $baseQuery)
            ->selectRaw("
            o.Phase,

            COUNT(DISTINCT o.VillageId) AS TotalVillages,

            COUNT(DISTINCT o.OwnerId) AS TotalBeneficiaries,

            SUM(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 THEN 1 ELSE 0 END) AS TotalPaid,

            SUM(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 0 THEN 1 ELSE 0 END) AS TotalNotPaid,

            COUNT(DISTINCT o.FlatId) AS TotalAllotment,

            COUNT(DISTINCT CASE
                WHEN o.IsPaid = 1 THEN o.FlatId
            END) AS TotalAssignedFlats
        ")
            ->groupBy('o.Phase')
            ->orderBy('o.Phase')
            ->get();

        // =========================
        // DISTRICT GAP REPORT
        // =========================
        $gapData = (clone $baseQuery)
            ->join('DistrictMaster as d', 'd.DistrictId', '=', 'o.DistrictId')
            ->selectRaw("
            d.DistrictName,

            COUNT(DISTINCT o.FlatId) AS Allotment,

            COUNT(DISTINCT CASE
                WHEN o.IsPaid = 1 THEN o.FlatId
            END) AS Paid,

            (
                COUNT(DISTINCT o.FlatId)
                -
                COUNT(DISTINCT CASE
                    WHEN o.IsPaid = 1 THEN o.FlatId
                END)
            ) AS Gap
        ")
            ->groupBy('d.DistrictName')
            ->orderBy('d.DistrictName')
            ->paginate(10, ['*'], 'gap_page');

        // =========================
        // REGISTRATION DATA
        // =========================
        $matched = DB::table('registary as r')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('OwnerMaster as o')
                    ->whereColumn('o.MobileNo', 'r.SecondPartyMobile');
            })
            ->count();

        $totalRegistration = DB::table('registary')->count();

        $registration = (object) [
            'TotalRegistration' => $totalRegistration,
            'Matched' => $matched,
            'UnMatched' => $totalRegistration - $matched,
        ];

        return view('mmgay.super-admin.dashboard', compact(
            'summary',
            'phaseData',
            'gapData',
            'registration'
        ));
    }

    public function districtList()
    {
        // 1. Villages summary count (Strictly counting valid villages with plots)
        $villages = DB::table('VillageMaster')
            ->selectRaw("
                DistrictId,
                COUNT(DISTINCT VillageId) AS VillagesWithPlots
            ")
            ->where(function ($q) {
                $q->where(DB::raw('COALESCE(TotalPlots,0)'), '>', 0)
                    ->orWhere(DB::raw('COALESCE(totalPlotsPhase2,0)'), '>', 0)
                    ->orWhere(DB::raw('COALESCE(totalPlotsPhase3,0)'), '>', 0);
            })
            ->groupBy('DistrictId');

        // 2. Owner summary logic - Linked and scoped only to valid villages with plots
        $owners = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->where(function ($q) {
                $q->where('v.TotalPlots', '>', 0)
                    ->orWhere('v.totalPlotsPhase2', '>', 0)
                    ->orWhere('v.totalPlotsPhase3', '>', 0);
            })
            ->selectRaw("
                o.DistrictId,
                COUNT(DISTINCT o.OwnerId) AS Beneficiaries,
                SUM(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 THEN 1 ELSE 0 END) AS Paid,
                SUM(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 0 THEN 1 ELSE 0 END) AS NotPaid,
                COUNT(DISTINCT CASE WHEN o.IsApproved = 1 THEN o.FlatId END) AS Allotment,
                COUNT(DISTINCT CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 THEN o.FlatId END) AS AssignedFlats
            ")
            ->groupBy('o.DistrictId');

        // 3. Main query execution (Combines both with identical filtering logic)
        $data = DB::table('DistrictMaster as d')
            ->leftJoinSub($villages, 'v', function ($join) {
                $join->on('d.DistrictId', '=', 'v.DistrictId');
            })
            ->leftJoinSub($owners, 'o', function ($join) {
                $join->on('d.DistrictId', '=', 'o.DistrictId');
            })
            ->whereNotNull('v.VillagesWithPlots')
            ->selectRaw("
                d.DistrictId,
                d.DistrictName,
                COALESCE(v.VillagesWithPlots, 0) AS VillagesWithPlots,
                COALESCE(o.Beneficiaries, 0) AS Beneficiaries,
                COALESCE(o.Paid, 0) AS Paid,
                COALESCE(o.NotPaid, 0) AS NotPaid,
                COALESCE(o.AssignedFlats, 0) AS AssignedFlats,
                COALESCE(o.Allotment, 0) AS Allotment,
                (COALESCE(o.Allotment, 0) - COALESCE(o.AssignedFlats, 0)) AS Gap
            ")
            ->orderBy('d.DistrictName')
            ->get();

        // 4. Gross Total (Now completely matching with Dashboard Card States)
        $grossTotal = (object) [
            'VillagesWithPlots' => $data->sum('VillagesWithPlots'),
            'Beneficiaries' => $data->sum('Beneficiaries'),
            'Paid' => $data->sum('Paid'),
            'NotPaid' => $data->sum('NotPaid'),
            'AssignedFlats' => $data->sum('AssignedFlats'),
            'Allotment' => $data->sum('Allotment'),
            'Gap' => $data->sum('Gap'),
        ];

        return view('mmgay.super-admin.district-list', compact('data', 'grossTotal'));
    }

    public function allVillagesList(Request $request)
    {
        $search = $request->input('search');
        $districtFilter = $request->input('district_id');

        // 1. Dropdown list ke liye active districts fetch karna
        $districtsList = DB::table('DistrictMaster as d')
            ->join('VillageMaster as vm', 'vm.DistrictId', '=', 'd.DistrictId')
            ->where(function ($q) {
                $q->where('vm.TotalPlots', '>', 0)
                    ->orWhere('vm.totalPlotsPhase2', '>', 0)
                    ->orWhere('vm.totalPlotsPhase3', '>', 0);
            })
            ->select('d.DistrictId', 'd.DistrictName')
            ->distinct()
            ->orderBy('d.DistrictName')
            ->get();

        // 2. Owner summary logic - Exactly identical to District List (COUNT DISTINCT Pattern)
        $ownersQuery = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->where(function ($q) {
                $q->where('v.TotalPlots', '>', 0)
                    ->orWhere('v.totalPlotsPhase2', '>', 0)
                    ->orWhere('v.totalPlotsPhase3', '>', 0);
            });

        // Subquery filters (If active)
        if (!empty($districtFilter)) {
            $ownersQuery->where('o.DistrictId', $districtFilter);
        }
        if (!empty($search)) {
            $ownersQuery->where('v.VillageName', 'LIKE', '%' . $search . '%');
        }

        // 3. Separate execution for Global Gross Total (Exactly mirrors district list aggregates)
        $totalsData = (clone $ownersQuery)->selectRaw("
        COUNT(DISTINCT o.OwnerId) AS Beneficiaries,
        COUNT(DISTINCT CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 THEN o.OwnerId END) AS Paid,
        COUNT(DISTINCT CASE WHEN o.IsApproved = 1 AND o.IsPaid = 0 THEN o.OwnerId END) AS NotPaid,
        COUNT(DISTINCT CASE WHEN o.IsApproved = 1 THEN o.FlatId END) AS Allotment,
        COUNT(DISTINCT CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 THEN o.FlatId END) AS AssignedFlats
    ")->first();

        $grossTotal = (object) [
            'Beneficiaries' => $totalsData->Beneficiaries ?? 0,
            'Paid' => $totalsData->Paid ?? 0,
            'NotPaid' => $totalsData->NotPaid ?? 0,
            'AssignedFlats' => $totalsData->AssignedFlats ?? 0,
            'Allotment' => $totalsData->Allotment ?? 0,
            'Gap' => ($totalsData->Allotment ?? 0) - ($totalsData->AssignedFlats ?? 0),
        ];

        // 4. Paginated output execution grouped cleanly at Village Level
        $villagesData = $ownersQuery->selectRaw("
        v.VillageId,
        v.VillageName,
        o.DistrictId,
        COUNT(DISTINCT o.OwnerId) AS Beneficiaries,
        COUNT(DISTINCT CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 THEN o.OwnerId END) AS Paid,
        COUNT(DISTINCT CASE WHEN o.IsApproved = 1 AND o.IsPaid = 0 THEN o.OwnerId END) AS NotPaid,
        COUNT(DISTINCT CASE WHEN o.IsApproved = 1 THEN o.FlatId END) AS Allotment,
        COUNT(DISTINCT CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 THEN o.FlatId END) AS AssignedFlats,
        (COUNT(DISTINCT CASE WHEN o.IsApproved = 1 THEN o.FlatId END) - COUNT(DISTINCT CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 THEN o.FlatId END)) AS Gap
    ")
            ->groupBy('v.VillageId', 'v.VillageName', 'o.DistrictId')
            ->orderBy('v.VillageName')
            ->paginate(15)
            ->appends($request->query());

        return view('mmgay.super-admin.all-villages-list', compact('villagesData', 'grossTotal', 'districtsList', 'search', 'districtFilter'));
    }

    public function beneficiariesList(Request $request)
    {
        $search = $request->input('search');
        $phaseFilter = $request->input('phase');
        $districtFilter = $request->input('district_id');
        $villageFilter = $request->input('village_id');

        // 1. Core query with inner join to enforce plot availability constraints
        $query = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->join('DistrictMaster as d', 'd.DistrictId', '=', 'o.DistrictId')
            ->leftJoin('FlatMaster as f', 'f.FlatId', '=', 'o.FlatId')
            ->where(function ($q) {
                $q->where('v.TotalPlots', '>', 0)
                    ->orWhere('v.totalPlotsPhase2', '>', 0)
                    ->orWhere('v.totalPlotsPhase3', '>', 0);
            })
            ->select([
                'o.OwnerId',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.RegistrationNo',
                'o.Phase',
                'd.DistrictName',
                'v.VillageName',
                'f.FlatNo',
                'o.IsApproved',
                'o.IsPaid'
            ]);

        // Filters application rules
        if (!empty($search)) {
            $query->where(function ($q) {
                $q->where('o.OwnerName', 'LIKE', '%' . $search . '%')
                    ->orWhere('o.MobileNo', 'LIKE', '%' . $search . '%')
                    ->orWhere('o.RegistrationNo', 'LIKE', '%' . $search . '%');
            });
        }
        if (!empty($phaseFilter)) {
            $query->where('o.Phase', $phaseFilter);
        }
        if (!empty($districtFilter)) {
            $query->where('o.DistrictId', $districtFilter);
        }
        if (!empty($villageFilter)) {
            $query->where('o.VillageId', $villageFilter);
        }

        $beneficiaries = $query->orderBy('o.OwnerName')->paginate(15)->appends($request->query());

        // 2. Dropdown Filter Sync
        $districts = DB::table('DistrictMaster as d')
            ->join('VillageMaster as vm', 'vm.DistrictId', '=', 'd.DistrictId')
            ->where(function ($q) {
                $q->where('vm.TotalPlots', '>', 0)
                    ->orWhere('vm.totalPlotsPhase2', '>', 0)
                    ->orWhere('vm.totalPlotsPhase3', '>', 0);
            })
            ->select('d.DistrictId', 'd.DistrictName')
            ->distinct()
            ->orderBy('d.DistrictName')
            ->get();

        $villages = !empty($districtFilter)
            ? DB::table('VillageMaster')
                ->where('DistrictId', $districtFilter)
                ->where(function ($q) {
                    $q->where('TotalPlots', '>', 0)
                        ->orWhere('totalPlotsPhase2', '>', 0)
                        ->orWhere('totalPlotsPhase3', '>', 0);
                })
                ->orderBy('VillageName')
                ->get()
            : collect();

        return view('mmgay.super-admin.beneficiaries-list', compact(
            'beneficiaries',
            'districts',
            'villages',
            'search',
            'phaseFilter',
            'districtFilter',
            'villageFilter'
        ));
    }

    public function getBeneficiaryFullDetails($ownerId)
    {
        $details = DB::table('OwnerMaster as o')
            ->leftJoin('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->leftJoin('DistrictMaster as d', 'd.DistrictId', '=', 'o.DistrictId')
            ->leftJoin('BlockMaster as b', 'b.BlockId', '=', 'o.BlockId')
            ->leftJoin('FlatMaster as f', 'f.FlatId', '=', 'o.FlatId')
            ->leftJoin('SocialCategoryMaster as c', 'c.CategoryId', '=', 'o.Caste')
            ->where('o.OwnerId', $ownerId)
            ->select([
                'o.OwnerId',
                'o.OwnerName',
                'o.RegistrationNo',
                'o.PPPId',
                'o.MemberId',
                'o.Gender',
                'o.Relation',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.OwnerAddress',
                'o.Phase',
                'o.IsApproved',
                'o.IsRejected',
                'o.IsPaid',
                'o.IsPaymentApproved',
                'o.Remarks',
                'o.DCRemarks',
                'd.DistrictName',
                'b.BlockName',
                'v.VillageName',
                'f.FlatNo',
                'o.Caste as CasteName'
            ])
            ->first();

        if (!$details) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found in system'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $details
        ]);
    }

    public function allotmentList(Request $request)
    {
        $search = $request->search;

        $query = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->leftJoin('DistrictMaster as d', 'd.DistrictId', '=', 'o.DistrictId')
            ->leftJoin('BlockMaster as b', 'b.BlockId', '=', 'o.BlockId')
            ->leftJoin('FlatMaster as f', 'f.FlatId', '=', 'o.FlatId')
            ->where(function ($q) {
                $q->where('v.TotalPlots', '>', 0)
                    ->orWhere('v.totalPlotsPhase2', '>', 0)
                    ->orWhere('v.totalPlotsPhase3', '>', 0);
            })
            ->where('o.FlatId', '>', 0);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('o.OwnerName', 'like', "%{$search}%")
                    ->orWhere('o.MobileNo', 'like', "%{$search}%")
                    ->orWhere('o.RegistrationNo', 'like', "%{$search}%")
                    ->orWhere('f.FlatNo', 'like', "%{$search}%")
                    ->orWhere('v.VillageName', 'like', "%{$search}%");
            });
        }

        $totalAllotment = (clone $query)->distinct('o.FlatId')->count('o.FlatId');

        $allotments = $query
            ->selectRaw("
            o.FlatId,
            MAX(f.FlatNo) as FlatNo,
            MAX(o.OwnerId) as OwnerId,
            MAX(o.OwnerName) as OwnerName,
            MAX(o.FatherHusbandName) as FatherHusbandName,
            MAX(o.MobileNo) as MobileNo,
            MAX(o.RegistrationNo) as RegistrationNo,
            MAX(d.DistrictName) as DistrictName,
            MAX(b.BlockName) as BlockName,
            MAX(v.VillageName) as VillageName,
            MAX(o.Phase) as Phase,
            MAX(o.IsPaid) as IsPaid,
            MAX(o.IsApproved) as IsApproved
        ")
            ->groupBy('o.FlatId')
            ->orderBy('o.FlatId')
            ->paginate(20)
            ->appends($request->query());

        return view('mmgay.super-admin.allotment-list', compact(
            'allotments',
            'totalAllotment',
            'search'
        ));
    }

    public function assignedFlatsList(Request $request)
    {
        $search = $request->input('search');

        $query = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->leftJoin('DistrictMaster as d', 'd.DistrictId', '=', 'o.DistrictId')
            ->leftJoin('BlockMaster as b', 'b.BlockId', '=', 'o.BlockId')
            ->leftJoin('FlatMaster as f', 'f.FlatId', '=', 'o.FlatId')
            ->where(function ($q) {
                $q->where('v.TotalPlots', '>', 0)
                    ->orWhere('v.totalPlotsPhase2', '>', 0)
                    ->orWhere('v.totalPlotsPhase3', '>', 0);
            })
            ->where('o.FlatId', '>', 0)
            ->where('o.IsPaid', 1);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('o.OwnerName', 'like', "%{$search}%")
                    ->orWhere('o.MobileNo', 'like', "%{$search}%")
                    ->orWhere('o.RegistrationNo', 'like', "%{$search}%")
                    ->orWhere('f.FlatNo', 'like', "%{$search}%")
                    ->orWhere('v.VillageName', 'like', "%{$search}%");
            });
        }

        $totalAssigned = (clone $query)->distinct('o.FlatId')->count('o.FlatId');

        $assignedFlats = $query->selectRaw("
            o.FlatId,
            MAX(f.FlatNo) as FlatNo,
            MAX(o.OwnerId) as OwnerId,
            MAX(o.OwnerName) as OwnerName,
            MAX(o.FatherHusbandName) as FatherHusbandName,
            MAX(o.MobileNo) as MobileNo,
            MAX(o.RegistrationNo) as RegistrationNo,
            MAX(d.DistrictName) as DistrictName,
            MAX(b.BlockName) as BlockName,
            MAX(v.VillageName) as VillageName,
            MAX(o.Phase) as Phase,
            MAX(o.IsPaid) as IsPaid,
            MAX(o.IsApproved) as IsApproved
        ")
            ->groupBy('o.FlatId')
            ->orderBy('o.FlatId')
            ->paginate(20)
            ->appends($request->query());

        return view('mmgay.super-admin.assigned-flats-list', compact(
            'assignedFlats',
            'totalAssigned',
            'search'
        ));
    }

    public function paidBeneficiaries(Request $request)
    {
        $search = $request->input('search');

        // Logic completely synced with OwnerMaster, VillageMaster and DistrictMaster
        $query = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->leftJoin('DistrictMaster as d', 'd.DistrictId', '=', 'o.DistrictId')
            ->where(function ($q) {
                $q->where('v.TotalPlots', '>', 0)
                    ->orWhere('v.totalPlotsPhase2', '>', 0)
                    ->orWhere('v.totalPlotsPhase3', '>', 0);
            })
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1); // Logic matching TotalPaid summary state

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('o.OwnerName', 'LIKE', "%{$search}%")
                    ->orWhere('o.MobileNo', 'LIKE', "%{$search}%")
                    ->orWhere('o.RegistrationNo', 'LIKE', "%{$search}%")
                    ->orWhere('v.VillageName', 'LIKE', "%{$search}%")
                    ->orWhere('d.DistrictName', 'LIKE', "%{$search}%");
            });
        }

        $totalPaid = (clone $query)->count('o.OwnerId');

        $paidBeneficiaries = $query->select([
            'o.OwnerId',
            'o.OwnerName',
            'o.FatherHusbandName',
            'o.MobileNo',
            'o.RegistrationNo',
            'v.VillageName',
            'd.DistrictName'
        ])
            ->orderBy('o.OwnerName')
            ->paginate(20)
            ->appends($request->query());

        return view('mmgay.super-admin.paid-beneficiaries-list', compact('paidBeneficiaries', 'search', 'totalPaid'));
    }
}