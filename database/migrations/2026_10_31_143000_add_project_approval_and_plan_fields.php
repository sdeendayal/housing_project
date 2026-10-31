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
                if (!Schema::hasColumn('ews_builder_flats', 'area_of_project')) {
                    $table->string('area_of_project', 150)->nullable()->after('address_of_project');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'developer_plan')) {
                    $table->string('developer_plan', 255)->nullable()->after('area_of_project');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'approved_building_plan')) {
                    $table->string('approved_building_plan', 10)->nullable()->after('developer_plan');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'building_plan_approval_date')) {
                    $table->date('building_plan_approval_date')->nullable()->after('approved_building_plan');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'building_plan_copy')) {
                    $table->string('building_plan_copy', 255)->nullable()->after('building_plan_approval_date');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'oc_status_yn')) {
                    $table->string('oc_status_yn', 10)->nullable()->after('building_plan_copy');
                }
            });
        }

        if (Schema::hasTable('ews_projects')) {
            Schema::table('ews_projects', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_projects', 'area_of_project')) {
                    $table->string('area_of_project', 150)->nullable()->after('address');
                }
                if (!Schema::hasColumn('ews_projects', 'developer_plan')) {
                    $table->string('developer_plan', 255)->nullable()->after('area_of_project');
                }
                if (!Schema::hasColumn('ews_projects', 'approved_building_plan')) {
                    $table->string('approved_building_plan', 10)->nullable()->after('developer_plan');
                }
                if (!Schema::hasColumn('ews_projects', 'building_plan_approval_date')) {
                    $table->date('building_plan_approval_date')->nullable()->after('approved_building_plan');
                }
                if (!Schema::hasColumn('ews_projects', 'building_plan_copy')) {
                    $table->string('building_plan_copy', 255)->nullable()->after('building_plan_approval_date');
                }
                if (!Schema::hasColumn('ews_projects', 'oc_status_yn')) {
                    $table->string('oc_status_yn', 10)->nullable()->after('building_plan_copy');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ews_builder_flats')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                $cols = ['area_of_project', 'developer_plan', 'approved_building_plan', 'building_plan_approval_date', 'building_plan_copy', 'oc_status_yn'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('ews_builder_flats', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('ews_projects')) {
            Schema::table('ews_projects', function (Blueprint $table) {
                $cols = ['area_of_project', 'developer_plan', 'approved_building_plan', 'building_plan_approval_date', 'building_plan_copy', 'oc_status_yn'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('ews_projects', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
