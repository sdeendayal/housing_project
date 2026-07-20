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

        // 3. Registration Stats (Filtered by Join)
        $regBaseQuery = DB::table('registary as r')
            ->join('OwnerMaster as o', 'r.SecondPartyMobile', '=', 'o.MobileNo')
            ->when($phase, fn($q) => $q->where('o.Phase', $phase))
            ->when($districtId, fn($q) => $q->where('o.DistrictId', $districtId))
            ->when($blockId, fn($q) => $q->where('o.BlockId', $blockId))
            ->when($villageId, fn($q) => $q->where('o.VillageId', $villageId));

        $totalRegistration = (clone $regBaseQuery)->count();
        $matched = (clone $regBaseQuery)->whereNotNull('o.MobileNo')->count();

        $registration = (object) [
            'TotalRegistration' => $totalRegistration,
            'Matched' => $matched,
            'UnMatched' => max(0, $totalRegistration - $matched),
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
            ->select('d.DistrictId', 'd.DistrictName')
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
            ->groupBy('d.DistrictId', 'd.DistrictName')
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
            ->get(['DistrictId', 'DistrictName']);

        return view('mmgay.super-admin.district-report', compact('report', 'grossTotal', 'districts'));
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

        $report = DB::table('VillageMaster as v')

            ->leftJoin('OwnerMaster as o', function ($join) use ($phase) {
                $join->on('v.VillageId', '=', 'o.VillageId');

                if (!empty($phase)) {
                    $join->where('o.Phase', '=', $phase);
                }
            })

            ->leftJoin(
                'FlatMaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )

            // Sirf wahi villages jinke plots 0 se zyada hain
            ->where('v.plots', '>', 0)

            // Phase filter
            ->when($phase, function ($query) use ($phase) {
                $query->where('v.phase', '=', $phase);
            })

            // Village filter
            ->when($villageId, function ($query) use ($villageId) {
                $query->where('v.VillageId', '=', $villageId);
            })

            ->select(
                'v.VillageId',
                'v.VillageName',
                'v.phase as Phase',
                'v.plots as TotalPlots'
            )

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

            ->groupBy(
                'v.VillageId',
                'v.VillageName',
                'v.phase',
                'v.plots'
            )

            ->orderBy('v.VillageName')

            ->get();

        $grossTotal = (object) [
            'TotalVillages' => $report->count(),

            'TotalPlots' => $report->sum('TotalPlots'),

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

        // Dropdown me sirf plots > 0 wale villages
        $villages = DB::table('VillageMaster as v')
            ->where('v.plots', '>', 0)

            // Phase select hone par us phase ke villages hi dikhaye
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
        | Dropdown Data
        |--------------------------------------------------------------------------
        */

        $phases = DB::table('OwnerMaster')
            ->whereNotNull('Phase')
            ->where('Phase', '!=', '')
            ->select('Phase')
            ->distinct()
            ->orderBy('Phase')
            ->pluck('Phase');

        $districts = DB::table('DistrictMaster')
            ->select('DistrictId', 'DistrictName')
            ->orderBy('DistrictName')
            ->get();

        $blocks = DB::table('BlockMaster')
            ->when($request->district_id, function ($query, $districtId) {
                $query->where('DistrictId', $districtId);
            })
            ->select(
                'BlockId',
                'BlockName',
                'DistrictId'
            )
            ->orderBy('BlockName')
            ->get();

        $villages = DB::table('VillageMaster')
            ->where('plots', '>', 0)
            ->when($request->district_id, function ($query, $districtId) {
                $query->where('DistrictId', $districtId);
            })
            ->when($request->block_id, function ($query, $blockId) {
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

        /*
        |--------------------------------------------------------------------------
        | Base Allotment Query
        |--------------------------------------------------------------------------
        */

        $baseQuery = DB::table('OwnerMaster as o')
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

                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('o.OwnerName', 'like', '%' . $search . '%')
                        ->orWhere('o.RegistrationNo', 'like', '%' . $search . '%')
                        ->orWhere('o.MobileNo', 'like', '%' . $search . '%')
                        ->orWhere('o.PPPId', 'like', '%' . $search . '%')
                        ->orWhere('o.FatherHusbandName', 'like', '%' . $search . '%')
                        ->orWhere('f.FlatNo', 'like', '%' . $search . '%');
                });
            });

        /*
        |--------------------------------------------------------------------------
        | Summary Counts
        |--------------------------------------------------------------------------
        */

        $summary = (clone $baseQuery)
            ->selectRaw("
            COUNT(DISTINCT o.OwnerId) AS Total,

            COUNT(DISTINCT CASE
                WHEN IFNULL(o.IsAllotmentCancelled, 0) = 0
                    AND IFNULL(o.IsRejected, 0) = 0
                    AND IFNULL(o.IsApproved, 0) = 1
                    AND IFNULL(o.IsPaid, 0) = 1
                THEN o.OwnerId
            END) AS ApprovedPaid,

            COUNT(DISTINCT CASE
                WHEN IFNULL(o.IsAllotmentCancelled, 0) = 0
                    AND IFNULL(o.IsRejected, 0) = 0
                    AND IFNULL(o.IsApproved, 0) = 1
                    AND IFNULL(o.IsPaid, 0) = 0
                THEN o.OwnerId
            END) AS ApprovedUnpaid,

            COUNT(DISTINCT CASE
                WHEN IFNULL(o.IsAllotmentCancelled, 0) = 0
                    AND IFNULL(o.IsRejected, 0) = 0
                    AND IFNULL(o.IsApproved, 0) = 0
                THEN o.OwnerId
            END) AS PendingApproval,

            COUNT(DISTINCT CASE
                WHEN IFNULL(o.IsAllotmentCancelled, 0) = 0
                    AND IFNULL(o.IsRejected, 0) = 1
                THEN o.OwnerId
            END) AS Rejected,

            COUNT(DISTINCT CASE
                WHEN IFNULL(o.IsAllotmentCancelled, 0) = 1
                THEN o.OwnerId
            END) AS Cancelled
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
                    ->whereRaw('IFNULL(o.IsAllotmentCancelled, 0) = 0')
                    ->whereRaw('IFNULL(o.IsRejected, 0) = 0')
                    ->whereRaw('IFNULL(o.IsApproved, 0) = 1')
                    ->whereRaw('IFNULL(o.IsPaid, 0) = 1');
                break;

            case 'approved_unpaid':
                $allotmentsQuery
                    ->whereRaw('IFNULL(o.IsAllotmentCancelled, 0) = 0')
                    ->whereRaw('IFNULL(o.IsRejected, 0) = 0')
                    ->whereRaw('IFNULL(o.IsApproved, 0) = 1')
                    ->whereRaw('IFNULL(o.IsPaid, 0) = 0');
                break;

            case 'pending':
                $allotmentsQuery
                    ->whereRaw('IFNULL(o.IsAllotmentCancelled, 0) = 0')
                    ->whereRaw('IFNULL(o.IsRejected, 0) = 0')
                    ->whereRaw('IFNULL(o.IsApproved, 0) = 0');
                break;

            case 'rejected':
                $allotmentsQuery
                    ->whereRaw('IFNULL(o.IsAllotmentCancelled, 0) = 0')
                    ->whereRaw('IFNULL(o.IsRejected, 0) = 1');
                break;

            case 'cancelled':
                $allotmentsQuery
                    ->whereRaw('IFNULL(o.IsAllotmentCancelled, 0) = 1');
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Allotment Records
        |--------------------------------------------------------------------------
        */

        $allotments = $allotmentsQuery
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
            ->paginate(25)
            ->withQueryString();

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

}