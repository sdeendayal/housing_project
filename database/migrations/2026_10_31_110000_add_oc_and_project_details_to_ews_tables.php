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
        Schema::table('ews_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('ews_projects', 'address')) {
                $table->text('address')->nullable()->after('name');
            }
            if (!Schema::hasColumn('ews_projects', 'sector_no')) {
                $table->string('sector_no', 100)->nullable()->after('name');
            }
            if (!Schema::hasColumn('ews_projects', 'developer_name')) {
                $table->string('developer_name', 255)->nullable()->after('name');
            }
        });

        Schema::table('ews_builder_flats', function (Blueprint $table) {
            if (!Schema::hasColumn('ews_builder_flats', 'developer_name')) {
                $table->string('developer_name', 255)->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'oc_status')) {
                $table->string('oc_status', 30)->nullable()->after('developer_name');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'oc_date')) {
                $table->date('oc_date')->nullable()->after('oc_status');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'oc_copy_file')) {
                $table->string('oc_copy_file', 255)->nullable()->after('oc_date');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'total_flats_in_project')) {
                $table->integer('total_flats_in_project')->nullable()->after('oc_copy_file');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'total_ews_flats_in_project')) {
                $table->integer('total_ews_flats_in_project')->nullable()->after('total_flats_in_project');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'flats_offered_for_draw')) {
                $table->integer('flats_offered_for_draw')->nullable()->after('total_ews_flats_in_project');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'remaining_flats_for_oc')) {
                $table->integer('remaining_flats_for_oc')->nullable()->after('flats_offered_for_draw');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ews_projects', function (Blueprint $table) {
            $table->dropColumn(['address', 'sector_no', 'developer_name']);
        });

        Schema::table('ews_builder_flats', function (Blueprint $table) {
            $table->dropColumn([
                'developer_name',
                'oc_status',
                'oc_date',
                'oc_copy_file',
                'total_flats_in_project',
                'total_ews_flats_in_project',
                'flats_offered_for_draw',
                'remaining_flats_for_oc',
            ]);
        });
    }
};
