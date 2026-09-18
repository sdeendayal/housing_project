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

            $project = EwsProject::firstOrCreate(
                [
                    'district_id' => $distId,
                    'name'        => $projectName,
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
                EwsBlock::firstOrCreate(
                    [
                        'project_id' => $project->id,
                        'name'       => $blockName,
                    ]
                );
                $blockCount++;
            }
        }

        $this->command->info("Seeding completed! Seeded {$projectCount} projects and {$blockCount} blocks into master tables.");
    }
}
