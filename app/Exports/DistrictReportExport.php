<?php

namespace App\Exports;

use App\Models\DistrictReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DistrictReportExport implements FromView
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }


    public function view(): View
    {
        $controller = app(\App\Http\Controllers\MMGAY\SuperAdmin\SuperAdminController::class);

        $data = $controller->districtReportData($this->request);

        return view(
            'mmgay.super-admin.district-report-excel',
            $data
        );
    }
}
