<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class PropertiesExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithChunkReading
{
    use Exportable;

    public function __construct(
        private readonly array $filters
    ) {
    }

    public function query()
    {
        $receiptTotals = DB::table('cash_receipt_details')
            ->selectRaw('
                asset_number,
                SUM(total_paid_amount) AS receipt_paid
            ')
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->groupBy('asset_number');

        return DB::table('property_registration as pr')
            ->leftJoin(
                'districts as d',
                'd.DistrictId',
                '=',
                'pr.DistrictId'
            )
            ->leftJoin(
                'cities as c',
                'c.CityId',
                '=',
                'pr.CityId'
            )
            ->leftJoin(
                'sectors as s',
                's.SectorId',
                '=',
                'pr.SectorId'
            )
            ->leftJoin('property_auction_detail as pad', function ($join) {
                $join->on('pad.AssetId', '=', 'pr.AssetId')
                    ->where('pad.IsDeleted', 0)
                    ->where('pad.IsActive', 1);
            })
            ->leftJoin(
                'property_private_purchasers as ppp',
                function ($join) {
                    $join->on(
                        'ppp.PrivatePurchaserId',
                        '=',
                        'pad.PurchaserID'
                    )
                        ->where('ppp.IsDeleted', 0)
                        ->where('ppp.IsActive', 1);
                }
            )
            ->leftJoinSub($receiptTotals, 'cr', function ($join) {
                $join->on('cr.asset_number', '=', 'pr.AssetId');
            })
            ->select([
                'pr.AssetId',
                'pr.AssetName',
                'pr.AssetSize',
                'pr.Unit',
                'd.DistrictName',
                'c.CityName',
                's.SectorName',
                'ppp.ApplicationNo',
                'ppp.PrivatePurchaserName',
                'ppp.MobileNo',
                'pad.FlatCost',
            ])
            ->selectRaw('
                (
                    COALESCE(pad.ReceivedAmount, 0)
                    + COALESCE(cr.receipt_paid, 0)
                ) AS total_received,

                GREATEST(
                    COALESCE(pad.FlatCost, 0)
                    - (
                        COALESCE(pad.ReceivedAmount, 0)
                        + COALESCE(cr.receipt_paid, 0)
                    ),
                    0
                ) AS pending_amount
            ')
            ->where('pr.IsDeleted', 0)
            ->where('pr.IsActive', 1)
            ->when(
                $this->filters['district_id'] ?? null,
                fn ($query, $value) =>
                    $query->where('pr.DistrictId', $value)
            )
            ->when(
                $this->filters['city_id'] ?? null,
                fn ($query, $value) =>
                    $query->where('pr.CityId', $value)
            )
            ->when(
                $this->filters['sector_id'] ?? null,
                fn ($query, $value) =>
                    $query->where('pr.SectorId', $value)
            )
            ->orderBy('pr.AssetId');
    }

    public function headings(): array
    {
        return [
            'Asset ID',
            'Asset Name',
            'Size',
            'Unit',
            'District',
            'City',
            'Sector',
            'Application Number',
            'Purchaser Name',
            'Mobile',
            'Total Cost',
            'Received Amount',
            'Pending Amount',
        ];
    }

    public function map($row): array
    {
        return [
            $row->AssetId,
            $row->AssetName,
            $row->AssetSize,
            $row->Unit,
            $row->DistrictName,
            $row->CityName,
            $row->SectorName,
            $row->ApplicationNo,
            $row->PrivatePurchaserName,
            $row->MobileNo,
            (float) ($row->FlatCost ?? 0),
            (float) ($row->total_received ?? 0),
            (float) ($row->pending_amount ?? 0),
        ];
    }

    public function chunkSize(): int
    {
        return 2000;
    }
}