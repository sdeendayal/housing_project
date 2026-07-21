<?php

namespace App\Http\Controllers\MMGAY\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DashboardExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DistrictReportExport;
use App\Exports\VillageReportExport;
use App\Exports\AllotmentReportExport;
use App\Exports\RegistrationReportExport;


class SuperAdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $phase = $request->phase;
        $districtId = $request->district_id;
        $blockId = $request->block_id;
        $villageId = $request->village_id;

        // 1. Dropdown Data (Consistent with filters)
        $districts = DB::table('DistrictMaster as d')
            ->join('VillageMaster as v', 'v.DistrictId', '=', 'd.DistrictId')
            ->where('v.plots', '>', 0)
            ->when($phase, fn($q) => $q->where('v.Phase', $phase))
            ->select('d.DistrictId', 'd.DistrictName')
            ->distinct()->orderBy('d.DistrictName')->get();

        $blocks = $districtId ? DB::table('BlockMaster as b')
            ->join('VillageMaster as v', 'v.BlockId', '=', 'b.BlockId')
            ->where('b.DistrictId', $districtId)
            ->when($phase, fn($q) => $q->where('v.Phase', $phase))
            ->where('v.plots', '>', 0)
            ->select('b.BlockId', 'b.BlockName')
            ->distinct()->orderBy('b.BlockName')->get() : collect();

        $villages = $blockId ? DB::table('VillageMaster')
            ->where('BlockId', $blockId)
            ->when($phase, fn($q) => $q->where('Phase', $phase))
            ->where('plots', '>', 0)
            ->orderBy('VillageName')
            ->get(['VillageId', 'VillageName']) : collect();

        // 2. Main Stats (Districts, Villages, Beneficiaries)
        $totalDistricts = ($phase || $districtId)
            ? DB::table('DistrictMaster as d')
                ->join('VillageMaster as v', 'v.DistrictId', '=', 'd.DistrictId')
                ->where('v.plots', '>', 0)
                ->when($phase, fn($q) => $q->where('v.Phase', $phase))
                ->when($districtId, fn($q) => $q->where('d.DistrictId', $districtId))
                ->distinct('d.DistrictId')->count('d.DistrictId')
            : 22;

        $totalVillages = DB::table('VillageMaster')
            ->where('plots', '>', 0)
            ->when($phase, fn($q) => $q->where('Phase', $phase))
            ->when($districtId, fn($q) => $q->where('DistrictId', $districtId))
            ->when($blockId, fn($q) => $q->where('BlockId', $blockId))
            ->when($villageId, fn($q) => $q->where('VillageId', $villageId))
            ->count();

        $registeredBeneficiaries = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->where('v.plots', '>', 0)
            ->when($phase, fn($q) => $q->where('o.Phase', $phase))
            ->when($districtId, fn($q) => $q->where('o.DistrictId', $districtId))
            ->when($blockId, fn($q) => $q->where('o.BlockId', $blockId))
            ->when($villageId, fn($q) => $q->where('o.VillageId', $villageId))
            ->count();

        // Allotment Stats (Single Query)
        $stats = DB::table('OwnerMaster as o')
            ->join('FlatMaster as f', 'f.FlatId', '=', 'o.FlatId')
            ->when($phase, fn($q) => $q->where('o.Phase', $phase))
            ->when($districtId, fn($q) => $q->where('o.DistrictId', $districtId))
            ->when($blockId, fn($q) => $q->where('o.BlockId', $blockId))
            ->when($villageId, fn($q) => $q->where('o.VillageId', $villageId))
            ->selectRaw("
            COUNT(*) as GrossTotal,
            SUM(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 1 AND o.IsAllotmentCancelled = 0 THEN 1 ELSE 0 END) as ApprovedPaid,
            SUM(CASE WHEN o.IsApproved = 1 AND o.IsPaid = 0 AND o.IsAllotmentCancelled = 0 THEN 1 ELSE 0 END) as ApprovedUnpaid,
            SUM(CASE WHEN o.IsApproved = 0 AND o.IsPaid = 0 AND o.IsRejected = 0 THEN 1 ELSE 0 END) as PendingApprovalPayment,
            SUM(CASE WHEN o.IsRejected = 1 THEN 1 ELSE 0 END) as Rejected,
            SUM(CASE WHEN o.IsAllotmentCancelled = 1 THEN 1 ELSE 0 END) as AllotmentCancelled
        ")
            ->first();

        // 3. Registration Stats (Duplicate Free)

        $regBaseQuery = DB::table('dddnew1.registary as r')
            ->whereExists(function ($query) use ($phase, $districtId, $blockId, $villageId) {
                $query->selectRaw(1)
                    ->from('OwnerMaster as o')
                    ->whereColumn(
                        'o.MobileNo',
                        'r.SecondPartyMobile'
                    )
                    ->when(
                        filled($phase),
                        fn($q) => $q->where('o.Phase', $phase)
                    )
                    ->when(
                        filled($districtId),
                        fn($q) => $q->where(
                            'o.DistrictId',
                            $districtId
                        )
                    )
                    ->when(
                        filled($blockId),
                        fn($q) => $q->where(
                            'o.BlockId',
                            $blockId
                        )
                    )
                    ->when(
                        filled($villageId),
                        fn($q) => $q->where(
                            'o.VillageId',
                            $villageId
                        )
                    );
            });

        $totalRegistration = DB::table('dddnew1.registary')->count();

        $matched = (clone $regBaseQuery)->count();

        $unMatched = $totalRegistration - $matched;

        $registration = (object) [
            'TotalRegistration' => $totalRegistration,
            'Matched' => $matched,
            'UnMatched' => $unMatched,
        ];

        // Summary Object
        $summary = (object) [
            'TotalDistricts' => $totalDistricts,
            'TotalVillages' => $totalVillages,
            'RegisteredBeneficiaries' => $registeredBeneficiaries,
            'AllottedBeneficiaries' => $stats->GrossTotal ?? 0, // Design ke Allotted card ke liye
            'GrossTotal' => $stats->GrossTotal ?? 0, // Allotment Status cards ke liye
            'ApprovedPaid' => $stats->ApprovedPaid ?? 0,
            'ApprovedUnpaid' => $stats->ApprovedUnpaid ?? 0,
            'PendingApprovalPayment' => $stats->PendingApprovalPayment ?? 0,
            'Rejected' => $stats->Rejected ?? 0,
            'AllotmentCancelled' => $stats->AllotmentCancelled ?? 0,
        ];

        return view('mmgay.super-admin.dashboard', compact('summary', 'registration', 'districts', 'blocks', 'villages'));
    }
    public function getDistricts($phase = null)
    {
        $villageQuery = DB::table('VillageMaster')
            ->select('DistrictId');

        if ($phase == 1 || $phase == 2 || $phase == 3) {

            $villageQuery->where('phase', $phase)
                ->where('plots', '>', 0);

        } elseif ($phase == 4) {

            $villageQuery->where(function ($q) {
                $q->where('plots', 0)
                    ->orWhereNull('plots');
            });

        }

        $districts = DB::table('DistrictMaster')
            ->whereIn('DistrictId', $villageQuery)
            ->orderBy('DistrictName')
            ->get([
                'DistrictId',
                'DistrictName'
            ]);

        return response()->json($districts);
    }

    public function getBlocks($districtId, $phase = null)
    {
        $village = DB::table('VillageMaster')
            ->select('BlockId')
            ->where('DistrictId', $districtId);

        if ($phase == 1 || $phase == 2 || $phase == 3) {

            $village->where('phase', $phase)
                ->where('plots', '>', 0);
        }

        if ($phase == 4) {

            $village->where(function ($q) {
                $q->where('plots', 0)
                    ->orWhereNull('plots');
            });
        }

        $blocks = DB::table('BlockMaster')
            ->whereIn('BlockId', $village)
            ->orderBy('BlockName')
            ->get(['BlockId', 'BlockName']);

        return response()->json($blocks);
    }

    public function getVillages($blockId, $phase = null)
    {
        $villages = DB::table('VillageMaster')
            ->where('BlockId', $blockId);

        if ($phase == 1 || $phase == 2 || $phase == 3) {

            $villages->where('phase', $phase)
                ->where('plots', '>', 0);
        }

        if ($phase == 4) {

            $villages->where(function ($q) {
                $q->where('plots', 0)
                    ->orWhereNull('plots');
            });
        }

        return response()->json(

            $villages
                ->orderBy('VillageName')
                ->get(['VillageId', 'VillageName'])

        );
    }

    public function dashboardPdf(Request $request)
    {
        $data = $this->dashboardData($request);

        $pdf = Pdf::loadView(
            'mmgay.super-admin.dashboard-pdf',
            $data
        );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Dashboard_Report.pdf');
    }

    public function exportExcel(Request $request)
    {
        // dashboardData() method wahi array return karega jo aapne banaya hai
        $data = $this->dashboardData($request);
        return Excel::download(new DashboardExport($data), 'Dashboard_Report.xlsx');
    }

    public function exportPDF(Request $request)
    {
        // dashboardData() se array data lein
        $data = $this->dashboardData($request);

        $pdf = Pdf::loadView('mmgay.super-admin.dashboard-pdf', ['data' => $data]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('Dashboard_Report.pdf');
    }

    public function dashboardData(Request $request)
    {
        $phase = $request->phase;
        $districtId = $request->district_id;
        $blockId = $request->block_id;
        $villageId = $request->village_id;

        $villageQuery = DB::table('VillageMaster')
            ->where('plots', '>', 0);

        if ($phase)
            $villageQuery->where('phase', $phase);

        if ($districtId)
            $villageQuery->where('DistrictId', $districtId);

        if ($blockId)
            $villageQuery->where('BlockId', $blockId);

        if ($villageId)
            $villageQuery->where('VillageId', $villageId);

        $totalVillages = (clone $villageQuery)->count();

        $registered = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->where('v.plots', '>', 0);

        if ($phase)
            $registered->where('v.phase', $phase);

        if ($districtId)
            $registered->where('o.DistrictId', $districtId);

        if ($blockId)
            $registered->where('o.BlockId', $blockId);

        if ($villageId)
            $registered->where('o.VillageId', $villageId);

        $registeredBeneficiaries = $registered->count();

        $allotment = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->where('v.plots', '>', 0);

        if ($phase)
            $allotment->where('v.phase', $phase);

        if ($districtId)
            $allotment->where('o.DistrictId', $districtId);

        if ($blockId)
            $allotment->where('o.BlockId', $blockId);

        if ($villageId)
            $allotment->where('o.VillageId', $villageId);

        $stats = $allotment->selectRaw("
        COUNT(*) GrossTotal,

        SUM(CASE
            WHEN IsApproved=1
            AND IsPaid=1
            AND IsAllotmentCancelled=0
            THEN 1 ELSE 0 END) ApprovedPaid,

        SUM(CASE
            WHEN IsApproved=1
            AND IsPaid=0
            AND IsAllotmentCancelled=0
            THEN 1 ELSE 0 END) ApprovedUnpaid,

        SUM(CASE
            WHEN IsApproved=0
            AND IsRejected=0
            THEN 1 ELSE 0 END) PendingApprovalPayment,

        SUM(CASE
            WHEN IsRejected=1
            THEN 1 ELSE 0 END) Rejected,

        SUM(CASE
            WHEN IsAllotmentCancelled=1
            THEN 1 ELSE 0 END) AllotmentCancelled

    ")->first();

        return [
            'TotalVillages' => $totalVillages,
            'RegisteredBeneficiaries' => $registeredBeneficiaries,
            'GrossTotal' => $stats->GrossTotal ?? 0,
            'ApprovedPaid' => $stats->ApprovedPaid ?? 0,
            'ApprovedUnpaid' => $stats->ApprovedUnpaid ?? 0,
            'PendingApprovalPayment' => $stats->PendingApprovalPayment ?? 0,
            'Rejected' => $stats->Rejected ?? 0,
            'AllotmentCancelled' => $stats->AllotmentCancelled ?? 0,
        ];
    }

    public function districtWiseReport(Request $request)
    {
        $phase = $request->phase;
        $districtId = $request->district_id;

        /*
        |--------------------------------------------------------------------------
        | Step 1: Owner data को पहले Village-wise aggregate करें
        |--------------------------------------------------------------------------
        */
        $ownerStats = DB::table('OwnerMaster as o')

            ->leftJoin('FlatMaster as f', 'f.FlatId', '=', 'o.FlatId')

            ->when($phase, function ($query) use ($phase) {
                $query->where('o.Phase', '=', $phase);
            })

            ->select('o.VillageId')

            ->selectRaw("
            COUNT(DISTINCT o.OwnerId) AS RegisteredBeneficiaries,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                THEN o.OwnerId
            END) AS AllottedBeneficiaries,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND o.IsAllotmentCancelled = 0
                THEN o.OwnerId
            END) AS ApprovedPaid,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 0
                    AND o.IsAllotmentCancelled = 0
                THEN o.OwnerId
            END) AS ApprovedUnpaid,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 0
                    AND o.IsPaid = 0
                    AND o.IsRejected = 0
                THEN o.OwnerId
            END) AS PendingApprovalPayment,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsRejected = 1
                THEN o.OwnerId
            END) AS Rejected,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsAllotmentCancelled = 1
                THEN o.OwnerId
            END) AS AllotmentCancelled
        ")

            ->groupBy('o.VillageId');

        /*
        |--------------------------------------------------------------------------
        | Step 2: Village data को District-wise aggregate करें
        |--------------------------------------------------------------------------
        */
        $districtStats = DB::table('VillageMaster as v')

            ->leftJoinSub($ownerStats, 'os', function ($join) {
                $join->on('v.VillageId', '=', 'os.VillageId');
            })

            ->where('v.plots', '>', 0)

            ->when($phase, function ($query) use ($phase) {
                $query->where('v.phase', '=', $phase);
            })

            ->select('v.DistrictId')

            ->selectRaw("
            COUNT(DISTINCT v.VillageId) AS VillagesWithPlots,

            COALESCE(
                SUM(os.RegisteredBeneficiaries),
                0
            ) AS RegisteredBeneficiaries,

            COALESCE(
                SUM(os.AllottedBeneficiaries),
                0
            ) AS AllottedBeneficiaries,

            COALESCE(
                SUM(os.ApprovedPaid),
                0
            ) AS ApprovedPaid,

            COALESCE(
                SUM(os.ApprovedUnpaid),
                0
            ) AS ApprovedUnpaid,

            COALESCE(
                SUM(os.PendingApprovalPayment),
                0
            ) AS PendingApprovalPayment,

            COALESCE(
                SUM(os.Rejected),
                0
            ) AS Rejected,

            COALESCE(
                SUM(os.AllotmentCancelled),
                0
            ) AS AllotmentCancelled
        ")

            ->groupBy('v.DistrictId');

        /*
        |--------------------------------------------------------------------------
        | Step 3: District Master के साथ final report
        |--------------------------------------------------------------------------
        */
        $report = DB::table('DistrictMaster as d')

            ->leftJoinSub($districtStats, 'ds', function ($join) {
                $join->on('d.DistrictId', '=', 'ds.DistrictId');
            })

            ->when($districtId, function ($query) use ($districtId) {
                $query->where('d.DistrictId', '=', $districtId);
            })

            ->select([
                'd.DistrictId',
                'd.DistrictName',
            ])

            ->selectRaw("
            COALESCE(ds.VillagesWithPlots, 0)
                AS VillagesWithPlots,

            COALESCE(ds.RegisteredBeneficiaries, 0)
                AS RegisteredBeneficiaries,

            COALESCE(ds.AllottedBeneficiaries, 0)
                AS AllottedBeneficiaries,

            COALESCE(ds.ApprovedPaid, 0)
                AS ApprovedPaid,

            COALESCE(ds.ApprovedUnpaid, 0)
                AS ApprovedUnpaid,

            COALESCE(ds.PendingApprovalPayment, 0)
                AS PendingApprovalPayment,

            COALESCE(ds.Rejected, 0)
                AS Rejected,

            COALESCE(ds.AllotmentCancelled, 0)
                AS AllotmentCancelled
        ")

            ->orderBy('d.DistrictName')

            ->get();

        /*
        |--------------------------------------------------------------------------
        | Gross totals
        |--------------------------------------------------------------------------
        | District records कम होते हैं, इसलिए collection sum fast रहेगा।
        */
        $grossTotal = (object) [
            'VillagesWithPlots' =>
                $report->sum('VillagesWithPlots'),

            'RegisteredBeneficiaries' =>
                $report->sum('RegisteredBeneficiaries'),

            'AllottedBeneficiaries' =>
                $report->sum('AllottedBeneficiaries'),

            'ApprovedPaid' =>
                $report->sum('ApprovedPaid'),

            'ApprovedUnpaid' =>
                $report->sum('ApprovedUnpaid'),

            'PendingApprovalPayment' =>
                $report->sum('PendingApprovalPayment'),

            'Rejected' =>
                $report->sum('Rejected'),

            'AllotmentCancelled' =>
                $report->sum('AllotmentCancelled'),
        ];

        /*
        |--------------------------------------------------------------------------
        | District dropdown
        |--------------------------------------------------------------------------
        */
        $districts = DB::table('DistrictMaster')
            ->orderBy('DistrictName')
            ->get([
                'DistrictId',
                'DistrictName',
            ]);

        return view(
            'mmgay.super-admin.district-report',
            compact(
                'report',
                'grossTotal',
                'districts'
            )
        );
    }

    public function districtReportPdf(Request $request)
    {
        $data = $this->districtReportData($request);

        $pdf = Pdf::loadView(
            'mmgay.super-admin.district-report-pdf',
            $data
        );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('District_Report.pdf');
    }


    public function districtReportExcel(Request $request)
    {
        return Excel::download(
            new DistrictReportExport($request),
            'District_Report.xlsx'
        );
    }

    public function districtReportData(Request $request)
    {
        $phase = $request->phase;

        $districtId = $request->district_id;


        $report = DB::table('DistrictMaster as d')

            ->leftJoin('VillageMaster as v', function ($join) use ($phase) {

                $join->on('d.DistrictId', '=', 'v.DistrictId')
                    ->where('v.plots', '>', 0);

                if ($phase) {
                    $join->where('v.phase', $phase);
                }

            })

            ->leftJoin('OwnerMaster as o', function ($join) use ($phase) {

                $join->on('v.VillageId', '=', 'o.VillageId');

                if ($phase) {
                    $join->where('o.Phase', $phase);
                }

            })

            ->leftJoin('FlatMaster as f', 'f.FlatId', '=', 'o.FlatId')


            ->select(
                'd.DistrictId',
                'd.DistrictName'
            )


            ->selectRaw("

        COUNT(DISTINCT v.VillageId) AS VillagesWithPlots,

        COUNT(DISTINCT o.OwnerId) AS RegisteredBeneficiaries,


        COUNT(DISTINCT CASE
            WHEN f.FlatId IS NOT NULL THEN o.OwnerId
        END) AS AllottedBeneficiaries,


        COUNT(DISTINCT CASE
            WHEN f.FlatId IS NOT NULL
            AND o.IsApproved = 1
            AND o.IsPaid = 1
            AND o.IsAllotmentCancelled = 0
            THEN o.OwnerId
        END) AS ApprovedPaid,


        COUNT(DISTINCT CASE
            WHEN f.FlatId IS NOT NULL
            AND o.IsApproved = 1
            AND o.IsPaid = 0
            AND o.IsAllotmentCancelled = 0
            THEN o.OwnerId
        END) AS ApprovedUnpaid,


        COUNT(DISTINCT CASE
            WHEN f.FlatId IS NOT NULL
            AND o.IsApproved = 0
            AND o.IsPaid = 0
            AND o.IsRejected = 0
            THEN o.OwnerId
        END) AS PendingApprovalPayment,


        COUNT(DISTINCT CASE
            WHEN f.FlatId IS NOT NULL
            AND o.IsRejected = 1
            THEN o.OwnerId
        END) AS Rejected,


        COUNT(DISTINCT CASE
            WHEN f.FlatId IS NOT NULL
            AND o.IsAllotmentCancelled = 1
            THEN o.OwnerId
        END) AS AllotmentCancelled

    ")


            ->when($districtId, function ($q) use ($districtId) {

                $q->where('d.DistrictId', $districtId);

            })


            ->groupBy(
                'd.DistrictId',
                'd.DistrictName'
            )


            ->orderBy('d.DistrictName')


            ->get();


        $grossTotal = (object) [

            'VillagesWithPlots' => $report->sum('VillagesWithPlots'),

            'RegisteredBeneficiaries' => $report->sum('RegisteredBeneficiaries'),

            'AllottedBeneficiaries' => $report->sum('AllottedBeneficiaries'),

            'ApprovedPaid' => $report->sum('ApprovedPaid'),

            'ApprovedUnpaid' => $report->sum('ApprovedUnpaid'),

            'PendingApprovalPayment' => $report->sum('PendingApprovalPayment'),

            'Rejected' => $report->sum('Rejected'),

            'AllotmentCancelled' => $report->sum('AllotmentCancelled'),

        ];


        $districts = DB::table('DistrictMaster')
            ->orderBy('DistrictName')
            ->get([
                'DistrictId',
                'DistrictName'
            ]);


        return [

            'report' => $report,

            'grossTotal' => $grossTotal,

            'districts' => $districts

        ];
    }

    public function villageReportData(Request $request)
    {
        $phase = $request->phase;
        $villageId = $request->village_id;

        /*
        |--------------------------------------------------------------------------
        | Main grouped query
        |--------------------------------------------------------------------------
        */
        $baseQuery = DB::table('VillageMaster as v')

            ->leftJoin('OwnerMaster as o', function ($join) use ($phase) {
                $join->on('v.VillageId', '=', 'o.VillageId');

                if (!empty($phase)) {
                    $join->where('o.Phase', '=', $phase);
                }
            })

            ->leftJoin('FlatMaster as f', 'f.FlatId', '=', 'o.FlatId')

            ->where('v.plots', '>', 0)

            ->when($phase, function ($query) use ($phase) {
                $query->where('v.phase', '=', $phase);
            })

            ->when($villageId, function ($query) use ($villageId) {
                $query->where('v.VillageId', '=', $villageId);
            })

            ->select([
                'v.VillageId',
                'v.VillageName',
                'v.phase as Phase',
                'v.plots as TotalPlots',
            ])

            ->selectRaw("
            COUNT(DISTINCT o.OwnerId) AS RegisteredBeneficiaries,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                THEN o.OwnerId
            END) AS AllottedBeneficiaries,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END) AS ApprovedPaid,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND COALESCE(o.IsPaid, 0) = 0
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END) AS ApprovedUnpaid,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND COALESCE(o.IsApproved, 0) = 0
                    AND COALESCE(o.IsPaid, 0) = 0
                    AND COALESCE(o.IsRejected, 0) = 0
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END) AS PendingApprovalPayment,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsRejected = 1
                THEN o.OwnerId
            END) AS Rejected,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsAllotmentCancelled = 1
                THEN o.OwnerId
            END) AS AllotmentCancelled
        ")

            ->groupBy([
                'v.VillageId',
                'v.VillageName',
                'v.phase',
                'v.plots',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Fast pagination count
        |--------------------------------------------------------------------------
        | Laravel को grouped query की expensive count query चलाने से रोकता है।
        */
        $totalVillages = DB::table('VillageMaster as v')
            ->where('v.plots', '>', 0)

            ->when($phase, function ($query) use ($phase) {
                $query->where('v.phase', '=', $phase);
            })

            ->when($villageId, function ($query) use ($villageId) {
                $query->where('v.VillageId', '=', $villageId);
            })

            ->count();

        /*
        |--------------------------------------------------------------------------
        | Gross totals directly from database
        |--------------------------------------------------------------------------
        | पूरी collection PHP memory में load नहीं होगी।
        */
        $grossTotal = DB::query()
            ->fromSub(clone $baseQuery, 'village_report')
            ->selectRaw("
            COUNT(*) AS TotalVillages,
            COALESCE(SUM(TotalPlots), 0) AS TotalPlots,
            COALESCE(SUM(RegisteredBeneficiaries), 0) AS RegisteredBeneficiaries,
            COALESCE(SUM(AllottedBeneficiaries), 0) AS AllottedBeneficiaries,
            COALESCE(SUM(ApprovedPaid), 0) AS ApprovedPaid,
            COALESCE(SUM(ApprovedUnpaid), 0) AS ApprovedUnpaid,
            COALESCE(SUM(PendingApprovalPayment), 0) AS PendingApprovalPayment,
            COALESCE(SUM(Rejected), 0) AS Rejected,
            COALESCE(SUM(AllotmentCancelled), 0) AS AllotmentCancelled
        ")
            ->first();

        /*
        |--------------------------------------------------------------------------
        | 50 records per page
        |--------------------------------------------------------------------------
        */
        $report = (clone $baseQuery)
            ->orderBy('v.VillageName')
            ->paginate(
                perPage: 50,
                columns: ['*'],
                pageName: 'page',
                page: null,
                total: $totalVillages
            )
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Village dropdown
        |--------------------------------------------------------------------------
        */
        $villages = DB::table('VillageMaster as v')
            ->where('v.plots', '>', 0)

            ->when($phase, function ($query) use ($phase) {
                $query->where('v.phase', '=', $phase);
            })

            ->orderBy('v.VillageName')

            ->get([
                'v.VillageId',
                'v.VillageName',
            ]);

        return [
            'report' => $report,
            'grossTotal' => $grossTotal,
            'villages' => $villages,
        ];
    }

    public function villageWiseReport(Request $request)
    {
        $data = $this->villageReportData($request);

        return view(
            'mmgay.super-admin.village-report',
            $data
        );
    }

    public function villageReportPdf(Request $request)
    {
        $data = $this->villageReportData($request);

        $pdf = Pdf::loadView(
            'mmgay.super-admin.village-report-pdf',
            $data
        );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Village_Report.pdf');
    }

    public function villageReportExcel(Request $request)
    {
        return Excel::download(
            new VillageReportExport($request),
            'Village_Report.xlsx'
        );
    }

    public function applicants(Request $request)
    {
        $search = $request->search;
        $phase = $request->phase;
        $districtId = $request->district_id;
        $blockId = $request->block_id;
        $villageId = $request->village_id;
        $status = $request->status;

        $query = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->leftJoin('FlatMaster as f', 'f.FlatId', '=', 'o.FlatId')
            ->where('v.plots', '>', 0)
            ->select(
                'o.OwnerId',
                'o.secure_id',
                'o.OwnerName',
                'o.Relation',
                'o.FatherHusbandName',
                'o.Gender',
                'o.DistrictId',
                'o.BlockId',
                'o.VillageId',
                'o.OwnerAddress',
                'o.RegistrationNo',
                'o.PPPId',
                'o.MemberId',
                'o.Caste',
                'o.MobileNo',
                'o.CompanyId',
                'o.Phase',
                'o.IsApproved',
                'o.IsRejected',
                'o.IsPaid',
                'o.IsPaymentApproved',
                'o.IsAllotmentCancelled',
                'o.Remarks',
                'o.DCRemarks',
                'o.CreatedDate',
                'v.VillageName',
                'f.FlatId',
                'f.FlatNo',

                DB::raw("
                CASE
                    WHEN o.IsAllotmentCancelled = 1
                        THEN 'Cancelled'

                    WHEN o.IsRejected = 1
                        THEN 'Rejected'

                    WHEN o.IsApproved = 1
                        AND o.IsPaid = 1
                        THEN 'Approved & Paid'

                    WHEN o.IsApproved = 1
                        AND (
                            o.IsPaid = 0
                            OR o.IsPaid IS NULL
                        )
                        THEN 'Approved & Unpaid'

                    WHEN (
                        o.IsApproved = 0
                        OR o.IsApproved IS NULL
                    )
                        THEN 'Yet to be Approved'

                    ELSE 'Allotted'
                END AS ApplicantStatus
            ")
            );

        $query->when($phase, function ($q) use ($phase) {
            $q->where('o.Phase', $phase);
        });

        $query->when($districtId, function ($q) use ($districtId) {
            $q->where('o.DistrictId', $districtId);
        });

        $query->when($blockId, function ($q) use ($blockId) {
            $q->where('o.BlockId', $blockId);
        });

        $query->when($villageId, function ($q) use ($villageId) {
            $q->where('o.VillageId', $villageId);
        });

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('o.OwnerName', 'like', "%{$search}%")
                    ->orWhere('o.MobileNo', 'like', "%{$search}%")
                    ->orWhere('o.RegistrationNo', 'like', "%{$search}%")
                    ->orWhere('o.PPPId', 'like', "%{$search}%")
                    ->orWhere('f.FlatNo', 'like', "%{$search}%");
            });
        });

        $query->when($status, function ($q) use ($status) {
            switch ($status) {
                case 'approved_paid':
                    $q->where('o.IsApproved', 1)
                        ->where('o.IsPaid', 1);
                    break;

                case 'approved_unpaid':
                    $q->where('o.IsApproved', 1)
                        ->where(function ($subQuery) {
                            $subQuery
                                ->where('o.IsPaid', 0)
                                ->orWhereNull('o.IsPaid');
                        });
                    break;

                case 'pending':
                    $q->where(function ($subQuery) {
                        $subQuery
                            ->where('o.IsApproved', 0)
                            ->orWhereNull('o.IsApproved');
                    });
                    break;

                case 'rejected':
                    $q->where('o.IsRejected', 1);
                    break;

                case 'cancelled':
                    $q->where('o.IsAllotmentCancelled', 1);
                    break;
            }
        });

        $applicants = $query
            ->orderByDesc('o.OwnerId')
            ->paginate(20)
            ->withQueryString();

        $villages = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->where('v.plots', '>', 0)
            ->when($phase, fn($q) => $q->where('o.Phase', $phase))
            ->when($districtId, fn($q) => $q->where('o.DistrictId', $districtId))
            ->when($blockId, fn($q) => $q->where('o.BlockId', $blockId))
            ->select(
                'v.VillageId',
                'v.VillageName'
            )
            ->distinct()
            ->orderBy('v.VillageName')
            ->get();

        return view(
            'mmgay.super-admin.applicants.index',
            compact('applicants', 'villages')
        );
    }

    private function applicantsQuery(Request $request)
    {
        $search = $request->search;
        $phase = $request->phase;
        $districtId = $request->district_id;
        $blockId = $request->block_id;
        $villageId = $request->village_id;
        $status = $request->status;

        $query = DB::table('OwnerMaster as o')
            ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->leftJoin('FlatMaster as f', 'f.FlatId', '=', 'o.FlatId')
            ->where('v.plots', '>', 0)
            ->select(
                'o.OwnerId',
                'o.secure_id',
                'o.OwnerName',
                'o.Relation',
                'o.FatherHusbandName',
                'o.Gender',
                'o.DistrictId',
                'o.BlockId',
                'o.VillageId',
                'o.OwnerAddress',
                'o.RegistrationNo',
                'o.PPPId',
                'o.MemberId',
                'o.Caste',
                'o.MobileNo',
                'o.CompanyId',
                'o.Phase',
                'o.IsApproved',
                'o.IsRejected',
                'o.IsPaid',
                'o.IsPaymentApproved',
                'o.IsAllotmentCancelled',
                'o.Remarks',
                'o.DCRemarks',
                'o.CreatedDate',
                'v.VillageName',
                'f.FlatId',
                'f.FlatNo',

                DB::raw("
                    CASE
                        WHEN o.IsAllotmentCancelled = 1
                            THEN 'Cancelled'

                        WHEN o.IsRejected = 1
                            THEN 'Rejected'

                        WHEN o.IsApproved = 1
                            AND o.IsPaid = 1
                            THEN 'Approved & Paid'

                        WHEN o.IsApproved = 1
                            AND (
                                o.IsPaid = 0
                                OR o.IsPaid IS NULL
                            )
                            THEN 'Approved & Unpaid'

                        WHEN (
                            o.IsApproved = 0
                            OR o.IsApproved IS NULL
                        )
                            THEN 'Yet to be Approved'

                        ELSE 'Allotted'
                    END AS ApplicantStatus
                ")
            );

        $query->when($phase, function ($q) use ($phase) {
            $q->where('o.Phase', $phase);
        });

        $query->when($districtId, function ($q) use ($districtId) {
            $q->where('o.DistrictId', $districtId);
        });

        $query->when($blockId, function ($q) use ($blockId) {
            $q->where('o.BlockId', $blockId);
        });

        $query->when($villageId, function ($q) use ($villageId) {
            $q->where('o.VillageId', $villageId);
        });

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('o.OwnerName', 'like', "%{$search}%")
                    ->orWhere('o.MobileNo', 'like', "%{$search}%")
                    ->orWhere('o.RegistrationNo', 'like', "%{$search}%")
                    ->orWhere('o.PPPId', 'like', "%{$search}%")
                    ->orWhere('f.FlatNo', 'like', "%{$search}%");
            });
        });

        $query->when($status, function ($q) use ($status) {
            switch ($status) {
                case 'approved_paid':
                    $q->where('o.IsApproved', 1)
                        ->where('o.IsPaid', 1);
                    break;

                case 'approved_unpaid':
                    $q->where('o.IsApproved', 1)
                        ->where(function ($subQuery) {
                            $subQuery
                                ->where('o.IsPaid', 0)
                                ->orWhereNull('o.IsPaid');
                        });
                    break;

                case 'pending':
                    $q->where(function ($subQuery) {
                        $subQuery
                            ->where('o.IsApproved', 0)
                            ->orWhereNull('o.IsApproved');
                    });
                    break;

                case 'rejected':
                    $q->where('o.IsRejected', 1);
                    break;

                case 'cancelled':
                    $q->where('o.IsAllotmentCancelled', 1);
                    break;
            }
        });

        return $query;
    }

    public function applicantsExcel(Request $request)
    {
        $applicants = $this->applicantsQuery($request)
            ->orderByDesc('o.OwnerId')
            ->get();

        $fileName = 'applicants-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $downloadToken = $request->download_token;

        $response = response()->streamDownload(function () use ($applicants) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Sr. No.',
                'Owner ID',
                'Application No.',
                'Applicant',
                'Father / Husband',
                'Mobile',
                'PPP ID',
                'Village',
                'Phase',
                'Flat ID',
                'Flat No.',
                'Status',
            ]);

            foreach ($applicants as $index => $applicant) {
                fputcsv($handle, [
                    $index + 1,
                    $applicant->OwnerId,
                    $applicant->RegistrationNo,
                    $applicant->OwnerName,
                    $applicant->FatherHusbandName,
                    $applicant->MobileNo,
                    $applicant->PPPId,
                    $applicant->VillageName,
                    $applicant->Phase,
                    $applicant->FlatId,
                    $applicant->FlatNo,
                    $applicant->ApplicantStatus,
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);

        if ($downloadToken) {
            $response->headers->setCookie(
                cookie(
                    'download_token',
                    $downloadToken,
                    5,
                    '/',
                    null,
                    false,
                    false,
                    false,
                    'Lax'
                )
            );
        }

        return $response;
    }

    /*
    |--------------------------------------------------------------------------
    | PDF Export
    |--------------------------------------------------------------------------
    */

    public function applicantsPdf(Request $request)
    {
        $applicants = $this->applicantsQuery($request)
            ->orderByDesc('o.OwnerId')
            ->get();

        $downloadToken = $request->download_token;

        $pdf = Pdf::loadView(
            'mmgay.super-admin.applicants.pdf',
            compact('applicants')
        )->setPaper('a4', 'landscape');

        $response = $pdf->download(
            'applicants-' . now()->format('Y-m-d-H-i-s') . '.pdf'
        );

        if ($downloadToken) {
            $response->headers->setCookie(
                cookie(
                    'download_token',
                    $downloadToken,
                    5,
                    '/',
                    null,
                    false,
                    false,
                    false,
                    'Lax'
                )
            );
        }

        return $response;
    }

    public function allotmentReport(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Dropdown Data — Cached
        |--------------------------------------------------------------------------
        */

        $phases = Cache::remember('allotment_report_phases', 1800, function () {
            return DB::table('OwnerMaster')
                ->whereNotNull('Phase')
                ->where('Phase', '!=', '')
                ->distinct()
                ->orderBy('Phase')
                ->pluck('Phase');
        });

        $districts = Cache::remember('allotment_report_districts', 1800, function () {
            return DB::table('DistrictMaster')
                ->select('DistrictId', 'DistrictName')
                ->orderBy('DistrictName')
                ->get();
        });

        $districtId = $request->input('district_id');
        $blockId = $request->input('block_id');

        $blocksCacheKey = 'allotment_report_blocks_' . ($districtId ?: 'all');

        $blocks = Cache::remember($blocksCacheKey, 1800, function () use ($districtId) {
            return DB::table('BlockMaster')
                ->when($districtId, function ($query) use ($districtId) {
                    $query->where('DistrictId', $districtId);
                })
                ->select(
                    'BlockId',
                    'BlockName',
                    'DistrictId'
                )
                ->orderBy('BlockName')
                ->get();
        });

        $villagesCacheKey = sprintf(
            'allotment_report_villages_%s_%s',
            $districtId ?: 'all',
            $blockId ?: 'all'
        );

        $villages = Cache::remember($villagesCacheKey, 1800, function () use ($districtId, $blockId) {
            return DB::table('VillageMaster')
                ->where('plots', '>', 0)
                ->when($districtId, function ($query) use ($districtId) {
                    $query->where('DistrictId', $districtId);
                })
                ->when($blockId, function ($query) use ($blockId) {
                    $query->where('BlockId', $blockId);
                })
                ->select(
                    'VillageId',
                    'VillageName',
                    'DistrictId',
                    'BlockId'
                )
                ->orderBy('VillageName')
                ->get();
        });

        /*
        |--------------------------------------------------------------------------
        | Base Query — No unnecessary joins
        |--------------------------------------------------------------------------
        */

        $baseQuery = DB::table('OwnerMaster as o')
            ->where('o.FlatId', '>', 0)
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('VillageMaster as vm')
                    ->whereColumn('vm.VillageId', 'o.VillageId')
                    ->where('vm.plots', '>', 0);
            });

        /*
        |--------------------------------------------------------------------------
        | Common Filters
        |--------------------------------------------------------------------------
        */

        $baseQuery
            ->when($request->filled('phase'), function ($query) use ($request) {
                $query->where('o.Phase', $request->phase);
            })
            ->when($request->filled('district_id'), function ($query) use ($request) {
                $query->where('o.DistrictId', $request->district_id);
            })
            ->when($request->filled('block_id'), function ($query) use ($request) {
                $query->where('o.BlockId', $request->block_id);
            })
            ->when($request->filled('village_id'), function ($query) use ($request) {
                $query->where('o.VillageId', $request->village_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);
                $likeSearch = '%' . $search . '%';

                $query->where(function ($subQuery) use ($likeSearch) {
                    $subQuery
                        ->where('o.OwnerName', 'like', $likeSearch)
                        ->orWhere('o.RegistrationNo', 'like', $likeSearch)
                        ->orWhere('o.MobileNo', 'like', $likeSearch)
                        ->orWhere('o.PPPId', 'like', $likeSearch)
                        ->orWhere('o.FatherHusbandName', 'like', $likeSearch)

                        // FlatMaster join ki jagah EXISTS
                        ->orWhereExists(function ($flatQuery) use ($likeSearch) {
                            $flatQuery->selectRaw('1')
                                ->from('FlatMaster as sf')
                                ->whereColumn('sf.FlatId', 'o.FlatId')
                                ->where('sf.FlatNo', 'like', $likeSearch);
                        });
                });
            });

        /*
        |--------------------------------------------------------------------------
        | Summary — OwnerMaster par directly
        |--------------------------------------------------------------------------
        */

        $summary = (clone $baseQuery)
            ->selectRaw("
            COUNT(*) AS Total,

            SUM(
                CASE
                    WHEN COALESCE(o.IsAllotmentCancelled, 0) = 0
                        AND COALESCE(o.IsRejected, 0) = 0
                        AND COALESCE(o.IsApproved, 0) = 1
                        AND COALESCE(o.IsPaid, 0) = 1
                    THEN 1
                    ELSE 0
                END
            ) AS ApprovedPaid,

            SUM(
                CASE
                    WHEN COALESCE(o.IsAllotmentCancelled, 0) = 0
                        AND COALESCE(o.IsRejected, 0) = 0
                        AND COALESCE(o.IsApproved, 0) = 1
                        AND COALESCE(o.IsPaid, 0) = 0
                    THEN 1
                    ELSE 0
                END
            ) AS ApprovedUnpaid,

            SUM(
                CASE
                    WHEN COALESCE(o.IsAllotmentCancelled, 0) = 0
                        AND COALESCE(o.IsRejected, 0) = 0
                        AND COALESCE(o.IsApproved, 0) = 0
                    THEN 1
                    ELSE 0
                END
            ) AS PendingApproval,

            SUM(
                CASE
                    WHEN COALESCE(o.IsAllotmentCancelled, 0) = 0
                        AND COALESCE(o.IsRejected, 0) = 1
                    THEN 1
                    ELSE 0
                END
            ) AS Rejected,

            SUM(
                CASE
                    WHEN COALESCE(o.IsAllotmentCancelled, 0) = 1
                    THEN 1
                    ELSE 0
                END
            ) AS Cancelled
        ")
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        $allotmentsQuery = clone $baseQuery;

        switch ($request->status) {
            case 'approved_paid':
                $allotmentsQuery
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0')
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw('COALESCE(o.IsApproved, 0) = 1')
                    ->whereRaw('COALESCE(o.IsPaid, 0) = 1');
                break;

            case 'approved_unpaid':
                $allotmentsQuery
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0')
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw('COALESCE(o.IsApproved, 0) = 1')
                    ->whereRaw('COALESCE(o.IsPaid, 0) = 0');
                break;

            case 'pending':
                $allotmentsQuery
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0')
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw('COALESCE(o.IsApproved, 0) = 0');
                break;

            case 'rejected':
                $allotmentsQuery
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0')
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 1');
                break;

            case 'cancelled':
                $allotmentsQuery
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 1');
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Step 1: Only Owner IDs paginate
        |--------------------------------------------------------------------------
        |
        | Pagination count ab heavy joins ke bina chalega.
        |
        */

        $allotments = $allotmentsQuery
            ->select('o.OwnerId')
            ->orderByDesc('o.OwnerId')
            ->paginate(25)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Step 2: Current page ke 25 records ki details
        |--------------------------------------------------------------------------
        */

        $ownerIds = $allotments->getCollection()
            ->pluck('OwnerId')
            ->all();

        if (!empty($ownerIds)) {
            $records = DB::table('OwnerMaster as o')
                ->join(
                    'VillageMaster as v',
                    'v.VillageId',
                    '=',
                    'o.VillageId'
                )
                ->leftJoin(
                    'DistrictMaster as d',
                    'd.DistrictId',
                    '=',
                    'o.DistrictId'
                )
                ->leftJoin(
                    'BlockMaster as b',
                    'b.BlockId',
                    '=',
                    'o.BlockId'
                )
                ->leftJoin(
                    'FlatMaster as f',
                    'f.FlatId',
                    '=',
                    'o.FlatId'
                )
                ->whereIn('o.OwnerId', $ownerIds)
                ->select(
                    'o.OwnerId',
                    'o.RegistrationNo',
                    'o.OwnerName',
                    'o.FatherHusbandName',
                    'o.MobileNo',
                    'o.PPPId',
                    'o.Phase',
                    'o.FlatId',
                    'o.IsApproved',
                    'o.IsRejected',
                    'o.IsPaid',
                    'o.IsAllotmentCancelled',

                    'd.DistrictName',
                    'b.BlockName',
                    'v.VillageName',

                    'f.FlatNo'
                )
                ->orderByDesc('o.OwnerId')
                ->get();

            $allotments->setCollection($records);
        }

        return view(
            'mmgay.super-admin.allotment-report',
            compact(
                'allotments',
                'summary',
                'phases',
                'districts',
                'blocks',
                'villages'
            )
        );
    }

    public function exportAllotmentExcel(Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $filters = $request->only([
            'phase',
            'district_id',
            'block_id',
            'village_id',
            'search',
            'status',
        ]);

        $fileName = 'allotment-report-'
            . now()->format('d-m-Y-H-i-s')
            . '.xlsx';

        return Excel::download(
            new AllotmentReportExport($filters),
            $fileName
        );
    }

    public function exportAllotmentPdf(Request $request)
    {
        $query = $this->getFilteredAllotmentQuery($request);

        /*
         * Dompdf large dataset par bahut memory use karta hai.
         * Isliye PDF me practical record limit rakhi gayi hai.
         */
        $pdfLimit = 2000;

        $allotments = $query
            ->select([
                'o.OwnerId',
                'o.RegistrationNo',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.Phase',
                'o.FlatId',
                'o.IsApproved',
                'o.IsRejected',
                'o.IsPaid',
                'o.IsAllotmentCancelled',

                'd.DistrictName',
                'b.BlockName',
                'v.VillageName',
                'f.FlatNo',
            ])
            ->orderByDesc('o.OwnerId')
            ->limit($pdfLimit)
            ->get();

        $totalRecords = (clone $query)
            ->distinct('o.OwnerId')
            ->count('o.OwnerId');

        $filters = [
            'phase' => $request->phase,
            'district_id' => $request->district_id,
            'block_id' => $request->block_id,
            'village_id' => $request->village_id,
            'search' => $request->search,
            'status' => $request->status,
        ];

        $pdf = Pdf::loadView(
            'mmgay.super-admin.allotment.pdf',
            compact(
                'allotments',
                'filters',
                'totalRecords',
                'pdfLimit'
            )
        )
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', false);

        return $pdf->download(
            'allotment-report-' . now()->format('d-m-Y-H-i-s') . '.pdf'
        );
    }

    private function getFilteredAllotmentQuery(Request $request)
    {
        $query = DB::table('OwnerMaster as o')
            ->join(
                'VillageMaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )
            ->leftJoin(
                'DistrictMaster as d',
                'd.DistrictId',
                '=',
                'o.DistrictId'
            )
            ->leftJoin(
                'BlockMaster as b',
                'b.BlockId',
                '=',
                'o.BlockId'
            )
            ->leftJoin(
                'FlatMaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )
            ->where('v.plots', '>', 0)
            ->whereNotNull('o.FlatId')
            ->where('o.FlatId', '>', 0);

        if ($request->filled('phase')) {
            $query->where('o.Phase', $request->phase);
        }

        if ($request->filled('district_id')) {
            $query->where(
                'o.DistrictId',
                $request->district_id
            );
        }

        if ($request->filled('block_id')) {
            $query->where(
                'o.BlockId',
                $request->block_id
            );
        }

        if ($request->filled('village_id')) {
            $query->where(
                'o.VillageId',
                $request->village_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where(
                        'o.OwnerName',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'o.RegistrationNo',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'o.MobileNo',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'o.PPPId',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'o.FatherHusbandName',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'f.FlatNo',
                        'like',
                        '%' . $search . '%'
                    );
            });
        }

        switch ($request->status) {
            case 'approved_paid':
                $query
                    ->whereRaw(
                        'IFNULL(o.IsAllotmentCancelled, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsRejected, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsApproved, 0) = 1'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsPaid, 0) = 1'
                    );
                break;

            case 'approved_unpaid':
                $query
                    ->whereRaw(
                        'IFNULL(o.IsAllotmentCancelled, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsRejected, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsApproved, 0) = 1'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsPaid, 0) = 0'
                    );
                break;

            case 'pending':
                $query
                    ->whereRaw(
                        'IFNULL(o.IsAllotmentCancelled, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsRejected, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsApproved, 0) = 0'
                    );
                break;

            case 'rejected':
                $query
                    ->whereRaw(
                        'IFNULL(o.IsAllotmentCancelled, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsRejected, 0) = 1'
                    );
                break;

            case 'cancelled':
                $query->whereRaw(
                    'IFNULL(o.IsAllotmentCancelled, 0) = 1'
                );
                break;
        }

        return $query;
    }

    public function registration(Request $request)
    {
        $phase = $request->input('phase');
        $districtId = $request->input('district_id');
        $blockId = $request->input('block_id');
        $villageId = $request->input('village_id');
        $search = trim((string) $request->input('search'));
        $type = $request->input('type', 'all');

        $allowedTypes = [
            'all',
            'unique_registry',
            'duplicate_registry',
            'blank_registry',
            'matched',
            'unmatched',
            'unique_matched_mobile',
            'repeated_matched_mobile',
        ];

        if (!in_array($type, $allowedTypes, true)) {
            $type = 'all';
        }

        /*
        |--------------------------------------------------------------------------
        | Global registry counts
        |--------------------------------------------------------------------------
        | These counts do not change with dashboard filters or search.
        */

        $totalRegistrations = DB::table('dddnew1.registary')->count();

        $blankRegistryNumbers = DB::table('dddnew1.registary as r')
            ->where(function ($query) {
                $query
                    ->whereNull('r.RegistaryNumber')
                    ->orWhereRaw("TRIM(r.RegistaryNumber) = ''");
            })
            ->count();

        $uniqueRegistrations = DB::table('dddnew1.registary as r')
            ->whereNotNull('r.RegistaryNumber')
            ->whereRaw("TRIM(r.RegistaryNumber) != ''")
            ->distinct()
            ->count('r.RegistaryNumber');

        $duplicateRegistrations = max(
            0,
            $totalRegistrations
            - $uniqueRegistrations
            - $blankRegistryNumbers
        );

        /*
        |--------------------------------------------------------------------------
        | Global matched rows
        |--------------------------------------------------------------------------
        | EXISTS prevents duplicate OwnerMaster mobile rows from increasing count.
        */

        $globalMatchedQuery = DB::table('dddnew1.registary as r')
            ->whereNotNull('r.SecondPartyMobile')
            ->whereRaw("TRIM(r.SecondPartyMobile) != ''")
            ->whereExists(function ($query) {
                $query
                    ->selectRaw('1')
                    ->from('OwnerMaster as o')
                    ->whereNotNull('o.MobileNo')
                    ->whereRaw("TRIM(o.MobileNo) != ''")
                    ->whereColumn(
                        'o.MobileNo',
                        'r.SecondPartyMobile'
                    );
            });

        $matchedRegistrations = (clone $globalMatchedQuery)->count();

        $unmatchedRegistrations = max(
            0,
            $totalRegistrations - $matchedRegistrations
        );

        $uniqueMatchedMobiles = (clone $globalMatchedQuery)
            ->distinct()
            ->count('r.SecondPartyMobile');

        $repeatedMatchedMobileRows = max(
            0,
            $matchedRegistrations - $uniqueMatchedMobiles
        );

        $uniqueMatchedRegistrations = (clone $globalMatchedQuery)
            ->whereNotNull('r.RegistaryNumber')
            ->whereRaw("TRIM(r.RegistaryNumber) != ''")
            ->distinct()
            ->count('r.RegistaryNumber');

        /*
        |--------------------------------------------------------------------------
        | Registry ranked subquery
        |--------------------------------------------------------------------------
        | MySQL 8+ required because ROW_NUMBER() and COUNT() OVER() are used.
        */

        $registryRankedSubQuery = DB::table('dddnew1.registary as rs')
            ->select(
                'rs.District',
                'rs.TehsilName',
                'rs.Village',
                'rs.Token',
                'rs.Khewat',
                'rs.FirstParty',
                'rs.TotalArea',
                'rs.Bhag',
                'rs.TransferArea',
                'rs.SecondParty',
                'rs.SecondPartyMobile',
                'rs.RegistaryNumber',
                'rs.RegistaryDate'
            )
            ->selectRaw("
            ROW_NUMBER() OVER (
                PARTITION BY NULLIF(TRIM(rs.RegistaryNumber), '')
                ORDER BY
                    rs.RegistaryDate DESC,
                    rs.Token DESC
            ) AS registry_row_number
        ")
            ->selectRaw("
            COUNT(*) OVER (
                PARTITION BY NULLIF(TRIM(rs.RegistaryNumber), '')
            ) AS registry_group_count
        ")
            ->selectRaw("
            ROW_NUMBER() OVER (
                PARTITION BY NULLIF(TRIM(rs.SecondPartyMobile), '')
                ORDER BY
                    rs.RegistaryDate DESC,
                    rs.Token DESC
            ) AS mobile_row_number
        ")
            ->selectRaw("
            COUNT(*) OVER (
                PARTITION BY NULLIF(TRIM(rs.SecondPartyMobile), '')
            ) AS mobile_group_count
        ");

        /*
        |--------------------------------------------------------------------------
        | Owner mobile subquery
        |--------------------------------------------------------------------------
        | One OwnerId per mobile.
        | Dashboard filters are applied before selecting minimum OwnerId.
        */

        $ownerMobileSubQuery = DB::table('OwnerMaster as om')
            ->selectRaw('om.MobileNo, MIN(om.OwnerId) AS OwnerId')
            ->whereNotNull('om.MobileNo')
            ->whereRaw("TRIM(om.MobileNo) != ''")
            ->when(filled($phase), function ($query) use ($phase) {
                $query->where('om.Phase', $phase);
            })
            ->when(filled($districtId), function ($query) use ($districtId) {
                $query->where('om.DistrictId', $districtId);
            })
            ->when(filled($blockId), function ($query) use ($blockId) {
                $query->where('om.BlockId', $blockId);
            })
            ->when(filled($villageId), function ($query) use ($villageId) {
                $query->where('om.VillageId', $villageId);
            })
            ->groupBy('om.MobileNo');

        /*
        |--------------------------------------------------------------------------
        | Listing query
        |--------------------------------------------------------------------------
        | LEFT JOIN is required so unmatched and blank records can also be shown.
        */

        $registrationsQuery = DB::query()
            ->fromSub($registryRankedSubQuery, 'r')
            ->leftJoinSub(
                $ownerMobileSubQuery,
                'matched_owner',
                function ($join) {
                    $join->on(
                        'matched_owner.MobileNo',
                        '=',
                        'r.SecondPartyMobile'
                    );
                }
            )
            ->leftJoin(
                'OwnerMaster as o',
                'o.OwnerId',
                '=',
                'matched_owner.OwnerId'
            );

        /*
        |--------------------------------------------------------------------------
        | Card type filter
        |--------------------------------------------------------------------------
        */

        switch ($type) {
            case 'all':
                // Show all physical registry rows.
                break;

            case 'unique_registry':
                $registrationsQuery
                    ->whereNotNull('r.RegistaryNumber')
                    ->whereRaw("TRIM(r.RegistaryNumber) != ''")
                    ->where('r.registry_row_number', 1);
                break;

            case 'duplicate_registry':
                $registrationsQuery
                    ->whereNotNull('r.RegistaryNumber')
                    ->whereRaw("TRIM(r.RegistaryNumber) != ''")
                    ->where('r.registry_group_count', '>', 1)
                    ->where('r.registry_row_number', '>', 1);
                break;

            case 'blank_registry':
                $registrationsQuery
                    ->where(function ($query) {
                        $query
                            ->whereNull('r.RegistaryNumber')
                            ->orWhereRaw("TRIM(r.RegistaryNumber) = ''");
                    });
                break;

            case 'unmatched':
                $registrationsQuery
                    ->whereNull('matched_owner.OwnerId');
                break;

            case 'unique_matched_mobile':
                $registrationsQuery
                    ->whereNotNull('matched_owner.OwnerId')
                    ->whereNotNull('r.SecondPartyMobile')
                    ->whereRaw("TRIM(r.SecondPartyMobile) != ''")
                    ->where('r.mobile_row_number', 1);
                break;

            case 'repeated_matched_mobile':
                $registrationsQuery
                    ->whereNotNull('matched_owner.OwnerId')
                    ->whereNotNull('r.SecondPartyMobile')
                    ->whereRaw("TRIM(r.SecondPartyMobile) != ''")
                    ->where('r.mobile_row_number', '>', 1);
                break;

            case 'matched':
            default:
                $registrationsQuery
                    ->whereNotNull('matched_owner.OwnerId');
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Search filter
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {
            $registrationsQuery->where(function ($query) use ($search) {
                $query
                    ->where('r.SecondPartyMobile', $search)
                    ->orWhere('r.RegistaryNumber', $search)
                    ->orWhere('r.Token', $search)
                    ->orWhere(
                        'r.SecondParty',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'r.FirstParty',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'o.OwnerName',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere('o.RegistrationNo', $search);
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Filtered result count
        |--------------------------------------------------------------------------
        */

        $filteredRegistrations = (clone $registrationsQuery)->count();

        /*
        |--------------------------------------------------------------------------
        | Paginated records
        |--------------------------------------------------------------------------
        */

        $registrations = $registrationsQuery
            ->select(
                'r.District',
                'r.TehsilName',
                'r.Village',
                'r.Token',
                'r.Khewat',
                'r.FirstParty',
                'r.TotalArea',
                'r.Bhag',
                'r.TransferArea',
                'r.SecondParty',
                'r.SecondPartyMobile',
                'r.RegistaryNumber',
                'r.RegistaryDate',
                'r.registry_row_number',
                'r.registry_group_count',
                'r.mobile_row_number',
                'r.mobile_group_count',

                'o.OwnerId',
                'o.RegistrationNo',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.PPPId',
                'o.MemberId',
                'o.Caste',
                'o.Phase',
                'o.DistrictId',
                'o.BlockId',
                'o.VillageId'
            )
            ->orderByDesc('r.RegistaryDate')
            ->orderByDesc('r.Token')
            ->paginate(25)
            ->withQueryString();

        return view(
            'mmgay.super-admin.registration',
            compact(
                'registrations',
                'type',
                'totalRegistrations',
                'uniqueRegistrations',
                'duplicateRegistrations',
                'blankRegistryNumbers',
                'matchedRegistrations',
                'unmatchedRegistrations',
                'uniqueMatchedMobiles',
                'repeatedMatchedMobileRows',
                'uniqueMatchedRegistrations',
                'filteredRegistrations'
            )
        );
    }

    public function exportRegistrationExcel(Request $request)
    {
        $type = $request->input('type', 'all');

        $allowedTypes = [
            'all',
            'unique_registry',
            'duplicate_registry',
            'blank_registry',
            'matched',
            'unmatched',
            'unique_matched_mobile',
            'repeated_matched_mobile',
        ];

        if (!in_array($type, $allowedTypes, true)) {
            $type = 'all';
        }

        $filters = [
            'type' => $type,
            'phase' => $request->input('phase'),
            'district_id' => $request->input('district_id'),
            'block_id' => $request->input('block_id'),
            'village_id' => $request->input('village_id'),
            'search' => $request->input('search'),
        ];

        $fileName = 'registry-' .
            $type . '-' .
            now()->format('Y-m-d-H-i-s') .
            '.xlsx';



        return Excel::download(
            new RegistrationReportExport($filters),
            $fileName
        );
    }

    public function exportRegistrationPdf(Request $request)
    {
        $registrations = $this->getRegistrationExportQuery($request)
            ->orderByDesc('r.RegistaryDate')
            ->get();

        $typeLabels = [
            'all' => 'All Registry Records',
            'unique_registry' => 'Unique Registry Records',
            'duplicate_registry' => 'Duplicate Registry Records',
            'blank_registry' => 'Records with Missing Registry Numbers',
            'matched' => 'Matched Registry Records',
            'unmatched' => 'Unmatched Registry Records',
            'unique_matched_mobile' => 'Unique Matched Mobile Records',
            'repeated_matched_mobile' => 'Repeated Matched-Mobile Records',
        ];

        $type = $request->input('type', 'matched');
        $reportTitle = $typeLabels[$type] ?? 'Registry Report';

        $pdf = Pdf::loadView(
            'mmgay.super-admin.exports.registration-pdf',
            compact(
                'registrations',
                'reportTitle'
            )
        )->setPaper('a4', 'landscape');

        return $pdf->download(
            'registry-report-' . now()->format('Y-m-d-H-i-s') . '.pdf'
        );
    }

    private function getRegistrationExportQuery(Request $request)
    {
        $phase = $request->input('phase');
        $districtId = $request->input('district_id');
        $blockId = $request->input('block_id');
        $villageId = $request->input('village_id');
        $search = trim((string) $request->input('search'));
        $type = $request->input('type', 'matched');

        $allowedTypes = [
            'all',
            'unique_registry',
            'duplicate_registry',
            'blank_registry',
            'matched',
            'unmatched',
            'unique_matched_mobile',
            'repeated_matched_mobile',
        ];

        if (!in_array($type, $allowedTypes, true)) {
            $type = 'matched';
        }

        $registryRankedSubQuery = DB::table('dddnew1.registary as rs')
            ->select(
                'rs.District',
                'rs.TehsilName',
                'rs.Village',
                'rs.Token',
                'rs.Khewat',
                'rs.FirstParty',
                'rs.TotalArea',
                'rs.Bhag',
                'rs.TransferArea',
                'rs.SecondParty',
                'rs.SecondPartyMobile',
                'rs.RegistaryNumber',
                'rs.RegistaryDate'
            )
            ->selectRaw("
            ROW_NUMBER() OVER (
                PARTITION BY NULLIF(TRIM(rs.RegistaryNumber), '')
                ORDER BY rs.RegistaryDate DESC, rs.Token DESC
            ) AS registry_row_number
        ")
            ->selectRaw("
            COUNT(*) OVER (
                PARTITION BY NULLIF(TRIM(rs.RegistaryNumber), '')
            ) AS registry_group_count
        ")
            ->selectRaw("
            ROW_NUMBER() OVER (
                PARTITION BY NULLIF(TRIM(rs.SecondPartyMobile), '')
                ORDER BY rs.RegistaryDate DESC, rs.Token DESC
            ) AS mobile_row_number
        ")
            ->selectRaw("
            COUNT(*) OVER (
                PARTITION BY NULLIF(TRIM(rs.SecondPartyMobile), '')
            ) AS mobile_group_count
        ");

        $ownerMobileSubQuery = DB::table('OwnerMaster as om')
            ->selectRaw('om.MobileNo, MIN(om.OwnerId) AS OwnerId')
            ->whereNotNull('om.MobileNo')
            ->whereRaw("TRIM(om.MobileNo) != ''")
            ->when(filled($phase), function ($query) use ($phase) {
                $query->where('om.Phase', $phase);
            })
            ->when(filled($districtId), function ($query) use ($districtId) {
                $query->where('om.DistrictId', $districtId);
            })
            ->when(filled($blockId), function ($query) use ($blockId) {
                $query->where('om.BlockId', $blockId);
            })
            ->when(filled($villageId), function ($query) use ($villageId) {
                $query->where('om.VillageId', $villageId);
            })
            ->groupBy('om.MobileNo');

        $query = DB::query()
            ->fromSub($registryRankedSubQuery, 'r')
            ->leftJoinSub(
                $ownerMobileSubQuery,
                'matched_owner',
                function ($join) {
                    $join->on(
                        'matched_owner.MobileNo',
                        '=',
                        'r.SecondPartyMobile'
                    );
                }
            )
            ->leftJoin(
                'OwnerMaster as o',
                'o.OwnerId',
                '=',
                'matched_owner.OwnerId'
            );

        switch ($type) {
            case 'all':
                break;

            case 'unique_registry':
                $query
                    ->whereNotNull('r.RegistaryNumber')
                    ->whereRaw("TRIM(r.RegistaryNumber) != ''")
                    ->where('r.registry_row_number', 1);
                break;

            case 'duplicate_registry':
                $query
                    ->whereNotNull('r.RegistaryNumber')
                    ->whereRaw("TRIM(r.RegistaryNumber) != ''")
                    ->where('r.registry_group_count', '>', 1)
                    ->where('r.registry_row_number', '>', 1);
                break;

            case 'blank_registry':
                $query->where(function ($subQuery) {
                    $subQuery
                        ->whereNull('r.RegistaryNumber')
                        ->orWhereRaw("TRIM(r.RegistaryNumber) = ''");
                });
                break;

            case 'unmatched':
                $query->whereNull('matched_owner.OwnerId');
                break;

            case 'unique_matched_mobile':
                $query
                    ->whereNotNull('matched_owner.OwnerId')
                    ->whereNotNull('r.SecondPartyMobile')
                    ->whereRaw("TRIM(r.SecondPartyMobile) != ''")
                    ->where('r.mobile_row_number', 1);
                break;

            case 'repeated_matched_mobile':
                $query
                    ->whereNotNull('matched_owner.OwnerId')
                    ->whereNotNull('r.SecondPartyMobile')
                    ->whereRaw("TRIM(r.SecondPartyMobile) != ''")
                    ->where('r.mobile_row_number', '>', 1);
                break;

            case 'matched':
            default:
                $query->whereNotNull('matched_owner.OwnerId');
                break;
        }

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('r.SecondPartyMobile', $search)
                    ->orWhere('r.RegistaryNumber', $search)
                    ->orWhere('r.Token', $search)
                    ->orWhere('r.SecondParty', 'like', '%' . $search . '%')
                    ->orWhere('r.FirstParty', 'like', '%' . $search . '%')
                    ->orWhere('o.OwnerName', 'like', '%' . $search . '%')
                    ->orWhere('o.RegistrationNo', $search);
            });
        }

        return $query->select(
            'r.District',
            'r.TehsilName',
            'r.Village',
            'r.Token',
            'r.Khewat',
            'r.FirstParty',
            'r.TotalArea',
            'r.Bhag',
            'r.TransferArea',
            'r.SecondParty',
            'r.SecondPartyMobile',
            'r.RegistaryNumber',
            'r.RegistaryDate',
            'r.registry_group_count',
            'r.mobile_group_count',
            'o.OwnerId',
            'o.RegistrationNo',
            'o.OwnerName',
            'o.FatherHusbandName',
            'o.MobileNo',
            'o.PPPId',
            'o.MemberId',
            'o.Caste',
            'o.Phase'
        );
    }

}