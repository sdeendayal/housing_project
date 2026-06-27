<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('physical_possession_applications', 'citizen_visit_date')) {
            return;
        }

        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->dateTime('citizen_visit_date')->nullable()->change();
        });

        Schema::table('officer_application_actions', function (Blueprint $table) {
            $table->dateTime('citizen_visit_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('physical_possession_applications', 'citizen_visit_date')) {
            return;
        }

        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->date('citizen_visit_date')->nullable()->change();
        });

        Schema::table('officer_application_actions', function (Blueprint $table) {
            $table->date('citizen_visit_date')->nullable()->change();
        });
    }
};
