<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RewariEligibleSeeder extends Seeder
{
    /**
     * Run the database seeds for Rewari eligible beneficiaries (Card 7 / ews_eligible_6).
     */
    public function run(): void
    {
        ini_set('memory_limit', '2G');

        $this->command->info("Deleting existing Rewari records (dist_id = 19) from ews_eligible_6 table...");
        $deleted = DB::table('ews_eligible_6')->where('dist_id', 19)->delete();
        $this->command->info("Deleted {$deleted} existing Rewari records from ews_eligible_6.");

        $rewariBatch = [];
        $rewariCount = 0;
        $batchSize = 250;
        $seenRewariAppNos = [];

        // 1. Existing 2 Rewari records
        $rewariFilePath = database_path('seeders/data/1. 2 eligible Rewari ADC.xlsx');
        if (file_exists($rewariFilePath)) {
            $this->command->info("Loading Rewari Excel file from {$rewariFilePath}...");
            $rewariReader = IOFactory::createReaderForFile($rewariFilePath);
            $rewariReader->setReadDataOnly(true);
            $rewariSpreadsheet = $rewariReader->load($rewariFilePath);
            $rewariSheet = $rewariSpreadsheet->getActiveSheet();
            $rewariRows = $rewariSheet->toArray();
            
            $this->command->info("Seeding Rewari (2 eligible) data into ews_eligible_6 table...");
            
            // Loop starting from index 2 (row 3 of Excel)
            foreach (array_slice($rewariRows, 2) as $r) {
                $appNo = $r[1] !== null ? trim((string)$r[1]) : '';
                if (empty($appNo)) {
                    continue;
                }
                if (isset($seenRewariAppNos[$appNo])) {
                    continue;
                }
                $seenRewariAppNos[$appNo] = true;
                
                $rewariBatch[] = [
                    'application_number' => $appNo,
                    'full_name' => $r[2] !== null ? trim((string)$r[2]) : '',
                    'aadhar_no' => null,
                    'mobile_number' => $r[3] !== null ? trim((string)$r[3]) : '',
                    'status' => $r[5] !== null ? trim((string)$r[5]) : 'Eligible',
                    'priority' => null,
                    'category' => null,
                    'secure_id' => md5('ews_eligible_6_' . $appNo . '_' . uniqid(rand(), true)),
                    'dist_name' => 'REWARI',
                    'dist_id' => 19,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                if (count($rewariBatch) >= $batchSize) {
                    DB::table('ews_eligible_6')->insert($rewariBatch);
                    $rewariCount += count($rewariBatch);
                    $rewariBatch = [];
                }
            }
            $rewariSpreadsheet->disconnectWorksheets();
            unset($rewariSpreadsheet);
        } else {
            $this->command->warn("Rewari Excel file not found at: {$rewariFilePath}");
        }

        // 2. Additional Rewari 224 eligible applicants
        $rewari224FilePath = database_path('seeders/data/Rewari-224 eligible applicants (1).xlsx');
        if (!file_exists($rewari224FilePath)) {
            $userProfile = getenv('USERPROFILE') ?: 'C:/Users/hp';
            $rewari224FilePath = $userProfile . '/Downloads/Rewari-224 eligible applicants (1).xlsx';
        }

        if (file_exists($rewari224FilePath)) {
            $this->command->info("Loading Rewari 224 Excel file from {$rewari224FilePath}...");
            $rewariReader2 = IOFactory::createReaderForFile($rewari224FilePath);
            $rewariReader2->setReadDataOnly(true);
            $rewariSpreadsheet2 = $rewariReader2->load($rewari224FilePath);
            $rewariSheet2 = $rewariSpreadsheet2->getSheet(0);
            $rewariRows2 = $rewariSheet2->toArray();
            
            $this->command->info("Seeding Rewari 224 eligible applicants into ews_eligible_6 table...");
            
            // Loop starting from index 1 (row 2 of Excel)
            foreach (array_slice($rewariRows2, 1) as $r) {
                $appNo = $r[1] !== null ? trim((string)$r[1]) : '';
                if (empty($appNo)) {
                    continue;
                }
                if (isset($seenRewariAppNos[$appNo])) {
                    continue;
                }

                $status = $r[5] !== null ? trim((string)$r[5]) : 'Eligible';
                if (!empty($status) && (stripos($status, 'not') !== false || stripos($status, 'uneligible') !== false)) {
                    continue;
                }

                $seenRewariAppNos[$appNo] = true;
                
                $rewariBatch[] = [
                    'application_number' => $appNo,
                    'full_name' => $r[2] !== null ? trim((string)$r[2]) : '',
                    'aadhar_no' => null,
                    'mobile_number' => $r[3] !== null ? trim((string)$r[3]) : '',
                    'status' => 'Eligible',
                    'priority' => null,
                    'category' => null,
                    'secure_id' => md5('ews_eligible_6_' . $appNo . '_' . uniqid(rand(), true)),
                    'dist_name' => 'REWARI',
                    'dist_id' => 19,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                if (count($rewariBatch) >= $batchSize) {
                    DB::table('ews_eligible_6')->insert($rewariBatch);
                    $rewariCount += count($rewariBatch);
                    $rewariBatch = [];
                }
            }
            $rewariSpreadsheet2->disconnectWorksheets();
            unset($rewariSpreadsheet2);
        } else {
            $this->command->warn("Rewari 224 Excel file not found at: {$rewari224FilePath}");
        }

        if (count($rewariBatch) > 0) {
            DB::table('ews_eligible_6')->insert($rewariBatch);
            $rewariCount += count($rewariBatch);
        }

        $this->command->info("Successfully seeded {$rewariCount} total Rewari records into ews_eligible_6 table.");
    }
}
