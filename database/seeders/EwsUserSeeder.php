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

        $this->command->info("Checking if aws_flats_crid table has data...");
        $recordsCount = DB::table('aws_flats_crid')->count();
        if ($recordsCount === 0) {
            $this->command->warn("aws_flats_crid table is empty. Running AwsFlatsCridSeeder first...");
            $seeder = new AwsFlatsCridSeeder();
            $seeder->setCommand($this->command);
            $seeder->run();
        }

        $this->command->info("Deleting existing EWS users and role types...");
        $existingEwsUserIds = User::where('role', 'ews_user')->pluck('id');
        RoleType::whereIn('user_id', $existingEwsUserIds)->delete();
        User::where('role', 'ews_user')->delete();

        $this->command->info("Seeding EWS users from aws_flats_crid table into users and role_types...");

        $count = 0;
        $passwordHash = Hash::make('123456');

        DB::transaction(function () use ($ewsRole, $passwordHash, &$count) {
            $beneficiaries = DB::table('aws_flats_crid')->orderBy('Id', 'asc')->get();
            $rowIndex = 2; // Mimic the excel row index for mobile mapping consistency

            foreach ($beneficiaries as $data) {
                $ewsId = $data->Id;

                // Determine mobile number
                if ($rowIndex === 2) {
                    $mobile = '8888888888';
                } elseif ($rowIndex === 3) {
                    $mobile = '7777777777';
                } else {
                    $mobile = '9' . str_pad((string)$ewsId, 9, '0', STR_PAD_LEFT);
                }

                $userId = DB::table('users')->insertGetId([
                    'name' => $data->membername ?? 'EWS User',
                    'email' => "ews_{$ewsId}@gmail.com",
                    'mobile' => $mobile,
                    'password' => $passwordHash,
                    'role' => 'ews_user',
                    'scheme' => 'EWS',
                    'Is_Active' => '1',
                    'Is_Deleted' => '0',
                    'district_name' => $data->District ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('role_types')->insert([
                    'user_id' => $userId,
                    'role_id' => $ewsRole->id,
                    'Is_Active' => '1',
                    'Is_Deleted' => '0',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $count++;
                $rowIndex++;
            }
        });

        $this->command->info("Successfully seeded {$count} users and role mappings from aws_flats_crid.");
    }
}
