<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EmOffice;
use App\Models\District;
use App\Models\City;
use App\Models\Sector;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PropertyExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PropertiesExport;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PropertyManagementController extends Controller
{
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
        | Total registered properties
        |--------------------------------------------------------------------------
        */

        $totalApplicationsQuery = DB::table('property_registration')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1);

        $applyLocationFilters($totalApplicationsQuery);

        $totalApplications = $totalApplicationsQuery
            ->count('AssetId');

        /*
        |--------------------------------------------------------------------------
        | Total allotted properties
        |--------------------------------------------------------------------------
        */

        $allottedUnitsQuery = DB::table('property_auction_detail')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1);

        $applyLocationFilters($allottedUnitsQuery);

        $allottedUnits = $allottedUnitsQuery
            ->distinct()
            ->count('AssetId');

        /*
        |--------------------------------------------------------------------------
        | Total revenue
        |--------------------------------------------------------------------------
        */

        $totalRevenueQuery = DB::table('cash_receipt_details')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1);

        $applyLocationFilters($totalRevenueQuery);

        $totalRevenue = (float) $totalRevenueQuery
            ->sum('total_paid_amount');

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

        $totalPurchasers = $totalPurchasersQuery
            ->count('PrivatePurchaserId');

        /*
        |--------------------------------------------------------------------------
        | Optimized asset payment aggregation
        |--------------------------------------------------------------------------
        | First filter property_auction_detail.
        | Then join only receipts related to those filtered assets.
        |--------------------------------------------------------------------------
        */

        $assetPaymentsQuery = DB::table(
            'property_auction_detail as pad'
        )
            ->leftJoin(
                'cash_receipt_details as cr',
                function ($join) {
                    $join->on(
                        'cr.asset_number',
                        '=',
                        'pad.AssetId'
                    )
                        ->where('cr.IsDeleted', 0)
                        ->where('cr.IsActive', 1);
                }
            )
            ->selectRaw('
            pad.AssetId,
            pad.FlatCost,
            pad.ReceivedAmount,
            (
                COALESCE(pad.ReceivedAmount, 0)
                + COALESCE(SUM(cr.total_paid_amount), 0)
            ) AS total_received
        ')
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
        | Eligibility and payment statistics in one query
        |--------------------------------------------------------------------------
        */

        $dashboardPaymentStats = DB::query()
            ->fromSub($assetPaymentsQuery, 'payments')
            ->selectRaw('
            COUNT(*) AS total_candidates,

            SUM(
                CASE
                    WHEN payments.total_received >= 60000
                    THEN 1
                    ELSE 0
                END
            ) AS eligible_candidates,

            SUM(
                CASE
                    WHEN payments.total_received < 60000
                    THEN 1
                    ELSE 0
                END
            ) AS not_eligible_candidates,

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

        $eligiblePhysicalPossession = (int) (
            $dashboardPaymentStats->eligible_candidates ?? 0
        );

        $notEligiblePhysicalPossession = (int) (
            $dashboardPaymentStats->not_eligible_candidates ?? 0
        );

        $totalPhysicalPossessionCandidates = (int) (
            $dashboardPaymentStats->total_candidates ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | Keep existing Blade paymentStats variable compatible
        |--------------------------------------------------------------------------
        */

        $paymentStats = (object) [
            'total_records' => $totalPhysicalPossessionCandidates,

            'total_paid_properties' => (int) (
                $dashboardPaymentStats->total_paid_properties ?? 0
            ),

            'pending_properties' => (int) (
                $dashboardPaymentStats->pending_properties ?? 0
            ),
        ];

        /*
        |--------------------------------------------------------------------------
        | Optimized EMI statistics
        |--------------------------------------------------------------------------
        */

        $dueEmiQuery = DB::table('installment_due as due')
            ->join(
                'property_registration as pr',
                'pr.AssetId',
                '=',
                'due.AssetId'
            )
            ->selectRaw('
            due.AssetId,
            COUNT(DISTINCT due.InstallmentNumber) AS total_emi
        ')
            ->where('due.IsDeleted', 0)
            ->where('due.IsActive', 1)
            ->where('pr.IsDeleted', 0)
            ->where('pr.IsActive', 1);

        $applyLocationFilters($dueEmiQuery, 'pr');

        $dueEmiQuery->groupBy('due.AssetId');

        $receiptSumsQuery = DB::table('cash_receipt_details')
            ->select('asset_number', DB::raw('SUM(total_paid_amount) as total_receipts'))
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->groupBy('asset_number');

        $paidEmiQuery = DB::table('property_auction_detail as pad')
            ->join(
                'property_registration as pr_paid',
                'pr_paid.AssetId',
                '=',
                'pad.AssetId'
            )
            ->leftJoinSub($receiptSumsQuery, 'cr_sum', 'cr_sum.asset_number', '=', 'pad.AssetId')
            ->join('installment_due as due', function($join) {
                $join->on('due.AssetId', '=', 'pad.AssetId')
                     ->where('due.InstallmentNumber', '=', 1);
            })
            ->selectRaw('
                pad.AssetId,
                LEAST(
                    (SELECT COUNT(*) FROM installment_due WHERE AssetId = pad.AssetId AND IsDeleted = 0 AND IsActive = 1),
                    FLOOR((COALESCE(pad.ReceivedAmount, 0) + COALESCE(cr_sum.total_receipts, 0)) / COALESCE(NULLIF(due.EMIAmount, 0), due.DueAmount, 1))
                ) as paid_emi
            ')
            ->where('pr_paid.IsDeleted', 0)
            ->where('pr_paid.IsActive', 1);

        // Apply the dashboard location filter before aggregating.
        $applyLocationFilters($paidEmiQuery, 'pr_paid');

        $paidEmiQuery->groupBy('pad.AssetId', 'pad.ReceivedAmount', 'due.EMIAmount', 'due.DueAmount', 'cr_sum.total_receipts');

        $emiData = DB::query()
            ->fromSub($dueEmiQuery, 'due_summary')
            ->leftJoinSub(
                $paidEmiQuery,
                'paid_summary',
                function ($join) {
                    $join->on(
                        'paid_summary.AssetId',
                        '=',
                        'due_summary.AssetId'
                    );
                }
            )
            ->selectRaw('
            COALESCE(
                SUM(due_summary.total_emi),
                0
            ) AS total_emi,

            COALESCE(
                SUM(
                    COALESCE(paid_summary.paid_emi, 0)
                ),
                0
            ) AS paid_emi,

            COALESCE(
                SUM(
                    GREATEST(
                        due_summary.total_emi
                        - COALESCE(
                            paid_summary.paid_emi,
                            0
                        ),
                        0
                    )
                ),
                0
            ) AS pending_emi
        ')
            ->first();

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
            'emiData',
            'paymentStats',
            'eligiblePhysicalPossession',
            'notEligiblePhysicalPossession',
            'totalPhysicalPossessionCandidates',
            'latestPhysicalApplications'
        ));
    }

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

        $receiptTotals = DB::table('cash_receipt_details')
            ->selectRaw('
            asset_number,
            SUM(total_paid_amount) AS receipt_paid
        ')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->groupBy('asset_number');

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
            ->leftJoinSub($receiptTotals, 'cr', function ($join) {
                $join->on('cr.asset_number', '=', 'pr.AssetId');
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
            ->selectRaw('
            COALESCE(cr.receipt_paid, 0) AS receipt_paid,

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
            ->paginate(50)
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

        $receiptTotals = DB::table('cash_receipt_details')
            ->selectRaw('
            asset_number,
            SUM(total_paid_amount) AS receipt_paid
        ')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->groupBy('asset_number');

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
            ->leftJoinSub($receiptTotals, 'cr', function ($join) {
                $join->on('cr.asset_number', '=', 'pr.AssetId');
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
            ->selectRaw('
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

        return view('mmsay.departmentDraw', compact(
            'districts',
            'drawDistricts',
            'grandTotal',
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

        // total records
        $totalRecords = $query->count();

        // pagination (20 per page)
        $data = $query->paginate(10);

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

    public function departmentPhysicalLetter()
    {
        $letters = DB::table('physical_possession_applications as ppa')
            ->select(
                'ppa.*',
                DB::raw("
                (
                    SELECT
                        CASE
                            WHEN SUM(
                                CASE
                                    WHEN ppd.review_status = 'rejected'
                                    THEN 1 ELSE 0
                                END
                            ) > 0
                            THEN 'rejected'

                            WHEN COUNT(ppd.id) > 0
                            AND COUNT(ppd.id) =
                                SUM(
                                    CASE
                                        WHEN ppd.review_status = 'approved'
                                        THEN 1 ELSE 0
                                    END
                                )
                            THEN 'approved'

                            ELSE 'pending'
                        END
                    FROM physical_possession_documents ppd
                    WHERE ppd.property_auction_id = ppa.property_auction_id
                ) as status
            ")
            )
            ->orderByDesc('ppa.id')
            ->get();

        return view(
            'mmsay.departmentPhysicalLetter',
            compact('letters')
        );
    }

    public function allotmentLetter($id)
    {
        $property = DB::table('property_auction_detail as pad')
            ->leftJoin('property_registration as pr', 'pad.AssetId', '=', 'pr.AssetId')
            ->leftJoin('property_private_purchasers as ppp', 'pad.PurchaserID', '=', 'ppp.PrivatePurchaserId')
            ->leftJoin('districts as d', 'pad.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'pad.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'pad.SectorId', '=', 's.SectorId')
            ->where('pad.PropertyAuctionId', $id)
            ->select(
                'pad.*',
                'pr.AssetName',
                'pr.AssetSize',
                'ppp.PrivatePurchaserName',
                'ppp.PurchaserFatherName',
                'ppp.MobileNo',
                'ppp.ApplicationNo',
                'ppp.PPPId',
                'd.DistrictName',
                'c.CityName',
                's.SectorName'
            )
            ->first();

        return view('mmsay.allotmentLetter', compact('property'));
    }
    public function downloadAllotmentLetter($id)
    {
        $property = DB::table('property_auction_detail as pad')
            ->leftJoin('property_registration as pr', 'pad.AssetId', '=', 'pr.AssetId')
            ->leftJoin('property_private_purchasers as ppp', 'pad.PurchaserID', '=', 'ppp.PrivatePurchaserId')
            ->leftJoin('districts as d', 'pad.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'pad.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'pad.SectorId', '=', 's.SectorId')
            ->where('pad.PropertyAuctionId', $id)
            ->select(
                'pad.*',
                'pr.AssetName',
                'pr.AssetSize',
                'ppp.PrivatePurchaserName',
                'ppp.PurchaserFatherName',
                'ppp.ApplicationNo',
                'ppp.PPPId',
                'ppp.MobileNo',
                'd.DistrictName',
                'c.CityName',
                's.SectorName'
            )
            ->first();

        if (!$property) {
            abort(404);
        }

        return view(
            'mmsay.allotment_letter_pdf',
            compact('property')
        );
    }

    public function exportAllotmentLetterPdf($id)
    {
        $property = DB::table('property_auction_detail as pad')
            ->leftJoin('property_registration as pr', 'pad.AssetId', '=', 'pr.AssetId')
            ->leftJoin('property_private_purchasers as ppp', 'pad.PurchaserID', '=', 'ppp.PrivatePurchaserId')
            ->leftJoin('districts as d', 'pad.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'pad.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'pad.SectorId', '=', 's.SectorId')
            ->where('pad.PropertyAuctionId', $id)
            ->select(
                'pad.*',
                'pr.AssetName',
                'pr.AssetSize',
                'ppp.PrivatePurchaserName',
                'ppp.PurchaserFatherName',
                'ppp.ApplicationNo',
                'ppp.PPPId',
                'ppp.MobileNo',
                'd.DistrictName',
                'c.CityName',
                's.SectorName'
            )
            ->first();

        if (!$property) {
            abort(404);
        }

        $pdf = Pdf::loadView(
            'mmsay.allotment_letter_pdf',
            compact('property')
        );

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download(
            'Allotment_Letter_' .
            $property->ApplicationNo .
            '.pdf'
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

    /*
    |--------------------------------------------------------------------------
    | Eligible candidates query
    |--------------------------------------------------------------------------
    | Eligibility = property_auction_detail.ReceivedAmount
    |             + SUM(cash_receipt_details.total_paid_amount) >= 60000
    |
    | The latest physical-possession application is joined when it exists.
    | An eligible asset without an application appears as Awaiting Schedule.
    */
    private function eligiblePossessionQuery(Request $request, bool $applyStatus = true)
    {
        $filters = $this->possessionFilters($request);
        $statusSql = $this->possessionStatusSql();

        /*
        | Dashboard uses this exact payment calculation.
        | Do not filter through property_registration before eligibility is decided.
        */
        $assetPayments = DB::table('property_auction_detail as pad')
            ->leftJoin('cash_receipt_details as cr', function ($join) {
                $join->on('cr.asset_number', '=', 'pad.AssetId')
                    ->where('cr.IsDeleted', 0)
                    ->where('cr.IsActive', 1);
            })
            ->selectRaw('
            MAX(pad.PropertyAuctionId) AS PropertyAuctionId,
            pad.AssetId,
            pad.FlatCost,
            pad.ReceivedAmount,
            MAX(pad.PurchaserID) AS PurchaserID,
            MAX(pad.BranchId) AS BranchId,
            MAX(pad.DistrictId) AS DistrictId,
            MAX(pad.CityId) AS CityId,
            MAX(pad.SectorId) AS SectorId,
            COALESCE(SUM(cr.total_paid_amount), 0) AS receipt_total,
            (
                COALESCE(pad.ReceivedAmount, 0)
                + COALESCE(SUM(cr.total_paid_amount), 0)
            ) AS total_received
        ')
            ->where('pad.IsDeleted', 0)
            ->where('pad.IsActive', 1)
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
            ->leftJoin('property_private_purchasers as ppp', function ($join) {
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
                $search = '%' . addcslashes($filters['search'], '%_\\') . '%';

                $q->where(function ($sub) use ($search) {
                    $sub->where('payments.AssetId', 'like', $search)
                        ->orWhere('pr.AssetName', 'like', $search)
                        ->orWhere('ppp.PrivatePurchaserName', 'like', $search)
                        ->orWhere('ppp.MobileNo', 'like', $search)
                        ->orWhere('ppp.ApplicationNo', 'like', $search)
                        ->orWhere('ppa.application_number', 'like', $search)
                        ->orWhere('ppa.possession_id', 'like', $search);
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
            ->paginate(50)
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
        $perChunk = 500;
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
        $paymentSummary = DB::table('property_auction_detail as pad')
            ->leftJoin('cash_receipt_details as cr', function ($join) {
                $join->on('cr.asset_number', '=', 'pad.AssetId')
                    ->where('cr.IsDeleted', 0)
                    ->where('cr.IsActive', 1);
            })
            ->where('pad.IsDeleted', 0)
            ->where('pad.IsActive', 1)
            // ReceivedAmount is NOT NULL; plain comparison can use the composite index.
            ->where('pad.ReceivedAmount', '<', 60000)
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
            ->selectRaw('COALESCE(SUM(cr.total_paid_amount), 0) AS cash_received')
            ->selectRaw('
            COALESCE(pad.ReceivedAmount, 0)
            + COALESCE(SUM(cr.total_paid_amount), 0)
            AS total_received
        ')
            ->groupBy([
                'pad.PropertyAuctionId',
                'pad.AssetId',
                'pad.PurchaserID',
                'pad.DistrictId',
                'pad.CityId',
                'pad.SectorId',
                'pad.FlatCost',
                'pad.ReceivedAmount',
            ])
            ->havingRaw('
            COALESCE(pad.ReceivedAmount, 0)
            + COALESCE(SUM(cr.total_paid_amount), 0) < 60000
        ');

        $query = DB::query()
            ->fromSub($paymentSummary, 'ps')
            ->join('property_registration as pr', function ($join) {
                $join->on('pr.AssetId', '=', 'ps.AssetId')
                    ->where('pr.IsDeleted', 0)
                    ->where('pr.IsActive', 1);
            })
            ->leftJoin('property_private_purchasers as ppp', function ($join) {
                $join->on('ppp.PrivatePurchaserId', '=', 'ps.PurchaserID')
                    ->where('ppp.IsDeleted', 0);
            })
            ->leftJoin('districts as d', 'd.DistrictId', '=', 'ps.DistrictId')
            ->leftJoin('cities as c', 'c.CityId', '=', 'ps.CityId')
            ->leftJoin('sectors as s', 's.SectorId', '=', 'ps.SectorId');

        $search = trim((string) $request->input('search'));

        if ($search !== '') {
            $like = '%' . addcslashes($search, '%_\\') . '%';

            $query->where(function ($subQuery) use ($like) {
                $subQuery->where('ps.AssetId', 'like', $like)
                    ->orWhere('pr.AssetName', 'like', $like)
                    ->orWhere('ppp.PrivatePurchaserName', 'like', $like)
                    ->orWhere('ppp.MobileNo', 'like', $like)
                    ->orWhere('ppp.ApplicationNo', 'like', $like);
            });
        }

        return $query
            ->select([
                'ps.PropertyAuctionId as property_auction_id',
                'ps.AssetId as asset_id',
                'pr.AssetName as asset_name',
                'pr.AssetSize as asset_size',
                'pr.Unit as asset_unit',
                'ppp.PrivatePurchaserName as applicant_name',
                'ppp.MobileNo as mobile',
                'ppp.ApplicationNo as application_number',
                'ppp.ApplicationNo as purchaser_application_number',
                'd.DistrictName as district_name',
                'c.CityName as city_name',
                's.SectorName as sector_name',
                'ps.FlatCost as flat_cost',
            ])
            ->selectRaw('COALESCE(ps.ReceivedAmount, 0) AS initial_received')
            ->selectRaw('ps.cash_received')
            ->selectRaw('ps.total_received AS received_amount')
            ->selectRaw(
                'GREATEST(COALESCE(ps.FlatCost, 0) - ps.total_received, 0)
             AS pending_amount'
            )
            ->selectRaw(
                'GREATEST(60000 - ps.total_received, 0)
             AS eligibility_shortfall'
            );
    }

    public function physicalPossessionNotEligible(Request $request)
    {
        $districtId = $request->integer('district_id') ?: null;
        $cityId = $request->integer('city_id') ?: null;
        $sectorId = $request->integer('sector_id') ?: null;
        $search = trim((string) $request->input('search'));
        $filters = [
            'district_id' => $districtId,
            'city_id' => $cityId,
            'sector_id' => $sectorId,
            'search' => $search,
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

        // Length-aware pagination is required for page numbers and total records.
        $applications = $this->notEligiblePossessionQuery($request)
            ->orderByDesc('ps.PropertyAuctionId')
            ->paginate(50)
            ->withQueryString();

        return view('mmsay.physicalPossessionNotEligible', compact(
            'applications',
            'districts',
            'cities',
            'sectors',
            'districtId',
            'cityId',
            'sectorId',
            'search',
            'filters'
        ));
    }

    public function physicalPossessionNotEligibleCsv(Request $request)
    {
        $fileName = 'physical-possession-not-eligible-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
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
                'Total Received',
                'Total Pending',
            ]);

            $serial = 1;

            $this->notEligiblePossessionQuery($request)
                ->orderByDesc('ps.PropertyAuctionId')
                ->chunkByIdDesc(500, function ($rows) use ($handle, &$serial) {
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
                            $row->received_amount,
                            $row->pending_amount,
                        ]);
                    }
                }, 'ps.PropertyAuctionId', 'property_auction_id');

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function physicalPossessionNotEligiblePrint(Request $request)
    {
        $perChunk = 500000;
        $afterId = max(0, $request->integer('after_id'));

        $rows = $this->notEligiblePossessionQuery($request)
            ->when(
                $afterId > 0,
                fn($query) => $query->where('ps.PropertyAuctionId', '<', $afterId)
            )
            ->orderByDesc('ps.PropertyAuctionId')
            ->limit($perChunk + 1)
            ->get();

        $hasMore = $rows->count() > $perChunk;
        $applications = $rows->take($perChunk)->values();
        $nextAfterId = $hasMore
            ? $applications->last()->property_auction_id
            : null;

        return view('mmsay.physicalPossessionNotEligiblePrint', compact(
            'applications',
            'hasMore',
            'nextAfterId'
        ));
    }

    private function fullPaidPropertiesQuery(Request $request)
    {
        $paymentSummary = DB::table('property_auction_detail as pad')
            ->leftJoin('cash_receipt_details as cr', function ($join) {
                $join->on('cr.asset_number', '=', 'pad.AssetId')
                    ->where('cr.IsDeleted', 0)
                    ->where('cr.IsActive', 1);
            })
            ->where('pad.IsDeleted', 0)
            ->where('pad.IsActive', 1)
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
            ->selectRaw('COALESCE(SUM(cr.total_paid_amount), 0) AS cash_received')
            ->selectRaw('
            COALESCE(pad.ReceivedAmount, 0)
            + COALESCE(SUM(cr.total_paid_amount), 0)
            AS total_paid
        ')
            ->groupBy([
                'pad.PropertyAuctionId',
                'pad.AssetId',
                'pad.PurchaserID',
                'pad.DistrictId',
                'pad.CityId',
                'pad.SectorId',
                'pad.FlatCost',
                'pad.ReceivedAmount',
            ])
            ->havingRaw('
            COALESCE(pad.ReceivedAmount, 0)
            + COALESCE(SUM(cr.total_paid_amount), 0)
            >= COALESCE(pad.FlatCost, 0)
        ');

        $query = DB::query()
            ->fromSub($paymentSummary, 'ps')
            ->join('property_registration as pr', function ($join) {
                $join->on('pr.AssetId', '=', 'ps.AssetId')
                    ->where('pr.IsDeleted', 0)
                    ->where('pr.IsActive', 1);
            })
            ->leftJoin('property_private_purchasers as ppp', function ($join) {
                $join->on('ppp.PrivatePurchaserId', '=', 'ps.PurchaserID')
                    ->where('ppp.IsDeleted', 0);
            })
            ->leftJoin('districts as d', 'd.DistrictId', '=', 'ps.DistrictId')
            ->leftJoin('cities as c', 'c.CityId', '=', 'ps.CityId')
            ->leftJoin('sectors as s', 's.SectorId', '=', 'ps.SectorId');

        $search = trim((string) $request->input('search'));

        if ($search !== '') {
            $like = '%' . addcslashes($search, '%_\\') . '%';

            $query->where(function ($subQuery) use ($like) {
                $subQuery->where('ps.AssetId', 'like', $like)
                    ->orWhere('pr.AssetName', 'like', $like)
                    ->orWhere('ppp.ApplicationNo', 'like', $like)
                    ->orWhere('ppp.PrivatePurchaserName', 'like', $like)
                    ->orWhere('ppp.MobileNo', 'like', $like);
            });
        }

        return $query
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
            ])
            ->selectRaw('GREATEST(ps.total_paid - ps.FlatCost, 0) AS excess_amount');
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

        $properties = $this->fullPaidPropertiesQuery($request)
            ->orderByDesc('ps.PropertyAuctionId')
            ->paginate(50)
            ->withQueryString();

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
                ->chunkByIdDesc(500, function ($rows) use ($handle, &$serial) {
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
        $perChunk = 500;
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
        $paymentSummary = DB::table('property_auction_detail as pad')
            ->leftJoin('cash_receipt_details as cr', function ($join) {
                $join->on('cr.asset_number', '=', 'pad.AssetId')
                    ->where('cr.IsDeleted', 0)
                    ->where('cr.IsActive', 1);
            })
            ->where('pad.IsDeleted', 0)
            ->where('pad.IsActive', 1)
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
            ->selectRaw('COALESCE(SUM(cr.total_paid_amount), 0) AS cash_received')
            ->selectRaw('
            COALESCE(pad.ReceivedAmount, 0)
            + COALESCE(SUM(cr.total_paid_amount), 0)
            AS total_paid
        ')
            ->groupBy([
                'pad.PropertyAuctionId',
                'pad.AssetId',
                'pad.PurchaserID',
                'pad.DistrictId',
                'pad.CityId',
                'pad.SectorId',
                'pad.FlatCost',
                'pad.ReceivedAmount',
            ])
            ->havingRaw('
            COALESCE(pad.ReceivedAmount, 0)
            + COALESCE(SUM(cr.total_paid_amount), 0)
            < COALESCE(pad.FlatCost, 0)
        ');

        $query = DB::query()
            ->fromSub($paymentSummary, 'ps')
            ->join('property_registration as pr', function ($join) {
                $join->on('pr.AssetId', '=', 'ps.AssetId')
                    ->where('pr.IsDeleted', 0)
                    ->where('pr.IsActive', 1);
            })
            ->leftJoin('property_private_purchasers as ppp', function ($join) {
                $join->on('ppp.PrivatePurchaserId', '=', 'ps.PurchaserID')
                    ->where('ppp.IsDeleted', 0);
            })
            ->leftJoin('districts as d', 'd.DistrictId', '=', 'ps.DistrictId')
            ->leftJoin('cities as c', 'c.CityId', '=', 'ps.CityId')
            ->leftJoin('sectors as s', 's.SectorId', '=', 'ps.SectorId');

        $search = trim((string) $request->input('search'));

        if ($search !== '') {
            $like = '%' . addcslashes($search, '%_\\') . '%';

            $query->where(function ($subQuery) use ($like) {
                $subQuery->where('ps.AssetId', 'like', $like)
                    ->orWhere('pr.AssetName', 'like', $like)
                    ->orWhere('ppp.ApplicationNo', 'like', $like)
                    ->orWhere('ppp.PrivatePurchaserName', 'like', $like)
                    ->orWhere('ppp.MobileNo', 'like', $like);
            });
        }

        return $query
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
            ])
            ->selectRaw('
            GREATEST(COALESCE(ps.FlatCost, 0) - ps.total_paid, 0)
            AS pending_amount
        ');
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

        $properties = $this->partialPaidPropertiesQuery($request)
            ->orderByDesc('ps.PropertyAuctionId')
            ->paginate(50)
            ->withQueryString();

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
                ->chunkByIdDesc(500, function ($rows) use ($handle, &$serial) {
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
        $perChunk = 500;
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

}