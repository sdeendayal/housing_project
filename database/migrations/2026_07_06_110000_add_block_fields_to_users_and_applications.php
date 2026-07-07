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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('block_id')->nullable()->after('district_name');
            $table->string('block_name')->nullable()->after('block_id');
        });

        Schema::table('mmgay_possession_applications', function (Blueprint $table) {
            $table->integer('block_id')->nullable()->after('district_name');
            $table->string('block_name')->nullable()->after('block_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['block_id', 'block_name']);
        });

        Schema::table('mmgay_possession_applications', function (Blueprint $table) {
            $table->dropColumn(['block_id', 'block_name']);
        });
    }
};
