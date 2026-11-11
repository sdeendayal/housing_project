<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EwsTcpSeeder extends Seeder
{
    /**
     * Seed TCP (Town & Country Planning) Role, User, and RoleType.
     */
    public function run(): void
    {
        // 1. Ensure TCP role exists in roles table
        $role = Role::updateOrCreate(
            ['slug' => 'ews_tcp'],
            [
                'name' => 'EWS Town & Country Planning (TCP)',
                'dashboard_route' => 'ews.tcp.dashboard',
                'dashboard_path' => '/ews/tcp/dashboard',
                'is_active' => true,
            ]
        );

        // 2. Ensure TCP user exists in users table
        $user = User::updateOrCreate(
            ['email' => 'ews_tcp@gmail.com'],
            [
                'name' => 'EWS TCP Officer',
                'mobile' => '7777777777',
                'password' => Hash::make('password'),
                'role' => 'ews_tcp',
                'scheme' => 'EWS',
                'Is_Active' => '1',
                'Is_Deleted' => '0',
            ]
        );

        // 3. Ensure role_types mapping exists
        RoleType::updateOrCreate(
            ['user_id' => $user->id],
            [
                'role_id' => $role->id,
                'Is_Active' => '1',
                'Is_Deleted' => '0',
            ]
        );

        $this->command->info('EWS TCP seeded successfully into roles, users, and role_types.');
        $this->command->info('Email: ews_tcp@gmail.com | Password: password | Dashboard: /ews/tcp/dashboard');
    }
}

