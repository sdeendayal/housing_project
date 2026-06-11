<?php

namespace Database\Seeders;

use App\Models\RoleGroup;
use Illuminate\Database\Seeder;

class RoleGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Citizen',
                'slug' => 'citizen',
                'description' => 'Public users who login via citizen mobile OTP.',
            ],
            [
                'name' => 'Department',
                'slug' => 'department',
                'description' => 'Department officers and staff who login via department mobile OTP.',
            ],
        ];

        foreach ($groups as $group) {
            RoleGroup::updateOrCreate(
                ['slug' => $group['slug']],
                $group
            );
        }

        // Keep legacy slug compatible during transition
        RoleGroup::updateOrCreate(
            ['slug' => 'departmental'],
            [
                'name' => 'Department',
                'description' => 'Legacy departmental slug — use department group.',
            ]
        );
    }
}
