<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The list of tables to add the 'phase' column to.
     */
    protected array $tables = [
        'adc_not_verified',
        'all_ews_data_1',
        'all_ews_data_544',
        'aws_flats_crid',
        'ews_allotted_8',
        'ews_bookings_7',
        'ews_eligible_6',
        'ews_eligible_draw_list_5',
        'ews_house_ownership_reject_4',
        'ews_reject_property_in_india_3',
        'ews_reject_ppp_exclusion_2',
        'ews_waiting_list_9',
        'ppt_members'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET SESSION innodb_strict_mode=0;');

        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                if (!Schema::hasColumn($tableName, 'phase')) {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->integer('phase')->default(1)->nullable();
                    });
                }
            }
        }

        DB::statement('SET SESSION innodb_strict_mode=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET SESSION innodb_strict_mode=0;');

        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                if (Schema::hasColumn($tableName, 'phase')) {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropColumn('phase');
                    });
                }
            }
        }

        DB::statement('SET SESSION innodb_strict_mode=1;');
    }
};
