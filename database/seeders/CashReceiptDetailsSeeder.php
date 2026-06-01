<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashReceiptDetailsSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('seeders/data/9CashReceiptDetails.csv');

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

            $branchId   = (int) ($row[4] ?? 0);
            $districtId = (int) ($row[5] ?? 0);
            $cityId     = (int) ($row[6] ?? 0);
            $sectorId   = (int) ($row[7] ?? 0);
            $assetId    = (int) ($row[1] ?? 0);

            // FK validation
            if (
                !DB::table('em_offices')->where('BranchId', $branchId)->exists() ||
                !DB::table('districts')->where('DistrictId', $districtId)->exists() ||
                !DB::table('cities')->where('CityId', $cityId)->exists() ||
                !DB::table('sectors')->where('SectorId', $sectorId)->exists() ||
                !DB::table('property_registration')->where('AssetId', $assetId)->exists()
            ) {
                dump(['Invalid Row' => $row]);
                continue;
            }

            $data[] = [
                'id'                => (int) ($row[0] ?? 0),
                'asset_number'      => $assetId,
                'total_paid_amount' => (float) ($row[2] ?? 0),
                'receipt_number'    => $row[3] ?? null,

                'BranchId'          => $branchId,
                'DistrictId'        => $districtId,
                'CityId'            => $cityId,
                'SectorId'          => $sectorId,

                'IsActive'          => (int) ($row[8] ?? 1),
                'IsDeleted'         => (int) ($row[9] ?? 0),

                'created_date' => !empty($row[10])
                    ? Carbon::parse($row[10])->format('Y-m-d H:i:s')
                    : null,

                'CreatedBy' => !empty($row[11])
                    ? (int) $row[11]
                    : null,

                'ModifiedDate' => !empty($row[12])
                    ? Carbon::parse($row[12])->format('Y-m-d H:i:s')
                    : null,

                'ModifiedBy' => !empty($row[13])
                    ? (int) $row[13]
                    : null,

                'CompanyId' => !empty($row[14])
                    ? (int) $row[14]
                    : 544,
            ];
        }

        fclose($file);

        collect($data)->chunk(500)->each(function ($chunk) {
            DB::table('cash_receipt_details')->upsert(
                $chunk->toArray(),
                ['id']
            );
        });

        $this->command->info('Cash Receipt Details seeded successfully!');
    }
}