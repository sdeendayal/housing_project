<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class EwsAllottedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '-1');
        $filePath = database_path('seeders/data/final_draw_developers.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("Excel file not found at: {$filePath}");
            return;
        }

        $this->command->info("Loading Excel file from {$filePath}...");
        
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('509 fnal draw sheet');
        if (!$sheet) {
            $this->command->error("Sheet '509 fnal draw sheet' not found in Excel file.");
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

        $this->command->info("Truncating existing ews_allotted_8 table...");
        DB::table('ews_allotted_8')->truncate();

        $this->command->info("Seeding data into ews_allotted_8 table (starting from row 4)...");

        // Ensure ews_districts table exists
        if (!Schema::hasTable('ews_districts')) {
            Schema::create('ews_districts', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        // Ensure ews_allotted_8 has the columns
        if (!Schema::hasColumn('ews_allotted_8', 'secure_id')) {
            Schema::table('ews_allotted_8', function (Blueprint $table) {
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
                'secure_id' => $data['secure_id'] ?? \Illuminate\Support\Str::random(32),
                'dist_name' => $data['dist_name'] ?? $data['DistrictName'] ?? 'SONIPAT',
                'dist_id' => $data['dist_id'] ?? $data['DistrictId'] ?? $districtId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('ews_allotted_8')->insert($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            DB::table('ews_allotted_8')->insert($batch);
            $count += count($batch);
        }

        $this->command->info("Successfully seeded {$count} records into the ews_allotted_8 table.");
    }
}
