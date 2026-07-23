<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PptGurugramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Delete existing Gurugram records to ensure idempotency
        $this->command->info("Clearing existing Gurugram records (district_id = 6) from ppt_members table...");
        DB::table('ppt_members')->where('district_id', 6)->delete();

        $csvPath = database_path('seeders/data/ppt_gurugram.csv');
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            return;
        }

        $this->command->info("Starting import of Gurugram members from: {$csvPath}");

        $handle = fopen($csvPath, 'r');
        $headers = fgetcsv($handle);
        if ($headers === false) {
            fclose($handle);
            $this->command->error("Unable to read headers from CSV.");
            return;
        }

        // Clean headers to remove spaces and UTF-8 BOM
        $headers = array_map(function($header) {
            $header = preg_replace('/^[\xef\xbb\xbf\xff\xfe]+/i', '', trim($header));
            return $header;
        }, $headers);

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

            // Inject district GURUGRAM and district_id = 6
            $data['district'] = 'GURUGRAM';
            $data['district_id'] = 6;

            // Ensure valid created_at and updated_at timestamps
            if (empty($data['created_at'])) {
                $data['created_at'] = now();
            }
            if (empty($data['updated_at'])) {
                $data['updated_at'] = now();
            }

            // Remove 'id' and '﻿id' (with BOM) to let database auto-increment the ID and avoid primary key duplicate errors
            unset($data['id']);
            unset($data['﻿id']);

            $batch[] = $data;
            $count++;

            // Batch insert in chunks of 100 to avoid MySQL placeholder limit (134 columns * 100 = 13400 placeholders)
            if (count($batch) >= 100) {
                DB::table('ppt_members')->insert($batch);
                $batch = [];
                if ($count % 5000 === 0) {
                    $this->command->info("Seeded {$count} records...");
                }
            }
        }

        if (count($batch) > 0) {
            DB::table('ppt_members')->insert($batch);
        }

        fclose($handle);
        $duration = round(microtime(true) - $startTime, 2);
        $this->command->info("Successfully imported {$count} Gurugram members into ppt_members table in {$duration} seconds.");
    }
}
