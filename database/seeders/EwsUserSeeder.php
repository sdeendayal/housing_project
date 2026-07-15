<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EwsUserSeeder extends Seeder
{
    public function run(): void
    {
        $ewsRole = Role::where('slug', 'ews_user')->first();
        if (!$ewsRole) {
            $this->command->error('EWS User role not found. Run RoleSeeder first.');
            return;
        }

        $this->command->info("Checking if all_ews_data_1 table has data...");
        $recordsCount = DB::table('all_ews_data_1')->count();
        if ($recordsCount === 0) {
            $this->command->warn("all_ews_data_1 table is empty. Running AllEwsDataSeeder first...");
            $seeder = new AllEwsDataSeeder();
            $seeder->setCommand($this->command);
            $seeder->run();
        }

        $this->command->info("Deleting existing EWS users and role types...");
        $existingEwsUserIds = User::where('role', 'ews_user')->pluck('id');
        
        // Delete in chunks to avoid query parameter limit issues
        RoleType::whereIn('user_id', $existingEwsUserIds)->delete();
        User::where('role', 'ews_user')->delete();

        $this->command->info("Seeding EWS users from all_ews_data_1 table...");

        $passwordHash = Hash::make('123456');
        $batch = [];
        $batchSize = 1000;
        
        $insertedMobiles = [];
        // Fetch all existing mobiles in database to prevent unique constraint conflicts
        $existingMobiles = DB::table('users')->whereNotNull('mobile')->pluck('mobile')->toArray();
        foreach ($existingMobiles as $m) {
            $insertedMobiles[$m] = true;
        }

        $allEwsData = DB::table('all_ews_data_1')->orderBy('id', 'asc')->get();

        foreach ($allEwsData as $record) {
            $mobile = trim($record->mobile_number ?? '');
            
            // Clean mobile number
            if ($mobile === 'NA' || $mobile === 'None' || $mobile === 'none' || $mobile === 'null' || !is_numeric($mobile) || strlen($mobile) !== 10) {
                $mobile = null;
            }

            // Avoid inserting duplicate mobile numbers to respect unique constraint
            if ($mobile !== null) {
                if (isset($insertedMobiles[$mobile])) {
                    $mobile = null; // Set to null to avoid conflict
                } else {
                    $insertedMobiles[$mobile] = true;
                }
            }

            $batch[] = [
                'name' => trim($record->full_name ?? 'EWS User'),
                'email' => "ews_{$record->id}@gmail.com",
                'mobile' => $mobile,
                'password' => $passwordHash,
                'role' => 'ews_user',
                'scheme' => 'EWS',
                'Is_Active' => '1',
                'Is_Deleted' => '0',
                'district_name' => trim($record->bt_name ?? 'Sonipat'),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('users')->insert($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            DB::table('users')->insert($batch);
        }

        // Now, seed the role types mapping in bulk
        $this->command->info("Seeding role mappings for the newly created EWS users...");
        
        $newEwsUserIds = DB::table('users')
            ->where('role', 'ews_user')
            ->pluck('id');

        $roleBatch = [];
        foreach ($newEwsUserIds as $userId) {
            $roleBatch[] = [
                'user_id' => $userId,
                'role_id' => $ewsRole->id,
                'Is_Active' => '1',
                'Is_Deleted' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($roleBatch) >= $batchSize) {
                DB::table('role_types')->insert($roleBatch);
                $roleBatch = [];
            }
        }

        if (count($roleBatch) > 0) {
            DB::table('role_types')->insert($roleBatch);
        }

        $this->command->info("Successfully seeded " . $newEwsUserIds->count() . " EWS users and role mappings from all_ews_data_1.");
    }
}
