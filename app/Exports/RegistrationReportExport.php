<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RegistrationReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithColumnWidths
{
    private array $filters;
    private ?array $registryColumns = null;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }


    private function registryColumns(): array
    {
        if ($this->registryColumns !== null) {
            return $this->registryColumns;
        }

        $columns = DB::select(
            "SHOW COLUMNS FROM `dddnew1`.`registary`"
        );

        return $this->registryColumns = array_map(
            static fn ($column) => $column->Field,
            $columns
        );
    }

    public function collection(): Collection
    {
        $phase = $this->filters['phase'] ?? null;
        $districtId = $this->filters['district_id'] ?? null;
        $blockId = $this->filters['block_id'] ?? null;
        $villageId = $this->filters['village_id'] ?? null;
        $search = trim((string) ($this->filters['search'] ?? ''));
        $type = $this->filters['type'] ?? 'all';

       


        $allowedTypes = [
            'all',
            'unique_registry',
            'duplicate_registry',
            'blank_registry',
            'matched',
            'unmatched',
            'unique_matched_mobile',
            'repeated_matched_mobile',
        ];

        if (!in_array($type, $allowedTypes, true)) {
            $type = 'all';
        }

        /*
        |--------------------------------------------------------------------------
        | Ranked registry subquery
        |--------------------------------------------------------------------------
        | MySQL 8+ required.
        */
        $registryRankedSubQuery = DB::table('dddnew1.registary as rs')
            ->select('rs.*')
            ->selectRaw("
                ROW_NUMBER() OVER (
                    PARTITION BY NULLIF(TRIM(rs.RegistaryNumber), '')
                    ORDER BY
                        rs.RegistaryDate DESC,
                        rs.Token DESC
                ) AS registry_row_number
            ")
            ->selectRaw("
                COUNT(*) OVER (
                    PARTITION BY NULLIF(TRIM(rs.RegistaryNumber), '')
                ) AS registry_group_count
            ")
            ->selectRaw("
                ROW_NUMBER() OVER (
                    PARTITION BY NULLIF(TRIM(rs.SecondPartyMobile), '')
                    ORDER BY
                        rs.RegistaryDate DESC,
                        rs.Token DESC
                ) AS mobile_row_number
            ")
            ->selectRaw("
                COUNT(*) OVER (
                    PARTITION BY NULLIF(TRIM(rs.SecondPartyMobile), '')
                ) AS mobile_group_count
            ");

        /*
        |--------------------------------------------------------------------------
        | Filtered owner-mobile subquery
        |--------------------------------------------------------------------------
        | One OwnerId per matching mobile.
        */
        $ownerMobileSubQuery = DB::table('OwnerMaster as om')
            ->selectRaw('om.MobileNo, MIN(om.OwnerId) AS OwnerId')
            ->whereNotNull('om.MobileNo')
            ->whereRaw("TRIM(om.MobileNo) != ''")
            ->when(filled($phase), function ($query) use ($phase) {
                $query->where('om.Phase', $phase);
            })
            ->when(filled($districtId), function ($query) use ($districtId) {
                $query->where('om.DistrictId', $districtId);
            })
            ->when(filled($blockId), function ($query) use ($blockId) {
                $query->where('om.BlockId', $blockId);
            })
            ->when(filled($villageId), function ($query) use ($villageId) {
                $query->where('om.VillageId', $villageId);
            })
            ->groupBy('om.MobileNo');

        $query = DB::query()
            ->fromSub($registryRankedSubQuery, 'r')
            ->leftJoinSub(
                $ownerMobileSubQuery,
                'matched_owner',
                function ($join) {
                    $join->on(
                        'matched_owner.MobileNo',
                        '=',
                        'r.SecondPartyMobile'
                    );
                }
            )
            ->leftJoin(
                'OwnerMaster as o',
                'o.OwnerId',
                '=',
                'matched_owner.OwnerId'
            );

        /*
        |--------------------------------------------------------------------------
        | Card type filter
        |--------------------------------------------------------------------------
        */
        switch ($type) {
            case 'all':
                break;

            case 'unique_registry':
                $query
                    ->whereNotNull('r.RegistaryNumber')
                    ->whereRaw("TRIM(r.RegistaryNumber) != ''")
                    ->where('r.registry_row_number', 1);
                break;

            case 'duplicate_registry':
                $query
                    ->whereNotNull('r.RegistaryNumber')
                    ->whereRaw("TRIM(r.RegistaryNumber) != ''")
                    ->where('r.registry_group_count', '>', 1)
                    ->where('r.registry_row_number', '>', 1);
                break;

            case 'blank_registry':
                $query->where(function ($subQuery) {
                    $subQuery
                        ->whereNull('r.RegistaryNumber')
                        ->orWhereRaw("TRIM(r.RegistaryNumber) = ''");
                });
                break;

            case 'unmatched':
                $query->whereNull('matched_owner.OwnerId');
                break;

            case 'unique_matched_mobile':
                $query
                    ->whereNotNull('matched_owner.OwnerId')
                    ->whereNotNull('r.SecondPartyMobile')
                    ->whereRaw("TRIM(r.SecondPartyMobile) != ''")
                    ->where('r.mobile_row_number', 1);
                break;

            case 'repeated_matched_mobile':
                $query
                    ->whereNotNull('matched_owner.OwnerId')
                    ->whereNotNull('r.SecondPartyMobile')
                    ->whereRaw("TRIM(r.SecondPartyMobile) != ''")
                    ->where('r.mobile_row_number', '>', 1);
                break;

            case 'matched':
            default:
                $query->whereNotNull('matched_owner.OwnerId');
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('r.SecondPartyMobile', $search)
                    ->orWhere('r.RegistaryNumber', $search)
                    ->orWhere('r.Token', $search)
                    ->orWhere(
                        'r.SecondParty',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'r.FirstParty',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'o.OwnerName',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere('o.RegistrationNo', $search);
            });
        }

        $registrySelectColumns = array_map(
            static fn ($column) => 'r.' . $column,
            $this->registryColumns()
        );

        return $query
            ->select($registrySelectColumns)
            ->addSelect([
                'r.registry_group_count',
                'r.mobile_group_count',
                'o.OwnerId as matched_owner_id',
                'o.RegistrationNo as matched_registration_no',
                'o.OwnerName as matched_owner_name',
                'o.FatherHusbandName as matched_father_husband_name',
                'o.MobileNo as matched_owner_mobile',
                'o.PPPId as matched_ppp_id',
                'o.MemberId as matched_member_id',
                'o.Caste as matched_caste',
                'o.Phase as matched_phase',
            ])
            ->orderByDesc('r.RegistaryDate')
            ->orderByDesc('r.Token')
            ->orderBy('r.RegistaryNumber')
            ->orderBy('r.SecondPartyMobile')
            ->orderBy('r.SecondParty')
            ->orderBy('r.FirstParty')
            ->orderBy('r.District')
            ->orderBy('r.TehsilName')
            ->orderBy('r.Village')
            ->get();
    }

    public function headings(): array
    {
        return array_merge(
            ['Sr. No.'],
            $this->registryColumns(),
            [
                'Registry Row Count',
                'Mobile Row Count',
                'Match Status',
                'Matched Application No.',
                'Matched Owner ID',
                'Matched Owner Name',
                'Matched Father / Husband Name',
                'Matched Owner Mobile',
                'Matched PPP ID',
                'Matched Member ID',
                'Matched Caste',
                'Matched Phase',
            ]
        );
    }

    public function map($row): array
    {
        static $serialNumber = 0;
        $serialNumber++;

        $registryValues = [];

        foreach ($this->registryColumns() as $column) {
            $value = $row->{$column} ?? '';

            if (
                $value !== '' &&
                $value !== null &&
                stripos($column, 'date') !== false
            ) {
                $timestamp = strtotime((string) $value);

                if ($timestamp !== false) {
                    $value = date('d-m-Y', $timestamp);
                }
            }

            $registryValues[] = $value ?? '';
        }

        return array_merge(
            [$serialNumber],
            $registryValues,
            [
                $row->registry_group_count ?? 0,
                $row->mobile_group_count ?? 0,
                !empty($row->matched_owner_id)
                    ? 'Matched'
                    : 'Unmatched',
                $row->matched_registration_no ?? '',
                $row->matched_owner_id ?? '',
                $row->matched_owner_name ?? '',
                $row->matched_father_husband_name ?? '',
                $row->matched_owner_mobile ?? '',
                $row->matched_ppp_id ?? '',
                $row->matched_member_id ?? '',
                $row->matched_caste ?? '',
                $row->matched_phase ?? '',
            ]
        );
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
        ];
    }
}