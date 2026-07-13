<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleGroup;
use App\Models\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DepartmentUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        if (!$adminRole) {
            $this->command->error('Admin role not found. Run RoleSeeder first.');

            return;
        }

        $departmentUser = User::updateOrCreate(
            ['email' => 'department@gmail.com'],
            [
                'name' => 'Department User',
                'mobile' => '9876543210',
                'password' => Hash::make('123456'),
                'role' => 'department',
                'scheme' => 'MMGAY',
            ]
        );

        // Ensure role mapping exists
        RoleType::updateOrCreate(
            ['user_id' => $departmentUser->id],
            ['role_id' => $adminRole->id]
        );

        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'mobile' => '9990009999',
                'password' => Hash::make('123456'),
                'role' => 'super_admin',
                'scheme' => 'MMGAY',
            ]
        );
    }
}
