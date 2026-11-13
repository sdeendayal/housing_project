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
        Schema::table('ews_builder_flats', function (Blueprint $table) {
            if (!Schema::hasColumn('ews_builder_flats', 'master_development_plan')) {
                $table->string('master_development_plan', 255)->nullable()->after('address_of_project');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'approved_purpose')) {
                $table->string('approved_purpose', 255)->nullable()->after('master_development_plan');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'licence_validity_date')) {
                $table->string('licence_validity_date', 100)->nullable()->after('license_no');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'grant_status')) {
                $table->string('grant_status', 100)->nullable()->after('licence_validity_date');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'case_no')) {
                $table->string('case_no', 100)->nullable()->after('grant_status');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'application_no')) {
                $table->string('application_no', 100)->nullable()->after('case_no');
            }
        });

        Schema::table('ews_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('ews_projects', 'master_development_plan')) {
                $table->string('master_development_plan', 255)->nullable()->after('address');
            }
            if (!Schema::hasColumn('ews_projects', 'approved_purpose')) {
                $table->string('approved_purpose', 255)->nullable()->after('master_development_plan');
            }
            if (!Schema::hasColumn('ews_projects', 'licence_validity_date')) {
                $table->string('licence_validity_date', 100)->nullable()->after('license_no');
            }
            if (!Schema::hasColumn('ews_projects', 'grant_status')) {
                $table->string('grant_status', 100)->nullable()->after('licence_validity_date');
            }
            if (!Schema::hasColumn('ews_projects', 'case_no')) {
                $table->string('case_no', 100)->nullable()->after('grant_status');
            }
            if (!Schema::hasColumn('ews_projects', 'application_no')) {
                $table->string('application_no', 100)->nullable()->after('case_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ews_builder_flats', function (Blueprint $table) {
            $cols = ['master_development_plan', 'approved_purpose', 'licence_validity_date', 'grant_status', 'case_no', 'application_no'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('ews_builder_flats', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('ews_projects', function (Blueprint $table) {
            $cols = ['master_development_plan', 'approved_purpose', 'licence_validity_date', 'grant_status', 'case_no', 'application_no'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('ews_projects', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
