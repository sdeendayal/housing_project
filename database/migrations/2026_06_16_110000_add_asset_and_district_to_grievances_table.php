<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('grievances', function (Blueprint $table) {
            $table->integer('asset_id')->nullable()->after('mobile_number');
            $table->integer('district_id')->nullable()->after('asset_id');
            $table->string('district', 100)->nullable()->after('district_id');
        });
    }

    public function down(): void
    {
        Schema::table('grievances', function (Blueprint $table) {
            $table->dropColumn(['asset_id', 'district_id', 'district']);
        });
    }
};
