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

class PropertyManagementController extends Controller
{
    public function dashboard()
    {
        $totalApplications = DB::table('property_registration')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->count();

        $allottedUnits = DB::table('property_auction_detail')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->count();

        $totalRevenue = DB::table('cash_receipt_details')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->sum('total_paid_amount');

        // ❌ OLD pendingInstallments REMOVED

        // 🚀 NEW EMI LOGIC (FINAL)
        $emiData = DB::select("
        SELECT 
    SUM(i.total_emi) AS total_emi,
    SUM(IFNULL(l.paid_emi, 0)) AS paid_emi,
    SUM(i.total_emi - IFNULL(l.paid_emi, 0)) AS pending_emi

FROM 
(
    SELECT 
        i.AssetId,
        COUNT(*) AS total_emi
    FROM installment_due i
    INNER JOIN property_private_purchasers p
        ON p.Flat_Id = i.AssetId
    GROUP BY i.AssetId
) i

LEFT JOIN 
(
    SELECT 
        AssetId,
        COUNT(DISTINCT InstallmentNumber) AS paid_emi
    FROM ledger
    GROUP BY AssetId
) l 
ON l.AssetId = i.AssetId;
    ");

        $emiData = $emiData[0];

        $totalPurchasers = DB::table('property_private_purchasers')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->count();

        $months = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'
        ];

        $monthlyRaw = DB::table('property_registration')
            ->selectRaw('MONTH(CreatedDate) as month_no, COUNT(*) as total')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->whereYear('CreatedDate', date('Y'))
            ->groupBy('month_no')
            ->pluck('total', 'month_no')
            ->toArray();

        $monthlyLabels = $months;
        $monthlyCounts = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyCounts[] = $monthlyRaw[$i] ?? 0;
        }

        $weeklyRaw = DB::table('property_registration')
            ->selectRaw('WEEK(CreatedDate,1) as week_no, COUNT(*) as total')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->whereYear('CreatedDate', date('Y'))
            ->groupBy('week_no')
            ->orderBy('week_no')
            ->get();

        $weeklyLabels = [];
        $weeklyCounts = [];

        foreach ($weeklyRaw as $row) {
            $weeklyLabels[] = 'W' . $row->week_no;
            $weeklyCounts[] = $row->total;
        }

        $latestPhysicalApplications = DB::table('physical_possession_applications as ppa')
            ->select(
                'ppa.id',
                'ppa.applicant_name',
                'ppa.application_number',
                'ppa.asset_name',
                'ppa.mobile',
                'ppa.created_at'
            )
            ->latest('ppa.id')
            ->limit(5)
            ->get();

        return view('mmsay.departmentDashboard', compact(
            'monthlyLabels',
            'monthlyCounts',
            'weeklyLabels',
            'weeklyCounts',
            'totalApplications',
            'allottedUnits',
            'totalRevenue',
            'emiData',
            'totalPurchasers',
            'latestPhysicalApplications'
        ));
    }
    public function index(Request $request)
    {
        $query = DB::table('property_registration as pr')
            ->leftJoin('districts as d', 'pr.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'pr.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'pr.SectorId', '=', 's.SectorId')
            ->select(
                'pr.*',
                'd.DistrictName as district',
                'c.CityName as city',
                's.SectorName as sector'
            )
            ->where('pr.IsDeleted', 0);



        if ($request->district) {
            $query->where('d.DistrictName', $request->district);
        }

        if ($request->city) {
            $query->where('c.CityName', $request->city);
        }

        if ($request->sector) {
            $query->where('s.SectorName', $request->sector);
        }

        // if ($request->status !== null && $request->status !== '') {
        //     $query->where('pr.Status', $request->status);
        // }

        $properties = $query->paginate(20)->withQueryString();

        return view('mmsay.departmentPropertyRegistration', [
            'properties' => $properties,
            'districts' => DB::table('districts')
                ->orderBy('DistrictName')
                ->get()
        ]);
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



}