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
        if (Schema::hasTable('ews_towns')) {
            Schema::table('ews_towns', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_towns', 'zone_id')) {
                    $table->unsignedBigInteger('zone_id')->nullable()->after('district_id');
                    $table->index('zone_id');
                }
                if (!Schema::hasColumn('ews_towns', 'zone_name')) {
                    $table->string('zone_name')->nullable()->after('zone_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ews_towns')) {
            Schema::table('ews_towns', function (Blueprint $table) {
                if (Schema::hasColumn('ews_towns', 'zone_id')) {
                    $table->dropIndex(['zone_id']);
                    $table->dropColumn('zone_id');
                }
                if (Schema::hasColumn('ews_towns', 'zone_name')) {
                    $table->dropColumn('zone_name');
                }
            });
        }
    }
};
