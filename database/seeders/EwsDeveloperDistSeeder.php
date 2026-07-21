<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EwsDeveloperDistSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ews_developer_districts')->truncate();

        $districts = [
            ['id' => 1, 'name' => 'AMBALA'],
            ['id' => 2, 'name' => 'BHIWANI'],
            ['id' => 3, 'name' => 'CHARKHI-DADRI'],
            ['id' => 4, 'name' => 'FARIDABAD'],
            ['id' => 5, 'name' => 'FATEHABAD'],
            ['id' => 6, 'name' => 'GURUGRAM'],
            ['id' => 7, 'name' => 'HANSI'],
            ['id' => 8, 'name' => 'HISAR'],
            ['id' => 9, 'name' => 'JHAJJAR'],
            ['id' => 10, 'name' => 'JIND'],
            ['id' => 11, 'name' => 'KAITHAL'],
            ['id' => 12, 'name' => 'KARNAL'],
            ['id' => 13, 'name' => 'KURUKSHETRA'],
            ['id' => 14, 'name' => 'MAHENDERGARH'],
            ['id' => 15, 'name' => 'NUH'],
            ['id' => 16, 'name' => 'PALWAL'],
            ['id' => 17, 'name' => 'PANCHKULA'],
            ['id' => 18, 'name' => 'PANIPAT'],
            ['id' => 19, 'name' => 'REWARI'],
            ['id' => 20, 'name' => 'ROHTAK'],
            ['id' => 21, 'name' => 'SIRSA'],
            ['id' => 22, 'name' => 'SONIPAT'],
            ['id' => 23, 'name' => 'YAMUNANAGAR'],
        ];

        foreach ($districts as $district) {
            DB::table('ews_developer_districts')->updateOrInsert(
                ['id' => $district['id']],
                ['name' => $district['name'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
