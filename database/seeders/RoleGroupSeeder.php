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
                'id' => 1,
                'name' => 'Citizen',
                'slug' => 'citizen',
                'description' => 'Public users who login via citizen mobile OTP.',
            ],
            [
                'id' => 2,
                'name' => 'Department',
                'slug' => 'department',
                'description' => 'Department/District officers and staff who login for Physical Possession.',
            ],
            [
                'id' => 3,
                'name' => 'Villager',
                'slug' => 'villager',
                'description' => 'Rural/Villager users under MMGAY scheme who login via mobile OTP.',
            ],
            [
                'id' => 4,
                'name' => 'MMGAV BDO',
                'slug' => 'mmgav_bdeo',
                'description' => 'MMGAV BDO officers who login via email and password.',
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
