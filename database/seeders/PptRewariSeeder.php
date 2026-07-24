<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PptRewariSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Delete existing Rewari records to make this seeder re-runnable
        $this->command->info("Clearing existing Rewari records (district_id = 19) from ppt_members table...");
        DB::table('ppt_members')->where('district_id', 19)->delete();

        $csvPath = database_path('seeders/data/rewari.csv');
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            return;
        }

        $handle = fopen($csvPath, 'r');
        $headers = fgetcsv($handle);
        if ($headers === false) {
            fclose($handle);
            return;
        }

        $headers = array_map('trim', $headers);
        $batch = [];
        $count = 0;
        $startTime = microtime(true);

        while (($row = fgetcsv($handle)) !== false) {
            // Skip empty or mismatch rows
            if (count($row) !== count($headers)) {
                continue;
            }

            $data = array_combine($headers, $row);
            if ($data === false) {
                continue;
            }

            // Inject district REWARI and district_id = 19
            $data['district'] = 'REWARI';
            $data['district_id'] = 19;

            // Ensure valid created_at and updated_at timestamps
            if (empty($data['created_at'])) {
                $data['created_at'] = now();
            }
            if (empty($data['updated_at'])) {
                $data['updated_at'] = now();
            }

            $batch[] = $data;
            $count++;

            // Batch insert in chunks of 200
            if (count($batch) >= 200) {
                DB::table('ppt_members')->insert($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            DB::table('ppt_members')->insert($batch);
        }

        fclose($handle);
        $duration = round(microtime(true) - $startTime, 2);
        $this->command->info("Successfully imported {$count} Rewari members into ppt_members table in {$duration} seconds.");
    }
}
