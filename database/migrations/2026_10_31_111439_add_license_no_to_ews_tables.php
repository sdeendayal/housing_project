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
        if (Schema::hasTable('ews_builder_flats') && !Schema::hasColumn('ews_builder_flats', 'license_no')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                $table->string('license_no', 150)->nullable()->after('developer_name');
            });
        }

        if (Schema::hasTable('ews_projects') && !Schema::hasColumn('ews_projects', 'license_no')) {
            Schema::table('ews_projects', function (Blueprint $table) {
                $table->string('license_no', 150)->nullable()->after('developer_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ews_builder_flats') && Schema::hasColumn('ews_builder_flats', 'license_no')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                $table->dropColumn('license_no');
            });
        }

        if (Schema::hasTable('ews_projects') && Schema::hasColumn('ews_projects', 'license_no')) {
            Schema::table('ews_projects', function (Blueprint $table) {
                $table->dropColumn('license_no');
            });
        }
    }
};
