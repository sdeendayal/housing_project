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
        Schema::create('mmgay_site_developments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('block_id');
            $table->unsignedBigInteger('village_id');
            $table->string('road_status')->default('Not Started');
            $table->string('water_status')->default('Not Started');
            $table->string('electricity_status')->default('Not Started');
            $table->string('sewerage_status')->default('Not Started');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            $table->index(['district_id', 'block_id', 'village_id']);
        });

        Schema::create('mmgay_site_development_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_development_id');
            $table->string('photo_path');
            $table->timestamps();

            $table->foreign('site_development_id')
                ->references('id')
                ->on('mmgay_site_developments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mmgay_site_development_photos');
        Schema::dropIfExists('mmgay_site_developments');
    }
};
