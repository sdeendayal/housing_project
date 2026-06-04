<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Skip if table already uses the new column names
        if (! Schema::hasTable('otps') || Schema::hasColumn('otps', 'mobile_number')) {
            return;
        }

        // Remove soft deletes if present
        if (Schema::hasColumn('otps', 'deleted_at')) {
            Schema::table('otps', function (Illuminate\Database\Schema\Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        DB::statement('ALTER TABLE otps CHANGE mobile mobile_number VARCHAR(10) NOT NULL');
        DB::statement('ALTER TABLE otps CHANGE attempt_count attempts TINYINT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE otps ADD verified_at TIMESTAMP NULL AFTER expires_at');

        if (Schema::hasColumn('otps', 'is_verified')) {
            DB::statement('UPDATE otps SET verified_at = NOW() WHERE is_verified = 1');
            Schema::table('otps', function (Illuminate\Database\Schema\Blueprint $table) {
                $table->dropColumn('is_verified');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('otps') || ! Schema::hasColumn('otps', 'mobile_number')) {
            return;
        }

        DB::statement('ALTER TABLE otps CHANGE mobile_number mobile VARCHAR(10) NOT NULL');
        DB::statement('ALTER TABLE otps CHANGE attempts attempt_count TINYINT UNSIGNED NOT NULL DEFAULT 0');
        Schema::table('otps', function ($table) {
            $table->boolean('is_verified')->default(false)->after('expires_at');
            $table->dropColumn('verified_at');
        });
    }
};
