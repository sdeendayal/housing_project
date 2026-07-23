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
        Schema::table('ews_builder_flats', function (Blueprint $table) {
            $table->string('flat_code', 100)->nullable()->after('flat_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ews_builder_flats', function (Blueprint $table) {
            $table->dropColumn('flat_code');
        });
    }
};
