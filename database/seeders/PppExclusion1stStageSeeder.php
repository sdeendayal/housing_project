<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Str;

class PppExclusion1stStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set higher memory limit to avoid exhaustion with large Excel files
        ini_set('memory_limit', '2G');

        $filePath = database_path('seeders/data/PPP ecclusion data ist stage.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("Excel file not found at: {$filePath}");
            return;
        }

        $this->command->info("Loading Excel file from {$filePath}...");
        
        $spreadsheet = IOFactory::load($filePath);

        $sheetsToImport = [
            'Fridabad' => ['dist_name' => 'FARIDABAD', 'dist_id' => 4],
            'Panipat' => ['dist_name' => 'PANIPAT', 'dist_id' => 18],
            'Gurugram' => ['dist_name' => 'GURUGRAM', 'dist_id' => 6],
            'Rewari' => ['dist_name' => 'REWARI', 'dist_id' => 19],
            'Rohtak' => ['dist_name' => 'ROHTAK', 'dist_id' => 20],
            'Sonipat' => ['dist_name' => 'SONIPAT', 'dist_id' => 22]
        ];

        // Clear existing 1st stage records
        $this->command->info("Clearing existing '1st stage' records from ews_reject_ppp_exclusion_2 table...");
        DB::table('ews_reject_ppp_exclusion_2')->where('stage', '1st stage')->delete();

        $batchSize = 250;
        $totalCount = 0;
        $startTime = microtime(true);

        foreach ($sheetsToImport as $sheetName => $distData) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (!$sheet) {
                $this->command->warn("Sheet '{$sheetName}' not found in Excel file. Skipping...");
                continue;
            }

            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            $this->command->info("Processing sheet '{$sheetName}' ({$distData['dist_name']}). Total rows: {$highestRow}...");

            // Read header row (row 1)
            $header = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $header[] = trim($sheet->getCell([$col, 1])->getValue()); // Row 1 has headers
            }

            // Clean headers
            $header = array_map(function ($h) {
                return trim($h, "\xEF\xBB\xBF \t\n\r\0\x0B");
            }, $header);

            $batch = [];
            $sheetCount = 0;

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
                    'dist_name' => $distData['dist_name'],
                    'dist_id' => $distData['dist_id'],
                    'stage' => '1st stage',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($batch) >= $batchSize) {
                    DB::table('ews_reject_ppp_exclusion_2')->insert($batch);
                    $sheetCount += count($batch);
                    $batch = [];
                }
            }

            if (count($batch) > 0) {
                DB::table('ews_reject_ppp_exclusion_2')->insert($batch);
                $sheetCount += count($batch);
            }

            $this->command->info("Seeded {$sheetCount} records from sheet '{$sheetName}'.");
            $totalCount += $sheetCount;
        }

        $duration = round(microtime(true) - $startTime, 2);
        $this->command->info("Successfully seeded a total of {$totalCount} '1st stage' records in {$duration} seconds.");
    }
}
