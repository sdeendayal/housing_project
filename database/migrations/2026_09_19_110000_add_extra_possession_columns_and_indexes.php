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
        // 1. Add possession_given_at to ews_allotted_8 for fast reporting queries
        if (Schema::hasTable('ews_allotted_8')) {
            Schema::table('ews_allotted_8', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_allotted_8', 'possession_given_at')) {
                    $table->timestamp('possession_given_at')->nullable()->index()->after('possession_status');
                }
            });
        }

        // 2. Add extra future-proof metadata fields to ews_beneficiary_possessions
        if (Schema::hasTable('ews_beneficiary_possessions')) {
            Schema::table('ews_beneficiary_possessions', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'citizen_name')) {
                    $table->string('citizen_name', 150)->nullable()->after('application_number');
                }
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'citizen_mobile')) {
                    $table->string('citizen_mobile', 20)->nullable()->after('citizen_name');
                }
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'district_name')) {
                    $table->string('district_name', 100)->nullable()->after('flat_no');
                }
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'project_name')) {
                    $table->string('project_name', 255)->nullable()->after('district_name');
                }
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'app_version')) {
                    $table->string('app_version', 50)->nullable()->after('remarks');
                }
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'device_info')) {
                    $table->string('device_info', 255)->nullable()->after('app_version');
                }
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'verified_by_user_id')) {
                    $table->unsignedBigInteger('verified_by_user_id')->nullable()->index()->after('possession_given_at');
                }
                if (!Schema::hasColumn('ews_beneficiary_possessions', 'verified_at')) {
                    $table->timestamp('verified_at')->nullable()->after('verified_by_user_id');
                }
            });
        }

        // 3. Add app_version and device_info to audit logs
        if (Schema::hasTable('ews_possession_audit_logs')) {
            Schema::table('ews_possession_audit_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_possession_audit_logs', 'app_version')) {
                    $table->string('app_version', 50)->nullable()->after('user_agent');
                }
                if (!Schema::hasColumn('ews_possession_audit_logs', 'device_info')) {
                    $table->string('device_info', 255)->nullable()->after('app_version');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ews_possession_audit_logs')) {
            Schema::table('ews_possession_audit_logs', function (Blueprint $table) {
                $table->dropColumn(['app_version', 'device_info']);
            });
        }

        if (Schema::hasTable('ews_beneficiary_possessions')) {
            Schema::table('ews_beneficiary_possessions', function (Blueprint $table) {
                $table->dropColumn([
                    'citizen_name',
                    'citizen_mobile',
                    'district_name',
                    'project_name',
                    'app_version',
                    'device_info',
                    'verified_by_user_id',
                    'verified_at',
                ]);
            });
        }

        if (Schema::hasTable('ews_allotted_8')) {
            Schema::table('ews_allotted_8', function (Blueprint $table) {
                if (Schema::hasColumn('ews_allotted_8', 'possession_given_at')) {
                    $table->dropColumn('possession_given_at');
                }
            });
        }
    }
};
