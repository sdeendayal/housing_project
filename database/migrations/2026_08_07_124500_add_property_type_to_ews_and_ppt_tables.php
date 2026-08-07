<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private array $tables = [
        'all_ews_data_1',
        'ews_reject_ppp_exclusion_2',
        'ews_reject_property_in_india_3',
        'ews_house_ownership_reject_4',
        'ews_eligible_draw_list_5',
        'ews_eligible_6',
        'ews_bookings_7',
        'ews_allotted_8',
        'ews_waiting_list_9',
        'ppt_members',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'property_type')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('property_type', 100)->default('flat');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'property_type')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('property_type');
                });
            }
        }
    }
};
