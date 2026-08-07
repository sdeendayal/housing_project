<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EwsBookingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '2G');

        $this->command->info("Truncating existing ews_bookings_7 table...");
        DB::table('ews_bookings_7')->truncate();

        if (!Schema::hasTable('all_ews_data_544')) {
            $this->command->error("Source table all_ews_data_544 does not exist.");
            return;
        }

        $this->command->info("Fetching source records from all_ews_data_544...");
        $records = DB::table('all_ews_data_544')->get();
        $this->command->info("Total records in all_ews_data_544: " . count($records));

        $tableColumns = Schema::getColumnListing('ews_bookings_7');
        $sourceColumns = Schema::getColumnListing('all_ews_data_544');

        $sourceColMap = [];
        foreach ($sourceColumns as $col) {
            $sourceColMap[strtolower($col)] = $col;
        }

        $batch = [];
        $batchSize = 250;
        $count = 0;
        $seenPurchasers = [];

        foreach ($records as $row) {
            // Check for Paid status
            $isPaid = (strcasecmp(trim($row->Paid ?? ''), 'paid') === 0);
            if (!$isPaid) {
                continue;
            }

            // Check uniqueness based on PrivatePurchaserId
            $purchaserId = $row->PrivatePurchaserId ?? null;
            if ($purchaserId !== null && $purchaserId !== '' && $purchaserId !== 'NULL' && $purchaserId !== 'null') {
                if (isset($seenPurchasers[$purchaserId])) {
                    // Skip duplicates
                    continue;
                }
                $seenPurchasers[$purchaserId] = true;
            }

            $rowInsert = [];
            foreach ($tableColumns as $dbCol) {
                if ($dbCol === 'id' || $dbCol === 'created_at' || $dbCol === 'updated_at') {
                    continue;
                }

                // Explicit mappings for standard keys
                if ($dbCol === 'application_number') {
                    $rowInsert[$dbCol] = $row->ApplicationNo ?? $row->ApplicationNo_2 ?? null;
                } elseif ($dbCol === 'full_name') {
                    $rowInsert[$dbCol] = $row->PrivatePurchaserName ?? null;
                } elseif ($dbCol === 'aadhar_no') {
                    $rowInsert[$dbCol] = $row->AadhaarNo ?? null;
                } elseif ($dbCol === 'mobile_number') {
                    $rowInsert[$dbCol] = $row->MobileNo ?? $row->MobileNo_2 ?? null;
                } elseif ($dbCol === 'dist_name') {
                    $rowInsert[$dbCol] = $row->dist ?? null;
                } else {
                    // Match case-insensitively
                    $lowerCol = strtolower($dbCol);
                    if (isset($sourceColMap[$lowerCol])) {
                        $sourceKey = $sourceColMap[$lowerCol];
                        $val = $row->$sourceKey ?? null;
                        if ($val === 'NULL' || $val === 'null' || $val === '') {
                            $val = null;
                        }
                        $rowInsert[$dbCol] = $val;
                    } else {
                        $rowInsert[$dbCol] = null;
                    }
                }
            }

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

        $this->command->info("Successfully seeded {$count} unique booking records into the ews_bookings_7 table.");
    }
}