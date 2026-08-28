<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\EmOffice;
use App\Models\District;
use App\Models\City;
use App\Models\Sector;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PropertyExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PropertiesExport;
use Symfony\Component\HttpFoundation\StreamedResponse;
use RuntimeException;

class PropertyManagementController extends Controller
{
    private const LIST_PAGE_SIZE = 50;
    private const EXPORT_CHUNK_SIZE = 1000;
    private const PRINT_CHUNK_SIZE = 500;

    /**
     * Indexed receipt total for one asset.
     *
     * This avoids materialising and grouping the complete receipt table on
     * every filtered listing request. The existing composite index beginning
     * with cash_receipt_details.asset_number is used for every lookup.
     */
    private function receiptTotalSql(string $assetColumn = 'pad.AssetId'): string
    {
        return "COALESCE((
            SELECT SUM(cr_sum.total_paid_amount)
            FROM cash_receipt_details AS cr_sum
            WHERE cr_sum.asset_number = {$assetColumn}
              AND cr_sum.IsDeleted = 0
              AND cr_sum.IsActive = 1
        ), 0)";
    }

    public function dashboard(Request $request)
    {
        $districtId = $request->filled('district_id')
            ? $request->integer('district_id')
            : null;

        $cityId = $request->filled('city_id')
            ? $request->integer('city_id')
            : null;

        $sectorId = $request->filled('sector_id')
            ? $request->integer('sector_id')
            : null;

        $dashboardCacheKey = 'mmsay:department-dashboard:' . md5(
            json_encode([
                'district_id' => $districtId,
                'city_id' => $cityId,
                'sector_id' => $sectorId,
            ])
        );

        // Cache is separated for every filter combination.
        $dashboardCacheSeconds = 600;

        /*
        |--------------------------------------------------------------------------
        | Reusable location filters
        |--------------------------------------------------------------------------
        */

        $applyLocationFilters = function ($query, string $alias = '') use ($districtId, $cityId, $sectorId) {
            $prefix = $alias !== '' ? $alias . '.' : '';

            return $query
                ->when($districtId, function ($query) use ($prefix, $districtId) {
                    $query->where(
                        $prefix . 'DistrictId',
                        $districtId
                    );
                })
                ->when($cityId, function ($query) use ($prefix, $cityId) {
                    $query->where(
                        $prefix . 'CityId',
                        $cityId
                    );
                })
                ->when($sectorId, function ($query) use ($prefix, $sectorId) {
                    $query->where(
                        $prefix . 'SectorId',
                        $sectorId
                    );
                });
        };

        /*
        |--------------------------------------------------------------------------
        | Filter dropdown data
        |--------------------------------------------------------------------------
        */

        $districts = DB::table('districts')
            ->select('DistrictId', 'DistrictName')
            ->where('Is_Deleted', 0)
            ->where('Is_Active', 1)
            ->orderBy('DistrictName')
            ->get();

        $cities = collect();

        if ($districtId) {
            $cities = DB::table('cities')
                ->select('CityId', 'CityName')
                ->where('DistrictId', $districtId)
                ->where('Is_Deleted', 0)
                ->where('Is_Active', 1)
                ->orderBy('CityName')
                ->get();
        }

        $sectors = collect();

        if ($cityId) {
            $sectors = DB::table('city_sector_associations as csa')
                ->join(
                    'sectors as s',
                    's.SectorId',
                    '=',
                    'csa.SectorId'
                )
                ->select(
                    's.SectorId',
                    's.SectorName'
                )
                ->where('csa.CityId', $cityId)
                ->where('csa.Is_Deleted', 0)
                ->where('csa.Is_Active', 1)
                ->where('s.Is_Deleted', 0)
                ->where('s.Is_Active', 1)
                ->distinct()
                ->orderBy('s.SectorName')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Total registrations (old registration data)
        |--------------------------------------------------------------------------
        | The old registration table stores location names instead of IDs.
        | Resolve the selected dashboard IDs once and filter the old table by
        | districtName, btName and wvName without adding any join to its count.
        */

        $selectedDistrictName = $districtId
            ? optional(
                $districts->firstWhere('DistrictId', $districtId)
            )->DistrictName
            : null;

        $selectedCityName = $cityId
            ? optional(
                $cities->firstWhere('CityId', $cityId)
            )->CityName
            : null;

        $selectedSectorName = $sectorId
            ? optional(
                $sectors->firstWhere('SectorId', $sectorId)
            )->SectorName
            : null;

        $oldRegistrationCountQuery = DB::table(
            'hfa.mmsay_old_registration_data'
        )
            ->when(
                $selectedDistrictName,
                fn($query) => $query->where(
                    'districtName',
                    $selectedDistrictName
                )
            )
            ->when(
                $selectedCityName,
                fn($query) => $query->where(
                    'btName',
                    $selectedCityName
                )
            )
            ->when(
                $selectedSectorName,
                fn($query) => $query->where(
                    'wvName',
                    $selectedSectorName
                )
            );

        $totalApplications = (clone $oldRegistrationCountQuery)->count('id');

        /*
        |--------------------------------------------------------------------------
        | Total revenue
        |--------------------------------------------------------------------------
        */

        $totalRevenueQuery = DB::table('cash_receipt_details')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1);

        $applyLocationFilters($totalRevenueQuery);

        $totalRevenue = (float) (clone $totalRevenueQuery)->sum('total_paid_amount');

        /*
        |--------------------------------------------------------------------------
        | Total purchasers
        |--------------------------------------------------------------------------
        */

        $totalPurchasersQuery = DB::table(
            'property_private_purchasers'
        )
            ->where('IsDeleted', 0)
            ->where('IsActive', 1);

        $applyLocationFilters($totalPurchasersQuery);

        $totalPurchasers = (clone $totalPurchasersQuery)->count(
            'PrivatePurchaserId'
        );

        /*
        |--------------------------------------------------------------------------
        | Optimized asset payment aggregation
        |--------------------------------------------------------------------------
        */

        $receiptSumsQuery = DB::table('cash_receipt_details')
            ->select('asset_number', DB::raw('SUM(total_paid_amount) as total_receipts'))
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->groupBy('asset_number');

        $assetPaymentsQuery = DB::table('property_auction_detail as pad')
            ->leftJoinSub($receiptSumsQuery, 'cr_sum', 'cr_sum.asset_number', '=', 'pad.AssetId')
            ->selectRaw("
            pad.AssetId,
            pad.FlatCost,
            pad.ReceivedAmount,
            (
                COALESCE(pad.ReceivedAmount, 0)
                + COALESCE(MAX(cr_sum.total_receipts), 0)
            ) AS total_received
        ")
            ->where('pad.IsDeleted', 0)
            ->where('pad.IsActive', 1);

        $applyLocationFilters($assetPaymentsQuery, 'pad');

        $assetPaymentsQuery->groupBy(
            'pad.AssetId',
            'pad.FlatCost',
            'pad.ReceivedAmount'
        );

        /*
        |--------------------------------------------------------------------------
        | Overall allotted/payment statistics (all 15,256 allotted assets)
        |--------------------------------------------------------------------------
        */

        $dashboardPaymentStats = DB::query()
            ->fromSub(clone $assetPaymentsQuery, 'payments')
            ->selectRaw('
            COUNT(*) AS total_candidates,
            SUM(
                CASE
                    WHEN payments.total_received
                        >= COALESCE(payments.FlatCost, 0)
                    THEN 1
                    ELSE 0
                END
            ) AS total_paid_properties,

            SUM(
                CASE
                    WHEN payments.total_received
                        < COALESCE(payments.FlatCost, 0)
                    THEN 1
                    ELSE 0
                END
            ) AS pending_properties
        ')
            ->first();

        $totalAllottedCandidates = (int) (
            $dashboardPaymentStats->total_candidates ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | Physical Possession payment eligibility
        |--------------------------------------------------------------------------
        | IMPORTANT: this calculation starts from the 11,984 beneficiaries present
        | in mmsay_eligible_beneficiaries. It does not use all 15,256 allottees.
        | The indexed generated integer column avoids VARCHAR-to-INT conversion.
        */

        /*
|--------------------------------------------------------------------------
| EMI and possession statistics for 11,984 verified beneficiaries
|--------------------------------------------------------------------------
*/

        $verifiedAssetPaymentsQuery = DB::table(
            'mmsay_eligible_beneficiaries as meb'
        )
            ->join('property_private_purchasers as ppp', function ($join) {
                $join->on(
                    'ppp.ApplicationNo',
                    '=',
                    'meb.application_number'
                )
                    ->where('ppp.IsDeleted', 0);
            })
            ->join('property_auction_detail as pad', function ($join) {
                $join->on('pad.AssetId', '=', 'ppp.Flat_Id')
                    ->on(
                        'pad.PurchaserID',
                        '=',
                        'ppp.PrivatePurchaserId'
                    )
                    ->where('pad.IsDeleted', 0)
                    ->where('pad.IsActive', 1);
            })
            ->leftJoinSub(
                clone $receiptSumsQuery,
                'verified_cr_sum',
                'verified_cr_sum.asset_number',
                '=',
                'pad.AssetId'
            )
            ->selectRaw('
        pad.PropertyAuctionId,
        pad.AssetId,
        pad.FlatCost,
        (
            COALESCE(pad.ReceivedAmount, 0)
            + COALESCE(MAX(verified_cr_sum.total_receipts), 0)
        ) AS total_received
    ');

        $applyLocationFilters(
            $verifiedAssetPaymentsQuery,
            'ppp'
        );

        $verifiedAssetPaymentsQuery->groupBy(
            'pad.PropertyAuctionId',
            'pad.AssetId',
            'pad.FlatCost',
            'pad.ReceivedAmount'
        );

        /*
        |--------------------------------------------------------------------------
        | Calculate EMI and possession counts in one query
        |--------------------------------------------------------------------------
        */

        $verifiedPaymentStats = DB::query()
            ->fromSub(
                clone $verifiedAssetPaymentsQuery,
                'verified_payments'
            )
            ->selectRaw('
        COUNT(*) AS total_candidates,

        SUM(
            CASE
                WHEN verified_payments.total_received
                     >= COALESCE(verified_payments.FlatCost, 0)
                THEN 1
                ELSE 0
            END
        ) AS total_paid_properties,

        SUM(
            CASE
                WHEN verified_payments.total_received
                     < COALESCE(verified_payments.FlatCost, 0)
                THEN 1
                ELSE 0
            END
        ) AS pending_properties,

        SUM(
            CASE
                WHEN verified_payments.total_received >= 60000
                THEN 1
                ELSE 0
            END
        ) AS eligible_candidates,

        SUM(
            CASE
                WHEN verified_payments.total_received < 60000
                THEN 1
                ELSE 0
            END
        ) AS not_eligible_candidates
    ')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | EMI Payment Status — only verified 11,984 beneficiaries
        |--------------------------------------------------------------------------
        */

        $paymentStats = (object) [
            'total_records' => (int) (
                $verifiedPaymentStats->total_candidates ?? 0
            ),

            'total_paid_properties' => (int) (
                $verifiedPaymentStats->total_paid_properties ?? 0
            ),

            'pending_properties' => (int) (
                $verifiedPaymentStats->pending_properties ?? 0
            ),
        ];

        /*
        |--------------------------------------------------------------------------
        | Physical possession ₹60,000 eligibility — same beneficiary base
        |--------------------------------------------------------------------------
        */

        $totalPhysicalPossessionCandidates = (int) (
            $verifiedPaymentStats->total_candidates ?? 0
        );

        $eligiblePhysicalPossession = (int) (
            $verifiedPaymentStats->eligible_candidates ?? 0
        );

        $notEligiblePhysicalPossession = (int) (
            $verifiedPaymentStats->not_eligible_candidates ?? 0
        );

        $totalPhysicalPossessionCandidates = (int) (
            $physicalPossessionStats->total_candidates ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | Total allotted units remains based on all allotted properties.
        | Saves one heavy database count query.
        |--------------------------------------------------------------------------
        */
        $allottedUnits = $totalAllottedCandidates;

        /*
        |--------------------------------------------------------------------------
        | Keep existing Blade paymentStats variable compatible
        |--------------------------------------------------------------------------
        */

        // $paymentStats = (object) [
        //     'total_records' => $totalAllottedCandidates,

        //     'total_paid_properties' => (int) (
        //         $dashboardPaymentStats->total_paid_properties ?? 0
        //     ),

        //     'pending_properties' => (int) (
        //         $dashboardPaymentStats->pending_properties ?? 0
        //     ),
        // ];

        /*
        |--------------------------------------------------------------------------
        | Latest physical possession applications
        |--------------------------------------------------------------------------
        */

        $latestPhysicalApplications = $this->eligiblePossessionSelect(
            $this->eligiblePossessionQuery($request)
        )
            ->whereNotNull('ppa.id')
            ->whereNotNull('ppa.citizen_visit_date')
            ->orderByDesc('ppa.id')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Physical verification statistics — optimized
        |--------------------------------------------------------------------------
        */

        $verificationBaseQuery = DB::table(
            'property_private_purchasers as ppp'
        )
            ->where('ppp.IsDeleted', 0)
            ->where('ppp.phase', 1)
            ->where('ppp.property_type', 'plot');

        $applyLocationFilters($verificationBaseQuery, 'ppp');

        /*
         * Total applicants को इसी table से count करें।
         */
        $totalVerificationAllottees = (clone $verificationBaseQuery)
            ->count('ppp.PrivatePurchaserId');

        /*
         * Eligible applicants के लिए direct indexed join।
         */
        $eligibleAllottees = (clone $verificationBaseQuery)
            ->join(
                'mmsay_eligible_beneficiaries as meb',
                'meb.application_number',
                '=',
                'ppp.ApplicationNo'
            )
            ->count('ppp.PrivatePurchaserId');

        /*
         * Not Eligible = Total - Eligible
         */
        $notEligibleAllottees = max(
            $totalVerificationAllottees - $eligibleAllottees,
            0
        );


        /*
        |--------------------------------------------------------------------------
        | Dashboard view
        |--------------------------------------------------------------------------
        */

        return view('mmsay.departmentDashboard', compact(
            'districts',
            'cities',
            'sectors',
            'districtId',
            'cityId',
            'sectorId',
            'totalApplications',
            'allottedUnits',
            'totalRevenue',
            'totalPurchasers',
            'paymentStats',
            'eligiblePhysicalPossession',
            'notEligiblePhysicalPossession',
            'totalPhysicalPossessionCandidates',
            'latestPhysicalApplications',
            'eligibleAllottees',
            'notEligibleAllottees'
        ));
    }

    private function verificationAllotteesQuery(
        Request $request,
        string $eligibility
    ) {
        // Dono URL formats handle honge
        $eligibility = str_replace('_', '-', strtolower($eligibility));

        abort_unless(
            in_array($eligibility, ['eligible', 'not-eligible'], true),
            404
        );

        $query = DB::table('property_private_purchasers as ppp')
            ->join('property_registration as pr', function ($join) {
                $join->on('pr.AssetId', '=', 'ppp.Flat_Id')
                    ->where('pr.IsDeleted', 0);
            })
            ->leftJoin('mmsay_eligible_beneficiaries as meb', function ($join) {
                $join->on(
                    'meb.application_number',
                    '=',
                    'ppp.ApplicationNo'
                );
            })
            ->leftJoin('districts as d', 'd.DistrictId', '=', 'ppp.DistrictId')
            ->leftJoin('cities as c', 'c.CityId', '=', 'ppp.CityId')
            ->leftJoin('sectors as s', 's.SectorId', '=', 'ppp.SectorId')
            ->where('ppp.IsDeleted', 0);

        if ($eligibility === 'eligible') {
            $query->whereNotNull('meb.id');
        } else {
            $query->whereNull('meb.id');
        }

        // Location filters
        $query
            ->when(
                $request->filled('district_id'),
                fn($builder) => $builder->where(
                    'ppp.DistrictId',
                    $request->integer('district_id')
                )
            )
            ->when(
                $request->filled('city_id'),
                fn($builder) => $builder->where(
                    'ppp.CityId',
                    $request->integer('city_id')
                )
            )
            ->when(
                $request->filled('sector_id'),
                fn($builder) => $builder->where(
                    'ppp.SectorId',
                    $request->integer('sector_id')
                )
            );

        // Search
        $search = trim((string) $request->input('search'));

        if ($search !== '') {
            $escapedSearch = addcslashes($search, '%_\\');
            $like = "%{$escapedSearch}%";

            $query->where(function ($searchQuery) use ($search, $like) {
                // Exact numeric search index use kar sakta hai
                if (ctype_digit($search)) {
                    $searchQuery
                        ->where('ppp.ApplicationNo', $search)
                        ->orWhere('ppp.MobileNo', $search)
                        ->orWhere('pr.AssetId', $search);
                } else {
                    $searchQuery
                        ->where('ppp.PrivatePurchaserName', 'like', $like)
                        ->orWhere('pr.AssetName', 'like', $like)
                        ->orWhere('ppp.PPPId', 'like', $like)
                        ->orWhere('ppp.MemberID', 'like', $like);
                }
            });
        }

        return $query->select([
            'ppp.PrivatePurchaserId as purchaser_id',
            'ppp.Flat_Id as asset_id',
            'ppp.ApplicationNo as application_number',
            'ppp.PrivatePurchaserName as applicant_name',
            'ppp.PurchaserFatherName as father_name',
            'ppp.MobileNo as mobile',
            'ppp.PPPId as ppp_id',
            'ppp.MemberID as member_id',
            'ppp.CasteCategoryName as caste_category',
            'ppp.MaritalStatus as marital_status',
            'ppp.Address as address',

            'pr.AssetName as asset_name',
            'pr.AssetSize as asset_size',
            'pr.Unit as asset_unit',

            'ppp.DistrictId as district_id',
            'ppp.CityId as city_id',
            'ppp.SectorId as sector_id',

            'd.DistrictName as district_name',
            'c.CityName as city_name',
            's.SectorName as sector_name',

            // Physical-verification report fields. These remain NULL for
            // not-eligible rows because the beneficiary record is absent.
            'meb.id as eligibility_record_id',
            'meb.secure_id as eligibility_secure_id',
            'meb.physical_verification',
            'meb.status_reason',
            'meb.remarks',
            'meb.own_residence',
            'meb.pmay_benefit',
            'meb.category as verification_category',
        ]);
    }

    public function verificationAllottees(
        Request $request,
        string $eligibility
    ) {
        $eligibility = str_replace('_', '-', strtolower($eligibility));

        abort_unless(
            in_array($eligibility, ['eligible', 'not-eligible'], true),
            404
        );

        $districtId = $request->integer('district_id') ?: null;
        $cityId = $request->integer('city_id') ?: null;
        $sectorId = $request->integer('sector_id') ?: null;
        $search = trim((string) $request->input('search'));

        $districts = DB::table('districts')
            ->select('DistrictId', 'DistrictName')
            ->where('Is_Deleted', 0)
            ->where('Is_Active', 1)
            ->orderBy('DistrictName')
            ->get();

        $cities = $districtId
            ? DB::table('cities')
                ->select('CityId', 'CityName')
                ->where('DistrictId', $districtId)
                ->where('Is_Deleted', 0)
                ->where('Is_Active', 1)
                ->orderBy('CityName')
                ->get()
            : collect();

        $sectors = $cityId
            ? DB::table('city_sector_associations as csa')
                ->join('sectors as s', 's.SectorId', '=', 'csa.SectorId')
                ->select('s.SectorId', 's.SectorName')
                ->where('csa.CityId', $cityId)
                ->where('csa.Is_Deleted', 0)
                ->where('csa.Is_Active', 1)
                ->where('s.Is_Deleted', 0)
                ->where('s.Is_Active', 1)
                ->distinct()
                ->orderBy('s.SectorName')
                ->get()
            : collect();

        $applications = $this
            ->verificationAllotteesQuery($request, $eligibility)
            ->orderByDesc('ppp.PrivatePurchaserId')
            ->paginate(50)
            ->withQueryString();

        $pageTitle = $eligibility === 'eligible'
            ? 'Eligible Allottees'
            : 'Not Eligible Allottees';

        $pageDescription = $eligibility === 'eligible'
            ? 'Applicants found in the eligible beneficiaries report'
            : 'Applicants not found in the eligible beneficiaries report';

        return view('mmsay.verificationAllottees', compact(
            'applications',
            'eligibility',
            'pageTitle',
            'pageDescription',
            'districts',
            'cities',
            'sectors',
            'districtId',
            'cityId',
            'sectorId',
            'search'
        ));
    }

    public function verificationAllotteesCsv(
        Request $request,
        string $eligibility
    ) {
        $eligibility = str_replace(
            '_',
            '-',
            strtolower($eligibility)
        );

        abort_unless(
            in_array(
                $eligibility,
                ['eligible', 'not-eligible'],
                true
            ),
            404
        );

        /*
         * Listing ka selected order.
         */
        $sortOrder = strtolower(
            (string) $request->input('sort_order', 'desc')
        );

        $sortOrder = in_array(
            $sortOrder,
            ['asc', 'desc'],
            true
        ) ? $sortOrder : 'desc';

        $fileName = $eligibility
            . '-allottees-'
            . $sortOrder
            . '-'
            . now()->format('Ymd-His')
            . '.csv';

        return response()->streamDownload(
            function () use ($request, $eligibility, $sortOrder) {
                $output = fopen('php://output', 'w');

                if ($output === false) {
                    throw new RuntimeException(
                        'Unable to open CSV output stream.'
                    );
                }

                // Excel UTF-8 support
                fwrite($output, "\xEF\xBB\xBF");

                fputcsv($output, [
                    'S.No.',
                    'Application No.',
                    'Applicant',
                    'Father/Husband Name',
                    'Mobile',
                    'Asset ID',
                    'Property',
                    'Size',
                    'District',
                    'City',
                    'Sector/Ward',
                    'Caste Category',
                    'Marital Status',
                    'Eligibility',
                    'Physical Verification',
                    'Reason',
                    'Remarks',
                ]);

                $serial = 1;
                $chunkSize = 1000;
                $lastPurchaserId = null;

                /*
                 * Base query mein current search/location filters
                 * automatically available rahenge.
                 */
                $baseQuery = $this->verificationAllotteesQuery(
                    $request,
                    $eligibility
                );

                while (true) {
                    $chunkQuery = clone $baseQuery;

                    /*
                     * Next batch condition sort direction ke according.
                     */
                    if ($lastPurchaserId !== null) {
                        if ($sortOrder === 'asc') {
                            $chunkQuery->where(
                                'ppp.PrivatePurchaserId',
                                '>',
                                $lastPurchaserId
                            );
                        } else {
                            $chunkQuery->where(
                                'ppp.PrivatePurchaserId',
                                '<',
                                $lastPurchaserId
                            );
                        }
                    }

                    $rows = $chunkQuery
                        ->orderBy(
                            'ppp.PrivatePurchaserId',
                            $sortOrder
                        )
                        ->limit($chunkSize)
                        ->get();

                    if ($rows->isEmpty()) {
                        break;
                    }

                    foreach ($rows as $row) {
                        fputcsv($output, [
                            $serial++,
                            $row->application_number ?? '',
                            $row->applicant_name ?? '',
                            $row->father_name ?? '',
                            $row->mobile ?? '',
                            $row->asset_id ?? '',
                            $row->asset_name ?? '',

                            trim(
                                ($row->asset_size ?? '')
                                . ' '
                                . ($row->asset_unit ?? '')
                            ),

                            $row->district_name ?? '',
                            $row->city_name ?? '',
                            $row->sector_name ?? '',
                            $row->caste_category ?? '',
                            $row->marital_status ?? '',

                            $eligibility === 'eligible'
                            ? 'Eligible'
                            : 'Not Eligible',

                            $row->physical_verification ?? '',
                            $row->status_reason ?? '',
                            $row->remarks ?? '',
                        ]);
                    }

                    $lastPurchaserId = (int) (
                        $rows->last()->purchaser_id
                    );

                    /*
                     * 1000 se kam records mile to last batch hai.
                     */
                    if ($rows->count() < $chunkSize) {
                        break;
                    }

                    if (ob_get_level() > 0) {
                        ob_flush();
                    }

                    flush();
                }

                fclose($output);
            },
            $fileName,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    public function verificationAllotteesPrint(
        Request $request,
        string $eligibility
    ) {
        $eligibility = str_replace(
            '_',
            '-',
            strtolower($eligibility)
        );

        abort_unless(
            in_array(
                $eligibility,
                ['eligible', 'not-eligible'],
                true
            ),
            404
        );

        /*
         * Current listing ka sort order.
         * sort_order nahi mila to latest records first.
         */
        $sortOrder = strtolower(
            (string) $request->input('sort_order', 'desc')
        );

        $sortOrder = in_array(
            $sortOrder,
            ['asc', 'desc'],
            true
        ) ? $sortOrder : 'desc';

        $perChunk = 1000;
        $afterId = max(0, $request->integer('after_id'));

        $query = $this->verificationAllotteesQuery(
            $request,
            $eligibility
        );

        /*
         * Keyset pagination condition sort order ke according
         * change hogi.
         */
        if ($afterId > 0) {
            if ($sortOrder === 'asc') {
                $query->where(
                    'ppp.PrivatePurchaserId',
                    '>',
                    $afterId
                );
            } else {
                $query->where(
                    'ppp.PrivatePurchaserId',
                    '<',
                    $afterId
                );
            }
        }

        $rows = $query
            ->orderBy(
                'ppp.PrivatePurchaserId',
                $sortOrder
            )
            ->limit($perChunk + 1)
            ->get();

        $hasMore = $rows->count() > $perChunk;

        $applications = $rows
            ->take($perChunk)
            ->values();

        $nextAfterId = $hasMore && $applications->isNotEmpty()
            ? $applications->last()->purchaser_id
            : null;

        return view(
            'mmsay.verificationAllotteesPrint',
            compact(
                'applications',
                'eligibility',
                'sortOrder',
                'hasMore',
                'nextAfterId'
            )
        );
    }

    // OLD property registration listing. This is a legacy view and will be removed in future releases.

    private function oldRegistrationFilters(Request $request): array
    {
        $districtId = $request->integer('district_id') ?: null;
        $cityId = $request->integer('city_id') ?: null;
        $sectorId = $request->integer('sector_id') ?: null;

        return [
            'district_id' => $districtId,
            'city_id' => $cityId,
            'sector_id' => $sectorId,
            'district_name' => $districtId
                ? DB::table('districts')->where('DistrictId', $districtId)->value('DistrictName')
                : null,
            'city_name' => $cityId
                ? DB::table('cities')->where('CityId', $cityId)->value('CityName')
                : null,
            'sector_name' => $sectorId
                ? DB::table('sectors')->where('SectorId', $sectorId)->value('SectorName')
                : null,
            'search' => trim((string) $request->input('search')),
        ];
    }

    private function oldRegistrationQuery(Request $request)
    {
        $filters = $this->oldRegistrationFilters($request);

        return DB::table('hfa.mmsay_old_registration_data')
            ->select([
                'id',
                'application_number',
                'family_id',
                'memberID',
                'fullName',
                'fatherFullName',
                'mobileNo',
                'gender',
                'age',
                'casteCategoryName',
                'occupationName',
                'familyIncome',
                'ruralUrban',
                'pinCode',
                'property_category',
                'property_details',
                'districtName',
                'btName',
                'wvName',
                'created_at',
            ])
            ->when(
                $filters['district_name'],
                fn($query, $name) =>
                $query->where('districtName', $name)
            )
            ->when(
                $filters['city_name'],
                fn($query, $name) =>
                $query->where('btName', $name)
            )
            ->when(
                $filters['sector_name'],
                fn($query, $name) =>
                $query->where('wvName', $name)
            )
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $search = $filters['search'];
                $like = '%' . addcslashes($search, '%_\\') . '%';

                $query->where(function ($subQuery) use ($search, $like) {
                    $subQuery->where('application_number', 'like', $like)
                        ->orWhere('fullName', 'like', $like)
                        ->orWhere('fatherFullName', 'like', $like)
                        ->orWhere('mobileNo', 'like', $like)
                        ->orWhere('family_id', 'like', $like)
                        ->orWhere('memberID', 'like', $like);

                    if (ctype_digit($search)) {
                        $subQuery->orWhere('id', (int) $search);
                    }
                });
            });
    }

    public function oldRegistrations(Request $request)
    {
        $filters = $this->oldRegistrationFilters($request);

        $districts = DB::table('districts')
            ->select('DistrictId', 'DistrictName')
            ->where('Is_Deleted', 0)
            ->where('Is_Active', 1)
            ->orderBy('DistrictName')
            ->get();

        $cities = $filters['district_id']
            ? DB::table('cities')
                ->select('CityId', 'CityName')
                ->where('DistrictId', $filters['district_id'])
                ->where('Is_Deleted', 0)
                ->where('Is_Active', 1)
                ->orderBy('CityName')
                ->get()
            : collect();

        $sectors = $filters['city_id']
            ? DB::table('city_sector_associations as csa')
                ->join('sectors as s', 's.SectorId', '=', 'csa.SectorId')
                ->select('s.SectorId', 's.SectorName')
                ->where('csa.CityId', $filters['city_id'])
                ->where('csa.Is_Deleted', 0)
                ->where('csa.Is_Active', 1)
                ->where('s.Is_Deleted', 0)
                ->where('s.Is_Active', 1)
                ->distinct()
                ->orderBy('s.SectorName')
                ->get()
            : collect();

        $registrations = $this->oldRegistrationQuery($request)
            ->orderByDesc('id')
            ->paginate(50)
            ->withQueryString();

        return view('mmsay.oldRegistrations', compact(
            'registrations',
            'districts',
            'cities',
            'sectors',
            'filters'
        ));
    }

    public function oldRegistrationFilterOptions(Request $request)
    {
        if ($request->filled('district_id')) {
            return response()->json([
                'cities' => DB::table('cities')
                    ->select('CityId as id', 'CityName as name')
                    ->where('DistrictId', $request->integer('district_id'))
                    ->where('Is_Deleted', 0)
                    ->where('Is_Active', 1)
                    ->orderBy('CityName')
                    ->get(),
            ]);
        }

        if ($request->filled('city_id')) {
            return response()->json([
                'sectors' => DB::table('city_sector_associations as csa')
                    ->join('sectors as s', 's.SectorId', '=', 'csa.SectorId')
                    ->select('s.SectorId as id', 's.SectorName as name')
                    ->where('csa.CityId', $request->integer('city_id'))
                    ->where('csa.Is_Deleted', 0)
                    ->where('csa.Is_Active', 1)
                    ->where('s.Is_Deleted', 0)
                    ->where('s.Is_Active', 1)
                    ->distinct()
                    ->orderBy('s.SectorName')
                    ->get(),
            ]);
        }

        return response()->json(['cities' => [], 'sectors' => []]);
    }

    public function oldRegistrationsCsv(Request $request)
    {
        $fileName = 'old-registrations-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            $propertyDetailsText = static function ($value): string {
                $decoded = json_decode((string) $value, true);

                if (!is_array($decoded)) {
                    return trim((string) $value);
                }

                $parts = [];
                foreach ($decoded as $key => $item) {
                    if ($key === 'id' || $item === null || $item === '') {
                        continue;
                    }

                    $label = ucwords(str_replace(['_', '-'], ' ', (string) $key));
                    $displayValue = is_array($item)
                        ? implode(', ', array_filter(array_map('strval', $item)))
                        : (string) $item;

                    if ($displayValue !== '') {
                        $parts[] = $label . ': ' . $displayValue;
                    }
                }

                return implode(' | ', $parts);
            };

            fputcsv($handle, [
                'S.No.',
                'Application No.',
                'Family ID',
                'Member ID',
                'Applicant',
                'Father Name',
                'Mobile',
                'Property Category',
                'Property Details',
                'Gender',
                'Age',
                'Caste Category',
                'Occupation',
                'Family Income',
                'Rural/Urban',
                'District',
                'Block/Town',
                'Village/Ward',
                'PIN Code',
                'Registration Date',
            ]);

            $serial = 1;

            $this->oldRegistrationQuery($request)
                ->orderBy('id')
                ->chunkById(1000, function ($rows) use ($handle, &$serial, $propertyDetailsText) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $serial++,
                            $row->application_number,
                            $row->family_id,
                            $row->memberID,
                            $row->fullName,
                            $row->fatherFullName,
                            $row->mobileNo,
                            $row->property_category,
                            $propertyDetailsText($row->property_details),
                            $row->gender,
                            $row->age,
                            $row->casteCategoryName,
                            $row->occupationName,
                            $row->familyIncome,
                            $row->ruralUrban,
                            $row->districtName,
                            $row->btName,
                            $row->wvName,
                            $row->pinCode,
                            $row->created_at,
                        ]);
                    }
                }, 'id', 'id');

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function oldRegistrationsPrint(Request $request)
    {
        $perChunk = 1000;
        $afterId = max(0, $request->integer('after_id'));

        $rows = $this->oldRegistrationQuery($request)
            ->when($afterId, fn($query) => $query->where('id', '<', $afterId))
            ->orderByDesc('id')
            ->limit($perChunk + 1)
            ->get();

        $hasMore = $rows->count() > $perChunk;
        $registrations = $rows->take($perChunk)->values();
        $nextAfterId = $hasMore ? $registrations->last()->id : null;

        return view('mmsay.oldRegistrationsPrint', compact(
            'registrations',
            'hasMore',
            'nextAfterId'
        ));
    }

    // OLD property registration listing. This is a legacy view and will be removed in future releases.

    public function propertyRegistration(Request $request)
    {
        $districtId = $request->integer('district_id') ?: null;
        $cityId = $request->integer('city_id') ?: null;
        $sectorId = $request->integer('sector_id') ?: null;
        $search = trim((string) $request->input('search'));
        $sortOrder = strtolower((string) $request->input('sort_order', 'desc'));
        $sortOrder = in_array($sortOrder, ['asc', 'desc'], true)
            ? $sortOrder
            : 'desc';

        $receiptTotalSql = $this->receiptTotalSql('pr.AssetId');

        $properties = DB::table('property_registration as pr')
            ->leftJoin('districts as d', 'd.DistrictId', '=', 'pr.DistrictId')
            ->leftJoin('cities as c', 'c.CityId', '=', 'pr.CityId')
            ->leftJoin('sectors as s', 's.SectorId', '=', 'pr.SectorId')
            ->leftJoin('property_auction_detail as pad', function ($join) {
                $join->on('pad.AssetId', '=', 'pr.AssetId')
                    ->where('pad.IsDeleted', 0)
                    ->where('pad.IsActive', 1);
            })
            ->leftJoin('property_private_purchasers as ppp', function ($join) {
                $join->on(
                    'ppp.PrivatePurchaserId',
                    '=',
                    'pad.PurchaserID'
                )
                    // Historical/inactive purchasers must still be displayed.
                    ->where('ppp.IsDeleted', 0);
            })
            ->select([
                'pr.AssetId',
                'pr.AssetName',
                'pr.AssetSize',
                'pr.Unit',
                'd.DistrictName as district',
                'c.CityName as city',
                's.SectorName as sector',
                'ppp.PrivatePurchaserId',
                'ppp.PrivatePurchaserName as purchaser_name',
                'ppp.MobileNo as mobile',
                'ppp.ApplicationNo as application_number',
                'pad.PropertyAuctionId',
                'pad.FlatCost',
                'pad.ReceivedAmount',
            ])
            ->selectRaw("
            {$receiptTotalSql} AS receipt_paid,

            (
                COALESCE(pad.ReceivedAmount, 0)
                + {$receiptTotalSql}
            ) AS total_received,

            GREATEST(
                COALESCE(pad.FlatCost, 0)
                - (
                    COALESCE(pad.ReceivedAmount, 0)
                    + {$receiptTotalSql}
                ),
                0
            ) AS pending_amount
        ")
            ->where('pr.IsDeleted', 0)
            ->where('pr.IsActive', 1)
            ->when(
                $districtId,
                fn($query) =>
                $query->where('pr.DistrictId', $districtId)
            )
            ->when(
                $cityId,
                fn($query) =>
                $query->where('pr.CityId', $cityId)
            )
            ->when(
                $sectorId,
                fn($query) =>
                $query->where('pr.SectorId', $sectorId)
            )
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('pr.AssetName', 'LIKE', "%{$search}%")
                        ->orWhere('pr.AssetId', $search)
                        ->orWhere(
                            'ppp.PrivatePurchaserName',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere('ppp.MobileNo', 'LIKE', "%{$search}%")
                        ->orWhere('ppp.ApplicationNo', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('pr.AssetId', $sortOrder)
            ->paginate(self::LIST_PAGE_SIZE)
            ->withQueryString();

        $districts = DB::table('districts')
            ->select('DistrictId', 'DistrictName')
            ->where('Is_Deleted', 0)
            ->where('Is_Active', 1)
            ->orderBy('DistrictName')
            ->get();

        $cities = $districtId
            ? DB::table('cities')
                ->select('CityId', 'CityName')
                ->where('DistrictId', $districtId)
                ->where('Is_Deleted', 0)
                ->where('Is_Active', 1)
                ->orderBy('CityName')
                ->get()
            : collect();

        $sectors = $cityId
            ? DB::table('city_sector_associations as csa')
                ->join('sectors as s', 's.SectorId', '=', 'csa.SectorId')
                ->select('s.SectorId', 's.SectorName')
                ->where('csa.CityId', $cityId)
                ->where('csa.Is_Deleted', 0)
                ->where('csa.Is_Active', 1)
                ->where('s.Is_Deleted', 0)
                ->where('s.Is_Active', 1)
                ->distinct()
                ->orderBy('s.SectorName')
                ->get()
            : collect();

        return view('mmsay.departmentPropertyRegistration', compact(
            'properties',
            'districts',
            'cities',
            'sectors',
            'districtId',
            'cityId',
            'sectorId',
            'search',
            'sortOrder'
        ));
    }

    private function getPropertyStatement($assetId): array
    {
        /*
        |--------------------------------------------------------------------------
        | Validate route parameter
        |--------------------------------------------------------------------------
        */

        abort_unless(
            is_numeric($assetId) && (int) $assetId > 0,
            404
        );

        $assetId = (int) $assetId;

        /*
        |--------------------------------------------------------------------------
        | Property, location, auction and purchaser information
        |--------------------------------------------------------------------------
        */

        $property = DB::table('property_registration as pr')
            ->leftJoin(
                'districts as d',
                'd.DistrictId',
                '=',
                'pr.DistrictId'
            )
            ->leftJoin(
                'cities as c',
                'c.CityId',
                '=',
                'pr.CityId'
            )
            ->leftJoin(
                'sectors as s',
                's.SectorId',
                '=',
                'pr.SectorId'
            )
            ->leftJoin('property_auction_detail as pad', function ($join) {
                $join->on('pad.AssetId', '=', 'pr.AssetId')
                    ->where('pad.IsDeleted', 0)
                    ->where('pad.IsActive', 1);
            })
            ->leftJoin(
                'property_private_purchasers as ppp',
                function ($join) {
                    $join->on(
                        'ppp.PrivatePurchaserId',
                        '=',
                        'pad.PurchaserID'
                    )
                        // IsActive may be 0 for a valid allotted purchaser.
                        ->where('ppp.IsDeleted', 0);
                }
            )
            ->select([
                'pr.AssetId',
                'pr.AssetName',
                'pr.AssetSize',
                'pr.Unit',
                'pr.DistrictId',
                'pr.CityId',
                'pr.SectorId',

                'd.DistrictName',
                'c.CityName',
                's.SectorName',

                'pad.PropertyAuctionId',
                'pad.FlatCost',
                'pad.ReceivedAmount',
                'pad.BalanceAmount',
                'pad.CreatedDate as AuctionCreatedDate',

                'ppp.PrivatePurchaserId',
                'ppp.PrivatePurchaserName',
                'ppp.PurchaserFatherName',
                'ppp.MobileNo',
                'ppp.ApplicationNo',
                'ppp.PPPId',
                'ppp.MemberID',
                'ppp.Address',
            ])
            ->where('pr.AssetId', $assetId)
            ->where('pr.IsDeleted', 0)
            ->where('pr.IsActive', 1)
            ->first();

        abort_if(!$property, 404);

        /*
        |--------------------------------------------------------------------------
        | Cash receipt information
        |--------------------------------------------------------------------------
        */

        $cashReceipts = DB::table('cash_receipt_details')
            ->select([
                'id',
                'receipt_number',
                'total_paid_amount',
                'created_date',
            ])
            ->where('asset_number', $assetId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->orderByDesc('created_date')
            ->orderByDesc('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FIFO payment allocation
        |--------------------------------------------------------------------------
        | Every cash receipt is allocated to the oldest unpaid installment first.
        | If a citizen misses one EMI and later deposits two EMI amounts, the
        | missed EMI is cleared first and the remaining amount clears the next EMI.
        */

        $receiptsForAllocation = DB::table('cash_receipt_details')
            ->select([
                'id',
                'receipt_number',
                'total_paid_amount',
                'created_date',
            ])
            ->where('asset_number', $assetId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->where('total_paid_amount', '>', 0)
            ->orderBy('created_date')
            ->orderBy('id')
            ->get()
            ->map(function ($receipt) {
                $receipt->remaining_amount = (float) (
                    $receipt->total_paid_amount ?? 0
                );

                return $receipt;
            })
            ->values();

        /*
         * The auction table's ReceivedAmount is also part of the total property
         * payment. Add it as the opening FIFO payment before cash receipts.
         */
        $openingAllocationAmount = (float) (
            $property->ReceivedAmount ?? 0
        );

        if ($openingAllocationAmount > 0) {
            $receiptsForAllocation->prepend((object) [
                'id' => 0,
                'receipt_number' => 'Initial Received Amount',
                'total_paid_amount' => $openingAllocationAmount,
                'created_date' =>
                    $property->AuctionCreatedDate ?? null,
                'remaining_amount' => $openingAllocationAmount,
            ]);
        }

        $emiDetails = DB::table('installment_due as due')
            ->select([
                'due.DueInstallmentId',
                'due.InstallmentNumber',
                'due.DueDate',
                'due.EMIAmount',
                'due.DueAmount',
                'due.PrincipleAmount',
                'due.InterestAmount',
                'due.GSTAmount',
            ])
            ->selectRaw('
                COALESCE(
                    NULLIF(due.DueAmount, 0),
                    due.EMIAmount,
                    0
                ) AS installment_payable
            ')
            ->where('due.AssetId', $assetId)
            ->where('due.IsDeleted', 0)
            ->where('due.IsActive', 1)
            ->orderBy('due.InstallmentNumber')
            ->get()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Reconcile property cost with EMI schedule
        |--------------------------------------------------------------------------
        | Example:
        | Property cost  = 100,000
        | EMI schedule   =  90,000
        | Non-EMI part   =  10,000
        |
        | The non-EMI/down-payment component must be consumed before allocating
        | the remaining payment pool to installment dues.
        */

        $schedulePayableTotal = (float) $emiDetails
            ->sum('installment_payable');

        $propertyCostForAllocation = (float) (
            $property->FlatCost ?? 0
        );

        $nonEmiComponent = max(
            $propertyCostForAllocation - $schedulePayableTotal,
            0
        );

        $nonEmiRemaining = $nonEmiComponent;
        $allocationTolerance = 0.01;

        foreach ($receiptsForAllocation as $paymentSource) {
            if ($nonEmiRemaining <= $allocationTolerance) {
                break;
            }

            if (
                $paymentSource->remaining_amount
                <= $allocationTolerance
            ) {
                continue;
            }

            $nonEmiAllocation = min(
                $nonEmiRemaining,
                $paymentSource->remaining_amount
            );

            $paymentSource->remaining_amount -= $nonEmiAllocation;
            $nonEmiRemaining -= $nonEmiAllocation;
        }

        $emiAllocationAvailable = (float) $receiptsForAllocation
            ->sum('remaining_amount');

        $normaliseReceiptNumber = static function ($number) {
            $number = trim((string) $number);

            if (
                $number !== ''
                && preg_match(
                    '/^[0-9]+(?:\.[0-9]+)?[eE][+-]?[0-9]+$/',
                    $number
                )
            ) {
                return number_format(
                    (float) $number,
                    0,
                    '.',
                    ''
                );
            }

            return $number;
        };

        $receiptIndex = 0;
        $receiptCount = $receiptsForAllocation->count();
        $tolerance = $allocationTolerance;
        $statusAsOfDate = now('Asia/Kolkata')->startOfDay();

        foreach ($emiDetails as $emi) {
            $payable = (float) (
                $emi->installment_payable ?? 0
            );

            $allocated = 0.0;
            $allocations = [];
            $lastPaymentDate = null;

            while (
                $allocated + $tolerance < $payable
                && $receiptIndex < $receiptCount
            ) {
                $receipt = $receiptsForAllocation[$receiptIndex];

                if ($receipt->remaining_amount <= $tolerance) {
                    $receiptIndex++;
                    continue;
                }

                $required = max(
                    $payable - $allocated,
                    0
                );

                $allocatedAmount = min(
                    $required,
                    $receipt->remaining_amount
                );

                if ($allocatedAmount <= $tolerance) {
                    $receiptIndex++;
                    continue;
                }

                $allocated += $allocatedAmount;
                $receipt->remaining_amount -= $allocatedAmount;

                $allocations[] = [
                    'receipt_number' =>
                        $normaliseReceiptNumber(
                            $receipt->receipt_number
                        ),
                    'receipt_date' => $receipt->created_date,
                    'allocated_amount' => $allocatedAmount,
                ];

                $lastPaymentDate = $receipt->created_date;

                if ($receipt->remaining_amount <= $tolerance) {
                    $receiptIndex++;
                }
            }

            $pending = max(
                $payable - $allocated,
                0
            );

            $emi->allocated_payment = round($allocated, 2);
            $emi->installment_pending = round($pending, 2);
            $emi->actual_payment_date = $lastPaymentDate;
            $emi->receipt_allocations = $allocations;
            $emi->receipt_numbers = collect($allocations)
                ->pluck('receipt_number')
                ->filter()
                ->unique()
                ->implode(', ');

            if ($payable > 0 && $pending <= $tolerance) {
                $emi->payment_status = 'paid';
            } elseif ($allocated > $tolerance) {
                $emi->payment_status = 'partial';
            } else {
                $emi->payment_status = 'pending';
            }

            /*
             * Display-only status:
             * A fully paid EMI whose due date is still in the future is shown as
             * "Advance Paid". On the due date (or afterwards) it automatically
             * appears as "Paid". Payment allocation and summary counts remain
             * based on payment_status and are not changed by this label.
             */
            $dueDateForStatus = !empty($emi->DueDate)
                ? \Carbon\Carbon::parse(
                    $emi->DueDate,
                    'Asia/Kolkata'
                )->startOfDay()
                : null;

            $emi->display_status = (
                $emi->payment_status === 'paid'
                && $dueDateForStatus
                && $dueDateForStatus->gt($statusAsOfDate)
            )
                ? 'advance_paid'
                : $emi->payment_status;
        }

        $unallocatedReceiptAmount = (float) $receiptsForAllocation
            ->sum('remaining_amount');

        $emiPendingAmount = (float) $emiDetails
            ->sum('installment_pending');

        /*
        |--------------------------------------------------------------------------
        | Payment totals
        |--------------------------------------------------------------------------
        */

        $receiptTotal = (float) $cashReceipts
            ->sum('total_paid_amount');

        $openingReceivedAmount = (float) (
            $property->ReceivedAmount ?? 0
        );

        $flatCost = (float) (
            $property->FlatCost ?? 0
        );

        $totalReceived =
            $openingReceivedAmount + $receiptTotal;

        $pendingAmount = max(
            $flatCost - $totalReceived,
            0
        );

        $excessAmount = max(
            $totalReceived - $flatCost,
            0
        );

        $totalEmiCount = $emiDetails
            ->pluck('InstallmentNumber')
            ->unique()
            ->count();

        $paidEmiCount = $emiDetails
            ->filter(function ($emi) {
                return $emi->payment_status === 'paid';
            })
            ->count();

        $partiallyPaidEmiCount = $emiDetails
            ->filter(function ($emi) {
                return $emi->payment_status === 'partial';
            })
            ->count();

        $pendingEmiCount = $emiDetails
            ->filter(function ($emi) {
                return $emi->payment_status === 'pending';
            })
            ->count();

        return compact(
            'property',
            'cashReceipts',
            'emiDetails',
            'receiptTotal',
            'openingReceivedAmount',
            'flatCost',
            'totalReceived',
            'pendingAmount',
            'excessAmount',
            'totalEmiCount',
            'paidEmiCount',
            'partiallyPaidEmiCount',
            'pendingEmiCount',
            'unallocatedReceiptAmount',
            'schedulePayableTotal',
            'nonEmiComponent',
            'emiAllocationAvailable',
            'emiPendingAmount'
        );
    }

    public function propertyDetails($assetId)
    {
        $assetId = (int) $assetId;

        abort_if($assetId <= 0, 404);

        return view(
            'mmsay.propertyDetails',
            $this->getPropertyStatement($assetId)
        );
    }

    public function propertyPrint($assetId)
    {
        $assetId = (int) $assetId;

        abort_if($assetId <= 0, 404);

        return view(
            'mmsay.propertyStatementPrint',
            $this->getPropertyStatement($assetId)
        );
    }

    public function printPropertyRecords(Request $request)
    {
        $filters = $request->only([
            'district_id',
            'city_id',
            'sector_id',
            'search',
            'sort_order',
        ]);

        $chunkSize = 500;

        $properties = $this->propertyExportQuery($filters)
            ->paginate($chunkSize)
            ->withQueryString();

        return view('mmsay.exports.propertiesPrint', compact(
            'properties',
            'filters',
            'chunkSize'
        ));
    }

    public function exportPropertiesCsv(Request $request)
    {
        $districtId = $request->integer('district_id') ?: null;
        $cityId = $request->integer('city_id') ?: null;
        $sectorId = $request->integer('sector_id') ?: null;
        $search = trim((string) $request->input('search'));
        $sortOrder = strtolower((string) $request->input('sort_order', 'desc'));
        $sortOrder = in_array($sortOrder, ['asc', 'desc'], true)
            ? $sortOrder
            : 'desc';

        $fileName = 'property-records-'
            . now()->format('Y-m-d-His')
            . '.csv';

        return response()->streamDownload(
            function () use ($districtId, $cityId, $sectorId, $search, $sortOrder) {
                $output = fopen('php://output', 'w');

                if ($output === false) {
                    throw new RuntimeException(
                        'Unable to open CSV output stream.'
                    );
                }

                /*
                 * UTF-8 BOM:
                 * Hindi names and special characters Excel mein sahi dikhenge.
                 */
                fwrite($output, "\xEF\xBB\xBF");

                /*
                 * Protect CSV fields from Excel formula injection.
                 */
                $safeCsvValue = static function ($value) {
                    if ($value === null) {
                        return '';
                    }

                    $value = (string) $value;

                    if (
                        $value !== ''
                        && in_array($value[0], ['=', '+', '-', '@'], true)
                    ) {
                        return "'" . $value;
                    }

                    return $value;
                };

                /*
                 * CSV headings
                 */
                fputcsv(
                    $output,
                    [
                        'Asset ID',
                        'Asset Name',
                        'Asset Size',
                        'Unit',
                        'District',
                        'City',
                        'Sector',
                        'Application Number',
                        'Purchaser Name',
                        'Father Name',
                        'Mobile',
                        'Total Cost',
                        'Initial Received',
                        'Cash Receipt Total',
                        'Total Received',
                        'Pending Amount',
                    ],
                    ',',
                    '"',
                    ''
                );

                /*
                 * Cash receipts aggregated once per asset.
                 */
                $receiptTotals = DB::table('cash_receipt_details')
                    ->selectRaw('
                    asset_number,
                    SUM(total_paid_amount) AS receipt_paid
                ')
                    ->where('IsDeleted', 0)
                    ->where('IsActive', 1)
                    ->groupBy('asset_number');

                /*
                 * Base filtered query.
                 */
                $query = DB::table('property_registration as pr')
                    ->leftJoin(
                        'districts as d',
                        'd.DistrictId',
                        '=',
                        'pr.DistrictId'
                    )
                    ->leftJoin(
                        'cities as c',
                        'c.CityId',
                        '=',
                        'pr.CityId'
                    )
                    ->leftJoin(
                        'sectors as s',
                        's.SectorId',
                        '=',
                        'pr.SectorId'
                    )
                    ->leftJoin(
                        'property_auction_detail as pad',
                        function ($join) {
                            $join
                                ->on('pad.AssetId', '=', 'pr.AssetId')
                                ->where('pad.IsDeleted', 0)
                                ->where('pad.IsActive', 1);
                        }
                    )
                    ->leftJoin(
                        'property_private_purchasers as ppp',
                        function ($join) {
                            $join
                                ->on(
                                    'ppp.PrivatePurchaserId',
                                    '=',
                                    'pad.PurchaserID'
                                )
                                // Include inactive but non-deleted purchasers.
                                ->where('ppp.IsDeleted', 0);
                        }
                    )
                    ->leftJoinSub(
                        $receiptTotals,
                        'cr',
                        function ($join) {
                            $join->on(
                                'cr.asset_number',
                                '=',
                                'pr.AssetId'
                            );
                        }
                    )
                    ->select([
                        'pr.AssetId',
                        'pr.AssetName',
                        'pr.AssetSize',
                        'pr.Unit',
                        'd.DistrictName',
                        'c.CityName',
                        's.SectorName',
                        'ppp.ApplicationNo',
                        'ppp.PrivatePurchaserName',
                        'ppp.PurchaserFatherName',
                        'ppp.MobileNo',
                        'pad.FlatCost',
                        'pad.ReceivedAmount',
                    ])
                    ->selectRaw('
                    COALESCE(cr.receipt_paid, 0)
                        AS receipt_paid,

                    (
                        COALESCE(pad.ReceivedAmount, 0)
                        + COALESCE(cr.receipt_paid, 0)
                    ) AS total_received,

                    GREATEST(
                        COALESCE(pad.FlatCost, 0)
                        - (
                            COALESCE(pad.ReceivedAmount, 0)
                            + COALESCE(cr.receipt_paid, 0)
                        ),
                        0
                    ) AS pending_amount
                ')
                    ->where('pr.IsDeleted', 0)
                    ->where('pr.IsActive', 1)
                    ->when(
                        $districtId,
                        fn($query) =>
                        $query->where(
                            'pr.DistrictId',
                            $districtId
                        )
                    )
                    ->when(
                        $cityId,
                        fn($query) =>
                        $query->where(
                            'pr.CityId',
                            $cityId
                        )
                    )
                    ->when(
                        $sectorId,
                        fn($query) =>
                        $query->where(
                            'pr.SectorId',
                            $sectorId
                        )
                    )
                    ->when(
                        $search !== '',
                        function ($query) use ($search) {
                            $query->where(
                                function ($query) use ($search) {
                                    $query
                                        ->where(
                                            'pr.AssetName',
                                            'LIKE',
                                            "%{$search}%"
                                        )
                                        ->orWhere(
                                            'ppp.PrivatePurchaserName',
                                            'LIKE',
                                            "%{$search}%"
                                        )
                                        ->orWhere(
                                            'ppp.MobileNo',
                                            'LIKE',
                                            "%{$search}%"
                                        )
                                        ->orWhere(
                                            'ppp.ApplicationNo',
                                            'LIKE',
                                            "%{$search}%"
                                        );

                                    if (ctype_digit($search)) {
                                        $query->orWhere(
                                            'pr.AssetId',
                                            (int) $search
                                        );
                                    }
                                }
                            );
                        }
                    );

                /*
                 * 2,000 records per database chunk.
                 * Entire dataset memory mein load nahi hoga.
                 */
                $chunkMethod = $sortOrder === 'desc'
                    ? 'chunkByIdDesc'
                    : 'chunkById';

                $query->{$chunkMethod}(
                    2000,
                    function ($records) use ($output, $safeCsvValue) {
                        foreach ($records as $item) {
                            fputcsv(
                                $output,
                                [
                                    $safeCsvValue($item->AssetId),
                                    $safeCsvValue($item->AssetName),
                                    $item->AssetSize,
                                    $safeCsvValue($item->Unit),
                                    $safeCsvValue(
                                        $item->DistrictName ?? '-'
                                    ),
                                    $safeCsvValue(
                                        $item->CityName ?? '-'
                                    ),
                                    $safeCsvValue(
                                        $item->SectorName ?? '-'
                                    ),
                                    $safeCsvValue(
                                        $item->ApplicationNo ?? ''
                                    ),
                                    $safeCsvValue(
                                        $item->PrivatePurchaserName ?? ''
                                    ),
                                    $safeCsvValue(
                                        $item->PurchaserFatherName ?? ''
                                    ),
                                    $safeCsvValue(
                                        $item->MobileNo ?? ''
                                    ),
                                    (float) ($item->FlatCost ?? 0),
                                    (float) (
                                        $item->ReceivedAmount ?? 0
                                    ),
                                    (float) (
                                        $item->receipt_paid ?? 0
                                    ),
                                    (float) (
                                        $item->total_received ?? 0
                                    ),
                                    (float) (
                                        $item->pending_amount ?? 0
                                    ),
                                ],
                                ',',
                                '"',
                                ''
                            );
                        }

                        // Immediately send generated rows to browser.
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }

                        flush();
                    },
                    'pr.AssetId',
                    'AssetId'
                );

                fclose($output);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',

                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate',

                'Pragma' =>
                    'no-cache',

                'X-Content-Type-Options' =>
                    'nosniff',
            ]
        );
    }

    private function propertyExportQuery(array $filters)
    {
        $sortOrder = strtolower((string) ($filters['sort_order'] ?? 'desc'));
        $sortOrder = in_array($sortOrder, ['asc', 'desc'], true)
            ? $sortOrder
            : 'desc';

        $receiptTotalSql = $this->receiptTotalSql('pr.AssetId');

        return DB::table('property_registration as pr')
            ->leftJoin('districts as d', 'd.DistrictId', '=', 'pr.DistrictId')
            ->leftJoin('cities as c', 'c.CityId', '=', 'pr.CityId')
            ->leftJoin('sectors as s', 's.SectorId', '=', 'pr.SectorId')
            ->leftJoin('property_auction_detail as pad', function ($join) {
                $join->on('pad.AssetId', '=', 'pr.AssetId')
                    ->where('pad.IsDeleted', 0)
                    ->where('pad.IsActive', 1);
            })
            ->leftJoin('property_private_purchasers as ppp', function ($join) {
                $join->on(
                    'ppp.PrivatePurchaserId',
                    '=',
                    'pad.PurchaserID'
                )
                    // Print/export must use the same purchaser rule as listing.
                    ->where('ppp.IsDeleted', 0);
            })
            ->select([
                'pr.AssetId',
                'pr.AssetName',
                'pr.AssetSize',
                'pr.Unit',
                'd.DistrictName',
                'c.CityName',
                's.SectorName',
                'ppp.ApplicationNo',
                'ppp.PrivatePurchaserName',
                'ppp.MobileNo',
                'pad.FlatCost',
            ])
            ->selectRaw("
            (
                COALESCE(pad.ReceivedAmount, 0)
                + {$receiptTotalSql}
            ) AS total_received,

            GREATEST(
                COALESCE(pad.FlatCost, 0)
                - (
                    COALESCE(pad.ReceivedAmount, 0)
                    + {$receiptTotalSql}
                ),
                0
            ) AS pending_amount
        ")
            ->where('pr.IsDeleted', 0)
            ->where('pr.IsActive', 1)
            ->when(
                $filters['district_id'] ?? null,
                fn($query, $value) =>
                $query->where('pr.DistrictId', $value)
            )
            ->when(
                $filters['city_id'] ?? null,
                fn($query, $value) =>
                $query->where('pr.CityId', $value)
            )
            ->when(
                $filters['sector_id'] ?? null,
                fn($query, $value) =>
                $query->where('pr.SectorId', $value)
            )
            ->when(
                trim($filters['search'] ?? '') !== '',
                function ($query) use ($filters) {
                    $search = trim($filters['search']);

                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('pr.AssetName', 'LIKE', "%{$search}%")
                            ->orWhere('pr.AssetId', $search)
                            ->orWhere(
                                'ppp.PrivatePurchaserName',
                                'LIKE',
                                "%{$search}%"
                            )
                            ->orWhere('ppp.MobileNo', 'LIKE', "%{$search}%")
                            ->orWhere('ppp.ApplicationNo', 'LIKE', "%{$search}%");
                    });
                }
            )
            ->orderBy('pr.AssetId', $sortOrder);
    }

    public function exportPropertiesExcel(Request $request)
    {
        $filters = $request->only([
            'district_id',
            'city_id',
            'sector_id',
            'search',
            'sort_order',
        ]);

        return Excel::download(
            new PropertiesExport($filters),
            'property-records-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    // AJAX DROPDOWNS
    public function getDistricts($name)
    {
        return DB::table('districts as d')
            ->join('em_offices as e', 'd.BranchId', '=', 'e.BranchId')
            ->where('e.BranchName', $name)
            ->select('d.DistrictName')
            ->distinct()
            ->pluck('DistrictName');
    }

    public function getCities($name)
    {
        return DB::table('cities as c')
            ->join('districts as d', 'c.DistrictId', '=', 'd.DistrictId')
            ->where('d.DistrictName', $name)
            ->select('c.CityName')
            ->distinct()
            ->pluck('CityName');
    }

    public function getSectors($name)
    {
        return DB::table('city_sector_associations as csa')
            ->join('cities as c', 'csa.CityId', '=', 'c.CityId')
            ->join('sectors as s', 'csa.SectorId', '=', 's.SectorId')
            ->where('c.CityName', $name)
            ->pluck('s.SectorName');
    }

    // EXCEL EXPORT
    public function export(Request $request)
    {
        return Excel::download(new PropertyExport($request), 'properties.xlsx');
    }

    public function mmsayDepartmentCashReceipt(Request $request)
    {
        $query = DB::table('cash_receipt_details as cr')
            ->leftJoin('em_offices as eo', 'cr.BranchId', '=', 'eo.BranchId')
            ->leftJoin('districts as d', 'cr.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'cr.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'cr.SectorId', '=', 's.SectorId')
            ->select(
                'cr.id',
                'eo.BranchName as em_office',
                'd.DistrictName as district_office',
                'c.CityName as city_office',
                's.SectorName as sector',
                'cr.asset_number',
                'cr.created_date as payment_date',
                'cr.receipt_number',
                'cr.total_paid_amount'
            )
            ->where('cr.IsDeleted', 0)
            ->where('cr.IsActive', 1);

        if ($request->em_office) {
            $query->where('eo.BranchName', $request->em_office);
        }

        if ($request->district) {
            $query->where('d.DistrictName', $request->district);
        }

        if ($request->city) {
            $query->where('c.CityName', $request->city);
        }

        if ($request->sector) {
            $query->where('s.SectorName', $request->sector);
        }

        $receipts = $query
            ->orderByDesc('cr.id')
            ->paginate(20)
            ->withQueryString();

        return view(
            'mmsay.mmsayDepartmentCashReceipt',
            [
                'receipts' => $receipts,
                'emOffices' => EmOffice::all()
            ]
        );
    }

    public function cashReceiptDistricts($name)
    {
        return DB::table('districts as d')
            ->join('em_offices as e', 'd.BranchId', '=', 'e.BranchId')
            ->where('e.BranchName', $name)
            ->select('d.DistrictName')
            ->distinct()
            ->pluck('DistrictName');
    }

    public function cashReceiptCities($name)
    {
        return DB::table('cities as c')
            ->join('districts as d', 'c.DistrictId', '=', 'd.DistrictId')
            ->where('d.DistrictName', $name)
            ->select('c.CityName')
            ->distinct()
            ->pluck('CityName');
    }

    public function cashReceiptSectors($name)
    {
        return DB::table('city_sector_associations as csa')
            ->join('cities as c', 'csa.CityId', '=', 'c.CityId')
            ->join('sectors as s', 'csa.SectorId', '=', 's.SectorId')
            ->where('c.CityName', $name)
            ->pluck('s.SectorName');
    }


    private function departmentDrawQuery(Request $request)
    {
        $districtId = $request->integer('district_id') ?: null;

        $sortOrder = strtolower(
            (string) $request->input('sort_order', 'desc')
        );

        $sortOrder = in_array($sortOrder, ['asc', 'desc'], true)
            ? $sortOrder
            : 'desc';

        return DB::table('property_registration as pr')
            ->join('districts as d', function ($join) {
                $join->on(
                    'd.DistrictId',
                    '=',
                    'pr.DistrictId'
                )
                    ->where('d.Is_Deleted', 0)
                    ->where('d.Is_Active', 1);
            })
            ->select([
                'd.DistrictId',
                'd.DistrictName',
            ])
            ->selectRaw('COUNT(pr.AssetId) AS total_assets')
            ->where('pr.IsDeleted', 0)
            ->where('pr.IsActive', 1)
            ->when(
                $districtId,
                fn($query) => $query->where(
                    'pr.DistrictId',
                    $districtId
                )
            )
            ->groupBy(
                'd.DistrictId',
                'd.DistrictName'
            )
            ->orderBy('total_assets', $sortOrder)
            ->orderBy('d.DistrictName');
    }

    public function mmsayDepartmentDraw(Request $request)
    {
        $districtId = $request->integer('district_id') ?: null;

        $sortOrder = strtolower(
            (string) $request->input('sort_order', 'desc')
        );

        $sortOrder = in_array($sortOrder, ['asc', 'desc'], true)
            ? $sortOrder
            : 'desc';

        $districts = DB::table('districts')
            ->select([
                'DistrictId',
                'DistrictName',
            ])
            ->where('Is_Deleted', 0)
            ->where('Is_Active', 1)
            ->orderBy('DistrictName')
            ->get();

        $drawDistricts = $this->departmentDrawQuery($request)
            ->get();

        $grandTotal = (int) $drawDistricts->sum('total_assets');

        /*
         * Draw PDFs are kept separate from registered asset totals.
         * This addition does not change the existing district summary query.
         */
        $drawDocumentRows = DB::table('property_draw_documents')
            ->select([
                'id',
                'document_code',
                'title',
                'district_id',
                'district_name',
                'location_label',
                'sector_label',
                'total_plots',
                'original_file_name',
                'file_path',
                'published_date',
            ])
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->when(
                $districtId,
                fn($query) => $query->where(
                    'district_id',
                    $districtId
                )
            )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $totalDocuments = $drawDocumentRows->count();

        $drawDocuments = $drawDocumentRows->groupBy(
            fn($document) => (int) $document->district_id
        );

        return view('mmsay.departmentDraw', compact(
            'districts',
            'drawDistricts',
            'grandTotal',
            'drawDocuments',
            'totalDocuments',
            'districtId',
            'sortOrder'
        ));
    }

    public function mmsayDepartmentDrawCsv(Request $request)
    {
        $fileName = 'district-draw-summary-'
            . now()->format('Y-m-d-His')
            . '.csv';

        return response()->streamDownload(
            function () use ($request) {
                $output = fopen('php://output', 'w');

                if ($output === false) {
                    throw new RuntimeException(
                        'Unable to open CSV output stream.'
                    );
                }

                // Excel में UTF-8 content सही दिखाने के लिए BOM
                fwrite($output, "\xEF\xBB\xBF");

                fputcsv($output, [
                    'S.No.',
                    'District ID',
                    'District Name',
                    'Total Assets',
                ]);

                $drawDistricts = $this->departmentDrawQuery($request)
                    ->get();

                $serial = 1;
                $grandTotal = 0;

                foreach ($drawDistricts as $district) {
                    $totalAssets = (int) $district->total_assets;

                    $grandTotal += $totalAssets;

                    fputcsv($output, [
                        $serial++,
                        $district->DistrictId,
                        $district->DistrictName,
                        $totalAssets,
                    ]);
                }

                // Empty separator row
                fputcsv($output, ['', '', '', '']);

                // Grand Total row
                fputcsv($output, [
                    '',
                    '',
                    'GRAND TOTAL',
                    $grandTotal,
                ]);

                fclose($output);
            },
            $fileName,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    public function mmsayDepartmentDrawPrint(Request $request)
    {
        $drawDistricts = $this->departmentDrawQuery($request)
            ->get();

        $grandTotal = (int) $drawDistricts->sum('total_assets');

        return view('mmsay.departmentDrawPrint', compact(
            'drawDistricts',
            'grandTotal'
        ));
    }

    public function mmsayDepartmentDrawDocumentView(int $documentId)
    {
        $document = DB::table('property_draw_documents')
            ->select('id', 'original_file_name', 'file_path')
            ->where('id', $documentId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->first();

        abort_if(!$document, 404, 'Draw document not found.');

        $filePath = ltrim((string) $document->file_path, '/');
        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        abort_unless(
            $filePath !== '' && $disk->exists($filePath),
            404,
            'Draw PDF file not found.'
        );

        $fileName = $document->original_file_name
            ?: basename($filePath);

        return $disk->response(
            $filePath,
            $fileName,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'
                    . addslashes($fileName)
                    . '"',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    public function mmsayDepartmentDrawDocumentDownload(int $documentId)
    {
        $document = DB::table('property_draw_documents')
            ->select('id', 'original_file_name', 'file_path')
            ->where('id', $documentId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->first();

        abort_if(!$document, 404, 'Draw document not found.');

        $filePath = ltrim((string) $document->file_path, '/');
        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        abort_unless(
            $filePath !== '' && $disk->exists($filePath),
            404,
            'Draw PDF file not found.'
        );

        return $disk->download(
            $filePath,
            $document->original_file_name ?: basename($filePath),
            [
                'Content-Type' => 'application/pdf',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    public function districtDetails($id)
    {
        $query = DB::table('property_registration as pr')
            ->leftJoin('districts as d', 'd.DistrictId', '=', 'pr.DistrictId')
            ->where('pr.DistrictId', $id)
            ->select(
                'pr.AssetId',
                'pr.AssetName',
                'pr.AssetSize',
                'pr.Unit',
                'd.DistrictName'
            );

        // paginate() already performs the exact count query. Reusing its total
        // avoids executing the same filtered count twice.
        $data = $query->paginate(10);
        $totalRecords = $data->total();

        $districtName = DB::table('districts')
            ->where('DistrictId', $id)
            ->value('DistrictName');

        return view('mmsay.departmentDrawDetails', compact(
            'data',
            'districtName',
            'totalRecords'
        ));
    }

    public function departmentEmiPayments()
    {
        $properties = DB::table('property_auction_detail as pad')

            ->join(
                'property_registration as pr',
                'pad.AssetId',
                '=',
                'pr.AssetId'
            )

            ->join(
                'property_private_purchasers as ppp',
                'pad.PurchaserID',
                '=',
                'ppp.PrivatePurchaserId'
            )

            ->leftJoin(
                'districts as d',
                'pad.DistrictId',
                '=',
                'd.DistrictId'
            )

            ->leftJoin(
                'cities as c',
                'pad.CityId',
                '=',
                'c.CityId'
            )

            ->leftJoin(
                'sectors as s',
                'pad.SectorId',
                '=',
                's.SectorId'
            )

            ->select(
                'pad.PropertyAuctionId',
                'pad.AssetId',
                'pad.FlatCost',
                'pad.ReceivedAmount',
                'pad.BalanceAmount',

                'pr.AssetName',
                'pr.AssetSize',

                'ppp.PrivatePurchaserName',
                'ppp.MobileNo',
                'ppp.ApplicationNo',

                'd.DistrictName as district',
                'c.CityName as city',
                's.SectorName as sector'
            )

            ->where('pad.IsDeleted', 0)
            ->where('pad.IsActive', 1)

            ->orderBy('pad.PropertyAuctionId', 'desc')

            ->paginate(20)

            ->withQueryString();

        return view(
            'mmsay.departmentEmiPayments',
            compact('properties')
        );
    }

    public function departmentPropertyEmiCalculation()
    {
        $districts = DB::table('districts')
            ->where('Is_Active', 1)
            ->where('Is_Deleted', 0)
            ->orderBy('DistrictName')
            ->get();

        return view(
            'mmsay.departmentPropertyEmiCalculation',
            compact('districts')
        );
    }

    public function emiGetCities(Request $request)
    {
        $cities = DB::table('cities')
            ->where('DistrictId', $request->district_id)
            ->where('Is_Active', 1)
            ->where('Is_Deleted', 0)
            ->orderBy('CityName')
            ->get();

        return response()->json($cities);
    }

    public function emiGetSectors(Request $request)
    {
        $sectors = DB::table('city_sector_associations as csa')
            ->join(
                'sectors as s',
                'csa.SectorId',
                '=',
                's.SectorId'
            )
            ->where('csa.CityId', $request->city_id)
            ->where('csa.Is_Active', 1)
            ->where('csa.Is_Deleted', 0)
            ->select(
                's.SectorId',
                's.SectorName'
            )
            ->distinct()
            ->get();

        return response()->json($sectors);
    }

    public function emiGetAssets(Request $request)
    {
        $assets = DB::table('property_registration')
            ->where('DistrictId', $request->district_id)
            ->where('CityId', $request->city_id)
            ->where('SectorId', $request->sector_id)
            ->where('IsActive', 1)
            ->where('IsDeleted', 0)
            ->select(
                'AssetId',
                'AssetName'
            )
            ->orderBy('AssetName')
            ->get();

        return response()->json($assets);
    }

    public function emiGetAssetDetails(Request $request)
    {
        $property = DB::table('property_auction_detail as pad')

            ->join(
                'property_registration as pr',
                'pad.AssetId',
                '=',
                'pr.AssetId'
            )

            ->join(
                'property_private_purchasers as ppp',
                'pad.PurchaserID',
                '=',
                'ppp.PrivatePurchaserId'
            )

            ->leftJoin(
                'installment_due as id',
                'pad.PropertyAuctionId',
                '=',
                'id.PropertyAuctionId'
            )

            ->where('pad.AssetId', $request->asset_id)

            ->select(
                'pad.PropertyAuctionId',
                'pad.FlatCost',
                'pad.ReceivedAmount',
                'pad.BalanceAmount',
                'pr.AssetName',
                'ppp.PrivatePurchaserName',
                DB::raw('MIN(id.OfferOfPossessionDate) as OfferOfPossessionDate')
            )

            ->groupBy(
                'pad.PropertyAuctionId',
                'pad.FlatCost',
                'pad.ReceivedAmount',
                'pad.BalanceAmount',
                'pr.AssetName',
                'ppp.PrivatePurchaserName'
            )

            ->first();

        return response()->json($property);
    }

    public function view($assetId)
    {
        $details = DB::table('property_auction_detail as pad')
            ->join(
                'property_private_purchasers as ppp',
                'pad.PurchaserID',
                '=',
                'ppp.PrivatePurchaserId'
            )
            ->join(
                'property_registration as pr',
                'pad.AssetId',
                '=',
                'pr.AssetId'
            )
            ->leftJoin('em_offices as eo', 'pad.BranchId', '=', 'eo.BranchId')
            ->leftJoin('districts as d', 'pad.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'pad.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'pad.SectorId', '=', 's.SectorId')
            ->where('pad.AssetId', $assetId)
            ->select(
                'pad.*',

                'ppp.PrivatePurchaserName',
                'ppp.PurchaserFatherName',
                'ppp.MobileNo',
                'ppp.ApplicationNo',
                'ppp.PPPId',
                'ppp.MemberID',
                'ppp.CasteCategoryName',
                'ppp.MaritalStatus',
                'ppp.Address',
                'ppp.CreateDate',

                'pr.AssetName',
                'pr.AssetSize',
                'pr.Unit',

                'eo.BranchName',
                'd.DistrictName',
                'c.CityName',
                's.SectorName'
            )
            ->first();

        if (!$details) {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | Payment Summary
        |--------------------------------------------------------------------------
        */

        $totalAmount = $details->FlatCost;

        // Registration amount
        $registrationPaid = $details->ReceivedAmount ?? 0;

        // EMI / Receipt payments
        $receiptPaid = DB::table('cash_receipt_details')
            ->where('asset_number', $assetId)
            ->where('IsDeleted', 0)
            ->sum('total_paid_amount');

        // Total paid
        $totalPaid = $registrationPaid + $receiptPaid;

        // Outstanding
        $outstanding = max(0, $totalAmount - $totalPaid);

        // Completion %
        $completionPercent = $totalAmount > 0
            ? round(($totalPaid / $totalAmount) * 100, 2)
            : 0;

        /*
|--------------------------------------------------------------------------
| Installments
|--------------------------------------------------------------------------
*/

        $receiptList = DB::table('cash_receipt_details')
            ->where('asset_number', $assetId)
            ->where('IsDeleted', 0)
            ->orderBy('created_date')
            ->get();

        $installments = DB::table('installment_due')
            ->where('AssetId', $assetId)
            ->orderBy('InstallmentNumber')
            ->get();

        /*
 |--------------------------------------------------------------------------
 | Installment Statistics (FIFO Logic)
 |--------------------------------------------------------------------------
 */

        $today = now()->toDateString();

        $totalInstallments = $installments->count();

        $paidInstallments = 0;
        $overdueInstallments = 0;
        $upcomingInstallments = 0;

        /*
        |--------------------------------------------------------------------------
        | EMI payment ko FIFO basis par adjust karo
        |--------------------------------------------------------------------------
        |
        | Example:
        | EMI = 2222.22
        | Paid = 44440
        | => 20 EMI Paid
        |
        */

        $receiptIndex = 0;
        $receiptCount = $receiptList->count();

        foreach ($installments as $emi) {

            if ($receiptIndex < $receiptCount) {

                $receipt = $receiptList[$receiptIndex];

                $emi->status = 'Paid';
                $emi->PaidOn = $receipt->created_date;
                $emi->receipt_number = $receipt->receipt_number;

                $paidInstallments++;
                $receiptIndex++;

            } else {

                $emi->PaidOn = '-';
                $emi->receipt_number = '-';

                if ($emi->DueDate <= $today) {

                    $emi->status = 'Overdue';
                    $overdueInstallments++;

                } else {

                    $emi->status = 'Upcoming';
                    $upcomingInstallments++;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Safety Recalculation
        |--------------------------------------------------------------------------
        */

        $paidInstallments = $installments
            ->where('status', 'Paid')
            ->count();

        $overdueInstallments = $installments
            ->where('status', 'Overdue')
            ->count();

        $upcomingInstallments = $installments
            ->where('status', 'Upcoming')
            ->count()
            + $installments->where('status', 'Partially Paid')->count();

        /*
        |--------------------------------------------------------------------------
        | Receipt History
        |--------------------------------------------------------------------------
        */

        $receipts = DB::table('cash_receipt_details')
            ->where('asset_number', $assetId)
            ->where('IsDeleted', 0)
            ->select(
                'id',
                'receipt_number',
                'total_paid_amount',
                'created_date'
            )
            ->orderBy('created_date', 'desc')
            ->get();

        // Registration Payment Entry
        if ($registrationPaid > 0) {

            $receipts->prepend((object) [
                'id' => 0,
                'receipt_number' => 'REGISTRATION',
                'total_paid_amount' => $registrationPaid,
                'created_date' => $details->CreateDate,
            ]);
        }

        $property = $details;
        $remainingAmount = $outstanding;

        return view(
            'mmsay.physicalPossessionView',
            compact(
                'details',
                'property',
                'totalAmount',
                'totalPaid',
                'outstanding',
                'remainingAmount',
                'completionPercent',
                'installments',
                'receipts',
                'totalInstallments',
                'paidInstallments',
                'overdueInstallments',
                'upcomingInstallments'
            )
        );
    }

    private function possessionStatusSql(string $alias = 'ppa'): string
    {
        $status = "LOWER(REPLACE(REPLACE(TRIM(COALESCE({$alias}.status, '')), '-', '_'), ' ', '_'))";
        $physical = "LOWER(REPLACE(REPLACE(TRIM(COALESCE({$alias}.physical_possession_status, '')), '-', '_'), ' ', '_'))";

        return "
        CASE
            /*
             * Final department verification.
             * Site Verified is intentionally not final Verified.
             */
            WHEN {$alias}.approved_at IS NOT NULL
                OR {$alias}.approved_by IS NOT NULL
                OR {$physical} IN (
                    'verified',
                    'approved',
                    'completed',
                    'possession_completed',
                    'handed_over'
                )
                OR {$status} IN (
                    'verified',
                    'approved',
                    'completed',
                    'possession_completed',
                    'handed_over'
                )
                THEN 'verified'

            /*
             * Site engineer verification is complete; department/e-possession
             * processing is still pending.
             */
            WHEN {$alias}.verified_at IS NOT NULL
                OR {$alias}.verified_by IS NOT NULL
                OR {$physical} IN (
                    'site_verified',
                    'verification_completed',
                    'possession_pending',
                    'pending_possession',
                    'e_possession_pending',
                    'ready_for_possession'
                )
                OR {$status} IN (
                    'site_verified',
                    'verification_completed',
                    'possession_pending',
                    'pending_possession',
                    'e_possession_pending',
                    'ready_for_possession'
                )
                THEN 'possession_pending'

            /* Citizen did not attend the selected visit. */
            WHEN {$physical} IN (
                    'visit_missed',
                    'citizen_absent',
                    'no_show',
                    'not_visited'
                )
                OR {$status} IN (
                    'visit_missed',
                    'citizen_absent',
                    'no_show',
                    'not_visited'
                )
                THEN 'visit_missed'

            /* A new visit has been assigned after a missed/cancelled visit. */
            WHEN {$physical} IN (
                    'visit_rescheduled',
                    'rescheduled'
                )
                OR {$status} IN (
                    'visit_rescheduled',
                    'rescheduled'
                )
                THEN 'visit_rescheduled'

            /*
             * Sonia-type case: citizen has selected one offered visit slot.
             * Explicit workflow text has priority over prefilled date fields.
             */
            WHEN {$physical} IN (
                    'pending_for_verify',
                    'pending_verification',
                    'verification_pending',
                    'submitted_for_verification',
                    'date_selected',
                    'slot_selected',
                    'citizen_visit_confirmed'
                )
                OR {$physical} LIKE '%slot_selected%'
                OR {$physical} LIKE '%date_selected%'
                OR {$status} IN (
                    'pending_for_verify',
                    'pending_verification',
                    'verification_pending',
                    'submitted_for_verification',
                    'date_selected',
                    'slot_selected',
                    'citizen_visit_confirmed'
                )
                OR {$status} LIKE '%slot_selected%'
                OR {$status} LIKE '%date_selected%'
                THEN 'pending_verification'

            /*
             * Munni/Kusum-type case: slots and a default visit date may exist,
             * but the citizen has not selected a slot yet.
             */
            WHEN {$physical} IN ('scheduled','schedule','visit_scheduled','slots_offered')
                OR {$status} IN ('scheduled','schedule','visit_scheduled','slots_offered')
                THEN 'scheduled'

            /*
             * Fallback for older rows where workflow text was not updated.
             * Explicit Slot Selected and Visit Scheduled values above always win.
             */
            WHEN {$alias}.citizen_visit_date IS NOT NULL
                THEN 'pending_verification'

            WHEN {$alias}.visit_slot_1 IS NOT NULL
                OR {$alias}.visit_slot_2 IS NOT NULL
                OR {$alias}.visit_slot_3 IS NOT NULL
                THEN 'scheduled'

            ELSE 'awaiting_schedule'
        END
    ";
    }

    private function possessionFilters(Request $request): array
    {
        return [
            'district_id' => $request->integer('district_id') ?: null,
            'city_id' => $request->integer('city_id') ?: null,
            'sector_id' => $request->integer('sector_id') ?: null,
            'status' => trim((string) $request->input('status')),
            'search' => trim((string) $request->input('search')),
        ];
    }

    private function eligiblePossessionQuery(Request $request, bool $applyStatus = true)
    {
        $filters = $this->possessionFilters($request);
        $statusSql = $this->possessionStatusSql();

        /*
        | Start from the 11,984 verified beneficiaries and aggregate receipts
        | in the same query. No repeated beneficiary/candidate subqueries.
        */
        $assetPayments = DB::table('mmsay_eligible_beneficiaries as eligible_meb')
            ->join('property_private_purchasers as eligible_ppp', function ($join) {
                $join->on(
                    'eligible_ppp.ApplicationNo',
                    '=',
                    'eligible_meb.application_number'
                )
                    ->where('eligible_ppp.IsDeleted', 0);
            })
            ->join('property_auction_detail as pad', function ($join) {
                $join->on('pad.AssetId', '=', 'eligible_ppp.Flat_Id')
                    ->on('pad.PurchaserID', '=', 'eligible_ppp.PrivatePurchaserId')
                    ->where('pad.IsDeleted', 0)
                    ->where('pad.IsActive', 1);
            })
            ->leftJoin('cash_receipt_details as receipt', function ($join) {
                $join->on('receipt.asset_number', '=', 'pad.AssetId')
                    ->where('receipt.IsDeleted', 0)
                    ->where('receipt.IsActive', 1);
            })
            ->selectRaw("
            MAX(pad.PropertyAuctionId) AS PropertyAuctionId,
            pad.AssetId,
            pad.FlatCost,
            pad.ReceivedAmount,
            MAX(pad.PurchaserID) AS PurchaserID,
            MAX(pad.BranchId) AS BranchId,
            MAX(pad.DistrictId) AS DistrictId,
            MAX(pad.CityId) AS CityId,
            MAX(pad.SectorId) AS SectorId,
            COALESCE(SUM(receipt.total_paid_amount), 0) AS receipt_total,
            (
                COALESCE(pad.ReceivedAmount, 0)
                + COALESCE(SUM(receipt.total_paid_amount), 0)
            ) AS total_received
        ")
            ->whereNotNull('eligible_meb.application_number')
            ->when($filters['district_id'], fn($q, $id) => $q->where('pad.DistrictId', $id))
            ->when($filters['city_id'], fn($q, $id) => $q->where('pad.CityId', $id))
            ->when($filters['sector_id'], fn($q, $id) => $q->where('pad.SectorId', $id))
            ->groupBy(
                'pad.AssetId',
                'pad.FlatCost',
                'pad.ReceivedAmount'
            );

        $latestApplications = DB::table('physical_possession_applications')
            ->selectRaw('asset_id, MAX(id) AS application_id')
            ->groupBy('asset_id');

        $query = DB::query()
            ->fromSub($assetPayments, 'payments')
            ->leftJoin('property_registration as pr', 'pr.AssetId', '=', 'payments.AssetId')
            ->join('property_private_purchasers as ppp', function ($join) {
                $join->on('ppp.PrivatePurchaserId', '=', 'payments.PurchaserID')
                    ->where('ppp.IsDeleted', 0);
            })
            ->leftJoin('districts as d', 'd.DistrictId', '=', 'payments.DistrictId')
            ->leftJoin('cities as c', 'c.CityId', '=', 'payments.CityId')
            ->leftJoin('sectors as s', 's.SectorId', '=', 'payments.SectorId')
            ->leftJoinSub($latestApplications, 'latest_ppa', function ($join) {
                $join->on('latest_ppa.asset_id', '=', 'payments.AssetId');
            })
            ->leftJoin('physical_possession_applications as ppa', 'ppa.id', '=', 'latest_ppa.application_id')
            ->where('payments.total_received', '>=', 60000)
            ->when($filters['search'], function ($q) use ($filters) {
                $rawSearch = $filters['search'];
                $search = '%' . addcslashes($rawSearch, '%_\\') . '%';

                $q->where(function ($sub) use ($rawSearch, $search) {
                    if (ctype_digit($rawSearch)) {
                        $sub->where('payments.AssetId', $rawSearch)
                            ->orWhere('ppp.MobileNo', $rawSearch)
                            ->orWhere('ppp.ApplicationNo', $rawSearch)
                            ->orWhere('ppa.possession_id', $rawSearch);
                    } else {
                        $sub->where('pr.AssetName', 'like', $search)
                            ->orWhere('ppp.PrivatePurchaserName', 'like', $search)
                            ->orWhere('ppa.application_number', 'like', $search)
                            ->orWhere('ppa.possession_id', 'like', $search);
                    }
                });
            });

        if (
            $applyStatus && in_array($filters['status'], [
                'awaiting_schedule',
                'scheduled',
                'pending_verification',
                'visit_missed',
                'visit_rescheduled',
                'possession_pending',
                'verified',
            ], true)
        ) {
            $query->whereRaw("({$statusSql}) = ?", [$filters['status']]);
        }

        return $query;
    }

    private function eligiblePossessionSelect($query)
    {
        $statusSql = $this->possessionStatusSql();

        return $query
            ->select([
                'payments.PropertyAuctionId as property_auction_id',
                'payments.AssetId as asset_id',
                'payments.FlatCost as flat_cost',
                'payments.ReceivedAmount as initial_received',
                'pr.AssetName as asset_name',
                'pr.AssetSize as asset_size',
                'pr.Unit as asset_unit',
                'payments.DistrictId as district_id',
                'payments.CityId as city_id',
                'payments.SectorId as sector_id',
                'd.DistrictName as district_name',
                'c.CityName as city_name',
                's.SectorName as sector_name',
                'ppp.PrivatePurchaserId as private_purchaser_id',
                'ppp.PrivatePurchaserName as applicant_name',
                'ppp.PurchaserFatherName as father_name',
                'ppp.MobileNo as mobile',
                'ppp.ApplicationNo as application_number',
                'ppp.ApplicationNo as purchaser_application_number',
                'ppp.PPPId as ppp_id',
                'ppp.MemberID as member_id',
                'ppp.Address as address',
                'ppa.id as application_id',
                'ppa.secure_id',
                'ppa.possession_id',
                'ppa.application_number as physical_application_number',
                'ppa.mmsay_application_no',
                'ppa.slip_id',
                'ppa.registration_details',
                'ppa.status',
                'ppa.physical_possession_status',
                'ppa.possession_date',
                'ppa.meeting_slot',
                'ppa.plot_image',
                'ppa.latitude',
                'ppa.longitude',
                'ppa.image_capture_datetime',
                'ppa.possession_certificate',
                'ppa.site_engineer_file',
                'ppa.verified_by',
                'ppa.verified_at',
                'ppa.remarks',
                'ppa.approved_by',
                'ppa.approved_at',
                'ppa.citizen_visit_date',
                'ppa.visit_slot_1',
                'ppa.visit_slot_2',
                'ppa.visit_slot_3',
                'ppa.visit_instructions',
                'ppa.created_at',
                'ppa.updated_at',
            ])
            ->selectRaw('payments.receipt_total AS receipt_total')
            ->selectRaw('payments.total_received AS received_amount')
            ->selectRaw("({$statusSql}) AS workflow_status");
    }

    public function physicalPossessionEligible(Request $request)
    {
        $filters = $this->possessionFilters($request);
        $statusSql = $this->possessionStatusSql();

        // Status cards and list use the exact same ₹60,000+ eligible base query.
        $statusStats = $this->eligiblePossessionQuery($request, false)
            ->selectRaw("
            COUNT(*) AS total_records,
            SUM(CASE WHEN ({$statusSql}) = 'awaiting_schedule' THEN 1 ELSE 0 END) AS awaiting_schedule,
            SUM(CASE WHEN ({$statusSql}) = 'scheduled' THEN 1 ELSE 0 END) AS scheduled,
            SUM(CASE WHEN ({$statusSql}) = 'pending_verification' THEN 1 ELSE 0 END) AS pending_verification,
            SUM(CASE WHEN ({$statusSql}) = 'visit_missed' THEN 1 ELSE 0 END) AS visit_missed,
            SUM(CASE WHEN ({$statusSql}) = 'visit_rescheduled' THEN 1 ELSE 0 END) AS visit_rescheduled,
            SUM(CASE WHEN ({$statusSql}) = 'possession_pending' THEN 1 ELSE 0 END) AS possession_pending,
            SUM(CASE WHEN ({$statusSql}) = 'verified' THEN 1 ELSE 0 END) AS verified
        ")
            ->first();

        $applications = $this->eligiblePossessionSelect(
            $this->eligiblePossessionQuery($request)
        )
            ->orderByDesc('payments.PropertyAuctionId')
            ->paginate(self::LIST_PAGE_SIZE)
            ->withQueryString();

        $districts = DB::table('districts')
            ->select('DistrictId', 'DistrictName')
            ->where('Is_Deleted', 0)
            ->where('Is_Active', 1)
            ->orderBy('DistrictName')
            ->get();

        // Dependency: no district means no city list.
        $cities = collect();
        if ($filters['district_id']) {
            $cities = DB::table('cities')
                ->select('CityId', 'CityName')
                ->where('DistrictId', $filters['district_id'])
                ->where('Is_Deleted', 0)
                ->where('Is_Active', 1)
                ->orderBy('CityName')
                ->get();
        }

        // Dependency: no city means no sector/village list.
        $sectors = collect();
        if ($filters['city_id']) {
            $sectors = DB::table('city_sector_associations as csa')
                ->join('sectors as s', 's.SectorId', '=', 'csa.SectorId')
                ->select('s.SectorId', 's.SectorName')
                ->where('csa.CityId', $filters['city_id'])
                ->where('csa.Is_Deleted', 0)
                ->where('csa.Is_Active', 1)
                ->where('s.Is_Deleted', 0)
                ->where('s.Is_Active', 1)
                ->distinct()
                ->orderBy('s.SectorName')
                ->get();
        }

        return view('mmsay.physicalPossessionEligible', compact(
            'applications',
            'statusStats',
            'filters',
            'districts',
            'cities',
            'sectors'
        ));
    }

    public function physicalPossessionShow(int $assetId)
    {
        $application = $this->eligiblePossessionSelect(
            $this->eligiblePossessionQuery(request(), false)
                ->where('payments.AssetId', $assetId)
        )->first();

        abort_if(!$application, 404);

        $cashReceipts = DB::table('cash_receipt_details')
            ->select('id', 'receipt_number', 'total_paid_amount', 'created_date')
            ->where('asset_number', $assetId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->orderByDesc('created_date')
            ->get();

        $initialReceived = (float) ($application->initial_received ?? 0);
        $receiptTotal = (float) $cashReceipts->sum('total_paid_amount');
        $flatCost = (float) ($application->flat_cost ?? 0);
        $totalReceived = $initialReceived + $receiptTotal;
        $pendingAmount = max($flatCost - $totalReceived, 0);
        $workflowStatus = $application->workflow_status;
        $auction = $application;

        return view('mmsay.physicalPossessionShow', compact(
            'application',
            'auction',
            'cashReceipts',
            'initialReceived',
            'receiptTotal',
            'flatCost',
            'totalReceived',
            'pendingAmount',
            'workflowStatus'
        ));
    }

    public function updatePhysicalPossessionVisit(Request $request, int $applicationId)
    {
        $validated = $request->validate([
            'visit_action' => [
                'required',
                'in:visit_missed,visit_rescheduled,visit_attended',
            ],
            'visit_datetime' => [
                'nullable',
                'date',
                'required_if:visit_action,visit_rescheduled',
            ],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $application = DB::table('physical_possession_applications')
            ->where('id', $applicationId)
            ->first();

        abort_if(!$application, 404);

        $update = [
            'remarks' => $validated['remarks'] ?? $application->remarks,
            'updated_at' => now(),
        ];

        switch ($validated['visit_action']) {
            case 'visit_missed':
                $update['status'] = 'pending';
                $update['physical_possession_status'] = 'Visit Missed';
                break;

            case 'visit_rescheduled':
                $visitDateTime = \Carbon\Carbon::parse($validated['visit_datetime']);

                $update['status'] = 'pending';
                $update['physical_possession_status'] = 'Visit Rescheduled';
                $update['citizen_visit_date'] = $visitDateTime;
                $update['possession_date'] = $visitDateTime->toDateString();
                $update['meeting_slot'] = $visitDateTime;
                break;

            case 'visit_attended':
                $update['status'] = 'pending';
                $update['physical_possession_status'] = 'Pending Verification';
                break;
        }

        DB::table('physical_possession_applications')
            ->where('id', $applicationId)
            ->update($update);

        return back()->with('success', 'Visit status updated successfully.');
    }

    public function physicalPossessionCsv(Request $request): StreamedResponse
    {
        $fileName = 'physical-possession-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'S.No.',
                'Physical Application No.',
                'Asset ID',
                'Asset',
                'Purchaser Application No.',
                'Applicant',
                'Mobile',
                'District',
                'City',
                'Sector/Village',
                'Received Amount',
                'Workflow Status',
                'Possession Date',
                'Meeting Slot',
            ]);

            $serial = 1;
            $query = $this->eligiblePossessionSelect(
                $this->eligiblePossessionQuery($request)
            );

            $query->chunkById(1000, function ($rows) use (&$serial, $handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $serial++,
                        $row->physical_application_number ?: $row->possession_id,
                        $row->asset_id,
                        $row->asset_name,
                        $row->purchaser_application_number,
                        $row->applicant_name,
                        $row->mobile,
                        $row->district_name,
                        $row->city_name,
                        $row->sector_name,
                        $row->received_amount,
                        ucwords(str_replace('_', ' ', $row->workflow_status)),
                        $row->possession_date,
                        $row->meeting_slot,
                    ]);
                }
            }, 'payments.PropertyAuctionId', 'property_auction_id');

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function physicalPossessionPrint(Request $request)
    {
        $perChunk = self::PRINT_CHUNK_SIZE;
        $afterId = max(0, $request->integer('after_id'));

        $rows = $this->eligiblePossessionSelect(
            $this->eligiblePossessionQuery($request)
                ->when($afterId, fn($q) => $q->where('payments.PropertyAuctionId', '>', $afterId))
        )
            ->orderBy('payments.PropertyAuctionId')
            ->limit($perChunk + 1)
            ->get();

        $hasMore = $rows->count() > $perChunk;
        $applications = $rows->take($perChunk);
        $nextAfterId = $hasMore ? $applications->last()->property_auction_id : null;

        return view('mmsay.physicalPossessionPrint', compact(
            'applications',
            'hasMore',
            'nextAfterId'
        ));
    }

    private function notEligiblePossessionQuery(Request $request)
    {
        /*
         * One pass only: beneficiary -> purchaser -> auction -> receipts.
         */
        $paymentSummary = DB::table(
            'mmsay_eligible_beneficiaries as meb'
        )
            ->join('property_private_purchasers as ppp', function ($join) {
                $join->on(
                    'ppp.ApplicationNo',
                    '=',
                    'meb.application_number'
                )
                    ->where('ppp.IsDeleted', 0);
            })
            ->join('property_auction_detail as pad', function ($join) {
                $join->on('pad.AssetId', '=', 'ppp.Flat_Id')
                    ->on(
                        'pad.PurchaserID',
                        '=',
                        'ppp.PrivatePurchaserId'
                    )
                    ->where('pad.IsDeleted', 0)
                    ->where('pad.IsActive', 1);
            })
            ->join('property_registration as pr', function ($join) {
                $join->on('pr.AssetId', '=', 'pad.AssetId')
                    ->where('pr.IsDeleted', 0);
            })
            ->leftJoin('cash_receipt_details as cr', function ($join) {
                $join->on('cr.asset_number', '=', 'pad.AssetId')
                    ->where('cr.IsDeleted', 0)
                    ->where('cr.IsActive', 1);
            })
            ->leftJoin(
                'districts as d',
                'd.DistrictId',
                '=',
                'ppp.DistrictId'
            )
            ->leftJoin(
                'cities as c',
                'c.CityId',
                '=',
                'ppp.CityId'
            )
            ->leftJoin(
                'sectors as s',
                's.SectorId',
                '=',
                'ppp.SectorId'
            )
            ->whereNotNull('meb.application_number')

            /*
             * Initial received already ₹60,000+ hai to receipts
             * calculate karne ke baad bhi Not Eligible nahi hoga.
             */
            ->whereRaw(
                'COALESCE(pad.ReceivedAmount, 0) < 60000'
            )
            // Location filters
            ->when(
                $request->filled('district_id'),
                fn($query) => $query->where(
                    'ppp.DistrictId',
                    $request->integer('district_id')
                )
            )
            ->when(
                $request->filled('city_id'),
                fn($query) => $query->where(
                    'ppp.CityId',
                    $request->integer('city_id')
                )
            )
            ->when(
                $request->filled('sector_id'),
                fn($query) => $query->where(
                    'ppp.SectorId',
                    $request->integer('sector_id')
                )
            );

        $search = trim((string) $request->input('search'));

        if ($search !== '') {
            $escapedSearch = addcslashes($search, '%_\\');
            $like = "%{$escapedSearch}%";

            $paymentSummary->where(
                function ($query) use ($search, $like) {
                    if (ctype_digit($search)) {
                        $query
                            ->where('ppp.ApplicationNo', $search)
                            ->orWhere('ppp.MobileNo', $search)
                            ->orWhere('pad.AssetId', $search);
                    } else {
                        $query
                            ->where(
                                'ppp.PrivatePurchaserName',
                                'like',
                                $like
                            )
                            ->orWhere(
                                'pr.AssetName',
                                'like',
                                $like
                            )
                            ->orWhere('ppp.PPPId', 'like', $like)
                            ->orWhere('ppp.MemberID', 'like', $like);
                    }
                }
            );
        }

        $paymentSummary
            ->groupBy(
                'pad.PropertyAuctionId',
                'pad.AssetId',
                'pad.FlatCost',
                'pad.ReceivedAmount',
                'pr.AssetName',
                'pr.AssetSize',
                'pr.Unit',
                'ppp.ApplicationNo',
                'ppp.PrivatePurchaserName',
                'ppp.PurchaserFatherName',
                'ppp.MobileNo',
                'ppp.PPPId',
                'ppp.MemberID',
                'ppp.DistrictId',
                'ppp.CityId',
                'ppp.SectorId',
                'd.DistrictName',
                'c.CityName',
                's.SectorName'
            )
            ->havingRaw('(
                COALESCE(pad.ReceivedAmount, 0)
                + COALESCE(SUM(cr.total_paid_amount), 0)
            ) < 60000');

        $paymentSummary
            ->select([
                'pad.PropertyAuctionId as property_auction_id',
                'pad.AssetId as asset_id',
                'pad.FlatCost as flat_cost',
                'pad.ReceivedAmount as initial_received',

                'pr.AssetName as asset_name',
                'pr.AssetSize as asset_size',
                'pr.Unit as asset_unit',

                'ppp.ApplicationNo as application_number',
                'ppp.PrivatePurchaserName as applicant_name',
                'ppp.PurchaserFatherName as father_name',
                'ppp.MobileNo as mobile',
                'ppp.PPPId as ppp_id',
                'ppp.MemberID as member_id',

                'ppp.DistrictId as district_id',
                'ppp.CityId as city_id',
                'ppp.SectorId as sector_id',

                'd.DistrictName as district_name',
                'c.CityName as city_name',
                's.SectorName as sector_name',
            ])
            ->selectRaw(
                'COALESCE(SUM(cr.total_paid_amount), 0) AS cash_received'
            )
            ->selectRaw('
            (
                COALESCE(pad.ReceivedAmount, 0)
                + COALESCE(SUM(cr.total_paid_amount), 0)
            ) AS received_amount
        ')
            ->selectRaw('
            GREATEST(
                COALESCE(pad.FlatCost, 0)
                - (
                    COALESCE(pad.ReceivedAmount, 0)
                    + COALESCE(SUM(cr.total_paid_amount), 0)
                ),
                0
            ) AS pending_amount
        ')
            ->selectRaw('
            GREATEST(
                60000
                - (
                    COALESCE(pad.ReceivedAmount, 0)
                    + COALESCE(SUM(cr.total_paid_amount), 0)
                ),
                0
            ) AS eligibility_shortfall
        ');

        return DB::query()
            ->fromSub($paymentSummary, 'ps');
    }

    public function physicalPossessionNotEligible(Request $request)
    {
        $districtId = $request->integer('district_id') ?: null;
        $cityId = $request->integer('city_id') ?: null;
        $sectorId = $request->integer('sector_id') ?: null;
        $search = trim((string) $request->input('search'));

        $sortOrder = strtolower(
            (string) $request->input('sort_order', 'desc')
        );

        $sortOrder = in_array(
            $sortOrder,
            ['asc', 'desc'],
            true
        ) ? $sortOrder : 'desc';

        $filters = [
            'district_id' => $districtId,
            'city_id' => $cityId,
            'sector_id' => $sectorId,
            'search' => $search,
            'sort_order' => $sortOrder,
        ];

        $districts = DB::table('districts')
            ->select('DistrictId', 'DistrictName')
            ->where('Is_Deleted', 0)
            ->where('Is_Active', 1)
            ->orderBy('DistrictName')
            ->get();

        $cities = $districtId
            ? DB::table('cities')
                ->select('CityId', 'CityName')
                ->where('DistrictId', $districtId)
                ->where('Is_Deleted', 0)
                ->where('Is_Active', 1)
                ->orderBy('CityName')
                ->get()
            : collect();

        $sectors = $cityId
            ? DB::table('city_sector_associations as csa')
                ->join(
                    'sectors as s',
                    's.SectorId',
                    '=',
                    'csa.SectorId'
                )
                ->select('s.SectorId', 's.SectorName')
                ->where('csa.CityId', $cityId)
                ->where('csa.Is_Deleted', 0)
                ->where('csa.Is_Active', 1)
                ->where('s.Is_Deleted', 0)
                ->where('s.Is_Active', 1)
                ->distinct()
                ->orderBy('s.SectorName')
                ->get()
            : collect();

        $applications = $this
            ->notEligiblePossessionQuery($request)
            ->orderBy('ps.property_auction_id', $sortOrder)
            ->paginate(self::LIST_PAGE_SIZE)
            ->withQueryString();

        return view(
            'mmsay.physicalPossessionNotEligible',
            compact(
                'applications',
                'districts',
                'cities',
                'sectors',
                'districtId',
                'cityId',
                'sectorId',
                'search',
                'sortOrder',
                'filters'
            )
        );
    }

    public function physicalPossessionNotEligibleCsv(
        Request $request
    ) {
        $sortOrder = strtolower(
            (string) $request->input('sort_order', 'desc')
        );

        $sortOrder = in_array(
            $sortOrder,
            ['asc', 'desc'],
            true
        ) ? $sortOrder : 'desc';

        $fileName = 'physical-possession-not-eligible-'
            . $sortOrder
            . '-'
            . now()->format('Ymd-His')
            . '.csv';

        return response()->streamDownload(
            function () use ($request, $sortOrder) {
                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    throw new RuntimeException(
                        'Unable to open CSV output stream.'
                    );
                }

                fwrite($handle, "\xEF\xBB\xBF");

                fputcsv($handle, [
                    'S.No.',
                    'Asset ID',
                    'Property',
                    'Application No.',
                    'Applicant',
                    'Mobile',
                    'District',
                    'City',
                    'Sector',
                    'Total Cost',
                    'Initial Received',
                    'Cash Received',
                    'Total Received',
                    'Property Pending',
                    'Required for ₹60,000',
                ]);

                $serial = 1;
                $lastId = null;

                $baseQuery = $this->notEligiblePossessionQuery(
                    $request
                );

                while (true) {
                    $query = clone $baseQuery;

                    if ($lastId !== null) {
                        $query->where(
                            'ps.property_auction_id',
                            $sortOrder === 'asc' ? '>' : '<',
                            $lastId
                        );
                    }

                    $rows = $query
                        ->orderBy(
                            'ps.property_auction_id',
                            $sortOrder
                        )
                        ->limit(self::EXPORT_CHUNK_SIZE)
                        ->get();

                    if ($rows->isEmpty()) {
                        break;
                    }

                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $serial++,
                            $row->asset_id,
                            $row->asset_name,
                            $row->application_number,
                            $row->applicant_name,
                            $row->mobile,
                            $row->district_name,
                            $row->city_name,
                            $row->sector_name,
                            $row->flat_cost,
                            $row->initial_received,
                            $row->cash_received,
                            $row->received_amount,
                            $row->pending_amount,
                            $row->eligibility_shortfall,
                        ]);
                    }

                    $lastId = (int) (
                        $rows->last()->property_auction_id
                    );

                    if (
                        $rows->count()
                        < self::EXPORT_CHUNK_SIZE
                    ) {
                        break;
                    }
                }

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }

    public function physicalPossessionNotEligiblePrint(
        Request $request
    ) {
        $sortOrder = strtolower(
            (string) $request->input('sort_order', 'desc')
        );

        $sortOrder = in_array(
            $sortOrder,
            ['asc', 'desc'],
            true
        ) ? $sortOrder : 'desc';

        $perChunk = self::PRINT_CHUNK_SIZE;
        $afterId = max(0, $request->integer('after_id'));

        $query = $this->notEligiblePossessionQuery($request);

        if ($afterId > 0) {
            $query->where(
                'ps.property_auction_id',
                $sortOrder === 'asc' ? '>' : '<',
                $afterId
            );
        }

        $rows = $query
            ->orderBy(
                'ps.property_auction_id',
                $sortOrder
            )
            ->limit($perChunk + 1)
            ->get();

        $hasMore = $rows->count() > $perChunk;

        $applications = $rows
            ->take($perChunk)
            ->values();

        $nextAfterId = $hasMore
            && $applications->isNotEmpty()
            ? $applications->last()->property_auction_id
            : null;

        return view(
            'mmsay.physicalPossessionNotEligiblePrint',
            compact(
                'applications',
                'hasMore',
                'nextAfterId',
                'sortOrder'
            )
        );
    }

    private function fullPaidPropertiesQuery(Request $request)
    {
        return $this->eligiblePaymentPropertiesQuery(
            $request,
            'full'
        );
    }

    public function fullPaidProperties(Request $request)
    {
        $districtId = $request->integer('district_id') ?: null;
        $cityId = $request->integer('city_id') ?: null;
        $sectorId = $request->integer('sector_id') ?: null;
        $search = trim((string) $request->input('search'));

        $districts = DB::table('districts')
            ->select('DistrictId', 'DistrictName')
            ->where('Is_Deleted', 0)
            ->where('Is_Active', 1)
            ->orderBy('DistrictName')
            ->get();

        $cities = $districtId
            ? DB::table('cities')
                ->select('CityId', 'CityName')
                ->where('DistrictId', $districtId)
                ->where('Is_Deleted', 0)
                ->where('Is_Active', 1)
                ->orderBy('CityName')
                ->get()
            : collect();

        $sectors = $cityId
            ? DB::table('city_sector_associations as csa')
                ->join('sectors as s', 's.SectorId', '=', 'csa.SectorId')
                ->select('s.SectorId', 's.SectorName')
                ->where('csa.CityId', $cityId)
                ->where('csa.Is_Deleted', 0)
                ->where('csa.Is_Active', 1)
                ->where('s.Is_Deleted', 0)
                ->where('s.Is_Active', 1)
                ->distinct()
                ->orderBy('s.SectorName')
                ->get()
            : collect();

        $properties = $this->paginatePaymentQuery(
            $this->fullPaidPropertiesQuery($request),
            $request
        );

        return view('mmsay.fullPaidProperties', compact(
            'properties',
            'districts',
            'cities',
            'sectors',
            'districtId',
            'cityId',
            'sectorId',
            'search'
        ));
    }

    public function fullPaidPropertiesCsv(Request $request)
    {
        $fileName = 'full-paid-properties-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'S.No.',
                'Asset ID',
                'Application No.',
                'Applicant',
                'Mobile',
                'Property',
                'District',
                'City',
                'Sector',
                'Flat Cost',
                'Initial Received',
                'Cash Receipts',
                'Total Paid',
                'Excess Amount',
            ]);

            $serial = 1;

            $this->fullPaidPropertiesQuery($request)
                ->orderByDesc('ps.PropertyAuctionId')
                ->chunkByIdDesc(self::EXPORT_CHUNK_SIZE, function ($rows) use ($handle, &$serial) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $serial++,
                            $row->asset_id,
                            $row->application_number,
                            $row->applicant_name,
                            $row->mobile,
                            $row->asset_name,
                            $row->district_name,
                            $row->city_name,
                            $row->sector_name,
                            $row->flat_cost,
                            $row->initial_received,
                            $row->cash_received,
                            $row->total_paid,
                            $row->excess_amount,
                        ]);
                    }
                }, 'ps.PropertyAuctionId', 'property_auction_id');

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function fullPaidPropertiesPrint(Request $request)
    {
        $perChunk = self::PRINT_CHUNK_SIZE;
        $afterId = max(0, $request->integer('after_id'));

        $rows = $this->fullPaidPropertiesQuery($request)
            ->when(
                $afterId > 0,
                fn($query) => $query->where('ps.PropertyAuctionId', '<', $afterId)
            )
            ->orderByDesc('ps.PropertyAuctionId')
            ->limit($perChunk + 1)
            ->get();

        $hasMore = $rows->count() > $perChunk;
        $properties = $rows->take($perChunk)->values();
        $nextAfterId = $hasMore ? $properties->last()->property_auction_id : null;

        return view('mmsay.fullPaidPropertiesPrint', compact(
            'properties',
            'hasMore',
            'nextAfterId'
        ));
    }

    private function partialPaidPropertiesQuery(Request $request)
    {
        return $this->eligiblePaymentPropertiesQuery(
            $request,
            'partial'
        );
    }

    public function pendingProperties(Request $request)
    {
        $districtId = $request->integer('district_id') ?: null;
        $cityId = $request->integer('city_id') ?: null;
        $sectorId = $request->integer('sector_id') ?: null;
        $search = trim((string) $request->input('search'));

        $districts = DB::table('districts')
            ->select('DistrictId', 'DistrictName')
            ->where('Is_Deleted', 0)
            ->where('Is_Active', 1)
            ->orderBy('DistrictName')
            ->get();

        $cities = $districtId
            ? DB::table('cities')
                ->select('CityId', 'CityName')
                ->where('DistrictId', $districtId)
                ->where('Is_Deleted', 0)
                ->where('Is_Active', 1)
                ->orderBy('CityName')
                ->get()
            : collect();

        $sectors = $cityId
            ? DB::table('city_sector_associations as csa')
                ->join('sectors as s', 's.SectorId', '=', 'csa.SectorId')
                ->select('s.SectorId', 's.SectorName')
                ->where('csa.CityId', $cityId)
                ->where('csa.Is_Deleted', 0)
                ->where('csa.Is_Active', 1)
                ->where('s.Is_Deleted', 0)
                ->where('s.Is_Active', 1)
                ->distinct()
                ->orderBy('s.SectorName')
                ->get()
            : collect();

        $properties = $this->paginatePaymentQuery(
            $this->partialPaidPropertiesQuery($request),
            $request
        );

        return view('mmsay.pendingProperties', compact(
            'properties',
            'districts',
            'cities',
            'sectors',
            'districtId',
            'cityId',
            'sectorId',
            'search'
        ));
    }

    public function partialPaidPropertiesCsv(Request $request)
    {
        $fileName = 'partial-paid-properties-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'S.No.',
                'Asset ID',
                'Application No.',
                'Applicant',
                'Mobile',
                'Property',
                'District',
                'City',
                'Sector',
                'Flat Cost',
                'Initial Received',
                'Cash Receipts',
                'Total Paid',
                'Pending Amount',
            ]);

            $serial = 1;

            $this->partialPaidPropertiesQuery($request)
                ->orderByDesc('ps.PropertyAuctionId')
                ->chunkByIdDesc(self::EXPORT_CHUNK_SIZE, function ($rows) use ($handle, &$serial) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $serial++,
                            $row->asset_id,
                            $row->application_number,
                            $row->applicant_name,
                            $row->mobile,
                            $row->asset_name,
                            $row->district_name,
                            $row->city_name,
                            $row->sector_name,
                            $row->flat_cost,
                            $row->initial_received,
                            $row->cash_received,
                            $row->total_paid,
                            $row->pending_amount,
                        ]);
                    }
                }, 'ps.PropertyAuctionId', 'property_auction_id');

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function partialPaidPropertiesPrint(Request $request)
    {
        $perChunk = self::PRINT_CHUNK_SIZE;
        $afterId = max(0, $request->integer('after_id'));

        $rows = $this->partialPaidPropertiesQuery($request)
            ->when(
                $afterId > 0,
                fn($query) => $query->where('ps.PropertyAuctionId', '<', $afterId)
            )
            ->orderByDesc('ps.PropertyAuctionId')
            ->limit($perChunk + 1)
            ->get();

        $hasMore = $rows->count() > $perChunk;
        $properties = $rows->take($perChunk)->values();
        $nextAfterId = $hasMore ? $properties->last()->property_auction_id : null;

        return view('mmsay.pendingPropertiesPrint', compact(
            'properties',
            'hasMore',
            'nextAfterId'
        ));
    }

    /**
     * Full/partial payment की common optimized query.
     *
     * @param string $paymentStatus full|partial
     */
    private function eligiblePaymentPropertiesQuery(
        Request $request,
        string $paymentStatus
    ) {
        if (!in_array($paymentStatus, ['full', 'partial'], true)) {
            throw new InvalidArgumentException(
                'Invalid payment status supplied.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Payment summary
        |--------------------------------------------------------------------------
        | Start from eligible beneficiaries so only required assets reach the
        | cash receipt join. Receipt amount is calculated once through SUM().
        */

        $operator = $paymentStatus === 'full' ? '>=' : '<';

        /*
        |--------------------------------------------------------------------------
        | Aggregate cash receipts only once per asset
        |--------------------------------------------------------------------------
        */

        $eligibleAssetIds = DB::table(
            'mmsay_eligible_beneficiaries as receipt_meb'
        )
            ->join('property_private_purchasers as receipt_ppp', function ($join) {
                $join->on(
                    'receipt_ppp.ApplicationNo',
                    '=',
                    'receipt_meb.application_number'
                )
                    ->where('receipt_ppp.IsDeleted', 0);
            })
            ->join('property_auction_detail as receipt_pad', function ($join) {
                $join->on(
                    'receipt_pad.PurchaserID',
                    '=',
                    'receipt_ppp.PrivatePurchaserId'
                )
                    ->on('receipt_pad.AssetId', '=', 'receipt_ppp.Flat_Id')
                    ->where('receipt_pad.IsDeleted', 0)
                    ->where('receipt_pad.IsActive', 1);
            })
            ->when(
                $request->filled('district_id'),
                fn($query) => $query->where(
                    'receipt_pad.DistrictId',
                    $request->integer('district_id')
                )
            )
            ->when(
                $request->filled('city_id'),
                fn($query) => $query->where(
                    'receipt_pad.CityId',
                    $request->integer('city_id')
                )
            )
            ->when(
                $request->filled('sector_id'),
                fn($query) => $query->where(
                    'receipt_pad.SectorId',
                    $request->integer('sector_id')
                )
            )
            ->select('receipt_pad.AssetId')
            ->distinct();

        $receiptTotals = DB::table('cash_receipt_details as cr')
            ->joinSub($eligibleAssetIds, 'eligible_assets', function ($join) {
                $join->on(
                    'eligible_assets.AssetId',
                    '=',
                    'cr.asset_number'
                );
            })
            ->selectRaw('
        cr.asset_number,
        SUM(COALESCE(cr.total_paid_amount, 0)) AS cash_received
    ')
            ->where('cr.IsDeleted', 0)
            ->where('cr.IsActive', 1)
            ->groupBy('cr.asset_number');

        /*
        |--------------------------------------------------------------------------
        | Eligible-beneficiary payment summary
        |--------------------------------------------------------------------------
        */

        $paymentSummary = DB::table(
            'mmsay_eligible_beneficiaries as meb'
        )
            ->join(
                'property_private_purchasers as eligible_ppp',
                function ($join) {
                    $join->on(
                        'eligible_ppp.ApplicationNo',
                        '=',
                        'meb.application_number'
                    )
                        ->where('eligible_ppp.IsDeleted', 0);
                }
            )

            ->join(
                'property_auction_detail as pad',
                function ($join) {
                    $join->on(
                        'pad.PurchaserID',
                        '=',
                        'eligible_ppp.PrivatePurchaserId'
                    )
                        ->on(
                            'pad.AssetId',
                            '=',
                            'eligible_ppp.Flat_Id'
                        )
                        ->where('pad.IsDeleted', 0)
                        ->where('pad.IsActive', 1);
                }
            )

            ->leftJoinSub(
                $receiptTotals,
                'rt',
                'rt.asset_number',
                '=',
                'pad.AssetId'
            )

            ->when(
                $request->filled('district_id'),
                fn($query) => $query->where(
                    'pad.DistrictId',
                    $request->integer('district_id')
                )
            )

            ->when(
                $request->filled('city_id'),
                fn($query) => $query->where(
                    'pad.CityId',
                    $request->integer('city_id')
                )
            )

            ->when(
                $request->filled('sector_id'),
                fn($query) => $query->where(
                    'pad.SectorId',
                    $request->integer('sector_id')
                )
            )

            ->whereRaw("
        (
            COALESCE(pad.ReceivedAmount, 0)
            + COALESCE(rt.cash_received, 0)
        ) {$operator} COALESCE(pad.FlatCost, 0)
    ")

            ->select([
                'pad.PropertyAuctionId',
                'pad.AssetId',
                'pad.PurchaserID',
                'pad.DistrictId',
                'pad.CityId',
                'pad.SectorId',
                'pad.FlatCost',
                'pad.ReceivedAmount',
            ])

            ->selectRaw('
        COALESCE(rt.cash_received, 0) AS cash_received
    ')

            ->selectRaw('
        (
            COALESCE(pad.ReceivedAmount, 0)
            + COALESCE(rt.cash_received, 0)
        ) AS total_paid
    ');

        /*
        |--------------------------------------------------------------------------
        | Property, applicant and location details
        |--------------------------------------------------------------------------
        */

        $query = DB::query()
            ->fromSub($paymentSummary, 'ps')

            ->join(
                'property_registration as pr',
                function ($join) {
                    $join->on(
                        'pr.AssetId',
                        '=',
                        'ps.AssetId'
                    )
                        ->where('pr.IsDeleted', 0)
                        ->where('pr.IsActive', 1);
                }
            )

            ->join(
                'property_private_purchasers as ppp',
                function ($join) {
                    $join->on(
                        'ppp.PrivatePurchaserId',
                        '=',
                        'ps.PurchaserID'
                    )
                        ->where('ppp.IsDeleted', 0);
                }
            )

            ->leftJoin(
                'districts as d',
                'd.DistrictId',
                '=',
                'ps.DistrictId'
            )

            ->leftJoin(
                'cities as c',
                'c.CityId',
                '=',
                'ps.CityId'
            )

            ->leftJoin(
                'sectors as s',
                's.SectorId',
                '=',
                'ps.SectorId'
            );

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $search = trim((string) $request->input('search'));

        if ($search !== '') {
            $like = '%' . addcslashes($search, '%_\\') . '%';

            $query->where(function ($subQuery) use ($like) {
                $subQuery
                    ->where('ps.AssetId', 'like', $like)
                    ->orWhere('pr.AssetName', 'like', $like)
                    ->orWhere('ppp.ApplicationNo', 'like', $like)
                    ->orWhere(
                        'ppp.PrivatePurchaserName',
                        'like',
                        $like
                    )
                    ->orWhere('ppp.MobileNo', 'like', $like);
            });
        }

        $query
            ->select([
                'ps.PropertyAuctionId as property_auction_id',
                'ps.AssetId as asset_id',
                'ps.FlatCost as flat_cost',
                'ps.ReceivedAmount as initial_received',
                'ps.cash_received',
                'ps.total_paid',

                'pr.AssetName as asset_name',
                'pr.AssetSize as asset_size',
                'pr.Unit as asset_unit',

                'ppp.ApplicationNo as application_number',
                'ppp.PrivatePurchaserName as applicant_name',
                'ppp.MobileNo as mobile',

                'd.DistrictName as district_name',
                'c.CityName as city_name',
                's.SectorName as sector_name',
            ]);

        if ($paymentStatus === 'full') {
            $query->selectRaw('
            GREATEST(
                ps.total_paid - COALESCE(ps.FlatCost, 0),
                0
            ) AS excess_amount
        ');
        } else {
            $query->selectRaw('
            GREATEST(
                COALESCE(ps.FlatCost, 0) - ps.total_paid,
                0
            ) AS pending_amount
        ');
        }

        return $query;
    }

    private function paginatePaymentQuery(
        $query,
        Request $request
    ): \Illuminate\Pagination\LengthAwarePaginator {
        $perPage = self::LIST_PAGE_SIZE;

        $page = max(
            \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage(),
            1
        );

        /*
         * COUNT(*) OVER() listing और total को एक query में देता है।
         */
        $rows = $query
            ->selectRaw('COUNT(*) OVER() AS filtered_total')
            ->orderByDesc('ps.PropertyAuctionId')
            ->forPage($page, $perPage)
            ->get();

        $total = $rows->isNotEmpty()
            ? (int) $rows->first()->filtered_total
            : 0;

        /*
         * Internal total field Blade में expose नहीं करना।
         */
        $rows->each(function ($row) {
            unset($row->filtered_total);
        });

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $rows,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
                'pageName' => 'page',
            ]
        );
    }

}