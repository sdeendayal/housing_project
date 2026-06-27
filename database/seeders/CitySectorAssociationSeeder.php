<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Seeders\Concerns\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySectorAssociationSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    private const CHUNK_SIZE = 500;

    public function run(): void
    {
        $csvFile = database_path('seeders/data/5City_Sector_Associations.csv');

        if (! file_exists($csvFile)) {
            throw new \Exception('CSV file not found: '.$csvFile);
        }

        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $validCityIds = [];
        $validSectorIds = [];
        if ($isSqlite) {
            $validCityIds = DB::table('cities')->pluck('CityId')->all();
            $validSectorIds = DB::table('sectors')->pluck('SectorId')->all();
        }

        $imported = $this->withoutForeignKeyChecks(function () use ($csvFile, $isSqlite, $validCityIds, $validSectorIds) {
            $file = fopen($csvFile, 'r');
            fgetcsv($file);

            $buffer = [];
            $count = 0;

            while (($row = fgetcsv($file, 0, ',')) !== false) {
                if (empty(array_filter($row))) {
                    continue;
                }

                $cityId = isset($row[2]) ? (int) $row[2] : null;
                $sectorId = isset($row[3]) ? (int) $row[3] : null;

                if ($isSqlite) {
                    if (!in_array($cityId, $validCityIds) || !in_array($sectorId, $validSectorIds)) {
                        continue;
                    }
                }

                $buffer[] = [
                    'AssociationId' => isset($row[0]) && $row[0] !== '' ? (int) $row[0] : null,
                    'BranchId' => isset($row[1]) ? (int) $row[1] : null,
                    'CityId' => $cityId,
                    'SectorId' => $sectorId,
                    'Is_Active' => isset($row[4]) ? (int) $row[4] : 1,
                    'Is_Deleted' => isset($row[5]) ? (int) $row[5] : 0,
                    'CreatedDate' => ! empty($row[6])
                        ? Carbon::parse($row[6])->format('Y-m-d H:i:s')
                        : null,
                    'CreatedBy' => ! empty($row[7]) ? (int) $row[7] : null,
                    'ModifiedDate' => ! empty($row[8])
                        ? Carbon::parse($row[8])->format('Y-m-d H:i:s')
                        : null,
                    'ModifiedBy' => ! empty($row[9]) ? (int) $row[9] : null,
                    'CompanyId' => isset($row[10]) && $row[10] !== ''
                        ? (int) $row[10]
                        : 544,
                ];

                if (count($buffer) >= self::CHUNK_SIZE) {
                    DB::table('city_sector_associations')->upsert(
                        $buffer,
                        ['AssociationId'],
                        [
                            'BranchId', 'CityId', 'SectorId', 'Is_Active', 'Is_Deleted',
                            'CreatedDate', 'CreatedBy', 'ModifiedDate', 'ModifiedBy', 'CompanyId',
                        ]
                    );
                    $count += count($buffer);
                    $buffer = [];
                }
            }

            fclose($file);

            if ($buffer !== []) {
                DB::table('city_sector_associations')->upsert(
                    $buffer,
                    ['AssociationId'],
                    [
                        'BranchId', 'CityId', 'SectorId', 'Is_Active', 'Is_Deleted',
                        'CreatedDate', 'CreatedBy', 'ModifiedDate', 'ModifiedBy', 'CompanyId',
                    ]
                );
                $count += count($buffer);
            }

            return $count;
        });

        $this->command?->info("City Sector Associations imported: {$imported}");
    }
}
