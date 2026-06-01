<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertyPrivatePurchasersSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('seeders/data/6PropertyPrivatePurchasers.csv');

        if (!file_exists($csvFile)) {
            throw new \Exception("CSV file not found: " . $csvFile);
        }

        $file = fopen($csvFile, 'r');

        // UTF-8 encoding fix
        stream_filter_append($file, 'convert.iconv.ISO-8859-1/UTF-8');

        // Header skip
        fgetcsv($file);

        $data = [];

        while (($row = fgetcsv($file, 1000, ',')) !== false) {

            if (empty(array_filter($row))) {
                continue;
            }

            $branchId   = (int) ($row[11] ?? 0);
            $districtId = (int) ($row[12] ?? 0);
            $cityId     = (int) ($row[13] ?? 0);
            $sectorId   = (int) ($row[14] ?? 0);

            // FK validation
            if (
                !DB::table('em_offices')->where('BranchId', $branchId)->exists() ||
                !DB::table('districts')->where('DistrictId', $districtId)->exists() ||
                !DB::table('cities')->where('CityId', $cityId)->exists() ||
                !DB::table('sectors')->where('SectorId', $sectorId)->exists()
            ) {
                dump(['Invalid Row' => $row]);
                continue;
            }

            $data[] = [
                'PrivatePurchaserId'   => (int) ($row[0] ?? 0),
                'Flat_Id'              => (int) ($row[1] ?? 0),

                'PrivatePurchaserName' => $this->cleanText($row[2] ?? ''),
                'PurchaserFatherName'  => $this->cleanText($row[3] ?? null),

                'MobileNo'             => !empty($row[4]) ? $row[4] : null,
                'ApplicationNo'        => !empty($row[5]) ? $row[5] : null,
                'PPPId'                => $row[6] ?? null,
                'MemberID'             => $row[7] ?? null,

                'CasteCategoryName'    => $this->cleanText($row[8] ?? null),
                'MaritalStatus'        => $this->cleanText($row[9] ?? null),
                'Address'              => $this->cleanText($row[10] ?? null),

                'BranchId'             => $branchId,
                'DistrictId'           => $districtId,
                'CityId'               => $cityId,
                'SectorId'             => $sectorId,

                'IsActive'             => (int) ($row[15] ?? 1),
                'IsDeleted'            => (int) ($row[16] ?? 0),
                'UserLoginCreated'     => (int) ($row[17] ?? 0),
                'Is_UserLogin_Active'  => (int) ($row[18] ?? 0),
                'Is_UserLogin_Deleted' => (int) ($row[19] ?? 0),

                'CreateDate' => !empty($row[20])
                    ? Carbon::parse($row[20])->format('Y-m-d H:i:s')
                    : null,

                'CreatedBy' => !empty($row[21])
                    ? (int) $row[21]
                    : null,

                'ModifiedDate' => !empty($row[22])
                    ? Carbon::parse($row[22])->format('Y-m-d H:i:s')
                    : null,

                'ModifiedBy' => !empty($row[23])
                    ? (int) $row[23]
                    : null,

                'CompanyId' => !empty($row[24])
                    ? (int) $row[24]
                    : 544,
            ];
        }

        fclose($file);

        collect($data)->chunk(500)->each(function ($chunk) {
            DB::table('property_private_purchasers')->upsert(
                $chunk->toArray(),
                ['PrivatePurchaserId']
            );
        });

        $this->command->info('Property Private Purchasers seeded successfully!');
    }

    private function cleanText($text)
    {
        if (empty($text)) {
            return null;
        }

        return mb_convert_encoding(
            trim($text),
            'UTF-8',
            'UTF-8, ISO-8859-1, Windows-1252'
        );
    }
}