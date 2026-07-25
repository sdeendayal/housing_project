<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $hasFlatmasterVillageIdKey = collect(DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = 'flatmaster' 
              AND COLUMN_NAME = 'VillageId' 
              AND REFERENCED_TABLE_NAME = 'villagemaster'
        "))->isNotEmpty();

        // Drop foreign keys referencing villagemaster or defined on it
        Schema::table('flatmaster', function (Blueprint $table) use ($hasFlatmasterVillageIdKey) {
            if ($hasFlatmasterVillageIdKey) {
                $table->dropForeign(['VillageId']);
            }
        });

        Schema::dropIfExists('villagemaster');

        Schema::create('villagemaster', function (Blueprint $table) {
            $table->id();
            $table->integer('VillageId');
            $table->integer('BlockId');
            $table->integer('DistrictId');
            $table->string('VillageName');
            $table->integer('plots')->nullable();
            $table->integer('phase')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('villagemaster');
        Schema::enableForeignKeyConstraints();
    }
};
