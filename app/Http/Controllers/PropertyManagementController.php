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

        $paidEmiQuery = DB::table('ledger as ledger')
            ->join(
                'property_registration as pr_paid',
                'pr_paid.AssetId',
                '=',
                'ledger.AssetId'
            )
            ->selectRaw('
            ledger.AssetId,
            COUNT(DISTINCT ledger.InstallmentNumber) AS paid_emi
        ')
            ->where('ledger.Is_Deleted', 0)
            ->where('ledger.Is_Active', 1)
            ->where('ledger.Payment', '>', 0)
            ->where('pr_paid.IsDeleted', 0)
            ->where('pr_paid.IsActive', 1);

        // Apply the dashboard location filter before aggregating the ledger.
        $applyLocationFilters($paidEmiQuery, 'pr_paid');

        $paidEmiQuery->groupBy('ledger.AssetId');

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
            'totalPhysicalPossessionCandidates'
        ));
    }

    public function fullPaidProperties()
    {
        $properties = DB::select("
        SELECT
            pad.AssetId,
            ppp.ApplicationNo,
            ppp.PrivatePurchaserName,
            ppp.MobileNo,
            pr.AssetName,
            pad.FlatCost,
            (
                COALESCE(pad.ReceivedAmount,0) +
                COALESCE(SUM(cr.total_paid_amount),0)
            ) AS total_paid
        FROM property_auction_detail pad

        INNER JOIN property_private_purchasers ppp
            ON pad.PurchaserID = ppp.PrivatePurchaserId

        INNER JOIN property_registration pr
            ON pad.AssetId = pr.AssetId

        LEFT JOIN cash_receipt_details cr
            ON cr.asset_number = pad.AssetId
            AND cr.IsDeleted = 0

        WHERE pad.IsDeleted = 0
          AND pad.IsActive = 1

        GROUP BY
            pad.AssetId,
            ppp.ApplicationNo,
            ppp.PrivatePurchaserName,
            ppp.MobileNo,
            pr.AssetName,
            pad.FlatCost,
            pad.ReceivedAmount

        HAVING total_paid >= pad.FlatCost

        ORDER BY pad.AssetId DESC
    ");

        return view('mmsay.fullPaidProperties', compact('properties'));
    }

    public function pendingProperties()
    {
        $properties = DB::table('property_auction_detail as pad')

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

            ->leftJoin('cash_receipt_details as cr', function ($join) {
                $join->on('cr.asset_number', '=', 'pad.AssetId')
                    ->where('cr.IsDeleted', 0);
            })

            ->select(
                'pad.AssetId',
                'pad.FlatCost',
                'pad.ReceivedAmount',

                'ppp.ApplicationNo',
                'ppp.PrivatePurchaserName',
                'ppp.MobileNo',

                'pr.AssetName'
            )

            ->selectRaw("
            (
                COALESCE(pad.ReceivedAmount,0)
                + COALESCE(SUM(cr.total_paid_amount),0)
            ) as total_paid
        ")

            ->groupBy(
                'pad.AssetId',
                'pad.FlatCost',
                'pad.ReceivedAmount',
                'ppp.ApplicationNo',
                'ppp.PrivatePurchaserName',
                'ppp.MobileNo',
                'pr.AssetName'
            )

            ->havingRaw("
            (
                COALESCE(pad.ReceivedAmount,0)
                + COALESCE(SUM(cr.total_paid_amount),0)
            ) < pad.FlatCost
        ")

            ->orderByDesc('pad.AssetId')

            ->paginate(50);

        return view('mmsay.pendingProperties', compact('properties'));
    }

    public function propertyRegistration(Request $request)
    {
        $districtId = $request->integer('district_id') ?: null;
        $cityId = $request->integer('city_id') ?: null;
        $sectorId = $request->integer('sector_id') ?: null;
        $search = trim((string) $request->input('search'));

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
            ->orderByDesc('pr.AssetId')
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
            'search'
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

        $fileName = 'property-records-'
            . now()->format('Y-m-d-His')
            . '.csv';

        return response()->streamDownload(
            function () use ($districtId, $cityId, $sectorId, $search) {
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
                $query->chunkById(
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
            ->orderBy('pr.AssetId');
    }

    public function exportPropertiesExcel(Request $request)
    {
        $filters = $request->only([
            'district_id',
            'city_id',
            'sector_id',
            'search',
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


    public function mmsayDepartmentDraw()
    {
        $districts = DB::table('property_registration as pr')
            ->leftJoin('districts as d', 'd.DistrictId', '=', 'pr.DistrictId')
            ->select(
                'd.DistrictId',
                'd.DistrictName',
                DB::raw('COUNT(pr.AssetId) as total_assets')
            )
            ->groupBy('d.DistrictId', 'd.DistrictName')
            ->orderBy('total_assets', 'DESC')
            ->get();

        // 👉 Grand Total calculate
        $grandTotal = $districts->sum('total_assets');

        return view('mmsay.departmentDraw', compact('districts', 'grandTotal'));
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

    public function mmsayDepartmentAllottedProperties()
    {
        $properties = DB::table('property_auction_detail as pad')

            ->leftJoin('property_registration as pr', 'pad.AssetId', '=', 'pr.AssetId')

            ->leftJoin(
                'property_private_purchasers as ppp',
                'pad.PurchaserID',
                '=',
                'ppp.PrivatePurchaserId'
            )

            ->leftJoin('districts as d', 'pad.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'pad.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'pad.SectorId', '=', 's.SectorId')

            // Total EMI Paid
            ->leftJoin(
                DB::raw("
                (
                    SELECT
                        AssetId,
                        SUM(Payment) as TotalEmiPaid
                    FROM ledger
                    WHERE Is_Deleted = 0
                    AND Is_Active = 1
                    GROUP BY AssetId
                ) lg
            "),
                'pad.AssetId',
                '=',
                'lg.AssetId'
            )

            ->select(
                'pad.*',

                'pr.AssetName',
                'pr.AssetSize',

                'd.DistrictName as district',
                'c.CityName as city',
                's.SectorName as sector',

                'ppp.PrivatePurchaserName',
                'ppp.MobileNo',
                'ppp.ApplicationNo',
                'ppp.PPPId',

                // Registration Amount
                DB::raw('COALESCE(pad.ReceivedAmount,0) as RegistrationAmount'),

                // EMI Paid
                DB::raw('COALESCE(lg.TotalEmiPaid,0) as TotalEmiPaid'),

                // Total Paid
                DB::raw('(
                COALESCE(pad.ReceivedAmount,0)
                + COALESCE(lg.TotalEmiPaid,0)
            ) as ReceivedAmount'),

                // Final Balance
                DB::raw('(
                COALESCE(pad.FlatCost,0)
                - (
                    COALESCE(pad.ReceivedAmount,0)
                    + COALESCE(lg.TotalEmiPaid,0)
                )
            ) as BalanceAmount'),

                DB::raw("
                CASE
                    WHEN (
                        COALESCE(pad.FlatCost,0)
                        - (
                            COALESCE(pad.ReceivedAmount,0)
                            + COALESCE(lg.TotalEmiPaid,0)
                        )
                    ) <= 0
                    THEN 'Paid'
                    ELSE 'Pending'
                END as PaymentStatus
            ")
            )

            ->where('pad.IsDeleted', 0)
            ->where('pad.IsActive', 1)

            ->orderByDesc('pad.PropertyAuctionId')

            ->paginate(20);

        return view(
            'mmsay.deptartmentPropertyAllotment',
            compact('properties')
        );
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

    public function emiStatus($assetId)
    {
        $property = DB::table('property_auction_detail as pad')

            ->leftJoin(
                'property_private_purchasers as ppp',
                'pad.PurchaserID',
                '=',
                'ppp.PrivatePurchaserId'
            )

            ->leftJoin(
                'property_registration as pr',
                'pad.AssetId',
                '=',
                'pr.AssetId'
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
                'pad.*',
                'ppp.*',
                'pr.AssetName',
                'pr.AssetSize',

                'd.DistrictName as district',
                'c.CityName as city',
                's.SectorName as sector'
            )

            ->where('pad.AssetId', $assetId)
            ->first();

        if (!$property) {
            abort(404, 'Property not found');
        }

        $flatCost = $property->FlatCost ?? 0;

        // Actual received amount from receipts
        $totalReceived = DB::table('cash_receipt_details')
            ->where('asset_number', $assetId)
            ->sum('total_paid_amount');

        $ReceivedAmount = $totalReceived;
        $BalanceAmount = max(0, $flatCost - $totalReceived);

        // EMI schedule
        $installments = DB::table('installment_due')
            ->where('AssetId', $assetId)
            ->orderBy('InstallmentNumber')
            ->get();

        $remaining = $totalReceived;
        $today = date('Y-m-d');

        $ledger = [];

        foreach ($installments as $emi) {

            $dueAmount = $emi->DueAmount;
            $paid = 0;

            if ($remaining > 0) {
                $paid = min($remaining, $dueAmount);
                $remaining -= $paid;
            }

            $balance = $dueAmount - $paid;

            if ($emi->DueDate > $today) {
                $status = 'Upcoming';
            } else {
                if ($paid == 0) {
                    $status = 'Unpaid';
                } elseif ($paid < $dueAmount) {
                    $status = 'Partial';
                } else {
                    $status = 'Paid';
                }
            }

            $ledger[] = [
                'no' => $emi->InstallmentNumber,
                'due' => $emi->DueDate,
                'emi' => $dueAmount,
                'paid' => $paid,
                'balance' => $balance,
                'status' => $status
            ];
        }

        return view('mmsay.emiStatus', compact(
            'property',
            'flatCost',
            'ReceivedAmount',
            'BalanceAmount',
            'totalReceived',
            'ledger'
        ));
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



}