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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;


class SuperAdminController extends Controller
{
    private const REPORT_CACHE_SECONDS = 300;

    private function reportCacheSeconds(): int
    {
        return max(1, (int) env(
            'REPORT_CACHE_SECONDS',
            self::REPORT_CACHE_SECONDS
        ));
    }

    /**
     * Build a stable cache key from current request filters.
     */
    private function reportCacheKey(string $prefix, Request $request, array $keys): string
    {
        $values = [];

        foreach ($keys as $key) {
            $value = $request->query($key);
            $values[$key] = is_string($value) ? trim($value) : $value;
        }

        return $prefix . '_' . md5(json_encode($values));
    }

    /**
     * Large report endpoints should not keep Laravel's SQL query log in memory.
     */
    private function prepareLargeReportRequest(): void
    {
        DB::disableQueryLog();
    }

    private function applyOwnerDashboardFilters(
        $query,
        $phase,
        $districtId,
        $blockId,
        $villageId
    ) {
        return $query
            ->when(
                filled($phase),
                fn($q) => $q->where('o.Phase', $phase)
            )
            ->when(
                filled($districtId),
                fn($q) => $q->where('o.DistrictId', $districtId)
            )
            ->when(
                filled($blockId),
                fn($q) => $q->where('o.BlockId', $blockId)
            )
            ->when(
                filled($villageId),
                fn($q) => $q->where('o.VillageId', $villageId)
            );
    }

    private function applyVillageDashboardFilters(
        $query,
        $phase,
        $districtId,
        $blockId,
        $villageId
    ) {
        return $query
            ->when(
                filled($phase),
                fn($q) => $q->where('v.Phase', $phase)
            )
            ->when(
                filled($districtId),
                fn($q) => $q->where('v.DistrictId', $districtId)
            )
            ->when(
                filled($blockId),
                fn($q) => $q->where('v.BlockId', $blockId)
            )
            ->when(
                filled($villageId),
                fn($q) => $q->where('v.VillageId', $villageId)
            );
    }

    public function dashboard(Request $request)
    {
        DB::disableQueryLog();

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */
        $phase = $request->filled('phase')
            ? (int) $request->phase
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->district_id
            : null;

        $blockId = $request->filled('block_id')
            ? (int) $request->block_id
            : null;

        $villageId = $request->filled('village_id')
            ? (int) $request->village_id
            : null;

        /*
        |--------------------------------------------------------------------------
        | Dropdown: Districts
        |--------------------------------------------------------------------------
        */
        $districts = DB::table('DistrictMaster as d')
            ->whereExists(function ($query) use ($phase) {
                $query
                    ->selectRaw('1')
                    ->from('VillageMaster as v')
                    ->whereColumn(
                        'v.DistrictId',
                        'd.DistrictId'
                    )
                    ->where('v.plots', '>', 0)
                    ->when(
                        filled($phase),
                        fn($q) => $q->where('v.Phase', $phase)
                    );
            })
            ->select([
                'd.DistrictId',
                'd.DistrictName',
            ])
            ->orderBy('d.DistrictName')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Dropdown: Blocks
        |--------------------------------------------------------------------------
        */
        $blocks = $districtId
            ? DB::table('BlockMaster as b')
                ->where('b.DistrictId', $districtId)
                ->whereExists(function ($query) use ($phase) {
                    $query
                        ->selectRaw('1')
                        ->from('VillageMaster as v')
                        ->whereColumn(
                            'v.BlockId',
                            'b.BlockId'
                        )
                        ->where('v.plots', '>', 0)
                        ->when(
                            filled($phase),
                            fn($q) => $q->where(
                                'v.Phase',
                                $phase
                            )
                        );
                })
                ->select([
                    'b.BlockId',
                    'b.BlockName',
                ])
                ->orderBy('b.BlockName')
                ->get()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Dropdown: Villages
        |--------------------------------------------------------------------------
        */
        $villages = $blockId
            ? DB::table('VillageMaster as v')
                ->where('v.BlockId', $blockId)
                ->where('v.plots', '>', 0)
                ->when(
                    filled($phase),
                    fn($q) => $q->where('v.Phase', $phase)
                )
                ->select([
                    'v.VillageId',
                    'v.VillageName',
                ])
                ->orderBy('v.VillageName')
                ->get()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Village Statistics
        |--------------------------------------------------------------------------
        | One query calculates village and district counts.
        |--------------------------------------------------------------------------
        */
        $villageStatsQuery = DB::table('VillageMaster as v')
            ->where('v.plots', '>', 0);

        $villageStatsQuery =
            $this->applyVillageDashboardFilters(
                $villageStatsQuery,
                $phase,
                $districtId,
                $blockId,
                $villageId
            );

        $villageStats = $villageStatsQuery
            ->selectRaw('
            COUNT(*) AS TotalVillages,
            COUNT(DISTINCT v.DistrictId) AS TotalDistricts
        ')
            ->first();

        $totalVillages = (int) (
            $villageStats->TotalVillages ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | Preserve original default district count
        |--------------------------------------------------------------------------
        */
        $totalDistricts = (!$phase && !$districtId)
            ? 22
            : (int) ($villageStats->TotalDistricts ?? 0);

        /*
        |--------------------------------------------------------------------------
        | Registered Beneficiaries
        |--------------------------------------------------------------------------
        | WHERE EXISTS avoids creating VillageMaster join rows.
        |--------------------------------------------------------------------------
        */
        $registeredQuery = DB::table('OwnerMaster as o')
            ->whereExists(function ($query) {
                $query
                    ->selectRaw('1')
                    ->from('VillageMaster as v')
                    ->whereColumn(
                        'v.VillageId',
                        'o.VillageId'
                    )
                    ->where('v.plots', '>', 0);
            });

        $registeredQuery =
            $this->applyOwnerDashboardFilters(
                $registeredQuery,
                $phase,
                $districtId,
                $blockId,
                $villageId
            );

        $registeredBeneficiaries =
            $registeredQuery->count('o.OwnerId');

        /*
        |--------------------------------------------------------------------------
        | Allotment Statistics
        |--------------------------------------------------------------------------
        | FlatMaster existence is preserved exactly like the old INNER JOIN.
        |--------------------------------------------------------------------------
        */
        $allotmentQuery = DB::table('OwnerMaster as o')
            ->whereExists(function ($query) {
                $query
                    ->selectRaw('1')
                    ->from('FlatMaster as f')
                    ->whereColumn(
                        'f.FlatId',
                        'o.FlatId'
                    );
            });

        $allotmentQuery =
            $this->applyOwnerDashboardFilters(
                $allotmentQuery,
                $phase,
                $districtId,
                $blockId,
                $villageId
            );

        $stats = $allotmentQuery
            ->selectRaw("
            COUNT(*) AS GrossTotal,

            COALESCE(
                SUM(
                    CASE
                        WHEN o.IsApproved = 1
                         AND o.IsPaid = 1
                         AND o.IsAllotmentCancelled = 0
                        THEN 1
                        ELSE 0
                    END
                ),
                0
            ) AS ApprovedPaid,

            COALESCE(
                SUM(
                    CASE
                        WHEN o.IsApproved = 1
                         AND o.IsPaid = 0
                         AND o.IsAllotmentCancelled = 0
                        THEN 1
                        ELSE 0
                    END
                ),
                0
            ) AS ApprovedUnpaid,

            COALESCE(
                SUM(
                    CASE
                        WHEN o.IsApproved = 0
                         AND o.IsPaid = 0
                         AND o.IsRejected = 0
                        THEN 1
                        ELSE 0
                    END
                ),
                0
            ) AS PendingApprovalPayment,

            COALESCE(
                SUM(
                    CASE
                        WHEN o.IsRejected = 1
                        THEN 1
                        ELSE 0
                    END
                ),
                0
            ) AS Rejected,

            COALESCE(
                SUM(
                    CASE
                        WHEN o.IsAllotmentCancelled = 1
                        THEN 1
                        ELSE 0
                    END
                ),
                0
            ) AS AllotmentCancelled
        ")
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Total Registration
        |--------------------------------------------------------------------------
        | This table total does not depend on dashboard filters.
        |--------------------------------------------------------------------------
        */
        $totalRegistration = Cache::remember(
            'super_admin_dashboard_total_registration',
            now()->addMinutes(5),
            fn() => DB::table('registary')->count()
        );

        /*
        |--------------------------------------------------------------------------
        | Registry Matched
        |--------------------------------------------------------------------------
        | Unique matched OwnerMaster mobile numbers.
        | WHERE EXISTS prevents duplicate registry records from multiplying rows.
        |--------------------------------------------------------------------------
        */
        $matchedQuery = DB::table('OwnerMaster as o')
            ->whereNotNull('o.MobileNo')
            ->where('o.MobileNo', '<>', '')
            ->whereExists(function ($query) {
                $query
                    ->selectRaw('1')
                    ->from('registary as r')
                    ->whereColumn(
                        'r.SecondPartyMobile',
                        'o.MobileNo'
                    )
                    ->whereNotNull('r.SecondPartyMobile')
                    ->where('r.SecondPartyMobile', '<>', '');
            });

        $matchedQuery =
            $this->applyOwnerDashboardFilters(
                $matchedQuery,
                $phase,
                $districtId,
                $blockId,
                $villageId
            );

        $matched = $matchedQuery
            ->distinct()
            ->count('o.MobileNo');

        $unMatched = max(
            0,
            (int) $totalRegistration - (int) $matched
        );

        /*
        |--------------------------------------------------------------------------
        | Registration Object
        |--------------------------------------------------------------------------
        */
        $registration = (object) [
            'TotalRegistration' =>
                (int) $totalRegistration,

            'Matched' =>
                (int) $matched,

            'UnMatched' =>
                (int) $unMatched,
        ];

        /*
        |--------------------------------------------------------------------------
        | Summary Object
        |--------------------------------------------------------------------------
        */
        $summary = (object) [
            'TotalDistricts' =>
                $totalDistricts,

            'TotalVillages' =>
                $totalVillages,

            'RegisteredBeneficiaries' =>
                (int) $registeredBeneficiaries,

            'AllottedBeneficiaries' =>
                (int) ($stats->GrossTotal ?? 0),

            'GrossTotal' =>
                (int) ($stats->GrossTotal ?? 0),

            'ApprovedPaid' =>
                (int) ($stats->ApprovedPaid ?? 0),

            'ApprovedUnpaid' =>
                (int) ($stats->ApprovedUnpaid ?? 0),

            'PendingApprovalPayment' =>
                (int) (
                    $stats->PendingApprovalPayment ?? 0
                ),

            'Rejected' =>
                (int) ($stats->Rejected ?? 0),

            'AllotmentCancelled' =>
                (int) (
                    $stats->AllotmentCancelled ?? 0
                ),
        ];

        return view(
            'mmgay.super-admin.dashboard',
            compact(
                'summary',
                'registration',
                'districts',
                'blocks',
                'villages'
            )
        );
    }

    private function possessionBaseQuery(Request $request)
    {
        $phase = $request->filled('phase')
            ? (int) $request->phase
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->district_id
            : null;

        $blockId = $request->filled('block_id')
            ? (int) $request->block_id
            : null;

        $villageId = $request->filled('village_id')
            ? (int) $request->village_id
            : null;

        return DB::table('OwnerMaster as o')

            /*
            |--------------------------------------------------------------------------
            | Display-only joins
            |--------------------------------------------------------------------------
            | LEFT JOIN रखा है, इसलिए missing village/flat के कारण eligible record
            | exclude नहीं होगा।
            |--------------------------------------------------------------------------
            */
            ->leftJoin(
                'VillageMaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )

            ->leftJoin(
                'FlatMaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )

            ->leftJoin(
                'mmgay_possession_applications as pa',
                'pa.owner_id',
                '=',
                'o.OwnerId'
            )

            /*
            |--------------------------------------------------------------------------
            | Registry matched + approved + paid
            |--------------------------------------------------------------------------
            */
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereRaw(
                'COALESCE(o.IsAllotmentCancelled, 0) = 0'
            )

            ->whereNotNull('o.MobileNo')
            ->where('o.MobileNo', '<>', '')

            ->whereExists(function ($query) {
                $query
                    ->selectRaw('1')
                    ->from('registary as r')
                    ->whereColumn(
                        'r.SecondPartyMobile',
                        'o.MobileNo'
                    );
            })

            ->when($phase, function ($query) use ($phase) {
                $query->where('o.Phase', $phase);
            })

            ->when($districtId, function ($query) use ($districtId) {
                $query->where(
                    'o.DistrictId',
                    $districtId
                );
            })

            ->when($blockId, function ($query) use ($blockId) {
                $query->where(
                    'o.BlockId',
                    $blockId
                );
            })

            ->when($villageId, function ($query) use ($villageId) {
                $query->where(
                    'o.VillageId',
                    $villageId
                );
            });
    }

    public function possessionStats(Request $request)
    {
        $this->prepareLargeReportRequest();

        $cacheKey = $this->reportCacheKey(
            'super_admin_possession_stats',
            $request,
            ['phase', 'district_id', 'block_id', 'village_id']
        );

        $totals = Cache::remember(
            $cacheKey,
            now()->addSeconds($this->reportCacheSeconds()),
            function () use ($request) {
                $row = $this->possessionBaseQuery($request)
                    ->selectRaw("
                        COUNT(DISTINCT o.OwnerId) AS eligible_count,
                        COUNT(DISTINCT CASE
                            WHEN LOWER(TRIM(COALESCE(
                                pa.physical_possession_status,
                                ''
                            ))) = 'verified'
                            THEN o.OwnerId
                        END) AS given_count
                    ")
                    ->first();

                $eligible = (int) ($row->eligible_count ?? 0);
                $given = (int) ($row->given_count ?? 0);

                return [
                    'eligible' => $eligible,
                    'given' => $given,
                    'pending' => max(0, $eligible - $given),
                ];
            }
        );

        return response()->json([
            'success' => true,
            'totals' => $totals,
        ]);
    }

    public function possessionList(
        Request $request,
        string $filter = 'all'
    ) {
        DB::disableQueryLog();

        $allowedFilters = [
            'all',
            'schedule_pending',
            'awaiting_citizen',
            'field_visit_pending',
            'document_verification',
            'verified',
        ];

        abort_unless(
            in_array($filter, $allowedFilters, true),
            404
        );

        $phase = $request->filled('phase')
            ? (int) $request->phase
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->district_id
            : null;

        $blockId = $request->filled('block_id')
            ? (int) $request->block_id
            : null;

        $villageId = $request->filled('village_id')
            ? (int) $request->village_id
            : null;

        $perPage = (int) $request->query('per_page', 20);

        if (!in_array($perPage, [20, 50, 100, 200], true)) {
            $perPage = 20;
        }

        /*
        |--------------------------------------------------------------------------
        | Dropdown Data
        |--------------------------------------------------------------------------
        */
        $districts = DB::table('DistrictMaster as d')
            ->join(
                'VillageMaster as v',
                'v.DistrictId',
                '=',
                'd.DistrictId'
            )
            ->where('v.plots', '>', 0)
            ->when($phase, function ($query) use ($phase) {
                $query->where('v.Phase', $phase);
            })
            ->select([
                'd.DistrictId',
                'd.DistrictName',
            ])
            ->distinct()
            ->orderBy('d.DistrictName')
            ->get();

        $blocks = $districtId
            ? DB::table('BlockMaster as b')
                ->join(
                    'VillageMaster as v',
                    'v.BlockId',
                    '=',
                    'b.BlockId'
                )
                ->where('b.DistrictId', $districtId)
                ->where('v.plots', '>', 0)
                ->when($phase, function ($query) use ($phase) {
                    $query->where('v.Phase', $phase);
                })
                ->select([
                    'b.BlockId',
                    'b.BlockName',
                ])
                ->distinct()
                ->orderBy('b.BlockName')
                ->get()
            : collect();

        $villages = $blockId
            ? DB::table('VillageMaster as v')
                ->where('v.BlockId', $blockId)
                ->where('v.plots', '>', 0)
                ->when($phase, function ($query) use ($phase) {
                    $query->where('v.Phase', $phase);
                })
                ->select([
                    'v.VillageId',
                    'v.VillageName',
                    'v.Phase',
                ])
                ->orderBy('v.VillageName')
                ->get()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Registry Matched + Approved + Paid + Not Cancelled
        |--------------------------------------------------------------------------
        */
        $baseQuery = $this->possessionBaseQuery($request);

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

        /*
        |--------------------------------------------------------------------------
        | Counts — single aggregate query
        |--------------------------------------------------------------------------
        | Previously six separate COUNT queries were executed. This keeps the
        | exact same status mapping while scanning the eligible result once.
        |--------------------------------------------------------------------------
        */
        $countCacheKey = $this->reportCacheKey(
            'super_admin_possession_counts',
            $request,
            ['phase', 'district_id', 'block_id', 'village_id']
        );

        $countRow = Cache::remember(
            $countCacheKey,
            now()->addSeconds($this->reportCacheSeconds()),
            function () use ($baseQuery) {
                return (clone $baseQuery)
                    ->selectRaw("
                COUNT(DISTINCT o.OwnerId) AS all_count,

                COUNT(DISTINCT CASE
                    WHEN pa.id IS NULL OR LOWER(TRIM(COALESCE(pa.physical_possession_status, ''))) = 'eligible for physical possession'
                    THEN o.OwnerId
                END) AS schedule_pending_count,

                COUNT(DISTINCT CASE
                    WHEN LOWER(TRIM(COALESCE(
                        pa.physical_possession_status,
                        ''
                    ))) = 'visit scheduled'
                    THEN o.OwnerId
                END) AS awaiting_citizen_count,

                COUNT(DISTINCT CASE
                    WHEN LOWER(TRIM(COALESCE(
                        pa.physical_possession_status,
                        ''
                    ))) = 'slot selected'
                    THEN o.OwnerId
                END) AS field_visit_pending_count,

                COUNT(DISTINCT CASE
                    WHEN LOWER(TRIM(COALESCE(
                        pa.physical_possession_status,
                        ''
                    ))) = 'site verified'
                    THEN o.OwnerId
                END) AS document_verification_count,

                COUNT(DISTINCT CASE
                    WHEN LOWER(TRIM(COALESCE(
                        pa.physical_possession_status,
                        ''
                    ))) = 'verified'
                    THEN o.OwnerId
                END) AS verified_count
                    ")
                    ->first();
            }
        );

        $counts = [
            'all' => (int) ($countRow->all_count ?? 0),
            'schedule_pending' =>
                (int) ($countRow->schedule_pending_count ?? 0),
            'awaiting_citizen' =>
                (int) ($countRow->awaiting_citizen_count ?? 0),
            'field_visit_pending' =>
                (int) ($countRow->field_visit_pending_count ?? 0),
            'document_verification' =>
                (int) ($countRow->document_verification_count ?? 0),
            'verified' =>
                (int) ($countRow->verified_count ?? 0),
        ];

        /*
        |--------------------------------------------------------------------------
        | Applications Query
        |--------------------------------------------------------------------------
        */
        $applicationsQuery = (clone $baseQuery)
            ->select([
                'o.OwnerId',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.RegistrationNo',
                'o.PPPId',
                'o.MemberId',
                'o.Phase',
                'o.OwnerAddress',
                'o.Caste',

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
            ]);

        /*
        |--------------------------------------------------------------------------
        | Selected Filter
        |--------------------------------------------------------------------------
        */
        switch ($filter) {
            case 'schedule_pending':
                $applicationsQuery->where(function ($q) use ($statusExpression) {
                    $q->whereNull('pa.id')
                        ->orWhereRaw($statusExpression . " = ?", ['eligible for physical possession']);
                });
                break;

            case 'awaiting_citizen':
                $applicationsQuery
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        $statusExpression . ' = ?',
                        ['visit scheduled']
                    );
                break;

            case 'field_visit_pending':
                $applicationsQuery
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        $statusExpression . ' = ?',
                        ['slot selected']
                    );
                break;

            case 'document_verification':
                $applicationsQuery
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        $statusExpression . ' = ?',
                        ['site verified']
                    );
                break;

            case 'verified':
                $applicationsQuery
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        $statusExpression . ' = ?',
                        ['verified']
                    );
                break;
        }

        $currentPage = max(1, (int) $request->query('page', 1));

        $filterCountKey = match ($filter) {
            'schedule_pending' => 'schedule_pending',
            'awaiting_citizen' => 'awaiting_citizen',
            'field_visit_pending' => 'field_visit_pending',
            'document_verification' => 'document_verification',
            'verified' => 'verified',
            default => 'all',
        };

        $totalApplications = (int) ($counts[$filterCountKey] ?? 0);
        $lastPage = max(1, (int) ceil($totalApplications / $perPage));
        $currentPage = min($currentPage, $lastPage);

        $applicationRows = $applicationsQuery
            ->orderByRaw(
                'CASE
                    WHEN pa.updated_at IS NULL THEN 1
                    ELSE 0
                END'
            )
            ->orderByDesc('pa.updated_at')
            ->orderBy('o.OwnerName')
            ->forPage($currentPage, $perPage)
            ->get();

        $applications = new LengthAwarePaginator(
            $applicationRows,
            $totalApplications,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
                'query' => $request->except('page'),
            ]
        );

        $filterLabels = [
            'all' => 'Total Eligible',
            'schedule_pending' => 'Schedule Pending',
            'awaiting_citizen' =>
                'Confirmation Pending From Citizen',
            'field_visit_pending' =>
                'Physical/Site Visit Pending',
            'document_verification' =>
                'Document Verification',
            'verified' => 'Possession Given',
        ];

        return view(
            'mmgay.super-admin.possession-list',
            compact(
                'applications',
                'counts',
                'filter',
                'filterLabels',
                'phase',
                'districtId',
                'blockId',
                'villageId',
                'districts',
                'blocks',
                'villages',
                'perPage'
            )
        );
    }

    public function possessionView($secureId)
    {
        $application = DB::table('mmgay_possession_applications as p')
            ->leftJoin('OwnerMaster as o', 'o.OwnerId', '=', 'p.owner_id')
            ->leftJoin('DistrictMaster as d', 'd.DistrictId', '=', 'o.DistrictId')
            ->leftJoin('BlockMaster as b', 'b.BlockId', '=', 'o.BlockId')
            ->leftJoin('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->leftJoin('FlatMaster as f', 'f.FlatId', '=', 'o.FlatId')
            ->where('p.secure_id', $secureId)
            ->select(
                'p.*',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.RegistrationNo',
                'o.OwnerAddress as Address',
                'o.OwnerAddress',
                'o.PPPId',
                'o.Caste',
                'o.Remarks as OwnerRemarks',
                'd.DistrictName',
                'b.BlockName',
                'v.VillageName',
                'f.FlatNo'
            )
            ->first();

        abort_if(!$application, 404);

        $timeline = DB::table('mmgay_possession_status_logs')
            ->where('application_id', $application->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view(
            'mmgay.super-admin.physical-possession.view',
            compact('application', 'timeline')
        );
    }

    private function applyPossessionStatusFilter(
        $query,
        string $filter
    ) {
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
                $query->where(function ($q) use ($statusExpression) {
                    $q->whereNull('pa.id')
                        ->orWhereRaw($statusExpression . " = ?", ['eligible for physical possession']);
                });
                break;

            case 'awaiting_citizen':
                $query
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        $statusExpression . ' = ?',
                        ['visit scheduled']
                    );
                break;

            case 'field_visit_pending':
                $query
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        $statusExpression . ' = ?',
                        ['slot selected']
                    );
                break;

            case 'document_verification':
                $query
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        $statusExpression . ' = ?',
                        ['site verified']
                    );
                break;

            case 'verified':
                $query
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        $statusExpression . ' = ?',
                        ['verified']
                    );
                break;
        }

        return $query;
    }

    public function possessionExportCsv(Request $request)
    {
        $filter = (string) $request->query(
            'filter',
            'all'
        );

        $allowedFilters = [
            'all',
            'schedule_pending',
            'awaiting_citizen',
            'field_visit_pending',
            'document_verification',
            'verified',
        ];

        if (!in_array($filter, $allowedFilters, true)) {
            $filter = 'all';
        }

        $query = $this->possessionBaseQuery($request);

        $query = $this->applyPossessionStatusFilter(
            $query,
            $filter
        );

        $recordsQuery = $query
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
            ->orderBy('o.OwnerName');

        $fileName = 'Possession_Report_'
            . now()->format('d-m-Y_H-i-s')
            . '.csv';

        return response()->streamDownload(
            function () use ($recordsQuery) {
                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    throw new \RuntimeException(
                        'CSV stream could not be opened.'
                    );
                }

                fwrite($handle, "\xEF\xBB\xBF");

                fputcsv($handle, [
                    'Sr. No.',
                    'Owner ID',
                    'Applicant Name',
                    'Father / Husband',
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

                $index = 0;

                foreach ($recordsQuery->cursor() as $record) {
                    fputcsv($handle, [
                        ++$index,
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
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }

    public function possessionPrint(
        Request $request,
        string $filter = 'all'
    ) {
        $allowedFilters = [
            'all',
            'schedule_pending',
            'awaiting_citizen',
            'field_visit_pending',
            'document_verification',
            'verified',
        ];

        abort_unless(
            in_array($filter, $allowedFilters, true),
            404
        );

        $query = $this->possessionBaseQuery($request);

        $query = $this->applyPossessionStatusFilter(
            $query,
            $filter
        );

        $applications = $query
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

        $filterLabels = [
            'all' => 'Total Eligible',
            'schedule_pending' => 'Schedule Pending',
            'awaiting_citizen' =>
                'Confirmation Pending From Citizen',
            'field_visit_pending' =>
                'Physical/Site Visit Pending',
            'document_verification' =>
                'Document Verification',
            'verified' => 'Possession Given',
        ];

        return view(
            'mmgay.super-admin.possession-print',
            compact(
                'applications',
                'filter',
                'filterLabels'
            )
        );
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
        $this->prepareLargeReportRequest();
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
        $this->prepareLargeReportRequest();

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */
        $phase = $request->filled('phase')
            ? (int) $request->phase
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->district_id
            : null;

        /*
        |--------------------------------------------------------------------------
        | Filter-based cache key
        |--------------------------------------------------------------------------
        | Same phase/district खोलने पर heavy aggregation दोबारा नहीं चलेगी।
        |--------------------------------------------------------------------------
        */
        $reportCacheKey = 'district_wise_report_' . md5(
            json_encode([
                'phase' => $phase,
                'district_id' => $districtId,
            ])
        );

        $data = Cache::remember(
            $reportCacheKey,
            now()->addMinutes(5),
            function () use ($phase, $districtId) {

                /*
                |--------------------------------------------------------------------------
                | Village counts directly district-wise
                |--------------------------------------------------------------------------
                */
                $villageStats = DB::table('VillageMaster as v')
                    ->where('v.plots', '>', 0)

                    ->when(
                        $phase !== null,
                        function ($query) use ($phase) {
                            $query->where('v.Phase', $phase);
                        }
                    )

                    ->when(
                        $districtId !== null,
                        function ($query) use ($districtId) {
                            $query->where(
                                'v.DistrictId',
                                $districtId
                            );
                        }
                    )

                    ->select('v.DistrictId')

                    ->selectRaw(
                        'COUNT(DISTINCT v.VillageId)
                        AS VillagesWithPlots'
                    )

                    ->groupBy('v.DistrictId');

                /*
                |--------------------------------------------------------------------------
                | Owner statistics directly district-wise
                |--------------------------------------------------------------------------
                | पहले Village-wise aggregate करके फिर District-wise SUM नहीं होगा।
                | केवल plots > 0 वाले villages के owners consider होंगे।
                |--------------------------------------------------------------------------
                */
                $ownerStats = DB::table('OwnerMaster as o')

                    ->join(
                        'VillageMaster as v',
                        function ($join) {
                            $join
                                ->on(
                                    'v.VillageId',
                                    '=',
                                    'o.VillageId'
                                )
                                ->on(
                                    'v.DistrictId',
                                    '=',
                                    'o.DistrictId'
                                );
                        }
                    )

                    ->leftJoin(
                        'FlatMaster as f',
                        'f.FlatId',
                        '=',
                        'o.FlatId'
                    )

                    ->where('v.plots', '>', 0)

                    ->when(
                        $phase !== null,
                        function ($query) use ($phase) {
                            $query
                                ->where('o.Phase', $phase)
                                ->where('v.Phase', $phase);
                        }
                    )

                    ->when(
                        $districtId !== null,
                        function ($query) use ($districtId) {
                            $query
                                ->where(
                                    'o.DistrictId',
                                    $districtId
                                )
                                ->where(
                                    'v.DistrictId',
                                    $districtId
                                );
                        }
                    )

                    ->select('o.DistrictId')

                    ->selectRaw("
                    COUNT(DISTINCT o.OwnerId)
                        AS RegisteredBeneficiaries,

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

                    ->groupBy('o.DistrictId');

                /*
                |--------------------------------------------------------------------------
                | Final district report
                |--------------------------------------------------------------------------
                */
                $report = DB::table('DistrictMaster as d')

                    ->leftJoinSub(
                        $villageStats,
                        'vs',
                        function ($join) {
                            $join->on(
                                'vs.DistrictId',
                                '=',
                                'd.DistrictId'
                            );
                        }
                    )

                    ->leftJoinSub(
                        $ownerStats,
                        'os',
                        function ($join) {
                            $join->on(
                                'os.DistrictId',
                                '=',
                                'd.DistrictId'
                            );
                        }
                    )

                    ->when(
                        $districtId !== null,
                        function ($query) use ($districtId) {
                            $query->where(
                                'd.DistrictId',
                                $districtId
                            );
                        }
                    )

                    /*
                    |--------------------------------------------------------------------------
                    | बिना district filter केवल relevant districts
                    |--------------------------------------------------------------------------
                    | Original report के सभी districts रखने हैं तो यह where हटाएं।
                    | अभी zero-value districts भी output में बने रहेंगे क्योंकि यह block
                    | जानबूझकर नहीं लगाया गया है।
                    |--------------------------------------------------------------------------
                    */

                    ->select([
                        'd.DistrictId',
                        'd.DistrictName',
                    ])

                    ->selectRaw("
                    COALESCE(
                        vs.VillagesWithPlots,
                        0
                    ) AS VillagesWithPlots,

                    COALESCE(
                        os.RegisteredBeneficiaries,
                        0
                    ) AS RegisteredBeneficiaries,

                    COALESCE(
                        os.AllottedBeneficiaries,
                        0
                    ) AS AllottedBeneficiaries,

                    COALESCE(
                        os.ApprovedPaid,
                        0
                    ) AS ApprovedPaid,

                    COALESCE(
                        os.ApprovedUnpaid,
                        0
                    ) AS ApprovedUnpaid,

                    COALESCE(
                        os.PendingApprovalPayment,
                        0
                    ) AS PendingApprovalPayment,

                    COALESCE(
                        os.Rejected,
                        0
                    ) AS Rejected,

                    COALESCE(
                        os.AllotmentCancelled,
                        0
                    ) AS AllotmentCancelled
                ")

                    ->orderBy('d.DistrictName')

                    ->get();

                /*
                |--------------------------------------------------------------------------
                | Gross totals
                |--------------------------------------------------------------------------
                */
                $grossTotal = (object) [
                    'VillagesWithPlots' => (int) $report->sum(
                        'VillagesWithPlots'
                    ),

                    'RegisteredBeneficiaries' => (int) $report->sum(
                        'RegisteredBeneficiaries'
                    ),

                    'AllottedBeneficiaries' => (int) $report->sum(
                        'AllottedBeneficiaries'
                    ),

                    'ApprovedPaid' => (int) $report->sum(
                        'ApprovedPaid'
                    ),

                    'ApprovedUnpaid' => (int) $report->sum(
                        'ApprovedUnpaid'
                    ),

                    'PendingApprovalPayment' => (int) $report->sum(
                        'PendingApprovalPayment'
                    ),

                    'Rejected' => (int) $report->sum(
                        'Rejected'
                    ),

                    'AllotmentCancelled' => (int) $report->sum(
                        'AllotmentCancelled'
                    ),
                ];

                return [
                    'report' => $report,
                    'grossTotal' => $grossTotal,
                ];
            }
        );

        /*
        |--------------------------------------------------------------------------
        | District dropdown
        |--------------------------------------------------------------------------
        */
        $districts = Cache::remember(
            'super_admin_district_report_dropdown',
            now()->addHours(1),
            function () {
                return DB::table('DistrictMaster')
                    ->orderBy('DistrictName')
                    ->get([
                        'DistrictId',
                        'DistrictName',
                    ]);
            }
        );

        $report = $data['report'];
        $grossTotal = $data['grossTotal'];

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
        $this->prepareLargeReportRequest();
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

    // Village Function Start 

    private function villageReportPhase(Request $request): ?int
    {
        $phase = (string) $request->input('phase', '');

        return in_array($phase, ['1', '2', '3'], true)
            ? (int) $phase
            : null;
    }

    private function villageReportVillageId(Request $request): ?int
    {
        return $request->filled('village_id')
            ? (int) $request->input('village_id')
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Optimized Village Report Query
    |--------------------------------------------------------------------------
    | OwnerMaster केवल एक बार scan होगा।
    |--------------------------------------------------------------------------
    */
    private function villageReportQuery(Request $request)
    {
        $phase = $this->villageReportPhase($request);
        $villageId = $this->villageReportVillageId($request);

        /*
        |--------------------------------------------------------------------------
        | Beneficiary Summary
        |--------------------------------------------------------------------------
        | Village grouping : OwnerMaster.VillageId
        | Allotment check  : OwnerMaster.FlatId = FlatMaster.FlatId
        |--------------------------------------------------------------------------
        */
        $beneficiarySummary = DB::table('OwnerMaster as o')
            ->leftJoin('FlatMaster as f', 'f.FlatId', '=', 'o.FlatId')
            ->when($phase !== null, function ($query) use ($phase) {
                $query->where('o.Phase', $phase);
            })
            ->when($villageId !== null, function ($query) use ($villageId) {
                $query->where('o.VillageId', $villageId);
            })
            ->selectRaw("
            o.VillageId,

            COUNT(*) AS RegisteredBeneficiaries,

            SUM(
                CASE
                    WHEN f.FlatId IS NOT NULL
                    THEN 1
                    ELSE 0
                END
            ) AS AllottedBeneficiaries,

            SUM(
                CASE
                    WHEN f.FlatId IS NOT NULL
                        AND COALESCE(o.IsApproved, 0) = 1
                        AND COALESCE(o.IsPaid, 0) = 1
                        AND COALESCE(o.IsRejected, 0) = 0
                        AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    THEN 1
                    ELSE 0
                END
            ) AS ApprovedPaid,

            SUM(
                CASE
                    WHEN f.FlatId IS NOT NULL
                        AND COALESCE(o.IsApproved, 0) = 1
                        AND COALESCE(o.IsPaid, 0) = 0
                        AND COALESCE(o.IsRejected, 0) = 0
                        AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    THEN 1
                    ELSE 0
                END
            ) AS ApprovedUnpaid,

            SUM(
                CASE
                    WHEN f.FlatId IS NOT NULL
                        AND COALESCE(o.IsApproved, 0) = 0
                        AND COALESCE(o.IsRejected, 0) = 0
                        AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                    THEN 1
                    ELSE 0
                END
            ) AS PendingApprovalPayment,

            SUM(
                CASE
                    WHEN f.FlatId IS NOT NULL
                        AND COALESCE(o.IsRejected, 0) = 1
                    THEN 1
                    ELSE 0
                END
            ) AS Rejected,

            SUM(
                CASE
                    WHEN f.FlatId IS NOT NULL
                        AND COALESCE(o.IsAllotmentCancelled, 0) = 1
                    THEN 1
                    ELSE 0
                END
            ) AS AllotmentCancelled
        ")
            ->groupBy('o.VillageId');

        /*
        |--------------------------------------------------------------------------
        | Final Village Report
        |--------------------------------------------------------------------------
        */
        return DB::table('VillageMaster as v')
            ->leftJoinSub(
                $beneficiarySummary,
                'beneficiary_summary',
                function ($join) {
                    $join->on(
                        'beneficiary_summary.VillageId',
                        '=',
                        'v.VillageId'
                    );
                }
            )
            ->where('v.plots', '>', 0)
            ->when($phase !== null, function ($query) use ($phase) {
                $query->where('v.Phase', $phase);
            })
            ->when($villageId !== null, function ($query) use ($villageId) {
                $query->where('v.VillageId', $villageId);
            })
            ->selectRaw("
            v.VillageId,
            v.VillageName,
            v.Phase,
            COALESCE(v.plots, 0) AS TotalPlots,

            COALESCE(
                beneficiary_summary.RegisteredBeneficiaries,
                0
            ) AS RegisteredBeneficiaries,

            COALESCE(
                beneficiary_summary.AllottedBeneficiaries,
                0
            ) AS AllottedBeneficiaries,

            COALESCE(
                beneficiary_summary.ApprovedPaid,
                0
            ) AS ApprovedPaid,

            COALESCE(
                beneficiary_summary.ApprovedUnpaid,
                0
            ) AS ApprovedUnpaid,

            COALESCE(
                beneficiary_summary.PendingApprovalPayment,
                0
            ) AS PendingApprovalPayment,

            COALESCE(
                beneficiary_summary.Rejected,
                0
            ) AS Rejected,

            COALESCE(
                beneficiary_summary.AllotmentCancelled,
                0
            ) AS AllotmentCancelled
        ")
            ->orderBy('v.Phase')
            ->orderBy('v.VillageName');
    }

    /*
    |--------------------------------------------------------------------------
    | Village Report Common Data
    |--------------------------------------------------------------------------
    */

    private function dashboardAllotmentStats(Request $request): object
    {
        $phase = $this->villageReportPhase($request);

        $villageId = $this->villageReportVillageId($request);

        return DB::table('OwnerMaster as o')
            ->join(
                'FlatMaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )
            ->when($phase !== null, function ($query) use ($phase) {
                $query->where('o.Phase', $phase);
            })
            ->when($villageId !== null, function ($query) use ($villageId) {
                $query->where('o.VillageId', $villageId);
            })
            ->selectRaw("
            COUNT(*) AS AllottedBeneficiaries,

            COALESCE(
                SUM(
                    CASE
                        WHEN o.IsApproved = 1
                            AND o.IsPaid = 1
                            AND o.IsAllotmentCancelled = 0
                        THEN 1
                        ELSE 0
                    END
                ),
                0
            ) AS ApprovedPaid,

            COALESCE(
                SUM(
                    CASE
                        WHEN o.IsApproved = 1
                            AND o.IsPaid = 0
                            AND o.IsAllotmentCancelled = 0
                        THEN 1
                        ELSE 0
                    END
                ),
                0
            ) AS ApprovedUnpaid,

            COALESCE(
                SUM(
                    CASE
                        WHEN o.IsApproved = 0
                            AND o.IsPaid = 0
                            AND o.IsRejected = 0
                        THEN 1
                        ELSE 0
                    END
                ),
                0
            ) AS PendingApprovalPayment,

            COALESCE(
                SUM(
                    CASE
                        WHEN o.IsRejected = 1
                        THEN 1
                        ELSE 0
                    END
                ),
                0
            ) AS Rejected,

            COALESCE(
                SUM(
                    CASE
                        WHEN o.IsAllotmentCancelled = 1
                        THEN 1
                        ELSE 0
                    END
                ),
                0
            ) AS AllotmentCancelled
        ")
            ->first();
    }


    private function villageReportData(
        Request $request,
        bool $paginate = true
    ): array {
        $phase = $this->villageReportPhase($request);

        /*
        |--------------------------------------------------------------------------
        | केवल एक database query
        |--------------------------------------------------------------------------
        */
        $allReportRows = $this->villageReportQuery($request)->get();

        /*
        |--------------------------------------------------------------------------
        | Gross Total
        |--------------------------------------------------------------------------
        */
        $grossTotal = (object) [
            'TotalVillages' =>
                $allReportRows->count(),

            'TotalPlots' =>
                (int) $allReportRows->sum('TotalPlots'),

            'RegisteredBeneficiaries' =>
                (int) $allReportRows->sum(
                    'RegisteredBeneficiaries'
                ),

            'AllottedBeneficiaries' =>
                (int) $allReportRows->sum(
                    'AllottedBeneficiaries'
                ),

            'ApprovedPaid' =>
                (int) $allReportRows->sum('ApprovedPaid'),

            'ApprovedUnpaid' =>
                (int) $allReportRows->sum('ApprovedUnpaid'),

            'PendingApprovalPayment' =>
                (int) $allReportRows->sum(
                    'PendingApprovalPayment'
                ),

            'Rejected' =>
                (int) $allReportRows->sum('Rejected'),

            'AllotmentCancelled' =>
                (int) $allReportRows->sum(
                    'AllotmentCancelled'
                ),
        ];

        /*
        |--------------------------------------------------------------------------
        | Screen Pagination
        |--------------------------------------------------------------------------
        | केवल 260 villages हैं, इसलिए collection pagination fast रहेगी।
        |--------------------------------------------------------------------------
        */
        if ($paginate) {
            $perPage = 50;

            $currentPage = LengthAwarePaginator::resolveCurrentPage();

            $currentItems = $allReportRows
                ->forPage($currentPage, $perPage)
                ->values();

            $report = new LengthAwarePaginator(
                $currentItems,
                $allReportRows->count(),
                $perPage,
                $currentPage,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        } else {
            /*
            |--------------------------------------------------------------------------
            | Print और PDF में सभी 260 villages
            |--------------------------------------------------------------------------
            */
            $report = $allReportRows;
        }

        /*
        |--------------------------------------------------------------------------
        | Village Dropdown
        |--------------------------------------------------------------------------
        */
        $villages = DB::table('VillageMaster as v')
            ->whereNotNull('v.plots')
            ->where('v.plots', '>', 0)

            ->when($phase !== null, function ($query) use ($phase) {
                $query->where('v.Phase', $phase);
            })

            ->select([
                'v.VillageId',
                'v.VillageName',
                'v.Phase',
            ])

            ->orderBy('v.Phase')
            ->orderBy('v.VillageName')
            ->get();

        return [
            'report' => $report,
            'grossTotal' => $grossTotal,
            'villages' => $villages,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Village Report Page
    |--------------------------------------------------------------------------
    */
    public function villageWiseReport(Request $request)
    {
        $this->prepareLargeReportRequest();
        return view(
            'mmgay.super-admin.village-report',
            $this->villageReportData($request, true)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Village Report PDF
    |--------------------------------------------------------------------------
    | paginate=false होने के कारण सभी matching villages आएंगे।
    | बिना filters के केवल plots > 0 वाले 260 villages आएंगे।
    |--------------------------------------------------------------------------
    */
    public function villageReportPdf(Request $request)
    {
        $this->prepareLargeReportRequest();
        $data = $this->villageReportData($request, false);

        $pdf = Pdf::loadView(
            'mmgay.super-admin.village-report-pdf',
            $data
        )
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download(
            'Village_Report_'
            . now()->format('d-m-Y_H-i-s')
            . '.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Village Report Print
    |--------------------------------------------------------------------------
    */
    public function villageReportPrint(Request $request)
    {
        $this->prepareLargeReportRequest();
        $data = $this->villageReportData($request, false);

        /*
        |--------------------------------------------------------------------------
        | Keep only valid villages with plots
        |--------------------------------------------------------------------------
        */
        $report = collect($data['report'] ?? [])
            ->filter(function ($row) {
                return !empty($row->VillageId)
                    && (int) ($row->TotalPlots ?? 0) > 0;
            })

            /*
            |--------------------------------------------------------------------------
            | Remove duplicate village rows
            |--------------------------------------------------------------------------
            */
            ->unique(function ($row) {
                return (string) $row->VillageId;
            })

            ->sortBy([
                ['Phase', 'asc'],
                ['VillageName', 'asc'],
            ])
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Print records
        |--------------------------------------------------------------------------
        */
        $data['report'] = $report;
        $data['totalVillages'] = $report->count();

        /*
        |--------------------------------------------------------------------------
        | Totals from only printed villages
        |--------------------------------------------------------------------------
        */
        $data['grossTotal'] = (object) [
            'TotalPlots' => (int) $report->sum(
                fn($row) => (int) ($row->TotalPlots ?? 0)
            ),

            'RegisteredBeneficiaries' => (int) $report->sum(
                fn($row) => (int) ($row->RegisteredBeneficiaries ?? 0)
            ),

            'AllottedBeneficiaries' => (int) $report->sum(
                fn($row) => (int) ($row->AllottedBeneficiaries ?? 0)
            ),

            'ApprovedPaid' => (int) $report->sum(
                fn($row) => (int) ($row->ApprovedPaid ?? 0)
            ),

            'ApprovedUnpaid' => (int) $report->sum(
                fn($row) => (int) ($row->ApprovedUnpaid ?? 0)
            ),

            'PendingApprovalPayment' => (int) $report->sum(
                fn($row) => (int) ($row->PendingApprovalPayment ?? 0)
            ),

            'Rejected' => (int) $report->sum(
                fn($row) => (int) ($row->Rejected ?? 0)
            ),

            'AllotmentCancelled' => (int) $report->sum(
                fn($row) => (int) ($row->AllotmentCancelled ?? 0)
            ),
        ];

        return view(
            'mmgay.super-admin.village-report-print',
            $data
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Village Report CSV
    |--------------------------------------------------------------------------
    | cursor() से records stream होंगे और memory कम लगेगी।
    |--------------------------------------------------------------------------
    */
    public function villageReportCsv(Request $request)
    {
        $this->prepareLargeReportRequest();
        $fileName = 'Village_Report_'
            . now()->format('d-m-Y_H-i-s')
            . '.csv';

        /*
        |--------------------------------------------------------------------------
        | Query केवल एक बार
        |--------------------------------------------------------------------------
        */
        $rows = $this->villageReportQuery($request)->get();

        $allotmentStats = $this->dashboardAllotmentStats($request);

        $grossTotal = [
            'TotalPlots' => (int) $rows->sum(
                fn($row) => (int) ($row->TotalPlots ?? 0)
            ),

            'RegisteredBeneficiaries' => (int) $rows->sum(
                fn($row) => (int) ($row->RegisteredBeneficiaries ?? 0)
            ),

            'AllottedBeneficiaries' =>
                (int) ($allotmentStats->AllottedBeneficiaries ?? 0),

            'ApprovedPaid' =>
                (int) ($allotmentStats->ApprovedPaid ?? 0),

            'ApprovedUnpaid' =>
                (int) ($allotmentStats->ApprovedUnpaid ?? 0),

            'PendingApprovalPayment' =>
                (int) ($allotmentStats->PendingApprovalPayment ?? 0),

            'Rejected' =>
                (int) ($allotmentStats->Rejected ?? 0),

            'AllotmentCancelled' =>
                (int) ($allotmentStats->AllotmentCancelled ?? 0),
        ];

        return response()->streamDownload(
            function () use ($rows, $grossTotal) {
                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    throw new \RuntimeException(
                        'CSV stream could not be opened.'
                    );
                }

                fwrite($handle, "\xEF\xBB\xBF");

                fputcsv($handle, [
                    'Sr. No.',
                    'Village',
                    'Phase',
                    'Total Plots',
                    'Applicants',
                    'Allotted',
                    'Approved & Paid',
                    'Approved & Unpaid',
                    'Yet to be Approved',
                    'Rejected',
                    'Cancelled',
                ]);

                foreach ($rows as $index => $row) {
                    fputcsv($handle, [
                        $index + 1,
                        $row->VillageName ?? '-',
                        $row->Phase ?? '-',
                        (int) ($row->TotalPlots ?? 0),
                        (int) ($row->RegisteredBeneficiaries ?? 0),
                        (int) ($row->AllottedBeneficiaries ?? 0),
                        (int) ($row->ApprovedPaid ?? 0),
                        (int) ($row->ApprovedUnpaid ?? 0),
                        (int) ($row->PendingApprovalPayment ?? 0),
                        (int) ($row->Rejected ?? 0),
                        (int) ($row->AllotmentCancelled ?? 0),
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Empty separator row
                |--------------------------------------------------------------------------
                */
                fputcsv($handle, []);

                /*
                |--------------------------------------------------------------------------
                | Gross Total row
                |--------------------------------------------------------------------------
                */
                fputcsv($handle, [
                    '',
                    'Gross Total',
                    '',
                    $grossTotal['TotalPlots'],
                    $grossTotal['RegisteredBeneficiaries'],
                    $grossTotal['AllottedBeneficiaries'],
                    $grossTotal['ApprovedPaid'],
                    $grossTotal['ApprovedUnpaid'],
                    $grossTotal['PendingApprovalPayment'],
                    $grossTotal['Rejected'],
                    $grossTotal['AllotmentCancelled'],
                ]);

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Village Report Excel
    |--------------------------------------------------------------------------
    */
    public function villageReportExcel(Request $request)
    {
        return Excel::download(
            new VillageReportExport($request),
            'Village_Report_'
            . now()->format('d-m-Y_H-i-s')
            . '.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Village Site Development
    |--------------------------------------------------------------------------
    | यहां phase filter जानबूझकर development query पर नहीं लगाया गया है।
    | VillageId से उस village के सभी development updates दिखेंगे।
    |--------------------------------------------------------------------------
    */
    public function villageSiteDevelopment(
        Request $request,
        int $villageId
    ) {
        $village = DB::table('VillageMaster as v')
            ->where('v.VillageId', $villageId)
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
        | Direct indexed lookup
        |--------------------------------------------------------------------------
        */
        $records = DB::table('mmgay_site_developments as sd')
            ->where('sd.village_id', '=', $villageId)
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

                    'road_status' =>
                        filled($record->road_status)
                        ? $record->road_status
                        : 'Not Updated',

                    'water_status' =>
                        filled($record->water_status)
                        ? $record->water_status
                        : 'Not Updated',

                    'electricity_status' =>
                        filled($record->electricity_status)
                        ? $record->electricity_status
                        : 'Not Updated',

                    'sewerage_status' =>
                        filled($record->sewerage_status)
                        ? $record->sewerage_status
                        : 'Not Updated',

                    'remarks' =>
                        $record->remarks ?: 'No remarks added.',

                    'updated_by' =>
                        $record->updated_by ?: '-',

                    'created_at' =>
                        $this->formatDevelopmentDate(
                            $record->created_at
                        ),

                    'updated_at' =>
                        $this->formatDevelopmentDate(
                            $record->updated_at
                        ),

                    'road_photo_url' =>
                        $this->siteDevelopmentPhotoUrl(
                            $record->road_photo
                        ),

                    'water_photo_url' =>
                        $this->siteDevelopmentPhotoUrl(
                            $record->water_photo
                        ),

                    'electricity_photo_url' =>
                        $this->siteDevelopmentPhotoUrl(
                            $record->electricity_photo
                        ),

                    'sewerage_photo_url' =>
                        $this->siteDevelopmentPhotoUrl(
                            $record->sewerage_photo
                        ),
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

    /*
    |--------------------------------------------------------------------------
    | Development Date Formatter
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Development Photo URL
    |--------------------------------------------------------------------------
    */
    private function siteDevelopmentPhotoUrl(?string $photo): ?string
    {
        if (empty($photo)) {
            return null;
        }

        $photo = str_replace('\\', '/', trim($photo));

        if (str_starts_with($photo, 'http')) {
            return $photo;
        }

        if (str_starts_with($photo, '/storage/')) {
            return asset(ltrim($photo, '/'));
        }

        if (str_starts_with($photo, 'storage/')) {
            return asset($photo);
        }

        if (str_starts_with($photo, 'public/')) {
            $photo = substr($photo, 7);
        }

        return asset('storage/' . ltrim($photo, '/'));
    }

    // VIllage Function End

    private function applyApplicantFilters(
        $query,
        Request $request
    ) {
        $phase = $request->filled('phase')
            ? (int) $request->phase
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->district_id
            : null;

        $blockId = $request->filled('block_id')
            ? (int) $request->block_id
            : null;

        $villageId = $request->filled('village_id')
            ? (int) $request->village_id
            : null;

        $status = trim(
            (string) $request->query('status', '')
        );

        $search = trim(
            (string) $request->query('search', '')
        );

        $query
            ->when(
                $phase,
                fn($q) => $q->where('o.Phase', $phase)
            )
            ->when(
                $districtId,
                fn($q) => $q->where(
                    'o.DistrictId',
                    $districtId
                )
            )
            ->when(
                $blockId,
                fn($q) => $q->where(
                    'o.BlockId',
                    $blockId
                )
            )
            ->when(
                $villageId,
                fn($q) => $q->where(
                    'o.VillageId',
                    $villageId
                )
            );

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        | Exact/index-friendly checks पहले और contains search बाद में।
        |--------------------------------------------------------------------------
        */
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                /*
                |------------------------------------------------------------------
                | Numeric/mobile/ID searches
                |------------------------------------------------------------------
                */
                if (ctype_digit($search)) {
                    $subQuery
                        ->where('o.OwnerId', (int) $search)
                        ->orWhere('o.MobileNo', $search)
                        ->orWhere('o.RegistrationNo', $search)
                        ->orWhere('o.PPPId', $search)
                        ->orWhere('f.FlatNo', $search);

                    return;
                }

                /*
                |------------------------------------------------------------------
                | General text search
                |------------------------------------------------------------------
                */
                $likeSearch = '%' . $search . '%';

                $subQuery
                    ->where(
                        'o.OwnerName',
                        'like',
                        $likeSearch
                    )
                    ->orWhere(
                        'o.MobileNo',
                        'like',
                        $likeSearch
                    )
                    ->orWhere(
                        'o.RegistrationNo',
                        'like',
                        $likeSearch
                    )
                    ->orWhere(
                        'o.PPPId',
                        'like',
                        $likeSearch
                    )
                    ->orWhere(
                        'f.FlatNo',
                        'like',
                        $likeSearch
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */
        switch ($status) {
            case 'approved_paid':
                $query
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1);
                break;

            case 'approved_unpaid':
                $query
                    ->where('o.IsApproved', 1)
                    ->where(function ($subQuery) {
                        $subQuery
                            ->where('o.IsPaid', 0)
                            ->orWhereNull('o.IsPaid');
                    });
                break;

            case 'pending':
                $query->where(function ($subQuery) {
                    $subQuery
                        ->where('o.IsApproved', 0)
                        ->orWhereNull('o.IsApproved');
                });
                break;

            case 'rejected':
                $query->where('o.IsRejected', 1);
                break;

            case 'cancelled':
                $query->where(
                    'o.IsAllotmentCancelled',
                    1
                );
                break;
        }

        return $query;
    }



    public function applicants(Request $request)
    {
        DB::disableQueryLog();

        $phase = $request->filled('phase')
            ? (int) $request->phase
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->district_id
            : null;

        $blockId = $request->filled('block_id')
            ? (int) $request->block_id
            : null;

        $perPage = (int) $request->query(
            'per_page',
            20
        );

        if (
            !in_array(
                $perPage,
                [20, 50, 100, 200],
                true
            )
        ) {
            $perPage = 20;
        }

        /*
        |--------------------------------------------------------------------------
        | Applicant Records
        |--------------------------------------------------------------------------
        | Indexed OwnerId DESC ordering.
        |--------------------------------------------------------------------------
        */
        $applicantQuery = $this->applicantsQuery($request);

        $applicantTotalKey = $this->reportCacheKey(
            'super_admin_applicants_total',
            $request,
            ['phase', 'district_id', 'block_id', 'village_id', 'status', 'search']
        );

        $totalApplicants = (int) Cache::remember(
            $applicantTotalKey,
            now()->addSeconds($this->reportCacheSeconds()),
            fn() => (clone $applicantQuery)->count('o.OwnerId')
        );

        $currentPage = max(1, (int) $request->query('page', 1));
        $lastPage = max(1, (int) ceil($totalApplicants / $perPage));
        $currentPage = min($currentPage, $lastPage);

        $applicantRows = (clone $applicantQuery)
            ->orderByDesc('o.OwnerId')
            ->forPage($currentPage, $perPage)
            ->get();

        $applicants = new LengthAwarePaginator(
            $applicantRows,
            $totalApplicants,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
                'query' => $request->except('page'),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Village Dropdown
        |--------------------------------------------------------------------------
        | OwnerMaster को JOIN करके DISTINCT करने के बजाय VillageMaster से query
        | चलती है और EXISTS से matching owner check होता है।
        |--------------------------------------------------------------------------
        */
        $villageDropdownKey = $this->reportCacheKey(
            'super_admin_applicant_villages',
            $request,
            ['phase', 'district_id', 'block_id']
        );

        $villages = Cache::remember(
            $villageDropdownKey,
            now()->addSeconds($this->reportCacheSeconds()),
            function () use ($phase, $districtId, $blockId) {
                return DB::table('VillageMaster as v')
                    ->where('v.plots', '>', 0)
                    ->when(
                        $districtId,
                        fn($q) => $q->where(
                            'v.DistrictId',
                            $districtId
                        )
                    )
                    ->when(
                        $blockId,
                        fn($q) => $q->where(
                            'v.BlockId',
                            $blockId
                        )
                    )
                    ->when(
                        $phase,
                        fn($q) => $q->where(
                            'v.Phase',
                            $phase
                        )
                    )
                    ->whereExists(function ($query) use ($phase, $districtId, $blockId) {
                        $query
                            ->selectRaw('1')
                            ->from('OwnerMaster as vo')
                            ->whereColumn(
                                'vo.VillageId',
                                'v.VillageId'
                            )
                            ->when(
                                $phase,
                                fn($q) => $q->where(
                                    'vo.Phase',
                                    $phase
                                )
                            )
                            ->when(
                                $districtId,
                                fn($q) => $q->where(
                                    'vo.DistrictId',
                                    $districtId
                                )
                            )
                            ->when(
                                $blockId,
                                fn($q) => $q->where(
                                    'vo.BlockId',
                                    $blockId
                                )
                            );
                    })
                    ->select([
                        'v.VillageId',
                        'v.VillageName',
                    ])
                    ->orderBy('v.VillageName')
                    ->get();
            }
        );

        return view(
            'mmgay.super-admin.applicants.index',
            compact(
                'applicants',
                'villages',
                'perPage'
            )
        );
    }

    private function applicantsQuery(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Main Query
        |--------------------------------------------------------------------------
        | VillageMaster INNER JOIN पुराने logic जैसा ही रखा गया है।
        | FlatMaster LEFT JOIN display और flat search के लिए जरूरी है।
        |--------------------------------------------------------------------------
        */
        $query = DB::table('OwnerMaster as o')
            ->join(
                'VillageMaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )
            ->leftJoin(
                'FlatMaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )
            ->where('v.plots', '>', 0)
            ->select([
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
            ])
            ->selectRaw("
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
        ");

        return $this->applyApplicantFilters(
            $query,
            $request
        );
    }

    public function applicantsExcel(Request $request)
    {
        $this->prepareLargeReportRequest();
        $fileName = 'applicants-'
            . now()->format('Y-m-d-H-i-s')
            . '.csv';

        $downloadToken = $request->query('download_token');

        /*
        |--------------------------------------------------------------------------
        | Query बनाएं, लेकिन get() न करें
        |--------------------------------------------------------------------------
        */
        $query = $this->applicantsQuery($request)
            ->orderByDesc('o.OwnerId');

        $response = response()->streamDownload(
            function () use ($query) {
                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    throw new \RuntimeException(
                        'CSV output stream could not be opened.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | UTF-8 BOM
                |--------------------------------------------------------------------------
                | Excel में Hindi/Unicode characters सही दिखेंगे।
                |--------------------------------------------------------------------------
                */
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

                $serialNumber = 1;

                /*
                |--------------------------------------------------------------------------
                | cursor() records एक-एक करके stream करेगा
                |--------------------------------------------------------------------------
                */
                foreach ($query->cursor() as $applicant) {
                    fputcsv($handle, [
                        $serialNumber,
                        $applicant->OwnerId ?? '',
                        $applicant->RegistrationNo ?? '',
                        $applicant->OwnerName ?? '',
                        $applicant->FatherHusbandName ?? '',
                        $applicant->MobileNo ?? '',
                        $applicant->PPPId ?? '',
                        $applicant->VillageName ?? '',
                        $applicant->Phase ?? '',
                        $applicant->FlatId ?? '',
                        $applicant->FlatNo ?? '',
                        $applicant->ApplicantStatus ?? 'Allotted',
                    ]);

                    $serialNumber++;

                    /*
                    |--------------------------------------------------------------------------
                    | Periodic flush
                    |--------------------------------------------------------------------------
                    | Large CSV में browser को output जल्दी मिलता रहेगा।
                    |--------------------------------------------------------------------------
                    */
                    if ($serialNumber % 500 === 0) {
                        fflush($handle);
                    }
                }

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',

                'Content-Disposition' =>
                    'attachment; filename="' . $fileName . '"',

                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate',

                'Pragma' =>
                    'no-cache',

                'Expires' =>
                    '0',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Download completion token
        |--------------------------------------------------------------------------
        */
        if (!empty($downloadToken)) {
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

    public function applicantsCsv(Request $request)
    {
        $this->prepareLargeReportRequest();
        $query = $this->applicantsQuery($request)
            ->orderByDesc('o.OwnerId');

        return response()->streamDownload(function () use ($query) {

            $file = fopen('php://output', 'w');

            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Sr.',
                'Owner ID',
                'Application No.',
                'Applicant',
                'Father/Husband',
                'Mobile',
                'PPP ID',
                'Village',
                'Phase',
                'Flat No.',
                'Status'
            ]);

            $sr = 1;

            foreach ($query->cursor() as $row) {

                fputcsv($file, [

                    $sr++,

                    $row->OwnerId,

                    $row->RegistrationNo,

                    $row->OwnerName,

                    $row->FatherHusbandName,

                    $row->MobileNo,

                    $row->PPPId,

                    $row->VillageName,

                    $row->Phase,

                    $row->FlatNo,

                    $row->ApplicantStatus

                ]);
            }

            fclose($file);

        }, 'Applicants_' . now()->format('Ymd_His') . '.csv');
    }

    public function applicantsPrint(Request $request)
    {
        $this->prepareLargeReportRequest();
        $perBatch = (int) $request->query('print_limit', 500);

        if (!in_array($perBatch, [200, 500, 1000, 2000], true)) {
            $perBatch = 500;
        }

        $printPage = max(
            1,
            (int) $request->query('print_page', 1)
        );

        $query = $this->applicantsQuery($request)
            ->orderByDesc('o.OwnerId');

        /*
        |--------------------------------------------------------------------------
        | Count once for print navigation
        |--------------------------------------------------------------------------
        */
        $totalRecords = (clone $query)->count();

        $totalPrintPages = max(
            1,
            (int) ceil($totalRecords / $perBatch)
        );

        if ($printPage > $totalPrintPages) {
            $printPage = $totalPrintPages;
        }

        /*
        |--------------------------------------------------------------------------
        | Only current print batch
        |--------------------------------------------------------------------------
        */
        $records = $query
            ->forPage($printPage, $perBatch)
            ->get();

        $startSerial =
            (($printPage - 1) * $perBatch) + 1;

        return view(
            'mmgay.super-admin.applicants-print',
            compact(
                'records',
                'totalRecords',
                'totalPrintPages',
                'printPage',
                'perBatch',
                'startSerial'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PDF Export
    |--------------------------------------------------------------------------
    */

    public function applicantsPdf(Request $request)
    {
        $this->prepareLargeReportRequest();
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $applicants = $this->applicantsQuery($request)
            ->orderByDesc('o.OwnerId')
            ->get();

        return view(
            'mmgay.super-admin.applicants.print',
            compact('applicants')
        );
    }

    private function allotmentReportBaseQuery(Request $request)
    {
        $phase = $request->filled('phase')
            ? (int) $request->phase
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->district_id
            : null;

        $blockId = $request->filled('block_id')
            ? (int) $request->block_id
            : null;

        $villageId = $request->filled('village_id')
            ? (int) $request->village_id
            : null;

        $search = trim(
            (string) $request->query('search', '')
        );

        /*
        |--------------------------------------------------------------------------
        | VillageMaster INNER JOIN
        |--------------------------------------------------------------------------
        | Correlated whereExists की जगह indexed join रखा गया है।
        |--------------------------------------------------------------------------
        */
        $query = DB::table('OwnerMaster as o')
            ->join('VillageMaster as vm', function ($join) {
                $join->on(
                    'vm.VillageId',
                    '=',
                    'o.VillageId'
                );
            })
            ->where('vm.plots', '>', 0)
            ->where('o.FlatId', '>', 0)

            ->when(
                $phase !== null,
                fn($q) => $q->where(
                    'o.Phase',
                    $phase
                )
            )

            ->when(
                $districtId !== null,
                fn($q) => $q->where(
                    'o.DistrictId',
                    $districtId
                )
            )

            ->when(
                $blockId !== null,
                fn($q) => $q->where(
                    'o.BlockId',
                    $blockId
                )
            )

            ->when(
                $villageId !== null,
                fn($q) => $q->where(
                    'o.VillageId',
                    $villageId
                )
            );

        if ($search === '') {
            return $query;
        }

        /*
        |--------------------------------------------------------------------------
        | Numeric search
        |--------------------------------------------------------------------------
        | Exact search index-friendly रहेगी।
        |--------------------------------------------------------------------------
        */
        if (ctype_digit($search)) {
            return $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where(
                        'o.OwnerId',
                        (int) $search
                    )
                    ->orWhere(
                        'o.MobileNo',
                        $search
                    )
                    ->orWhere(
                        'o.RegistrationNo',
                        $search
                    )
                    ->orWhere(
                        'o.PPPId',
                        $search
                    )
                    ->orWhereExists(function ($flatQuery) use ($search) {
                        $flatQuery
                            ->selectRaw('1')
                            ->from('FlatMaster as sf')
                            ->whereColumn(
                                'sf.FlatId',
                                'o.FlatId'
                            )
                            ->where(
                                'sf.FlatNo',
                                $search
                            );
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | General text search
        |--------------------------------------------------------------------------
        */
        $likeSearch = '%' . $search . '%';

        return $query->where(function ($subQuery) use ($likeSearch) {
            $subQuery
                ->where(
                    'o.OwnerName',
                    'like',
                    $likeSearch
                )
                ->orWhere(
                    'o.RegistrationNo',
                    'like',
                    $likeSearch
                )
                ->orWhere(
                    'o.MobileNo',
                    'like',
                    $likeSearch
                )
                ->orWhere(
                    'o.PPPId',
                    'like',
                    $likeSearch
                )
                ->orWhere(
                    'o.FatherHusbandName',
                    'like',
                    $likeSearch
                )
                ->orWhereExists(function ($flatQuery) use ($likeSearch) {
                    $flatQuery
                        ->selectRaw('1')
                        ->from('FlatMaster as sf')
                        ->whereColumn(
                            'sf.FlatId',
                            'o.FlatId'
                        )
                        ->where(
                            'sf.FlatNo',
                            'like',
                            $likeSearch
                        );
                });
        });
    }

    private function applyAllotmentReportStatus(
        $query,
        ?string $status
    ) {
        switch ($status) {
            case 'approved_paid':
                $query
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->where('o.IsRejected', 0)
                    ->where('o.IsAllotmentCancelled', 0);
                break;

            case 'approved_unpaid':
                $query
                    ->where('o.IsApproved', 1)
                    ->where(function ($subQuery) {
                        $subQuery
                            ->where('o.IsPaid', 0)
                            ->orWhereNull('o.IsPaid');
                    })
                    ->where('o.IsRejected', 0)
                    ->where('o.IsAllotmentCancelled', 0);
                break;

            case 'pending':
                $query
                    ->where(function ($subQuery) {
                        $subQuery
                            ->where('o.IsApproved', 0)
                            ->orWhereNull('o.IsApproved');
                    })
                    ->where('o.IsRejected', 0)
                    ->where('o.IsAllotmentCancelled', 0);
                break;

            case 'rejected':
                $query
                    ->where('o.IsRejected', 1)
                    ->where('o.IsAllotmentCancelled', 0);
                break;

            case 'cancelled':
                $query->where(
                    'o.IsAllotmentCancelled',
                    1
                );
                break;
        }

        return $query;
    }

    private function allotmentReportDetailsQuery(
        Request $request
    ) {
        $query = $this->allotmentReportBaseQuery(
            $request
        );

        $query = $this->applyAllotmentReportStatus(
            $query,
            $request->query('status')
        );

        return $query
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
                'VillageMaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )
            ->leftJoin(
                'FlatMaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )
            ->select([
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
                'f.FlatNo',
            ])
            ->selectRaw("
            CASE
                WHEN o.IsAllotmentCancelled = 1
                    THEN 'Cancelled'

                WHEN o.IsRejected = 1
                    THEN 'Rejected'

                WHEN o.IsApproved = 1
                 AND o.IsPaid = 1
                    THEN 'Approved & Paid'

                WHEN o.IsApproved = 1
                    THEN 'Approved & Unpaid'

                ELSE 'Yet to be Approved'
            END AS AllotmentStatus
        ")
            ->orderByDesc('o.OwnerId');
    }

    private function allotmentSummaryCacheKey(
        Request $request
    ): string {
        return 'allotment_summary_' . md5(
            json_encode([
                'phase' =>
                    $request->query('phase'),

                'district_id' =>
                    $request->query('district_id'),

                'block_id' =>
                    $request->query('block_id'),

                'village_id' =>
                    $request->query('village_id'),

                'search' => trim(
                    (string) $request->query(
                        'search',
                        ''
                    )
                ),
            ])
        );
    }

    private function allotmentTotalCacheKey(
        Request $request
    ): string {
        return 'allotment_total_' . md5(
            json_encode([
                'phase' =>
                    $request->query('phase'),

                'district_id' =>
                    $request->query('district_id'),

                'block_id' =>
                    $request->query('block_id'),

                'village_id' =>
                    $request->query('village_id'),

                'search' => trim(
                    (string) $request->query(
                        'search',
                        ''
                    )
                ),

                'status' =>
                    $request->query('status'),
            ])
        );
    }

    private function paginateAllotmentReport(
        Request $request,
        int $perPage = 25
    ): LengthAwarePaginator {
        $currentPage = max(
            1,
            (int) $request->query('page', 1)
        );

        /*
        |--------------------------------------------------------------------------
        | Lightweight filtered query
        |--------------------------------------------------------------------------
        */
        $filteredQuery = $this->allotmentReportBaseQuery(
            $request
        );

        $filteredQuery = $this->applyAllotmentReportStatus(
            $filteredQuery,
            $request->query('status')
        );

        /*
        |--------------------------------------------------------------------------
        | Filtered total cache
        |--------------------------------------------------------------------------
        | Laravel paginate की COUNT query हर page/card click पर नहीं चलेगी।
        |--------------------------------------------------------------------------
        */
        $total = (int) Cache::remember(
            $this->allotmentTotalCacheKey($request),
            now()->addMinutes(10),
            function () use ($filteredQuery) {
                return (clone $filteredQuery)
                    ->count('o.OwnerId');
            }
        );

        $lastPage = max(
            1,
            (int) ceil($total / $perPage)
        );

        if ($currentPage > $lastPage) {
            $currentPage = $lastPage;
        }

        /*
        |--------------------------------------------------------------------------
        | Current page Owner IDs only
        |--------------------------------------------------------------------------
        */
        $ownerIds = (clone $filteredQuery)
            ->select('o.OwnerId')
            ->orderByDesc('o.OwnerId')
            ->offset(
                ($currentPage - 1) * $perPage
            )
            ->limit($perPage)
            ->pluck('o.OwnerId')
            ->map(
                fn($ownerId) => (int) $ownerId
            )
            ->all();

        $records = collect();

        /*
        |--------------------------------------------------------------------------
        | Current 25 records details only
        |--------------------------------------------------------------------------
        */
        if (!empty($ownerIds)) {
            $records = DB::table('OwnerMaster as o')
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
                    'VillageMaster as v',
                    'v.VillageId',
                    '=',
                    'o.VillageId'
                )
                ->leftJoin(
                    'FlatMaster as f',
                    'f.FlatId',
                    '=',
                    'o.FlatId'
                )
                ->whereIn(
                    'o.OwnerId',
                    $ownerIds
                )
                ->select([
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
                    'f.FlatNo',
                ])
                ->orderByDesc('o.OwnerId')
                ->get();
        }

        return new LengthAwarePaginator(
            $records,
            $total,
            $perPage,
            $currentPage,
            [
                'path' =>
                    $request->url(),

                'pageName' =>
                    'page',

                'query' =>
                    $request->except('page'),
            ]
        );
    }

    public function allotmentReport(Request $request)
    {
        $this->prepareLargeReportRequest();
        DB::disableQueryLog();

        /*
        |--------------------------------------------------------------------------
        | Selected filters
        |--------------------------------------------------------------------------
        */
        $districtId = $request->filled('district_id')
            ? (int) $request->district_id
            : null;

        $blockId = $request->filled('block_id')
            ? (int) $request->block_id
            : null;

        $phase = $request->filled('phase')
            ? (int) $request->phase
            : null;

        /*
        |--------------------------------------------------------------------------
        | Phases
        |--------------------------------------------------------------------------
        */
        $phases = Cache::remember(
            'allotment_report_phases',
            now()->addHours(1),
            function () {
                return DB::table('OwnerMaster')
                    ->whereNotNull('Phase')
                    ->where('Phase', '<>', '')
                    ->distinct()
                    ->orderBy('Phase')
                    ->pluck('Phase');
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Districts
        |--------------------------------------------------------------------------
        */
        $districts = Cache::remember(
            'allotment_report_districts',
            now()->addHours(1),
            function () {
                return DB::table('DistrictMaster')
                    ->select([
                        'DistrictId',
                        'DistrictName',
                    ])
                    ->orderBy('DistrictName')
                    ->get();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Blocks
        |--------------------------------------------------------------------------
        */
        $blocksCacheKey =
            'allotment_report_blocks_'
            . ($districtId ?? 'all');

        $blocks = Cache::remember(
            $blocksCacheKey,
            now()->addHours(1),
            function () use ($districtId) {
                return DB::table('BlockMaster')
                    ->when(
                        $districtId !== null,
                        fn($query) => $query->where(
                            'DistrictId',
                            $districtId
                        )
                    )
                    ->select([
                        'BlockId',
                        'BlockName',
                        'DistrictId',
                    ])
                    ->orderBy('BlockName')
                    ->get();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Villages
        |--------------------------------------------------------------------------
        */
        $villagesCacheKey = sprintf(
            'allotment_report_villages_%s_%s_%s',
            $districtId ?? 'all',
            $blockId ?? 'all',
            $phase ?? 'all'
        );

        $villages = Cache::remember(
            $villagesCacheKey,
            now()->addHours(1),
            function () use ($districtId, $blockId, $phase) {
                return DB::table('VillageMaster as v')
                    ->where('v.plots', '>', 0)

                    ->when(
                        $districtId !== null,
                        fn($query) => $query->where(
                            'v.DistrictId',
                            $districtId
                        )
                    )

                    ->when(
                        $blockId !== null,
                        fn($query) => $query->where(
                            'v.BlockId',
                            $blockId
                        )
                    )

                    ->when(
                        $phase !== null,
                        fn($query) => $query->where(
                            'v.Phase',
                            $phase
                        )
                    )

                    ->select([
                        'v.VillageId',
                        'v.VillageName',
                        'v.DistrictId',
                        'v.BlockId',
                    ])
                    ->orderBy('v.VillageName')
                    ->get();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Summary cards
        |--------------------------------------------------------------------------
        */
        $summary = Cache::remember(
            $this->allotmentSummaryCacheKey($request),
            now()->addMinutes(10),
            function () use ($request) {
                return $this
                    ->allotmentReportBaseQuery($request)
                    ->selectRaw("
                    COUNT(o.OwnerId) AS Total,

                    COALESCE(
                        SUM(
                            CASE
                                WHEN o.IsApproved = 1
                                 AND o.IsPaid = 1
                                 AND o.IsRejected = 0
                                 AND o.IsAllotmentCancelled = 0
                                THEN 1
                                ELSE 0
                            END
                        ),
                        0
                    ) AS ApprovedPaid,

                    COALESCE(
                        SUM(
                            CASE
                                WHEN o.IsApproved = 1
                                 AND (
                                    o.IsPaid = 0
                                    OR o.IsPaid IS NULL
                                 )
                                 AND o.IsRejected = 0
                                 AND o.IsAllotmentCancelled = 0
                                THEN 1
                                ELSE 0
                            END
                        ),
                        0
                    ) AS ApprovedUnpaid,

                    COALESCE(
                        SUM(
                            CASE
                                WHEN (
                                    o.IsApproved = 0
                                    OR o.IsApproved IS NULL
                                )
                                 AND o.IsRejected = 0
                                 AND o.IsAllotmentCancelled = 0
                                THEN 1
                                ELSE 0
                            END
                        ),
                        0
                    ) AS PendingApproval,

                    COALESCE(
                        SUM(
                            CASE
                                WHEN o.IsRejected = 1
                                 AND o.IsAllotmentCancelled = 0
                                THEN 1
                                ELSE 0
                            END
                        ),
                        0
                    ) AS Rejected,

                    COALESCE(
                        SUM(
                            CASE
                                WHEN o.IsAllotmentCancelled = 1
                                THEN 1
                                ELSE 0
                            END
                        ),
                        0
                    ) AS Cancelled
                ")
                    ->first();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Fast filtered pagination
        |--------------------------------------------------------------------------
        */
        $allotments = $this->paginateAllotmentReport(
            $request,
            25
        );

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

    public function allotmentReportCsv(
        Request $request
    ) {
        $this->prepareLargeReportRequest();
        $fileName = 'Allotment_Report_'
            . now()->format('d-m-Y_H-i-s')
            . '.csv';

        $query = $this->allotmentReportDetailsQuery(
            $request
        );

        return response()->streamDownload(
            function () use ($query) {
                $handle = fopen(
                    'php://output',
                    'w'
                );

                if ($handle === false) {
                    throw new \RuntimeException(
                        'CSV output stream could not be opened.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | UTF-8 BOM for Excel
                |--------------------------------------------------------------------------
                */
                fwrite(
                    $handle,
                    "\xEF\xBB\xBF"
                );

                fputcsv($handle, [
                    'Sr. No.',
                    'Owner ID',
                    'Application No.',
                    'Applicant',
                    'Father / Husband',
                    'Mobile',
                    'PPP ID',
                    'District',
                    'Block',
                    'Village',
                    'Phase',
                    'Flat ID',
                    'Flat No.',
                    'Status',
                ]);

                $serialNumber = 1;

                foreach ($query->cursor() as $record) {
                    fputcsv($handle, [
                        $serialNumber++,
                        $record->OwnerId ?? '',
                        $record->RegistrationNo ?? '',
                        $record->OwnerName ?? '',
                        $record->FatherHusbandName ?? '',
                        $record->MobileNo ?? '',
                        $record->PPPId ?? '',
                        $record->DistrictName ?? '',
                        $record->BlockName ?? '',
                        $record->VillageName ?? '',
                        $record->Phase ?? '',
                        $record->FlatId ?? '',
                        $record->FlatNo ?? '',
                        $record->AllotmentStatus ?? '',
                    ]);

                    if ($serialNumber % 500 === 0) {
                        fflush($handle);
                    }
                }

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',

                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate',

                'Pragma' =>
                    'no-cache',

                'Expires' =>
                    '0',
            ]
        );
    }

    public function allotmentReportPrint(
        Request $request
    ) {
        $this->prepareLargeReportRequest();
        $printLimit = (int) $request->query(
            'print_limit',
            500
        );

        if (
            !in_array(
                $printLimit,
                [200, 500, 1000, 2000],
                true
            )
        ) {
            $printLimit = 500;
        }

        $printPage = max(
            1,
            (int) $request->query(
                'print_page',
                1
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Lightweight count
        |--------------------------------------------------------------------------
        */
        $countQuery = $this->allotmentReportBaseQuery(
            $request
        );

        $countQuery = $this->applyAllotmentReportStatus(
            $countQuery,
            $request->query('status')
        );

        $totalRecords = (int) $countQuery
            ->count('o.OwnerId');

        $totalPrintPages = max(
            1,
            (int) ceil(
                $totalRecords / $printLimit
            )
        );

        if ($printPage > $totalPrintPages) {
            $printPage = $totalPrintPages;
        }

        /*
        |--------------------------------------------------------------------------
        | Current print batch only
        |--------------------------------------------------------------------------
        */
        $records = $this
            ->allotmentReportDetailsQuery($request)
            ->forPage(
                $printPage,
                $printLimit
            )
            ->get();

        $startSerial =
            (($printPage - 1) * $printLimit) + 1;

        return view(
            'mmgay.super-admin.allotment-report-print',
            compact(
                'records',
                'totalRecords',
                'totalPrintPages',
                'printPage',
                'printLimit',
                'startSerial'
            )
        );
    }

    public function exportAllotmentExcel(Request $request)
    {
        $this->prepareLargeReportRequest();
        set_time_limit(600);
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
            . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($filters) {
            $file = fopen('php://output', 'w');

            // Add BOM for proper UTF-8 Excel support
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write CSV headers
            fputcsv($file, [
                'Sr. No.',
                'Application No.',
                'Owner ID',
                'Applicant Name',
                'Father/Husband Name',
                'Mobile No.',
                'PPP ID',
                'Member ID',
                'Gender',
                'Caste',
                'District',
                'Block',
                'Village',
                'Phase',
                'Plot No.',
                'Allotment Status',
            ]);

            $query = DB::table('OwnerMaster as o')
                ->join('VillageMaster as v', 'v.VillageId', '=', 'o.VillageId')
                ->leftJoin('DistrictMaster as d', 'd.DistrictId', '=', 'o.DistrictId')
                ->leftJoin('BlockMaster as b', 'b.BlockId', '=', 'o.BlockId')
                ->leftJoin('FlatMaster as f', 'f.FlatId', '=', 'o.FlatId')
                ->where('v.plots', '>', 0)
                ->whereNotNull('o.FlatId')
                ->where('o.FlatId', '>', 0);

            if (!empty($filters['phase'])) {
                $query->where('o.Phase', $filters['phase']);
            }
            if (!empty($filters['district_id'])) {
                $query->where('o.DistrictId', $filters['district_id']);
            }
            if (!empty($filters['block_id'])) {
                $query->where('o.BlockId', $filters['block_id']);
            }
            if (!empty($filters['village_id'])) {
                $query->where('o.VillageId', $filters['village_id']);
            }
            if (!empty($filters['search'])) {
                $search = trim($filters['search']);
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('o.RegistrationNo', $search)
                        ->orWhere('o.MobileNo', $search)
                        ->orWhere('o.PPPId', $search)
                        ->orWhere('f.FlatNo', $search)
                        ->orWhere('o.OwnerName', 'like', '%' . $search . '%')
                        ->orWhere('o.FatherHusbandName', 'like', '%' . $search . '%');
                });
            }

            switch ($filters['status'] ?? '') {
                case 'approved_paid':
                    $query->whereRaw('IFNULL(o.IsAllotmentCancelled, 0) = 0')
                        ->whereRaw('IFNULL(o.IsRejected, 0) = 0')
                        ->whereRaw('IFNULL(o.IsApproved, 0) = 1')
                        ->whereRaw('IFNULL(o.IsPaid, 0) = 1');
                    break;
                case 'approved_unpaid':
                    $query->whereRaw('IFNULL(o.IsAllotmentCancelled, 0) = 0')
                        ->whereRaw('IFNULL(o.IsRejected, 0) = 0')
                        ->whereRaw('IFNULL(o.IsApproved, 0) = 1')
                        ->whereRaw('IFNULL(o.IsPaid, 0) = 0');
                    break;
                case 'pending':
                    $query->whereRaw('IFNULL(o.IsAllotmentCancelled, 0) = 0')
                        ->whereRaw('IFNULL(o.IsRejected, 0) = 0')
                        ->whereRaw('IFNULL(o.IsApproved, 0) = 0');
                    break;
                case 'rejected':
                    $query->whereRaw('IFNULL(o.IsAllotmentCancelled, 0) = 0')
                        ->whereRaw('IFNULL(o.IsRejected, 0) = 1');
                    break;
                case 'cancelled':
                    $query->whereRaw('IFNULL(o.IsAllotmentCancelled, 0) = 1');
                    break;
            }

            $query->select([
                'o.OwnerId',
                'o.RegistrationNo',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.PPPId',
                'o.MemberId',
                'o.Gender',
                'o.Caste',
                'o.Phase',
                'd.DistrictName',
                'b.BlockName',
                'v.VillageName',
                'f.FlatNo',
                'o.IsAllotmentCancelled',
                'o.IsRejected',
                'o.IsApproved',
                'o.IsPaid'
            ])->orderBy('o.OwnerId');

            $serial = 0;
            $query->chunk(1000, function ($records) use ($file, &$serial) {
                foreach ($records as $record) {
                    $serial++;

                    if ((int) ($record->IsAllotmentCancelled ?? 0) === 1) {
                        $status = 'Cancelled';
                    } elseif ((int) ($record->IsRejected ?? 0) === 1) {
                        $status = 'Rejected';
                    } elseif (
                        (int) ($record->IsApproved ?? 0) === 1 &&
                        (int) ($record->IsPaid ?? 0) === 1
                    ) {
                        $status = 'Approved & Paid';
                    } elseif ((int) ($record->IsApproved ?? 0) === 1) {
                        $status = 'Approved & Unpaid';
                    } else {
                        $status = 'Yet to be Approved';
                    }

                    fputcsv($file, [
                        $serial,
                        $record->RegistrationNo ?? '-',
                        $record->OwnerId ?? '-',
                        $record->OwnerName ?? '-',
                        $record->FatherHusbandName ?? '-',
                        $record->MobileNo ?? '-',
                        $record->PPPId ?? '-',
                        $record->MemberId ?? '-',
                        $record->Gender ?? '-',
                        $record->Caste ?? '-',
                        $record->DistrictName ?? '-',
                        $record->BlockName ?? '-',
                        $record->VillageName ?? '-',
                        $record->Phase ?? '-',
                        $record->FlatNo ?? '-',
                        $status
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAllotmentPdf(Request $request)
    {
        $this->prepareLargeReportRequest();
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $query = $this->getFilteredAllotmentQuery($request);

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
            ->get();

        $filters = [
            'phase' => $request->phase,
            'district_id' => $request->district_id,
            'block_id' => $request->block_id,
            'village_id' => $request->village_id,
            'search' => $request->search,
            'status' => $request->status,
        ];

        return view(
            'mmgay.super-admin.allotment.print',
            compact('allotments', 'filters')
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
        $this->prepareLargeReportRequest();
        DB::disableQueryLog();

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

        $globalRegistrationStats = Cache::remember(
            'super_admin_registration_global_stats',
            now()->addSeconds($this->reportCacheSeconds()),
            function () {
                $basic = DB::table('registary as r')
                    ->selectRaw("
                        COUNT(*) AS total_registrations,
                        SUM(CASE
                            WHEN r.RegistaryNumber IS NULL
                              OR TRIM(r.RegistaryNumber) = ''
                            THEN 1 ELSE 0
                        END) AS blank_registry_numbers,
                        COUNT(DISTINCT NULLIF(TRIM(r.RegistaryNumber), ''))
                            AS unique_registrations
                    ")
                    ->first();

                $matched = DB::table('registary as r')
                    ->whereNotNull('r.SecondPartyMobile')
                    ->where('r.SecondPartyMobile', '<>', '')
                    ->whereExists(function ($query) {
                        $query
                            ->selectRaw('1')
                            ->from('OwnerMaster as o')
                            ->whereColumn('o.MobileNo', 'r.SecondPartyMobile');
                    })
                    ->selectRaw("
                        COUNT(*) AS matched_registrations,
                        COUNT(DISTINCT r.SecondPartyMobile)
                            AS unique_matched_mobiles,
                        COUNT(DISTINCT NULLIF(TRIM(r.RegistaryNumber), ''))
                            AS unique_matched_registrations
                    ")
                    ->first();

                return (object) [
                    'totalRegistrations' => (int) ($basic->total_registrations ?? 0),
                    'blankRegistryNumbers' => (int) ($basic->blank_registry_numbers ?? 0),
                    'uniqueRegistrations' => (int) ($basic->unique_registrations ?? 0),
                    'matchedRegistrations' => (int) ($matched->matched_registrations ?? 0),
                    'uniqueMatchedMobiles' => (int) ($matched->unique_matched_mobiles ?? 0),
                    'uniqueMatchedRegistrations' => (int) ($matched->unique_matched_registrations ?? 0),
                ];
            }
        );

        $totalRegistrations = $globalRegistrationStats->totalRegistrations;
        $blankRegistryNumbers = $globalRegistrationStats->blankRegistryNumbers;
        $uniqueRegistrations = $globalRegistrationStats->uniqueRegistrations;
        $duplicateRegistrations = max(
            0,
            $totalRegistrations - $uniqueRegistrations - $blankRegistryNumbers
        );
        $matchedRegistrations = $globalRegistrationStats->matchedRegistrations;
        $unmatchedRegistrations = max(0, $totalRegistrations - $matchedRegistrations);
        $uniqueMatchedMobiles = $globalRegistrationStats->uniqueMatchedMobiles;
        $repeatedMatchedMobileRows = max(0, $matchedRegistrations - $uniqueMatchedMobiles);
        $uniqueMatchedRegistrations = $globalRegistrationStats->uniqueMatchedRegistrations;

        /*
        |--------------------------------------------------------------------------
        | Registry ranked subquery
        |--------------------------------------------------------------------------
        | MySQL 8+ required because ROW_NUMBER() and COUNT() OVER() are used.
        */

        $registryRankedSubQuery = DB::table('registary as rs')
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

        $filteredCountKey = $this->reportCacheKey(
            'super_admin_registration_filtered_total',
            $request,
            ['phase', 'district_id', 'block_id', 'village_id', 'search', 'type']
        );

        $filteredRegistrations = (int) Cache::remember(
            $filteredCountKey,
            now()->addSeconds($this->reportCacheSeconds()),
            fn() => (clone $registrationsQuery)->count()
        );

        /*
        |--------------------------------------------------------------------------
        | Paginated records
        |--------------------------------------------------------------------------
        */

        $currentPage = max(1, (int) $request->query('page', 1));
        $perPage = 25;
        $lastPage = max(1, (int) ceil($filteredRegistrations / $perPage));
        $currentPage = min($currentPage, $lastPage);

        $registrationRows = $registrationsQuery
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
            ->forPage($currentPage, $perPage)
            ->get();

        $registrations = new LengthAwarePaginator(
            $registrationRows,
            $filteredRegistrations,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
                'query' => $request->except('page'),
            ]
        );

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
        $this->prepareLargeReportRequest();
        set_time_limit(600);
        ini_set('memory_limit', '512M');

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

        $fileName = 'registry-' .
            $type . '-' .
            now()->format('Y-m-d-H-i-s') .
            '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($request) {
            $file = fopen('php://output', 'w');

            // Add BOM for proper UTF-8 Excel support
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Get columns of the registry table dynamically
            $dbName = config('database.connections.mysql.database', 'dddnew1');
            $columns = DB::select("SHOW COLUMNS FROM `{$dbName}`.`registary`");
            $registryColumns = array_map(static fn($column) => $column->Field, $columns);

            // Write CSV headers
            $headers = array_merge(
                ['Sr. No.'],
                $registryColumns,
                [
                    'Registry Row Count',
                    'Mobile Row Count',
                    'Match Status',
                    'Matched Application No.',
                    'Matched Owner ID',
                    'Matched Owner Name',
                    'Matched Father / Husband Name',
                    'Matched Owner Mobile',
                    'Matched PPP ID',
                    'Matched Member ID',
                    'Matched Caste',
                    'Matched Phase',
                ]
            );
            fputcsv($file, $headers);

            $query = $this->getRegistrationExportQuery($request);

            $registrySelectColumns = array_map(static fn($column) => 'r.' . $column, $registryColumns);

            $query->select($registrySelectColumns)
                ->addSelect([
                    'r.registry_group_count',
                    'r.mobile_group_count',
                    'o.OwnerId as matched_owner_id',
                    'o.RegistrationNo as matched_registration_no',
                    'o.OwnerName as matched_owner_name',
                    'o.FatherHusbandName as matched_father_husband_name',
                    'o.MobileNo as matched_owner_mobile',
                    'o.PPPId as matched_ppp_id',
                    'o.MemberId as matched_member_id',
                    'o.Caste as matched_caste',
                    'o.Phase as matched_phase',
                ])
                ->orderByDesc('r.RegistaryDate')
                ->orderByDesc('r.Token')
                ->orderBy('r.RegistaryNumber')
                ->orderBy('r.SecondPartyMobile')
                ->orderBy('r.SecondParty')
                ->orderBy('r.FirstParty')
                ->orderBy('r.District')
                ->orderBy('r.TehsilName')
                ->orderBy('r.Village');

            $serial = 0;
            foreach ($query->cursor() as $row) {
                $serial++;
                $registryValues = [];
                foreach ($registryColumns as $column) {
                    $value = $row->{$column} ?? '';
                    if ($value !== '' && $value !== null && stripos($column, 'date') !== false) {
                        $timestamp = strtotime((string) $value);
                        if ($timestamp !== false) {
                            $value = date('d-m-Y', $timestamp);
                        }
                    }
                    $registryValues[] = $value ?? '';
                }

                fputcsv($file, array_merge(
                    [$serial],
                    $registryValues,
                    [
                        $row->registry_group_count ?? 0,
                        $row->mobile_group_count ?? 0,
                        !empty($row->matched_owner_id) ? 'Matched' : 'Unmatched',
                        $row->matched_registration_no ?? '',
                        $row->matched_owner_id ?? '',
                        $row->matched_owner_name ?? '',
                        $row->matched_father_husband_name ?? '',
                        $row->matched_owner_mobile ?? '',
                        $row->matched_ppp_id ?? '',
                        $row->matched_member_id ?? '',
                        $row->matched_caste ?? '',
                        $row->matched_phase ?? '',
                    ]
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportRegistrationPdf(Request $request)
    {
        $this->prepareLargeReportRequest();
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

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

        return view(
            'mmgay.super-admin.exports.registration-pdf',
            compact('registrations', 'reportTitle')
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

        $registryRankedSubQuery = DB::table('registary as rs')
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