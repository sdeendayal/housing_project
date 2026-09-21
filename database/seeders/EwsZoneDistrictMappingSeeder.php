<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EwsStpDistrict;
use App\Models\User;

class EwsZoneDistrictMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Based on Haryana Town & Country Planning (TCP) official mapping from 18.09.2026.xlsx:
     * 1. Rohtak Zone: Sonipat, Rohtak, Panipat, Jhajjar
     * 2. Faridabad Zone: Faridabad, Mewat (Nuh), Palwal
     * 3. Panchkula Zone: Ambala, Yamunanagar, Panchkula, Kaithal, Karnal, Kurukshetra
     * 4. Gurugram Zone: Gurugram, Rewari, Mahendragarh
     * 5. Hisar Zone: Hisar, Sirsa, Jind, Fatehabad, Bhiwani, Charkhi Dadri (including Hansi)
     */
    public function run(): void
    {
        // 1. Ensure the 5 TCP Zones exist in ews_stp_districts
        $zones = [
            'ROHTAK' => 'ROH',
            'FARIDABAD' => 'FBD',
            'PANCHKULA' => 'PKL',
            'GURUGRAM' => 'GGN',
            'HISAR' => 'HSR',
        ];

        // Clean up any extra entries not in the 5 official TCP Zones
        DB::table('ews_stp_districts')
            ->whereNotIn('name', array_keys($zones))
            ->delete();

        $zoneModelMap = [];
        foreach ($zones as $zoneName => $code) {
            $zone = EwsStpDistrict::updateOrCreate(
                ['name' => $zoneName],
                ['code' => $code, 'is_active' => 1]
            );
            $zoneModelMap[$zoneName] = $zone->id;
        }

        // 2. Mapping of Districts to Zones
        $mapping = [
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
            'MAHENDERGARH' => 'GURUGRAM',
            'MAHENDRAGARH' => 'GURUGRAM',

            // Hisar Zone
            'HISAR' => 'HISAR',
            'SIRSA' => 'HISAR',
            'JIND' => 'HISAR',
            'FATEHABAD' => 'HISAR',
            'BHIWANI' => 'HISAR',
            'CHARKHI-DADRI' => 'HISAR',
            'CHARKHI DADRI' => 'HISAR',
            'HANSI' => 'HISAR',
        ];

        foreach ($mapping as $districtName => $zoneName) {
            if (isset($zoneModelMap[$zoneName])) {
                $zoneId = $zoneModelMap[$zoneName];
                DB::table('ews_districts')
                    ->whereRaw('UPPER(name) = ?', [strtoupper($districtName)])
                    ->update(['zone_id' => $zoneId]);
            }
        }

        // 3. Update existing STP users to link to their correct TCP Zone
        $users = User::whereIn('role', ['ews_stp', 'stp', 'ews_developer'])->get();
        foreach ($users as $u) {
            $userDist = strtoupper(trim(str_replace(' ZONE', '', $u->district_name ?? '')));
            
            // Check if userDist maps to a zone
            $targetZoneName = $mapping[$userDist] ?? ($zones[$userDist] ?? null ? $userDist : null);
            if (!$targetZoneName && isset($zoneModelMap[$userDist])) {
                $targetZoneName = $userDist;
            }

            if ($targetZoneName && isset($zoneModelMap[$targetZoneName])) {
                $u->zone_id = $zoneModelMap[$targetZoneName];
                $u->zone_name = $targetZoneName . ' ZONE';
                $u->save();
            }
        }

        // 4. Update builder flats zone_id and zone_name
        $flats = DB::table('ews_builder_flats')->get();
        foreach ($flats as $f) {
            $flatDist = strtoupper(trim(str_replace(' ZONE', '', $f->district_name ?? '')));
            $targetZoneName = $mapping[$flatDist] ?? null;
            if ($targetZoneName && isset($zoneModelMap[$targetZoneName])) {
                DB::table('ews_builder_flats')->where('id', $f->id)->update([
                    'zone_id' => $zoneModelMap[$targetZoneName],
                    'zone_name' => $targetZoneName . ' ZONE',
                ]);
            }
        }
    }
}
