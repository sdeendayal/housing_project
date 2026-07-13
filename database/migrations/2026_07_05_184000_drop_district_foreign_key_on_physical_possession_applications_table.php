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
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            // Drop foreign key to districts table to allow mapping from districtmaster
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->dropForeign('physical_possession_applications_district_id_foreign');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->foreign('district_id')->references('DistrictId')->on('districts');
        });
    }
};
