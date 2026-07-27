<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class EwsBookingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '-1');
        $filePath = database_path('seeders/data/master_draw_sonipat.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("Excel file not found at: {$filePath}");
            return;
        }

        $this->command->info("Loading Excel file from {$filePath}...");
        
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getSheetByName('814 bookings');
        if (!$sheet) {
            $this->command->error("Sheet '814 bookings' not found in Excel file.");
            return;
        }
        
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $this->command->info("Excel sheet '814 bookings' loaded. Total rows: {$highestRow}, Total columns: {$highestColumnIndex}.");

        // Read header row (row 1)
        $header = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $header[] = trim($sheet->getCell([$col, 1])->getValue()); // Row 1 has headers!
        }

        // Clean headers to avoid any hidden characters
        $header = array_map(function ($h) {
            return trim($h, "\xEF\xBB\xBF \t\n\r\0\x0B");
        }, $header);

        $batch = [];
        $batchSize = 250; // Batch size to optimize database inserts
        $count = 0;

        $this->command->info("Truncating existing ews_bookings_7 table...");
        DB::table('ews_bookings_7')->truncate();

        $this->command->info("Seeding data into ews_bookings_7 table...");

        // Ensure ews_districts table exists
        if (!Schema::hasTable('ews_districts')) {
            Schema::create('ews_districts', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        // Ensure ews_bookings_7 has the columns
        if (!Schema::hasColumn('ews_bookings_7', 'secure_id')) {
            Schema::table('ews_bookings_7', function (Blueprint $table) {
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
            $rowInsert['secure_id'] = $data['secure_id'] ?? $data['secure_id'] ?? \Illuminate\Support\Str::random(32);
            $rowInsert['dist_name'] = $data['dist_name'] ?? $data['DistrictName'] ?? 'SONIPAT';
            $rowInsert['dist_id'] = $data['dist_id'] ?? $data['DistrictId'] ?? $districtId;
            $rowInsert['created_at'] = now();
            $rowInsert['updated_at'] = now();

            $batch[] = $rowInsert;

            if (count($batch) >= $batchSize) {
                DB::table('ews_bookings_7')->insert($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            DB::table('ews_bookings_7')->insert($batch);
            $count += count($batch);
        }

        $this->command->info("Successfully seeded {$count} records into the ews_bookings_7 table.");
        if (isset($spreadsheet)) {
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }
        gc_collect_cycles();
    }
}