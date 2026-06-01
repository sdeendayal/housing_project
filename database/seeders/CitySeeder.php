<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('seeders/data/3Cities.csv');

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

            $data[] = [
                'CityId'       => isset($row[0]) && $row[0] !== '' ? (int) $row[0] : null,
                'CityName'     => $row[1] ?? '',
                'BranchId'     => isset($row[2]) ? (int) $row[2] : null,
                'DistrictId'   => isset($row[3]) ? (int) $row[3] : null,
                'Is_Active'    => isset($row[4]) ? (int) $row[4] : 1,
                'Is_Deleted'   => isset($row[5]) ? (int) $row[5] : 0,

                'CreatedDate'  => !empty($row[6])
                    ? Carbon::parse($row[6])->format('Y-m-d H:i:s')
                    : null,

                'CreatedBy'    => !empty($row[7])
                    ? (int) $row[7]
                    : null,

                'ModifiedDate' => !empty($row[8])
                    ? Carbon::parse($row[8])->format('Y-m-d H:i:s')
                    : null,

                'ModifiedBy'   => !empty($row[9])
                    ? (int) $row[9]
                    : null,

                'CompanyId'    => isset($row[10]) && $row[10] !== ''
                    ? (int) $row[10]
                    : 544,
            ];
        }

        fclose($file);

        collect($data)->chunk(500)->each(function ($chunk) {

            DB::table('cities')->upsert(
                $chunk->toArray(),
                ['CityId'],
                [
                    'CityName',
                    'BranchId',
                    'DistrictId',
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

        $this->command->info('Cities seeded successfully!');
    }
}