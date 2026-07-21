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
        // 1. districtmaster
        if (!Schema::hasTable('districtmaster')) {
            Schema::create('districtmaster', function (Blueprint $table) {
                $table->integer('DistrictId')->primary();
                $table->string('DistrictName', 200);
            });
        }

        // 2. blockmaster
        if (!Schema::hasTable('blockmaster')) {
            Schema::create('blockmaster', function (Blueprint $table) {
                $table->integer('BlockId')->primary();
                $table->integer('DistrictId');
                $table->string('BlockName', 200);

                $table->foreign('DistrictId')->references('DistrictId')->on('districtmaster');
            });
        }

        // 3. villagemaster
        if (!Schema::hasTable('villagemaster')) {
            Schema::create('villagemaster', function (Blueprint $table) {
                $table->integer('VillageId')->primary();
                $table->integer('BlockId');
                $table->integer('DistrictId');
                $table->string('VillageName', 200);
                $table->integer('plots');
                $table->integer('phase');

                $table->foreign('BlockId')->references('BlockId')->on('blockmaster');
                $table->foreign('DistrictId')->references('DistrictId')->on('districtmaster');
            });
        }

        // 4. flatmaster
        if (!Schema::hasTable('flatmaster')) {
            Schema::create('flatmaster', function (Blueprint $table) {
                $table->integer('FlatId')->primary();
                $table->string('FlatNo', 100);
                $table->integer('VillageId');
                $table->integer('BlockId');
                $table->integer('DistrictId');
                $table->integer('CompanyId')->nullable();
                $table->dateTime('CreatedDate')->nullable();
                $table->integer('CreatedBy')->nullable();
                $table->dateTime('UpdatedDate')->nullable();
                $table->integer('UpdatedBy')->nullable();
                $table->boolean('IsActive')->default(true);

                $table->foreign('BlockId')->references('BlockId')->on('blockmaster');
                $table->foreign('DistrictId')->references('DistrictId')->on('districtmaster');
                $table->foreign('VillageId')->references('VillageId')->on('villagemaster');
            });
        }

        // 5. ownermaster
        if (!Schema::hasTable('ownermaster')) {
            Schema::create('ownermaster', function (Blueprint $table) {
                $table->integer('OwnerId')->primary();
                $table->string('OwnerName', 200);
                $table->string('Relation', 100)->nullable();
                $table->string('FatherHusbandName', 200)->nullable();
                $table->string('Gender', 20)->nullable();
                $table->integer('FlatId');
                $table->integer('DistrictId')->nullable();
                $table->integer('BlockId')->nullable();
                $table->integer('VillageId')->nullable();
                $table->text('OwnerAddress')->nullable();
                $table->string('RegistrationNo', 100)->nullable();
                $table->string('PPPId', 100)->nullable();
                $table->string('MemberId', 100)->nullable();
                $table->string('Caste', 11)->nullable();
                $table->string('MobileNo', 20)->nullable();
                $table->integer('CompanyId')->nullable();
                $table->tinyInteger('Phase')->nullable();
                $table->boolean('IsApproved')->default(false);
                $table->boolean('IsRejected')->default(false);
                $table->boolean('IsDcReconsidered')->default(false);
                $table->integer('DCReOpenedCount')->default(0);
                $table->boolean('IsPaid')->default(false);
                $table->boolean('IsPaymentApproved')->default(false);
                $table->boolean('IsAllotmentCancelled')->default(false);
                $table->text('Remarks')->nullable();
                $table->text('DCRemarks')->nullable();
                $table->integer('CreatedBy')->nullable();
                $table->dateTime('CreatedDate')->nullable();
                $table->integer('UpdatedBy')->nullable();
                $table->dateTime('UpdatedDate')->nullable();
            });
        }

        // 6. socialcategorymaster
        if (!Schema::hasTable('socialcategorymaster')) {
            Schema::create('socialcategorymaster', function (Blueprint $table) {
                $table->integer('CategoryId')->primary();
                $table->string('CategoryName', 150);
                $table->integer('CompanyId')->nullable();
                $table->dateTime('CreatedDate')->nullable();
                $table->integer('CreatedBy')->nullable();
                $table->dateTime('UpdatedDate')->nullable();
                $table->integer('UpdatedBy')->nullable();
                $table->boolean('IsActive')->default(true);
            });
        }

        // 7. allotment_table
        if (!Schema::hasTable('allotment_table')) {
            Schema::create('allotment_table', function (Blueprint $table) {
                $table->id('sr_no');
                $table->text('name')->nullable();
                $table->text('fathers_or_husband_name')->nullable();
                $table->integer('application_number')->nullable();
                $table->text('plot')->nullable();
                $table->text('Sector')->nullable();
                $table->text('ward')->nullable();
                $table->text('town')->nullable();
                $table->text('district')->nullable();
                $table->integer('sms_status')->nullable();
            });
        }

        // 8. allotment_table2
        if (!Schema::hasTable('allotment_table2')) {
            Schema::create('allotment_table2', function (Blueprint $table) {
                $table->id('sr_no');
                $table->text('name')->nullable();
                $table->text('fathers_or_husband_name')->nullable();
                $table->integer('application_number')->nullable();
                $table->text('plot')->nullable();
                $table->text('Sector')->nullable();
                $table->text('ward')->nullable();
                $table->text('town')->nullable();
                $table->text('district')->nullable();
            });
        }

        // 9. allotment_table_bkp
        if (!Schema::hasTable('allotment_table_bkp')) {
            Schema::create('allotment_table_bkp', function (Blueprint $table) {
                $table->id('sr_no');
                $table->integer('application_number')->nullable();
                $table->text('plot')->nullable();
                $table->text('name')->nullable();
                $table->text('fathers_or_husband_name')->nullable();
                $table->text('Sector')->nullable();
                $table->text('ward')->nullable();
                $table->text('town')->nullable();
                $table->text('district')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allotment_table_bkp');
        Schema::dropIfExists('allotment_table2');
        Schema::dropIfExists('allotment_table');
        Schema::dropIfExists('socialcategorymaster');
        Schema::dropIfExists('ownermaster');
        Schema::dropIfExists('flatmaster');
        Schema::dropIfExists('villagemaster');
        Schema::dropIfExists('blockmaster');
        Schema::dropIfExists('districtmaster');
    }
};
