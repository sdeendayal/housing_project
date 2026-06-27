<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('otps', function (Blueprint $table) {
            if (! Schema::hasColumn('otps', 'purpose')) {
                $table->string('purpose', 50)->default('login')->after('mobile_number');
            }

            if (! Schema::hasColumn('otps', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('purpose')
                    ->constrained()
                    ->nullOnDelete();
            }
        });

        Schema::table('otps', function (Blueprint $table) {
            if (! $this->indexExists('otps', 'otps_mobile_purpose_verified_idx')) {
                $table->index(['mobile_number', 'purpose', 'verified_at'], 'otps_mobile_purpose_verified_idx');
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        foreach (Schema::getIndexes($table) as $index) {
            if (($index['name'] ?? '') === $indexName) {
                return true;
            }
        }

        return false;
    }

    public function down(): void
    {
        Schema::table('otps', function (Blueprint $table) {
            $table->dropIndex('otps_mobile_purpose_verified_idx');

            if (Schema::hasColumn('otps', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('otps', 'purpose')) {
                $table->dropColumn('purpose');
            }
        });
    }
};
