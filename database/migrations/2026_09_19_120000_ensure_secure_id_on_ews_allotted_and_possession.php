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
        if (Schema::hasTable('ews_allotted_8')) {
            Schema::table('ews_allotted_8', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_allotted_8', 'secure_id')) {
                    $table->string('secure_id', 32)->nullable()->unique()->after('flat_no');
                }
            });
        }

        if (Schema::hasTable('ews_beneficiary_possessions')) {
            Schema::table('ews_beneficiary_possessions', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'beneficiary_secure_id')) {
                    $table->string('beneficiary_secure_id', 32)->nullable()->index()->after('beneficiary_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ews_beneficiary_possessions')) {
            Schema::table('ews_beneficiary_possessions', function (Blueprint $table) {
                if (Schema::hasColumn('ews_beneficiary_possessions', 'beneficiary_secure_id')) {
                    $table->dropColumn('beneficiary_secure_id');
                }
            });
        }
    }
};
