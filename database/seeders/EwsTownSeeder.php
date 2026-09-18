<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EwsTown;

class EwsTownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds all 87 Municipalities across all 23 districts of Haryana
     * from the official 'List of Municipalities with details' dataset:
     * - 11 Municipal Corporations
     * - 25 Municipal Councils
     * - 51 Municipal Committees
     */
    public function run(): void
    {
        $municipalities = [
            'AMBALA' => [
                ['name' => 'Ambala', 'type' => 'Municipal Corporation'],
                ['name' => 'Ambala Sadar', 'type' => 'Municipal Council'],
                ['name' => 'Naraingarh', 'type' => 'Municipal Committee'],
                ['name' => 'Barara', 'type' => 'Municipal Committee'],
            ],
            'BHIWANI' => [
                ['name' => 'Bhiwani', 'type' => 'Municipal Council'],
                ['name' => 'Siwani', 'type' => 'Municipal Committee'],
                ['name' => 'Bawani Khera', 'type' => 'Municipal Committee'],
                ['name' => 'Loharu', 'type' => 'Municipal Committee'],
            ],
            'CHARKHI-DADRI' => [
                ['name' => 'Charkhi Dadri', 'type' => 'Municipal Council'],
            ],
            'FARIDABAD' => [
                ['name' => 'Faridabad', 'type' => 'Municipal Corporation'],
            ],
            'FATEHABAD' => [
                ['name' => 'Fatehabad', 'type' => 'Municipal Council'],
                ['name' => 'Tohana', 'type' => 'Municipal Council'],
                ['name' => 'Ratia', 'type' => 'Municipal Committee'],
                ['name' => 'Bhuna', 'type' => 'Municipal Committee'],
                ['name' => 'Jakhal Mandi', 'type' => 'Municipal Committee'],
            ],
            'GURUGRAM' => [
                ['name' => 'Gurugram', 'type' => 'Municipal Corporation'],
                ['name' => 'Manesar', 'type' => 'Municipal Corporation'],
                ['name' => 'Sohna', 'type' => 'Municipal Council'],
                ['name' => 'Pataudi-Jatauli Mandi', 'type' => 'Municipal Council'],
                ['name' => 'Farukhnagar', 'type' => 'Municipal Committee'],
            ],
            'HANSI' => [
                ['name' => 'Hansi', 'type' => 'Municipal Council'],
                ['name' => 'Narnaund', 'type' => 'Municipal Committee'],
            ],
            'HISAR' => [
                ['name' => 'Hisar', 'type' => 'Municipal Corporation'],
                ['name' => 'Barwala', 'type' => 'Municipal Council'],
                ['name' => 'Uklana', 'type' => 'Municipal Committee'],
            ],
            'JHAJJAR' => [
                ['name' => 'Jhajjar', 'type' => 'Municipal Council'],
                ['name' => 'Bahadurgarh', 'type' => 'Municipal Council'],
                ['name' => 'Beri', 'type' => 'Municipal Committee'],
            ],
            'JIND' => [
                ['name' => 'Jind', 'type' => 'Municipal Council'],
                ['name' => 'Narwana', 'type' => 'Municipal Council'],
                ['name' => 'Safidon', 'type' => 'Municipal Committee'],
                ['name' => 'Uchana', 'type' => 'Municipal Committee'],
                ['name' => 'Julana', 'type' => 'Municipal Committee'],
            ],
            'KAITHAL' => [
                ['name' => 'Kaithal', 'type' => 'Municipal Council'],
                ['name' => 'Pundri', 'type' => 'Municipal Committee'],
                ['name' => 'Cheeka', 'type' => 'Municipal Committee'],
                ['name' => 'Kalayat', 'type' => 'Municipal Committee'],
                ['name' => 'Rajound', 'type' => 'Municipal Committee'],
                ['name' => 'Siwan', 'type' => 'Municipal Committee'],
            ],
            'KARNAL' => [
                ['name' => 'Karnal', 'type' => 'Municipal Corporation'],
                ['name' => 'Taraori', 'type' => 'Municipal Committee'],
                ['name' => 'Nilokheri', 'type' => 'Municipal Committee'],
                ['name' => 'Gharaunda', 'type' => 'Municipal Committee'],
                ['name' => 'Assandh', 'type' => 'Municipal Committee'],
                ['name' => 'Indri', 'type' => 'Municipal Committee'],
                ['name' => 'Nissing', 'type' => 'Municipal Committee'],
            ],
            'KURUKSHETRA' => [
                ['name' => 'Thanesar', 'type' => 'Municipal Council'],
                ['name' => 'Shahabad', 'type' => 'Municipal Committee'],
                ['name' => 'Ismailabad', 'type' => 'Municipal Committee'],
                ['name' => 'Ladwa', 'type' => 'Municipal Committee'],
                ['name' => 'Pehowa', 'type' => 'Municipal Committee'],
            ],
            'MAHENDERGARH' => [
                ['name' => 'Narnaul', 'type' => 'Municipal Council'],
                ['name' => 'Mahendragarh', 'type' => 'Municipal Committee'],
                ['name' => 'Kanina', 'type' => 'Municipal Committee'],
                ['name' => 'Ateli Mandi', 'type' => 'Municipal Committee'],
                ['name' => 'Nangal Chaudhary', 'type' => 'Municipal Committee'],
            ],
            'NUH' => [
                ['name' => 'Nuh', 'type' => 'Municipal Council'],
                ['name' => 'Ferozepur Jhirka', 'type' => 'Municipal Committee'],
                ['name' => 'Tauru', 'type' => 'Municipal Committee'],
                ['name' => 'Punhana', 'type' => 'Municipal Committee'],
            ],
            'PALWAL' => [
                ['name' => 'Palwal', 'type' => 'Municipal Council'],
                ['name' => 'Hodal', 'type' => 'Municipal Council'],
                ['name' => 'Hathin', 'type' => 'Municipal Committee'],
            ],
            'PANCHKULA' => [
                ['name' => 'Panchkula', 'type' => 'Municipal Corporation'],
                ['name' => 'Kalka', 'type' => 'Municipal Council'],
            ],
            'PANIPAT' => [
                ['name' => 'Panipat', 'type' => 'Municipal Corporation'],
                ['name' => 'Samalkha', 'type' => 'Municipal Council'],
            ],
            'REWARI' => [
                ['name' => 'Rewari', 'type' => 'Municipal Council'],
                ['name' => 'Bawal', 'type' => 'Municipal Committee'],
                ['name' => 'Dharuhera', 'type' => 'Municipal Committee'],
            ],
            'ROHTAK' => [
                ['name' => 'Rohtak', 'type' => 'Municipal Corporation'],
                ['name' => 'Meham', 'type' => 'Municipal Committee'],
                ['name' => 'Kalanaur', 'type' => 'Municipal Committee'],
                ['name' => 'Sampla', 'type' => 'Municipal Committee'],
            ],
            'SIRSA' => [
                ['name' => 'Sirsa', 'type' => 'Municipal Council'],
                ['name' => 'Mandi Dabwali', 'type' => 'Municipal Council'],
                ['name' => 'Rania', 'type' => 'Municipal Committee'],
                ['name' => 'Kalanwali', 'type' => 'Municipal Committee'],
                ['name' => 'Ellenabad', 'type' => 'Municipal Committee'],
            ],
            'SONIPAT' => [
                ['name' => 'Sonipat', 'type' => 'Municipal Corporation'],
                ['name' => 'Gohana', 'type' => 'Municipal Council'],
                ['name' => 'Gannaur', 'type' => 'Municipal Committee'],
                ['name' => 'Kharkhoda', 'type' => 'Municipal Committee'],
                ['name' => 'Kundli', 'type' => 'Municipal Committee'],
            ],
            'YAMUNANAGAR' => [
                ['name' => 'Yamunanagar', 'type' => 'Municipal Corporation'],
                ['name' => 'Radaur', 'type' => 'Municipal Committee'],
                ['name' => 'Sadhaura', 'type' => 'Municipal Committee'],
            ],
        ];

        $seededCount = 0;
        foreach ($municipalities as $districtKey => $townsList) {
            $district = DB::table('ews_districts')
                ->where(DB::raw('UPPER(name)'), $districtKey)
                ->orWhere(DB::raw('REPLACE(UPPER(name), " ", "-")'), $districtKey)
                ->orWhere(DB::raw('REPLACE(UPPER(name), "-", " ")'), str_replace('-', ' ', $districtKey))
                ->first();

            if (!$district) {
                $this->command->warn("District '{$districtKey}' not found in ews_districts!");
                continue;
            }

            foreach ($townsList as $town) {
                EwsTown::updateOrCreate(
                    [
                        'district_id' => $district->id,
                        'name'        => $town['name'],
                    ],
                    [
                        'district_id' => $district->id,
                        'name'        => $town['name'],
                        'type'        => $town['type'],
                    ]
                );
                $seededCount++;
            }
        }

        $this->command->info("EwsTownSeeder completed: {$seededCount} municipalities successfully seeded across 23 districts.");
    }
}
