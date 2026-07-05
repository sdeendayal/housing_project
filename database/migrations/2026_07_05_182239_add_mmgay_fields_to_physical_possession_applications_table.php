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
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->after('user_id');
            $table->string('scheme')->nullable()->default('MMSAY')->after('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->dropColumn(['owner_id', 'scheme']);
        });
    }
};
