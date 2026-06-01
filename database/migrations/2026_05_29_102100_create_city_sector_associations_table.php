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
        Schema::create('city_sector_associations', function (Blueprint $table) {
            $table->integer('AssociationId')->primary();
            $table->integer('BranchId');
            $table->integer('CityId');
            $table->integer('SectorId');
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

            $table->foreign('CityId')
                ->references('CityId')
                ->on('cities')
                ->onDelete('cascade');

            $table->foreign('SectorId')
                ->references('SectorId')
                ->on('sectors')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city_sector_associations');
    }
};
