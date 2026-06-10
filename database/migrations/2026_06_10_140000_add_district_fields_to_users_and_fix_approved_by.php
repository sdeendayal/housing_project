<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('district_id')->nullable()->after('role');
            $table->string('district_name')->nullable()->after('district_id');
        });

        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
        });

        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
        });

        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->foreign('approved_by')->references('id')->on('district_officers')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['district_id', 'district_name']);
        });
    }
};
