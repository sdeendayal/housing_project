<?php

namespace App\Http\Controllers\MMGAY\DistrictCEO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use App\Exports\DistrictVillageSummaryExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ApplicantReportExport;

class DistrictCEOController extends Controller
{
    public function dashboard(Request $request, $phase = 'all')
    {
        $user = auth()->user();
        /*
        |--------------------------------------------------------------------------
        | Logged-in User District
        |--------------------------------------------------------------------------
        */
        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        if (!$districtId) {
            abort(404, 'District not found.');
        }
        /*
        |--------------------------------------------------------------------------
        | Phase Filter
        |--------------------------------------------------------------------------
        */
        $phase = (string) $phase;

        if ($phase !== 'all') {
            $phase = (int) $phase;

            if (!in_array($phase, [1, 2, 3], true)) {
                $phase = 1;
            }
        }

        $isAllPhase = $phase === 'all';

        /*
        |--------------------------------------------------------------------------
        | Block Filter
        |--------------------------------------------------------------------------
        */
        $blockId = $request->filled('block_id')
            ? (int) $request->block_id
            : null;

        /*
        |--------------------------------------------------------------------------
        | Phase-wise Block Dropdown
        |--------------------------------------------------------------------------
        | Blocks are resolved through BlockMaster and constrained by the
        | same district/phase/village data already used by dashboard.
        |--------------------------------------------------------------------------
        */
        $blocks = DB::table('VillageMaster as v')
            ->join('BlockMaster as b', 'b.BlockId', '=', 'v.BlockId')
            ->where('v.DistrictId', $districtId)
            ->where('v.plots', '>', 0)
            ->whereNotNull('v.BlockId')
            ->when(!$isAllPhase, function ($query) use ($phase) {
                $query->where('v.phase', $phase);
            })
            ->select(
                'b.BlockId',
                'b.BlockName'
            )
            ->distinct()
            ->orderBy('b.BlockName')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Village Filter
        |--------------------------------------------------------------------------
        */
        $villageId = $request->filled('village_id')
            ? (int) $request->village_id
            : null;

        /*
        |--------------------------------------------------------------------------
        | Dependent Village Dropdown
        |--------------------------------------------------------------------------
        |--------------------------------------------------------------------------
        */
        $villages = DB::table('VillageMaster as v')
            ->where('v.DistrictId', $districtId)
            ->where('v.plots', '>', 0)
            ->when(!$isAllPhase, function ($query) use ($phase) {
                $query->where('v.phase', $phase);
            })
            ->when($blockId, function ($query) use ($blockId) {
                $query->where('v.BlockId', $blockId);
            })
            ->select(
                'v.VillageId',
                'v.VillageName',
                'v.phase',
                'v.BlockId'
            )
            ->orderBy('v.phase')
            ->orderBy('v.VillageName')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Validate Selected Village
        |--------------------------------------------------------------------------
        */
        if (
            $blockId &&
            !$blocks->contains(function ($block) use ($blockId) {
                return (int) $block->BlockId === $blockId;
            })
        ) {
            $blockId = null;
        }

        if (
            $villageId &&
            !$villages->contains(function ($village) use ($villageId) {
                return (int) $village->VillageId === $villageId;
            })
        ) {
            $villageId = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Main Dashboard Query
        |--------------------------------------------------------------------------
        */
        $query = DB::table('ownermaster as o')
            ->join('villagemaster as v', function ($join) {
                $join->on('o.VillageId', '=', 'v.VillageId')
                    ->on('o.DistrictId', '=', 'v.DistrictId');
            })
            ->join(
                'districtmaster as d',
                'o.DistrictId',
                '=',
                'd.DistrictId'
            )
            ->leftJoin(
                'flatmaster as f',
                'o.FlatId',
                '=',
                'f.FlatId'
            )
            ->where('o.DistrictId', $districtId)
            ->where('v.DistrictId', $districtId)
            ->where('v.plots', '>', 0)

            /*
            |--------------------------------------------------------------------------
            | Apply Phase Filter Only When Specific Phase is Selected
            |--------------------------------------------------------------------------
            */
            ->when(!$isAllPhase, function ($query) use ($phase) {
                $query->where('o.Phase', $phase)
                    ->where('v.phase', $phase);
            })

            /*
            |--------------------------------------------------------------------------
            | Ensure Owner and Village Phase Match in All Phase Mode
            |--------------------------------------------------------------------------
            */
            ->when($isAllPhase, function ($query) {
                $query->whereColumn('o.Phase', 'v.phase');
            })

            /*
            |--------------------------------------------------------------------------
            | Apply Village Filter
            |--------------------------------------------------------------------------
            */
            ->when($blockId, function ($query) use ($blockId) {
                $query->where('v.BlockId', $blockId);
            })
            ->when($villageId, function ($query) use ($villageId) {
                $query->where('v.VillageId', $villageId);
            });


        /*
        |--------------------------------------------------------------------------
        | Registry Verification - OLD + NEW
        |--------------------------------------------------------------------------
        |
        | OLD records: registary.flatid IS NULL
        |   -> verify by SecondPartyMobile = OwnerMaster.MobileNo
        |
        | NEW records: registary.flatid IS NOT NULL
        |   -> verify by flatid = OwnerMaster.FlatId
        |
        | EXISTS keeps every OwnerMaster beneficiary unique even if registary
        | contains duplicate/history rows.
        |--------------------------------------------------------------------------
        */
        $registryMatchedSql = "
            (
                EXISTS (
                    SELECT 1
                    FROM registary r_old
                    WHERE r_old.flatid IS NULL
                      AND r_old.SecondPartyMobile IS NOT NULL
                      AND r_old.SecondPartyMobile <> ''
                      AND o.MobileNo IS NOT NULL
                      AND o.MobileNo <> ''
                      AND r_old.SecondPartyMobile = o.MobileNo
                )
                OR
                EXISTS (
                    SELECT 1
                    FROM registary r_new
                    WHERE r_new.flatid IS NOT NULL
                      AND r_new.flatid > 0
                      AND o.FlatId IS NOT NULL
                      AND r_new.flatid = o.FlatId
                )
            )
        ";

        /*
        |--------------------------------------------------------------------------
        | Village-wise Dashboard Data
        |--------------------------------------------------------------------------
        */
        $villageData = $query
            ->selectRaw("
            d.DistrictName,
            v.VillageId,
            v.VillageName,
            v.phase AS Phase,
            v.plots AS TotalPlots,
            v.map_pdf AS PdfFile,
            COUNT(DISTINCT o.OwnerId) AS TotalApplicants,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                THEN o.OwnerId
            END) AS TotalAllotment,

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
            END) AS PendingApproval,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsRejected = 1
                THEN o.OwnerId
            END) AS Rejected,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsAllotmentCancelled = 1
                THEN o.OwnerId
            END) AS Cancelled,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsRejected, 0) = 0
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    AND $registryMatchedSql
                THEN o.OwnerId
            END) AS RegistryMatched,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsRejected, 0) = 0
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    AND NOT $registryMatchedSql
                THEN o.OwnerId
            END) AS RegistryUnmatched,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND o.Caste = 'SC'
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END) AS SC,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND o.Caste = 'Ghumantu'
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END) AS Ghumantu,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND o.Caste = 'Widow'
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END) AS Widow,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND o.Caste IN ('General', 'Others')
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END) AS Others
        ")
            ->groupBy(
                'd.DistrictName',
                'v.VillageId',
                'v.VillageName',
                'v.phase',
                'v.plots',
                'v.map_pdf'
            )
            ->orderBy('v.phase')
            ->orderBy('v.VillageName')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Master Data Totals
        |--------------------------------------------------------------------------
        */
        $totalVillages = $villageData
            ->unique(function ($row) {
                return $row->Phase . '-' . $row->VillageId;
            })
            ->count();

        $totalPlots = (int) $villageData->sum('TotalPlots');

        $totalApplicants = (int) $villageData->sum(
            'TotalApplicants'
        );

        $totalAllotment = (int) $villageData->sum(
            'TotalAllotment'
        );

        /*
        |--------------------------------------------------------------------------
        | Allotment Status Totals
        |--------------------------------------------------------------------------
        */
        $totalPaid = (int) $villageData->sum(
            'ApprovedPaid'
        );

        $totalApprovedUnpaid = (int) $villageData->sum(
            'ApprovedUnpaid'
        );

        $totalPending = (int) $villageData->sum(
            'PendingApproval'
        );

        $totalRejected = (int) $villageData->sum(
            'Rejected'
        );

        $totalCancelled = (int) $villageData->sum(
            'Cancelled'
        );

        /*
        |--------------------------------------------------------------------------
        | Registration Totals
        |--------------------------------------------------------------------------
        */
        $totalRegistryAllotted = (int) $villageData->sum('ApprovedPaid');

        $totalRegistryMatched = (int) $villageData->sum('RegistryMatched');

        $totalRegistryUnmatched = max(
            0,
            $totalRegistryAllotted - $totalRegistryMatched
        );

        /*
        |--------------------------------------------------------------------------
        | Category Totals
        |--------------------------------------------------------------------------
        */
        $totalSC = (int) $villageData->sum('SC');

        $totalGhumantu = (int) $villageData->sum(
            'Ghumantu'
        );

        $totalWidow = (int) $villageData->sum(
            'Widow'
        );

        $totalOthers = (int) $villageData->sum(
            'Others'
        );

        /*
        |--------------------------------------------------------------------------
        | Possession Totals
        |--------------------------------------------------------------------------
        | Possession base is exactly the unique Registry Done beneficiaries.
        | OLD = mobile match where registry flatid is NULL.
        | NEW = flatid match where registry flatid is available.
        |--------------------------------------------------------------------------
        */
        $latestPossessionApplication = DB::table(
            'mmgay_possession_applications as pa_latest'
        )
            ->selectRaw(
                'pa_latest.owner_id, MAX(pa_latest.id) AS latest_id'
            )
            ->groupBy('pa_latest.owner_id');

        $possessionCountQuery = DB::table('ownermaster as po')
            ->join('villagemaster as pv', function ($join) {
                $join->on('po.VillageId', '=', 'pv.VillageId')
                    ->on('po.DistrictId', '=', 'pv.DistrictId');
            })
            ->leftJoinSub(
                $latestPossessionApplication,
                'pal',
                'pal.owner_id',
                '=',
                'po.OwnerId'
            )
            ->leftJoin(
                'mmgay_possession_applications as pa',
                'pa.id',
                '=',
                'pal.latest_id'
            )
            ->where('po.DistrictId', $districtId)
            ->where('pv.DistrictId', $districtId)
            ->where('pv.plots', '>', 0)
            ->where('po.IsApproved', 1)
            ->where('po.IsPaid', 1)
            ->whereRaw('COALESCE(po.IsRejected, 0) = 0')
            ->whereRaw('COALESCE(po.IsAllotmentCancelled, 0) = 0')
            ->where(function ($registryQuery) {
                $registryQuery
                    ->whereExists(function ($oldRegistry) {
                        $oldRegistry
                            ->selectRaw('1')
                            ->from('registary as r_old')
                            ->whereNull('r_old.flatid')
                            ->whereNotNull('r_old.SecondPartyMobile')
                            ->where('r_old.SecondPartyMobile', '<>', '')
                            ->whereColumn(
                                'r_old.SecondPartyMobile',
                                'po.MobileNo'
                            );
                    })
                    ->orWhereExists(function ($newRegistry) {
                        $newRegistry
                            ->selectRaw('1')
                            ->from('registary as r_new')
                            ->whereNotNull('r_new.flatid')
                            ->where('r_new.flatid', '>', 0)
                            ->whereColumn(
                                'r_new.flatid',
                                'po.FlatId'
                            );
                    });
            })
            ->when(!$isAllPhase, function ($query) use ($phase) {
                $query->where('po.Phase', $phase)
                    ->where('pv.phase', $phase);
            })
            ->when($isAllPhase, function ($query) {
                $query->whereColumn('po.Phase', 'pv.phase');
            })
            ->when($blockId, function ($query) use ($blockId) {
                $query->where('pv.BlockId', $blockId);
            })
            ->when($villageId, function ($query) use ($villageId) {
                $query->where('po.VillageId', $villageId);
            });

        $totalRegisteredBeneficiaries = (clone $possessionCountQuery)
            ->distinct()
            ->count('po.OwnerId');

        $totalPossessionGiven = (clone $possessionCountQuery)
            ->whereRaw(
                "LOWER(TRIM(COALESCE(pa.physical_possession_status, ''))) = ?",
                ['verified']
            )
            ->distinct()
            ->count('po.OwnerId');

        $totalPossessionPending = max(
            0,
            $totalRegisteredBeneficiaries - $totalPossessionGiven
        );

        /*
        |--------------------------------------------------------------------------
        | Dashboard Totals
        |--------------------------------------------------------------------------
        */
        $totals = [
            /*
            |----------------------------------------------------------------------
            | Master Data
            |----------------------------------------------------------------------
            */
            'totalVillages' => $totalVillages,
            'totalPlots' => $totalPlots,
            'totalApplicants' => $totalApplicants,
            'totalAllotment' => $totalAllotment,

            /*
            |----------------------------------------------------------------------
            | Allotment Status
            |----------------------------------------------------------------------
            */
            'totalPaid' => $totalPaid,
            'totalApprovedUnpaid' => $totalApprovedUnpaid,
            'totalPending' => $totalPending,
            'totalRejected' => $totalRejected,
            'totalCancelled' => $totalCancelled,

            /*
            |----------------------------------------------------------------------
            | Registration Statistics
            |----------------------------------------------------------------------
            */
            'totalRegistryAllotted' => $totalRegistryAllotted,
            'totalRegistryMatched' => $totalRegistryMatched,
            'totalRegistryUnmatched' => $totalRegistryUnmatched,

            /*
            |----------------------------------------------------------------------
            | Possession
            |----------------------------------------------------------------------
            */
            'totalRegisteredBeneficiaries' => $totalRegisteredBeneficiaries,
            'totalPossessionGiven' => $totalPossessionGiven,
            'totalPossessionPending' => $totalPossessionPending,

            /*
            |----------------------------------------------------------------------
            | Category Totals
            |----------------------------------------------------------------------
            */
            'totalSC' => $totalSC,
            'totalGhumantu' => $totalGhumantu,
            'totalWidow' => $totalWidow,
            'totalOthers' => $totalOthers,
        ];

        /*
        |--------------------------------------------------------------------------
        | AJAX Response
        |--------------------------------------------------------------------------
        */
        if ($request->ajax()) {
            return response()->json([
                'success' => true,

                'phase' => $phase,

                'phase_label' => $isAllPhase
                    ? 'All Phases'
                    : 'Phase ' . $phase,

                'filters' => [
                    'block_id' => $blockId,
                    'village_id' => $villageId,
                ],

                'totals' => $totals,

                'villageData' => $villageData,

                'blocks' => $blocks,
                'villages' => $villages,
            ]);
        }

        $reportParams = [
            'phase' => $phase,
        ];

        if ($blockId) {
            $reportParams['block_id'] = $blockId;
        }

        if ($villageId) {
            $reportParams['village_id'] = $villageId;
        }

        /*
        |--------------------------------------------------------------------------
        | Dashboard View
        |--------------------------------------------------------------------------
        */
        return view(
            'mmgay.district-ceo.dashboard',
            compact(
                'phase',
                'totals',
                'villageData',
                'villages',
                'blocks',
                'villageId',
                'blockId',
                'reportParams'
            )
        );
    }

    public function possessionList(Request $request, string $filter = 'all')
    {
        $user = auth()->user();

        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        if (!$districtId) {
            abort(404, 'District not found.');
        }

        $allowedFilters = [
            'all',
            'schedule_pending',
            'awaiting_citizen',
            'field_visit_pending',
            'possession_pending',
            'verified',
        ];

        if (!in_array($filter, $allowedFilters, true)) {
            abort(404);
        }

        $phase = (string) $request->query('phase', 'all');

        if ($phase !== 'all') {
            $phase = (int) $phase;

            if (!in_array($phase, [1, 2, 3], true)) {
                $phase = 'all';
            }
        }

        $villageId = $request->filled('village_id')
            ? (int) $request->village_id
            : null;

        $villages = DB::table('villagemaster as v')
            ->where('v.DistrictId', $districtId)
            ->where('v.plots', '>', 0)
            ->when($phase !== 'all', function ($query) use ($phase) {
                $query->where('v.phase', $phase);
            })
            ->select([
                'v.VillageId',
                'v.VillageName',
                'v.phase',
            ])
            ->orderBy('v.phase')
            ->orderBy('v.VillageName')
            ->get();

        if (
            $villageId &&
            !$villages->contains(function ($village) use ($villageId) {
                return (int) $village->VillageId === $villageId;
            })
        ) {
            $villageId = null;
        }

        $perPage = (int) $request->query('per_page', 20);

        if (!in_array($perPage, [20, 50, 100, 200], true)) {
            $perPage = 20;
        }

        $isPrint = $request->boolean('print');

        /*
        |--------------------------------------------------------------------------
        | Eligible Beneficiaries
        |--------------------------------------------------------------------------
        | These are the same Registry Matched beneficiaries used by the
        | dashboard's "Possession to be given" card.
        |--------------------------------------------------------------------------
        */
        $baseQuery = DB::table('ownermaster as o')
            ->join('villagemaster as v', function ($join) {
                $join->on('o.VillageId', '=', 'v.VillageId')
                    ->on('o.DistrictId', '=', 'v.DistrictId');
            })

            ->join(
                'flatmaster as f',
                'o.FlatId',
                '=',
                'f.FlatId'
            )

            ->leftJoinSub(
                DB::table('mmgay_possession_applications as pa_latest')
                    ->selectRaw('pa_latest.owner_id, MAX(pa_latest.id) AS latest_id')
                    ->groupBy('pa_latest.owner_id'),
                'pal',
                'pal.owner_id',
                '=',
                'o.OwnerId'
            )
            ->leftJoin(
                'mmgay_possession_applications as pa',
                'pa.id',
                '=',
                'pal.latest_id'
            )

            ->where('o.DistrictId', $districtId)
            ->where('v.DistrictId', $districtId)
            ->where('v.plots', '>', 0)

            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)

            ->whereRaw(
                'COALESCE(o.IsAllotmentCancelled, 0) = 0'
            )
            ->whereRaw(
                'COALESCE(o.IsRejected, 0) = 0'
            )

            /*
            |--------------------------------------------------------------------------
            | Unique Registry Done: OLD mobile + NEW FlatId
            |--------------------------------------------------------------------------
            */
            ->where(function ($registryQuery) {
                $registryQuery
                    ->whereExists(function ($oldRegistry) {
                        $oldRegistry
                            ->selectRaw('1')
                            ->from('registary as r_old')
                            ->whereNull('r_old.flatid')
                            ->whereNotNull('r_old.SecondPartyMobile')
                            ->where('r_old.SecondPartyMobile', '<>', '')
                            ->whereColumn(
                                'r_old.SecondPartyMobile',
                                'o.MobileNo'
                            );
                    })
                    ->orWhereExists(function ($newRegistry) {
                        $newRegistry
                            ->selectRaw('1')
                            ->from('registary as r_new')
                            ->whereNotNull('r_new.flatid')
                            ->where('r_new.flatid', '>', 0)
                            ->whereColumn(
                                'r_new.flatid',
                                'o.FlatId'
                            );
                    });
            })

            ->when(
                $phase !== 'all',
                function ($query) use ($phase) {
                    $query
                        ->where('o.Phase', $phase)
                        ->where('v.phase', $phase);
                }
            )

            ->when(
                $phase === 'all',
                function ($query) {
                    $query->whereColumn(
                        'o.Phase',
                        'v.phase'
                    );
                }
            )

            ->when(
                $villageId,
                function ($query) use ($villageId) {
                    $query->where(
                        'o.VillageId',
                        $villageId
                    );
                }
            );

        $statusExpression = "
            LOWER(TRIM(COALESCE(pa.physical_possession_status, '')))
        ";

        $counts = [
            'all' => (clone $baseQuery)
                ->distinct()
                ->count('o.OwnerId'),

            'schedule_pending' => (clone $baseQuery)
                ->where(function ($query) {
                    $query->whereNull('pa.id')
                        ->orWhereNull('pa.physical_possession_status')
                        ->orWhere('pa.physical_possession_status', '');
                })
                ->distinct()
                ->count('o.OwnerId'),

            'awaiting_citizen' => (clone $baseQuery)
                ->whereRaw($statusExpression . ' = ?', ['visit scheduled'])
                ->distinct()
                ->count('o.OwnerId'),

            'field_visit_pending' => (clone $baseQuery)
                ->whereRaw($statusExpression . ' = ?', ['slot selected'])
                ->distinct()
                ->count('o.OwnerId'),

            'verified' => (clone $baseQuery)
                ->whereRaw($statusExpression . ' = ?', ['verified'])
                ->distinct()
                ->count('o.OwnerId'),

            'possession_pending' => (clone $baseQuery)
                ->whereNotNull('pa.id')
                ->whereRaw(
                    $statusExpression . " NOT IN (?, ?, ?)",
                    [
                        'visit scheduled',
                        'slot selected',
                        'verified',
                    ]
                )
                ->distinct()
                ->count('o.OwnerId'),
        ];

        $applicationsQuery = (clone $baseQuery)
            ->select([
                'o.OwnerId',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.Relation',
                'o.MobileNo',
                'o.RegistrationNo',
                'o.PPPId',
                'o.MemberId',
                'o.FlatId',
                'o.Phase',
                'o.OwnerAddress',
                'o.Caste',
                'o.DCRemarks',

                'v.VillageName',

                'f.FlatNo',

                'pa.id as application_id',
                'pa.secure_id',
                'pa.application_number',
                'pa.status',
                'pa.remarks',
                'pa.physical_possession_status',
                'pa.meeting_slot',
                'pa.citizen_visit_date',
                'pa.visit_slot_1',
                'pa.visit_slot_2',
                'pa.visit_slot_3',
                'pa.visit_instructions',
                'pa.latitude',
                'pa.longitude',
                'pa.plot_image',
                'pa.image_capture_datetime',
                'pa.possession_certificate',
                'pa.site_engineer_file',
                'pa.verified_by',
                'pa.verified_at',
                'pa.created_at',
                'pa.updated_at',
            ])->selectSub(
                function ($registryQuery) {
                    $registryQuery
                        ->from('registary as registry')
                        ->select('registry.RegistaryNumber')
                        ->where(function ($match) {
                            $match
                                ->where(function ($oldMatch) {
                                    $oldMatch
                                        ->whereNull('registry.flatid')
                                        ->whereNotNull('registry.SecondPartyMobile')
                                        ->where('registry.SecondPartyMobile', '<>', '')
                                        ->whereColumn(
                                            'registry.SecondPartyMobile',
                                            'o.MobileNo'
                                        );
                                })
                                ->orWhere(function ($newMatch) {
                                    $newMatch
                                        ->whereNotNull('registry.flatid')
                                        ->where('registry.flatid', '>', 0)
                                        ->whereColumn(
                                            'registry.flatid',
                                            'o.FlatId'
                                        );
                                });
                        })
                        ->whereNotNull('registry.RegistaryNumber')
                        ->where('registry.RegistaryNumber', '<>', '')
                        ->orderByDesc('registry.id')
                        ->limit(1);
                },
                'RegistaryNumber'
            );

        switch ($filter) {
            case 'schedule_pending':
                $applicationsQuery->where(function ($query) {
                    $query->whereNull('pa.id')
                        ->orWhereNull('pa.physical_possession_status')
                        ->orWhere('pa.physical_possession_status', '');
                });
                break;

            case 'awaiting_citizen':
                $applicationsQuery->whereRaw(
                    $statusExpression . ' = ?',
                    ['visit scheduled']
                );
                break;

            case 'field_visit_pending':
                $applicationsQuery->whereRaw(
                    $statusExpression . ' = ?',
                    ['slot selected']
                );
                break;

            case 'possession_pending':
                $applicationsQuery
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        $statusExpression . " NOT IN (?, ?, ?)",
                        [
                            'visit scheduled',
                            'slot selected',
                            'verified',
                        ]
                    );
                break;

            case 'verified':
                $applicationsQuery->whereRaw(
                    $statusExpression . ' = ?',
                    ['verified']
                );
                break;
        }

        $applications = $applicationsQuery
            ->orderByRaw(
                'CASE WHEN pa.updated_at IS NULL THEN 1 ELSE 0 END'
            )
            ->orderByDesc('pa.updated_at')
            ->orderBy('o.OwnerName')
            ->paginate($isPrint ? 200 : $perPage)
            ->withQueryString();

        $filterLabels = [
            'all' => 'Total Eligible',
            'schedule_pending' => 'Schedule Pending',
            'awaiting_citizen' => 'Awaiting Citizen',
            'field_visit_pending' => 'Field Visit Pending',
            'possession_pending' => 'Possession Pending',
            'verified' => 'Verified',
        ];

        return view(
            'mmgay.district-ceo.possession-list',
            compact(
                'applications',
                'counts',
                'filter',
                'filterLabels',
                'phase',
                'villageId',
                'villages',
                'perPage',
                'isPrint'
            )
        );
    }

    public function exportPossessionCsv(Request $request)
    {
        $user = auth()->user();

        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        abort_unless($districtId, 404, 'District not found.');

        $filter = trim(
            (string) $request->query('filter', 'all')
        );

        $allowedFilters = [
            'all',
            'schedule_pending',
            'awaiting_citizen',
            'field_visit_pending',
            'possession_pending',
            'verified',
        ];

        if (!in_array($filter, $allowedFilters, true)) {
            $filter = 'all';
        }

        $phase = (string) $request->query('phase', 'all');

        if ($phase !== 'all') {
            $phase = (int) $phase;

            if (!in_array($phase, [1, 2, 3], true)) {
                $phase = 'all';
            }
        }

        $villageId = $request->filled('village_id')
            ? (int) $request->query('village_id')
            : null;

        $query = DB::table('ownermaster as o')
            ->join('villagemaster as v', function ($join) {
                $join->on('o.VillageId', '=', 'v.VillageId')
                    ->on('o.DistrictId', '=', 'v.DistrictId');
            })
            ->join(
                'flatmaster as f',
                'o.FlatId',
                '=',
                'f.FlatId'
            )
            ->leftJoinSub(
                DB::table('mmgay_possession_applications as pa_latest')
                    ->selectRaw('pa_latest.owner_id, MAX(pa_latest.id) AS latest_id')
                    ->groupBy('pa_latest.owner_id'),
                'pal',
                'pal.owner_id',
                '=',
                'o.OwnerId'
            )
            ->leftJoin(
                'mmgay_possession_applications as pa',
                'pa.id',
                '=',
                'pal.latest_id'
            )
            ->where('o.DistrictId', $districtId)
            ->where('v.DistrictId', $districtId)
            ->where('v.plots', '>', 0)
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereRaw(
                'COALESCE(o.IsAllotmentCancelled, 0) = 0'
            )
            ->whereRaw(
                'COALESCE(o.IsRejected, 0) = 0'
            )
            /*
            |--------------------------------------------------------------------------
            | Unique Registry Done: OLD mobile + NEW FlatId
            |--------------------------------------------------------------------------
            */
            ->where(function ($registryQuery) {
                $registryQuery
                    ->whereExists(function ($oldRegistry) {
                        $oldRegistry
                            ->selectRaw('1')
                            ->from('registary as r_old')
                            ->whereNull('r_old.flatid')
                            ->whereNotNull('r_old.SecondPartyMobile')
                            ->where('r_old.SecondPartyMobile', '<>', '')
                            ->whereColumn(
                                'r_old.SecondPartyMobile',
                                'o.MobileNo'
                            );
                    })
                    ->orWhereExists(function ($newRegistry) {
                        $newRegistry
                            ->selectRaw('1')
                            ->from('registary as r_new')
                            ->whereNotNull('r_new.flatid')
                            ->where('r_new.flatid', '>', 0)
                            ->whereColumn(
                                'r_new.flatid',
                                'o.FlatId'
                            );
                    });
            })
            ->when($phase !== 'all', function ($query) use ($phase) {
                $query
                    ->where('o.Phase', $phase)
                    ->where('v.phase', $phase);
            })
            ->when($phase === 'all', function ($query) {
                $query->whereColumn(
                    'o.Phase',
                    'v.phase'
                );
            })
            ->when($villageId, function ($query) use ($villageId) {
                $query->where(
                    'o.VillageId',
                    $villageId
                );
            });

        $statusExpression = "
        LOWER(
            TRIM(
                COALESCE(
                    pa.physical_possession_status,
                    ''
                )
            )
        )
    ";

        switch ($filter) {
            case 'schedule_pending':
                $query->where(function ($query) {
                    $query
                        ->whereNull('pa.id')
                        ->orWhereNull(
                            'pa.physical_possession_status'
                        )
                        ->orWhere(
                            'pa.physical_possession_status',
                            ''
                        );
                });
                break;

            case 'awaiting_citizen':
                $query->whereRaw(
                    $statusExpression . ' = ?',
                    ['visit scheduled']
                );
                break;

            case 'field_visit_pending':
                $query->whereRaw(
                    $statusExpression . ' = ?',
                    ['slot selected']
                );
                break;

            case 'possession_pending':
                $query
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        $statusExpression .
                        ' NOT IN (?, ?, ?)',
                        [
                            'visit scheduled',
                            'slot selected',
                            'verified',
                        ]
                    );
                break;

            case 'verified':
                $query->whereRaw(
                    $statusExpression . ' = ?',
                    ['verified']
                );
                break;
        }

        $records = $query
            ->select([
                'o.OwnerId',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.RegistrationNo',
                'o.PPPId',
                'o.MemberId',
                'o.Phase',
                'v.VillageName',
                'f.FlatNo',
                'pa.application_number',
                'pa.physical_possession_status',
                'pa.meeting_slot',
                'pa.citizen_visit_date',
                'pa.possession_date',
                'pa.verified_at',
            ])
            ->orderBy('v.VillageName')
            ->orderBy('o.OwnerName')
            ->get();

        $filename = 'possession-applications-' .
            $filter .
            '-' .
            now()->format('Y-m-d-His') .
            '.csv';

        return response()->streamDownload(
            function () use ($records) {
                $handle = fopen('php://output', 'w');

                // Excel UTF-8 support
                fwrite($handle, "\xEF\xBB\xBF");

                fputcsv($handle, [
                    'Sr. No.',
                    'Owner ID',
                    'Applicant Name',
                    'Father / Husband Name',
                    'Mobile Number',
                    'Registration Number',
                    'PPP ID',
                    'Member ID',
                    'Flat Number',
                    'Village',
                    'Phase',
                    'Application Number',
                    'Possession Status',
                    'Meeting Slot',
                    'Citizen Visit Date',
                    'Possession Date',
                    'Verified At',
                ]);

                foreach ($records as $index => $record) {
                    fputcsv($handle, [
                        $index + 1,
                        $record->OwnerId,
                        $record->OwnerName,
                        $record->FatherHusbandName,
                        $record->MobileNo,
                        $record->RegistrationNo,
                        $record->PPPId,
                        $record->MemberId,
                        $record->FlatNo,
                        $record->VillageName,
                        $record->Phase,
                        $record->application_number,
                        $record->physical_possession_status
                        ?: 'Schedule Pending',
                        $record->meeting_slot,
                        $record->citizen_visit_date,
                        $record->possession_date,
                        $record->verified_at,
                    ]);
                }

                fclose($handle);
            },
            $filename,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }
    private function formatDevelopmentDate($date): ?string
    {
        if (blank($date)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($date)
                ->format('d-m-Y h:i A');
        } catch (\Throwable $exception) {
            return (string) $date;
        }
    }

    public function siteDevelopment(Request $request, int $villageId)
    {
        $user = auth()->user();

        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        if (!$districtId) {
            return response()->json([
                'success' => false,
                'message' => 'District not found.',
                'records' => [],
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Village
        |--------------------------------------------------------------------------
        */
        $village = DB::table('villagemaster as v')
            ->where('v.VillageId', $villageId)
            ->where('v.DistrictId', $districtId)
            ->where('v.plots', '>', 0)
            ->select([
                'v.VillageId',
                'v.VillageName',
                'v.Phase',
                'v.DistrictId',
                'v.BlockId',
            ])
            ->first();

        if (!$village) {
            return response()->json([
                'success' => false,
                'message' => 'Village record not found.',
                'records' => [],
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Site Development Records
        |--------------------------------------------------------------------------
        */
        $records = DB::table('mmgay_site_developments as sd')
            ->where('sd.district_id', $districtId)
            ->where('sd.village_id', $villageId)
            ->select([
                'sd.id',
                'sd.district_id',
                'sd.block_id',
                'sd.village_id',
                'sd.phase',

                'sd.road_status',
                'sd.water_status',
                'sd.electricity_status',
                'sd.sewerage_status',

                'sd.remarks',
                'sd.updated_by',

                'sd.created_at',
                'sd.updated_at',

                'sd.road_photo',
                'sd.water_photo',
                'sd.electricity_photo',
                'sd.sewerage_photo',
            ])
            ->orderByDesc('sd.id')
            ->limit(20)
            ->get();

        $records = $records
            ->map(function ($record) {

                return [

                    'id' => (int) $record->id,

                    'district_id' => $record->district_id,
                    'block_id' => $record->block_id,
                    'village_id' => $record->village_id,
                    'phase' => $record->phase,

                    'road_status' => filled($record->road_status)
                        ? $record->road_status
                        : 'Not Started',

                    'water_status' => filled($record->water_status)
                        ? $record->water_status
                        : 'Not Started',

                    'electricity_status' => filled($record->electricity_status)
                        ? $record->electricity_status
                        : 'Not Started',

                    'sewerage_status' => filled($record->sewerage_status)
                        ? $record->sewerage_status
                        : 'Not Started',

                    'remarks' => $record->remarks ?: 'No remarks available.',

                    'updated_by' => $record->updated_by ?: '-',

                    'created_at' => $this->formatDevelopmentDate($record->created_at),

                    'updated_at' => $this->formatDevelopmentDate($record->updated_at),

                    'road_photo_url' => $this->siteDevelopmentPhotoUrl($record->road_photo),

                    'water_photo_url' => $this->siteDevelopmentPhotoUrl($record->water_photo),

                    'electricity_photo_url' => $this->siteDevelopmentPhotoUrl($record->electricity_photo),

                    'sewerage_photo_url' => $this->siteDevelopmentPhotoUrl($record->sewerage_photo),

                ];
            })
            ->values();

        return response()->json([

            'success' => true,

            'village' => [
                'id' => $village->VillageId,
                'name' => $village->VillageName,
                'phase' => $village->Phase,
            ],

            'total_records' => $records->count(),

            'records' => $records,

        ]);
    }

    private function siteDevelopmentPhotoUrl(?string $photo): ?string
    {
        if (!$photo) {
            return null;
        }

        $photo = ltrim($photo, '/');

        if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) {
            return $photo;
        }

        if (str_starts_with($photo, 'storage/')) {
            return asset($photo);
        }

        return asset('storage/' . $photo);
    }

    public function list($phase, $status)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('mmgay.login');
        }

        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        if (!$districtId) {
            abort(404, 'District not found.');
        }

        $query = DB::table('ownermaster as o')
            ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
            ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->leftJoin('socialcategorymaster as sc', 'o.Caste', '=', 'sc.CategoryId')

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

        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        if (!$districtId) {
            abort(404, 'District not found.');
        }

        $owner = DB::table('ownermaster as o')
            ->leftJoin('districtmaster as d', 'o.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('blockmaster as b', 'o.BlockId', '=', 'b.BlockId')
            ->leftJoin('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
            ->leftJoin('flatmaster as f', 'o.FlatId', '=', 'f.FlatId')
            ->leftJoin('socialcategorymaster as sc', 'o.Caste', '=', 'sc.CategoryId')
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

        DB::table('ownermaster')
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

        DB::table('ownermaster')
            ->where('OwnerId', $id)
            ->update($data);

        return back()->with('success', 'Application updated successfully.');
    }

    public function physicalPossessionDashboard(Request $request)
    {
        $user = auth()->user();

        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        $totalApplications = DB::table('mmgay_possession_applications')
            ->where('district_id', $districtId)
            ->count();

        $visitScheduled = DB::table('mmgay_possession_applications')
            ->where('district_id', $districtId)
            ->where('physical_possession_status', 'Visit Scheduled')
            ->count();

        $slotSelected = DB::table('mmgay_possession_applications')
            ->where('district_id', $districtId)
            ->where('physical_possession_status', 'Slot Selected')
            ->count();

        $siteVerified = DB::table('mmgay_possession_applications')
            ->where('district_id', $districtId)
            ->where('physical_possession_status', 'Site Verified')
            ->count();

        $verified = DB::table('mmgay_possession_applications')
            ->where('district_id', $districtId)
            ->where('physical_possession_status', 'Verified')
            ->count();

        $query = DB::table('mmgay_possession_applications')
            ->where('district_id', $districtId);

        if ($request->application_number) {
            $query->where('application_number', 'like', '%' . $request->application_number . '%');
        }

        if ($request->mobile) {
            $query->where('mobile', 'like', '%' . $request->mobile . '%');
        }

        if ($request->status) {
            $query->where('physical_possession_status', $request->status);
        }

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $recentApplications = $query
            ->latest('id')
            ->paginate(10);

        return view(
            'mmgay.district-ceo.physical-possession.dashboard',
            compact(
                'totalApplications',
                'visitScheduled',
                'slotSelected',
                'siteVerified',
                'verified',
                'recentApplications'
            )
        );
    }

    public function physicalPossessionView($secure_id)
    {
        $user = auth()->user();

        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        $application = DB::table('mmgay_possession_applications as p')

            ->leftJoin('ownermaster as o', 'o.OwnerId', '=', 'p.owner_id')
            ->leftJoin('districtmaster as d', 'd.DistrictId', '=', 'o.DistrictId')
            ->leftJoin('blockmaster as b', 'b.BlockId', '=', 'o.BlockId')
            ->leftJoin('villagemaster as v', 'v.VillageId', '=', 'o.VillageId')

            ->where('p.secure_id', $secure_id)
            ->where('p.district_id', $districtId)

            ->select(
                'p.*',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.RegistrationNo',
                'o.OwnerAddress',
                'o.PPPId',
                'o.Caste',
                'o.Remarks',
                'd.DistrictName',
                'b.BlockName',
                'v.VillageName'
            )

            ->first();

        abort_if(!$application, 404);

        $timeline = DB::table('mmgay_possession_status_logs')

            ->where('application_id', $application->id)

            ->orderBy('created_at')

            ->get();

        return view(
            'mmgay.district-ceo.physical-possession.view',
            compact(
                'application',
                'timeline'
            )
        );
    }

    public function viewPossession($secureId)
    {
        $user = auth()->user();

        $application = DB::table('physical_possession_applications as p')
            ->join('ownermaster as o', 'o.OwnerId', '=', 'p.owner_id')
            ->leftJoin('districtmaster as d', 'd.DistrictId', '=', 'o.DistrictId')
            ->leftJoin('blockmaster as b', 'b.BlockId', '=', 'o.BlockId')
            ->leftJoin('villagemaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->leftJoin('flatmaster as f', 'f.FlatId', '=', 'o.FlatId')
            ->where('p.secure_id', $secureId)
            ->select(
                'p.*',
                'o.OwnerName',
                'o.MobileNo',
                'o.RegistrationNo',
                'o.PPPId',
                'o.FatherHusbandName',
                'o.Address',
                'd.DistrictName',
                'b.BlockName',
                'v.VillageName',
                'f.FlatNo'
            )
            ->first();


        abort_if(!$application, 404);



        // Timeline
        $timeline = DB::table('physical_possession_logs')
            ->where('application_id', $application->id)
            ->orderBy('created_at', 'asc')
            ->get();



        return view(
            'mmgay.district-ceo.physical-possession.view',
            compact('application', 'timeline')
        );
    }

    private function getVillageSummaryData(
        int $districtId,
        int|string $phase,
        ?int $villageId = null
    ) {
        return DB::table('ownermaster as o')
            ->join(
                'villagemaster as v',
                function ($join) {
                    $join->on('o.VillageId', '=', 'v.VillageId')
                        ->on('o.DistrictId', '=', 'v.DistrictId');
                }
            )
            ->join(
                'districtmaster as d',
                'o.DistrictId',
                '=',
                'd.DistrictId'
            )
            ->leftJoin(
                'flatmaster as f',
                'o.FlatId',
                '=',
                'f.FlatId'
            )
            ->where('o.DistrictId', $districtId)
            ->where('v.DistrictId', $districtId)
            ->where('v.plots', '>', 0)

            ->when(
                $phase === 'all',
                function ($query) {
                    $query->whereColumn('o.Phase', 'v.phase');
                },
                function ($query) use ($phase) {
                    $query
                        ->where('o.Phase', (int) $phase)
                        ->where('v.phase', (int) $phase);
                }
            )

            ->when($villageId, function ($query) use ($villageId) {
                $query->where('v.VillageId', $villageId);
            })

            ->selectRaw("
            d.DistrictName,
            v.VillageId,
            v.VillageName,
            v.phase AS Phase,
            v.plots AS TotalPlots,

            COUNT(DISTINCT o.OwnerId) AS TotalApplicants,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                THEN o.OwnerId
            END) AS TotalAllotment,

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
            END) AS PendingApproval,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsRejected = 1
                THEN o.OwnerId
            END) AS Rejected,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsAllotmentCancelled = 1
                THEN o.OwnerId
            END) AS Cancelled,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND o.Caste = 'SC'
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END) AS SC,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND o.Caste = 'Ghumantu'
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END) AS Ghumantu,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND o.Caste = 'Widow'
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END) AS Widow,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND o.Caste IN ('General', 'Others')
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END) AS Others
        ")

            ->groupBy(
                'd.DistrictName',
                'v.VillageId',
                'v.VillageName',
                'v.phase',
                'v.plots'
            )

            ->orderBy('v.phase')
            ->orderBy('v.VillageName')
            ->get();
    }

    public function exportVillageSummaryPdf(Request $request, $phase = 'all')
    {
        $user = auth()->user();

        abort_unless($user, 401);

        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        abort_unless($districtId, 404, 'District not found.');

        if ($phase !== 'all') {
            $phase = (int) $phase;

            if (!in_array($phase, [1, 2, 3], true)) {
                $phase = 1;
            }
        }

        $villageId = $request->filled('village_id')
            ? (int) $request->village_id
            : null;

        $villageData = $this->getVillageSummaryData(
            $districtId,
            $phase,
            $villageId
        );

        $totals = [
            'totalPlots' => (int) $villageData->sum('TotalPlots'),
            'totalApplicants' => (int) $villageData->sum('TotalApplicants'),
            'totalPaid' => (int) $villageData->sum('ApprovedPaid'),
            'totalSC' => (int) $villageData->sum('SC'),
            'totalGhumantu' => (int) $villageData->sum('Ghumantu'),
            'totalWidow' => (int) $villageData->sum('Widow'),
            'totalOthers' => (int) $villageData->sum('Others'),
            'totalAllotment' => (int) $villageData->sum('TotalAllotment'),
        ];

        $districtName = $user->district_name;

        $pdf = Pdf::loadView(
            'mmgay.district-ceo.exports.village-summary-pdf',
            compact(
                'phase',
                'villageData',
                'totals',
                'districtName'
            )
        )->setPaper('a4', 'landscape');

        $phaseLabel = $phase === 'all'
            ? 'all-phases'
            : 'phase-' . $phase;

        return $pdf->download(
            'village-summary-' . $phaseLabel . '.pdf'
        );
    }

    public function exportVillageSummaryExcel(Request $request, $phase = 'all')
    {
        $user = auth()->user();

        abort_unless($user, 401);

        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        abort_unless($districtId, 404, 'District not found.');

        if ($phase !== 'all') {
            $phase = (int) $phase;

            if (!in_array($phase, [1, 2, 3], true)) {
                $phase = 1;
            }
        }

        $villageId = $request->filled('village_id')
            ? (int) $request->village_id
            : null;

        $villageData = $this->getVillageSummaryData(
            $districtId,
            $phase,
            $villageId
        );

        $phaseLabel = $phase === 'all'
            ? 'all-phases'
            : 'phase-' . $phase;

        return Excel::download(
            new DistrictVillageSummaryExport($villageData),
            'village-summary-' . $phaseLabel . '.xlsx'
        );
    }

    public function report(Request $request, string $type)
    {
        $allowedTypes = [
            'villages',
            'plots',
            'applicants',
            'allotments',
        ];

        abort_unless(
            in_array($type, $allowedTypes, true),
            404
        );

        $user = auth()->user();

        if (!$user) {
            return redirect()->route('mmgay.login');
        }
        /*
        |--------------------------------------------------------------------------
        | District
        |--------------------------------------------------------------------------
        */
        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        if (!$districtId) {
            abort(404, 'District not found.');
        }
        /*
        |--------------------------------------------------------------------------
        | Phase Filter
        |--------------------------------------------------------------------------
        */
        $phaseInput = strtolower(
            trim((string) $request->query('phase', 'all'))
        );

        if ($phaseInput === 'all') {
            $phase = 'all';
        } else {
            $phase = (int) $phaseInput;

            if (!in_array($phase, [1, 2, 3], true)) {
                $phase = 'all';
            }
        }

        $isAllPhase = $phase === 'all';

        /*
        |--------------------------------------------------------------------------
        | Other Filters
        |--------------------------------------------------------------------------
        |
        | Dashboard se village filter alag naming ke saath aa sakta hai.
        | Isliye village_id, villageId aur village tino support kiye gaye hain.
        |
        */
        $villageInput = $request->query(
            'village_id',
            $request->query(
                'villageId',
                $request->query('village')
            )
        );

        $villageId = filled($villageInput)
            ? (int) $villageInput
            : null;

        $status = $request->query('status');

        $allowedStatuses = [
            'approved_paid',
            'approved_unpaid',
            'pending',
            'rejected',
            'cancelled',
            'registry_done',
            'registry_pending',
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = null;
        }

        $caste = $request->query('caste');

        $allowedCastes = [
            'SC',
            'Ghumantu',
            'Widow',
            'Others',
        ];

        if (!in_array($caste, $allowedCastes, true)) {
            $caste = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Village Dropdown
        |--------------------------------------------------------------------------
        */
        $villages = DB::table('villagemaster as v')
            ->where('v.DistrictId', $districtId)
            ->where('v.plots', '>', 0)
            ->when(!$isAllPhase, function ($query) use ($phase) {
                $query->where('v.phase', $phase);
            })
            ->select(
                'v.VillageId',
                'v.VillageName',
                'v.phase'
            )
            ->orderBy('v.phase')
            ->orderBy('v.VillageName')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Validate Village
        |--------------------------------------------------------------------------
        */
        if (
            $villageId &&
            !$villages->contains(function ($village) use ($villageId) {
                return (int) $village->VillageId === $villageId;
            })
        ) {
            $villageId = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Registry mobile summary
        |--------------------------------------------------------------------------
        | One row per mobile prevents duplicate Owner rows when the registry table
        | contains multiple entries for the same mobile number.
        */
        $registrySummary = $this->registryMobileSummaryQuery();

        /*
        |--------------------------------------------------------------------------
        | Main Report Query
        |--------------------------------------------------------------------------
        */
        $query = DB::table('ownermaster as o')

            ->join('villagemaster as v', function ($join) {
                $join->on(
                    'o.VillageId',
                    '=',
                    'v.VillageId'
                )
                    ->on(
                        'o.DistrictId',
                        '=',
                        'v.DistrictId'
                    )
                    ->on(
                        'o.Phase',
                        '=',
                        'v.phase'
                    );
            })

            ->join(
                'districtmaster as d',
                'o.DistrictId',
                '=',
                'd.DistrictId'
            )

            ->leftJoin(
                'flatmaster as f',
                'o.FlatId',
                '=',
                'f.FlatId'
            )

            ->leftJoinSub(
                $registrySummary,
                'r',
                function ($join) {
                    $join->on(
                        'r.SecondPartyMobile',
                        '=',
                        'o.MobileNo'
                    );
                }
            )

            ->where('o.DistrictId', $districtId)
            ->where('v.DistrictId', $districtId)
            ->where('v.plots', '>', 0)

            /*
            |--------------------------------------------------------------------------
            | Phase
            |--------------------------------------------------------------------------
            */
            ->when(!$isAllPhase, function ($query) use ($phase) {
                $query->where('o.Phase', $phase)
                    ->where('v.phase', $phase);
            })

            /*
            |--------------------------------------------------------------------------
            | Village
            |--------------------------------------------------------------------------
            */
            ->when($villageId, function ($query) use ($villageId) {
                $query->where('v.VillageId', $villageId);
            })

            /*
            |--------------------------------------------------------------------------
            | Caste
            |--------------------------------------------------------------------------
            */
            ->when($caste, function ($query) use ($caste) {
                if ($caste === 'Others') {
                    $query->where(function ($q) {
                        $q->whereNotIn('o.Caste', ['SC', 'Ghumantu', 'Widow'])
                            ->orWhereNull('o.Caste');
                    });

                    return;
                }

                $query->where('o.Caste', $caste);
            });

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */
        switch ($status) {
            case 'approved_paid':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw(
                        'COALESCE(o.IsAllotmentCancelled, 0) = 0'
                    );
                break;

            case 'approved_unpaid':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsApproved', 1)
                    ->whereRaw('COALESCE(o.IsPaid, 0) = 0')
                    ->whereRaw(
                        'COALESCE(o.IsAllotmentCancelled, 0) = 0'
                    );
                break;

            case 'pending':
                $query
                    ->whereNotNull('f.FlatId')
                    ->whereRaw('COALESCE(o.IsApproved, 0) = 0')
                    ->whereRaw('COALESCE(o.IsPaid, 0) = 0')
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw(
                        'COALESCE(o.IsAllotmentCancelled, 0) = 0'
                    );
                break;

            case 'rejected':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsRejected', 1);
                break;

            case 'cancelled':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsAllotmentCancelled', 1);
                break;

            case 'registry_done':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0')
                    ->whereNotNull('o.MobileNo')
                    ->where('o.MobileNo', '<>', '')
                    ->whereNotNull('r.SecondPartyMobile');
                break;

            case 'registry_pending':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0')
                    ->where(function ($query) {
                        $query
                            ->whereNull('o.MobileNo')
                            ->orWhere('o.MobileNo', '')
                            ->orWhereNull('r.SecondPartyMobile');
                    });
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Village-wise Count Data
        |--------------------------------------------------------------------------
        */
        $reportData = $query
            ->selectRaw("
            d.DistrictName,
            v.VillageId,
            v.VillageName,
            v.phase AS Phase,
            v.plots AS TotalPlots,
            v.map_pdf AS PdfFile,

            COUNT(DISTINCT o.OwnerId) AS TotalApplicants,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                THEN o.OwnerId
            END) AS TotalAllotment,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(
                        o.IsAllotmentCancelled,
                        0
                    ) = 0
                THEN o.OwnerId
            END) AS ApprovedPaid,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND COALESCE(o.IsPaid, 0) = 0
                    AND COALESCE(
                        o.IsAllotmentCancelled,
                        0
                    ) = 0
                THEN o.OwnerId
            END) AS ApprovedUnpaid,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND COALESCE(o.IsApproved, 0) = 0
                    AND COALESCE(o.IsPaid, 0) = 0
                    AND COALESCE(o.IsRejected, 0) = 0
                    AND COALESCE(
                        o.IsAllotmentCancelled,
                        0
                    ) = 0
                THEN o.OwnerId
            END) AS PendingApproval,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsRejected = 1
                THEN o.OwnerId
            END) AS Rejected,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsAllotmentCancelled = 1
                THEN o.OwnerId
            END) AS Cancelled,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsRejected, 0) = 0
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    AND o.MobileNo IS NOT NULL
                    AND o.MobileNo <> ''
                    AND r.SecondPartyMobile IS NOT NULL
                THEN o.OwnerId
            END) AS RegistryDone,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsRejected, 0) = 0
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    AND (
                        o.MobileNo IS NULL
                        OR o.MobileNo = ''
                        OR r.SecondPartyMobile IS NULL
                    )
                THEN o.OwnerId
            END) AS RegistryPending,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND o.Caste = 'SC'
                    AND COALESCE(
                        o.IsAllotmentCancelled,
                        0
                    ) = 0
                THEN o.OwnerId
            END) AS SC,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND o.Caste = 'Ghumantu'
                    AND COALESCE(
                        o.IsAllotmentCancelled,
                        0
                    ) = 0
                THEN o.OwnerId
            END) AS Ghumantu,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND o.Caste = 'Widow'
                    AND COALESCE(
                        o.IsAllotmentCancelled,
                        0
                    ) = 0
                THEN o.OwnerId
            END) AS Widow,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND o.Caste IN (
                        'General',
                        'Others'
                    )
                    AND COALESCE(
                        o.IsAllotmentCancelled,
                        0
                    ) = 0
                THEN o.OwnerId
            END) AS Others,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsRejected, 0) = 0
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    AND o.MobileNo IS NOT NULL
                    AND o.MobileNo <> ''
                    AND r.SecondPartyMobile IS NOT NULL
                THEN o.OwnerId
            END) AS Possession
        ")
            ->groupBy(
                'd.DistrictName',
                'v.VillageId',
                'v.VillageName',
                'v.phase',
                'v.plots',
                'v.map_pdf'
            )
            ->orderBy('v.phase')
            ->orderBy('v.VillageName')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Grand Totals
        |--------------------------------------------------------------------------
        */
        $totals = [
            'totalVillages' => $reportData->count(),

            'totalPlots' => (int) $reportData->sum(
                'TotalPlots'
            ),

            'totalApplicants' => (int) $reportData->sum(
                'TotalApplicants'
            ),

            'totalAllotment' => (int) $reportData->sum(
                'TotalAllotment'
            ),

            'approvedPaid' => (int) $reportData->sum(
                'ApprovedPaid'
            ),

            'approvedUnpaid' => (int) $reportData->sum(
                'ApprovedUnpaid'
            ),

            'pending' => (int) $reportData->sum(
                'PendingApproval'
            ),

            'rejected' => (int) $reportData->sum(
                'Rejected'
            ),

            'cancelled' => (int) $reportData->sum(
                'Cancelled'
            ),

            'registryDone' => (int) $reportData->sum(
                'RegistryDone'
            ),

            'registryPending' => (int) $reportData->sum(
                'RegistryPending'
            ),

            'sc' => (int) $reportData->sum('SC'),

            'ghumantu' => (int) $reportData->sum(
                'Ghumantu'
            ),

            'widow' => (int) $reportData->sum(
                'Widow'
            ),

            'others' => (int) $reportData->sum(
                'Others'
            ),

            'totalPossession' => (int) $reportData->sum(
                'Possession'
            ),
        ];

        /*
        |--------------------------------------------------------------------------
        | Preserve Active Filters
        |--------------------------------------------------------------------------
        */
        $reportParams = array_filter(
            [
                'phase' => $phase,
                'village_id' => $villageId,
                'status' => $status,
                'caste' => $caste,
            ],
            static fn($value) => $value !== null && $value !== ''
        );

        return view(
            'mmgay.district-ceo.report',
            compact(
                'type',
                'phase',
                'isAllPhase',
                'villageId',
                'status',
                'caste',
                'villages',
                'reportData',
                'totals',
                'reportParams'
            )
        );
    }

    public function applicantReport(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('mmgay.login');
        }

        /*
        |--------------------------------------------------------------------------
        | District
        |--------------------------------------------------------------------------
        */
        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        abort_unless($districtId, 404, 'District not found.');

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */
        $phase = (string) $request->query('phase', 'all');

        if ($phase !== 'all') {
            $phase = (int) $phase;

            if (!in_array($phase, [1, 2, 3], true)) {
                $phase = 'all';
            }
        }

        $isAllPhase = $phase === 'all';

        $villageId = $request->filled('village_id')
            ? (int) $request->query('village_id')
            : null;

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        |
        | Applicants card = all_applicants
        | बाकी सभी cards actual allotted applicants (FlatMaster matched) पर हैं।
        |--------------------------------------------------------------------------
        */
        $status = trim(
            (string) $request->query('status', 'all_applicants')
        );

        $allowedStatuses = [
            'all_applicants',
            'allotted',
            'approved_paid',
            'approved_unpaid',
            'pending',
            'rejected',
            'cancelled',
            'registry_allotted',
            'registry_done',
            'registry_pending',
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'all_applicants';
        }

        $caste = trim(
            (string) $request->query('caste', '')
        );

        $allowedCastes = [
            'SC',
            'Ghumantu',
            'Widow',
            'Others',
        ];

        if (!in_array($caste, $allowedCastes, true)) {
            $caste = null;
        }

        $search = trim(
            (string) $request->query('search', '')
        );

        $perPage = (int) $request->query('per_page', 50);

        if (!in_array($perPage, [25, 50, 100, 200], true)) {
            $perPage = 50;
        }

        /*
        |--------------------------------------------------------------------------
        | Villages Dropdown
        |--------------------------------------------------------------------------
        */
        $villages = DB::table('villagemaster as v')
            ->where('v.DistrictId', $districtId)
            ->where('v.plots', '>', 0)
            ->when(!$isAllPhase, function ($query) use ($phase) {
                $query->where('v.phase', $phase);
            })
            ->select(
                'v.VillageId',
                'v.VillageName',
                'v.phase'
            )
            ->orderBy('v.phase')
            ->orderBy('v.VillageName')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Validate Village
        |--------------------------------------------------------------------------
        */
        if (
            $villageId &&
            !$villages->contains(function ($village) use ($villageId) {
                return (int) $village->VillageId === $villageId;
            })
        ) {
            $villageId = null;
        }

        $registrySummary = $this->registryMobileSummaryQuery();

        /*
        |--------------------------------------------------------------------------
        | Base Query — Same Logic As Dashboard
        |--------------------------------------------------------------------------
        */
        $baseQuery = DB::table('ownermaster as o')
            ->join('villagemaster as v', function ($join) {
                $join->on('o.VillageId', '=', 'v.VillageId')
                    ->on('o.DistrictId', '=', 'v.DistrictId')
                    ->on('o.Phase', '=', 'v.phase');
            })
            ->join(
                'districtmaster as d',
                'o.DistrictId',
                '=',
                'd.DistrictId'
            )
            ->leftJoin(
                'flatmaster as f',
                'o.FlatId',
                '=',
                'f.FlatId'
            )
            ->leftJoinSub(
                $registrySummary,
                'rg',
                function ($join) {
                    $join->on(
                        'rg.SecondPartyMobile',
                        '=',
                        'o.MobileNo'
                    );
                }
            )
            ->where('o.DistrictId', $districtId)
            ->where('v.DistrictId', $districtId)
            ->where('v.plots', '>', 0)

            /*
            |--------------------------------------------------------------------------
            | Phase Filter — Same As Dashboard
            |--------------------------------------------------------------------------
            */
            ->when(!$isAllPhase, function ($query) use ($phase) {
                $query->where('o.Phase', $phase)
                    ->where('v.phase', $phase);
            })
            ->when($isAllPhase, function ($query) {
                $query->whereColumn('o.Phase', 'v.phase');
            })

            /*
            |--------------------------------------------------------------------------
            | Village Filter
            |--------------------------------------------------------------------------
            */
            ->when($villageId, function ($query) use ($villageId) {
                $query->where('v.VillageId', $villageId);
            })

            /*
            |--------------------------------------------------------------------------
            | Caste Filter
            |--------------------------------------------------------------------------
            */
            ->when($caste, function ($query) use ($caste) {
                if ($caste === 'Others') {
                    $query->where(function ($q) {
                        $q->whereNotIn('o.Caste', ['SC', 'Ghumantu', 'Widow'])
                            ->orWhereNull('o.Caste');
                    });

                    return;
                }

                $query->where('o.Caste', $caste);
            })

            /*
            |--------------------------------------------------------------------------
            | Search Filter
            |--------------------------------------------------------------------------
            */
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('o.OwnerName', 'like', "%{$search}%")
                        ->orWhere(
                            'o.FatherHusbandName',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'o.RegistrationNo',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'o.MobileNo',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'o.PPPId',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'o.MemberId',
                            'like',
                            "%{$search}%"
                        );
                });
            });

        /*
        |--------------------------------------------------------------------------
        | Status Card Counts — Exact Dashboard Conditions
        |--------------------------------------------------------------------------
        */
        $statusCounts = (clone $baseQuery)
            ->selectRaw("
            COUNT(DISTINCT o.OwnerId)
                AS totalApplicants,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                THEN o.OwnerId
            END)
                AS totalAllotted,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END)
                AS totalApprovedPaid,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND COALESCE(o.IsPaid, 0) = 0
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END)
                AS totalApprovedUnpaid,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND COALESCE(o.IsApproved, 0) = 0
                    AND COALESCE(o.IsPaid, 0) = 0
                    AND COALESCE(o.IsRejected, 0) = 0
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END)
                AS totalPending,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsRejected = 1
                THEN o.OwnerId
            END)
                AS totalRejected,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsAllotmentCancelled = 1
                THEN o.OwnerId
            END)
                AS totalCancelled,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                THEN o.OwnerId
            END)
                AS totalRegistryAllotted,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    AND o.MobileNo IS NOT NULL
                    AND o.MobileNo <> ''
                    AND EXISTS (
                        SELECT 1
                        FROM registary AS registry_done
                        WHERE registry_done.SecondPartyMobile = o.MobileNo
                    )
                THEN o.OwnerId
            END)
                AS totalRegistryDone,

            COUNT(DISTINCT CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    AND (
                        o.MobileNo IS NULL
                        OR o.MobileNo = ''
                        OR NOT EXISTS (
                            SELECT 1
                            FROM registary AS registry_pending
                            WHERE registry_pending.SecondPartyMobile = o.MobileNo
                        )
                    )
                THEN o.OwnerId
            END)
                AS totalRegistryPending
        ")
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Report Query
        |--------------------------------------------------------------------------
        */
        $query = clone $baseQuery;

        switch ($status) {
            case 'all_applicants':
                /*
                 * सभी filtered applicants.
                 */
                break;

            case 'allotted':
                $query->whereNotNull('f.FlatId');
                break;

            case 'approved_paid':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw(
                        'COALESCE(o.IsAllotmentCancelled, 0) = 0'
                    );
                break;

            case 'approved_unpaid':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsApproved', 1)
                    ->whereRaw('COALESCE(o.IsPaid, 0) = 0')
                    ->whereRaw(
                        'COALESCE(o.IsAllotmentCancelled, 0) = 0'
                    );
                break;

            case 'pending':
                $query
                    ->whereNotNull('f.FlatId')
                    ->whereRaw('COALESCE(o.IsApproved, 0) = 0')
                    ->whereRaw('COALESCE(o.IsPaid, 0) = 0')
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw(
                        'COALESCE(o.IsAllotmentCancelled, 0) = 0'
                    );
                break;

            case 'rejected':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsRejected', 1);
                break;

            case 'cancelled':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsAllotmentCancelled', 1);
                break;

            case 'registry_allotted':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw(
                        'COALESCE(o.IsAllotmentCancelled, 0) = 0'
                    );
                break;

            case 'registry_done':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0')
                    ->whereNotNull('o.MobileNo')
                    ->where('o.MobileNo', '<>', '')
                    ->whereNotNull('rg.SecondPartyMobile');
                break;

            case 'registry_pending':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0')
                    ->where(function ($query) {
                        $query
                            ->whereNull('o.MobileNo')
                            ->orWhere('o.MobileNo', '')
                            ->orWhereNull('rg.SecondPartyMobile');
                    });
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Applicants
        |--------------------------------------------------------------------------
        */
        $applicants = $query
            ->select([
                'o.OwnerId',
                'o.secure_id',
                'o.RegistrationNo',
                'o.OwnerName',
                'o.Relation',
                'o.FatherHusbandName',
                'o.Gender',
                'o.OwnerAddress',
                'o.PPPId',
                'o.MemberId',
                DB::raw("CASE
                    WHEN TRIM(LOWER(COALESCE(o.Caste, ''))) = 'sc' THEN 'SC'
                    WHEN TRIM(LOWER(COALESCE(o.Caste, ''))) = 'ghumantu' THEN 'Ghumantu'
                    WHEN TRIM(LOWER(COALESCE(o.Caste, ''))) = 'widow' THEN 'Widow'
                    ELSE 'Others'
                END AS Caste"),
                'o.MobileNo',
                'o.Phase',
                'o.FlatId',
                'o.IsApproved',
                'o.IsRejected',
                'o.IsPaid',
                'o.IsPaymentApproved',
                'o.IsAllotmentCancelled',
                'o.Remarks',
                'o.DCRemarks',
                'o.CreatedDate',
                'v.VillageName',
                'f.FlatNo',
                'rg.RegistaryNumber',
                'rg.RegistaryDate',
            ])
            ->selectRaw("
            CASE
                WHEN o.IsAllotmentCancelled = 1
                    THEN 'Cancelled'

                WHEN o.IsRejected = 1
                    THEN 'Rejected'

                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    THEN 'Approved & Paid'

                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND COALESCE(o.IsPaid, 0) = 0
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    THEN 'Approved & Unpaid'

                ELSE 'Yet to be Approved'
            END AS ApplicantStatus
        ")
            ->selectRaw("
            CASE
                WHEN f.FlatId IS NOT NULL
                    THEN 'Allotted'

                ELSE 'Not Allotted'
            END AS AllotmentStatus
        ")
            ->selectRaw("
            CASE
                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    AND o.MobileNo IS NOT NULL
                    AND o.MobileNo <> ''
                    AND EXISTS (
                        SELECT 1
                        FROM registary AS registry_status
                        WHERE registry_status.SecondPartyMobile = o.MobileNo
                    )
                    THEN 'Registry Done'

                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    THEN 'Registry Pending'

                ELSE 'Not Applicable'
            END AS RegistryStatus
        ")
            ->selectRaw("
            CASE
                WHEN o.IsAllotmentCancelled = 1
                    THEN 'Allotment Cancelled'

                WHEN o.IsRejected = 1
                    THEN COALESCE(
                        NULLIF(o.DCRemarks, ''),
                        NULLIF(o.Remarks, ''),
                        'Application Rejected'
                    )

                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    THEN 'Application approved and payment completed'

                WHEN f.FlatId IS NOT NULL
                    AND o.IsApproved = 1
                    AND COALESCE(o.IsPaid, 0) = 0
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    THEN 'Application approved, payment pending'

                ELSE COALESCE(
                    NULLIF(o.DCRemarks, ''),
                    NULLIF(o.Remarks, ''),
                    'Approval pending'
                )
            END AS StatusRemark
        ")
            ->distinct()
            ->orderBy('o.Phase')
            ->orderBy('v.VillageName')
            ->orderBy('o.OwnerId')
            ->paginate($perPage)
            ->withQueryString();

        return view(
            'mmgay.district-ceo.reports.applicants',
            compact(
                'phase',
                'villageId',
                'status',
                'caste',
                'search',
                'perPage',
                'villages',
                'applicants',
                'statusCounts'
            )
        );
    }

    public function printApplicantReport(Request $request)
    {
        $user = auth()->user();

        abort_unless($user, 401);

        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        abort_unless($districtId, 404, 'District not found.');

        $phase = in_array(
            (string) $request->query('phase', 'all'),
            ['all', '1', '2', '3'],
            true
        )
            ? (string) $request->query('phase', 'all')
            : 'all';

        $villageId = $request->filled('village_id')
            ? (int) $request->query('village_id')
            : null;

        $status = $request->filled('status')
            ? trim((string) $request->query('status'))
            : null;

        $caste = $request->filled('caste')
            ? trim((string) $request->query('caste'))
            : null;

        $search = trim((string) $request->query('search', ''));

        $perPage = 3000;

        $query = $this->applicantReportQuery(
            districtId: (int) $districtId,
            phase: $phase,
            villageId: $villageId,
            status: $status,
            caste: $caste,
            search: $search
        );

        // IMPORTANT:
        // Do not order by v.phase here if the base query uses DISTINCT.
        $applicants = $query
            ->orderBy('o.OwnerId')
            ->paginate($perPage)
            ->withQueryString();

        return view(
            'mmgay.district-ceo.reports.applicants-print',
            [
                'applicants' => $applicants,
                'districtName' => $user->district_name,
                'phase' => $phase,
                'villageId' => $villageId,
                'status' => $status,
                'caste' => $caste,
                'search' => $search,
            ]
        );
    }

    private function applicantReportQuery(
        int $districtId,
        string|int $phase = 'all',
        ?int $villageId = null,
        ?string $status = null,
        ?string $caste = null,
        string $search = ''
    ) {
        $query = DB::table('ownermaster as o')
            ->join('villagemaster as v', function ($join) {
                $join->on('o.VillageId', '=', 'v.VillageId')
                    ->on('o.DistrictId', '=', 'v.DistrictId')
                    ->on('o.Phase', '=', 'v.phase');
            })
            ->leftJoin(
                'flatmaster as f',
                'o.FlatId',
                '=',
                'f.FlatId'
            )
            ->where('o.DistrictId', $districtId)
            ->where('v.DistrictId', $districtId)

            /*
            |--------------------------------------------------------------------------
            | Phase Filter
            |--------------------------------------------------------------------------
            */
            ->when(
                $phase !== 'all',
                function ($query) use ($phase) {
                    $query
                        ->where('o.Phase', (int) $phase)
                        ->where('v.phase', (int) $phase);
                },
                function ($query) {
                    $query->whereColumn('o.Phase', 'v.phase');
                }
            )

            /*
            |--------------------------------------------------------------------------
            | Village Filter
            |--------------------------------------------------------------------------
            */
            ->when($villageId, function ($query) use ($villageId) {
                $query->where('o.VillageId', $villageId);
            })

            /*
            |--------------------------------------------------------------------------
            | Caste Filter
            |--------------------------------------------------------------------------
            */
            ->when($caste, function ($query) use ($caste) {
                if ($caste === 'Others') {
                    $query->where(function ($q) {
                        $q->whereNotIn('o.Caste', ['SC', 'Ghumantu', 'Widow'])
                            ->orWhereNull('o.Caste');
                    });

                    return;
                }

                $query->where('o.Caste', $caste);
            })

            /*
            |--------------------------------------------------------------------------
            | Search Filter
            |--------------------------------------------------------------------------
            */
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('o.OwnerName', 'like', "%{$search}%")
                        ->orWhere(
                            'o.FatherHusbandName',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'o.RegistrationNo',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'o.MobileNo',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'o.PPPId',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'v.VillageName',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'f.FlatNo',
                            'like',
                            "%{$search}%"
                        );
                });
            });

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */
        switch ($status) {
            case 'allotted':
                $query
                    ->whereNotNull('f.FlatId')
                    ->whereNotNull('f.FlatNo')
                    ->whereRaw("TRIM(f.FlatNo) <> ''")
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0');
                break;

            case 'approved_paid':
                $query
                    ->whereNotNull('f.FlatId')
                    ->whereNotNull('f.FlatNo')
                    ->whereRaw("TRIM(f.FlatNo) <> ''")
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0');
                break;

            case 'approved_unpaid':
                $query
                    ->whereNotNull('f.FlatId')
                    ->whereNotNull('f.FlatNo')
                    ->whereRaw("TRIM(f.FlatNo) <> ''")
                    ->where('o.IsApproved', 1)
                    ->whereRaw('COALESCE(o.IsPaid, 0) = 0')
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0');
                break;

            case 'pending':
                $query
                    ->whereRaw('COALESCE(o.IsApproved, 0) = 0')
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0');
                break;

            case 'rejected':
                $query->where('o.IsRejected', 1);
                break;

            case 'cancelled':
                $query->where('o.IsAllotmentCancelled', 1);
                break;

            case 'registry_done':
                $query
                    ->whereNotNull('f.FlatId')
                    ->whereNotNull('f.FlatNo')
                    ->whereRaw("TRIM(f.FlatNo) <> ''")
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0')
                    ->whereNotNull('o.MobileNo')
                    ->where('o.MobileNo', '<>', '')
                    ->whereExists(function ($registryQuery) {
                        $registryQuery
                            ->selectRaw('1')
                            ->from('registary as r')
                            ->whereColumn(
                                'r.SecondPartyMobile',
                                'o.MobileNo'
                            );
                    });
                break;

            case 'registry_pending':
                $query
                    ->whereNotNull('f.FlatId')
                    ->whereNotNull('f.FlatNo')
                    ->whereRaw("TRIM(f.FlatNo) <> ''")
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0')
                    ->where(function ($registryQuery) {
                        $registryQuery
                            ->whereNull('o.MobileNo')
                            ->orWhere('o.MobileNo', '')
                            ->orWhereNotExists(function ($subQuery) {
                                $subQuery
                                    ->selectRaw('1')
                                    ->from('registary as r')
                                    ->whereColumn(
                                        'r.SecondPartyMobile',
                                        'o.MobileNo'
                                    );
                            });
                    });
                break;

            case 'sc':
                $query
                    ->whereNotNull('f.FlatId')
                    ->whereNotNull('f.FlatNo')
                    ->whereRaw("TRIM(f.FlatNo) <> ''")
                    ->where('o.Caste', 'SC')
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0');
                break;

            case 'ghumantu':
                $query
                    ->whereNotNull('f.FlatId')
                    ->whereNotNull('f.FlatNo')
                    ->whereRaw("TRIM(f.FlatNo) <> ''")
                    ->where('o.Caste', 'Ghumantu')
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0');
                break;

            case 'widow':
                $query
                    ->whereNotNull('f.FlatId')
                    ->whereNotNull('f.FlatNo')
                    ->whereRaw("TRIM(f.FlatNo) <> ''")
                    ->where('o.Caste', 'Widow')
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0');
                break;

            case 'others':
                $query
                    ->whereNotNull('f.FlatId')
                    ->whereNotNull('f.FlatNo')
                    ->whereRaw("TRIM(f.FlatNo) <> ''")
                    ->whereIn('o.Caste', ['General', 'Others'])
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0');
                break;
        }

        return $query
            ->select([
                'o.OwnerId',
                'o.RegistrationNo',
                'o.OwnerName',
                'o.Relation',
                'o.FatherHusbandName',
                'o.Gender',
                DB::raw("CASE
                    WHEN TRIM(LOWER(COALESCE(o.Caste, ''))) = 'sc' THEN 'SC'
                    WHEN TRIM(LOWER(COALESCE(o.Caste, ''))) = 'ghumantu' THEN 'Ghumantu'
                    WHEN TRIM(LOWER(COALESCE(o.Caste, ''))) = 'widow' THEN 'Widow'
                    ELSE 'Others'
                END AS Caste"),
                'o.MobileNo',
                'o.PPPId',
                'o.Phase',
                'o.OwnerAddress',
                'o.FlatId',
                'o.IsApproved',
                'o.IsRejected',
                'o.IsPaid',
                'o.IsPaymentApproved',
                'o.IsAllotmentCancelled',
                'o.Remarks',
                'o.DCRemarks',
                'v.VillageName',
                'f.FlatNo',
            ])

            /*
            |--------------------------------------------------------------------------
            | Allotment Status
            |--------------------------------------------------------------------------
            */
            ->selectRaw("
            CASE
                WHEN COALESCE(o.IsAllotmentCancelled, 0) = 1
                    THEN 'Cancelled'

                WHEN f.FlatId IS NOT NULL
                    AND f.FlatNo IS NOT NULL
                    AND TRIM(f.FlatNo) <> ''
                    THEN 'Allotted'

                ELSE 'Not Allotted'
            END AS AllotmentStatus
        ")

            /*
            |--------------------------------------------------------------------------
            | Applicant Status
            |--------------------------------------------------------------------------
            */
            ->selectRaw("
            CASE
                WHEN COALESCE(o.IsAllotmentCancelled, 0) = 1
                    THEN 'Cancelled'

                WHEN COALESCE(o.IsRejected, 0) = 1
                    THEN 'Rejected'

                WHEN COALESCE(o.IsApproved, 0) = 1
                    AND COALESCE(o.IsPaid, 0) = 1
                    THEN 'Approved & Paid'

                WHEN COALESCE(o.IsApproved, 0) = 1
                    AND COALESCE(o.IsPaid, 0) = 0
                    THEN 'Approved & Unpaid'

                ELSE 'Yet to be Approved'
            END AS ApplicantStatus
        ")

            /*
            |--------------------------------------------------------------------------
            | Payment Status
            |--------------------------------------------------------------------------
            */
            ->selectRaw("
            CASE
                WHEN COALESCE(o.IsAllotmentCancelled, 0) = 1
                    THEN 'Not Applicable'

                WHEN COALESCE(o.IsPaid, 0) = 1
                    THEN 'Paid'

                WHEN COALESCE(o.IsApproved, 0) = 1
                    THEN 'Unpaid'

                ELSE 'Not Applicable'
            END AS PaymentStatus
        ")

            /*
            |--------------------------------------------------------------------------
            | Registry Status
            |--------------------------------------------------------------------------
            */
            ->selectRaw("
            CASE
                WHEN COALESCE(o.IsAllotmentCancelled, 0) = 1
                    THEN 'Not Applicable'

                WHEN f.FlatId IS NOT NULL
                    AND f.FlatNo IS NOT NULL
                    AND TRIM(f.FlatNo) <> ''
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsRejected, 0) = 0
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    AND o.MobileNo IS NOT NULL
                    AND o.MobileNo <> ''
                    AND EXISTS (
                        SELECT 1
                        FROM registary AS r
                        WHERE r.SecondPartyMobile = o.MobileNo
                    )
                    THEN 'Registry Done'

                WHEN f.FlatId IS NOT NULL
                    AND f.FlatNo IS NOT NULL
                    AND TRIM(f.FlatNo) <> ''
                    AND o.IsApproved = 1
                    AND o.IsPaid = 1
                    AND COALESCE(o.IsRejected, 0) = 0
                    AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    THEN 'Registry Pending'

                ELSE 'Not Applicable'
            END AS RegistryStatus
        ")
            ->distinct();
    }

    public function exportApplicantReportExcel(Request $request)
    {
        $user = auth()->user();

        abort_unless($user, 401);

        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        abort_unless($districtId, 404, 'District not found.');

        $phase = (string) $request->query('phase', 'all');
        if (!in_array($phase, ['all', '1', '2', '3'], true)) {
            $phase = 'all';
        }

        $villageId = $request->filled('village_id')
            ? (int) $request->query('village_id')
            : null;

        $status = trim((string) $request->query('status', 'all_applicants'));
        $caste = trim((string) $request->query('caste', ''));
        $search = trim((string) $request->query('search', ''));

        if (
            $caste !== '' && !in_array(
                $caste,
                ['SC', 'Ghumantu', 'Widow', 'General', 'Others'],
                true
            )
        ) {
            $caste = '';
        }

        $query = $this->applicantReportQuery(
            districtId: (int) $districtId,
            phase: $phase,
            villageId: $villageId,
            status: $status,
            caste: $caste !== '' ? $caste : null,
            search: $search
        );

        $export = new class ($query) implements
        \Maatwebsite\Excel\Concerns\FromQuery,
        \Maatwebsite\Excel\Concerns\WithHeadings,
        \Maatwebsite\Excel\Concerns\WithMapping {
            public function __construct(private $query)
            {
            }

            public function query()
            {
                return $this->query
                    ->reorder()
                    ->orderBy('o.OwnerId');
            }

            public function headings(): array
            {
                return [
                'Owner ID',
                'Registration Number',
                'Applicant Name',
                'Relation',
                'Father / Husband Name',
                'Gender',
                'Caste / Category',
                'Mobile Number',
                'PPP ID',
                'Member ID',
                'Phase',
                'Village',
                'Flat Number',
                'Allotment Status',
                'Applicant Status',
                'Payment Status',
                'Registry Status',
                ];
            }

            public function map($row): array
            {
                return [
                    $row->OwnerId,
                    $row->RegistrationNo,
                    $row->OwnerName,
                    $row->Relation,
                    $row->FatherHusbandName,
                    $row->Gender,
                    in_array(
                        strtolower(trim((string) ($row->Caste ?? ''))),
                        ['sc', 'ghumantu', 'widow'],
                        true
                    )
                ? match (strtolower(trim((string) ($row->Caste ?? '')))) {
                    'sc' => 'SC',
                    'ghumantu' => 'Ghumantu',
                    'widow' => 'Widow',
                }
                : 'Others',
                    $row->MobileNo,
                    $row->PPPId,
                    $row->MemberId ?? null,
                    $row->Phase,
                    $row->VillageName,
                    $row->FlatNo,
                    $row->AllotmentStatus,
                    $row->ApplicantStatus,
                    $row->PaymentStatus,
                    $row->RegistryStatus,
                ];
            }
        };

        return Excel::download(
            $export,
            'Applicant_Report_' . now()->format('d-m-Y_H-i-s') . '.xlsx'
        );
    }

    public function exportApplicantReportCsv(Request $request)
    {
        $user = auth()->user();

        abort_unless($user, 401);

        $districtId = DB::table('districtmaster')
            ->where('DistrictName', $user->district_name)
            ->value('DistrictId');

        abort_unless($districtId, 404, 'District not found.');

        $phase = (string) $request->query('phase', 'all');
        if (!in_array($phase, ['all', '1', '2', '3'], true)) {
            $phase = 'all';
        }

        $villageId = $request->filled('village_id')
            ? (int) $request->query('village_id')
            : null;

        $status = trim((string) $request->query('status', 'all_applicants'));
        $caste = trim((string) $request->query('caste', ''));
        $search = trim((string) $request->query('search', ''));

        if (
            $caste !== '' && !in_array(
                $caste,
                ['SC', 'Ghumantu', 'Widow', 'General', 'Others'],
                true
            )
        ) {
            $caste = '';
        }

        $query = $this->applicantReportQuery(
            districtId: (int) $districtId,
            phase: $phase,
            villageId: $villageId,
            status: $status,
            caste: $caste !== '' ? $caste : null,
            search: $search
        );

        $export = new class ($query) implements
        \Maatwebsite\Excel\Concerns\FromQuery,
        \Maatwebsite\Excel\Concerns\WithHeadings,
        \Maatwebsite\Excel\Concerns\WithMapping {
            public function __construct(private $query)
            {
            }

            public function query()
            {
                return $this->query
                    ->reorder()
                    ->orderBy('o.OwnerId');
            }

            public function headings(): array
            {
                return [
                'Owner ID',
                'Registration Number',
                'Applicant Name',
                'Relation',
                'Father / Husband Name',
                'Gender',
                'Caste / Category',
                'Mobile Number',
                'PPP ID',
                'Member ID',
                'Phase',
                'Village',
                'Flat Number',
                'Allotment Status',
                'Applicant Status',
                'Payment Status',
                'Registry Status',
                ];
            }

            public function map($row): array
            {
                return [
                    $row->OwnerId,
                    $row->RegistrationNo,
                    $row->OwnerName,
                    $row->Relation,
                    $row->FatherHusbandName,
                    $row->Gender,
                    in_array(
                        strtolower(trim((string) ($row->Caste ?? ''))),
                        ['sc', 'ghumantu', 'widow'],
                        true
                    )
                ? match (strtolower(trim((string) ($row->Caste ?? '')))) {
                    'sc' => 'SC',
                    'ghumantu' => 'Ghumantu',
                    'widow' => 'Widow',
                }
                : 'Others',
                    $row->MobileNo,
                    $row->PPPId,
                    $row->MemberId ?? null,
                    $row->Phase,
                    $row->VillageName,
                    $row->FlatNo,
                    $row->AllotmentStatus,
                    $row->ApplicantStatus,
                    $row->PaymentStatus,
                    $row->RegistryStatus,
                ];
            }
        };

        return Excel::download(
            $export,
            'Applicant_Report_' . now()->format('d-m-Y_H-i-s') . '.csv',
            ExcelFormat::CSV
        );
    }

    private function registryMobileSummaryQuery()
    {
        return DB::table('registary as registry_source')
            ->whereNotNull('registry_source.SecondPartyMobile')
            ->whereRaw("
            TRIM(registry_source.SecondPartyMobile) <> ''
        ")
            ->selectRaw("
            registry_source.SecondPartyMobile,

            SUBSTRING_INDEX(
                GROUP_CONCAT(
                    registry_source.RegistaryNumber
                    ORDER BY
                        registry_source.RegistaryDate DESC,
                        registry_source.id DESC
                    SEPARATOR '||'
                ),
                '||',
                1
            ) AS RegistaryNumber,

            MAX(
                registry_source.RegistaryDate
            ) AS RegistaryDate
        ")
            ->groupBy(
                'registry_source.SecondPartyMobile'
            );
    }

}