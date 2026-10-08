<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EwsProject;
use App\Models\EwsBlock;
use App\Models\EwsTown;

class EwsDistrictProjectsSeeder extends Seeder
{
    /**
     * Seeds projects and blocks mapped by District from the 6 official PDF files:
     * - Rohtak
     * - Panipat
     * - Rewari
     * - Sonipat
     * - Faridabad
     * - Gurugram
     */
    public function run(): void
    {
        $this->command->info("Starting EwsDistrictProjectsSeeder...");

        $data = [
            'ROHTAK' => [
                'JOP Palms' => ['blocks' => ['EWS']],
                'Suncity Heights' => ['blocks' => ['16']],
            ],
            'PANIPAT' => [
                'SPLENDOR GRANDE' => ['blocks' => ['EWS']],
            ],
            'REWARI' => [
                'The Cubix' => ['blocks' => ['EWS']],
                'Vipul Gardens' => ['blocks' => ['EWS']],
                'Amangani' => ['blocks' => ['EWS']],
            ],
            'SONIPAT' => [
                'Parker Infra Private Ltd.' => ['blocks' => ['A']],
                'Aakarshak Relators Pvt. Ltd.' => ['blocks' => ['A']],
                'Pardesi Developers Pvt. Ltd.' => ['blocks' => ['A']],
                'Indian Railway Welfare Organization' => ['blocks' => ['A']],
                'JBB Everest Buildtech Pvt. Ltd.' => ['blocks' => ['A']],
            ],
            'FARIDABAD' => [
                'Royal Heritage' => ['blocks' => ['A', 'B', 'C']],
                'Mulberry County' => ['blocks' => ['EWS-2']],
                'Edenwood' => ['blocks' => ['EWS']],
                'RPS Auria' => ['blocks' => ['A-01', 'A-02', 'B-01', 'B-02', 'B-03', 'C-01']],
                'RPS Savana' => ['blocks' => [
                    'ET-01', 'ET-02', 'ET-03', 'ET-05', 'ET-06', 'ET-07', 'ET-08', 'ET-09',
                    'ET-10', 'ET-12', 'ET-12A', 'ET-14', 'ET-15', 'ET-16', 'ET-17', 'ET-18',
                    'ET-20', 'ET-21', 'ET-22', 'ET-24', 'ET-25', 'ET-26'
                ]],
                'KLJ Group Housing Colony' => ['blocks' => ['EWS']],
                'Omaxe Heights-86' => ['blocks' => ['EWS']],
                'SPA Village & New Heights' => ['blocks' => ['EWS']],
                'Hill 43' => ['blocks' => ['EWS']],
                'Emerald Heights' => ['blocks' => ['EWS']],
                'The Ozone Park Apartments' => ['blocks' => ['EWS']],
            ],
            'GURUGRAM' => [
                'WINDCHANTS' => ['blocks' => ['EWS']],
                'Digi Homes' => ['blocks' => ['EWS']],
                'Group Housing Colony (Aqua Front Tower)' => ['blocks' => ['EWS']],
                'Joyville Gurugram' => ['blocks' => ['EWS']],
                'Waterfalls Residency' => ['blocks' => ['EWS']],
                'Indiabulls Centrum Park' => ['blocks' => ['EWS']],
                'ENIGMA' => ['blocks' => ['EWS']],
                'Essel Towers & Platinum Towers' => ['blocks' => ['S1', 'P']],
                'M3M 65th Avenue' => ['blocks' => ['EWS']],
                'M3M Cornerwalk' => ['blocks' => ['EWS']],
                'M3M Merina Sierra' => ['blocks' => ['EWS']],
                'Mahindra Luminare' => ['blocks' => ['EWS']],
                'Orchid Petals' => ['blocks' => ['EWS']],
                'Elevate' => ['blocks' => ['EWS']],
                'Maceo' => ['blocks' => ['A', 'B']],
                'Ashiana Mulbeery' => ['blocks' => ['EWS']],
                'Godrej Air' => ['blocks' => ['EWS']],
                'Godrej Area & 101' => ['blocks' => ['EWS']],
                'Godrej Meridien' => ['blocks' => ['EWS']],
                'Sobha City' => ['blocks' => ['EWS-02']],
                'Ultima' => ['blocks' => ['EWS']],
                'MICASA' => ['blocks' => ['EWS T-VIII']],
                'Coban' => ['blocks' => ['I', 'II']],
            ],
        ];

        $totalProjects = 0;
        $totalBlocks = 0;

        foreach ($data as $distName => $projects) {
            $district = DB::table('ews_districts')
                ->where(DB::raw('UPPER(name)'), $distName)
                ->first();

            if (!$district) {
                $this->command->warn("District '{$distName}' not found in ews_districts table!");
                continue;
            }

            // Resolve Zone for this district
            $zone = null;
            if (!empty($district->zone_id)) {
                $zone = DB::table('ews_stp_districts')->where('id', $district->zone_id)->first();
            }
            $zoneId = $zone ? $zone->id : $district->zone_id;
            $zoneName = $zone ? (str_contains(strtoupper($zone->name), 'ZONE') ? strtoupper($zone->name) : strtoupper($zone->name) . ' ZONE') : ($district->name . ' ZONE');

            // Find primary headquarters town for this district
            $town = EwsTown::where('district_id', $district->id)
                ->where(DB::raw('UPPER(name)'), $distName)
                ->first();
            if (!$town) {
                $town = EwsTown::where('district_id', $district->id)->first();
            }

            $townId = $town ? $town->id : null;
            $townName = $town ? $town->name : null;

            foreach ($projects as $projectName => $projData) {
                $cleanProjectName = trim($projectName);
                $projectAbbr = DB::table('ews_flat_abbreviations')
                    ->where('dist_id', $district->id)
                    ->where('project_name', $cleanProjectName)
                    ->value('project_abbr');
                if (!$projectAbbr) {
                    $projectAbbr = DB::table('ews_flat_abbreviations')
                        ->where('project_name', $cleanProjectName)
                        ->value('project_abbr');
                }

                $project = EwsProject::updateOrCreate(
                    [
                        'district_id' => $district->id,
                        'name' => $cleanProjectName,
                    ],
                    [
                        'zone_id' => $zoneId,
                        'zone_name' => $zoneName,
                        'district_id' => $district->id,
                        'district_name' => $district->name,
                        'town_id' => $townId,
                        'town_name' => $townName,
                        'name' => $cleanProjectName,
                        'project_abbr' => $projectAbbr,
                    ]
                );
                $totalProjects++;

                // Seed Blocks for this project
                foreach ($projData['blocks'] as $blockName) {
                    $cleanBlockName = trim($blockName);
                    EwsBlock::updateOrCreate(
                        [
                            'project_id' => $project->id,
                            'name' => $cleanBlockName,
                        ],
                        [
                            'zone_id'       => $zoneId,
                            'zone_name'     => $zoneName,
                            'district_id'   => $district->id,
                            'district_name' => $district->name,
                            'town_id'       => $townId,
                            'town_name'     => $townName,
                            'project_id'    => $project->id,
                            'project_name'  => $cleanProjectName,
                            'name'          => $cleanBlockName,
                        ]
                    );
                    $totalBlocks++;
                }
            }
        }

        // Global sync for any blocks with missing hierarchy
        $remainingBlocks = EwsBlock::whereNull('district_id')
            ->orWhereNull('zone_id')
            ->orWhereNull('town_id')
            ->orWhereNull('project_name')
            ->get();

        foreach ($remainingBlocks as $b) {
            $proj = EwsProject::find($b->project_id);
            if ($proj) {
                $b->update([
                    'zone_id'       => $b->zone_id ?? $proj->zone_id,
                    'zone_name'     => $b->zone_name ?? $proj->zone_name,
                    'district_id'   => $b->district_id ?? $proj->district_id,
                    'district_name' => $b->district_name ?? $proj->district_name,
                    'town_id'       => $b->town_id ?? $proj->town_id,
                    'town_name'     => $b->town_name ?? $proj->town_name,
                    'project_name'  => $b->project_name ?? $proj->name,
                ]);
            }
        }

        $this->command->info("EwsDistrictProjectsSeeder finished: Successfully seeded {$totalProjects} projects and {$totalBlocks} blocks across 6 districts.");
    }
}
