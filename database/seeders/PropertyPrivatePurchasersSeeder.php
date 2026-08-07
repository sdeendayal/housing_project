<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Seeders\Concerns\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyPrivatePurchasersSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    private const CHUNK_SIZE = 500;

    public function run(): void
    {
        ini_set('memory_limit', '512M');

        $csvFile = database_path('seeders/data/6PropertyPrivatePurchasers.csv');

        if (! file_exists($csvFile)) {
            throw new \Exception('CSV file not found: '.$csvFile);
        }

        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $validDistrictIds = [];
        $validCityIds = [];
        $validSectorIds = [];
        if ($isSqlite) {
            $validDistrictIds = DB::table('districts')->pluck('DistrictId')->all();
            $validCityIds = DB::table('cities')->pluck('CityId')->all();
            $validSectorIds = DB::table('sectors')->pluck('SectorId')->all();
        }

        $imported = $this->withoutForeignKeyChecks(function () use ($csvFile, $isSqlite, $validDistrictIds, $validCityIds, $validSectorIds) {
            $file = fopen($csvFile, 'r');
            stream_filter_append($file, 'convert.iconv.ISO-8859-1/UTF-8');
            fgetcsv($file);

            $buffer = [];
            $count = 0;

            while (($row = fgetcsv($file, 0, ',')) !== false) {
                if (empty(array_filter($row))) {
                    continue;
                }

                $districtId = (int) ($row[12] ?? 0);
                $cityId = (int) ($row[13] ?? 0);
                $sectorId = (int) ($row[14] ?? 0);

                if ($isSqlite) {
                    if (!in_array($districtId, $validDistrictIds) || !in_array($cityId, $validCityIds) || !in_array($sectorId, $validSectorIds)) {
                        continue;
                    }
                }

                $buffer[] = [
                    'PrivatePurchaserId' => (int) ($row[0] ?? 0),
                    'Flat_Id' => (int) ($row[1] ?? 0),
                    'PrivatePurchaserName' => $this->cleanText($row[2] ?? ''),
                    'PurchaserFatherName' => $this->cleanText($row[3] ?? null),
                    'MobileNo' => ! empty($row[4]) ? $row[4] : null,
                    'ApplicationNo' => ! empty($row[5]) ? $row[5] : null,
                    'PPPId' => $row[6] ?? null,
                    'MemberID' => $row[7] ?? null,
                    'CasteCategoryName' => $this->cleanText($row[8] ?? null),
                    'MaritalStatus' => $this->cleanText($row[9] ?? null),
                    'Address' => $this->cleanText($row[10] ?? null),
                    'BranchId' => (int) ($row[11] ?? 0),
                    'DistrictId' => $districtId,
                    'CityId' => $cityId,
                    'SectorId' => $sectorId,
                    'IsActive' => (int) ($row[15] ?? 1),
                    'IsDeleted' => (int) ($row[16] ?? 0),
                    'UserLoginCreated' => (int) ($row[17] ?? 0),
                    'Is_UserLogin_Active' => (int) ($row[18] ?? 0),
                    'Is_UserLogin_Deleted' => (int) ($row[19] ?? 0),
                    'CreateDate' => ! empty($row[20])
                        ? Carbon::parse($row[20])->format('Y-m-d H:i:s')
                        : null,
                    'CreatedBy' => ! empty($row[21]) ? (int) $row[21] : null,
                    'ModifiedDate' => ! empty($row[22])
                        ? Carbon::parse($row[22])->format('Y-m-d H:i:s')
                        : null,
                    'ModifiedBy' => ! empty($row[23]) ? (int) $row[23] : null,
                    'CompanyId' => ! empty($row[24]) ? (int) $row[24] : 544,
                    'phase' => '1',
                    'property_type' => 'plot',
                ];

                if (count($buffer) >= self::CHUNK_SIZE) {
                    DB::table('property_private_purchasers')->upsert($buffer, ['PrivatePurchaserId']);
                    $count += count($buffer);
                    $buffer = [];
                }
            }

            fclose($file);

            if ($buffer !== []) {
                DB::table('property_private_purchasers')->upsert($buffer, ['PrivatePurchaserId']);
                $count += count($buffer);
            }

            return $count;
        });

        $this->command?->info("Property Private Purchasers imported: {$imported}");
    }

    private function cleanText($text)
    {
        if (empty($text)) {
            return null;
        }

        return mb_convert_encoding(trim($text), 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
    }
}
