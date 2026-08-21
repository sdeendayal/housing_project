<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdcNotVerifiedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '2G');

        $this->command->info("Truncating existing adc_not_verified table...");
        DB::table('adc_not_verified')->truncate();

        // 1. Seed Gurugram Data
        $ggnFilePath = database_path('seeders/data/ggn not eli.xlsx');
        if (file_exists($ggnFilePath)) {
            $this->command->info("Loading Gurugram Excel file from {$ggnFilePath}...");
            $ggnReader = IOFactory::createReaderForFile($ggnFilePath);
            $ggnReader->setReadDataOnly(true);
            $ggnSpreadsheet = $ggnReader->load($ggnFilePath);
            $ggnSheet = $ggnSpreadsheet->getSheetByName('Not_Eligible') ?? $ggnSpreadsheet->getActiveSheet();
            $ggnRows = $ggnSheet->toArray();
            
            $ggnBatch = [];
            $ggnCount = 0;
            $batchSize = 250;

            // Loop starting from index 3 (row 4 of Excel)
            foreach (array_slice($ggnRows, 3) as $r) {
                $appNo = $r[2] !== null ? trim($r[2]) : '';
                if (empty($appNo)) {
                    continue;
                }
                
                $ggnBatch[] = [
                    'application_number' => $appNo,
                    'full_name' => $r[3] !== null ? trim($r[3]) : '',
                    'aadhar_no' => null,
                    'mobile_number' => $r[4] !== null ? trim($r[4]) : null,
                    'status' => $r[5] !== null ? trim($r[5]) : 'Not Eligible',
                    'priority' => null,
                    'category' => null,
                    'secure_id' => md5('adc_not_verified_' . $appNo . '_' . uniqid(rand(), true)),
                    'dist_name' => 'GURUGRAM',
                    'dist_id' => 6,
                    'remarks' => $r[6] !== null ? trim($r[6]) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                if (count($ggnBatch) >= $batchSize) {
                    DB::table('adc_not_verified')->insert($ggnBatch);
                    $ggnCount += count($ggnBatch);
                    $ggnBatch = [];
                }
            }
            
            if (count($ggnBatch) > 0) {
                DB::table('adc_not_verified')->insert($ggnBatch);
                $ggnCount += count($ggnBatch);
            }
            $this->command->info("Successfully seeded {$ggnCount} Gurugram not-verified records.");
            $ggnSpreadsheet->disconnectWorksheets();
            unset($ggnSpreadsheet);
        } else {
            $this->command->error("Gurugram Excel file not found at: {$ggnFilePath}");
        }

        // 2. Seed Faridabad Data
        $fbdFilePath = database_path('seeders/data/Not_eligible_fbd(ADC).xlsx');
        if (file_exists($fbdFilePath)) {
            $this->command->info("Loading Faridabad Excel file from {$fbdFilePath}...");
            $fbdReader = IOFactory::createReaderForFile($fbdFilePath);
            $fbdReader->setReadDataOnly(true);
            $fbdSpreadsheet = $fbdReader->load($fbdFilePath);
            $fbdSheet = $fbdSpreadsheet->getActiveSheet();
            $fbdRows = $fbdSheet->toArray();
            
            $fbdBatch = [];
            $fbdCount = 0;
            $batchSize = 250;

            // Loop starting from index 1 (row 2 of Excel)
            foreach (array_slice($fbdRows, 1) as $r) {
                $appNo = $r[2] !== null ? trim($r[2]) : '';
                if (empty($appNo)) {
                    continue;
                }
                
                $fbdBatch[] = [
                    'application_number' => $appNo,
                    'full_name' => $r[3] !== null ? trim($r[3]) : '',
                    'aadhar_no' => null,
                    'mobile_number' => $r[4] !== null ? trim($r[4]) : null,
                    'status' => $r[5] !== null ? trim($r[5]) : 'Not Eligible',
                    'priority' => null,
                    'category' => null,
                    'secure_id' => md5('adc_not_verified_' . $appNo . '_' . uniqid(rand(), true)),
                    'dist_name' => 'FARIDABAD',
                    'dist_id' => 4,
                    'remarks' => $r[6] !== null ? trim($r[6]) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                if (count($fbdBatch) >= $batchSize) {
                    DB::table('adc_not_verified')->insert($fbdBatch);
                    $fbdCount += count($fbdBatch);
                    $fbdBatch = [];
                }
            }
            
            if (count($fbdBatch) > 0) {
                DB::table('adc_not_verified')->insert($fbdBatch);
                $fbdCount += count($fbdBatch);
            }
            $this->command->info("Successfully seeded {$fbdCount} Faridabad not-verified records.");
            $fbdSpreadsheet->disconnectWorksheets();
            unset($fbdSpreadsheet);
        } else {
            $this->command->error("Faridabad Excel file not found at: {$fbdFilePath}");
        }

        gc_collect_cycles();
    }
}
