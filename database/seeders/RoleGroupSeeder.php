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
                'description' => 'Department/District officers and staff who login for Physical Possession.',
            ],
        ];

        foreach ($groups as $group) {
            RoleGroup::updateOrCreate(
                ['slug' => $group['slug']],
                $group
            );
        }
    }
}
