<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleGroup;
use App\Models\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MmgayBdoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Fetch the department role group
        $departmentGroup = RoleGroup::whereIn('slug', ['department', 'departmental'])->first();

        if (!$departmentGroup) {
            $this->command->error('Department group not found. Run RoleGroupSeeder first.');
            return;
        }

        // 2. Create MMGAY BDO role
        $bdoRole = Role::updateOrCreate(
            ['slug' => 'mmgay_bdo'],
            [
                'role_group_id' => $departmentGroup->id,
                'name' => 'MMGAY BDO',
                'slug' => 'mmgay_bdo',
                'dashboard_route' => 'mmgay.bdo.dashboard',
                'dashboard_path' => null,
            ]
        );

        $this->command->info('MMGAY BDO Role seeded.');

        // 3. Create MMGAY BDO user
        $bdoUser = User::updateOrCreate(
            ['email' => 'bdo@mmgay.com'],
            [
                'name' => 'MMGAY BDO',
                'email' => 'bdo@mmgay.com',
                'mobile' => '8888888888',
                'password' => Hash::make('password123'),
                'role' => 'mmgay_bdo',
                'scheme' => 'MMGAY',
                'Is_Active' => '1',
                'Is_Deleted' => '0',
                'district_id' => 3, // Default district Rewari in districtmaster table
                'district_name' => 'REWARI',
            ]
        );

        // 4. Create role_types entry
        RoleType::updateOrCreate(
            ['user_id' => $bdoUser->id],
            [
                'role_id' => $bdoRole->id,
                'role_group_id' => $departmentGroup->id,
            ]
        );

        $this->command->info('MMGAY BDO User seeded successfully. Login with: bdo@mmgay.com / password123');
    }
}
