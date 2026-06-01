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
        $receipts = DB::table('cash_receipt_details as cr')
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
            ->where('cr.IsActive', 1)
            ->orderByDesc('cr.id')
            ->paginate(20); // 20 records per page

        return view('mmsay.mmsayDepartmentCashReceipt', compact('receipts'));
    }

}