<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ews_blocks', function (Blueprint $table) {
            if (!Schema::hasColumn('ews_blocks', 'zone_id')) {
                $table->unsignedBigInteger('zone_id')->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('ews_blocks', 'zone_name')) {
                $table->string('zone_name')->nullable()->after('zone_id');
            }
            if (!Schema::hasColumn('ews_blocks', 'district_id')) {
                $table->unsignedBigInteger('district_id')->nullable()->after('zone_name')->index();
            }
            if (!Schema::hasColumn('ews_blocks', 'district_name')) {
                $table->string('district_name')->nullable()->after('district_id');
            }
            if (!Schema::hasColumn('ews_blocks', 'town_id')) {
                $table->unsignedBigInteger('town_id')->nullable()->after('district_name')->index();
            }
            if (!Schema::hasColumn('ews_blocks', 'town_name')) {
                $table->string('town_name')->nullable()->after('town_id');
            }
            if (!Schema::hasColumn('ews_blocks', 'project_name')) {
                $table->string('project_name')->nullable()->after('project_id');
            }
        });

        // Master Data Backfill: Populate hierarchy for all existing blocks from ews_projects & ews_builder_flats
        $blocks = DB::table('ews_blocks')->get();
        foreach ($blocks as $blk) {
            $project = DB::table('ews_projects')->where('id', $blk->project_id)->first();
            $flat = DB::table('ews_builder_flats')
                ->where('block_id', $blk->id)
                ->orWhere(function ($q) use ($blk) {
                    $q->where('project_id', $blk->project_id)
                      ->where('block_tower_number', $blk->name);
                })
                ->first();

            $zoneId = $project->zone_id ?? ($flat->zone_id ?? null);
            $zoneName = $project->zone_name ?? ($flat->zone_name ?? null);
            $districtId = $project->district_id ?? ($flat->district_id ?? null);
            $districtName = $project->district_name ?? ($flat->district_name ?? null);
            $townId = $project->town_id ?? ($flat->town_id ?? null);
            $townName = $project->town_name ?? ($flat->town_name ?? null);
            $projectName = $project->name ?? ($flat->project_name ?? null);

            DB::table('ews_blocks')->where('id', $blk->id)->update([
                'zone_id'       => $zoneId,
                'zone_name'     => $zoneName,
                'district_id'   => $districtId,
                'district_name' => $districtName,
                'town_id'       => $townId,
                'town_name'     => $townName,
                'project_name'  => $projectName,
                'updated_at'    => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ews_blocks', function (Blueprint $table) {
            $cols = ['zone_id', 'zone_name', 'district_id', 'district_name', 'town_id', 'town_name', 'project_name'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('ews_blocks', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
