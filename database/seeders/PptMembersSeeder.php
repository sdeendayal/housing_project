<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PptMembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table first to avoid duplicate records on re-run
        DB::table('ppt_members')->truncate();

        $csvPath = database_path('seeders/data/ppt_members.csv');
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

        while (($row = fgetcsv($handle)) !== false) {
            // Skip empty or mismatch rows
            if (count($row) !== count($headers)) {
                continue;
            }

            $data = array_combine($headers, $row);
            if ($data === false) {
                continue;
            }

            // Inject the 2 requested extra columns
            $data['district'] = 'SONIPAT';
            $data['district_id'] = 22;

            // Ensure valid created_at and updated_at timestamps
            if (empty($data['created_at'])) {
                $data['created_at'] = now();
            }
            if (empty($data['updated_at'])) {
                $data['updated_at'] = now();
            }

            $batch[] = $data;
            $count++;

            // Batch insert in chunks of 200 to keep it within MySQL's placeholder limits
            if (count($batch) >= 200) {
                DB::table('ppt_members')->insert($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            DB::table('ppt_members')->insert($batch);
        }

        fclose($handle);
        $this->command->info("Successfully imported {$count} members into ppt_members table.");
    }
}
