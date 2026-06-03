<?php

namespace Database\Seeders;

use App\Models\RoleGroup;
use App\Models\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DepartmentUserSeeder extends Seeder
{
    public function run(): void
    {
        $departmentalRole = RoleGroup::where('slug', 'departmental')->first();

        if (! $departmentalRole) {
            $this->command->error('Departmental role group not found. Run RoleGroupSeeder first.');

            return;
        }

        // Skip if department user already exists
        $existing = User::where('email', 'department@gmail.com')->first();

        if ($existing) {
            // Ensure role mapping exists
            RoleType::firstOrCreate(
                ['user_id' => $existing->id],
                ['role_group_id' => $departmentalRole->id]
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
            'role_group_id' => $departmentalRole->id,
        ]);
    }
}
