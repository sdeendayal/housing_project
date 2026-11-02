<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ews_builder_flats')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                // Ensure is_aproved_by_dtp default is 0 and non-null for fast index lookups
                $table->index('zone_id', 'ews_flats_zone_id_idx');
                $table->index('district_id', 'ews_flats_dist_id_idx');
                $table->index('town_id', 'ews_flats_town_id_idx');
                $table->index('project_id', 'ews_flats_proj_id_idx');
                $table->index('license_no', 'ews_flats_lic_no_idx');
                
                // Composite indexes for ultra-fast filtering across All/Pending/Approved
                $table->index(['zone_id', 'is_aproved_by_dtp'], 'ews_flats_zone_dtp_idx');
                $table->index(['district_id', 'is_aproved_by_dtp'], 'ews_flats_dist_dtp_idx');
                $table->index(['is_aproved_by_dtp', 'id'], 'ews_flats_dtp_id_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ews_builder_flats')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                $table->dropIndex('ews_flats_zone_id_idx');
                $table->dropIndex('ews_flats_dist_id_idx');
                $table->dropIndex('ews_flats_town_id_idx');
                $table->dropIndex('ews_flats_proj_id_idx');
                $table->dropIndex('ews_flats_lic_no_idx');
                $table->dropIndex('ews_flats_zone_dtp_idx');
                $table->dropIndex('ews_flats_dist_dtp_idx');
                $table->dropIndex('ews_flats_dtp_id_idx');
            });
        }
    }
};

