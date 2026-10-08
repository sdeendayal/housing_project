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
        if (Schema::hasTable('ews_beneficiary_possessions')) {
            Schema::table('ews_beneficiary_possessions', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'zone_id')) {
                    $table->unsignedBigInteger('zone_id')->nullable()->index()->after('district_name');
                }
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'zone_name')) {
                    $table->string('zone_name')->nullable()->after('zone_id');
                }
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'dist_id')) {
                    $table->unsignedBigInteger('dist_id')->nullable()->index()->after('zone_name');
                }
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'town_id')) {
                    $table->unsignedBigInteger('town_id')->nullable()->index()->after('dist_id');
                }
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'town_name')) {
                    $table->string('town_name')->nullable()->after('town_id');
                }
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'project_id')) {
                    $table->unsignedBigInteger('project_id')->nullable()->index()->after('town_name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ews_beneficiary_possessions')) {
            Schema::table('ews_beneficiary_possessions', function (Blueprint $table) {
                $columns = ['zone_id', 'zone_name', 'dist_id', 'town_id', 'town_name', 'project_id'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('ews_beneficiary_possessions', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
