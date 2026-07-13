<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegistarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = 'C:/Users/hp/Downloads/registary.csv';

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            return;
        }

        $file = fopen($csvPath, 'r');
        
        // Read header
        $header = fgetcsv($file);
        if (!$header) {
            $this->command->error("CSV file is empty.");
            fclose($file);
            return;
        }

        // Clean headers (remove BOM or spaces if any)
        $header = array_map(function($h) {
            return trim($h, "\xEF\xBB\xBF \t\n\r\0\x0B");
        }, $header);

        $batch = [];
        $batchSize = 500;
        $count = 0;

        // Clear existing table data to avoid duplicates if re-running
        DB::table('registary')->truncate();

        while (($row = fgetcsv($file)) !== false) {
            if (count($header) !== count($row)) {
                // Skip invalid rows or pad them
                continue;
            }

            $data = array_combine($header, $row);

            $batch[] = [
                'District' => $data['District'] ?? null,
                'TehsilName' => $data['TehsilName'] ?? null,
                'Village' => $data['Village'] ?? null,
                'Token' => $data['Token'] ?? null,
                'Khewat' => $data['Khewat'] ?? null,
                'FirstParty' => $data['FirstParty'] ?? null,
                'TotalArea' => $data['TotalArea'] ?? null,
                'Bhag' => $data['Bhag'] ?? null,
                'TransferArea' => $data['TransferArea'] ?? null,
                'SecondParty' => $data['SecondParty'] ?? null,
                'SecondPartyMobile' => $data['SecondPartyMobile'] ?? null,
                'RegistaryNumber' => $data['RegistaryNumber'] ?? null,
                'RegistaryDate' => !empty($data['RegistaryDate']) ? $data['RegistaryDate'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('registary')->insert($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            DB::table('registary')->insert($batch);
            $count += count($batch);
        }

        fclose($file);

        $this->command->info("Successfully seeded {$count} records into the registary table.");
    }
}
