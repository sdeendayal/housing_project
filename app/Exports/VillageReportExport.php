<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VillageReportExport implements FromView
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $controller = app(
            \App\Http\Controllers\MMGAY\SuperAdmin\SuperAdminController::class
        );

        $data = $controller->villageReportData($this->request);

        return view(
            'mmgay.super-admin.village-report-excel',
            $data
        );
    }
}