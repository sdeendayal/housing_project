<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ews_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('ews_projects', 'zone_id')) {
                $table->unsignedBigInteger('zone_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('ews_projects', 'zone_name')) {
                $table->string('zone_name')->nullable()->after('zone_id');
            }
            if (!Schema::hasColumn('ews_projects', 'district_name')) {
                $table->string('district_name')->nullable()->after('district_id');
            }
            if (!Schema::hasColumn('ews_projects', 'town_id')) {
                $table->unsignedBigInteger('town_id')->nullable()->after('district_name');
            }
            if (!Schema::hasColumn('ews_projects', 'town_name')) {
                $table->string('town_name')->nullable()->after('town_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ews_projects', function (Blueprint $table) {
            $cols = ['zone_id', 'zone_name', 'district_name', 'town_id', 'town_name'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('ews_projects', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
