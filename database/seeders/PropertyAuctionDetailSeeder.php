<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Seeders\Concerns\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyAuctionDetailSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    private const CHUNK_SIZE = 500;

    public function run(): void
    {
        ini_set('memory_limit', '512M');

        $csvFile = database_path('seeders/data/7PropertyAuctionDetail.csv');

        if (! file_exists($csvFile)) {
            throw new \Exception('CSV file not found: '.$csvFile);
        }

        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $validDistrictIds = [];
        $validCityIds = [];
        $validSectorIds = [];
        $validAssetIds = [];
        $validPurchaserIds = [];
        if ($isSqlite) {
            $validDistrictIds = DB::table('districts')->pluck('DistrictId')->all();
            $validCityIds = DB::table('cities')->pluck('CityId')->all();
            $validSectorIds = DB::table('sectors')->pluck('SectorId')->all();
            $validAssetIds = DB::table('property_registration')->pluck('AssetId')->all();
            $validPurchaserIds = DB::table('property_private_purchasers')->pluck('PrivatePurchaserId')->all();
        }

        $imported = $this->withoutForeignKeyChecks(function () use ($csvFile, $isSqlite, $validDistrictIds, $validCityIds, $validSectorIds, $validAssetIds, $validPurchaserIds) {
            $file = fopen($csvFile, 'r');
            stream_filter_append($file, 'convert.iconv.ISO-8859-1/UTF-8');
            fgetcsv($file);

            $buffer = [];
            $count = 0;

            while (($row = fgetcsv($file, 0, ',')) !== false) {
                if (empty(array_filter($row))) {
                    continue;
                }

                $districtId = (int) ($row[2] ?? 0);
                $cityId = (int) ($row[3] ?? 0);
                $sectorId = (int) ($row[4] ?? 0);
                $assetId = (int) ($row[5] ?? 0);
                $purchaserId = (int) ($row[9] ?? 0);

                if ($isSqlite) {
                    if (
                        !in_array($districtId, $validDistrictIds) ||
                        !in_array($cityId, $validCityIds) ||
                        !in_array($sectorId, $validSectorIds) ||
                        !in_array($assetId, $validAssetIds) ||
                        !in_array($purchaserId, $validPurchaserIds)
                    ) {
                        continue;
                    }
                }

                $buffer[] = [
                    'PropertyAuctionId' => (int) ($row[0] ?? 0),
                    'BranchId' => (int) ($row[1] ?? 0),
                    'DistrictId' => $districtId,
                    'CityId' => $cityId,
                    'SectorId' => $sectorId,
                    'AssetId' => $assetId,
                    'FlatCost' => (float) ($row[6] ?? 0),
                    'ReceivedAmount' => (float) ($row[7] ?? 0),
                    'BalanceAmount' => (float) ($row[8] ?? 0),
                    'PurchaserID' => $purchaserId,
                    'IsActive' => (int) ($row[10] ?? 1),
                    'IsDeleted' => (int) ($row[11] ?? 0),
                    'CreatedDate' => ! empty($row[12])
                        ? Carbon::parse($row[12])->format('Y-m-d H:i:s')
                        : null,
                    'CreatedBy' => ! empty($row[13]) ? (int) $row[13] : null,
                    'ModifiedDate' => ! empty($row[14])
                        ? Carbon::parse($row[14])->format('Y-m-d H:i:s')
                        : null,
                    'ModifiedBy' => ! empty($row[15]) ? (int) $row[15] : null,
                    'CompanyId' => ! empty($row[16]) ? (int) $row[16] : 544,
                ];

                if (count($buffer) >= self::CHUNK_SIZE) {
                    DB::table('property_auction_detail')->upsert($buffer, ['PropertyAuctionId']);
                    $count += count($buffer);
                    $buffer = [];
                }
            }

            fclose($file);

            if ($buffer !== []) {
                DB::table('property_auction_detail')->upsert($buffer, ['PropertyAuctionId']);
                $count += count($buffer);
            }

            return $count;
        });

        $this->command?->info("Property Auction Detail imported: {$imported}");
    }
}
