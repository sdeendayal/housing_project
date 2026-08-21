<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('property_private_purchasers', function (Blueprint $table) {
            $table->index('ApplicationNo', 'idx_ppp_application_no');
            $table->index('DistrictId', 'idx_ppp_district_id');
            $table->index('MobileNo', 'idx_ppp_mobile_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_private_purchasers', function (Blueprint $table) {
            $table->dropIndex('idx_ppp_application_no');
            $table->dropIndex('idx_ppp_district_id');
            $table->dropIndex('idx_ppp_mobile_no');
        });
    }
};
