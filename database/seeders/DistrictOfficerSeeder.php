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
        $departmentGroup = RoleGroup::whereIn('slug', ['department', 'departmental'])->first();
        $districtOfficerRole = Role::where('slug', 'district_officer')->first();

        if (! $departmentGroup || ! $districtOfficerRole) {
            $this->command->error('Department group or district_officer role not found. Run RoleGroupSeeder and RoleSeeder first.');

            return;
        }

        $officers = [
            ['name' => 'Saharanpur Officer', 'mobile' => '9999900001', 'district_name' => 'Saharanpur'],
            ['name' => 'Muzaffarnagar Officer', 'mobile' => '9999900002', 'district_name' => 'Muzaffarnagar'],
            ['name' => 'Meerut Officer', 'mobile' => '9999900003', 'district_name' => 'Meerut'],
            ['name' => 'Haridwar Officer', 'mobile' => '9999900004', 'district_name' => 'Haridwar'],
            ['name' => 'Rohtak Officer', 'mobile' => '9999900005', 'district_name' => 'ROHTAK'],
        ];

        $plainPassword = Hash::make('officer123');

        foreach ($officers as $officerData) {
            $district = DB::table('districts')
                ->where('DistrictName', 'like', '%'.$officerData['district_name'].'%')
                ->where('Is_Deleted', 0)
                ->first();

            $user = User::updateOrCreate(
                ['mobile' => $officerData['mobile']],
                [
                    'name' => $officerData['name'],
                    'email' => null,
                    'password' => $plainPassword,
                    'role' => 'district_officer',
                    'district_id' => $district->DistrictId ?? null,
                    'district_name' => $district->DistrictName ?? $officerData['district_name'],
                ]
            );

            RoleType::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'role_id' => $districtOfficerRole->id,
                    'role_group_id' => $departmentGroup->id,
                ]
            );
        }

        $this->command->info('District officer users created in users table (department role group).');
        $this->command->info('Login via Department Officer Login | Example: 9999900005 | OTP sent via SMS');
    }
}
