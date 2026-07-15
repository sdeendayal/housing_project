<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class EwsWaitingListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('seeders/data/final_draw_developers.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("Excel file not found at: {$filePath}");
            return;
        }

        $this->command->info("Loading Excel file from {$filePath}...");
        
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('WL final draw sheet');
        if (!$sheet) {
            $this->command->error("Sheet 'WL final draw sheet' not found in Excel file.");
            return;
        }
        
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $this->command->info("Excel sheet loaded. Total rows: {$highestRow}, Total columns: {$highestColumnIndex}.");

        // Read header row (row 3)
        $header = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $header[] = trim($sheet->getCell([$col, 3])->getValue()); // Row 3 has headers!
        }

        // Clean headers to avoid any hidden characters
        $header = array_map(function ($h) {
            return trim($h, "\xEF\xBB\xBF \t\n\r\0\x0B");
        }, $header);

        $batch = [];
        $batchSize = 250; // Batch size to optimize database inserts
        $count = 0;

        $this->command->info("Truncating existing ews_waiting_list_9 table...");
        DB::table('ews_waiting_list_9')->truncate();

        $this->command->info("Seeding data into ews_waiting_list_9 table (starting from row 4)...");

        for ($row = 4; $row <= $highestRow; $row++) {
            $rowData = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $rowData[] = $sheet->getCell([$col, $row])->getValue();
            }

            // Combine header and row data
            $data = array_combine($header, $rowData);

            // Handle NULL strings in Excel sheets if they are literally written as "NULL"
            foreach ($data as $key => $val) {
                if ($val === 'NULL' || $val === 'null' || $val === '') {
                    $data[$key] = null;
                }
            }

            $batch[] = [
                'application_number' => $data['Application no'] ?? null,
                'full_name' => $data['full_name'] ?? null,
                'aadhar_no' => $data['aadhar_no'] ?? null,
                'mobile_number' => $data['mobile_number'] ?? null,
                'flat_no' => $data['flat no.'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('ews_waiting_list_9')->insert($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            DB::table('ews_waiting_list_9')->insert($batch);
            $count += count($batch);
        }

        $this->command->info("Successfully seeded {$count} records into the ews_waiting_list_9 table.");
    }
}
