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

        $zoneMapping = [
            // Rohtak Zone
            'SONIPAT' => 'ROHTAK',
            'ROHTAK' => 'ROHTAK',
            'PANIPAT' => 'ROHTAK',
            'JHAJJAR' => 'ROHTAK',

            // Faridabad Zone
            'FARIDABAD' => 'FARIDABAD',
            'NUH' => 'FARIDABAD',
            'MEWAT' => 'FARIDABAD',
            'PALWAL' => 'FARIDABAD',

            // Panchkula Zone
            'AMBALA' => 'PANCHKULA',
            'YAMUNANAGAR' => 'PANCHKULA',
            'PANCHKULA' => 'PANCHKULA',
            'KAITHAL' => 'PANCHKULA',
            'KARNAL' => 'PANCHKULA',
            'KURUKSHETRA' => 'PANCHKULA',

            // Gurugram Zone
            'GURUGRAM' => 'GURUGRAM',
            'REWARI' => 'GURUGRAM',
            'MAHENDRAGARH' => 'GURUGRAM',

            // Hisar Zone
            'HISAR' => 'HISAR',
            'SIRSA' => 'HISAR',
            'JIND' => 'HISAR',
            'FATEHABAD' => 'HISAR',
            'BHIWANI' => 'HISAR',
            'CHARKHI-DADRI' => 'HISAR',
            'CHARKHI DADRI' => 'HISAR',
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

            // Resolve Zone from ews_stp_districts
            $zone = null;
            if (!empty($district->zone_id)) {
                $zone = DB::table('ews_stp_districts')->where('id', $district->zone_id)->first();
            }
            if (!$zone) {
                $mappedZoneName = $zoneMapping[$districtKey] ?? null;
                if ($mappedZoneName) {
                    $zone = DB::table('ews_stp_districts')->where('name', $mappedZoneName)->first();
                    if ($zone && empty($district->zone_id)) {
                        DB::table('ews_districts')->where('id', $district->id)->update(['zone_id' => $zone->id]);
                    }
                }
            }

            $zoneId = $zone ? $zone->id : null;
            $zoneName = $zone ? (str_contains(strtoupper($zone->name), 'ZONE') ? strtoupper($zone->name) : strtoupper($zone->name) . ' ZONE') : null;

            foreach ($townsList as $town) {
                EwsTown::updateOrCreate(
                    [
                        'district_id' => $district->id,
                        'name'        => $town['name'],
                    ],
                    [
                        'district_id' => $district->id,
                        'zone_id'     => $zoneId,
                        'zone_name'   => $zoneName,
                        'name'        => $town['name'],
                        'type'        => $town['type'],
                    ]
                );
                $seededCount++;
            }
        }

        // Backfill any existing towns in ews_towns (e.g. manually added ones) missing zone_id
        $unlinkedTowns = EwsTown::whereNull('zone_id')->get();
        foreach ($unlinkedTowns as $unlinkedTown) {
            $dist = DB::table('ews_districts')->where('id', $unlinkedTown->district_id)->first();
            if ($dist) {
                $z = null;
                if (!empty($dist->zone_id)) {
                    $z = DB::table('ews_stp_districts')->where('id', $dist->zone_id)->first();
                }
                if (!$z && !empty($dist->name)) {
                    $normDist = strtoupper(trim(str_replace('-', ' ', $dist->name)));
                    $mappedZ = $zoneMapping[$normDist] ?? ($zoneMapping[str_replace(' ', '-', $normDist)] ?? null);
                    if ($mappedZ) {
                        $z = DB::table('ews_stp_districts')->where('name', $mappedZ)->first();
                    }
                }
                if ($z) {
                    $zName = str_contains(strtoupper($z->name), 'ZONE') ? strtoupper($z->name) : strtoupper($z->name) . ' ZONE';
                    $unlinkedTown->update([
                        'zone_id'   => $z->id,
                        'zone_name' => $zName,
                    ]);
                }
            }
        }

        $this->command->info("EwsTownSeeder completed: {$seededCount} municipalities successfully seeded across 23 districts with zone_id and zone_name.");
    }
}
