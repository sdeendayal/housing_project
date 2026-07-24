<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Str;

class DistHouseOwnershipRejectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set higher memory limit to avoid exhaustion with large Excel files
        ini_set('memory_limit', '2G');

        $districts = [
            [
                'file' => 'FARIDABAD_Completed_MC_22-01-2026 (2) (3).xlsx',
                'dist_name' => 'FARIDABAD',
                'dist_id' => 4
            ],
            [
                'file' => 'GURGAON_Completed_24-01-2026 (3).xlsx',
                'dist_name' => 'GURUGRAM',
                'dist_id' => 6
            ],
            [
                'file' => 'PANIPAT_MC_Completed_22-01-2026 (2) (3).xlsx',
                'dist_name' => 'PANIPAT',
                'dist_id' => 18
            ],
            [
                'file' => 'REWARI_Completed_25-01-2026 (3).xlsx',
                'dist_name' => 'REWARI',
                'dist_id' => 19
            ],
            [
                'file' => 'Rohtak_completed (2) (3).xlsx',
                'dist_name' => 'ROHTAK',
                'dist_id' => 20
            ]
        ];

        $targetTable = 'ews_house_ownership_reject_4';

        // Clear existing records for these districts
        $distIds = array_column($districts, 'dist_id');
        $this->command->info("Clearing existing records for district IDs (" . implode(',', $distIds) . ") from {$targetTable}...");
        DB::table($targetTable)->whereIn('dist_id', $distIds)->delete();

        $batchSize = 250;
        $totalCount = 0;
        $startTime = microtime(true);

        foreach ($districts as $dist) {
            $filePath = database_path("seeders/data/{$dist['file']}");
            if (!file_exists($filePath)) {
                $this->command->error("Excel file not found at: {$filePath}");
                continue;
            }

            $this->command->info("Loading Excel file from {$filePath}...");
            $spreadsheet = IOFactory::load($filePath);
            
            // Try different cases of the sheet name
            $sheet = $spreadsheet->getSheetByName('House_ownership') ?: $spreadsheet->getSheetByName('house_ownership');
            if (!$sheet) {
                $this->command->error("Sheet 'House_ownership' or 'house_ownership' not found in file {$dist['file']}.");
                continue;
            }

            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            $this->command->info("Excel sheet loaded for {$dist['dist_name']}. Total rows: {$highestRow}, Total columns: {$highestColumnIndex}.");

            // Read header row (row 1)
            $header = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $header[] = trim($sheet->getCell([$col, 1])->getValue()); // Row 1 has headers
            }

            // Clean headers to avoid any hidden characters
            $header = array_map(function ($h) {
                return trim($h, "\xEF\xBB\xBF \t\n\r\0\x0B");
            }, $header);

            $batch = [];
            $distCount = 0;

            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $rowData[] = $sheet->getCell([$col, $row])->getValue();
                }

                // Combine header and row data
                $data = array_combine($header, $rowData);
                if ($data === false) {
                    continue;
                }

                // Handle NULL strings in Excel sheets if they are literally written as "NULL"
                foreach ($data as $key => $val) {
                    if ($val === 'NULL' || $val === 'null' || $val === '') {
                        $data[$key] = null;
                    }
                }

                $batch[] = [
                    'application_number' => $data['application_number'] ?? null,
                    'full_name' => $data['full_name'] ?? null,
                    'aadhar_no' => $data['aadhar_no'] ?? null,
                    'mobile_number' => $data['mobile_number'] ?? null,
                    'secure_id' => Str::random(32),
                    'dist_name' => $dist['dist_name'],
                    'dist_id' => $dist['dist_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($batch) >= $batchSize) {
                    DB::table($targetTable)->insert($batch);
                    $distCount += count($batch);
                    $batch = [];
                }
            }

            if (count($batch) > 0) {
                DB::table($targetTable)->insert($batch);
                $distCount += count($batch);
            }

            $this->command->info("Successfully seeded {$distCount} records for {$dist['dist_name']}.");
            $totalCount += $distCount;
        }

        $duration = round(microtime(true) - $startTime, 2);
        $this->command->info("Successfully seeded a total of {$totalCount} records into the {$targetTable} table in {$duration} seconds.");
    }
}
