<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('property_auction_detail', function (Blueprint $table) {
            $table->integer('PropertyAuctionId')->primary();
            $table->integer('BranchId');
            $table->integer('DistrictId');
            $table->integer('CityId');
            $table->integer('SectorId');
            $table->integer('AssetId');
            $table->decimal('FlatCost', 15, 2)->default(0);
            $table->decimal('ReceivedAmount', 15, 2)->default(0);
            $table->decimal('BalanceAmount', 15, 2)->default(0);
            $table->integer('PurchaserID');
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
            $table->foreign('AssetId')->references('AssetId')->on('property_registration')->onDelete('cascade');
            $table->foreign('PurchaserID')->references('PrivatePurchaserId')->on('property_private_purchasers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_auction_detail');
    }
};