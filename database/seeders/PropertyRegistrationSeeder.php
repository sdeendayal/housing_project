<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertyRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        // Correct CSV path
        $csvFile = database_path('seeders/data/8PropertyRegistration.csv');

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
                'AssetId'       => (int) ($row[0] ?? 0),
                'AssetName'     => $this->cleanText($row[1] ?? ''),
                'AssetSize'     => (int) ($row[2] ?? 0),
                'Unit'          => $this->cleanText($row[3] ?? null),

                'BranchId'      => $branchId,
                'DistrictId'    => $districtId,
                'CityId'        => $cityId,
                'SectorId'      => $sectorId,

                'IsActive'      => (int) ($row[8] ?? 1),
                'IsDeleted'     => (int) ($row[9] ?? 0),

                'CreatedDate'   => !empty($row[10])
                    ? Carbon::parse($row[10])->format('Y-m-d H:i:s')
                    : null,

                'CreatedBy'     => !empty($row[11])
                    ? (int) $row[11]
                    : null,

                'ModifiedDate'  => !empty($row[12])
                    ? Carbon::parse($row[12])->format('Y-m-d H:i:s')
                    : null,

                'ModifiedBy'    => !empty($row[13])
                    ? (int) $row[13]
                    : null,

                'CompanyId'     => !empty($row[14])
                    ? (int) $row[14]
                    : 544,
            ];
        }

        fclose($file);

        collect($data)->chunk(500)->each(function ($chunk) {
            DB::table('property_registration')->upsert(
                $chunk->toArray(),
                ['AssetId']
            );
        });

        $this->command->info('Property Registration seeded successfully!');
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