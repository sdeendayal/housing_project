<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ews_builder_flats', function (Blueprint $table) {
            if (!Schema::hasColumn('ews_builder_flats', 'dist_id')) {
                $table->unsignedBigInteger('dist_id')->nullable()->after('district_id');
            }
            if (!Schema::hasColumn('ews_builder_flats', 'dist_name')) {
                $table->string('dist_name', 255)->nullable()->after('district_name');
            }
        });

        // Sync existing rows
        DB::statement("UPDATE ews_builder_flats SET dist_id = district_id WHERE dist_id IS NULL AND district_id IS NOT NULL");
        DB::statement("UPDATE ews_builder_flats SET dist_name = district_name WHERE dist_name IS NULL AND district_name IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ews_builder_flats', function (Blueprint $table) {
            if (Schema::hasColumn('ews_builder_flats', 'dist_id')) {
                $table->dropColumn('dist_id');
            }
            if (Schema::hasColumn('ews_builder_flats', 'dist_name')) {
                $table->dropColumn('dist_name');
            }
        });
    }
};
