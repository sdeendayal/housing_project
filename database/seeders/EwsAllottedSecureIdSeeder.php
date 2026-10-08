<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class EwsAllottedSecureIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Ensures all records in ews_allotted_8 have a unique 32-character secure_id.
     */
    public function run(): void
    {
        ini_set('memory_limit', '1G');

        if (!Schema::hasTable('ews_allotted_8')) {
            $this->command->warn("Table 'ews_allotted_8' does not exist. Skipping secure_id generation.");
            return;
        }

        // Check if secure_id column exists; if not, add it
        if (!Schema::hasColumn('ews_allotted_8', 'secure_id')) {
            $this->command->info("Adding 'secure_id' column to 'ews_allotted_8' table...");
            Schema::table('ews_allotted_8', function ($table) {
                $table->string('secure_id', 32)->nullable()->unique()->after('flat_no');
            });
        }

        // Find records missing secure_id
        $missingCount = DB::table('ews_allotted_8')
            ->whereNull('secure_id')
            ->orWhere('secure_id', '')
            ->count();

        if ($missingCount === 0) {
            $this->command->info("All records in ews_allotted_8 already have a valid secure_id. (Total: " . DB::table('ews_allotted_8')->count() . ")");
            return;
        }

        $this->command->info("Found {$missingCount} records missing secure_id in ews_allotted_8. Generating 32-digit unique tokens...");

        $updatedCount = 0;
        DB::table('ews_allotted_8')
            ->whereNull('secure_id')
            ->orWhere('secure_id', '')
            ->select('id')
            ->chunkById(500, function ($rows) use (&$updatedCount) {
                foreach ($rows as $row) {
                    $uniqueFound = false;
                    $secureId = '';

                    while (!$uniqueFound) {
                        // 32-character random string (letters + digits)
                        $secureId = Str::random(32);
                        $exists = DB::table('ews_allotted_8')->where('secure_id', $secureId)->exists();
                        if (!$exists) {
                            $uniqueFound = true;
                        }
                    }

                    DB::table('ews_allotted_8')
                        ->where('id', $row->id)
                        ->update(['secure_id' => $secureId]);

                    $updatedCount++;
                }
            });

        $this->command->info("Successfully populated 32-character secure_id for {$updatedCount} records in ews_allotted_8.");
    }
}
