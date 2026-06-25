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
        Schema::table('users', function (Blueprint $table) {
            $table->string('scheme', 100)->nullable()->after('role');
        });

        // Update all existing citizen users to MMSAY
        DB::table('users')
            ->where('role', 'citizen')
            ->update(['scheme' => 'MMSAY']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('scheme');
        });
    }
};
