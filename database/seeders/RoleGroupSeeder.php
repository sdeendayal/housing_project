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
                'description' => 'Public users who login via mobile OTP.',
            ],
            [
                'name' => 'Departmental',
                'slug' => 'departmental',
                'description' => 'Department users who login via mobile OTP.',
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
