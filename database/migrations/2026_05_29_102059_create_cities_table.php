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
        Schema::create('cities', function (Blueprint $table) {
            $table->integer('CityId')->primary();
            $table->string('CityName', 150);
            $table->integer('BranchId');
            $table->integer('DistrictId');
            $table->boolean('Is_Active')->default(1);
            $table->boolean('Is_Deleted')->default(0);
            $table->dateTime('CreatedDate')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->dateTime('ModifiedDate')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->integer('CompanyId');

            $table->foreign('BranchId')
                ->references('BranchId')
                ->on('em_offices')
                ->onDelete('cascade');

            $table->foreign('DistrictId')
                ->references('DistrictId')
                ->on('districts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
