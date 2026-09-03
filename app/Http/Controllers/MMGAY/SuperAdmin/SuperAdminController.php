<?php

namespace App\Http\Controllers\MMGAY\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DashboardExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;



class SuperAdminController extends Controller
{
    private function prepareLargeReportRequest(): void
    {
        DB::disableQueryLog();

        @ini_set('memory_limit', '-1');
        @set_time_limit(0);
    }

    private function reportCacheKey(
        string $prefix,
        Request $request,
        array $keys = []
    ): string {

        $filters = [];

        foreach ($keys as $key) {
            $filters[$key] = $request->input($key);
        }

        return $prefix . '_' . md5(json_encode($filters));
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
        $districts = DB::table('districtmaster as d')
            ->whereExists(function ($query) use ($phase) {
                $query
                    ->selectRaw('1')
                    ->from('villagemaster as v')
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
            ? DB::table('blockmaster as b')
                ->where('b.DistrictId', $districtId)
                ->whereExists(function ($query) use ($phase) {
                    $query
                        ->selectRaw('1')
                        ->from('villagemaster as v')
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
            ? DB::table('villagemaster as v')
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
        $villageStatsQuery = DB::table('villagemaster as v')
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
        $registeredQuery = DB::table('ownermaster as o')
            ->whereExists(function ($query) {
                $query
                    ->selectRaw('1')
                    ->from('villagemaster as v')
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
        $allotmentQuery = DB::table('ownermaster as o')
            ->whereExists(function ($query) {
                $query
                    ->selectRaw('1')
                    ->from('flatmaster as f')
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
        | Registration Statistics + Physical Possession Base
        |--------------------------------------------------------------------------
        |
        | FINAL BUSINESS RULE
        |
        | Approved & Paid beneficiaries are the registration base.
        |
        | OLD registry records:
        |   registary.flatid IS NULL
        |   -> SecondPartyMobile = OwnerMaster.MobileNo
        |
        | NEW registry records:
        |   registary.flatid available
        |   -> registary.flatid = OwnerMaster.FlatId
        |
        | UNION removes duplicate OwnerIds automatically. Therefore one
        | beneficiary matching both old and new rules is counted only once.
        |--------------------------------------------------------------------------
        */

        /*
         * Old registry records -> Mobile match
         */
        $oldRegistryOwnerIds = DB::table('ownermaster as o')
            ->join('registary as r', function ($join) {
                $join->on(
                    'r.SecondPartyMobile',
                    '=',
                    'o.MobileNo'
                );
            })
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereRaw(
                'COALESCE(o.IsAllotmentCancelled, 0) = 0'
            )
            ->whereNull('r.flatid')
            ->whereNotNull('o.MobileNo')
            ->where('o.MobileNo', '<>', '');

        $oldRegistryOwnerIds =
            $this->applyOwnerDashboardFilters(
                $oldRegistryOwnerIds,
                $phase,
                $districtId,
                $blockId,
                $villageId
            );

        $oldRegistryOwnerIds = $oldRegistryOwnerIds
            ->select('o.OwnerId')
            ->distinct();


        /*
         * New registry records -> FlatId match
         */
        $newRegistryOwnerIds = DB::table('ownermaster as o')
            ->join('registary as r', function ($join) {
                $join->on(
                    'r.flatid',
                    '=',
                    'o.FlatId'
                );
            })
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereRaw(
                'COALESCE(o.IsAllotmentCancelled, 0) = 0'
            )
            ->whereNotNull('r.flatid')
            ->where('r.flatid', '>', 0);

        $newRegistryOwnerIds =
            $this->applyOwnerDashboardFilters(
                $newRegistryOwnerIds,
                $phase,
                $districtId,
                $blockId,
                $villageId
            );

        $newRegistryOwnerIds = $newRegistryOwnerIds
            ->select('o.OwnerId')
            ->distinct();


        /*
         * Combined unique Registry Done OwnerIds.
         *
         * UNION (not UNION ALL) is intentional.
         */
        $registryDoneOwnerIds = $oldRegistryOwnerIds
            ->union($newRegistryOwnerIds);


        /*
         * Approved & Paid = total registration eligible.
         * Use the already-calculated dashboard allotment statistic so another
         * large OwnerMaster COUNT query is not required.
         */
        $totalRegistrationEligible =
            (int) ($stats->ApprovedPaid ?? 0);


        /*
         * Unique Registration Done
         */
        $registrationDone = (int) DB::query()
            ->fromSub(
                clone $registryDoneOwnerIds,
                'registry_done'
            )
            ->count();


        /*
         * Registration Pending
         */
        $registrationPending = max(
            0,
            $totalRegistrationEligible - $registrationDone
        );


        /*
        |--------------------------------------------------------------------------
        | Registration Object
        |--------------------------------------------------------------------------
        |
        | Existing Blade variable names are preserved to avoid breaking other
        | code:
        |
        | TotalRegistration = Approved & Paid
        | Matched           = Registration Done
        | UnMatched         = Registration Pending
        |--------------------------------------------------------------------------
        */
        $registration = (object) [
            'TotalRegistration' =>
                $totalRegistrationEligible,

            'Matched' =>
                $registrationDone,

            'UnMatched' =>
                $registrationPending,
        ];


        /*
        |--------------------------------------------------------------------------
        | Physical Possession
        |--------------------------------------------------------------------------
        |
        | ONLY Registry Done beneficiaries can enter Physical Possession.
        | The old physical-possession workflow remains unchanged.
        |--------------------------------------------------------------------------
        */

        $possessionEligible = $registrationDone;


        /*
         * Possession Given:
         * registry-done unique OwnerId + final verified possession status.
         */
        $possessionGiven = (int) DB::query()
            ->fromSub(
                clone $registryDoneOwnerIds,
                'registry_done'
            )
            ->join(
                'mmgay_possession_applications as pa',
                'pa.owner_id',
                '=',
                'registry_done.OwnerId'
            )
            ->whereRaw("
                LOWER(
                    TRIM(
                        COALESCE(
                            pa.physical_possession_status,
                            ''
                        )
                    )
                ) = ?
            ", ['verified'])
            ->distinct()
            ->count('registry_done.OwnerId');


        $possession = (object) [
            'TotalEligible' =>
                (int) $possessionEligible,

            'Given' =>
                (int) $possessionGiven,

            'Pending' =>
                max(
                    0,
                    (int) $possessionEligible
                    - (int) $possessionGiven
                ),
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
                'possession',
                'districts',
                'blocks',
                'villages'
            )
        );
    }

    private function possessionBaseQuery(Request $request)
    {
        $phase = $request->filled('phase')
            ? (int) $request->input('phase')
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->input('district_id')
            : null;

        $blockId = $request->filled('block_id')
            ? (int) $request->input('block_id')
            : null;

        $villageId = $request->filled('village_id')
            ? (int) $request->input('village_id')
            : null;

        /*
        |--------------------------------------------------------------------------
        | Registry verified unique Owner IDs
        |--------------------------------------------------------------------------
        |
        | OLD registry records:
        |   registary.flatid IS NULL
        |   -> SecondPartyMobile = OwnerMaster.MobileNo
        |
        | NEW registry records:
        |   registary.flatid IS NOT NULL
        |   -> registary.flatid = OwnerMaster.FlatId
        |
        | UNION is intentional (not UNION ALL), so an OwnerId matching both
        | rules is returned only once. With the verified data this gives the
        | unique possession eligible base (1924 before possession status split).
        |--------------------------------------------------------------------------
        */

        $oldRegistryOwners = DB::table('registary as r_old')
            ->join(
                'ownermaster as o_old',
                'o_old.MobileNo',
                '=',
                'r_old.SecondPartyMobile'
            )
            ->whereNull('r_old.flatid')
            ->whereNotNull('r_old.SecondPartyMobile')
            ->where('r_old.SecondPartyMobile', '<>', '')
            ->select('o_old.OwnerId')
            ->distinct();

        $newRegistryOwners = DB::table('registary as r_new')
            ->join(
                'ownermaster as o_new',
                'o_new.FlatId',
                '=',
                'r_new.flatid'
            )
            ->whereNotNull('r_new.flatid')
            ->where('r_new.flatid', '>', 0)
            ->select('o_new.OwnerId')
            ->distinct();

        $registryVerifiedOwners = $oldRegistryOwners
            ->union($newRegistryOwners);

        /*
        |--------------------------------------------------------------------------
        | Latest possession application per owner
        |--------------------------------------------------------------------------
        | This prevents duplicate OwnerMaster rows when an owner has more than
        | one possession application/history row.
        |--------------------------------------------------------------------------
        */
        $latestPossessionApplication = DB::table(
            'mmgay_possession_applications as pa_latest'
        )
            ->selectRaw(
                'pa_latest.owner_id, MAX(pa_latest.id) AS latest_id'
            )
            ->groupBy('pa_latest.owner_id');

        return DB::table('ownermaster as o')

            ->leftJoinSub(
                $latestPossessionApplication,
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

            /*
            |--------------------------------------------------------------------------
            | Approved & Paid base
            |--------------------------------------------------------------------------
            */
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereRaw(
                'COALESCE(o.IsRejected, 0) = 0'
            )
            ->whereRaw(
                'COALESCE(o.IsAllotmentCancelled, 0) = 0'
            )

            /*
            |--------------------------------------------------------------------------
            | Only unique Registry Done beneficiaries can enter Possession
            |--------------------------------------------------------------------------
            */
            ->whereIn(
                'o.OwnerId',
                $registryVerifiedOwners
            )

            ->when(
                $phase !== null,
                fn($query) => $query->where(
                    'o.Phase',
                    $phase
                )
            )

            ->when(
                $districtId !== null,
                fn($query) => $query->where(
                    'o.DistrictId',
                    $districtId
                )
            )

            ->when(
                $blockId !== null,
                fn($query) => $query->where(
                    'o.BlockId',
                    $blockId
                )
            )

            ->when(
                $villageId !== null,
                fn($query) => $query->where(
                    'o.VillageId',
                    $villageId
                )
            );
    }

    public function possessionStats(Request $request)
    {
        DB::disableQueryLog();

        $counts = $this->possessionCounts($request);

        $eligible = (int) ($counts['all'] ?? 0);
        $given = (int) ($counts['verified'] ?? 0);

        return response()->json([
            'success' => true,

            'totals' => [
                'eligible' => $eligible,
                'given' => $given,
                'pending' => max(
                    0,
                    $eligible - $given
                ),
            ],
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
            ? (int) $request->input('phase')
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->input('district_id')
            : null;

        $blockId = $request->filled('block_id')
            ? (int) $request->input('block_id')
            : null;

        $villageId = $request->filled('village_id')
            ? (int) $request->input('village_id')
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
        | Dependent dropdowns
        |--------------------------------------------------------------------------
        */
        $districts = Cache::remember(
            'possession_districts_v3_'
            . ($phase ?? 'all'),
            now()->addMinutes(30),
            function () use ($phase) {
                return DB::table('districtmaster as d')
                    ->whereExists(function ($query) use ($phase) {
                        $query
                            ->selectRaw('1')
                            ->from('villagemaster as v')
                            ->whereColumn(
                                'v.DistrictId',
                                'd.DistrictId'
                            )
                            ->where('v.plots', '>', 0)

                            ->when(
                                $phase !== null,
                                fn($subQuery) =>
                                $subQuery->where(
                                    'v.Phase',
                                    $phase
                                )
                            );
                    })
                    ->select([
                        'd.DistrictId',
                        'd.DistrictName',
                    ])
                    ->orderBy('d.DistrictName')
                    ->get();
            }
        );

        $blocks = $districtId !== null
            ? Cache::remember(
                'possession_blocks_v3_'
                . $districtId
                . '_'
                . ($phase ?? 'all'),
                now()->addMinutes(30),
                function () use ($districtId, $phase) {
                    return DB::table('blockmaster as b')
                        ->where(
                            'b.DistrictId',
                            $districtId
                        )
                        ->whereExists(
                            function ($query) use ($phase) {
                                $query
                                    ->selectRaw('1')
                                    ->from('villagemaster as v')
                                    ->whereColumn(
                                        'v.BlockId',
                                        'b.BlockId'
                                    )
                                    ->where('v.plots', '>', 0)

                                    ->when(
                                        $phase !== null,
                                        fn($subQuery) =>
                                        $subQuery->where(
                                            'v.Phase',
                                            $phase
                                        )
                                    );
                            }
                        )
                        ->select([
                            'b.BlockId',
                            'b.BlockName',
                        ])
                        ->orderBy('b.BlockName')
                        ->get();
                }
            )
            : collect();

        $villages = $blockId !== null
            ? Cache::remember(
                'possession_villages_v3_'
                . $blockId
                . '_'
                . ($phase ?? 'all'),
                now()->addMinutes(30),
                function () use ($blockId, $phase) {
                    return DB::table('villagemaster as v')
                        ->where('v.BlockId', $blockId)
                        ->where('v.plots', '>', 0)

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
                            'v.Phase',
                        ])
                        ->orderBy('v.VillageName')
                        ->get();
                }
            )
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Card counts
        |--------------------------------------------------------------------------
        */
        $counts = $this->possessionCounts($request);

        /*
        |--------------------------------------------------------------------------
        | Filtered query
        |--------------------------------------------------------------------------
        */
        $filteredQuery = $this
            ->applyPossessionStatusFilter(
                $this->possessionBaseQuery($request),
                $filter
            );

        /*
        |--------------------------------------------------------------------------
        | Manual pagination
        |--------------------------------------------------------------------------
        | paginate() की additional COUNT query नहीं चलेगी।
        |--------------------------------------------------------------------------
        */
        $currentPage = max(
            1,
            (int) $request->query('page', 1)
        );

        $totalApplications = (int) (
            $counts[$filter] ?? $counts['all']
        );

        $lastPage = max(
            1,
            (int) ceil(
                $totalApplications / $perPage
            )
        );

        $currentPage = min(
            $currentPage,
            $lastPage
        );

        /*
        |--------------------------------------------------------------------------
        | Current page owner IDs
        |--------------------------------------------------------------------------
        */
        $pageOwnerIds = (clone $filteredQuery)
            ->select('o.OwnerId')
            ->orderByRaw("
            CASE
                WHEN pa.updated_at IS NULL
                THEN 1
                ELSE 0
            END
        ")
            ->orderByDesc('pa.updated_at')
            ->orderBy('o.OwnerName')
            ->forPage(
                $currentPage,
                $perPage
            )
            ->pluck('o.OwnerId')
            ->map(
                fn($id) => (int) $id
            )
            ->all();

        $applicationRows = collect();

        /*
        |--------------------------------------------------------------------------
        | Fetch only current page details
        |--------------------------------------------------------------------------
        */
        if (!empty($pageOwnerIds)) {
            $applicationRows = DB::table('ownermaster as o')
                ->leftJoin(
                    'villagemaster as v',
                    'v.VillageId',
                    '=',
                    'o.VillageId'
                )
                ->leftJoin(
                    'flatmaster as f',
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
                ->whereIn(
                    'o.OwnerId',
                    $pageOwnerIds
                )
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
                ])
                ->orderByRaw("
                CASE
                    WHEN pa.updated_at IS NULL
                    THEN 1
                    ELSE 0
                END
            ")
                ->orderByDesc('pa.updated_at')
                ->orderBy('o.OwnerName')
                ->get();
        }

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
            ->leftJoin('ownermaster as o', 'o.OwnerId', '=', 'p.owner_id')
            ->leftJoin('districtmaster as d', 'd.DistrictId', '=', 'o.DistrictId')
            ->leftJoin('blockmaster as b', 'b.BlockId', '=', 'o.BlockId')
            ->leftJoin('villagemaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->leftJoin('flatmaster as f', 'f.FlatId', '=', 'o.FlatId')
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
        switch ($filter) {
            case 'schedule_pending':
                $query->where(function ($subQuery) {
                    $subQuery
                        ->whereNull('pa.id')
                        ->orWhereRaw(
                            "LOWER(TRIM(COALESCE(
                            pa.physical_possession_status,
                            ''
                        ))) = ?",
                            ['eligible for physical possession']
                        );
                });
                break;

            case 'awaiting_citizen':
                $query
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        "LOWER(TRIM(
                        pa.physical_possession_status
                    )) = ?",
                        ['visit scheduled']
                    );
                break;

            case 'field_visit_pending':
                $query
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        "LOWER(TRIM(
                        pa.physical_possession_status
                    )) = ?",
                        ['slot selected']
                    );
                break;

            case 'document_verification':
                $query
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        "LOWER(TRIM(
                        pa.physical_possession_status
                    )) = ?",
                        ['site verified']
                    );
                break;

            case 'verified':
                $query
                    ->whereNotNull('pa.id')
                    ->whereRaw(
                        "LOWER(TRIM(
                        pa.physical_possession_status
                    )) = ?",
                        ['verified']
                    );
                break;
        }

        return $query;
    }

    private function possessionCacheKey(
        string $prefix,
        Request $request
    ): string {
        return $prefix . '_' . md5(
            json_encode([
                'phase' =>
                    $request->query('phase'),

                'district_id' =>
                    $request->query('district_id'),

                'block_id' =>
                    $request->query('block_id'),

                'village_id' =>
                    $request->query('village_id'),
            ])
        );
    }

    private function possessionCounts(
        Request $request
    ): array {
        $cacheKey = $this->possessionCacheKey(
            'super_admin_possession_counts_v5',
            $request
        );

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(3),
            function () use ($request) {
                $row = $this
                    ->possessionBaseQuery($request)
                    ->selectRaw("
                    COUNT(DISTINCT o.OwnerId)
                        AS all_count,

                    COUNT(DISTINCT CASE
                        WHEN pa.id IS NULL
                          OR LOWER(TRIM(COALESCE(
                                pa.physical_possession_status,
                                ''
                             ))) =
                             'eligible for physical possession'
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

                return [
                    'all' =>
                        (int) ($row->all_count ?? 0),

                    'schedule_pending' =>
                        (int) (
                            $row->schedule_pending_count ?? 0
                        ),

                    'awaiting_citizen' =>
                        (int) (
                            $row->awaiting_citizen_count ?? 0
                        ),

                    'field_visit_pending' =>
                        (int) (
                            $row->field_visit_pending_count ?? 0
                        ),

                    'document_verification' =>
                        (int) (
                            $row->document_verification_count ?? 0
                        ),

                    'verified' =>
                        (int) ($row->verified_count ?? 0),
                ];
            }
        );
    }

    public function possessionExportCsv(Request $request)
    {
        DB::disableQueryLog();

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

        /*
        |--------------------------------------------------------------------------
        | Same filters and status logic
        |--------------------------------------------------------------------------
        */
        $recordsQuery = $this
            ->applyPossessionStatusFilter(
                $this->possessionBaseQuery($request),
                $filter
            )

            ->leftJoin(
                'villagemaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )

            ->leftJoin(
                'flatmaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )

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
            ]);

        /*
        |--------------------------------------------------------------------------
        | Same stable ordering as Print
        |--------------------------------------------------------------------------
        */
        $recordsQuery = $this->applyPossessionExportOrder(
            $recordsQuery
        );

        $fileName = 'Possession_Report_'
            . now()->format('d-m-Y_H-i-s')
            . '.csv';

        return response()->streamDownload(
            function () use ($recordsQuery) {
                $handle = fopen(
                    'php://output',
                    'w'
                );

                if ($handle === false) {
                    throw new \RuntimeException(
                        'CSV stream could not be opened.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Excel-compatible UTF-8
                |--------------------------------------------------------------------------
                */
                fwrite(
                    $handle,
                    "\xEF\xBB\xBF"
                );

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

                $serialNumber = 1;

                foreach ($recordsQuery->cursor() as $record) {
                    fputcsv($handle, [
                        $serialNumber++,
                        $record->OwnerId ?? '',
                        $record->OwnerName ?? '',
                        $record->FatherHusbandName ?? '',
                        $record->MobileNo ?? '',
                        $record->RegistrationNo ?? '',
                        $record->PPPId ?? '',
                        $record->MemberId ?? '',
                        $record->FlatNo ?? '',
                        $record->VillageName ?? '',
                        $record->Phase ?? '',
                        $record->application_number ?? '',
                        $record->physical_possession_status
                        ?: 'Schedule Pending',
                        $record->meeting_slot ?? '',
                        $record->citizen_visit_date ?? '',
                        $record->possession_date ?? '',
                        $record->verified_at ?? '',
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Periodically flush output
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
    }

    private function applyPossessionExportOrder($query)
    {
        return $query

            // Meeting slot wale sabse upar
            ->orderByRaw("
            CASE
                WHEN pa.meeting_slot IS NULL
                AND pa.visit_slot_1 IS NULL
                AND pa.visit_slot_2 IS NULL
                AND pa.visit_slot_3 IS NULL
                THEN 1
                ELSE 0
            END ASC
        ")

            // Latest updated first
            ->orderByDesc('pa.updated_at')

            // Latest meeting first
            ->orderByDesc(DB::raw("
            COALESCE(
                pa.meeting_slot,
                pa.visit_slot_1,
                pa.visit_slot_2,
                pa.visit_slot_3
            )
        "))

            ->orderBy('o.OwnerName')
            ->orderBy('o.OwnerId');
    }

    public function possessionPrint(
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

        /*
        |--------------------------------------------------------------------------
        | Same filters and status logic as CSV
        |--------------------------------------------------------------------------
        */
        $applicationsQuery = $this
            ->applyPossessionStatusFilter(
                $this->possessionBaseQuery($request),
                $filter
            )

            ->leftJoin(
                'villagemaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )

            ->leftJoin(
                'flatmaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )

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
            ]);

        /*
        |--------------------------------------------------------------------------
        | Same stable ordering as CSV
        |--------------------------------------------------------------------------
        */
        $applications = $this
            ->applyPossessionExportOrder(
                $applicationsQuery
            )
            ->get();

        $filterLabels = [
            'all' =>
                'Total Eligible',

            'schedule_pending' =>
                'Schedule Pending',

            'awaiting_citizen' =>
                'Confirmation Pending From Citizen',

            'field_visit_pending' =>
                'Physical/Site Visit Pending',

            'document_verification' =>
                'Document Verification',

            'verified' =>
                'Possession Given',
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

    public function possessionFilterDistricts(
        Request $request
    ) {
        $phase = $request->filled('phase')
            ? (int) $request->input('phase')
            : null;

        $districts = Cache::remember(
            'possession_ajax_districts_v2_'
            . ($phase ?? 'all'),
            now()->addMinutes(30),
            function () use ($phase) {
                return DB::table('districtmaster as d')
                    ->whereExists(function ($query) use ($phase) {
                        $query
                            ->selectRaw('1')
                            ->from('villagemaster as v')
                            ->whereColumn(
                                'v.DistrictId',
                                'd.DistrictId'
                            )
                            ->where('v.plots', '>', 0)

                            ->when(
                                $phase !== null,
                                fn($subQuery) =>
                                $subQuery->where(
                                    'v.Phase',
                                    $phase
                                )
                            );
                    })
                    ->select([
                        'd.DistrictId',
                        'd.DistrictName',
                    ])
                    ->orderBy('d.DistrictName')
                    ->get();
            }
        );

        return response()->json([
            'success' => true,
            'districts' => $districts,
        ]);
    }

    public function possessionFilterBlocks(
        Request $request
    ) {
        $phase = $request->filled('phase')
            ? (int) $request->input('phase')
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->input('district_id')
            : null;

        if ($districtId === null) {
            return response()->json([
                'success' => true,
                'blocks' => [],
            ]);
        }

        $blocks = Cache::remember(
            'possession_ajax_blocks_v2_'
            . $districtId
            . '_'
            . ($phase ?? 'all'),
            now()->addMinutes(30),
            function () use ($districtId, $phase) {
                return DB::table('blockmaster as b')
                    ->where('b.DistrictId', $districtId)

                    ->whereExists(function ($query) use ($phase) {
                        $query
                            ->selectRaw('1')
                            ->from('villagemaster as v')
                            ->whereColumn(
                                'v.BlockId',
                                'b.BlockId'
                            )
                            ->where('v.plots', '>', 0)

                            ->when(
                                $phase !== null,
                                fn($subQuery) =>
                                $subQuery->where(
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
                    ->get();
            }
        );

        return response()->json([
            'success' => true,
            'blocks' => $blocks,
        ]);
    }

    public function possessionFilterVillages(
        Request $request
    ) {
        $phase = $request->filled('phase')
            ? (int) $request->input('phase')
            : null;

        $blockId = $request->filled('block_id')
            ? (int) $request->input('block_id')
            : null;

        if ($blockId === null) {
            return response()->json([
                'success' => true,
                'villages' => [],
            ]);
        }

        $villages = Cache::remember(
            'possession_ajax_villages_v2_'
            . $blockId
            . '_'
            . ($phase ?? 'all'),
            now()->addMinutes(30),
            function () use ($blockId, $phase) {
                return DB::table('villagemaster as v')
                    ->where('v.BlockId', $blockId)
                    ->where('v.plots', '>', 0)

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
                    ])
                    ->orderBy('v.VillageName')
                    ->get();
            }
        );

        return response()->json([
            'success' => true,
            'villages' => $villages,
        ]);
    }

    public function getDistricts($phase = null)
    {
        $villageQuery = DB::table('villagemaster')
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

        $districts = DB::table('districtmaster')
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
        $village = DB::table('villagemaster')
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

        $blocks = DB::table('blockmaster')
            ->whereIn('BlockId', $village)
            ->orderBy('BlockName')
            ->get(['BlockId', 'BlockName']);

        return response()->json($blocks);
    }

    public function getVillages($blockId, $phase = null)
    {
        $villages = DB::table('villagemaster')
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

        $villageQuery = DB::table('villagemaster')
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

        $registered = DB::table('ownermaster as o')
            ->join('villagemaster as v', 'v.VillageId', '=', 'o.VillageId')
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

        $allotment = DB::table('ownermaster as o')
            ->join('villagemaster as v', 'v.VillageId', '=', 'o.VillageId')
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
        DB::disableQueryLog();
        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */
        $phase = $request->filled('phase')
            ? (int) $request->input('phase')
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->input('district_id')
            : null;

        /*
        |--------------------------------------------------------------------------
        | Cache key
        |--------------------------------------------------------------------------
        */
        $cacheKey = 'district_report_v5_' . md5(
            json_encode([
                'phase' => $phase,
                'district_id' => $districtId,
            ])
        );

        $reportData = Cache::remember(
            $cacheKey,
            now()->addMinutes(5),
            function () use ($phase, $districtId) {

                /*
                |--------------------------------------------------------------------------
                | Relevant districts and village count
                |--------------------------------------------------------------------------
                | केवल plots > 0 वाले villages और selected phase consider होंगे।
                |--------------------------------------------------------------------------
                */
                $villageRows = DB::table('villagemaster as v')
                    ->where('v.plots', '>', 0)

                    ->when(
                        $phase !== null,
                        fn($query) => $query->where(
                            'v.Phase',
                            $phase
                        )
                    )

                    ->when(
                        $districtId !== null,
                        fn($query) => $query->where(
                            'v.DistrictId',
                            $districtId
                        )
                    )

                    ->select([
                        'v.DistrictId',
                    ])

                    ->selectRaw("
                    COUNT(DISTINCT v.VillageId)
                        AS VillagesWithPlots
                ")

                    ->groupBy('v.DistrictId')
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | Only districts having villages with plots
                |--------------------------------------------------------------------------
                */
                $reportDistrictIds = $villageRows
                    ->pluck('DistrictId')
                    ->filter()
                    ->map(
                        fn($id) => (int) $id
                    )
                    ->unique()
                    ->values();

                /*
                |--------------------------------------------------------------------------
                | No matching district
                |--------------------------------------------------------------------------
                */
                if ($reportDistrictIds->isEmpty()) {
                    return [
                        'report' => collect(),

                        'grossTotal' => (object) [
                            'VillagesWithPlots' => 0,
                            'RegisteredBeneficiaries' => 0,
                            'AllottedBeneficiaries' => 0,
                            'ApprovedPaid' => 0,
                            'ApprovedUnpaid' => 0,
                            'PendingApprovalPayment' => 0,
                            'Rejected' => 0,
                            'AllotmentCancelled' => 0,
                        ],
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | Registered beneficiaries
                |--------------------------------------------------------------------------
                | Dashboard applicants logic same:
                |
                | OwnerMaster.Phase filter
                | Owner का village plots > 0 होना चाहिए
                |--------------------------------------------------------------------------
                */
                $registeredRows = DB::table('ownermaster as o')
                    ->whereIn(
                        'o.DistrictId',
                        $reportDistrictIds->all()
                    )

                    ->whereExists(function ($query) {
                        $query
                            ->selectRaw('1')
                            ->from('villagemaster as rv')
                            ->whereColumn(
                                'rv.VillageId',
                                'o.VillageId'
                            )
                            ->where('rv.plots', '>', 0);
                    })

                    ->when(
                        $phase !== null,
                        fn($query) => $query->where(
                            'o.Phase',
                            $phase
                        )
                    )

                    ->when(
                        $districtId !== null,
                        fn($query) => $query->where(
                            'o.DistrictId',
                            $districtId
                        )
                    )

                    ->select([
                        'o.DistrictId',
                    ])

                    ->selectRaw("
                    COUNT(o.OwnerId)
                        AS RegisteredBeneficiaries
                ")

                    ->groupBy('o.DistrictId')
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | Allotment statistics
                |--------------------------------------------------------------------------
                | Dashboard के exact allotment logic के अनुसार:
                |
                | OwnerMaster INNER JOIN FlatMaster
                | OwnerMaster.Phase filter
                |
                | Village phase/plots condition यहां जानबूझकर नहीं लगाई गई,
                | इसलिए dashboard के counts match रहेंगे।
                |--------------------------------------------------------------------------
                */
                $allotmentRows = DB::table('ownermaster as o')
                    ->join(
                        'flatmaster as f',
                        'f.FlatId',
                        '=',
                        'o.FlatId'
                    )

                    ->whereIn(
                        'o.DistrictId',
                        $reportDistrictIds->all()
                    )

                    ->when(
                        $phase !== null,
                        fn($query) => $query->where(
                            'o.Phase',
                            $phase
                        )
                    )

                    ->when(
                        $districtId !== null,
                        fn($query) => $query->where(
                            'o.DistrictId',
                            $districtId
                        )
                    )

                    ->select([
                        'o.DistrictId',
                    ])

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

                    ->groupBy('o.DistrictId')
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | Convert aggregate collections into indexed maps
                |--------------------------------------------------------------------------
                */
                $villageMap = $villageRows->keyBy(
                    fn($row) => (int) $row->DistrictId
                );

                $registeredMap = $registeredRows->keyBy(
                    fn($row) => (int) $row->DistrictId
                );

                $allotmentMap = $allotmentRows->keyBy(
                    fn($row) => (int) $row->DistrictId
                );

                /*
                |--------------------------------------------------------------------------
                | District names
                |--------------------------------------------------------------------------
                | केवल villages with plots वाले districts fetch होंगे।
                |--------------------------------------------------------------------------
                */
                $districtRows = DB::table('districtmaster as d')
                    ->whereIn(
                        'd.DistrictId',
                        $reportDistrictIds->all()
                    )

                    ->when(
                        $districtId !== null,
                        fn($query) => $query->where(
                            'd.DistrictId',
                            $districtId
                        )
                    )

                    ->select([
                        'd.DistrictId',
                        'd.DistrictName',
                    ])

                    ->orderBy('d.DistrictName')
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | Merge district records
                |--------------------------------------------------------------------------
                */
                $report = $districtRows
                    ->map(function ($district) use ($villageMap, $registeredMap, $allotmentMap) {
                        $id = (int) $district->DistrictId;

                        $village = $villageMap->get($id);
                        $registered = $registeredMap->get($id);
                        $allotment = $allotmentMap->get($id);

                        return (object) [
                            'DistrictId' =>
                                $id,

                            'DistrictName' =>
                                $district->DistrictName,

                            'VillagesWithPlots' =>
                                (int) (
                                    $village->VillagesWithPlots ?? 0
                                ),

                            'RegisteredBeneficiaries' =>
                                (int) (
                                    $registered->RegisteredBeneficiaries
                                    ?? 0
                                ),

                            'AllottedBeneficiaries' =>
                                (int) (
                                    $allotment->AllottedBeneficiaries
                                    ?? 0
                                ),

                            'ApprovedPaid' =>
                                (int) (
                                    $allotment->ApprovedPaid ?? 0
                                ),

                            'ApprovedUnpaid' =>
                                (int) (
                                    $allotment->ApprovedUnpaid ?? 0
                                ),

                            'PendingApprovalPayment' =>
                                (int) (
                                    $allotment->PendingApprovalPayment
                                    ?? 0
                                ),

                            'Rejected' =>
                                (int) (
                                    $allotment->Rejected ?? 0
                                ),

                            'AllotmentCancelled' =>
                                (int) (
                                    $allotment->AllotmentCancelled
                                    ?? 0
                                ),
                        ];
                    })
                    ->values();

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
        | Phase-dependent district dropdown
        |--------------------------------------------------------------------------
        | केवल plots > 0 वाले village districts आएँगे।
        |--------------------------------------------------------------------------
        */
        $districtCacheKey = 'district_report_dropdown_v3_'
            . ($phase ?? 'all');

        $districts = Cache::remember(
            $districtCacheKey,
            now()->addMinutes(30),
            function () use ($phase) {
                return DB::table('districtmaster as d')
                    ->whereExists(function ($query) use ($phase) {
                        $query
                            ->selectRaw('1')
                            ->from('villagemaster as v')
                            ->whereColumn(
                                'v.DistrictId',
                                'd.DistrictId'
                            )
                            ->where('v.plots', '>', 0)

                            ->when(
                                $phase !== null,
                                fn($subQuery) => $subQuery->where(
                                    'v.Phase',
                                    $phase
                                )
                            );
                    })

                    ->select([
                        'd.DistrictId',
                        'd.DistrictName',
                    ])

                    ->orderBy('d.DistrictName')
                    ->get();
            }
        );

        $report = $reportData['report'];
        $grossTotal = $reportData['grossTotal'];

        return view(
            'mmgay.super-admin.district-report',
            compact(
                'report',
                'grossTotal',
                'districts'
            )
        );
    }

    public function districtReportCsv(Request $request)
    {
        DB::disableQueryLog();

        /*
        |--------------------------------------------------------------------------
        | Existing district report का same filtered cached data
        |--------------------------------------------------------------------------
        */
        $districtReportView = $this->districtWiseReport($request);

        $viewData = $districtReportView->getData();

        $report = collect($viewData['report'] ?? []);
        $grossTotal = $viewData['grossTotal'] ?? (object) [];

        /*
        |--------------------------------------------------------------------------
        | Same stable order as screen and print
        |--------------------------------------------------------------------------
        */
        $report = $report
            ->sortBy(function ($row) {
                return mb_strtolower(
                    trim((string) ($row->DistrictName ?? ''))
                );
            })
            ->values();

        $phase = $request->filled('phase')
            ? 'Phase-' . (int) $request->input('phase')
            : 'All-Phases';

        $district = $request->filled('district_id')
            ? 'District-' . (int) $request->input('district_id')
            : 'All-Districts';

        $fileName = 'District_Report_'
            . $phase
            . '_'
            . $district
            . '_'
            . now()->format('d-m-Y_H-i-s')
            . '.csv';

        return response()->streamDownload(
            function () use ($report, $grossTotal) {
                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    throw new \RuntimeException(
                        'CSV stream could not be opened.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | UTF-8 BOM — Excel compatible
                |--------------------------------------------------------------------------
                */
                fwrite($handle, "\xEF\xBB\xBF");

                fputcsv($handle, [
                    'Sr. No.',
                    'District',
                    'Villages',
                    'Applicants',
                    'Allotted',
                    'Approved & Paid',
                    'Approved & Unpaid',
                    'Yet to be Approved',
                    'Rejected',
                    'Cancelled',
                ]);

                foreach ($report as $index => $row) {
                    fputcsv($handle, [
                        $index + 1,
                        $row->DistrictName ?? '-',
                        (int) ($row->VillagesWithPlots ?? 0),
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
                | Separator
                |--------------------------------------------------------------------------
                */
                fputcsv($handle, []);

                /*
                |--------------------------------------------------------------------------
                | Gross total
                |--------------------------------------------------------------------------
                */
                fputcsv($handle, [
                    '',
                    'GROSS TOTAL',
                    (int) ($grossTotal->VillagesWithPlots ?? 0),
                    (int) ($grossTotal->RegisteredBeneficiaries ?? 0),
                    (int) ($grossTotal->AllottedBeneficiaries ?? 0),
                    (int) ($grossTotal->ApprovedPaid ?? 0),
                    (int) ($grossTotal->ApprovedUnpaid ?? 0),
                    (int) ($grossTotal->PendingApprovalPayment ?? 0),
                    (int) ($grossTotal->Rejected ?? 0),
                    (int) ($grossTotal->AllotmentCancelled ?? 0),
                ]);

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',

                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate',

                'Pragma' => 'no-cache',

                'Expires' => '0',
            ]
        );
    }

    public function districtReportPrint(Request $request)
    {
        DB::disableQueryLog();

        /*
        |--------------------------------------------------------------------------
        | Existing screen function का same filtered cached data
        |--------------------------------------------------------------------------
        */
        $districtReportView = $this->districtWiseReport($request);

        $viewData = $districtReportView->getData();

        $report = collect($viewData['report'] ?? [])
            ->sortBy(function ($row) {
                return mb_strtolower(
                    trim((string) ($row->DistrictName ?? ''))
                );
            })
            ->values();

        $grossTotal = $viewData['grossTotal'] ?? (object) [];
        $districts = $viewData['districts'] ?? collect();

        $selectedDistrict = null;

        if ($request->filled('district_id')) {
            $selectedDistrict = $districts->firstWhere(
                'DistrictId',
                (int) $request->input('district_id')
            );
        }

        $filters = (object) [
            'phase' => $request->filled('phase')
                ? (int) $request->input('phase')
                : null,

            'districtName' =>
                $selectedDistrict->DistrictName ?? null,
        ];

        return view(
            'mmgay.super-admin.district-report-print',
            compact(
                'report',
                'grossTotal',
                'filters'
            )
        );
    }

    public function districtReportDistricts(
        Request $request
    ) {
        DB::disableQueryLog();

        $phase = $request->filled('phase')
            ? (int) $request->input('phase')
            : null;

        $cacheKey = 'district_report_ajax_districts_v2_'
            . ($phase ?? 'all');

        $districts = Cache::remember(
            $cacheKey,
            now()->addMinutes(30),
            function () use ($phase) {
                return DB::table('districtmaster as d')
                    ->whereExists(function ($query) use ($phase) {
                        $query
                            ->selectRaw('1')
                            ->from('villagemaster as v')
                            ->whereColumn(
                                'v.DistrictId',
                                'd.DistrictId'
                            )
                            ->where('v.plots', '>', 0)

                            ->when(
                                $phase !== null,
                                fn($subQuery) => $subQuery->where(
                                    'v.Phase',
                                    $phase
                                )
                            );
                    })

                    ->select([
                        'd.DistrictId',
                        'd.DistrictName',
                    ])

                    ->orderBy('d.DistrictName')
                    ->get();
            }
        );

        return response()->json([
            'success' => true,
            'districts' => $districts,
        ]);
    }

    public function districtReportData(Request $request)
    {
        $phase = $request->phase;

        $districtId = $request->district_id;


        $report = DB::table('districtmaster as d')

            ->leftJoin('villagemaster as v', function ($join) use ($phase) {

                $join->on('d.DistrictId', '=', 'v.DistrictId')
                    ->where('v.plots', '>', 0);

                if ($phase) {
                    $join->where('v.phase', $phase);
                }

            })

            ->leftJoin('ownermaster as o', function ($join) use ($phase) {

                $join->on('v.VillageId', '=', 'o.VillageId');

                if ($phase) {
                    $join->where('o.Phase', $phase);
                }

            })

            ->leftJoin('flatmaster as f', 'f.FlatId', '=', 'o.FlatId')


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


        $districts = DB::table('districtmaster')
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
        $villageId = $request->filled('village_id')
            ? (int) $request->input('village_id')
            : null;
        $districtId = $request->filled('district_id')
            ? (int) $request->input('district_id')
            : null;

        /*
        |--------------------------------------------------------------------------
        | Beneficiary Summary
        |--------------------------------------------------------------------------
        | Village grouping : OwnerMaster.VillageId
        | Allotment check  : OwnerMaster.FlatId = FlatMaster.FlatId
        |--------------------------------------------------------------------------
        */
        $beneficiarySummary = DB::table('ownermaster as o')
            ->leftJoin('flatmaster as f', 'f.FlatId', '=', 'o.FlatId')
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
        return DB::table('villagemaster as v')
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
            ->when(
                $districtId !== null,
                function ($query) use ($districtId) {
                    $query->where(
                        'v.DistrictId',
                        $districtId
                    );
                }
            )
            ->when(
                $villageId !== null,
                function ($query) use ($villageId) {
                    $query->where(
                        'v.VillageId',
                        $villageId
                    );
                }
            )
            ->selectRaw("
            v.VillageId,
            v.VillageName,
            v.Phase,
            v.map_pdf AS pdf,
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

    public function villageReportFilterDistricts(
        Request $request
    ) {
        DB::disableQueryLog();

        $phase = $request->filled('phase')
            ? (int) $request->input('phase')
            : null;

        $cacheKey = 'village_report_ajax_districts_v2_'
            . ($phase ?? 'all');

        $districts = Cache::remember(
            $cacheKey,
            now()->addMinutes(30),
            function () use ($phase) {
                return DB::table('districtmaster as d')
                    ->whereExists(function ($query) use ($phase) {
                        $query
                            ->selectRaw('1')
                            ->from('villagemaster as v')
                            ->whereColumn(
                                'v.DistrictId',
                                'd.DistrictId'
                            )
                            ->whereNotNull('v.plots')
                            ->where('v.plots', '>', 0)

                            ->when(
                                $phase !== null,
                                fn($subQuery) =>
                                $subQuery->where(
                                    'v.Phase',
                                    $phase
                                )
                            );
                    })

                    ->select([
                        'd.DistrictId',
                        'd.DistrictName',
                    ])

                    ->orderBy('d.DistrictName')
                    ->get();
            }
        );

        return response()->json([
            'success' => true,
            'districts' => $districts,
        ]);
    }

    public function villageReportFilterVillages(
        Request $request
    ) {
        DB::disableQueryLog();

        $phase = $request->filled('phase')
            ? (int) $request->input('phase')
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->input('district_id')
            : null;

        $cacheKey = 'village_report_ajax_villages_v2_'
            . ($phase ?? 'all')
            . '_'
            . ($districtId ?? 'all');

        $villages = Cache::remember(
            $cacheKey,
            now()->addMinutes(30),
            function () use ($phase, $districtId) {
                return DB::table('villagemaster as v')
                    ->whereNotNull('v.plots')
                    ->where('v.plots', '>', 0)

                    ->when(
                        $phase !== null,
                        fn($query) => $query->where(
                            'v.Phase',
                            $phase
                        )
                    )

                    ->when(
                        $districtId !== null,
                        fn($query) => $query->where(
                            'v.DistrictId',
                            $districtId
                        )
                    )

                    ->select([
                        'v.VillageId',
                        'v.VillageName',
                    ])

                    ->orderBy('v.VillageName')
                    ->get();
            }
        );

        return response()->json([
            'success' => true,
            'villages' => $villages,
        ]);
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

        return DB::table('ownermaster as o')
            ->join(
                'flatmaster as f',
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

    // ---------------------------Village Report Data-----------------------------------------------|

    private function villageReportData(
        Request $request,
        bool $paginate = true
    ): array {
        DB::disableQueryLog();

        $phase = $this->villageReportPhase($request);

        $districtId = $request->filled('district_id')
            ? (int) $request->input('district_id')
            : null;

        /*
        |--------------------------------------------------------------------------
        | Main report query
        |--------------------------------------------------------------------------
        */
        $allReportRows = $this
            ->villageReportQuery($request)
            ->get();

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
        */
        if ($paginate) {
            $perPage = 50;

            $currentPage = max(
                1,
                LengthAwarePaginator::resolveCurrentPage()
            );

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
            $report = $allReportRows;
        }

        /*
        |--------------------------------------------------------------------------
        | Phase-dependent District Dropdown
        |--------------------------------------------------------------------------
        | केवल plots > 0 वाले villages के districts आएंगे।
        |--------------------------------------------------------------------------
        */
        $districtCacheKey = 'village_report_districts_v2_'
            . ($phase ?? 'all');

        $districts = Cache::remember(
            $districtCacheKey,
            now()->addMinutes(30),
            function () use ($phase) {
                return DB::table('districtmaster as d')
                    ->whereExists(function ($query) use ($phase) {
                        $query
                            ->selectRaw('1')
                            ->from('villagemaster as v')
                            ->whereColumn(
                                'v.DistrictId',
                                'd.DistrictId'
                            )
                            ->whereNotNull('v.plots')
                            ->where('v.plots', '>', 0)

                            ->when(
                                $phase !== null,
                                fn($subQuery) =>
                                $subQuery->where(
                                    'v.Phase',
                                    $phase
                                )
                            );
                    })

                    ->select([
                        'd.DistrictId',
                        'd.DistrictName',
                    ])

                    ->orderBy('d.DistrictName')
                    ->get();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Phase + District-dependent Village Dropdown
        |--------------------------------------------------------------------------
        */
        $villageCacheKey = 'village_report_villages_v2_'
            . ($phase ?? 'all')
            . '_'
            . ($districtId ?? 'all');

        $villages = Cache::remember(
            $villageCacheKey,
            now()->addMinutes(30),
            function () use ($phase, $districtId) {
                return DB::table('villagemaster as v')
                    ->whereNotNull('v.plots')
                    ->where('v.plots', '>', 0)

                    ->when(
                        $phase !== null,
                        fn($query) => $query->where(
                            'v.Phase',
                            $phase
                        )
                    )

                    ->when(
                        $districtId !== null,
                        fn($query) => $query->where(
                            'v.DistrictId',
                            $districtId
                        )
                    )

                    ->select([
                        'v.VillageId',
                        'v.VillageName',
                        'v.Phase',
                        'v.DistrictId',
                    ])

                    ->orderBy('v.Phase')
                    ->orderBy('v.VillageName')
                    ->get();
            }
        );

        return [
            'report' => $report,
            'grossTotal' => $grossTotal,
            'districts' => $districts,
            'villages' => $villages,
        ];
    }

    public function villageWiseReport(Request $request)
    {
        return view(
            'mmgay.super-admin.village-report',
            $this->villageReportData($request, true)
        );
    }
    public function villageReportPrint(Request $request)
    {
        $data = $this->villageReportData($request, false);

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

    public function villageReportCsv(Request $request)
    {
        DB::disableQueryLog();

        @set_time_limit(0);

        $fileName = 'Village_Report_'
            . now()->format('d-m-Y_H-i-s')
            . '.csv';

        /*
        |--------------------------------------------------------------------------
        | Query builder only
        |--------------------------------------------------------------------------
        | यहां get() नहीं चलेगा। Actual records stream callback में cursor()
        | द्वारा एक-एक करके पढ़े जाएंगे।
        |--------------------------------------------------------------------------
        */
        $rowsQuery = $this->villageReportQuery($request);

        return response()->streamDownload(
            function () use ($rowsQuery, $request) {
                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    throw new \RuntimeException(
                        'CSV stream could not be opened.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | UTF-8 BOM
                |--------------------------------------------------------------------------
                | Excel में Hindi/Unicode text सही खुलेगा।
                |--------------------------------------------------------------------------
                */
                fwrite($handle, "\xEF\xBB\xBF");

                /*
                |--------------------------------------------------------------------------
                | CSV headings पहले भेजें
                |--------------------------------------------------------------------------
                | इससे click के तुरंत बाद browser download शुरू कर सकता है।
                |--------------------------------------------------------------------------
                */
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

                fflush($handle);

                /*
                |--------------------------------------------------------------------------
                | Running totals
                |--------------------------------------------------------------------------
                | Total plots और applicants streaming के साथ calculate होंगे।
                |--------------------------------------------------------------------------
                */
                $serialNumber = 1;
                $totalPlots = 0;
                $registeredBeneficiaries = 0;

                /*
                |--------------------------------------------------------------------------
                | Stream rows
                |--------------------------------------------------------------------------
                | get() के बजाय cursor() memory usage बहुत कम रखेगा।
                |--------------------------------------------------------------------------
                */
                foreach ($rowsQuery->cursor() as $row) {
                    $rowTotalPlots = (int) (
                        $row->TotalPlots ?? 0
                    );

                    $rowRegistered = (int) (
                        $row->RegisteredBeneficiaries ?? 0
                    );

                    $totalPlots += $rowTotalPlots;
                    $registeredBeneficiaries += $rowRegistered;

                    fputcsv($handle, [
                        $serialNumber++,
                        $row->VillageName ?? '-',
                        $row->Phase ?? '-',
                        $rowTotalPlots,
                        $rowRegistered,
                        (int) (
                            $row->AllottedBeneficiaries ?? 0
                        ),
                        (int) (
                            $row->ApprovedPaid ?? 0
                        ),
                        (int) (
                            $row->ApprovedUnpaid ?? 0
                        ),
                        (int) (
                            $row->PendingApprovalPayment ?? 0
                        ),
                        (int) (
                            $row->Rejected ?? 0
                        ),
                        (int) (
                            $row->AllotmentCancelled ?? 0
                        ),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Output flush
                    |--------------------------------------------------------------------------
                    | हर 200 records के बाद buffered output browser को भेजें।
                    |--------------------------------------------------------------------------
                    */
                    if (($serialNumber - 1) % 200 === 0) {
                        fflush($handle);

                        if (
                            function_exists('ob_get_level')
                            && ob_get_level() > 0
                        ) {
                            @ob_flush();
                        }

                        flush();
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Dashboard-matching allotment totals
                |--------------------------------------------------------------------------
                | Cursor पूरा consume होने के बाद query चलेगी, ताकि unbuffered query
                | connection conflict न हो और existing total logic भी न बदले।
                |--------------------------------------------------------------------------
                */
                $allotmentStats = $this
                    ->dashboardAllotmentStats($request);

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
                    $totalPlots,
                    $registeredBeneficiaries,

                    (int) (
                        $allotmentStats->AllottedBeneficiaries
                        ?? 0
                    ),

                    (int) (
                        $allotmentStats->ApprovedPaid
                        ?? 0
                    ),

                    (int) (
                        $allotmentStats->ApprovedUnpaid
                        ?? 0
                    ),

                    (int) (
                        $allotmentStats->PendingApprovalPayment
                        ?? 0
                    ),

                    (int) (
                        $allotmentStats->Rejected
                        ?? 0
                    ),

                    (int) (
                        $allotmentStats->AllotmentCancelled
                        ?? 0
                    ),
                ]);

                fflush($handle);
                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',

                'Content-Disposition' =>
                    'attachment; filename="' . $fileName . '"',

                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate, max-age=0',

                'Pragma' => 'no-cache',

                'Expires' => '0',

                /*
                |--------------------------------------------------------------------------
                | Disable proxy buffering
                |--------------------------------------------------------------------------
                */
                'X-Accel-Buffering' => 'no',
            ]
        );
    }
    public function villageSiteDevelopment(
        Request $request,
        int $villageId
    ) {
        $village = DB::table('villagemaster as v')
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

    // Applicant Filters

    private function applicantsBaseQuery(Request $request)
    {
        $query = DB::table('ownermaster as o')
            ->join(
                'villagemaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )
            ->leftJoin(
                'flatmaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )
            ->where('v.plots', '>', 0);

        return $this->applyApplicantFilters(
            $query,
            $request
        );
    }

    private function applicantsQuery(Request $request)
    {
        return $this
            ->applicantsBaseQuery($request)
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
    }

    private function applicantsCountCacheKey(
        Request $request
    ): string {
        return 'superadmin_applicants_count_v2_' . md5(
            json_encode([
                'search' => trim(
                    (string) $request->query('search', '')
                ),
                'phase' => $request->query('phase'),
                'district_id' => $request->query('district_id'),
                'block_id' => $request->query('block_id'),
                'village_id' => $request->query('village_id'),
                'status' => $request->query('status'),
            ])
        );
    }

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
            ? (int) $request->input('phase')
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->input('district_id')
            : null;

        $blockId = $request->filled('block_id')
            ? (int) $request->input('block_id')
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

        $currentPage = max(
            1,
            (int) $request->query('page', 1)
        );

        /*
        |--------------------------------------------------------------------------
        | Cached total
        |--------------------------------------------------------------------------
        | Heavy count हर page load पर नहीं चलेगा।
        |--------------------------------------------------------------------------
        */
        $totalApplicants = Cache::remember(
            $this->applicantsCountCacheKey($request),
            now()->addMinutes(2),
            function () use ($request) {
                return (int) $this
                    ->applicantsBaseQuery($request)
                    ->distinct()
                    ->count('o.OwnerId');
            }
        );

        $lastPage = max(
            1,
            (int) ceil(
                $totalApplicants / $perPage
            )
        );

        $currentPage = min(
            $currentPage,
            $lastPage
        );

        /*
        |--------------------------------------------------------------------------
        | Current page IDs only
        |--------------------------------------------------------------------------
        | पहले सिर्फ indexed OwnerId fetch होंगे।
        |--------------------------------------------------------------------------
        */
        $ownerIds = $this
            ->applicantsBaseQuery($request)
            ->select('o.OwnerId')
            ->distinct()
            ->orderByDesc('o.OwnerId')
            ->forPage(
                $currentPage,
                $perPage
            )
            ->pluck('o.OwnerId')
            ->map(
                static fn($id) => (int) $id
            )
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Current page details
        |--------------------------------------------------------------------------
        */
        $rows = collect();

        if (!empty($ownerIds)) {
            $rows = DB::table('ownermaster as o')
                ->join(
                    'villagemaster as v',
                    'v.VillageId',
                    '=',
                    'o.VillageId'
                )
                ->leftJoin(
                    'flatmaster as f',
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
            ")
                ->orderByDesc('o.OwnerId')
                ->get();
        }

        $applicants = new LengthAwarePaginator(
            $rows,
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
        | Village dropdown
        |--------------------------------------------------------------------------
        */
        $villageCacheKey = 'applicant_villages_v2_' . md5(
            json_encode([
                'phase' => $phase,
                'district_id' => $districtId,
                'block_id' => $blockId,
            ])
        );

        $villages = Cache::remember(
            $villageCacheKey,
            now()->addMinutes(20),
            function () use ($phase, $districtId, $blockId) {
                return DB::table('villagemaster as v')
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

                    ->whereExists(function ($query) use ($phase, $districtId, $blockId) {
                        $query
                            ->selectRaw('1')
                            ->from('ownermaster as vo')
                            ->whereColumn(
                                'vo.VillageId',
                                'v.VillageId'
                            )

                            ->when(
                                $phase !== null,
                                fn($subQuery) =>
                                $subQuery->where(
                                    'vo.Phase',
                                    $phase
                                )
                            )

                            ->when(
                                $districtId !== null,
                                fn($subQuery) =>
                                $subQuery->where(
                                    'vo.DistrictId',
                                    $districtId
                                )
                            )

                            ->when(
                                $blockId !== null,
                                fn($subQuery) =>
                                $subQuery->where(
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

    public function applicantsExcel(Request $request)
    {
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
        DB::disableQueryLog();

        set_time_limit(0);

        $fileName = 'Applicants_'
            . now()->format('Ymd_His')
            . '.csv';

        /*
        |--------------------------------------------------------------------------
        | Lightweight export query
        |--------------------------------------------------------------------------
        | केवल CSV में इस्तेमाल होने वाले columns लिए गए हैं।
        |--------------------------------------------------------------------------
        */
        $query = DB::table('ownermaster as o')
            ->join(
                'villagemaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )
            ->leftJoin(
                'flatmaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )
            ->where('v.plots', '>', 0);

        /*
        |--------------------------------------------------------------------------
        | Existing filters
        |--------------------------------------------------------------------------
        */
        $query = $this->applyApplicantFilters(
            $query,
            $request
        );

        $query
            ->select([
                'o.OwnerId',
                'o.RegistrationNo',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.PPPId',
                'o.Phase',

                'o.IsApproved',
                'o.IsRejected',
                'o.IsPaid',
                'o.IsAllotmentCancelled',

                'v.VillageName',

                'f.FlatNo',
            ])
            ->orderByDesc('o.OwnerId');

        return response()->streamDownload(
            function () use ($query) {
                /*
                |--------------------------------------------------------------------------
                | Remove existing output buffers
                |--------------------------------------------------------------------------
                */
                while (ob_get_level() > 0) {
                    @ob_end_clean();
                }

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
                */
                fwrite($handle, "\xEF\xBB\xBF");

                /*
                |--------------------------------------------------------------------------
                | Header immediately output करें
                |--------------------------------------------------------------------------
                */
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
                    'Flat No.',
                    'Status',
                ]);

                fflush($handle);

                if (function_exists('flush')) {
                    flush();
                }

                $serialNumber = 1;

                /*
                |--------------------------------------------------------------------------
                | Single SQL query streaming
                |--------------------------------------------------------------------------
                | lazyByIdDesc() नहीं है, इसलिए repeated database queries नहीं चलेंगी।
                |--------------------------------------------------------------------------
                */
                foreach ($query->cursor() as $applicant) {
                    if (
                        (int) $applicant->IsAllotmentCancelled === 1
                    ) {
                        $status = 'Cancelled';
                    } elseif (
                        (int) $applicant->IsRejected === 1
                    ) {
                        $status = 'Rejected';
                    } elseif (
                        (int) $applicant->IsApproved === 1
                        && (int) $applicant->IsPaid === 1
                    ) {
                        $status = 'Approved & Paid';
                    } elseif (
                        (int) $applicant->IsApproved === 1
                        && (int) $applicant->IsPaid === 0
                    ) {
                        $status = 'Approved & Unpaid';
                    } elseif (
                        (int) $applicant->IsApproved === 0
                    ) {
                        $status = 'Yet to be Approved';
                    } else {
                        $status = 'Allotted';
                    }

                    fputcsv($handle, [
                        $serialNumber++,
                        $applicant->OwnerId ?? '',
                        $applicant->RegistrationNo ?? '',
                        $applicant->OwnerName ?? '',
                        $applicant->FatherHusbandName ?? '',
                        $applicant->MobileNo ?? '',
                        $applicant->PPPId ?? '',
                        $applicant->VillageName ?? '',
                        $applicant->Phase ?? '',
                        $applicant->FlatNo ?? '',
                        $status,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Periodic browser flush
                    |--------------------------------------------------------------------------
                    */
                    if (($serialNumber - 1) % 200 === 0) {
                        fflush($handle);

                        if (function_exists('flush')) {
                            flush();
                        }
                    }
                }

                fflush($handle);
                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',

                'Content-Disposition' =>
                    'attachment; filename="' . $fileName . '"',

                'Cache-Control' =>
                    'no-cache, no-store, must-revalidate, max-age=0',

                'Pragma' =>
                    'no-cache',

                'Expires' =>
                    '0',

                'X-Accel-Buffering' =>
                    'no',
            ]
        );
    }

    public function applicantView(string $secureId)
    {
        DB::disableQueryLog();

        $applicant = DB::table('ownermaster as o')
            ->leftJoin(
                'districtmaster as d',
                'd.DistrictId',
                '=',
                'o.DistrictId'
            )
            ->leftJoin(
                'blockmaster as b',
                'b.BlockId',
                '=',
                'o.BlockId'
            )
            ->leftJoin(
                'villagemaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )
            ->leftJoin(
                'flatmaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )
            ->where('o.secure_id', $secureId)
            ->select([
                /*
                |--------------------------------------------------------------------------
                | Applicant identity
                |--------------------------------------------------------------------------
                */
                'o.OwnerId',
                'o.secure_id',
                'o.OwnerName',
                'o.Relation',
                'o.FatherHusbandName',
                'o.Gender',
                'o.Caste',
                'o.MobileNo',
                'o.OwnerAddress',

                /*
                |--------------------------------------------------------------------------
                | Application identifiers
                |--------------------------------------------------------------------------
                */
                'o.RegistrationNo',
                'o.PPPId',
                'o.MemberId',
                'o.CompanyId',
                'o.Phase',

                /*
                |--------------------------------------------------------------------------
                | Location and property
                |--------------------------------------------------------------------------
                */
                'o.DistrictId',
                'o.BlockId',
                'o.VillageId',
                'o.FlatId',

                'd.DistrictName',
                'b.BlockName',
                'v.VillageName',
                'v.plots as VillagePlots',

                'f.FlatNo',

                /*
                |--------------------------------------------------------------------------
                | Status fields
                |--------------------------------------------------------------------------
                */
                'o.IsApproved',
                'o.IsRejected',
                'o.IsDcReconsidered',
                'o.DCReOpenedCount',
                'o.IsPaid',
                'o.IsPaymentApproved',
                'o.IsAllotmentCancelled',

                /*
                |--------------------------------------------------------------------------
                | Remarks and audit information
                |--------------------------------------------------------------------------
                */
                'o.Remarks',
                'o.DCRemarks',
                'o.CreatedBy',
                'o.CreatedDate',
                'o.UpdatedBy',
                'o.UpdatedDate',
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
                 AND o.IsPaid = 0
                    THEN 'Approved & Unpaid'

                WHEN o.IsApproved = 0
                    THEN 'Yet to be Approved'

                ELSE 'Allotted'
            END AS ApplicantStatus
        ")
            ->first();

        abort_if(
            $applicant === null,
            404,
            'Applicant record not found.'
        );

        return view(
            'mmgay.super-admin.applicants.show',
            compact('applicant')
        );
    }

    public function applicantsPrint(Request $request)
    {
        DB::disableQueryLog();

        $perBatch = (int) $request->query(
            'print_limit',
            500
        );

        if (
            !in_array(
                $perBatch,
                [200, 500, 1000, 2000],
                true
            )
        ) {
            $perBatch = 500;
        }

        $printPage = max(
            1,
            (int) $request->query(
                'print_page',
                1
            )
        );

        $countCacheKey =
            $this->applicantsCountCacheKey($request)
            . '_print';

        $totalRecords = Cache::remember(
            $countCacheKey,
            now()->addMinutes(2),
            function () use ($request) {
                return (int) $this
                    ->applicantsBaseQuery($request)
                    ->distinct()
                    ->count('o.OwnerId');
            }
        );

        $totalPrintPages = max(
            1,
            (int) ceil(
                $totalRecords / $perBatch
            )
        );

        $printPage = min(
            $printPage,
            $totalPrintPages
        );

        $ownerIds = $this
            ->applicantsBaseQuery($request)
            ->select('o.OwnerId')
            ->distinct()
            ->orderByDesc('o.OwnerId')
            ->forPage(
                $printPage,
                $perBatch
            )
            ->pluck('o.OwnerId')
            ->map(
                static fn($id) => (int) $id
            )
            ->all();

        $records = collect();

        if (!empty($ownerIds)) {
            $records = DB::table('ownermaster as o')
                ->join(
                    'villagemaster as v',
                    'v.VillageId',
                    '=',
                    'o.VillageId'
                )
                ->leftJoin(
                    'flatmaster as f',
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
                    'o.OwnerName',
                    'o.FatherHusbandName',
                    'o.MobileNo',
                    'o.RegistrationNo',
                    'o.PPPId',
                    'o.Phase',
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
            ")
                ->orderByDesc('o.OwnerId')
                ->get();
        }

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

    public function applicantsPdf(Request $request)
    {
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

    // Applicants Function End

    private function allotmentReportBaseQuery(Request $request)
    {
        $phase = $request->filled('phase')
            ? (int) $request->input('phase')
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->input('district_id')
            : null;

        $blockId = $request->filled('block_id')
            ? (int) $request->input('block_id')
            : null;

        $villageId = $request->filled('village_id')
            ? (int) $request->input('village_id')
            : null;

        $search = trim(
            (string) $request->query('search', '')
        );

        /*
        |--------------------------------------------------------------------------
        | Lightweight base
        |--------------------------------------------------------------------------
        | FlatMaster one-to-one join है, इसलिए flat search direct और fast रहेगा।
        | Village को EXISTS से verify किया गया है, duplicate join नहीं होगा।
        |--------------------------------------------------------------------------
        */
        $query = DB::table('ownermaster as o')
            ->leftJoin(
                'flatmaster as sf',
                'sf.FlatId',
                '=',
                'o.FlatId'
            )
            ->where('o.FlatId', '>', 0)

            ->whereExists(function ($subQuery) {
                $subQuery
                    ->selectRaw('1')
                    ->from('villagemaster as vm')
                    ->whereColumn(
                        'vm.VillageId',
                        'o.VillageId'
                    )
                    ->where('vm.plots', '>', 0);
            })

            ->when(
                $phase !== null,
                fn($q) => $q->where('o.Phase', $phase)
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
        | Exact numeric search
        |--------------------------------------------------------------------------
        */
        if (ctype_digit($search)) {
            return $query->where(
                function ($subQuery) use ($search) {
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
                        ->orWhere(
                            'sf.FlatNo',
                            $search
                        );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | General contains search
        |--------------------------------------------------------------------------
        */
        $likeSearch = '%' . $search . '%';

        return $query->where(
            function ($subQuery) use ($likeSearch) {
                $subQuery
                    ->where(
                        'o.OwnerName',
                        'like',
                        $likeSearch
                    )
                    ->orWhere(
                        'o.FatherHusbandName',
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
                        'sf.FlatNo',
                        'like',
                        $likeSearch
                    );
            }
        );
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
                'districtmaster as d',
                'd.DistrictId',
                '=',
                'o.DistrictId'
            )
            ->leftJoin(
                'blockmaster as b',
                'b.BlockId',
                '=',
                'o.BlockId'
            )
            ->leftJoin(
                'villagemaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )
            ->select([
                'o.OwnerId',
                'o.secure_id',
                'o.RegistrationNo',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.PPPId',
                'o.MemberId',
                'o.Gender',
                DB::raw("
                    CASE
                        WHEN LOWER(TRIM(COALESCE(o.Caste, ''))) IN ('sc', 'widow', 'ghumantu')
                            THEN TRIM(o.Caste)
                        ELSE 'Others'
                    END AS Caste
                "),
                'o.Phase',
                'o.FlatId',

                'o.IsApproved',
                'o.IsRejected',
                'o.IsPaid',
                'o.IsAllotmentCancelled',

                'd.DistrictName',
                'b.BlockName',
                'v.VillageName',

                'sf.FlatNo',
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
        return 'allotment_summary_v8_' . md5(
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
        return 'allotment_total_v8_' . md5(
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

        $filteredQuery = $this->allotmentReportBaseQuery(
            $request
        );

        $filteredQuery = $this->applyAllotmentReportStatus(
            $filteredQuery,
            $request->query('status')
        );

        /*
        |--------------------------------------------------------------------------
        | Cached total
        |--------------------------------------------------------------------------
        */
        $total = (int) Cache::remember(
            $this->allotmentTotalCacheKey($request),
            now()->addMinutes(5),
            function () use ($filteredQuery) {
                return (clone $filteredQuery)
                    ->count('o.OwnerId');
            }
        );

        $lastPage = max(
            1,
            (int) ceil($total / $perPage)
        );

        $currentPage = min(
            $currentPage,
            $lastPage
        );

        /*
        |--------------------------------------------------------------------------
        | केवल current page IDs
        |--------------------------------------------------------------------------
        */
        $ownerIds = (clone $filteredQuery)
            ->reorder()
            ->select('o.OwnerId')
            ->orderByDesc('o.OwnerId')
            ->forPage(
                $currentPage,
                $perPage
            )
            ->pluck('o.OwnerId')
            ->map(
                static fn($ownerId) => (int) $ownerId
            )
            ->all();

        $records = collect();

        if (!empty($ownerIds)) {
            $records = DB::table('ownermaster as o')
                ->leftJoin(
                    'districtmaster as d',
                    'd.DistrictId',
                    '=',
                    'o.DistrictId'
                )
                ->leftJoin(
                    'blockmaster as b',
                    'b.BlockId',
                    '=',
                    'o.BlockId'
                )
                ->leftJoin(
                    'villagemaster as v',
                    'v.VillageId',
                    '=',
                    'o.VillageId'
                )
                ->leftJoin(
                    'flatmaster as f',
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
                    'o.secure_id',
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
                'path' => $request->url(),
                'pageName' => 'page',
                'query' => $request->except('page'),
            ]
        );
    }

    public function allotmentReport(Request $request)
    {
        DB::disableQueryLog();

        $phase = $request->filled('phase')
            ? (int) $request->input('phase')
            : null;

        $districtId = $request->filled('district_id')
            ? (int) $request->input('district_id')
            : null;

        $blockId = $request->filled('block_id')
            ? (int) $request->input('block_id')
            : null;

        /*
        |--------------------------------------------------------------------------
        | Dropdowns
        |--------------------------------------------------------------------------
        */
        $phases = Cache::remember(
            'allotment_report_phases_v2',
            now()->addHours(1),
            function () {
                return DB::table('ownermaster')
                    ->whereNotNull('Phase')
                    ->distinct()
                    ->orderBy('Phase')
                    ->pluck('Phase');
            }
        );

        $districts = Cache::remember(
            'allotment_report_districts_v2',
            now()->addHours(1),
            function () {
                return DB::table('districtmaster')
                    ->select([
                        'DistrictId',
                        'DistrictName',
                    ])
                    ->orderBy('DistrictName')
                    ->get();
            }
        );

        $blocks = Cache::remember(
            'allotment_report_blocks_v2_'
            . ($districtId ?? 'all'),
            now()->addHours(1),
            function () use ($districtId) {
                return DB::table('blockmaster')
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

        $villages = Cache::remember(
            sprintf(
                'allotment_report_villages_v2_%s_%s_%s',
                $districtId ?? 'all',
                $blockId ?? 'all',
                $phase ?? 'all'
            ),
            now()->addHours(1),
            function () use ($districtId, $blockId, $phase) {
                return DB::table('villagemaster as v')
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
        | Summary
        |--------------------------------------------------------------------------
        */
        $summary = Cache::remember(
            $this->allotmentSummaryCacheKey($request),
            now()->addMinutes(5),
            function () use ($request) {
                return $this
                    ->allotmentReportBaseQuery($request)
                    ->selectRaw("
                    COUNT(o.OwnerId) AS Total,

                    COALESCE(SUM(
                        CASE
                            WHEN o.IsApproved = 1
                             AND o.IsPaid = 1
                             AND o.IsRejected = 0
                             AND o.IsAllotmentCancelled = 0
                            THEN 1 ELSE 0
                        END
                    ), 0) AS ApprovedPaid,

                    COALESCE(SUM(
                        CASE
                            WHEN o.IsApproved = 1
                             AND (
                                o.IsPaid = 0
                                OR o.IsPaid IS NULL
                             )
                             AND o.IsRejected = 0
                             AND o.IsAllotmentCancelled = 0
                            THEN 1 ELSE 0
                        END
                    ), 0) AS ApprovedUnpaid,

                    COALESCE(SUM(
                        CASE
                            WHEN (
                                o.IsApproved = 0
                                OR o.IsApproved IS NULL
                            )
                             AND o.IsRejected = 0
                             AND o.IsAllotmentCancelled = 0
                            THEN 1 ELSE 0
                        END
                    ), 0) AS PendingApproval,

                    COALESCE(SUM(
                        CASE
                            WHEN o.IsRejected = 1
                             AND o.IsAllotmentCancelled = 0
                            THEN 1 ELSE 0
                        END
                    ), 0) AS Rejected,

                    COALESCE(SUM(
                        CASE
                            WHEN o.IsAllotmentCancelled = 1
                            THEN 1 ELSE 0
                        END
                    ), 0) AS Cancelled
                ")
                    ->first();
            }
        );

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

    public function allotmentReportCsv(Request $request)
    {
        DB::disableQueryLog();

        @set_time_limit(0);

        $fileName = 'Allotment_Report_'
            . now()->format('d-m-Y_H-i-s')
            . '.csv';

        $query = $this->allotmentReportDetailsQuery(
            $request
        );

        return response()->streamDownload(
            function () use ($query) {
                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    throw new \RuntimeException(
                        'CSV output stream could not be opened.'
                    );
                }

                fwrite($handle, "\xEF\xBB\xBF");

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

                fflush($handle);

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

                    if (($serialNumber - 1) % 250 === 0) {
                        fflush($handle);
                        flush();
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

                'Pragma' => 'no-cache',

                'Expires' => '0',

                'X-Accel-Buffering' => 'no',
            ]
        );
    }

    public function allotmentReportPrint(Request $request)
    {
        DB::disableQueryLog();

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

        $countQuery = $this->allotmentReportBaseQuery(
            $request
        );

        $countQuery = $this->applyAllotmentReportStatus(
            $countQuery,
            $request->query('status')
        );

        $totalRecords = (int) Cache::remember(
            $this->allotmentTotalCacheKey($request)
            . '_print',
            now()->addMinutes(5),
            fn() => (clone $countQuery)
                ->count('o.OwnerId')
        );

        $totalPrintPages = max(
            1,
            (int) ceil(
                $totalRecords / $printLimit
            )
        );

        $printPage = min(
            $printPage,
            $totalPrintPages
        );

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

            $query = DB::table('ownermaster as o')
                ->join('villagemaster as v', 'v.VillageId', '=', 'o.VillageId')
                ->leftJoin('districtmaster as d', 'd.DistrictId', '=', 'o.DistrictId')
                ->leftJoin('blockmaster as b', 'b.BlockId', '=', 'o.BlockId')
                ->leftJoin('flatmaster as f', 'f.FlatId', '=', 'o.FlatId')
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
                DB::raw("
                    CASE
                        WHEN LOWER(TRIM(COALESCE(o.Caste, ''))) IN ('sc', 'widow', 'ghumantu')
                            THEN TRIM(o.Caste)
                        ELSE 'Others'
                    END AS Caste
                "),
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

                    // Caste output: only SC, Widow and Ghumantu keep their category.
                    // Every other value (General, Gen, Others, blank, etc.) is exported as Others.
                    $rawCaste = strtoupper(trim((string) ($record->Caste ?? '')));

                    $exportCaste = match ($rawCaste) {
                        'SC' => 'SC',
                        'WIDOW' => 'Widow',
                        'GHUMANTU' => 'Ghumantu',
                        default => 'Others',
                    };

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
                        $exportCaste,
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
        $query = DB::table('ownermaster as o')
            ->join(
                'villagemaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )
            ->leftJoin(
                'districtmaster as d',
                'd.DistrictId',
                '=',
                'o.DistrictId'
            )
            ->leftJoin(
                'blockmaster as b',
                'b.BlockId',
                '=',
                'o.BlockId'
            )
            ->leftJoin(
                'flatmaster as f',
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

        $totalRegistrations = DB::table('registary')->count();

        $blankRegistryNumbers = DB::table('registary as r')
            ->where(function ($query) {
                $query
                    ->whereNull('r.RegistaryNumber')
                    ->orWhereRaw("TRIM(r.RegistaryNumber) = ''");
            })
            ->count();

        $uniqueRegistrations = DB::table('registary as r')
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

        $globalMatchedQuery = DB::table('registary as r')
            ->whereNotNull('r.SecondPartyMobile')
            ->whereRaw("TRIM(r.SecondPartyMobile) != ''")
            ->whereExists(function ($query) {
                $query
                    ->selectRaw('1')
                    ->from('ownermaster as o')
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

        $ownerMobileSubQuery = DB::table('ownermaster as om')
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
                'ownermaster as o',
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

        $ownerMobileSubQuery = DB::table('ownermaster as om')
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
                'ownermaster as o',
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

    public function registryDone(Request $request)
    {
        DB::disableQueryLog();

        /*
        |--------------------------------------------------------------------------
        | REGISTRY DONE - SAME LOGIC AS DASHBOARD
        |--------------------------------------------------------------------------
        |
        | Eligible beneficiary:
        |   IsApproved = 1
        |   IsPaid = 1
        |   IsAllotmentCancelled = 0
        |
        | OLD Registry:
        |   registary.flatid IS NULL
        |   registary.SecondPartyMobile = ownermaster.MobileNo
        |
        | NEW Registry:
        |   registary.flatid > 0
        |   registary.flatid = ownermaster.FlatId
        |
        | UNION keeps one OwnerId only once.
        |--------------------------------------------------------------------------
        */


        /*
        |--------------------------------------------------------------------------
        | OLD REGISTRY
        |--------------------------------------------------------------------------
        */

        $oldRegistryOwnerIds = DB::table('ownermaster as o')
            ->join('registary as r', function ($join) {
                $join->on(
                    'r.SecondPartyMobile',
                    '=',
                    'o.MobileNo'
                );
            })
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereRaw(
                'COALESCE(o.IsAllotmentCancelled, 0) = 0'
            )
            ->whereNull('r.flatid')
            ->whereNotNull('o.MobileNo')
            ->where('o.MobileNo', '<>', '');

        /*
        |--------------------------------------------------------------------------
        | NEW REGISTRY
        |--------------------------------------------------------------------------
        */

        $newRegistryOwnerIds = DB::table('ownermaster as o')
            ->join('registary as r', function ($join) {
                $join->on(
                    'r.flatid',
                    '=',
                    'o.FlatId'
                );
            })
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereRaw(
                'COALESCE(o.IsAllotmentCancelled, 0) = 0'
            )
            ->whereNotNull('r.flatid')
            ->where('r.flatid', '>', 0);


        /*
        |--------------------------------------------------------------------------
        | APPLY FILTERS BEFORE UNION
        |--------------------------------------------------------------------------
        */

        if ($request->filled('phase')) {

            $oldRegistryOwnerIds->where(
                'o.Phase',
                $request->phase
            );

            $newRegistryOwnerIds->where(
                'o.Phase',
                $request->phase
            );
        }

        if ($request->filled('district_id')) {

            $oldRegistryOwnerIds->where(
                'o.DistrictId',
                $request->district_id
            );

            $newRegistryOwnerIds->where(
                'o.DistrictId',
                $request->district_id
            );
        }

        if ($request->filled('block_id')) {

            $oldRegistryOwnerIds->where(
                'o.BlockId',
                $request->block_id
            );

            $newRegistryOwnerIds->where(
                'o.BlockId',
                $request->block_id
            );
        }

        if ($request->filled('village_id')) {

            $oldRegistryOwnerIds->where(
                'o.VillageId',
                $request->village_id
            );

            $newRegistryOwnerIds->where(
                'o.VillageId',
                $request->village_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | UNIQUE OWNER IDS
        |--------------------------------------------------------------------------
        */

        $oldRegistryOwnerIds = $oldRegistryOwnerIds
            ->select('o.OwnerId')
            ->distinct();

        $newRegistryOwnerIds = $newRegistryOwnerIds
            ->select('o.OwnerId')
            ->distinct();

        $registryDoneOwnerIds = $oldRegistryOwnerIds
            ->union($newRegistryOwnerIds);


        /*
        |--------------------------------------------------------------------------
        | MAIN LIST
        |--------------------------------------------------------------------------
        */

        $query = DB::query()
            ->fromSub(
                $registryDoneOwnerIds,
                'rd'
            )
            ->join(
                'ownermaster as o',
                'o.OwnerId',
                '=',
                'rd.OwnerId'
            )

            ->leftJoin(
                'villagemaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )

            ->leftJoin(
                'districtmaster as d',
                'd.DistrictId',
                '=',
                'o.DistrictId'
            )

            ->leftJoin(
                'blockmaster as b',
                'b.BlockId',
                '=',
                'o.BlockId'
            )

            ->leftJoin(
                'flatmaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )

            ->select([
                'o.OwnerId',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.RegistrationNo',
                'o.FlatId',
                'o.Phase',

                'v.VillageId',
                'v.VillageName',

                'd.DistrictId',
                'd.DistrictName',

                'b.BlockId',
                'b.BlockName',

                'f.FlatNo',
            ]);


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'o.OwnerName',
                    'like',
                    '%' . $search . '%'
                )

                    ->orWhere(
                        'o.FatherHusbandName',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'o.MobileNo',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'o.RegistrationNo',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'o.OwnerId',
                        'like',
                        '%' . $search . '%'
                    );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $registryDone = $query
            ->orderBy('o.OwnerId')
            ->paginate(25)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | PHASES
        |--------------------------------------------------------------------------
        */

        $phases = DB::table('villagemaster')
            ->where('plots', '>', 0)
            ->whereNotNull('Phase')
            ->where('Phase', '<>', '')
            ->distinct()
            ->orderBy('Phase')
            ->pluck('Phase');


        /*
        |--------------------------------------------------------------------------
        | DISTRICTS
        |--------------------------------------------------------------------------
        */

        $districtQuery = DB::table('districtmaster')
            ->select(
                'DistrictId',
                'DistrictName'
            )
            ->orderBy('DistrictName');

        if ($request->filled('phase')) {

            $districtQuery->whereExists(function ($q) use ($request) {

                $q->select(DB::raw(1))
                    ->from('ownermaster as o')
                    ->whereColumn(
                        'o.DistrictId',
                        'districtmaster.DistrictId'
                    )
                    ->where(
                        'o.Phase',
                        $request->phase
                    );
            });
        }

        $districts = $districtQuery->get();


        /*
        |--------------------------------------------------------------------------
        | BLOCKS
        |--------------------------------------------------------------------------
        */

        $blockQuery = DB::table('blockmaster')
            ->select(
                'BlockId',
                'BlockName'
            )
            ->orderBy('BlockName');

        if ($request->filled('district_id')) {

            $blockQuery->whereExists(function ($q) use ($request) {

                $q->select(DB::raw(1))
                    ->from('ownermaster as o')
                    ->whereColumn(
                        'o.BlockId',
                        'blockmaster.BlockId'
                    )
                    ->where(
                        'o.DistrictId',
                        $request->district_id
                    );

                if ($request->filled('phase')) {

                    $q->where(
                        'o.Phase',
                        $request->phase
                    );
                }
            });
        }

        $blocks = $blockQuery->get();


        /*
        |--------------------------------------------------------------------------
        | VILLAGES
        |--------------------------------------------------------------------------
        */

        $villageQuery = DB::table('villagemaster')
            ->select(
                'VillageId',
                'VillageName'
            )
            ->where('plots', '>', 0)
            ->orderBy('VillageName');

        if ($request->filled('phase')) {

            $villageQuery->where(
                'Phase',
                $request->phase
            );
        }

        if ($request->filled('district_id')) {

            $villageQuery->where(
                'DistrictId',
                $request->district_id
            );
        }

        if ($request->filled('block_id')) {

            $villageQuery->where(
                'BlockId',
                $request->block_id
            );
        }

        $villages = $villageQuery->get();


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'mmgay.super-admin.registry-done.index',
            compact(
                'registryDone',
                'phases',
                'districts',
                'blocks',
                'villages'
            )
        );
    }


    /**
     * Registry Done dependent dropdowns.
     */
    public function registryDoneOptions(Request $request)
    {
        DB::disableQueryLog();

        $type = $request->get('type');

        if ($type === 'districts') {
            return response()->json(
                DB::table('districtmaster as d')
                    ->whereExists(function ($q) use ($request) {
                        $q->selectRaw('1')
                            ->from('ownermaster as o')
                            ->whereColumn('o.DistrictId', 'd.DistrictId')
                            ->when(
                                $request->filled('phase'),
                                fn($x) => $x->where('o.Phase', $request->phase)
                            );
                    })
                    ->select([
                        'd.DistrictId as id',
                        'd.DistrictName as name',
                    ])
                    ->orderBy('d.DistrictName')
                    ->get()
            );
        }

        if ($type === 'blocks') {
            return response()->json(
                DB::table('blockmaster as b')
                    ->where('b.DistrictId', (int) $request->district_id)
                    ->whereExists(function ($q) use ($request) {
                        $q->selectRaw('1')
                            ->from('ownermaster as o')
                            ->whereColumn('o.BlockId', 'b.BlockId')
                            ->where('o.DistrictId', (int) $request->district_id)
                            ->when(
                                $request->filled('phase'),
                                fn($x) => $x->where('o.Phase', $request->phase)
                            );
                    })
                    ->select([
                        'b.BlockId as id',
                        'b.BlockName as name',
                    ])
                    ->orderBy('b.BlockName')
                    ->get()
            );
        }

        if ($type === 'villages') {
            return response()->json(
                DB::table('villagemaster as v')
                    ->where('v.BlockId', (int) $request->block_id)
                    ->whereExists(function ($q) use ($request) {
                        $q->selectRaw('1')
                            ->from('ownermaster as o')
                            ->whereColumn('o.VillageId', 'v.VillageId')
                            ->where('o.BlockId', (int) $request->block_id)
                            ->when(
                                $request->filled('district_id'),
                                fn($x) => $x->where(
                                    'o.DistrictId',
                                    (int) $request->district_id
                                )
                            )
                            ->when(
                                $request->filled('phase'),
                                fn($x) => $x->where(
                                    'o.Phase',
                                    $request->phase
                                )
                            );
                    })
                    ->select([
                        'v.VillageId as id',
                        'v.VillageName as name',
                    ])
                    ->orderBy('v.VillageName')
                    ->get()
            );
        }

        return response()->json([]);
    }


    /**
     * Registry Details.
     */
    public function registryDoneShow($secureId)
    {
        $owner = DB::table('ownermaster as o')
            ->join('villagemaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->leftJoin('districtmaster as d', 'd.DistrictId', '=', 'o.DistrictId')
            ->leftJoin('blockmaster as b', 'b.BlockId', '=', 'o.BlockId')
            ->leftJoin('flatmaster as f', 'f.FlatId', '=', 'o.FlatId')
            ->whereRaw(
                "SHA2(CONCAT(o.OwnerId, '-', o.MobileNo), 256) = ?",
                [$secureId]
            )
            ->select([
                'o.*',
                'f.FlatNo',
                'v.VillageName',
                'v.map_pdf as PdfFile',
                'd.DistrictName',
                'b.BlockName',
            ])
            ->first();

        abort_if(!$owner, 404);

        /*
         * Prefer NEW registry by FlatId.
         * Otherwise use OLD registry by mobile.
         */
        $registry = null;

        if (!empty($owner->FlatId)) {
            $registry = DB::table('registary')
                ->where('flatid', '>', 0)
                ->where('flatid', $owner->FlatId)
                ->orderByDesc('RegistaryDate')
                ->orderByDesc('id')
                ->first();
        }

        if (!$registry && !empty($owner->MobileNo)) {
            $registry = DB::table('registary')
                ->whereNull('flatid')
                ->where('SecondPartyMobile', $owner->MobileNo)
                ->orderByDesc('RegistaryDate')
                ->orderByDesc('id')
                ->first();
        }

        abort_if(!$registry, 404);

        return view(
            'mmgay.super-admin.registry-done.show',
            [
                'owner' => $owner,
                'registry' => $registry,
                'secureId' => $secureId,
            ]
        );
    }


    /**
     * Registry Print.
     */
    public function registryDonePrint($secureId)
    {
        $owner = DB::table('ownermaster as o')
            ->join('villagemaster as v', 'v.VillageId', '=', 'o.VillageId')
            ->leftJoin('districtmaster as d', 'd.DistrictId', '=', 'o.DistrictId')
            ->leftJoin('blockmaster as b', 'b.BlockId', '=', 'o.BlockId')
            ->leftJoin('flatmaster as f', 'f.FlatId', '=', 'o.FlatId')
            ->whereRaw(
                "SHA2(CONCAT(o.OwnerId, '-', o.MobileNo), 256) = ?",
                [$secureId]
            )
            ->select([
                'o.*',
                'f.FlatNo',
                'v.VillageName',
                'v.map_pdf as PdfFile',
                'd.DistrictName',
                'b.BlockName',
            ])
            ->first();

        abort_if(!$owner, 404);

        $registry = null;

        if (!empty($owner->FlatId)) {
            $registry = DB::table('registary')
                ->where('flatid', '>', 0)
                ->where('flatid', $owner->FlatId)
                ->orderByDesc('RegistaryDate')
                ->orderByDesc('id')
                ->first();
        }

        if (!$registry && !empty($owner->MobileNo)) {
            $registry = DB::table('registary')
                ->whereNull('flatid')
                ->where('SecondPartyMobile', $owner->MobileNo)
                ->orderByDesc('RegistaryDate')
                ->orderByDesc('id')
                ->first();
        }

        abort_if(!$registry, 404);

        return view(
            'mmgay.super-admin.registry-done.print',
            [
                'owner' => $owner,
                'registry' => $registry,
                'secureId' => $secureId,
            ]
        );
    }

    public function registryYetDone(Request $request)
    {
        DB::disableQueryLog();

        $registryYetDone = $this->registryYetDoneQuery($request)
            ->orderBy('o.OwnerId')
            ->paginate(25)
            ->withQueryString();

        $phases = DB::table('villagemaster')
            ->where('plots', '>', 0)
            ->whereNotNull('phase')
            ->where('phase', '<>', '')
            ->distinct()
            ->orderBy('phase')
            ->pluck('phase');

        $districts = DB::table('districtmaster')
            ->select([
                'DistrictId',
                'DistrictName',
            ])
            ->orderBy('DistrictName')
            ->get();

        $blocks = DB::table('blockmaster')
            ->select([
                'BlockId',
                'BlockName',
            ])
            ->orderBy('BlockName')
            ->get();

        $villages = DB::table('villagemaster')
            ->select([
                'VillageId',
                'VillageName',
            ])
            ->orderBy('VillageName')
            ->get();

        return view(
            'mmgay.super-admin.registry-yet-done.index',
            compact(
                'registryYetDone',
                'phases',
                'districts',
                'blocks',
                'villages'
            )
        );
    }

    public function registryYetDoneOptions(Request $request)
    {
        $type = $request->get('type');

        if ($type === 'districts') {

            return response()->json(
                DB::table('districtmaster')
                    ->select(
                        'DistrictId as id',
                        'DistrictName as name'
                    )
                    ->when(
                        $request->filled('phase'),
                        function ($q) use ($request) {
                            $q->whereExists(function ($sub) use ($request) {
                                $sub->select(DB::raw(1))
                                    ->from('ownermaster as o')
                                    ->whereColumn(
                                        'o.DistrictId',
                                        'districtmaster.DistrictId'
                                    )
                                    ->where(
                                        'o.Phase',
                                        $request->phase
                                    );
                            });
                        }
                    )
                    ->orderBy('DistrictName')
                    ->get()
            );
        }


        if ($type === 'blocks') {

            return response()->json(
                DB::table('blockmaster')
                    ->select(
                        'BlockId as id',
                        'BlockName as name'
                    )
                    ->when(
                        $request->filled('district_id'),
                        function ($q) use ($request) {
                            $q->whereExists(function ($sub) use ($request) {

                                $sub->select(DB::raw(1))
                                    ->from('ownermaster as o')
                                    ->whereColumn(
                                        'o.BlockId',
                                        'blockmaster.BlockId'
                                    )
                                    ->where(
                                        'o.DistrictId',
                                        $request->district_id
                                    );

                                if ($request->filled('phase')) {
                                    $sub->where(
                                        'o.Phase',
                                        $request->phase
                                    );
                                }
                            });
                        }
                    )
                    ->orderBy('BlockName')
                    ->get()
            );
        }


        if ($type === 'villages') {

            return response()->json(
                DB::table('villagemaster')
                    ->select(
                        'VillageId as id',
                        'VillageName as name'
                    )
                    ->where('plots', '>', 0)
                    ->when(
                        $request->filled('phase'),
                        fn($q) => $q->where(
                            'Phase',
                            $request->phase
                        )
                    )
                    ->when(
                        $request->filled('district_id'),
                        fn($q) => $q->where(
                            'DistrictId',
                            $request->district_id
                        )
                    )
                    ->when(
                        $request->filled('block_id'),
                        fn($q) => $q->where(
                            'BlockId',
                            $request->block_id
                        )
                    )
                    ->orderBy('VillageName')
                    ->get()
            );
        }


        return response()->json([]);
    }

    public function registryYetDonePrint(Request $request)
    {
        DB::disableQueryLog();

        $registryYetDone = $this->registryYetDoneQuery($request)
            ->orderBy('o.OwnerId')
            ->get();

        return view(
            'mmgay.super-admin.registry-yet-done.print',
            compact('registryYetDone')
        );
    }

    public function registryYetDoneCsv(Request $request)
    {
        DB::disableQueryLog();

        $filename = 'Registry_Yet_To_Be_Done_' .
            now()->format('d-m-Y_H-i-s') .
            '.csv';

        return response()->streamDownload(
            function () use ($request) {

                $handle = fopen('php://output', 'w');

                /*
                |--------------------------------------------------------------------------
                | UTF-8 BOM
                |--------------------------------------------------------------------------
                */
                fwrite(
                    $handle,
                    "\xEF\xBB\xBF"
                );

                /*
                |--------------------------------------------------------------------------
                | Header
                |--------------------------------------------------------------------------
                */
                fputcsv($handle, [
                    'Sr. No.',
                    'Application No.',
                    'Owner ID',
                    'Applicant Name',
                    'Father / Husband',
                    'Mobile',
                    'Village',
                    'Block',
                    'District',
                    'Phase',
                    'Flat No.',
                    'Flat ID',
                    'Status',
                ]);

                $srNo = 0;

                $query = $this->registryYetDoneQuery($request)
                    ->orderBy('o.OwnerId');

                /*
                |--------------------------------------------------------------------------
                | Chunk
                |--------------------------------------------------------------------------
                */

                $query->chunkById(
                    1000,
                    function ($rows) use ($handle, &$srNo) {

                        foreach ($rows as $row) {

                            $srNo++;

                            fputcsv($handle, [
                                $srNo,
                                $row->RegistrationNo,
                                $row->OwnerId,
                                $row->OwnerName,
                                $row->FatherHusbandName,
                                $row->MobileNo,
                                $row->VillageName,
                                $row->BlockName,
                                $row->DistrictName,
                                $row->Phase,
                                $row->FlatNo,
                                $row->FlatId,
                                'Registry Pending',
                            ]);
                        }

                    },
                    'o.OwnerId',
                    'OwnerId'
                );

                fclose($handle);
            },
            $filename,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }

    private function registryYetDoneQuery(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Registry matched Owner IDs
        |--------------------------------------------------------------------------
        | OLD Registry:
        |   flatid IS NULL
        |   SecondPartyMobile = Owner.MobileNo
        |
        | NEW Registry:
        |   flatid > 0
        |   flatid = Owner.FlatId
        |--------------------------------------------------------------------------
        */

        $oldRegistryOwners = DB::table('registary as r')
            ->join('ownermaster as o', function ($join) {
                $join->on(
                    'r.SecondPartyMobile',
                    '=',
                    'o.MobileNo'
                );
            })
            ->whereNull('r.flatid')
            ->whereNotNull('r.SecondPartyMobile')
            ->where('r.SecondPartyMobile', '<>', '')
            ->select('o.OwnerId');

        $newRegistryOwners = DB::table('registary as r')
            ->join('ownermaster as o', function ($join) {
                $join->on(
                    'r.flatid',
                    '=',
                    'o.FlatId'
                );
            })
            ->whereNotNull('r.flatid')
            ->where('r.flatid', '>', 0)
            ->select('o.OwnerId');

        $registryMatchedOwners = $oldRegistryOwners
            ->union($newRegistryOwners);


        /*
        |--------------------------------------------------------------------------
        | Main Yet-To-Be-Done Query
        |--------------------------------------------------------------------------
        */

        $query = DB::table('ownermaster as o')

            ->leftJoin(
                'villagemaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )

            ->leftJoin(
                'districtmaster as d',
                'd.DistrictId',
                '=',
                'o.DistrictId'
            )

            ->leftJoin(
                'blockmaster as b',
                'b.BlockId',
                '=',
                'o.BlockId'
            )

            ->leftJoin(
                'flatmaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )

            /*
            |--------------------------------------------------------------------------
            | Exclude Registry Done
            |--------------------------------------------------------------------------
            */
            ->leftJoinSub(
                $registryMatchedOwners,
                'ro',
                function ($join) {
                    $join->on(
                        'ro.OwnerId',
                        '=',
                        'o.OwnerId'
                    );
                }
            )

            ->whereNull('ro.OwnerId')

            /*
            |--------------------------------------------------------------------------
            | Eligible Applicants
            |--------------------------------------------------------------------------
            */
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereRaw(
                'COALESCE(o.IsRejected, 0) = 0'
            )
            ->whereRaw(
                'COALESCE(o.IsAllotmentCancelled, 0) = 0'
            )

            /*
            |--------------------------------------------------------------------------
            | Select
            |--------------------------------------------------------------------------
            */
            ->select([
                'o.OwnerId',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.RegistrationNo',

                'o.FlatId',
                'f.FlatNo',

                'o.Phase',

                'o.VillageId',
                'v.VillageName',

                'o.BlockId',
                'b.BlockName',

                'o.DistrictId',
                'd.DistrictName',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        $query->when(
            $request->filled('phase'),
            function ($q) use ($request) {
                $q->where(
                    'o.Phase',
                    $request->phase
                );
            }
        );

        $query->when(
            $request->filled('district_id'),
            function ($q) use ($request) {
                $q->where(
                    'o.DistrictId',
                    $request->district_id
                );
            }
        );

        $query->when(
            $request->filled('block_id'),
            function ($q) use ($request) {
                $q->where(
                    'o.BlockId',
                    $request->block_id
                );
            }
        );

        $query->when(
            $request->filled('village_id'),
            function ($q) use ($request) {
                $q->where(
                    'o.VillageId',
                    $request->village_id
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $query->when(
            $request->filled('search'),
            function ($q) use ($request) {

                $search = trim($request->search);

                $q->where(function ($searchQuery) use ($search) {

                    $searchQuery
                        ->where('o.OwnerName', 'LIKE', "%{$search}%")
                        ->orWhere(
                            'o.FatherHusbandName',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'o.MobileNo',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'o.RegistrationNo',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'o.OwnerId',
                            'LIKE',
                            "%{$search}%"
                        );
                });
            }
        );

        return $query;
    }



}