<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DashboardExport implements FromArray, WithHeadings
{
    protected $data;
    public function __construct(array $data) { $this->data = $data; }

    public function array(): array {
        return [
            ['Metric', 'Total Count'],
            ['Total Villages', $this->data['TotalVillages']],
            ['Registered Beneficiaries', $this->data['RegisteredBeneficiaries']],
            ['Approved & Paid', $this->data['ApprovedPaid']],
            ['Approved & Unpaid', $this->data['ApprovedUnpaid']],
            ['Pending Approval', $this->data['PendingApprovalPayment']],
            ['Rejected', $this->data['Rejected']],
            ['Cancelled', $this->data['AllotmentCancelled']],
        ];
    }
    public function headings(): array { return ['Category', 'Value']; }
}