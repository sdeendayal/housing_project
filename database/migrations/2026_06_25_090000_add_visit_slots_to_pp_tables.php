<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->dateTime('visit_slot_1')->nullable()->after('citizen_visit_date');
            $table->dateTime('visit_slot_2')->nullable()->after('visit_slot_1');
            $table->dateTime('visit_slot_3')->nullable()->after('visit_slot_2');
        });

        Schema::table('officer_application_actions', function (Blueprint $table) {
            $table->dateTime('visit_slot_1')->nullable()->after('citizen_visit_date');
            $table->dateTime('visit_slot_2')->nullable()->after('visit_slot_1');
            $table->dateTime('visit_slot_3')->nullable()->after('visit_slot_2');
        });
    }

    public function down(): void
    {
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->dropColumn(['visit_slot_1', 'visit_slot_2', 'visit_slot_3']);
        });

        Schema::table('officer_application_actions', function (Blueprint $table) {
            $table->dropColumn(['visit_slot_1', 'visit_slot_2', 'visit_slot_3']);
        });
    }
};
