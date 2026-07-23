<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DistrictVillageSummaryExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles
{
    public function __construct(
        protected Collection $villageData
    ) {
    }

    public function collection(): Collection
    {
        $data = $this->villageData->values();

        $data->push((object) [
            'is_grand_total' => true,
            'VillageName' => 'Grand Total',
            'TotalPlots' => $this->villageData->sum('TotalPlots'),
            'TotalApplicants' => $this->villageData->sum('TotalApplicants'),
            'ApprovedPaid' => $this->villageData->sum('ApprovedPaid'),
            'SC' => $this->villageData->sum('SC'),
            'Ghumantu' => $this->villageData->sum('Ghumantu'),
            'Widow' => $this->villageData->sum('Widow'),
            'Others' => $this->villageData->sum('Others'),
            'TotalAllotment' => $this->villageData->sum('TotalAllotment'),
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            'Sr. No.',
            'Village',
            'Total Plots',
            'Applicants',
            'Approved & Paid',
            'SC',
            'Ghumantu',
            'Widow',
            'Others',
            'Allotted',
        ];
    }

    public function map($row): array
    {
        static $serialNumber = 0;

        if (!empty($row->is_grand_total)) {
            return [
                '',
                'Grand Total',
                (int) ($row->TotalPlots ?? 0),
                (int) ($row->TotalApplicants ?? 0),
                (int) ($row->ApprovedPaid ?? 0),
                (int) ($row->SC ?? 0),
                (int) ($row->Ghumantu ?? 0),
                (int) ($row->Widow ?? 0),
                (int) ($row->Others ?? 0),
                (int) ($row->TotalAllotment ?? 0),
            ];
        }

        $serialNumber++;

        return [
            $serialNumber,
            $row->VillageName ?? '-',
            (int) ($row->TotalPlots ?? 0),
            (int) ($row->TotalApplicants ?? 0),
            (int) ($row->ApprovedPaid ?? 0),
            (int) ($row->SC ?? 0),
            (int) ($row->Ghumantu ?? 0),
            (int) ($row->Widow ?? 0),
            (int) ($row->Others ?? 0),
            (int) ($row->TotalAllotment ?? 0),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');

        $sheet->getStyle('A1:J1')
            ->getFont()
            ->setBold(true);

        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle("A{$lastRow}:J{$lastRow}")
            ->getFont()
            ->setBold(true);

        $sheet->getStyle("A{$lastRow}:J{$lastRow}")
            ->getFill()
            ->setFillType(
                \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setARGB('FFE2E8F0');

        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        return [];
    }
}