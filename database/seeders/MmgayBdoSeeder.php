<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MmgayBdoSeeder extends Seeder
{
    public function run(): void
    {
        // 2. Create MMGAV BDO role
        $bdoRole = Role::updateOrCreate(
            ['slug' => 'mmgav_bdeo'],
            [
                'name' => 'MMGAV BDO',
                'slug' => 'mmgav_bdeo',
                'dashboard_route' => 'mmgay.bdo.dashboard',
                'dashboard_path' => null,
            ]
        );

        $this->command->info('MMGAV BDO Role seeded.');

        // 3. Create MMGAY BDO user
        $bdoUser = User::updateOrCreate(
            ['email' => 'bdo@mmgay.com'],
            [
                'name' => 'MMGAY BDPO',
                'email' => 'bdo@mmgay.com',
                'mobile' => '8888888888',
                'password' => Hash::make('password123'),
                'role' => 'mmgav_bdeo',
                'scheme' => 'MMGAY',
                'Is_Active' => '1',
                'Is_Deleted' => '0',
                'district_id' => 3, // Default district Rewari in districtmaster table
                'district_name' => 'REWARI',
                'block_id' => 37, // Rewari block in blockmaster table
                'block_name' => 'Rewari',
            ]
        );

        // 4. Create role_types entry
        RoleType::updateOrCreate(
            ['user_id' => $bdoUser->id],
            [
                'role_id' => $bdoRole->id,
            ]
        );

        $this->command->info('MMGAY BDPO User seeded successfully. Login with: bdo@mmgay.com / password123');
    }
}
