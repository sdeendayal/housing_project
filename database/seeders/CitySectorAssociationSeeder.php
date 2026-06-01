<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitySectorAssociationSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('seeders/data/5City_Sector_Associations.csv');

        if (!file_exists($csvFile)) {
            throw new \Exception("CSV file not found: " . $csvFile);
        }

        $file = fopen($csvFile, 'r');

        // Header skip
        fgetcsv($file);

        $data = [];

        while (($row = fgetcsv($file, 1000, ',')) !== false) {

            // Empty row skip
            if (empty(array_filter($row))) {
                continue;
            }

            $branchId = isset($row[1]) ? (int)$row[1] : null;
            $cityId   = isset($row[2]) ? (int)$row[2] : null;
            $sectorId = isset($row[3]) ? (int)$row[3] : null;

            // FK validation
            $branchExists = DB::table('em_offices')
                ->where('BranchId', $branchId)
                ->exists();

            $cityExists = DB::table('cities')
                ->where('CityId', $cityId)
                ->exists();

            $sectorExists = DB::table('sectors')
                ->where('SectorId', $sectorId)
                ->exists();

            // Invalid row skip
            if (!$branchExists || !$cityExists || !$sectorExists) {

                dump([
                    'Invalid Row' => $row,
                    'Branch Exists' => $branchExists,
                    'City Exists' => $cityExists,
                    'Sector Exists' => $sectorExists,
                ]);

                continue;
            }

            $data[] = [
                'AssociationId' => isset($row[0]) && $row[0] !== '' ? (int) $row[0] : null,
                'BranchId'      => $branchId,
                'CityId'        => $cityId,
                'SectorId'      => $sectorId,
                'Is_Active'     => isset($row[4]) ? (int) $row[4] : 1,
                'Is_Deleted'    => isset($row[5]) ? (int) $row[5] : 0,

                'CreatedDate'   => !empty($row[6])
                    ? Carbon::parse($row[6])->format('Y-m-d H:i:s')
                    : null,

                'CreatedBy'     => !empty($row[7])
                    ? (int) $row[7]
                    : null,

                'ModifiedDate'  => !empty($row[8])
                    ? Carbon::parse($row[8])->format('Y-m-d H:i:s')
                    : null,

                'ModifiedBy'    => !empty($row[9])
                    ? (int) $row[9]
                    : null,

                'CompanyId'     => isset($row[10]) && $row[10] !== ''
                    ? (int) $row[10]
                    : 544,
            ];
        }

        fclose($file);

        collect($data)->chunk(500)->each(function ($chunk) {
            DB::table('city_sector_associations')->upsert(
                $chunk->toArray(),
                ['AssociationId'],
                [
                    'BranchId',
                    'CityId',
                    'SectorId',
                    'Is_Active',
                    'Is_Deleted',
                    'CreatedDate',
                    'CreatedBy',
                    'ModifiedDate',
                    'ModifiedBy',
                    'CompanyId'
                ]
            );
        });

        $this->command->info('City Sector Associations seeded successfully!');
    }
}