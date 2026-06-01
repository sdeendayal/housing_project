<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PropertyExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('property_registration as pr')
            ->leftJoin('em_offices as eo', 'pr.BranchId', '=', 'eo.BranchId')
            ->leftJoin('districts as d', 'pr.DistrictId', '=', 'd.DistrictId')
            ->leftJoin('cities as c', 'pr.CityId', '=', 'c.CityId')
            ->leftJoin('sectors as s', 'pr.SectorId', '=', 's.SectorId');

        // filters
        if ($this->request->em_office) {
            $query->where('eo.BranchName', $this->request->em_office);
        }

        if ($this->request->district) {
            $query->where('d.DistrictName', $this->request->district);
        }

        if ($this->request->city) {
            $query->where('c.CityName', $this->request->city);
        }

        if ($this->request->sector) {
            $query->where('s.SectorName', $this->request->sector);
        }

        // ✅ HERE IS THE FIX (THIS IS YOUR LINE)
        return $query->select(
            'pr.AssetId',
            'pr.AssetName',
            'pr.AssetSize',
            'eo.BranchName as em_office',
            'd.DistrictName as district',
            'c.CityName as city',
            's.SectorName as sector'
        )->get();
    }

    public function headings(): array
    {
        return [
            "Asset ID",
            "Asset Name",
            "Size",
            "EM Office",
            "District",
            "City",
            "Sector"
        ];
    }
}