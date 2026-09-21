<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EwsProject;
use App\Models\EwsBlock;

class EwsProjectsAndBlocksSeeder extends Seeder
{
    /**
     * Seed ews_projects and ews_blocks master tables from ews_flat_abbreviations.
     */
    public function run(): void
    {
        $this->command->info("Starting EwsProjectsAndBlocksSeeder from ews_flat_abbreviations...");

        // Clean any test placeholder data if present
        DB::table('ews_projects')->where('name', 'like', 'test%')->orWhere('name', 'like', 'Test%')->delete();

        // 1. Get unique projects per district
        $projects = DB::table('ews_flat_abbreviations')
            ->select('dist_id', 'project_name')
            ->distinct()
            ->whereNotNull('dist_id')
            ->whereNotNull('project_name')
            ->get();

        $projectCount = 0;
        $blockCount = 0;

        foreach ($projects as $p) {
            $projectName = trim($p->project_name);
            $distId = (int)$p->dist_id;

            $district = DB::table('ews_districts')->where('id', $distId)->first();
            $zone = null;
            if ($district && !empty($district->zone_id)) {
                $zone = DB::table('ews_stp_districts')->where('id', $district->zone_id)->first();
            }
            $zoneId = $zone ? $zone->id : ($district->zone_id ?? null);
            $zoneName = $zone ? (str_contains(strtoupper($zone->name), 'ZONE') ? strtoupper($zone->name) : strtoupper($zone->name) . ' ZONE') : ($district ? $district->name . ' ZONE' : null);

            // Primary town for this district
            $town = null;
            if ($district) {
                $town = DB::table('ews_towns')
                    ->where('district_id', $distId)
                    ->where(DB::raw('UPPER(name)'), strtoupper($district->name))
                    ->first();
                if (!$town) {
                    $town = DB::table('ews_towns')->where('district_id', $distId)->first();
                }
            }
            $townId = $town ? $town->id : null;
            $townName = $town ? $town->name : null;

            $project = EwsProject::updateOrCreate(
                [
                    'district_id' => $distId,
                    'name'        => $projectName,
                ],
                [
                    'zone_id'       => $zoneId,
                    'zone_name'     => $zoneName,
                    'district_id'   => $distId,
                    'district_name' => $district ? $district->name : null,
                    'town_id'       => $townId,
                    'town_name'     => $townName,
                    'name'          => $projectName,
                ]
            );
            $projectCount++;

            // 2. For this project and district, get all unique blocks
            $blocks = DB::table('ews_flat_abbreviations')
                ->where('dist_id', $distId)
                ->where('project_name', $projectName)
                ->select('block_tower')
                ->distinct()
                ->whereNotNull('block_tower')
                ->pluck('block_tower');

            foreach ($blocks as $bName) {
                $blockName = trim($bName);
                EwsBlock::updateOrCreate(
                    [
                        'project_id' => $project->id,
                        'name'       => $blockName,
                    ],
                    [
                        'zone_id'       => $project->zone_id ?? $zoneId,
                        'zone_name'     => $project->zone_name ?? $zoneName,
                        'district_id'   => $project->district_id ?? $distId,
                        'district_name' => $project->district_name ?? ($district ? $district->name : null),
                        'town_id'       => $project->town_id ?? $townId,
                        'town_name'     => $project->town_name ?? $townName,
                        'project_id'    => $project->id,
                        'project_name'  => $project->name,
                        'name'          => $blockName,
                    ]
                );
                $blockCount++;
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

        $this->command->info("Seeding completed! Seeded {$projectCount} projects and {$blockCount} blocks into master tables.");
    }
}
