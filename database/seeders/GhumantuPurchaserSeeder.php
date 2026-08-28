<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GhumantuPurchaserSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        $csvFile = database_path('ghumantu_702_data.csv');

        if (!file_exists($csvFile)) {
            $this->command?->error('Ghumantu CSV file not found at: ' . $csvFile);
            return;
        }

        $this->command?->info('Resetting is_ghumantu to 0 in property_private_purchasers table...');
        DB::table('property_private_purchasers')->update(['is_ghumantu' => 0]);

        $this->command?->info('Reading MemberIDs from ' . $csvFile . '...');
        $file = fopen($csvFile, 'r');
        
        // Skip CSV header
        fgetcsv($file);

        $memberIds = [];
        while (($row = fgetcsv($file, 0, ',')) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }
            // MemberID is in the 3rd column (index 2)
            $memberId = trim($row[2] ?? '');
            if (!empty($memberId)) {
                $memberIds[] = $memberId;
            }
        }
        fclose($file);

        $memberIds = array_unique($memberIds);
        $totalMemberIdsCount = count($memberIds);
        $this->command?->info("Found {$totalMemberIdsCount} unique MemberIDs in CSV.");

        $this->command?->info('Updating property_private_purchasers table with is_ghumantu = 1...');
        
        $chunks = array_chunk($memberIds, 500);
        $totalUpdated = 0;
        
        foreach ($chunks as $chunk) {
            $updated = DB::table('property_private_purchasers')
                ->whereIn('MemberID', $chunk)
                ->update(['is_ghumantu' => 1]);
            $totalUpdated += $updated;
        }

        $this->command?->info("Successfully marked {$totalUpdated} property purchasers as ghumantu (is_ghumantu = 1).");
    }
}
