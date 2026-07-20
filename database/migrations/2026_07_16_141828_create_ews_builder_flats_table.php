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
        Schema::create('ews_builder_flats', function (Blueprint $table) {
            $table->id();
            $table->string('secure_id', 64)->nullable()->unique();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->string('district_name');
            $table->string('town_name');
            $table->string('project_name');
            $table->string('block_tower_number');
            $table->string('floor');
            $table->string('flat_number');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ews_builder_flats');
    }
};
