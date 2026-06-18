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

        return view('mmsay.departmentDashboard', compact(
            'monthlyLabels',
            'monthlyCounts',
            'weeklyLabels',
            'weeklyCounts',
            'totalApplications',
            'allottedUnits',
            'totalRevenue',
            'emiData',
            'totalPurchasers'
        ));
    }
    public function index(Request $request)
    {
        $query = DB::table('property_registration as pr')
            ->leftJoin('em_offices as e', 'pr.BranchId', '=', 'e.BranchId')
            ->leftJoin('districts as d', 'pr.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'pr.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'pr.SectorId', '=', 's.SectorId')
            ->select(
                'pr.*',
                'e.BranchName as em_office',
                'd.DistrictName as district',
                'c.CityName as city',
                's.SectorName as sector'
            )
            ->where('pr.IsDeleted', 0);

        if ($request->em_office) {
            $query->where('e.BranchName', $request->em_office);
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

        // if ($request->status !== null && $request->status !== '') {
        //     $query->where('pr.Status', $request->status);
        // }

        $properties = $query->paginate(20)->withQueryString();

        return view('mmsay.departmentPropertyRegistration', [
            'properties' => $properties,
            'emOffices' => EmOffice::all()
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


}