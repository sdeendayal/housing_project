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
        Schema::create('cash_receipt_details', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('asset_number');
            $table->decimal('total_paid_amount', 15, 2)->default(0);
            $table->string('receipt_number', 50)->nullable();
            $table->integer('BranchId');
            $table->integer('DistrictId');
            $table->integer('CityId');
            $table->integer('SectorId');
            $table->boolean('IsActive')->default(1);
            $table->boolean('IsDeleted')->default(0);
            $table->dateTime('created_date')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->dateTime('ModifiedDate')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('CompanyId');

            $table->foreign('BranchId')->references('BranchId')->on('em_offices')->onDelete('cascade');
            $table->foreign('DistrictId')->references('DistrictId')->on('districts')->onDelete('cascade');
            $table->foreign('CityId')->references('CityId')->on('cities')->onDelete('cascade');
            $table->foreign('SectorId')->references('SectorId')->on('sectors')->onDelete('cascade');
            $table->foreign('asset_number')->references('AssetId')->on('property_registration')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_receipt_details');
    }
};
