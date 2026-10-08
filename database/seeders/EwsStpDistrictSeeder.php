<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class EwsStpDistrictSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed ONLY the 5 designated TCP STP/DTP zones: Rohtak, Faridabad, Panchkula, Gurugram, Hisar
        $districts = [
            ['name' => 'ROHTAK',    'code' => 'ROH', 'is_active' => 1],
            ['name' => 'FARIDABAD', 'code' => 'FBD', 'is_active' => 1],
            ['name' => 'PANCHKULA', 'code' => 'PKL', 'is_active' => 1],
            ['name' => 'GURUGRAM',  'code' => 'GGN', 'is_active' => 1],
            ['name' => 'HISAR',     'code' => 'HSR', 'is_active' => 1],
        ];

        // Ensure only these 5 exist (remove extraneous entries like Sonipat/Panipat)
        DB::table('ews_stp_districts')
            ->whereNotIn('name', ['ROHTAK', 'FARIDABAD', 'PANCHKULA', 'GURUGRAM', 'HISAR'])
            ->delete();

        foreach ($districts as $district) {
            DB::table('ews_stp_districts')->updateOrInsert(
                ['name' => $district['name']],
                [
                    'code' => $district['code'],
                    'is_active' => $district['is_active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 2. Ensure ews_stp role exists in roles table
        Role::updateOrCreate(
            ['slug' => 'ews_stp'],
            [
                'name' => 'EWS Senior Town Planner (STP)',
                'dashboard_route' => 'ews.developer.dashboard',
                'is_active' => true,
            ]
        );

        // Also ensure ews_developer role remains for backward compatibility
        Role::updateOrCreate(
            ['slug' => 'ews_developer'],
            [
                'name' => 'EWS Developer / STP',
                'dashboard_route' => 'ews.developer.dashboard',
                'is_active' => true,
            ]
        );
    }
}

