<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MmsayOldRegistrationDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table first to avoid duplicate records on re-run
        DB::table('mmsay_old_registration_data')->truncate();

        $dataDir = database_path('seeders/data/mmsay_old_registration_data');
        if (!is_dir($dataDir)) {
            $this->command->error("Data directory not found at: {$dataDir}");
            return;
        }

        $csvFiles = glob($dataDir . '/*.csv');
        if (empty($csvFiles)) {
            $this->command->error("No CSV files found in: {$dataDir}");
            return;
        }

        // Sort files to import in a deterministic order
        sort($csvFiles);

        $totalCount = 0;
        $batchSize = 500;

        foreach ($csvFiles as $csvPath) {
            $filename = basename($csvPath);
            $this->command->info("Importing file: {$filename}");

            if (!file_exists($csvPath)) {
                $this->command->error("CSV file not found at: {$csvPath}");
                continue;
            }

            $handle = fopen($csvPath, 'r');
            if (!$handle) {
                $this->command->error("Could not open file: {$csvPath}");
                continue;
            }

            // Read headers
            $headers = fgetcsv($handle);
            if ($headers === false) {
                fclose($handle);
                continue;
            }

            // Clean headers (remove BOM or spaces if any)
            $headers = array_map(function ($h) {
                return trim($h, "\xEF\xBB\xBF \t\n\r\0\x0B");
            }, $headers);

            $batch = [];
            $fileCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                // Skip mismatch rows or pad/trim
                if (count($row) !== count($headers)) {
                    if (count($row) < count($headers)) {
                        $row = array_pad($row, count($headers), null);
                    } else {
                        $row = array_slice($row, 0, count($headers));
                    }
                }

                $data = array_combine($headers, $row);
                if ($data === false) {
                    continue;
                }

                // Remove any fields matching 'id', we let the database handle it
                unset($data['id']);

                // Ensure valid created_at and updated_at timestamps
                $data['created_at'] = now();
                $data['updated_at'] = now();

                $batch[] = $data;
                $fileCount++;
                $totalCount++;

                if (count($batch) >= $batchSize) {
                    DB::table('mmsay_old_registration_data')->insert($batch);
                    $batch = [];
                }
            }

            if (count($batch) > 0) {
                DB::table('mmsay_old_registration_data')->insert($batch);
            }

            fclose($handle);
            $this->command->info("Successfully imported {$fileCount} records from {$filename}.");
        }

        $this->command->info("Successfully imported total of {$totalCount} records into mmsay_old_registration_data table.");
    }
}
