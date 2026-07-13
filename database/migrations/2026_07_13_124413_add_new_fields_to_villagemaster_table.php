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
        Schema::table('villagemaster', function (Blueprint $table) {
            $table->integer('TotalPlots')->nullable();
            $table->string('Phase1')->nullable();
            $table->integer('totalPlotsPhase2')->nullable();
            $table->string('Phase2')->nullable();
            $table->integer('totalPlotsPhase3')->nullable();
            $table->string('Phase3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('villagemaster', function (Blueprint $table) {
            $table->dropColumn([
                'TotalPlots',
                'Phase1',
                'totalPlotsPhase2',
                'Phase2',
                'totalPlotsPhase3',
                'Phase3',
            ]);
        });
    }
};
