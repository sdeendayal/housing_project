<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ews_builder_flats')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_builder_flats', 'sector_no')) {
                    $table->string('sector_no', 100)->nullable()->after('project_name');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'address_of_project')) {
                    $table->text('address_of_project')->nullable()->after('sector_no');
                }
            });
        }

        if (Schema::hasTable('ews_projects')) {
            Schema::table('ews_projects', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_projects', 'sector_no')) {
                    $table->string('sector_no', 100)->nullable()->after('license_no');
                }
                if (!Schema::hasColumn('ews_projects', 'address_of_project') && !Schema::hasColumn('ews_projects', 'address')) {
                    $table->text('address_of_project')->nullable()->after('sector_no');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ews_builder_flats')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                if (Schema::hasColumn('ews_builder_flats', 'sector_no')) {
                    $table->dropColumn('sector_no');
                }
                if (Schema::hasColumn('ews_builder_flats', 'address_of_project')) {
                    $table->dropColumn('address_of_project');
                }
            });
        }
    }
};
