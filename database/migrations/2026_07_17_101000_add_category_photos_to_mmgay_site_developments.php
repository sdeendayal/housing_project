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
        Schema::table('mmgay_site_developments', function (Blueprint $table) {
            $table->string('road_photo')->nullable();
            $table->string('water_photo')->nullable();
            $table->string('electricity_photo')->nullable();
            $table->string('sewerage_photo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mmgay_site_developments', function (Blueprint $table) {
            $table->dropColumn(['road_photo', 'water_photo', 'electricity_photo', 'sewerage_photo']);
        });
    }
};
