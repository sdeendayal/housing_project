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
                'dashboard_route' => null,
                'dashboard_path' => null,
            ],
            [
                'name' => 'EWS Developer',
                'slug' => 'ews_developer',
                'dashboard_route' => 'ews.developer.dashboard',
                'dashboard_path' => null,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }

        $this->syncExistingRoleTypes();

        $this->command->info('Roles seeded successfully.');
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
