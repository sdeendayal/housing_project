<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Str;

class GurugramEwsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('seeders/data/GURGAON_Completed_24-01-2026.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("Excel file not found at: {$filePath}");
            return;
        }

        $this->command->info("Loading Excel file from {$filePath}...");
        
        $spreadsheet = IOFactory::load($filePath);
        
        // Select the sheet by name 'GURGAON_Completed_24-01-2026'
        $sheet = $spreadsheet->getSheetByName('GURGAON_Completed_24-01-2026');
        if (!$sheet) {
            $sheet = $spreadsheet->getActiveSheet();
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
        $batchSize = 250; // Batch size to optimize database inserts
        $count = 0;

        $this->command->info("Clearing existing Gurugram records (dist_id = 6) from all_ews_data_1 table...");
        DB::table('all_ews_data_1')->where('dist_id', 6)->delete();

        $this->command->info("Seeding data into all_ews_data_1 table for GURUGRAM...");

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
                // Skip 'id' from Excel to allow DB auto-increment without constraint issues
                if ($h === 'id') {
                    continue;
                }
                
                $val = $data[$h] ?? null;
                // Handle NULL strings in Excel sheets if they are literally written as "NULL"
                if ($val === 'NULL' || $val === 'null' || $val === '') {
                    $val = null;
                }
                $rowInsert[$h] = $val;
            }
            
            $rowInsert['secure_id'] = Str::random(32);
            $rowInsert['dist_name'] = 'GURUGRAM';
            $rowInsert['dist_id'] = 6;
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

        $this->command->info("Successfully seeded {$count} Gurugram records into the all_ews_data_1 table.");

        $this->command->info("Populating member_id and ppt_member_id columns from ppt_members table for Gurugram...");
        try {
            $affectedMemberIds = DB::update("
                UPDATE all_ews_data_1 
                JOIN ppt_members ON all_ews_data_1.mobile_number = ppt_members.mobileNo
                SET all_ews_data_1.member_id = ppt_members.memberID
                WHERE all_ews_data_1.dist_id = 6
            ");
            $affectedPptMemberIds = DB::update("
                UPDATE all_ews_data_1
                JOIN (
                    SELECT mobileNo, MIN(id) as min_id 
                    FROM ppt_members 
                    GROUP BY mobileNo
                ) as sub ON all_ews_data_1.mobile_number = sub.mobileNo
                SET all_ews_data_1.ppt_member_id = sub.min_id
                WHERE all_ews_data_1.dist_id = 6
            ");
            $this->command->info("Successfully populated {$affectedMemberIds} member_id records and {$affectedPptMemberIds} ppt_member_id records for Gurugram.");
        } catch (\Exception $e) {
            $this->command->error("Error populating IDs: " . $e->getMessage());
        }
    }
}
