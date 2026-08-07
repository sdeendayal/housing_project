<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EwsEligibleDrawListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("Truncating existing ews_eligible_draw_list_5 table...");
        DB::table('ews_eligible_draw_list_5')->truncate();

        $this->command->info("Fetching records from all_ews_data_544...");
        $records = DB::table('all_ews_data_544')->get();
        $this->command->info("Total records in all_ews_data_544: " . count($records));

        $batch = [];
        $batchSize = 250;
        $count = 0;
        $seenPurchasers = [];

        foreach ($records as $row) {
            $purchaserId = $row->PrivatePurchaserId ?? null;
            if ($purchaserId !== null && $purchaserId !== '' && $purchaserId !== 'NULL' && $purchaserId !== 'null') {
                if (isset($seenPurchasers[$purchaserId])) {
                    // Skip duplicate purchaser records
                    continue;
                }
                $seenPurchasers[$purchaserId] = true;
            }

            $batch[] = [
                'application_number' => $row->ApplicationNo ?? $row->ApplicationNo_2 ?? null,
                'full_name' => $row->PrivatePurchaserName ?? null,
                'aadhar_no' => $row->AadhaarNo ?? null,
                'mobile_number' => $row->MobileNo ?? $row->MobileNo_2 ?? null,
                'secure_id' => $row->secure_id ?? \Illuminate\Support\Str::random(32),
                'dist_name' => $row->dist ?? null,
                'dist_id' => $row->dist_id ?? null,
                'property_type' => $row->property_type ?? 'flat',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('ews_eligible_draw_list_5')->insert($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            DB::table('ews_eligible_draw_list_5')->insert($batch);
            $count += count($batch);
        }

        $this->command->info("Successfully seeded {$count} unique records into the ews_eligible_draw_list_5 table.");
    }
}