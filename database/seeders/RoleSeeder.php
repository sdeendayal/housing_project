<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Citizen',
                'slug' => 'citizen',
                'dashboard_route' => 'citizen.dashboard',
                'dashboard_path' => null,
            ],
            [
                'name' => 'Villager',
                'slug' => 'villager',
                'dashboard_route' => 'mmgav.villager.dashboard',
                'dashboard_path' => null,
            ],
            [
                'name' => 'District Officer',
                'slug' => 'district_officer',
                'dashboard_route' => 'pp.officer.dashboard',
                'dashboard_path' => null,
            ],
            [
                'name' => 'State Officer',
                'slug' => 'state_officer',
                'dashboard_route' => null,
                'dashboard_path' => '/mmsay-department-dashboard',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'dashboard_route' => 'department.dashboard',
                'dashboard_path' => null,
            ],
            [
                'name' => 'Director',
                'slug' => 'director',
                'dashboard_route' => 'department.dashboard',
                'dashboard_path' => null,
            ],
            [
                'name' => 'District CEO',
                'slug' => 'district_ceo',
                'dashboard_route' => 'district.dashboard',
                'dashboard_path' => null,
            ],
            [
                'name' => 'Deputy Commissioner',
                'slug' => 'dc',
                'dashboard_route' => 'district.dashboard',
                'dashboard_path' => null,
            ],
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'dashboard_route' => 'admin.dashboard',
                'dashboard_path' => null,
            ],
            [
                'name' => 'EWS User',
                'slug' => 'ews_user',
                'dashboard_route' => 'ews.dashboard',
                'dashboard_path' => null,
            ],
            [
                'name' => 'EWS Department',
                'slug' => 'ews_department',
                'dashboard_route' => 'ews.department.dashboard',
                'dashboard_path' => '/ews/department/dashboard',
            ],
            [
                'name' => 'EWS Developer',
                'slug' => 'ews_developer',
                'dashboard_route' => 'ews.developer.dashboard',
                'dashboard_path' => null,
            ],
            [
                'name' => 'EWS Senior Town Planner (STP)',
                'slug' => 'ews_stp',
                'dashboard_route' => 'ews.stp.dashboard',
                'dashboard_path' => '/ews/stp/dashboard',
            ],
            [
                'name' => 'EWS Town & Country Planning (TCP)',
                'slug' => 'ews_tcp',
                'dashboard_route' => 'ews.tcp.dashboard',
                'dashboard_path' => '/ews/tcp/dashboard',
            ],
            [
                'name' => 'MMGAY DTP',
                'slug' => 'mmgay-dtp',
                'dashboard_route' => 'pp.dtp.dashboard',
                'dashboard_path' => null,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }

        $this->seedDefaultUsersAndRoleTypes();
        $this->syncExistingRoleTypes();

        $this->command->info('Roles, Users and RoleTypes seeded successfully.');
    }

    private function seedDefaultUsersAndRoleTypes(): void
    {
        // 1. DTP User (MMGAY DTP)
        $dtpRole = Role::where('slug', 'mmgay-dtp')->first();
        if ($dtpRole) {
            $dtpUser = \App\Models\User::updateOrCreate(
                ['email' => 'dtp@gmail.com'],
                [
                    'name' => 'MMGAY DTP Officer',
                    'mobile' => '9888877777',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'mmgay-dtp',
                    'scheme' => 'MMGAY',
                    'Is_Active' => '1',
                    'Is_Deleted' => '0',
                ]
            );
            \App\Models\RoleType::updateOrCreate(
                ['user_id' => $dtpUser->id],
                [
                    'role_id' => $dtpRole->id,
                    'Is_Active' => '1',
                    'Is_Deleted' => '0',
                ]
            );
        }

        // 2. STP User (EWS Senior Town Planner)
        $stpRole = Role::where('slug', 'ews_stp')->first();
        if ($stpRole) {
            $stpUser = \App\Models\User::updateOrCreate(
                ['email' => 'ews_stp@gmail.com'],
                [
                    'name' => 'STP Rohtak Zone',
                    'mobile' => '9999999999',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'ews_stp',
                    'scheme' => 'EWS',
                    'district_name' => 'ROHTAK ZONE',
                    'zone_name' => 'ROHTAK ZONE',
                    'Is_Active' => '1',
                    'Is_Deleted' => '0',
                ]
            );
            \App\Models\RoleType::updateOrCreate(
                ['user_id' => $stpUser->id],
                [
                    'role_id' => $stpRole->id,
                    'Is_Active' => '1',
                    'Is_Deleted' => '0',
                ]
            );
        }

        // 3. TCP User (EWS Town & Country Planning)
        $tcpRole = Role::where('slug', 'ews_tcp')->first();
        if ($tcpRole) {
            $tcpUser = \App\Models\User::updateOrCreate(
                ['email' => 'ews_tcp@gmail.com'],
                [
                    'name' => 'EWS TCP Officer',
                    'mobile' => '7777777777',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'ews_tcp',
                    'scheme' => 'EWS',
                    'Is_Active' => '1',
                    'Is_Deleted' => '0',
                ]
            );
            \App\Models\RoleType::updateOrCreate(
                ['user_id' => $tcpUser->id],
                [
                    'role_id' => $tcpRole->id,
                    'Is_Active' => '1',
                    'Is_Deleted' => '0',
                ]
            );
        }
    }

    private function syncExistingRoleTypes(): void
    {
        $roleTypes = \App\Models\RoleType::with(['user'])->whereNull('role_id')->get();

        foreach ($roleTypes as $roleType) {
            $userRole = $roleType->user?->role;

            $roleSlug = match (true) {
                $userRole === 'district_officer' => 'district_officer',
                $userRole === 'district_ceo' => 'district_ceo',
                $userRole === 'dc' => 'dc',
                $userRole === 'mmgay-dtp' => 'mmgay-dtp',
                $userRole === 'ews_stp' || $userRole === 'stp' => 'ews_stp',
                $userRole === 'ews_tcp' || $userRole === 'tcp' => 'ews_tcp',
                $userRole === 'ews_department' => 'ews_department',
                $userRole === 'ews_developer' => 'ews_developer',
                default => null,
            };

            if (! $roleSlug) {
                continue;
            }

            $role = Role::where('slug', $roleSlug)->first();

            if ($role) {
                $roleType->update([
                    'role_id' => $role->id,
                ]);
            }
        }
    }
}
