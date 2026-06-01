<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmOfficeSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('seeders/data/1EmOffices.csv');

        if (!file_exists($csvFile)) {
            throw new \Exception("CSV file not found: " . $csvFile);
        }

        $file = fopen($csvFile, 'r');

        // Header read & skip
        $header = fgetcsv($file);

        $data = [];

        while (($row = fgetcsv($file, 1000, ',')) !== false) {

            // Empty row skip
            if (empty(array_filter($row))) {
                continue;
            }

            $data[] = [
                'BranchId'     => isset($row[0]) && $row[0] !== '' ? (int) $row[0] : null,
                'BranchName'   => $row[1] ?? '',
                'Is_Active'    => isset($row[2]) ? (int) $row[2] : 1,
                'Is_Deleted'   => isset($row[3]) ? (int) $row[3] : 0,

                'CreatedDate'  => !empty($row[4])
                    ? Carbon::parse($row[4])->format('Y-m-d H:i:s')
                    : null,

                'CreatedBy'    => !empty($row[5])
                    ? (int) $row[5]
                    : null,

                'ModifiedDate' => !empty($row[6])
                    ? Carbon::parse($row[6])->format('Y-m-d H:i:s')
                    : null,

                'ModifiedBy'   => !empty($row[7])
                    ? (int) $row[7]
                    : null,

                'CompanyId'    => isset($row[8]) && $row[8] !== ''
                    ? (int) $row[8]
                    : 544,
            ];
        }

        fclose($file);

        // Chunk insert for performance
        collect($data)->chunk(500)->each(function ($chunk) {

            DB::table('em_offices')->upsert(
                $chunk->toArray(),
                ['BranchId'], // unique column
                [
                    'BranchName',
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

        $this->command->info('Em Offices seeded successfully!');
    }
}