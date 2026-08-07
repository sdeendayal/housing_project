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
            if (!Schema::hasColumn('property_private_purchasers', 'phase')) {
                $table->string('phase', 50)->nullable()->after('CompanyId');
            }
            if (!Schema::hasColumn('property_private_purchasers', 'property_type')) {
                $table->string('property_type', 100)->nullable()->after('phase');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_private_purchasers', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('property_private_purchasers', 'phase')) {
                $columnsToDrop[] = 'phase';
            }
            if (Schema::hasColumn('property_private_purchasers', 'property_type')) {
                $columnsToDrop[] = 'property_type';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
