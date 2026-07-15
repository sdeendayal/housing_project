<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class AllEwsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('seeders/data/all_ews_data.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("Excel file not found at: {$filePath}");
            return;
        }

        $this->command->info("Loading Excel file from {$filePath}...");
        
        $spreadsheet = IOFactory::load($filePath);
        
        // Select the 'org data' sheet
        $sheet = $spreadsheet->getSheetByName('org data');
        if (!$sheet) {
            $this->command->error("Sheet 'org data' not found in Excel file.");
            return;
        }
        
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $this->command->info("Excel sheet 'org data' loaded. Total rows: {$highestRow}, Total columns: {$highestColumnIndex}.");

        // Read header row (row 1)
        $header = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $header[] = trim($sheet->getCell([$col, 1])->getValue());
        }

        // Clean headers to avoid any hidden characters
        $header = array_map(function ($h) {
            return trim($h, "\xEF\xBB\xBF \t\n\r\0\x0B");
        }, $header);

        $batch = [];
        $batchSize = 250; // Batch size to optimize database inserts
        $count = 0;

        $this->command->info("Truncating existing all_ews_data_1 table...");
        DB::table('all_ews_data_1')->truncate();

        $this->command->info("Seeding data into all_ews_data_1 table from 'org data'...");

        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $rowData[] = $sheet->getCell([$col, $row])->getValue();
            }

            // Combine header and row data
            $data = array_combine($header, $rowData);

            $rowInsert = [];
            foreach ($header as $h) {
                if (empty($h)) {
                    continue;
                }
                $val = $data[$h] ?? null;
                // Handle NULL strings in Excel sheets if they are literally written as "NULL"
                if ($val === 'NULL' || $val === 'null' || $val === '') {
                    $val = null;
                }
                $rowInsert[$h] = $val;
            }
            $rowInsert['created_at'] = now();
            $rowInsert['updated_at'] = now();

            $batch[] = $rowInsert;

            if (count($batch) >= $batchSize) {
                DB::table('all_ews_data_1')->insert($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            DB::table('all_ews_data_1')->insert($batch);
            $count += count($batch);
        }

        $this->command->info("Successfully seeded {$count} records into the all_ews_data_1 table from 'org data' sheet.");
    }
}
