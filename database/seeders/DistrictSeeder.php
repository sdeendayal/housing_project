<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('seeders/data/2Districts.csv');

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
                'DistrictId'   => isset($row[0]) && $row[0] !== '' ? (int) $row[0] : null,
                'DistrictName' => $row[1] ?? '',
                'BranchId'     => isset($row[2]) ? (int) $row[2] : null,
                'Is_Active'    => isset($row[3]) ? (int) $row[3] : 1,
                'Is_Deleted'   => isset($row[4]) ? (int) $row[4] : 0,

                'CreatedDate'  => !empty($row[5])
                    ? Carbon::parse($row[5])->format('Y-m-d H:i:s')
                    : null,

                'CreatedBy'    => !empty($row[6])
                    ? (int) $row[6]
                    : null,

                'ModifiedDate' => !empty($row[7])
                    ? Carbon::parse($row[7])->format('Y-m-d H:i:s')
                    : null,

                'ModifiedBy'   => !empty($row[8])
                    ? (int) $row[8]
                    : null,

                'CompanyId'    => isset($row[9]) && $row[9] !== ''
                    ? (int) $row[9]
                    : 544,
            ];
        }

        fclose($file);

        collect($data)->chunk(500)->each(function ($chunk) {

            DB::table('districts')->upsert(
                $chunk->toArray(),
                ['DistrictId'],
                [
                    'DistrictName',
                    'BranchId',
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

        $this->command->info('Districts seeded successfully!');
    }
}