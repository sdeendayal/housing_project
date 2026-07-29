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
        Schema::table('mmgay_site_developments', function (Blueprint $table) {
            $table->string('phase')->nullable()->after('village_id');
        });

        Schema::table('mmgay_site_development_logs', function (Blueprint $table) {
            $table->string('phase')->nullable()->after('village_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mmgay_site_developments', function (Blueprint $table) {
            $table->dropColumn('phase');
        });

        Schema::table('mmgay_site_development_logs', function (Blueprint $table) {
            $table->dropColumn('phase');
        });
    }
};
