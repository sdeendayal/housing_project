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
        if (Schema::hasTable('ews_builder_flats') && !Schema::hasColumn('ews_builder_flats', 'secure_id')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                $table->string('secure_id', 64)->nullable()->unique()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ews_builder_flats') && Schema::hasColumn('ews_builder_flats', 'secure_id')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                $table->dropColumn('secure_id');
            });
        }
    }
};
