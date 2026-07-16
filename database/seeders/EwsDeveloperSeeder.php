<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EwsDeveloperSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('slug', 'ews_developer')->first();
        if (!$role) {
            $this->command->error('EWS Developer role not found. Run RoleSeeder first.');
            return;
        }

        $role->update(['dashboard_route' => 'ews.developer.dashboard']);

        // Clean up any user with role 'ews_developer' first
        $existingDevs = User::where('role', 'ews_developer')->get();
        foreach ($existingDevs as $dev) {
            RoleType::where('user_id', $dev->id)->delete();
            $dev->delete();
        }

        // Create developer user with mobile 9999999999
        $user = User::create([
            'name' => 'EWS Developer Team',
            'email' => 'ews_developer@gmail.com',
            'mobile' => '9999999999',
            'password' => Hash::make('password'),
            'role' => 'ews_developer',
            'scheme' => 'EWS',
            'Is_Active' => '1',
            'Is_Deleted' => '0',
            'district_name' => 'Sonipat',
        ]);

        RoleType::create([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'Is_Active' => '1',
            'Is_Deleted' => '0',
        ]);

        $this->command->info('EWS Developer seeded successfully. Mobile: 9999999999, OTP: 111111 (Local)');
    }
}
