<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('physical_possession_documents', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE physical_possession_documents MODIFY application_id BIGINT UNSIGNED NULL');
        }

        Schema::table('physical_possession_documents', function (Blueprint $table) {
            $table->foreign('application_id')
                ->references('id')
                ->on('physical_possession_applications')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean('is_verified')->default(false)->after('mime_type');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
        });
    }

    public function down(): void
    {
        Schema::table('physical_possession_documents', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'is_verified', 'verified_at']);
            $table->dropForeign(['application_id']);
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE physical_possession_documents MODIFY application_id BIGINT UNSIGNED NOT NULL');
        }

        Schema::table('physical_possession_documents', function (Blueprint $table) {
            $table->foreign('application_id')
                ->references('id')
                ->on('physical_possession_applications')
                ->cascadeOnDelete();
        });
    }
};
