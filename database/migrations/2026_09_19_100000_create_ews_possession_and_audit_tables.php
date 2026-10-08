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
        // 1. Add possession status columns to ews_allotted_8 (Card 8 table)
        if (Schema::hasTable('ews_allotted_8')) {
            Schema::table('ews_allotted_8', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_allotted_8', 'is_possession_given')) {
                    $table->tinyInteger('is_possession_given')->default(0)->index()->after('phase')
                          ->comment('0: Pending, 1: Possession Given');
                }
                if (!Schema::hasColumn('ews_allotted_8', 'possession_status')) {
                    $table->string('possession_status', 20)->default('PENDING')->index()->after('is_possession_given')
                          ->comment('PENDING, GIVEN');
                }
            });
        }

        // 2. Create ews_beneficiary_possessions table
        if (!Schema::hasTable('ews_beneficiary_possessions')) {
            Schema::create('ews_beneficiary_possessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('beneficiary_id')->index();
                $table->string('application_number', 100)->index();
                $table->string('flat_no', 100)->nullable()->index();
                $table->unsignedBigInteger('stp_user_id')->index();
                $table->string('possession_status', 20)->default('PENDING')->index()->comment('GIVEN, PENDING');
                $table->string('possession_letter_path', 255)->nullable();
                $table->string('possession_letter_original_name', 255)->nullable();
                $table->string('beneficiary_flat_photo_path', 255)->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->text('remarks')->nullable();
                $table->timestamp('possession_given_at')->nullable();
                $table->timestamps();
            });
        }

        // 3. Create ews_possession_audit_logs table
        if (!Schema::hasTable('ews_possession_audit_logs')) {
            Schema::create('ews_possession_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('possession_id')->nullable()->index();
                $table->unsignedBigInteger('beneficiary_id')->index();
                $table->string('application_number', 100)->index();
                $table->string('action', 50)->comment('SUBMIT_POSSESSION, STATUS_UPDATE');
                $table->string('old_status', 30)->nullable();
                $table->string('new_status', 30);
                $table->unsignedBigInteger('stp_user_id')->index();
                $table->string('stp_user_name', 150)->nullable();
                $table->string('latitude', 50)->nullable();
                $table->string('longitude', 50)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->json('payload_snapshot')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ews_possession_audit_logs');
        Schema::dropIfExists('ews_beneficiary_possessions');

        if (Schema::hasTable('ews_allotted_8')) {
            Schema::table('ews_allotted_8', function (Blueprint $table) {
                if (Schema::hasColumn('ews_allotted_8', 'possession_status')) {
                    $table->dropColumn('possession_status');
                }
                if (Schema::hasColumn('ews_allotted_8', 'is_possession_given')) {
                    $table->dropColumn('is_possession_given');
                }
            });
        }
    }
};
