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
        if (!Schema::hasTable('ews_flat_audit_logs')) {
            Schema::create('ews_flat_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('flat_id')->index();
                $table->string('flat_code', 100)->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('user_name', 255)->nullable();
                $table->string('user_role', 50)->nullable();
                $table->string('action', 50)->default('UPDATED');
                $table->text('summary')->nullable();
                $table->json('changed_fields')->nullable();
                $table->string('ip_address', 50)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ews_flat_audit_logs');
    }
};
