<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
        
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        
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

        // Ensure ews_districts table exists
        if (!Schema::hasTable('ews_districts')) {
            Schema::create('ews_districts', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        // Ensure all_ews_data_1 has the columns
        if (!Schema::hasColumn('all_ews_data_1', 'secure_id')) {
            Schema::table('all_ews_data_1', function (Blueprint $table) {
                $table->string('secure_id', 32)->nullable()->unique();
                $table->string('dist_name')->nullable();
                $table->unsignedBigInteger('dist_id')->nullable();
            });
        }
        if (!Schema::hasColumn('all_ews_data_1', 'member_id')) {
            Schema::table('all_ews_data_1', function (Blueprint $table) {
                $table->string('member_id', 50)->nullable()->index();
            });
        }
        if (!Schema::hasColumn('all_ews_data_1', 'ppt_member_id')) {
            Schema::table('all_ews_data_1', function (Blueprint $table) {
                $table->unsignedBigInteger('ppt_member_id')->nullable()->index();
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

            // Only import Sonipat data
            if (strtoupper($rowInsert['dist_name']) !== 'SONIPAT' && $rowInsert['dist_id'] != 22) {
                continue;
            }

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

        $this->command->info("Populating member_id and ppt_member_id columns from ppt_members table...");
        try {
            $affectedMemberIds = DB::update("
                UPDATE all_ews_data_1 
                JOIN ppt_members ON all_ews_data_1.mobile_number = ppt_members.mobileNo
                SET all_ews_data_1.member_id = ppt_members.memberID
            ");
            $affectedPptMemberIds = DB::update("
                UPDATE all_ews_data_1
                JOIN (
                    SELECT mobileNo, MIN(id) as min_id 
                    FROM ppt_members 
                    GROUP BY mobileNo
                ) as sub ON all_ews_data_1.mobile_number = sub.mobileNo
                SET all_ews_data_1.ppt_member_id = sub.min_id
            ");
            $this->command->info("Successfully populated {$affectedMemberIds} member_id records and {$affectedPptMemberIds} ppt_member_id records.");
        } catch (\Exception $e) {
            $this->command->error("Error populating IDs: " . $e->getMessage());
        }
        if (isset($spreadsheet)) {
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }
        gc_collect_cycles();
    }
}