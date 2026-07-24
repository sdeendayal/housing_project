<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PptPanipatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Delete existing Panipat records to make this seeder re-runnable
        $this->command->info("Clearing existing Panipat records (district_id = 18) from ppt_members table...");
        DB::table('ppt_members')->where('district_id', 18)->delete();

        $csvPath = database_path('seeders/data/panipat.csv');
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

            // Inject district PANIPAT and district_id = 18
            $data['district'] = 'PANIPAT';
            $data['district_id'] = 18;

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
        $this->command->info("Successfully imported {$count} Panipat members into ppt_members table in {$duration} seconds.");
    }
}
