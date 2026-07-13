<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleGroup;
use App\Models\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DistrictOfficerSeeder extends Seeder
{
    public function run(): void
    {
        $districtOfficerRole = Role::where('slug', 'district_officer')->first();

        if (! $districtOfficerRole) {
            $this->command->error('District_officer role not found. Run RoleSeeder first.');

            return;
        }

        $districtsWithApps = DB::table('property_private_purchasers')
            ->select('DistrictId')
            ->distinct()
            ->pluck('DistrictId');

        $districts = DB::table('districts')
            ->whereIn('DistrictId', $districtsWithApps)
            ->where('Is_Active', 1)
            ->where('Is_Deleted', 0)
            ->get();

        if ($districts->isEmpty()) {
            $this->command->warn('No active districts found in property_private_purchasers. Seeding fallback Rohtak Site Engineer.');
            $districts = DB::table('districts')
                ->where('DistrictName', 'ROHTAK')
                ->where('Is_Active', 1)
                ->where('Is_Deleted', 0)
                ->get();
        }

        $plainPassword = Hash::make('officer123');

        foreach ($districts as $district) {
            $mobile = '99999' . str_pad($district->DistrictId, 5, '0', STR_PAD_LEFT);
            $name = ucwords(strtolower($district->DistrictName)) . ' Site Engineer';

            $user = User::updateOrCreate(
                ['mobile' => $mobile],
                [
                    'name' => $name,
                    'email' => null,
                    'password' => $plainPassword,
                    'role' => 'district_officer',
                    'district_id' => $district->DistrictId,
                    'district_name' => $district->DistrictName,
                ]
            );

            RoleType::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'role_id' => $districtOfficerRole->id,
                ]
            );
        }

        $this->command->info('District officer users created in users table (department role group).');
        $this->command->info('Login via Department Officer Login | Example: 9999900005 | OTP sent via SMS');
    }
}
