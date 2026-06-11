<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('officer_application_actions', function (Blueprint $table) {
            $table->date('citizen_visit_date')->nullable()->after('district_name');
            $table->text('visit_instructions')->nullable()->after('citizen_visit_date');
        });

        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->date('citizen_visit_date')->nullable()->after('approved_at');
            $table->text('visit_instructions')->nullable()->after('citizen_visit_date');
        });
    }

    public function down(): void
    {
        Schema::table('officer_application_actions', function (Blueprint $table) {
            $table->dropColumn(['citizen_visit_date', 'visit_instructions']);
        });

        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->dropColumn(['citizen_visit_date', 'visit_instructions']);
        });
    }
};
