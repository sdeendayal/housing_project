<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RohtakEligibleSeeder extends Seeder
{
    /**
     * Run the database seeds for Rohtak eligible beneficiaries (Card 7 / ews_eligible_6).
     */
    public function run(): void
    {
        ini_set('memory_limit', '2G');

        $rohtakFilePath = 'E:/AAAAAAA/2. Rohtak eligible and uneligible (ADC) (Puchna Pdega).xlsx';
        if (!file_exists($rohtakFilePath)) {
            $rohtakFilePath = database_path('seeders/data/2. Rohtak eligible and uneligible (ADC) (Puchna Pdega).xlsx');
        }
        if (!file_exists($rohtakFilePath)) {
            $rohtakFilePath = database_path('seeders/data/2. Rohtak eligible and uneligible (ADC).xlsx');
        }

        if (!file_exists($rohtakFilePath)) {
            $this->command->error("Rohtak Excel file not found at: {$rohtakFilePath}");
            return;
        }

        $this->command->info("Deleting existing Rohtak records (dist_id = 20) from ews_eligible_6 table...");
        $deleted = DB::table('ews_eligible_6')->where('dist_id', 20)->delete();
        $this->command->info("Deleted {$deleted} existing Rohtak records from ews_eligible_6.");

        $this->command->info("Loading Rohtak Excel file from {$rohtakFilePath}...");
        $rohtakReader = IOFactory::createReaderForFile($rohtakFilePath);
        $rohtakReader->setReadDataOnly(true);
        $rohtakSpreadsheet = $rohtakReader->load($rohtakFilePath);

        $rohtakBatch = [];
        $rohtakCount = 0;
        $batchSize = 250;
        $seenRohtakAppNos = [];

        // Only process Sheet with Eligible applicants (e.g. 'BPL flats eligible ' / Sheet index 1)
        // Note: Sheet 'Reason not eligible' (Index 0) is excluded because ews_eligible_6 is only for eligible applicants.
        $rohtakSheet = null;
        foreach ($rohtakSpreadsheet->getAllSheets() as $sh) {
            $title = strtolower(trim($sh->getTitle()));
            if (str_contains($title, 'eligible') && !str_contains($title, 'not') && !str_contains($title, 'uneligible')) {
                $rohtakSheet = $sh;
                break;
            }
        }
        if (!$rohtakSheet && $rohtakSpreadsheet->getSheetCount() > 1) {
            $rohtakSheet = $rohtakSpreadsheet->getSheet(1);
        } elseif (!$rohtakSheet) {
            $rohtakSheet = $rohtakSpreadsheet->getActiveSheet();
        }

        if ($rohtakSheet) {
            $this->command->info("Processing sheet: '{$rohtakSheet->getTitle()}'...");
            $rows = $rohtakSheet->toArray();

            // Loop starting from index 2 (row 3 of Excel)
            foreach (array_slice($rows, 2) as $r) {
                $appNo = $r[1] !== null ? trim((string)$r[1]) : '';
                if (empty($appNo)) {
                    continue;
                }
                if (isset($seenRohtakAppNos[$appNo])) {
                    continue;
                }

                $status = $r[5] !== null ? trim((string)$r[5]) : 'Eligible';
                // Strictly exclude any record marked as Not Eligible / Uneligible
                if (!empty($status) && (stripos($status, 'not') !== false || stripos($status, 'uneligible') !== false)) {
                    continue;
                }

                $seenRohtakAppNos[$appNo] = true;

                $rohtakBatch[] = [
                    'application_number' => $appNo,
                    'full_name' => $r[2] !== null ? trim((string)$r[2]) : '',
                    'aadhar_no' => null,
                    'mobile_number' => $r[3] !== null ? trim((string)$r[3]) : '',
                    'status' => 'Eligible',
                    'priority' => null,
                    'category' => null,
                    'secure_id' => md5('ews_eligible_6_' . $appNo . '_' . uniqid(rand(), true)),
                    'dist_name' => 'ROHTAK',
                    'dist_id' => 20,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($rohtakBatch) >= $batchSize) {
                    DB::table('ews_eligible_6')->insert($rohtakBatch);
                    $rohtakCount += count($rohtakBatch);
                    $rohtakBatch = [];
                }
            }
        }

        if (count($rohtakBatch) > 0) {
            DB::table('ews_eligible_6')->insert($rohtakBatch);
            $rohtakCount += count($rohtakBatch);
        }

        $this->command->info("Successfully seeded {$rohtakCount} unique eligible Rohtak records into ews_eligible_6 table.");
        $rohtakSpreadsheet->disconnectWorksheets();
        unset($rohtakSpreadsheet);
    }
}
