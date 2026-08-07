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
        Schema::table('property_private_purchasers', function (Blueprint $table) {
            $table->string('phase', 50)->nullable()->after('CompanyId');
            $table->string('property_type', 100)->nullable()->after('phase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_private_purchasers', function (Blueprint $table) {
            $table->dropColumn(['phase', 'property_type']);
        });
    }
};
