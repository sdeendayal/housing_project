<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PanipatEligibleSeeder extends Seeder
{
    /**
     * Run the database seeds for Panipat eligible beneficiaries (Card 7 / ews_eligible_6).
     */
    public function run(): void
    {
        ini_set('memory_limit', '2G');

        $panipatFilePath = 'E:/AAAAAAA/3. eligible panipat final ADC.xlsx';
        if (!file_exists($panipatFilePath)) {
            $panipatFilePath = database_path('seeders/data/3. eligible panipat final ADC.xlsx');
        }

        if (!file_exists($panipatFilePath)) {
            $this->command->error("Panipat Excel file not found at: {$panipatFilePath}");
            return;
        }

        $this->command->info("Deleting existing Panipat records (dist_id = 18) from ews_eligible_6 table...");
        $deleted = DB::table('ews_eligible_6')->where('dist_id', 18)->delete();
        $this->command->info("Deleted {$deleted} existing Panipat records from ews_eligible_6.");

        $this->command->info("Loading Panipat Excel file from {$panipatFilePath}...");
        $panipatReader = IOFactory::createReaderForFile($panipatFilePath);
        $panipatReader->setReadDataOnly(true);
        $panipatSpreadsheet = $panipatReader->load($panipatFilePath);
        $panipatSheet = $panipatSpreadsheet->getActiveSheet();
        $panipatRows = $panipatSheet->toArray();

        $panipatBatch = [];
        $panipatCount = 0;
        $batchSize = 250;
        $seenPanipatAppNos = [];

        $this->command->info("Seeding Panipat eligible data into ews_eligible_6 table...");

        // Loop starting from index 1 (row 2 of Excel)
        foreach (array_slice($panipatRows, 1) as $r) {
            $appNo = $r[3] !== null ? trim((string)$r[3]) : '';
            if (empty($appNo)) {
                continue;
            }
            if (isset($seenPanipatAppNos[$appNo])) {
                continue;
            }

            // Strictly seed only Eligible applicants (Eligibility col 14 == 'YES')
            // Exclude 'NO', 'DEATH', and any uneligible records
            $excelStatus = $r[14] !== null ? strtoupper(trim((string)$r[14])) : '';
            if ($excelStatus !== 'YES') {
                continue;
            }

            $seenPanipatAppNos[$appNo] = true;

            $panipatBatch[] = [
                'application_number' => $appNo,
                'full_name' => $r[4] !== null ? trim((string)$r[4]) : '',
                'aadhar_no' => null,
                'mobile_number' => $r[6] !== null ? trim((string)$r[6]) : '',
                'status' => 'Eligible',
                'priority' => null,
                'category' => null,
                'secure_id' => md5('ews_eligible_6_' . $appNo . '_' . uniqid(rand(), true)),
                'dist_name' => 'PANIPAT',
                'dist_id' => 18,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($panipatBatch) >= $batchSize) {
                DB::table('ews_eligible_6')->insert($panipatBatch);
                $panipatCount += count($panipatBatch);
                $panipatBatch = [];
            }
        }

        if (count($panipatBatch) > 0) {
            DB::table('ews_eligible_6')->insert($panipatBatch);
            $panipatCount += count($panipatBatch);
        }

        $this->command->info("Successfully seeded {$panipatCount} unique eligible Panipat records into ews_eligible_6 table.");
        $panipatSpreadsheet->disconnectWorksheets();
        unset($panipatSpreadsheet);
    }
}
