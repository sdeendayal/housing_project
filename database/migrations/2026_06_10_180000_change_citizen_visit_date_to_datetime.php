<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('physical_possession_applications', 'citizen_visit_date')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE physical_possession_applications MODIFY citizen_visit_date DATETIME NULL');
            DB::statement('ALTER TABLE officer_application_actions MODIFY citizen_visit_date DATETIME NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('physical_possession_applications', 'citizen_visit_date')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE physical_possession_applications MODIFY citizen_visit_date DATE NULL');
            DB::statement('ALTER TABLE officer_application_actions MODIFY citizen_visit_date DATE NULL');
        }
    }
};
