<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class EwsEligibleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '-1');
        $filePath = database_path('seeders/data/eligible_draw_list.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("Excel file not found at: {$filePath}");
            return;
        }

        $this->command->info("Loading Excel file from {$filePath}...");
        
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getSheetByName('Sheet1');
        if (!$sheet) {
            $this->command->error("Sheet 'Sheet1' not found in Excel file.");
            return;
        }
        
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $this->command->info("Excel sheet loaded. Total rows: {$highestRow}, Total columns: {$highestColumnIndex}.");

        // Read header row (row 2)
        $header = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $header[] = trim($sheet->getCell([$col, 2])->getValue()); // Row 2 has headers!
        }

        // Clean headers to avoid any hidden characters
        $header = array_map(function ($h) {
            return trim($h, "\xEF\xBB\xBF \t\n\r\0\x0B");
        }, $header);

        $batch = [];
        $batchSize = 250; // Batch size to optimize database inserts
        $count = 0;

        $this->command->info("Truncating existing ews_eligible_6 table...");
        DB::table('ews_eligible_6')->truncate();

        $this->command->info("Seeding data into ews_eligible_6 table (starting from row 3)...");

        // Ensure ews_districts table exists
        if (!Schema::hasTable('ews_districts')) {
            Schema::create('ews_districts', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        // Ensure ews_eligible_6 has the columns
        if (!Schema::hasColumn('ews_eligible_6', 'secure_id')) {
            Schema::table('ews_eligible_6', function (Blueprint $table) {
                $table->string('secure_id', 32)->nullable()->unique();
                $table->string('dist_name')->nullable();
                $table->unsignedBigInteger('dist_id')->nullable();
            });
        }

        // Fetch Sonipat ID from EWS master districts table
        $masterDist = DB::table('ews_districts')->where('name', 'SONIPAT')->first();
        $districtId = $masterDist ? $masterDist->id : 22;
        $districtName = $masterDist ? $masterDist->name : 'SONIPAT';

        // Ensure Sonipat is seeded in ews_districts and fetch the ID
        $district = DB::table('ews_districts')->where('id', $districtId)->first();
        if (!$district) {
            DB::table('ews_districts')->insert([
                'id' => $districtId,
                'name' => $districtName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($row = 3; $row <= $highestRow; $row++) {
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

            $distName = $data['dist_name'] ?? $data['DistrictName'] ?? 'SONIPAT';
            $distId = $data['dist_id'] ?? $data['DistrictId'] ?? $districtId;

            // Only import Sonipat data
            if (strtoupper($distName) !== 'SONIPAT' && $distId != 22) {
                continue;
            }

            $batch[] = [
                'application_number' => $data['Application no'] ?? null,
                'full_name' => $data['full_name'] ?? null,
                'aadhar_no' => $data['aadhar_no'] ?? null,
                'mobile_number' => $data['mobile_number'] ?? null,
                'status' => $data['Status'] ?? null,
                'priority' => $data['Priority'] ?? null,
                'category' => $data['category'] ?? null,
                'secure_id' => $data['secure_id'] ?? \Illuminate\Support\Str::random(32),
                'dist_name' => $distName,
                'dist_id' => $distId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('ews_eligible_6')->insert($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            DB::table('ews_eligible_6')->insert($batch);
            $count += count($batch);
        }

        $this->command->info("Successfully seeded {$count} Sonipat records into the ews_eligible_6 table.");
        if (isset($spreadsheet)) {
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }

        // Seed Faridabad Data
        $fbdFilePath = database_path('seeders/data/Eligible_bene_Faridabad (1)9ADC).xlsx');
        if (file_exists($fbdFilePath)) {
            $this->command->info("Loading Faridabad Excel file from {$fbdFilePath}...");
            $fbdReader = IOFactory::createReaderForFile($fbdFilePath);
            $fbdReader->setReadDataOnly(true);
            $fbdSpreadsheet = $fbdReader->load($fbdFilePath);
            $fbdSheet = $fbdSpreadsheet->getActiveSheet();
            $fbdRows = $fbdSheet->toArray();
            
            $fbdBatch = [];
            $fbdCount = 0;
            $this->command->info("Seeding Faridabad data into ews_eligible_6 table...");
            
            foreach (array_slice($fbdRows, 1) as $r) {
                $appNo = $r[2] !== null ? trim($r[2]) : '';
                if (empty($appNo)) {
                    continue;
                }
                
                $fbdBatch[] = [
                    'application_number' => $appNo,
                    'full_name' => $r[3] !== null ? trim($r[3]) : '',
                    'aadhar_no' => null,
                    'mobile_number' => $r[4] !== null ? trim($r[4]) : '',
                    'status' => $r[5] !== null ? trim($r[5]) : 'Eligible',
                    'priority' => null,
                    'category' => null,
                    'secure_id' => md5('ews_eligible_6_' . $appNo . '_' . uniqid(rand(), true)),
                    'dist_name' => 'FARIDABAD',
                    'dist_id' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                if (count($fbdBatch) >= $batchSize) {
                    DB::table('ews_eligible_6')->insert($fbdBatch);
                    $fbdCount += count($fbdBatch);
                    $fbdBatch = [];
                }
            }
            
            if (count($fbdBatch) > 0) {
                DB::table('ews_eligible_6')->insert($fbdBatch);
                $fbdCount += count($fbdBatch);
            }
            $this->command->info("Successfully seeded {$fbdCount} Faridabad records.");
            $fbdSpreadsheet->disconnectWorksheets();
            unset($fbdSpreadsheet);
        } else {
            $this->command->error("Faridabad Excel file not found at: {$fbdFilePath}");
        }

        // Seed Gurugram Data
        $ggnFilePath = database_path('seeders/data/Draw Result Gurugram 2709   (ok) (ADC).xlsx');
        if (file_exists($ggnFilePath)) {
            $this->command->info("Loading Gurugram Excel file from {$ggnFilePath}...");
            $ggnReader = IOFactory::createReaderForFile($ggnFilePath);
            $ggnReader->setReadDataOnly(true);
            $ggnSpreadsheet = $ggnReader->load($ggnFilePath);
            $ggnSheet = $ggnSpreadsheet->getSheetByName('Sheet1');
            $ggnRows = $ggnSheet->toArray();
            
            $ggnBatch = [];
            $ggnCount = 0;
            $this->command->info("Seeding Gurugram data into ews_eligible_6 table...");
            
            // Loop starting from index 2 (row 3 of Excel)
            foreach (array_slice($ggnRows, 2) as $r) {
                $appNo = $r[4] !== null ? trim($r[4]) : '';
                if (empty($appNo)) {
                    continue;
                }
                
                $ggnBatch[] = [
                    'application_number' => $appNo,
                    'full_name' => $r[2] !== null ? trim($r[2]) : '',
                    'aadhar_no' => null,
                    'mobile_number' => null,
                    'status' => 'Eligible',
                    'priority' => null,
                    'category' => null,
                    'secure_id' => md5('ews_eligible_6_' . $appNo . '_' . uniqid(rand(), true)),
                    'dist_name' => 'GURUGRAM',
                    'dist_id' => 6,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                if (count($ggnBatch) >= $batchSize) {
                    DB::table('ews_eligible_6')->insert($ggnBatch);
                    $ggnCount += count($ggnBatch);
                    $ggnBatch = [];
                }
            }
            
            if (count($ggnBatch) > 0) {
                DB::table('ews_eligible_6')->insert($ggnBatch);
                $ggnCount += count($ggnBatch);
            }
            $this->command->info("Successfully seeded {$ggnCount} Gurugram records.");
            $ggnSpreadsheet->disconnectWorksheets();
            unset($ggnSpreadsheet);
        } else {
            $this->command->error("Gurugram Excel file not found at: {$ggnFilePath}");
        }

        gc_collect_cycles();
    }
}