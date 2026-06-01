<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertyAuctionDetailSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('seeders/data/7PropertyAuctionDetail.csv');

        if (!file_exists($csvFile)) {
            throw new \Exception("CSV file not found: " . $csvFile);
        }

        $file = fopen($csvFile, 'r');

        // Encoding fix
        stream_filter_append($file, 'convert.iconv.ISO-8859-1/UTF-8');

        // Header skip
        fgetcsv($file);

        $data = [];

        while (($row = fgetcsv($file, 1000, ',')) !== false) {

            if (empty(array_filter($row))) {
                continue;
            }

            $branchId    = (int) ($row[1] ?? 0);
            $districtId  = (int) ($row[2] ?? 0);
            $cityId      = (int) ($row[3] ?? 0);
            $sectorId    = (int) ($row[4] ?? 0);
            $assetId     = (int) ($row[5] ?? 0);
            $purchaserId = (int) ($row[9] ?? 0);

            // FK validation
            if (
                !DB::table('em_offices')->where('BranchId', $branchId)->exists() ||
                !DB::table('districts')->where('DistrictId', $districtId)->exists() ||
                !DB::table('cities')->where('CityId', $cityId)->exists() ||
                !DB::table('sectors')->where('SectorId', $sectorId)->exists() ||
                !DB::table('property_registration')->where('AssetId', $assetId)->exists() ||
                !DB::table('property_private_purchasers')->where('PrivatePurchaserId', $purchaserId)->exists()
            ) {
                dump(['Invalid Row' => $row]);
                continue;
            }

            $data[] = [
                'PropertyAuctionId' => (int) ($row[0] ?? 0),
                'BranchId'          => $branchId,
                'DistrictId'        => $districtId,
                'CityId'            => $cityId,
                'SectorId'          => $sectorId,
                'AssetId'           => $assetId,

                'FlatCost'          => (float) ($row[6] ?? 0),
                'ReceivedAmount'    => (float) ($row[7] ?? 0),
                'BalanceAmount'     => (float) ($row[8] ?? 0),

                'PurchaserID'       => $purchaserId,

                'IsActive'          => (int) ($row[10] ?? 1),
                'IsDeleted'         => (int) ($row[11] ?? 0),

                'CreatedDate' => !empty($row[12])
                    ? Carbon::parse($row[12])->format('Y-m-d H:i:s')
                    : null,

                'CreatedBy' => !empty($row[13])
                    ? (int) $row[13]
                    : null,

                'ModifiedDate' => !empty($row[14])
                    ? Carbon::parse($row[14])->format('Y-m-d H:i:s')
                    : null,

                'ModifiedBy' => !empty($row[15])
                    ? (int) $row[15]
                    : null,

                'CompanyId' => !empty($row[16])
                    ? (int) $row[16]
                    : 544,
            ];
        }

        fclose($file);

        collect($data)->chunk(500)->each(function ($chunk) {
            DB::table('property_auction_detail')->upsert(
                $chunk->toArray(),
                ['PropertyAuctionId']
            );
        });

        $this->command->info('Property Auction Detail seeded successfully!');
    }
}