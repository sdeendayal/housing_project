<?php

namespace App\Exports;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AllotmentReportExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithColumnWidths
{
    protected array $filters;

    private int $serialNumber = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query(): Builder
    {
        $query = DB::table('OwnerMaster as o')
            ->join(
                'VillageMaster as v',
                'v.VillageId',
                '=',
                'o.VillageId'
            )
            ->leftJoin(
                'DistrictMaster as d',
                'd.DistrictId',
                '=',
                'o.DistrictId'
            )
            ->leftJoin(
                'BlockMaster as b',
                'b.BlockId',
                '=',
                'o.BlockId'
            )
            ->leftJoin(
                'FlatMaster as f',
                'f.FlatId',
                '=',
                'o.FlatId'
            )
            ->where('v.plots', '>', 0)
            ->whereNotNull('o.FlatId')
            ->where('o.FlatId', '>', 0);

        $this->applyFilters($query);

        return $query
            ->select([
                'o.OwnerId',
                'o.RegistrationNo',
                'o.OwnerName',
                'o.FatherHusbandName',
                'o.MobileNo',
                'o.PPPId',
                'o.MemberId',
                'o.Gender',
                'o.Caste',
                'o.Phase',

                'd.DistrictName',
                'b.BlockName',
                'v.VillageName',
                'f.FlatNo',
            ])
            ->selectRaw("
                CASE
                    WHEN IFNULL(o.IsAllotmentCancelled, 0) = 1
                        THEN 'Cancelled'

                    WHEN IFNULL(o.IsRejected, 0) = 1
                        THEN 'Rejected'

                    WHEN IFNULL(o.IsApproved, 0) = 1
                        AND IFNULL(o.IsPaid, 0) = 1
                        THEN 'Approved & Paid'

                    WHEN IFNULL(o.IsApproved, 0) = 1
                        AND IFNULL(o.IsPaid, 0) = 0
                        THEN 'Approved & Unpaid'

                    ELSE 'Yet to be Approved'
                END AS AllotmentStatus
            ")
            ->orderBy('o.OwnerId');
    }

    private function applyFilters(Builder $query): void
    {
        if (!empty($this->filters['phase'])) {
            $query->where('o.Phase', $this->filters['phase']);
        }

        if (!empty($this->filters['district_id'])) {
            $query->where(
                'o.DistrictId',
                $this->filters['district_id']
            );
        }

        if (!empty($this->filters['block_id'])) {
            $query->where(
                'o.BlockId',
                $this->filters['block_id']
            );
        }

        if (!empty($this->filters['village_id'])) {
            $query->where(
                'o.VillageId',
                $this->filters['village_id']
            );
        }

        /*
         * Search sirf tabhi query me add hogi
         * jab search value available ho.
         */
        if (!empty($this->filters['search'])) {
            $search = trim($this->filters['search']);

            $query->where(function (Builder $subQuery) use ($search) {
                $subQuery
                    ->where('o.RegistrationNo', $search)
                    ->orWhere('o.MobileNo', $search)
                    ->orWhere('o.PPPId', $search)
                    ->orWhere('f.FlatNo', $search)
                    ->orWhere(
                        'o.OwnerName',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'o.FatherHusbandName',
                        'like',
                        '%' . $search . '%'
                    );
            });
        }

        switch ($this->filters['status'] ?? '') {
            case 'approved_paid':
                $query
                    ->whereRaw(
                        'IFNULL(o.IsAllotmentCancelled, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsRejected, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsApproved, 0) = 1'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsPaid, 0) = 1'
                    );
                break;

            case 'approved_unpaid':
                $query
                    ->whereRaw(
                        'IFNULL(o.IsAllotmentCancelled, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsRejected, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsApproved, 0) = 1'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsPaid, 0) = 0'
                    );
                break;

            case 'pending':
                $query
                    ->whereRaw(
                        'IFNULL(o.IsAllotmentCancelled, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsRejected, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsApproved, 0) = 0'
                    );
                break;

            case 'rejected':
                $query
                    ->whereRaw(
                        'IFNULL(o.IsAllotmentCancelled, 0) = 0'
                    )
                    ->whereRaw(
                        'IFNULL(o.IsRejected, 0) = 1'
                    );
                break;

            case 'cancelled':
                $query->whereRaw(
                    'IFNULL(o.IsAllotmentCancelled, 0) = 1'
                );
                break;
        }
    }

    public function headings(): array
    {
        return [
            'Sr. No.',
            'Application No.',
            'Owner ID',
            'Applicant Name',
            'Father/Husband Name',
            'Mobile No.',
            'PPP ID',
            'Member ID',
            'Gender',
            'Caste',
            'District',
            'Block',
            'Village',
            'Phase',
            'Plot No.',
            'Allotment Status',
        ];
    }

    public function map($allotment): array
    {
        return [
            ++$this->serialNumber,
            $allotment->RegistrationNo ?? '-',
            $allotment->OwnerId ?? '-',
            $allotment->OwnerName ?? '-',
            $allotment->FatherHusbandName ?? '-',
            $allotment->MobileNo ?? '-',
            $allotment->PPPId ?? '-',
            $allotment->MemberId ?? '-',
            $allotment->Gender ?? '-',
            $allotment->Caste ?? '-',
            $allotment->DistrictName ?? '-',
            $allotment->BlockName ?? '-',
            $allotment->VillageName ?? '-',
            $allotment->Phase ?? '-',
            $allotment->FlatNo ?? '-',
            $allotment->AllotmentStatus
                ?? 'Yet to be Approved',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 12,
            'D' => 25,
            'E' => 25,
            'F' => 15,
            'G' => 18,
            'H' => 15,
            'I' => 10,
            'J' => 15,
            'K' => 20,
            'L' => 20,
            'M' => 25,
            'N' => 10,
            'O' => 12,
            'P' => 22,
        ];
    }
}