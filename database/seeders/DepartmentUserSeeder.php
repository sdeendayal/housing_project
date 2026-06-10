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
        $departmentGroup = RoleGroup::whereIn('slug', ['department', 'departmental'])->first();
        $adminRole = Role::where('slug', 'admin')->first();

        if (! $departmentGroup || ! $adminRole) {
            $this->command->error('Department group or admin role not found. Run RoleGroupSeeder and RoleSeeder first.');

            return;
        }

        // Skip if department user already exists
        $existing = User::where('email', 'department@gmail.com')->first();

        if ($existing) {
            // Ensure role mapping exists
            RoleType::updateOrCreate(
                ['user_id' => $existing->id],
                ['role_id' => $adminRole->id, 'role_group_id' => $departmentGroup->id]
            );

            return;
        }

        $user = User::create([
            'name' => 'Department User',
            'email' => 'department@gmail.com',
            'mobile' => '9876543210',
            'password' => Hash::make('123456'),
            'role' => 'department',
        ]);

        RoleType::create([
            'user_id' => $user->id,
            'role_id' => $adminRole->id,
            'role_group_id' => $departmentGroup->id,
        ]);
    }
}
