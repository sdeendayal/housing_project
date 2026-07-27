<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Str;

class GurugramHouseOwnershipRejectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '2G');
        $filePath = database_path('seeders/data/GURGAON_Completed_24-01-2026 (3).xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("Excel file not found at: {$filePath}");
            return;
        }

        $this->command->info("Loading Excel file from {$filePath} (House_ownership sheet only)...");
        
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly(['House_ownership']);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getSheetByName('House_ownership');
        
        if (!$sheet) {
            $this->command->error("Sheet 'House_ownership' not found in Excel file.");
            return;
        }
        
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $this->command->info("Excel sheet loaded. Total rows: {$highestRow}, Total columns: {$highestColumnIndex}.");

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
        $batchSize = 250; 
        $count = 0;
        $startTime = microtime(true);

        $this->command->info("Clearing existing Gurugram house ownership records (dist_id = 6) from ews_house_ownership_reject_4 table...");
        DB::table('ews_house_ownership_reject_4')->where('dist_id', 6)->delete();

        $this->command->info("Seeding Gurugram data into ews_house_ownership_reject_4 table (starting from row 2)...");

        // Ensure columns exist just in case
        if (!Schema::hasColumn('ews_house_ownership_reject_4', 'secure_id')) {
            Schema::table('ews_house_ownership_reject_4', function (Blueprint $table) {
                $table->string('secure_id', 32)->nullable()->unique();
                $table->string('dist_name')->nullable();
                $table->unsignedBigInteger('dist_id')->nullable();
            });
        }

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
                'dist_name' => 'GURUGRAM',
                'dist_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('ews_house_ownership_reject_4')->insert($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            DB::table('ews_house_ownership_reject_4')->insert($batch);
            $count += count($batch);
        }

        $duration = round(microtime(true) - $startTime, 2);
        $this->command->info("Successfully seeded {$count} Gurugram records into the ews_house_ownership_reject_4 table in {$duration} seconds.");
        if (isset($spreadsheet)) {
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }
        gc_collect_cycles();
    }
}