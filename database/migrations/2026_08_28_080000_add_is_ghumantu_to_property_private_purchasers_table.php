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
        if (Schema::hasTable('property_private_purchasers') && !Schema::hasColumn('property_private_purchasers', 'is_ghumantu')) {
            Schema::table('property_private_purchasers', function (Blueprint $table) {
                $table->tinyInteger('is_ghumantu')->default(0)->after('property_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('property_private_purchasers') && Schema::hasColumn('property_private_purchasers', 'is_ghumantu')) {
            Schema::table('property_private_purchasers', function (Blueprint $table) {
                $table->dropColumn('is_ghumantu');
            });
        }
    }
};
