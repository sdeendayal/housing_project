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
        // 1. Add indexes to physical_possession_applications with short custom names
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->index('owner_id', 'ppa_owner_id_idx');
            $table->index('scheme', 'ppa_scheme_idx');
            $table->index('physical_possession_status', 'ppa_status_idx');
            $table->index('district_id', 'ppa_dist_id_idx');
        });

        // 2. Add indexes to ownermaster
        Schema::table('ownermaster', function (Blueprint $table) {
            $table->index('MobileNo', 'om_mobileno_idx');
            $table->index('DistrictId', 'om_dist_idx');
            $table->index('IsApproved', 'om_approved_idx');
            $table->index('IsPaid', 'om_paid_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->dropIndex('ppa_owner_id_idx');
            $table->dropIndex('ppa_scheme_idx');
            $table->dropIndex('ppa_status_idx');
            $table->dropIndex('ppa_dist_id_idx');
        });

        Schema::table('ownermaster', function (Blueprint $table) {
            $table->dropIndex('om_mobileno_idx');
            $table->dropIndex('om_dist_idx');
            $table->dropIndex('om_approved_idx');
            $table->dropIndex('om_paid_idx');
        });
    }
};
