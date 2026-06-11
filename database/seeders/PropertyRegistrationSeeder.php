<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Seeders\Concerns\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyRegistrationSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    private const CHUNK_SIZE = 500;

    public function run(): void
    {
        ini_set('memory_limit', '512M');

        $csvFile = database_path('seeders/data/8PropertyRegistration.csv');

        if (! file_exists($csvFile)) {
            throw new \Exception('CSV file not found: '.$csvFile);
        }

        $imported = $this->withoutForeignKeyChecks(function () use ($csvFile) {
            $file = fopen($csvFile, 'r');
            stream_filter_append($file, 'convert.iconv.ISO-8859-1/UTF-8');
            fgetcsv($file);

            $buffer = [];
            $count = 0;

            while (($row = fgetcsv($file, 0, ',')) !== false) {
                if (empty(array_filter($row))) {
                    continue;
                }

                $buffer[] = [
                    'AssetId' => (int) ($row[0] ?? 0),
                    'AssetName' => $this->cleanText($row[1] ?? ''),
                    'AssetSize' => (int) ($row[2] ?? 0),
                    'Unit' => $this->cleanText($row[3] ?? null),
                    'BranchId' => (int) ($row[4] ?? 0),
                    'DistrictId' => (int) ($row[5] ?? 0),
                    'CityId' => (int) ($row[6] ?? 0),
                    'SectorId' => (int) ($row[7] ?? 0),
                    'IsActive' => (int) ($row[8] ?? 1),
                    'IsDeleted' => (int) ($row[9] ?? 0),
                    'CreatedDate' => ! empty($row[10])
                        ? Carbon::parse($row[10])->format('Y-m-d H:i:s')
                        : null,
                    'CreatedBy' => ! empty($row[11]) ? (int) $row[11] : null,
                    'ModifiedDate' => ! empty($row[12])
                        ? Carbon::parse($row[12])->format('Y-m-d H:i:s')
                        : null,
                    'ModifiedBy' => ! empty($row[13]) ? (int) $row[13] : null,
                    'CompanyId' => ! empty($row[14]) ? (int) $row[14] : 544,
                ];

                if (count($buffer) >= self::CHUNK_SIZE) {
                    DB::table('property_registration')->upsert($buffer, ['AssetId']);
                    $count += count($buffer);
                    $buffer = [];
                }
            }

            fclose($file);

            if ($buffer !== []) {
                DB::table('property_registration')->upsert($buffer, ['AssetId']);
                $count += count($buffer);
            }

            return $count;
        });

        $this->command?->info("Property Registration imported: {$imported}");
    }

    private function cleanText($text)
    {
        if (empty($text)) {
            return null;
        }

        return mb_convert_encoding(trim($text), 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
    }
}
