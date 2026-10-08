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
        Schema::create('ews_flat_abbreviations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dist_id')->nullable()->index();
            $table->string('dist_name', 255)->nullable();
            $table->unsignedBigInteger('town_id')->nullable()->index();
            $table->string('town_name', 255)->nullable();
            $table->unsignedBigInteger('zone_id')->nullable()->index();
            $table->string('zone_name', 255)->nullable();
            $table->string('town_abbr', 50)->nullable();
            $table->string('project_name', 255)->nullable();
            $table->string('project_abbr', 50)->nullable();
            $table->string('floor', 100)->nullable();
            $table->string('floor_abbr', 50)->nullable();
            $table->string('block_tower', 100)->nullable();
            $table->string('block_abbr', 50)->nullable();
            $table->string('flat_no', 100)->nullable();
            $table->string('flat_abbr', 50)->nullable();
            $table->string('final_no', 255)->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ews_flat_abbreviations');
    }
};
