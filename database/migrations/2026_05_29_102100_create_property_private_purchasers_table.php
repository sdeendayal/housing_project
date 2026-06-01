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
        Schema::create('property_private_purchasers', function (Blueprint $table) {
            $table->integer('PrivatePurchaserId')->primary();
            $table->integer('Flat_Id');
            $table->string('PrivatePurchaserName', 200);
            $table->string('PurchaserFatherName', 200)->nullable();
            $table->bigInteger('MobileNo')->nullable();
            $table->integer('ApplicationNo')->nullable();
            $table->string('PPPId', 50)->nullable();
            $table->string('MemberID', 50)->nullable();
            $table->string('CasteCategoryName', 100)->nullable();
            $table->string('MaritalStatus', 50)->nullable();
            $table->text('Address')->nullable();
            $table->integer('BranchId');
            $table->integer('DistrictId');
            $table->integer('CityId');
            $table->integer('SectorId');
            $table->boolean('IsActive')->default(1);
            $table->boolean('IsDeleted')->default(0);
            $table->boolean('UserLoginCreated')->default(0);
            $table->boolean('Is_UserLogin_Active')->default(0);
            $table->boolean('Is_UserLogin_Deleted')->default(0);
            $table->dateTime('CreateDate')->nullable();
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
        Schema::dropIfExists('property_private_purchasers');
    }
};
