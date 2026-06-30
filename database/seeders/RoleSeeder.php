<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleGroup;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $citizenGroup = RoleGroup::where('slug', 'citizen')->first();
        $departmentGroup = RoleGroup::whereIn('slug', ['department', 'departmental'])->first();
        $villagerGroup = RoleGroup::where('slug', 'villager')->first();

        if (! $citizenGroup || ! $departmentGroup || ! $villagerGroup) {
            $this->command->error('Role groups not found. Run RoleGroupSeeder first.');

            return;
        }

        $roles = [
            [
                'role_group_id' => $citizenGroup->id,
                'name' => 'Citizen',
                'slug' => 'citizen',
                'dashboard_route' => 'citizen.dashboard',
                'dashboard_path' => null,
            ],
            [
                'role_group_id' => $villagerGroup->id,
                'name' => 'Villager',
                'slug' => 'villager',
                'dashboard_route' => 'mmgay.citizen.dashboard',
                'dashboard_path' => null,
            ],
            [
                'role_group_id' => $departmentGroup->id,
                'name' => 'District Officer',
                'slug' => 'district_officer',
                'dashboard_route' => 'pp.officer.dashboard',
                'dashboard_path' => null,
            ],
            [
                'role_group_id' => $departmentGroup->id,
                'name' => 'State Officer',
                'slug' => 'state_officer',
                'dashboard_route' => null,
                'dashboard_path' => '/mmsay-department-dashboard',
            ],
            [
                'role_group_id' => $departmentGroup->id,
                'name' => 'Admin',
                'slug' => 'admin',
                'dashboard_route' => 'department.dashboard',
                'dashboard_path' => null,
            ],
            [
                'role_group_id' => $departmentGroup->id,
                'name' => 'Director',
                'slug' => 'director',
                'dashboard_route' => 'department.dashboard',
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
        $roleTypes = \App\Models\RoleType::with(['roleGroup', 'user'])->whereNull('role_id')->get();

        foreach ($roleTypes as $roleType) {
            $groupSlug = $roleType->roleGroup?->slug;
            $userRole = $roleType->user?->role;

            $roleSlug = match (true) {
                $userRole === 'district_officer' => 'district_officer',
                $groupSlug === 'citizen' => 'citizen',
                in_array($groupSlug, ['department', 'departmental'], true) => 'admin',
                default => null,
            };

            if (! $roleSlug) {
                continue;
            }

            $role = Role::where('slug', $roleSlug)->first();

            if ($role) {
                $roleType->update([
                    'role_id' => $role->id,
                    'role_group_id' => $role->role_group_id,
                ]);
            }
        }
    }
}
