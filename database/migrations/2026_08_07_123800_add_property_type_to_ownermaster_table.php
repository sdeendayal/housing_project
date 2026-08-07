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
        Schema::table('ownermaster', function (Blueprint $table) {
            if (!Schema::hasColumn('ownermaster', 'property_type')) {
                $table->string('property_type', 100)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ownermaster', function (Blueprint $table) {
            if (Schema::hasColumn('ownermaster', 'property_type')) {
                $table->dropColumn('property_type');
            }
        });
    }
};
