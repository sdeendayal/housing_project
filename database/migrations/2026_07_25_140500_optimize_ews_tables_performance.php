<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Optimize all_ews_data_1
        try {
            Schema::table('all_ews_data_1', function (Blueprint $table) {
                try {
                    $table->dropIndex('idx_all_ews_mobile');
                } catch (\Exception $e) {}

                $table->string('application_number', 255)->nullable()->change();
                $table->string('mobile_number', 255)->nullable()->change();
                $table->string('full_name', 255)->nullable()->change();

                $table->index('application_number', 'all_ews_data_1_app_num_idx');
                $table->index('mobile_number', 'all_ews_data_1_mobile_idx');
                $table->index('full_name', 'all_ews_data_1_full_name_idx');
                $table->index('dist_id', 'all_ews_data_1_dist_id_idx');
            });
        } catch (\Exception $e) {
            Log::warning("Error migrating all_ews_data_1: " . $e->getMessage());
        }

        // 2. Optimize ppt_members
        try {
            Schema::table('ppt_members', function (Blueprint $table) {
                $table->string('memberID', 255)->nullable()->change();
                $table->string('familyID', 255)->nullable()->change();
                $table->string('mobileNo', 255)->nullable()->change();
                $table->string('fullName', 255)->nullable()->change();

                $table->index('memberID', 'ppt_members_member_id_idx');
                $table->index('familyID', 'ppt_members_family_id_idx');
                $table->index('mobileNo', 'ppt_members_mobile_no_idx');
                $table->index('fullName', 'ppt_members_full_name_idx');
                $table->index('district_id', 'ppt_members_district_id_idx');
            });
        } catch (\Exception $e) {
            Log::warning("Error migrating ppt_members: " . $e->getMessage());
        }

        // 3. Optimize ews_bookings_7
        try {
            Schema::table('ews_bookings_7', function (Blueprint $table) {
                $table->string('application_number', 255)->nullable()->change();
                $table->string('mobile_number', 255)->nullable()->change();
                $table->string('full_name', 255)->nullable()->change();

                $table->index('application_number', 'ews_bookings_7_app_num_idx');
                $table->index('mobile_number', 'ews_bookings_7_mobile_idx');
                $table->index('full_name', 'ews_bookings_7_full_name_idx');
                $table->index('dist_id', 'ews_bookings_7_dist_id_idx');
            });
        } catch (\Exception $e) {
            Log::warning("Error migrating ews_bookings_7: " . $e->getMessage());
        }

        // 4. Optimize other tables
        $tables = [
            'ews_allotted_8',
            'ews_waiting_list_9',
            'ews_reject_ppp_exclusion_2',
            'ews_reject_property_in_india_3',
            'ews_house_ownership_reject_4',
            'ews_eligible_draw_list_5',
            'ews_eligible_6'
        ];

        foreach ($tables as $tableName) {
            try {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->index('application_number', $tableName . '_app_num_idx');
                    $table->index('mobile_number', $tableName . '_mobile_idx');
                    $table->index('full_name', $tableName . '_full_name_idx');
                    $table->index('dist_id', $tableName . '_dist_id_idx');
                });
            } catch (\Exception $e) {
                Log::warning("Error migrating $tableName: " . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'ews_allotted_8',
            'ews_waiting_list_9',
            'ews_reject_ppp_exclusion_2',
            'ews_reject_property_in_india_3',
            'ews_house_ownership_reject_4',
            'ews_eligible_draw_list_5',
            'ews_eligible_6'
        ];

        foreach ($tables as $tableName) {
            try {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->dropIndex($tableName . '_app_num_idx');
                    $table->dropIndex($tableName . '_mobile_idx');
                    $table->dropIndex($tableName . '_full_name_idx');
                    $table->dropIndex($tableName . '_dist_id_idx');
                });
            } catch (\Exception $e) {}
        }

        try {
            Schema::table('ews_bookings_7', function (Blueprint $table) {
                $table->dropIndex('ews_bookings_7_app_num_idx');
                $table->dropIndex('ews_bookings_7_mobile_idx');
                $table->dropIndex('ews_bookings_7_full_name_idx');
                $table->dropIndex('ews_bookings_7_dist_id_idx');

                $table->text('application_number')->nullable()->change();
                $table->text('mobile_number')->nullable()->change();
                $table->text('full_name')->nullable()->change();
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('ppt_members', function (Blueprint $table) {
                $table->dropIndex('ppt_members_member_id_idx');
                $table->dropIndex('ppt_members_family_id_idx');
                $table->dropIndex('ppt_members_mobile_no_idx');
                $table->dropIndex('ppt_members_full_name_idx');
                $table->dropIndex('ppt_members_district_id_idx');

                $table->text('memberID')->nullable()->change();
                $table->text('familyID')->nullable()->change();
                $table->text('mobileNo')->nullable()->change();
                $table->text('fullName')->nullable()->change();
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('all_ews_data_1', function (Blueprint $table) {
                $table->dropIndex('all_ews_data_1_app_num_idx');
                $table->dropIndex('all_ews_data_1_mobile_idx');
                $table->dropIndex('all_ews_data_1_full_name_idx');
                $table->dropIndex('all_ews_data_1_dist_id_idx');

                $table->text('application_number')->nullable()->change();
                $table->text('mobile_number')->nullable()->change();
                $table->text('full_name')->nullable()->change();

                $table->index('mobile_number', 'idx_all_ews_mobile');
            });
        } catch (\Exception $e) {}
    }
};
