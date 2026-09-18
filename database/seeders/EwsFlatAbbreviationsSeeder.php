<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EwsFlatAbbreviation;
use App\Models\EwsTown;

class EwsFlatAbbreviationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("Starting EwsFlatAbbreviationsSeeder for table 'ews_flat_abbreviations'...");

        // Preload Town, District and Zone mapping
        $towns = EwsTown::all()->keyBy(function ($item) {
            return strtolower(trim($item->name));
        });

        $districts = DB::table('ews_districts')->get()->keyBy('id');
        $zones = DB::table('ews_stp_districts')->get()->keyBy('id');

        $flatRecords = $this->getAllPdfRecords();
        $this->command->info("Total flat records parsed from PDFs: " . count($flatRecords));

        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($flatRecords as $record) {
            $townKey = strtolower(trim($record['town_name']));
            $town = $towns->get($townKey);

            if (!$town) {
                // If not exact match, try matching partial
                foreach ($towns as $name => $t) {
                    if (str_contains($townKey, $name) || str_contains($name, $townKey)) {
                        $town = $t;
                        break;
                    }
                }
            }

            if (!$town) {
                $this->command->warn("Town not found for: {$record['town_name']}. Skipping final_no: {$record['final_no']}");
                continue;
            }

            $district = $districts->get($town->district_id);
            $distId = $district ? $district->id : $town->district_id;
            $distName = $district ? $district->name : strtoupper($town->name);

            $zoneId = $district ? $district->zone_id : null;
            $zoneObj = $zoneId ? $zones->get($zoneId) : null;
            $zoneName = $zoneObj ? $zoneObj->name . ' ZONE' : ($distName . ' ZONE');

            $data = [
                'dist_id'      => $distId,
                'dist_name'    => $distName,
                'town_id'      => $town->id,
                'town_name'    => $town->name,
                'zone_id'      => $zoneId,
                'zone_name'    => $zoneName,
                'town_abbr'    => trim($record['town_abbr'] ?? ''),
                'project_name' => trim($record['project_name'] ?? ''),
                'project_abbr' => trim($record['project_abbr'] ?? ''),
                'floor'        => trim($record['floor'] ?? ''),
                'floor_abbr'   => trim($record['floor_abbr'] ?? ''),
                'block_tower'  => trim($record['block_tower'] ?? ''),
                'block_abbr'   => trim($record['block_abbr'] ?? ''),
                'flat_no'      => trim($record['flat_no'] ?? ''),
                'flat_abbr'    => trim($record['flat_abbr'] ?? ''),
                'final_no'     => trim($record['final_no'] ?? ''),
            ];

            // Update or create based on final_no
            $existing = EwsFlatAbbreviation::where('final_no', $data['final_no'])->first();
            if ($existing) {
                $existing->update($data);
                $updatedCount++;
            } else {
                EwsFlatAbbreviation::create($data);
                $insertedCount++;
            }
        }

        $this->command->info("Seeding completed! Inserted: {$insertedCount}, Updated: {$updatedCount}. Total: " . ($insertedCount + $updatedCount));
    }

    /**
     * Parse all 6 PDF files into structured row records.
     */
    private function getAllPdfRecords(): array
    {
        return array_merge(
            $this->getRohtakRecords(),
            $this->getPanipatRecords(),
            $this->getRewariRecords(),
            $this->getSonipatRecords(),
            $this->getFaridabadRecords(),
            $this->getGurugramRecords()
        );
    }

    /**
     * Rohtak (8 records)
     */
    private function getRohtakRecords(): array
    {
        return [
            ['town_name' => 'Rohtak', 'town_abbr' => 'RTK', 'project_name' => 'JOP Palms', 'project_abbr' => 'JOP', 'floor' => 'Ground Floor', 'floor_abbr' => 'GF', 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => '001', 'flat_abbr' => '001', 'final_no' => 'PNP-JOP-GF-EWS-001'],
            ['town_name' => 'Rohtak', 'town_abbr' => 'RTK', 'project_name' => 'JOP Palms', 'project_abbr' => 'JOP', 'floor' => 'First Floor', 'floor_abbr' => '1F', 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => '101', 'flat_abbr' => '101', 'final_no' => 'PNP-JOP-1F-EWS-101'],
            ['town_name' => 'Rohtak', 'town_abbr' => 'RTK', 'project_name' => 'Suncity Heights', 'project_abbr' => 'SUN', 'floor' => 'Ground Floor', 'floor_abbr' => 'GF', 'block_tower' => '16', 'block_abbr' => 'T16', 'flat_no' => '001', 'flat_abbr' => '001', 'final_no' => 'RTK-SUN-GF-T16-E-001'],
            ['town_name' => 'Rohtak', 'town_abbr' => 'RTK', 'project_name' => 'Suncity Heights', 'project_abbr' => 'SUN', 'floor' => 'First Floor', 'floor_abbr' => '1F', 'block_tower' => '16', 'block_abbr' => 'T16', 'flat_no' => '101', 'flat_abbr' => '101', 'final_no' => 'RTK-SUN-1F-T16-E-101'],
            ['town_name' => 'Rohtak', 'town_abbr' => 'RTK', 'project_name' => 'Suncity Heights', 'project_abbr' => 'SUN', 'floor' => 'Second Floor', 'floor_abbr' => '2F', 'block_tower' => '16', 'block_abbr' => 'T16', 'flat_no' => '201', 'flat_abbr' => '201', 'final_no' => 'RTK-SUN-2F-T16-E-201'],
            ['town_name' => 'Rohtak', 'town_abbr' => 'RTK', 'project_name' => 'Suncity Heights', 'project_abbr' => 'SUN', 'floor' => 'Third Floor', 'floor_abbr' => '3F', 'block_tower' => '16', 'block_abbr' => 'T16', 'flat_no' => '301', 'flat_abbr' => '301', 'final_no' => 'RTK-SUN-3F-T16-E-301'],
            ['town_name' => 'Rohtak', 'town_abbr' => 'RTK', 'project_name' => 'Suncity Heights', 'project_abbr' => 'SUN', 'floor' => 'Forth Floor', 'floor_abbr' => '4F', 'block_tower' => '16', 'block_abbr' => 'T16', 'flat_no' => '401', 'flat_abbr' => '401', 'final_no' => 'RTK-SUN-4F-T16-E-401'],
            ['town_name' => 'Rohtak', 'town_abbr' => 'RTK', 'project_name' => 'Suncity Heights', 'project_abbr' => 'SUN', 'floor' => 'Fifth Floor', 'floor_abbr' => '5F', 'block_tower' => '16', 'block_abbr' => 'T16', 'flat_no' => '501', 'flat_abbr' => '501', 'final_no' => 'RTK-SUN-5F-T16-E-501'],
        ];
    }

    /**
     * Panipat (3 records)
     */
    private function getPanipatRecords(): array
    {
        return [
            ['town_name' => 'Panipat', 'town_abbr' => 'PNP', 'project_name' => 'SPLENDOR GRANDE', 'project_abbr' => 'SPLG', 'floor' => 'Fifth Floor', 'floor_abbr' => '5F', 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => '501', 'flat_abbr' => '501', 'final_no' => 'PNP-SPLG-5F-EWS-501'],
            ['town_name' => 'Panipat', 'town_abbr' => 'PNP', 'project_name' => 'SPLENDOR GRANDE', 'project_abbr' => 'SPLG', 'floor' => 'Sixth Floor', 'floor_abbr' => '6F', 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => '601', 'flat_abbr' => '601', 'final_no' => 'PNP-SPLG-6F-EWS-601'],
            ['town_name' => 'Panipat', 'town_abbr' => 'PNP', 'project_name' => 'SPLENDOR GRANDE', 'project_abbr' => 'SPLG', 'floor' => 'Seventh Floor', 'floor_abbr' => '7F', 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => '701', 'flat_abbr' => '701', 'final_no' => 'PNP-SPLG-7F-EWS-701'],
        ];
    }

    /**
     * Rewari (30 records)
     */
    private function getRewariRecords(): array
    {
        $rows = [];

        // 1. The Cubix (9 flats)
        $cubFloors = [
            ['Ground Floor', 'GF', '001', '001', 'RWR-CUB-GF-EWS-001'],
            ['First Floor', '1F', '101', '101', 'RWR-CUB-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'RWR-CUB-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'RWR-CUB-3F-EWS-301'],
            ['Forth Floor', '4F', '401', '401', 'RWR-CUB-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'RWR-CUB-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'RWR-CUB-6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'RWR-CUB-7F-EWS-701'],
            ['Eighth Floor', '8F', '801', '801', 'RWR-CUB-8F-EWS-801'],
        ];
        foreach ($cubFloors as $r) {
            $rows[] = ['town_name' => 'Rewari', 'town_abbr' => 'RWR', 'project_name' => 'The Cubix', 'project_abbr' => 'CUB', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 2. Vipul Gardens (10 flats)
        $vplFloors = [
            ['Ground Floor', 'GF', '001', '001', 'RWR-VPL-GF-EWS-001'],
            ['First Floor', '1F', '101', '101', 'RWR-VPL-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'RWR-VPL-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'RWR-VPL-3F-EWS-301'],
            ['Forth Floor', '4F', '401', '401', 'RWR-VPL-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'RWR-VPL-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'RWR-VPL-6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'RWR-VPL-7F-EWS-701'],
            ['Eighth Floor', '8F', '801', '801', 'RWR-VPL-8F-EWS-801'],
            ['Nineth Floor', '9F', '901', '901', 'RWR-VPL-9F-EWS-901'],
        ];
        foreach ($vplFloors as $r) {
            $rows[] = ['town_name' => 'Rewari', 'town_abbr' => 'RWR', 'project_name' => 'Vipul Gardens', 'project_abbr' => 'VPL', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 3. Amangani (11 flats)
        $amnFloors = [
            ['Ground Floor', 'GF', '001', '001', 'RWR-AMN-GF-EWS-001'],
            ['First Floor', '1F', '101', '101', 'RWR-AMN-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'RWR-AMN-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'RWR-AMN-3F-EWS-301'],
            ['Forth Floor', '4F', '401', '401', 'RWR-AMN-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'RWR-AMN-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'RWR-AMN-6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'RWR-AMN-7F-EWS-701'],
            ['Eighth Floor', '8F', '801', '801', 'RWR-AMN-8F-EWS-801'],
            ['Nineth Floor', '9F', '901', '901', 'RWR-AMN-9F-EWS-901'],
            ['Tenth Floor', '10F', '1001', '1001', 'RWR-AMN-10F-EWS-1001'],
        ];
        foreach ($amnFloors as $r) {
            $rows[] = ['town_name' => 'Rewari', 'town_abbr' => 'RWR', 'project_name' => 'Amangani', 'project_abbr' => 'AMN', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        return $rows;
    }

    /**
     * Sonipat (30 records)
     */
    private function getSonipatRecords(): array
    {
        $rows = [];

        // 1. Parker Infra Private Ltd. (7 flats)
        $pipd = [
            ['Ground Floor', 'GF', '01', '01', 'SNP-PIPD-GF-01'],
            ['First Floor', '1F', '101', '101', 'SNP-PIPD-1F-101'],
            ['Second Floor', '2F', '201', '201', 'SNP-PIPD-2F-201'],
            ['Third Floor', '3F', '301', '301', 'SNP-PIPD-3F-301'],
            ['Fourth Floor', '4F', '401', '401', 'SNP-PIPD-4F-401'],
            ['Fifth floor', '5F', '501', '501', 'SNP-PIPD-5F-501'],
            ['Sixth Floor', '6F', '601', '601', 'SNP-PIPD-6F-601'],
        ];
        foreach ($pipd as $r) {
            $rows[] = ['town_name' => 'Sonipat', 'town_abbr' => 'SNP', 'project_name' => 'Parker Infra Private Ltd.', 'project_abbr' => 'PIPD', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'A', 'block_abbr' => 'BA', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 2. Aakarshak Relators Pvt. Ltd. (8 flats)
        $arpl = [
            ['Ground Floor', 'GF', '01', '01', 'SNP-ARPL-GF-01'],
            ['First Floor', '1F', '101', '101', 'SNP- ARPL -1F-101'],
            ['Second Floor', '2F', '201', '201', 'SNP- ARPL -2F-201'],
            ['Third Floor', '3F', '301', '301', 'SNP- ARPL -3F-301'],
            ['Fourth Floor', '4F', '401', '401', 'SNP- ARPL -4F-401'],
            ['Fifth Floor', '5F', '501', '501', 'SNP- ARPL -5F-501'],
            ['Sixth Floor', '6F', '601', '601', 'SNP- ARPL -6F-601'],
            ['Seventh Floor', '7F', '701', '701', 'SNP- ARPL -7F-701'],
        ];
        foreach ($arpl as $r) {
            $rows[] = ['town_name' => 'Sonipat', 'town_abbr' => 'SNP', 'project_name' => 'Aakarshak Relators Pvt. Ltd.', 'project_abbr' => 'ARPL', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'A', 'block_abbr' => 'BA', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 3. Pardesi Developers Pvt. Ltd. (5 flats)
        $pdpl = [
            ['Ground Floor', 'GF', '01', '01', 'SNP-PDPL-GF-01'],
            ['First Floor', '1F', '101', '101', 'SNP- PDPL -1F-101'],
            ['Second Floor', '2F', '201', '201', 'SNP- PDPL -2F-201'],
            ['Third Floor', '3F', '301', '301', 'SNP- PDPL -3F-301'],
            ['Fourth Floor', '4F', '401', '401', 'SNP- PDPL -4F-401'],
        ];
        foreach ($pdpl as $r) {
            $rows[] = ['town_name' => 'Sonipat', 'town_abbr' => 'SNP', 'project_name' => 'Pardesi Developers Pvt. Ltd.', 'project_abbr' => 'PDPL', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'A', 'block_abbr' => 'BA', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 4. Indian Railway Welfare Organization (5 flats)
        $irwo = [
            ['Ground Floor', 'GF', '01', '01', 'SNP-IRWO-GF-BN-01'],
            ['First Floor', '1F', '101', '101', 'SNP- IRWO -1F-BN-101'],
            ['Second Floor', '2F', '201', '201', 'SNP- IRWO -2F-BN-201'],
            ['Third Floor', '3F', '301', '301', 'SNP- IRWO -3F-BN-301'],
            ['Fourth Floor', '4F', '401', '401', 'SNP- IRWO -4F-BN-401'],
        ];
        foreach ($irwo as $r) {
            $rows[] = ['town_name' => 'Sonipat', 'town_abbr' => 'SNP', 'project_name' => 'Indian Railway Welfare Organization', 'project_abbr' => 'IRWO', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'A', 'block_abbr' => 'BA', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 5. JBB Everest Buildtech Pvt. Ltd. (5 flats)
        $jebpl = [
            ['Ground Floor', 'GF', '01', '01', 'SNP-JEBPL-GF-BA-01'],
            ['First Floor', '1F', '101', '101', 'SNP- JEBPL -1F-BA-101'],
            ['Second Floor', '2F', '201', '201', 'SNP- JEBPL -2F-BA-201'],
            ['Third Floor', '3F', '301', '301', 'SNP- JEBPL -3F-BA-301'],
            ['Fourth Floor', '4F', '401', '401', 'SNP- JEBPL -4F-BA-401'],
        ];
        foreach ($jebpl as $r) {
            $rows[] = ['town_name' => 'Sonipat', 'town_abbr' => 'SNP', 'project_name' => 'JBB Everest Buildtech Pvt. Ltd.', 'project_abbr' => 'JEBPL', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'A', 'block_abbr' => 'BA', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        return $rows;
    }

    /**
     * Faridabad (113 records)
     */
    private function getFaridabadRecords(): array
    {
        $rows = [];

        // 1. Royal Heritage (RH) - Blocks A, B, C
        foreach (['A' => 'BA', 'B' => 'BB', 'C' => 'BC'] as $blk => $abbr) {
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'Royal Heritage', 'project_abbr' => 'RH', 'floor' => 'Ground Floor', 'floor_abbr' => 'GF', 'block_tower' => $blk, 'block_abbr' => $abbr, 'flat_no' => 'G01', 'flat_abbr' => 'G01', 'final_no' => "FBD-RH-GF-{$abbr}-G01"];
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'Royal Heritage', 'project_abbr' => 'RH', 'floor' => 'First Floor', 'floor_abbr' => '1F', 'block_tower' => $blk, 'block_abbr' => $abbr, 'flat_no' => '101', 'flat_abbr' => '101', 'final_no' => "FBD-RH-1F-{$abbr}-101"];
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'Royal Heritage', 'project_abbr' => 'RH', 'floor' => 'Second Floor', 'floor_abbr' => '2F', 'block_tower' => $blk, 'block_abbr' => $abbr, 'flat_no' => '201', 'flat_abbr' => '201', 'final_no' => "FBD-RH-2F-{$abbr}-201"];
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'Royal Heritage', 'project_abbr' => 'RH', 'floor' => 'Third Floor', 'floor_abbr' => '3F', 'block_tower' => $blk, 'block_abbr' => $abbr, 'flat_no' => '301', 'flat_abbr' => '301', 'final_no' => "FBD-RH-3F-{$abbr}-301"];
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'Royal Heritage', 'project_abbr' => 'RH', 'floor' => 'Forth Floor', 'floor_abbr' => '4F', 'block_tower' => $blk, 'block_abbr' => $abbr, 'flat_no' => '401', 'flat_abbr' => '401', 'final_no' => "FBD-RH-4F-{$abbr}-401"];
        }

        // 2. Mulberry County (MC)
        $mc = [
            ['Ground Floor', 'GF', '001', '001', 'FBD-MC-GF-EWS-2-001'],
            ['First Floor', '1F', '101', '101', 'FBD-MC-1F-EWS-2-101'],
            ['Second Floor', '2F', '102', '102', 'FBD-MC-2F-EWS-2-201'],
            ['Third Floor', '3F', '103', '103', 'FBD-MC-3F-EWS-2-301'],
            ['Forth Floor', '4F', '104', '104', 'FBD-MC-4F-EWS-2-401'],
            ['Fifth Floor', '5F', '105', '105', 'FBD-MC-5F-EWS-2-501'],
            ['Sixth Floor', '6F', '106', '106', 'FBD-MC-6F-EWS-2-601'],
            ['Seventh Floor', '7F', '107', '107', 'FBD-MC-7F-EWS-2-701'],
        ];
        foreach ($mc as $r) {
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'Mulberry County', 'project_abbr' => 'MC', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS-2', 'block_abbr' => 'EWS-2', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 3. Edenwood (EWD)
        $ewd = [
            ['Ground Floor', 'GF', '001', '001', 'FBD-EWD-GF-EWS-001'],
            ['First Floor', '1F', '101', '101', 'FBD-EWD-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'FBD-EWD-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'FBD-EWD-3F-EWS-301'],
        ];
        foreach ($ewd as $r) {
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'Edenwood', 'project_abbr' => 'EWD', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 4. RPS Auria (AUR)
        $aurBlocks = [
            'A-01' => [['Ground Floor', 'GF', '001', '001', 'FBD-AUR-GF-A-01-001'], ['First Floor', '1F', '101', '101', 'FBD-AUR-1F-A-01-101'], ['Second Floor', '2F', '201', '201', 'FBD-AUR-2F-A-01-201'], ['Third Floor', '3F', '301', '301', 'FBD-AUR-3F-A-01-301']],
            'A-02' => [['Ground Floor', 'GF', '001', '001', 'FBD-AUR-GF-A-02-001'], ['First Floor', '1F', '101', '101', 'FBD-AUR-1F-A-02-101'], ['Second Floor', '2F', '201', '201', 'FBD-AUR-2F-A-02-201'], ['Third Floor', '3F', '301', '301', 'FBD-AUR-3F-A-02-301']],
            'B-01' => [['Ground Floor', 'GF', '001', '001', 'FBD-AUR-GF-B-01-001'], ['First Floor', '1F', '101', '101', 'FBD-AUR-1F-B-01-101'], ['Second Floor', '2F', '201', '201', 'FBD-AUR-2F-B-01-201'], ['Third Floor', '3F', '301', '301', 'FBD-AUR-3F-B-01-301']],
            'B-02' => [['Ground Floor', 'GF', '001', '001', 'FBD-AUR-GF-B-02-001'], ['First Floor', '1F', '101', '101', 'FBD-AUR-1F-B-02-101'], ['Second Floor', '2F', '201', '201', 'FBD-AUR-2F-B-02-201']],
            'B-03' => [['Ground Floor', 'GF', '001', '001', 'FBD-AUR-GF-B-03-001'], ['First Floor', '1F', '101', '101', 'FBD-AUR-1F-B-03-101'], ['Second Floor', '2F', '201', '201', 'FBD-AUR-2F-B-03-201'], ['Third Floor', '3F', '301', '301', 'FBD-AUR-3F-B-03-301']],
            'C-01' => [['Ground Floor', 'GF', '001', '001', 'FBD-AUR-GF-C-01-001'], ['First Floor', '1F', '101', '101', 'FBD-AUR-1F-C-01-101'], ['Second Floor', '2F', '201', '201', 'FBD-AUR-2F-C-01-201'], ['Third Floor', '3F', '301', '301', 'FBD-AUR-3F-C-01-301']],
        ];
        foreach ($aurBlocks as $blk => $rowsArr) {
            foreach ($rowsArr as $r) {
                $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'RPS Auria', 'project_abbr' => 'AUR', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => $blk, 'block_abbr' => $blk, 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
            }
        }

        // 5. RPS Savana (SAV)
        $sav = [
            ['ET-01', 'ET-01', 'Ground Floor', 'GF', '001', '001', 'FBD-SAV-GF-ET-01-001'],
            ['ET-02', 'ET-02', 'Ground Floor', 'GF', '001', '001', 'FBD-SAV-GF-ET-02-001'],
            ['ET-02', 'ET-02', 'Second Floor', '2F', '201', '201', 'FBD-SAV-2F-ET-02-201'],
            ['ET-03', 'ET-03', 'Second Floor', '2F', '201', '201', 'FBD-SAV-2F-ET-03-201'],
            ['ET-05', 'ET-05', 'Ground Floor', 'GF', '001', '001', 'FBD-SAV-GF-ET-05-001'],
            ['ET-05', 'ET-05', 'First Floor', '1F', '101', '101', 'FBD-SAV-1F-ET-05-101'],
            ['ET-06', 'ET-06', 'Ground Floor', 'GF', '001', '001', 'FBD-SAV-GF-ET-06-101'],
            ['ET-07', 'ET-07', 'Ground Floor', 'GF', '001', '001', 'FBD-SAV-GF-ET-07-001'],
            ['ET-07', 'ET-07', 'Third Floor', '3F', '301', '301', 'FBD-SAV-3F-ET-07-301'],
            ['ET-08', 'ET-08', 'Ground Floor', 'GF', '001', '001', 'FBD-SAV-GF-ET-08-001'],
            ['ET-08', 'ET-08', 'Second Floor', '2F', '201', '201', 'FBD-SAV-2F-ET-08-201'],
            ['ET-08', 'ET-08', 'Third Floor', '3F', '301', '301', 'FBD-SAV-3F-ET-08-301'],
            ['ET-09', 'ET-09', 'Second Floor', '2F', '201', '201', 'FBD-SAV-2F-ET-09-201'],
            ['ET-10', 'ET-10', 'First Floor', '1F', '101', '101', 'FBD-SAV-1F-ET-10-101'],
            ['ET-10', 'ET-10', 'Second Floor', '2F', '201', '201', 'FBD-SAV-2F-ET-10-201'],
            ['ET-12', 'ET-12', 'Ground Floor', 'GF', '001', '001', 'FBD-SAV-GF-ET-12-001'],
            ['ET-12A', 'ET-12A', 'First Floor', '1F', '101', '101', 'FBD-SAV-1F-ET-12A-101'],
            ['ET-14', 'ET-14', 'First Floor', '1F', '101', '101', 'FBD-SAV-1F-ET-14-101'],
            ['ET-15', 'ET-15', 'First Floor', '1F', '101', '101', 'FBD-SAV-1F-ET-15-101'],
            ['ET-15', 'ET-15', 'Third Floor', '3F', '301', '301', 'FBD-SAV-3F-ET-15-301'],
            ['ET-16', 'ET-16', 'Ground Floor', 'GF', '001', '001', 'FBD-SAV-GF-ET-16-001'],
            ['ET-16', 'ET-16', 'Second Floor', '2F', '201', '201', 'FBD-SAV-2F-ET-16-201'],
            ['ET-17', 'ET-17', 'First Floor', '1F', '101', '101', 'FBD-SAV-1F-ET-17-101'],
            ['ET-17', 'ET-17', 'Third Floor', '3F', '301', '301', 'FBD-SAV-3F-ET-17-301'],
            ['ET-18', 'ET-18', 'Ground Floor', 'GF', '001', '001', 'FBD-SAV-GF-ET-18-001'],
            ['ET-18', 'ET-18', 'Second Floor', '2F', '201', '201', 'FBD-SAV-2F-ET-18-201'],
            ['ET-20', 'ET-20', 'Ground Floor', 'GF', '001', '001', 'FBD-SAV-GF-ET-20-001'],
            ['ET-20', 'ET-20', 'First Floor', '1F', '101', '101', 'FBD-SAV-1F-ET-20-101'],
            ['ET-20', 'ET-20', 'Third Floor', '3F', '301', '301', 'FBD-SAV-3F-ET-20-301'],
            ['ET-21', 'ET-21', 'First Floor', '1F', '101', '101', 'FBD-SAV-1F-ET-21-101'],
            ['ET-22', 'ET-22', 'Second Floor', '2F', '201', '201', 'FBD-SAV-2F-ET-22-201'],
            ['ET-24', 'ET-24', 'Ground Floor', 'GF', '001', '001', 'FBD-SAV-GF-ET-24-001'],
            ['ET-24', 'ET-24', 'Second Floor', '2F', '201', '201', 'FBD-SAV-2F-ET-24-201'],
            ['ET-25', 'ET-25', 'Third Floor', '3F', '301', '301', 'FBD-SAV-3F-ET-25-301'],
            ['ET-26', 'ET-26', 'First Floor', '1F', '101', '101', 'FBD-SAV-1F-ET-26-101'],
            ['ET-26', 'ET-26', 'Second Floor', '2F', '201', '201', 'FBD-SAV-2F-ET-26-201'],
            ['ET-26', 'ET-26', 'Third Floor', '3F', '301', '301', 'FBD-SAV-3F-ET-26-301'],
        ];
        foreach ($sav as $r) {
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'RPS Savana', 'project_abbr' => 'SAV', 'block_tower' => $r[0], 'block_abbr' => $r[1], 'floor' => $r[2], 'floor_abbr' => $r[3], 'flat_no' => $r[4], 'flat_abbr' => $r[5], 'final_no' => $r[6]];
        }

        // 6. KLJ Group Housing Colony (KLJ)
        $klj = [
            ['Ground Floor', 'GF', '001', '001', 'FBD-KLJ-GF-EWS-001'],
            ['First Floor', '1F', '101', '101', 'FBD-KLJ-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'FBD-KLJ-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'FBD-KLJ-3F-EWS-301'],
            ['Forth Floor', '4F', '401', '401', 'FBD-KLJ-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'FBD-KLJ-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'FBD-KLJ-6F-EWS-601'],
        ];
        foreach ($klj as $r) {
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'KLJ Group Housing Colony', 'project_abbr' => 'KLJ', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 7. Omaxe Heights-86 (OMX)
        $omx = [
            ['Forth Floor', '4F', '401', '401', 'FBD-OMX-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'FBD-OMX-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'FBD-OMX-6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'FBD-OMX-7F-EWS-701'],
            ['Eighth Floor', '8F', '801', '801', 'FBD-OMX-8F-EWS-801'],
        ];
        foreach ($omx as $r) {
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'Omaxe Heights-86', 'project_abbr' => 'OMX', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // SPA Village & New Heights (SPA)
        $spa = [
            ['Seventh Floor', '7F', '701', '701', 'FBD-SPA-7F-EWS-701'],
            ['Ninth Floor', '9F', '901', '901', 'FBD-SPA-9F-EWS-901'],
        ];
        foreach ($spa as $r) {
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'SPA Village & New Heights', 'project_abbr' => 'SPA', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // Hill 43 (HILL)
        $hill = [
            ['Sixth Floor', '6F', '601', '601', 'FBD-HILL-6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'FBD-HILL-7F-EWS-701'],
        ];
        foreach ($hill as $r) {
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'Hill 43', 'project_abbr' => 'HILL', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 8. Emerald Heights (EHGT)
        $ehgt = [
            ['First Floor', '1F', '101', '101', 'FBD-EHGT-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'FBD-EHGT-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'FBD-EHGT-3F-EWS-301'],
            ['Forth Floor', '4F', '401', '401', 'FBD-EHGT-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'FBD-EHGT-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'FBD-EHGT-6F-EWS-601'],
        ];
        foreach ($ehgt as $r) {
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'Emerald Heights', 'project_abbr' => 'EHGT', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 9. The Ozone Park Apartments (OPA)
        $opa = [
            ['Ground Floor', 'GF', '101', '101', 'FBD-OPA-GF-EWS-101'],
            ['First Floor', '1F', '101', '101', 'FBD-OPA-1F-EWS-101'],
            ['Second Floor', '2F', '101', '101', 'FBD-OPA-2F-EWS-101'],
            ['Third Floor', '3F', '101', '101', 'FBD-OPA-3F-EWS-101'],
        ];
        foreach ($opa as $r) {
            $rows[] = ['town_name' => 'Faridabad', 'town_abbr' => 'FBD', 'project_name' => 'The Ozone Park Apartments', 'project_abbr' => 'OPA', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        return $rows;
    }

    /**
     * Gurugram (187 records)
     */
    private function getGurugramRecords(): array
    {
        $rows = [];

        // 1. WINDCHANTS (WCT)
        $wct = [
            ['Ground Floor', 'GF', '01', '01', 'GGM-WCT-GF-EWS-01'],
            ['First Floor', '1F', '101', '101', 'GGM-WCT-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-WCT-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-WCT-3F-EWS-301'],
        ];
        foreach ($wct as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'WINDCHANTS', 'project_abbr' => 'WCT', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 2. Digi Homes (DGH)
        $dgh = [
            ['Ground Floor', 'GF', '01', '01', 'GGM-DGH-GF-EWS-01'],
            ['First Floor', '1F', '101', '101', 'GGM-DGH-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-DGH-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-DGH-3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-DGH-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'GGM-DGH-5F-EWS-501'],
        ];
        foreach ($dgh as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Digi Homes', 'project_abbr' => 'DGH', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 3. Group Housing Colony (Aqua Front Tower) (AFT)
        $aft = [
            ['Ground Floor', 'GF', '001', '001', 'GGM-AFT-GF-EWS-01'],
            ['First Floor', '1F', '101', '101', 'GGM-AFT-1F- EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-AFT-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-AFT-3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-AFT-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'GGM-AFT-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'GGM-AFT-6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'GGM-AFT-7F-EWS-701'],
            ['Eighth Floor', '8F', '801', '801', 'GGM-AFT-8F-EWS-801'],
            ['Ninth Floor', '9F', '901', '901', 'GGM-AFT-9F-EWS-901'],
            ['Tenth Floor', '10F', '1001', '1001', 'GGM-AFT-10F-EWS-1001'],
            ['Eleventh Floor', '11F', '1101', '1101', 'GGM-AFT-11F-EWS-1101'],
        ];
        foreach ($aft as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Group Housing Colony (Aqua Front Tower)', 'project_abbr' => 'AFT', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 4. Joyville Gurugram (JOY)
        $joy = [
            ['Ground Floor', 'GF', '01', '01', 'GGM-JOY-GF-EWS-01'],
            ['First Floor', '1F', '101', '101', 'GGM-JOY-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-JOY-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-JOY-3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-JOY-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'GGM-JOY-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'GGM-JOY-6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'GGM-JOY-7F-EWS-701'],
            ['Eighth Floor', '8F', '801', '801', 'GGM-JOY-8F-EWS-801'],
            ['Ninth Floor', '9F', '901', '901', 'GGM-JOY-9F-EWS-901'],
            ['Tenth Floor', '10F', '1001', '1001', 'GGM-JOY-10F-EWS-1001'],
            ['Eleventh Floor', '11F', '1101', '1101', 'GGM-JOY-11F-EWS-1101'],
            ['Twelfth Floor', '12F', '1201', '1201', 'GGM-JOY-12F-EWS-1201'],
            ['Thirteenth Floor', '13F', '1301', '1301', 'GGM-JOY-13F-EWS-1301'],
        ];
        foreach ($joy as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Joyville Gurugram', 'project_abbr' => 'JOY', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 5. Waterfalls Residency (WR)
        $wr = [
            ['Ground Floor', 'GF', '01', '01', 'GGM-WR-GF-EWS-01'],
            ['First Floor', '1F', '101', '101', 'GGM-WR-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-WR-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-WR-3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-WR-4F-EWS-401'],
        ];
        foreach ($wr as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Waterfalls Residency', 'project_abbr' => 'WR', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 6. Indiabulls Centrum Park (ICP)
        $icp = [
            ['Ground Floor', 'GF', '01', '01', 'GGM-ICP-GF-EWS-01'],
            ['First Floor', '1F', '101', '101', 'GGM-ICP-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-ICP-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-ICP-3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-ICP-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'GGM-ICP-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'GGM-ICP-6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'GGM-ICP-7F-EWS-701'],
            ['Eighth Floor', '8F', '801', '801', 'GGM-ICP-8F-EWS-801'],
            ['Ninth Floor', '9F', '901', '901', 'GGM-ICP-9F-EWS-901'],
            ['Tenth Floor', '10F', '1001', '1001', 'GGM-ICP-10F-EWS-1001'],
            ['Eleventh Floor', '11F', '1101', '1101', 'GGM-ICP-11F-EWS-1101'],
        ];
        foreach ($icp as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Indiabulls Centrum Park', 'project_abbr' => 'ICP', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 7. ENIGMA (ENG)
        $eng = [
            ['Ground Floor', 'GF', '01', '01', 'GGM-ENG-GF-EWS-01'],
            ['First Floor', '1F', '101', '101', 'GGM-ENG-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-ENG-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-ENG-3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-ENG-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'GGM-ENG-5F-EWS-501'],
        ];
        foreach ($eng as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'ENIGMA', 'project_abbr' => 'ENG', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 8. Essel Towers & Platinum Towers (ETP)
        $etp = [
            ['S1', 'TS1', 'Ground Floor', 'GF', 'E-01', 'E-01', 'GGM-ETP-GF-TS1-E-01'],
            ['S1', 'TS1', 'First Floor', '1F', 'E-101', 'E-101', 'GGM-ETP-1F-TS1-E-101'],
            ['S1', 'TS1', 'Second Floor', '2F', 'E-201', 'E-201', 'GGM-ETP-2F-TS1-E-201'],
            ['S1', 'TS1', 'Third Floor', '3F', 'E-301', 'E-301', 'GGM-ETP-3F-TS1-E-301'],
            ['P', 'TP', 'Ground Floor', 'GF', 'E-01', 'E-01', 'GGM-ETP-GF-TP-E-01'],
            ['P', 'TP', 'First Floor', '1F', 'E-101', 'E-101', 'GGM-ETP-1F-TP-E-101'],
            ['P', 'TP', 'Second Floor', '2F', 'E-201', 'E-201', 'GGM-ETP-2F-TP-E-201'],
        ];
        foreach ($etp as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Essel Towers & Platinum Towers', 'project_abbr' => 'ETP', 'block_tower' => $r[0], 'block_abbr' => $r[1], 'floor' => $r[2], 'floor_abbr' => $r[3], 'flat_no' => $r[4], 'flat_abbr' => $r[5], 'final_no' => $r[6]];
        }

        // 9. M3M 65th Avenue (M3MA)
        $m3ma = [
            ['Ground Floor', 'GF', '001', '001', 'GGM-M3MA-GF-EWS-01'],
            ['First Floor', '1F', '101', '101', 'GGM-M3MA-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-M3MA-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-M3MA-3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-M3MA-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'GGM-M3MA-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'GGM-M3MA-6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'GGM-M3MA-7F-EWS-701'],
            ['Eighth Floor', '8F', '801', '801', 'GGM-M3MA-8F-EWS-801'],
            ['Ninth Floor', '9F', '901', '901', 'GGM-M3MA-9F-EWS-901'],
            ['Tenth Floor', '10F', '1001', '1001', 'GGM-M3MA-10F-EWS-1001'],
            ['Eleventh Floor', '11F', '1101', '1101', 'GGM-M3MA-11F-EWS-1101'],
            ['Twelfth Floor', '12F', '1201', '1201', 'GGM-M3MA-12F-EWS-1201'],
            ['Thirteenth Floor', '13F', '1301', '1301', 'GGM-M3MA-13F-EWS-1301'],
            ['Fourteenth Floor', '14F', '1401', '1401', 'GGM-M3MA-14F-EWS-1401'],
            ['Fifteenth Floor', '15F', '1501', '1501', 'GGM-M3MA-15F-EWS-1501'],
            ['Sixteenth Floor', '16F', '1601', '1601', 'GGM-M3MA-16F-EWS-1601'],
            ['Seventeenth Floor', '17F', '1701', '1701', 'GGM-M3MA-17F-EWS-1701'],
        ];
        foreach ($m3ma as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'M3M 65th Avenue', 'project_abbr' => 'M3MA', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 10. M3M Cornerwalk (M3MC)
        $m3mc = [
            ['Ground Floor', 'GF', '001', '001', 'GGM-M3MC-GF-EWS-01'],
            ['First Floor', '1F', '101', '101', 'GGM-M3MC-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-M3MC-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-M3MC-3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-M3MC-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'GGM-M3MC-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'GGM-M3MC-6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'GGM-M3MC-7F-EWS-701'],
        ];
        foreach ($m3mc as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'M3M Cornerwalk', 'project_abbr' => 'M3MC', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 11. M3M Merina Sierra (M3MMS)
        $m3mms = [
            ['Ground Floor', 'GF', '001', '001', 'GGM-M3MMS-GF-EWS-001'],
            ['First Floor', '1F', '101', '101', 'GGM-M3MMS-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-M3MMS-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-M3MMS-3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-M3MMS-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'GGM-M3MMS-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'GGM-M3MMS-6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'GGM-M3MMS-7F-EWS-701'],
            ['Eighth Floor', '8F', '801', '801', 'GGM-M3MMS-8F-EWS-801'],
            ['Ninth Floor', '9F', '901', '901', 'GGM-M3MMS-9F-EWS-901'],
            ['Tenth Floor', '10F', '1001', '1001', 'GGM-M3MMS-10F-EWS-1001'],
            ['Eleventh Floor', '11F', '1101', '1101', 'GGM-M3MMS-11F-EWS-1101'],
            ['Twelfth Floor', '12F', '1201', '1201', 'GGM-M3MMS-12F-EWS-1201'],
            ['Thirteenth Floor', '13F', '1301', '1301', 'GGM-M3MMS-13F-EWS-1301'],
        ];
        foreach ($m3mms as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'M3M Merina Sierra', 'project_abbr' => 'M3MMS', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 12. Mahindra Luminare (ML)
        $ml = [
            ['Ground Floor', 'GF', '01', '01', 'GGM-ML-GF-EWS-01'],
            ['First Floor', '1F', '101', '101', 'GGM-ML-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-ML-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-ML-3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-ML-4F-EWS-401'],
        ];
        foreach ($ml as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Mahindra Luminare', 'project_abbr' => 'ML', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 13. Orchid Petals (OP)
        $op = [
            ['Ground Floor', 'GF', '1', '1', 'GGM-OP-GF-EWS-1'],
            ['First Floor', '1F', '101', '101', 'GGM-OP-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-OP-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-OP-3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-OP-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'GGM-OP-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'GGM-OP-6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'GGM-OP-7F-EWS-701'],
        ];
        foreach ($op as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Orchid Petals', 'project_abbr' => 'OP', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 14. Elevate (ELV)
        $elv = [
            ['Ground Floor', 'GF', 'SH-01', 'SH-01', 'GGM-ELV-GF-EWS-SH-01'],
            ['First Floor', '1F', 'SH-101', 'SH-101', 'GGM-ELV-1F-EWS-SH-101'],
            ['Second Floor', '2F', 'SH-201', 'SH- 201', 'GGM-ELV-2F-EWS-SH-201'],
            ['Third Floor', '3F', 'SH-301', 'SH-301', 'GGM-ELV-3F-EWS-SH-301'],
            ['Fourth Floor', '4F', 'SH-401', 'SH-401', 'GGM-ELV-4F-EWS-SH-401'],
            ['Fifth Floor', '5F', 'SH-501', 'SH-501', 'GGM-ELV-5F-EWS-SH-501'],
            ['Sixth Floor', '6F', 'Sh-601', 'Sh-601', 'GGM-ELV-6F-EWS-SH-601'],
        ];
        foreach ($elv as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Elevate', 'project_abbr' => 'ELV', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 15. Maceo (MAC)
        $mac = [
            ['A', 'BA', 'Ground Floor', 'GF', 'E-A001', 'E-A001', 'GGM-MAC-GF-BA-E-A001'],
            ['A', 'BA', 'First Floor', '1F', 'E-A101', 'E-A101', 'GGM-MAC-1F-BA-E-A101'],
            ['A', 'BA', 'Second Floor', '2F', 'E-A201', 'E-A201', 'GGM-MAC-2F-BA-E-A201'],
            ['A', 'BA', 'Third Floor', '3F', 'E-A301', 'E-A301', 'GGM-MAC-3F-BA-E-A301'],
            ['B', 'BB', 'Ground Floor', 'GF', 'E-B001', 'E-B001', 'GGM-MAC-GF-BB-E-B001'],
            ['B', 'BB', 'First Floor', '1F', 'E-B101', 'E-B101', 'GGM-MAC-1F-BB-E-B101'],
            ['B', 'BB', 'Second Floor', '2F', 'E-B201', 'E-B201', 'GGM-MAC-2F-BB-E-B201'],
            ['B', 'BB', 'Third Floor', '3F', 'E-B301', 'E-B301', 'GGM-MAC-3F-BB-E-B301'],
            ['B', 'BB', 'Fourth Floor', '4F', 'E-B401', 'E-B401', 'GGM-MAC-4F-BB-E-B401'],
        ];
        foreach ($mac as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Maceo', 'project_abbr' => 'MAC', 'block_tower' => $r[0], 'block_abbr' => $r[1], 'floor' => $r[2], 'floor_abbr' => $r[3], 'flat_no' => $r[4], 'flat_abbr' => $r[5], 'final_no' => $r[6]];
        }

        // 16. Ashiana Mulbeery (AM)
        $am = [
            ['Ground Floor', 'GF', '01', '01', 'GGM-AM-GF-EWS-01'],
            ['First Floor', '1F', '101', '101', 'GGM-AM-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-AM-2F-EWS-201'],
        ];
        foreach ($am as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Ashiana Mulbeery', 'project_abbr' => 'AM', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 17. Godrej Air (GA)
        $ga = [
            ['Ground Floor', 'GF', '001', '001', 'GGM-GA- GF-EWS-001'],
            ['First Floor', '1F', '101', '101', 'GGM-GA- 1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-GA- 2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-GA- 3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-GA- 4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'GGM-GA- 5F-EWS-501'],
        ];
        foreach ($ga as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Godrej Air', 'project_abbr' => 'GA', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 18. Godrej Area & 101 (GA101)
        $ga101 = [
            ['Ground Floor', 'GF', '001', '001', 'GGM-GA101- GF-EWS-001'],
            ['First Floor', '1F', '101', '101', 'GGM-GA101- 1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-GA101- 2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-GA101- 3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-GA101- 4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'GGM-GA101- 5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'GGM-GA101- 6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'GGM-GA101- 7F-EWS-701'],
            ['Eighth Floor', '8F', '801', '801', 'GGM-GA101- 8F-EWS-801'],
        ];
        foreach ($ga101 as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Godrej Area & 101', 'project_abbr' => 'GA101', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 19. Godrej Meridien (GM)
        $gmFloors = ['Ground Floor' => 'GF', 'First Floor' => '1F', 'Second Floor' => '2F', 'Third Floor' => '3F', 'Fourth Floor' => '4F', 'Fifth Floor' => '5F', 'Sixth Floor' => '6F', 'Seventh Floor' => '7F'];
        foreach ($gmFloors as $flr => $abbr) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Godrej Meridien', 'project_abbr' => 'GM', 'floor' => $flr, 'floor_abbr' => $abbr, 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => '01', 'flat_abbr' => '01', 'final_no' => "GGM-GM- {$abbr}-EWS-01"];
        }

        // 20. Sobha City (SC)
        $sc = [
            ['Ground Floor', 'GF', 'E-0201', 'E-0201', 'GGM-SC-GF-EWS-02-E-0201'],
            ['First Floor', '1F', 'E-0214', 'E-0214', 'GGM-SC-1F-EWS-02-E-0214'],
            ['Second Floor', '2F', 'E-0228', 'E-0228', 'GGM-SC-2F-EWS-02-E-0228'],
            ['Third Floor', '3F', 'E-0242', 'E-0242', 'GGM-SC-3F-EWS-02-E-0242'],
            ['Fourth Floor', '4F', 'E-0256', 'E-0256', 'GGM-SC-4F-EWS-02-E-0256'],
            ['Fifth Floor', '5F', 'E-0270', 'E-0270', 'GGM-SC-5F-EWS-02-E-0270'],
            ['Sixth Floor', '6F', 'E-0284', 'E-0284', 'GGM-SC-6F-EWS-02-E-0284'],
        ];
        foreach ($sc as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Sobha City', 'project_abbr' => 'SC', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS-02', 'block_abbr' => 'EWS-02', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 21. Ultima (ULT)
        $ult = [
            ['Ground Floor', 'GF', '01', '01', 'GGM-ULT-GF-EWS-01'],
            ['First Floor', '1F', '101', '101', 'GGM-ULT-1F-EWS-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-ULT-2F-EWS-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-ULT-3F-EWS-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-ULT-4F-EWS-401'],
            ['Fifth Floor', '5F', '501', '501', 'GGM-ULT-5F-EWS-501'],
            ['Sixth Floor', '6F', '601', '601', 'GGM-ULT-6F-EWS-601'],
            ['Seventh Floor', '7F', '701', '701', 'GGM-ULT-7F-EWS-701'],
        ];
        foreach ($ult as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Ultima', 'project_abbr' => 'ULT', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS', 'block_abbr' => 'EWS', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 22. MICASA (MIC)
        $mic = [
            ['First Floor', '1F', '101', '101', 'GGM-MIC-1F-EWS T-VIII-101'],
            ['Second Floor', '2F', '201', '201', 'GGM-MIC-2F-EWS T-VIII-201'],
            ['Third Floor', '3F', '301', '301', 'GGM-MIC-3F-EWS T-VIII-301'],
            ['Fourth Floor', '4F', '401', '401', 'GGM-MIC-4F-EWS T-VIII-401'],
            ['Fifth Floor', '5F', '501', '501', 'GGM-MIC-5F-EWS T-VIII-501'],
            ['Sixth Floor', '6F', '601', '601', 'GGM-MIC-6F-EWS T-VIII-601'],
        ];
        foreach ($mic as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'MICASA', 'project_abbr' => 'MIC', 'floor' => $r[0], 'floor_abbr' => $r[1], 'block_tower' => 'EWS T-VIII', 'block_abbr' => 'EWS T-VIII', 'flat_no' => $r[2], 'flat_abbr' => $r[3], 'final_no' => $r[4]];
        }

        // 23. Coban (COB)
        $cob = [
            ['I', 'BI', 'Ground Floor', 'GF', 'G-001', 'G-001', 'GGM-COB-GF-BI-G-001'],
            ['I', 'BI', 'First Floor', '1F', '101', '101', 'GGM-COB-1F-BI-G-101'],
            ['I', 'BI', 'Second Floor', '2F', '201', '201', 'GGM-COB-2F-BI-G-201'],
            ['I', 'BI', 'Third Floor', '3F', '301', '301', 'GGM-COB-3F-BI-G-301'],
            ['II', 'BII', 'Ground Floor', 'GF', 'G-001', 'G-001', 'GGM-COB-GF-BII-G-001'],
        ];
        foreach ($cob as $r) {
            $rows[] = ['town_name' => 'Gurugram', 'town_abbr' => 'GGM', 'project_name' => 'Coban', 'project_abbr' => 'COB', 'block_tower' => $r[0], 'block_abbr' => $r[1], 'floor' => $r[2], 'floor_abbr' => $r[3], 'flat_no' => $r[4], 'flat_abbr' => $r[5], 'final_no' => $r[6]];
        }

        return $rows;
    }
}
