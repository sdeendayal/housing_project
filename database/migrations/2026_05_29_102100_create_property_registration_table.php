<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('property_registration', function (Blueprint $table) {
            $table->integer('AssetId')->primary();
            $table->string('AssetName', 200);
            $table->integer('AssetSize');
            $table->string('Unit', 50)->nullable();
            $table->integer('BranchId');
            $table->integer('DistrictId');
            $table->integer('CityId');
            $table->integer('SectorId');
            $table->boolean('IsActive')->default(1);
            $table->boolean('IsDeleted')->default(0);
            $table->dateTime('CreatedDate')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->dateTime('ModifiedDate')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('CompanyId');

            $table->foreign('BranchId')->references('BranchId')->on('em_offices')->onDelete('cascade');
            $table->foreign('DistrictId')->references('DistrictId')->on('districts')->onDelete('cascade');
            $table->foreign('CityId')->references('CityId')->on('cities')->onDelete('cascade');
            $table->foreign('SectorId')->references('SectorId')->on('sectors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_registration');
    }
};
