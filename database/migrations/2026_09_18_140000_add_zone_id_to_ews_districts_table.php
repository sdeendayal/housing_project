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
        if (Schema::hasTable('ews_districts')) {
            Schema::table('ews_districts', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_districts', 'zone_id')) {
                    $table->unsignedBigInteger('zone_id')->nullable()->after('name');
                    $table->index('zone_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ews_districts')) {
            Schema::table('ews_districts', function (Blueprint $table) {
                if (Schema::hasColumn('ews_districts', 'zone_id')) {
                    $table->dropIndex(['zone_id']);
                    $table->dropColumn('zone_id');
                }
            });
        }
    }
};
