<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Data synchronization in ews_builder_flats before dropping duplicate columns
        if (Schema::hasTable('ews_builder_flats')) {
            // Merge dist_id into district_id
            if (Schema::hasColumn('ews_builder_flats', 'dist_id') && Schema::hasColumn('ews_builder_flats', 'district_id')) {
                DB::statement("UPDATE ews_builder_flats SET district_id = dist_id WHERE district_id IS NULL AND dist_id IS NOT NULL");
            }

            // Merge dist_name into district_name
            if (Schema::hasColumn('ews_builder_flats', 'dist_name') && Schema::hasColumn('ews_builder_flats', 'district_name')) {
                DB::statement("UPDATE ews_builder_flats SET district_name = dist_name WHERE district_name IS NULL AND dist_name IS NOT NULL");
            }

            // Merge is_aproved_by_dtp into is_approved_by_dtp
            if (Schema::hasColumn('ews_builder_flats', 'is_aproved_by_dtp') && Schema::hasColumn('ews_builder_flats', 'is_approved_by_dtp')) {
                DB::statement("UPDATE ews_builder_flats SET is_approved_by_dtp = is_aproved_by_dtp WHERE (is_approved_by_dtp IS NULL OR is_approved_by_dtp = 0) AND is_aproved_by_dtp = 1");
            }

            // 2. Drop legacy indexes that use is_aproved_by_dtp
            try {
                DB::statement("ALTER TABLE ews_builder_flats DROP INDEX ews_flats_zone_dtp_idx");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE ews_builder_flats DROP INDEX ews_flats_dist_dtp_idx");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE ews_builder_flats DROP INDEX ews_flats_dtp_id_idx");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE ews_builder_flats DROP INDEX ews_builder_flats_is_aproved_by_dtp_index");
            } catch (\Throwable $e) {}

            // 3. Drop duplicate columns (dist_id, dist_name, is_aproved_by_dtp)
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                if (Schema::hasColumn('ews_builder_flats', 'dist_id')) {
                    $table->dropColumn('dist_id');
                }
                if (Schema::hasColumn('ews_builder_flats', 'dist_name')) {
                    $table->dropColumn('dist_name');
                }
                if (Schema::hasColumn('ews_builder_flats', 'is_aproved_by_dtp')) {
                    $table->dropColumn('is_aproved_by_dtp');
                }
            });

            // 4. Re-create composite indexes on is_approved_by_dtp
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_builder_flats', 'is_approved_by_dtp')) {
                    $table->tinyInteger('is_approved_by_dtp')->default(0)->index();
                }
                $table->index(['zone_id', 'is_approved_by_dtp'], 'ews_flats_zone_dtp_idx');
                $table->index(['district_id', 'is_approved_by_dtp'], 'ews_flats_dist_dtp_idx');
                $table->index(['is_approved_by_dtp', 'id'], 'ews_flats_dtp_id_idx');
            });
        }

        // 5. Create ews_stp_approvals table for dedicated audit trail of every STP action
        if (!Schema::hasTable('ews_stp_approvals')) {
            Schema::create('ews_stp_approvals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('flat_id')->index();
                $table->string('flat_code', 100)->nullable()->index();
                $table->unsignedBigInteger('stp_user_id')->nullable()->index();
                $table->string('stp_user_name', 255)->nullable();
                $table->string('stp_role', 50)->nullable();
                $table->unsignedBigInteger('zone_id')->nullable()->index();
                $table->string('zone_name', 100)->nullable();
                $table->unsignedBigInteger('district_id')->nullable()->index();
                $table->string('district_name', 100)->nullable();
                $table->string('action', 50)->default('APPROVED'); // APPROVED, REVOKED, REJECTED, RECONCILED, UPDATED
                $table->tinyInteger('status')->default(1); // 1 = Approved, 0 = Pending/Revoked, etc.
                $table->text('remarks')->nullable();
                $table->json('metadata')->nullable();
                $table->string('ip_address', 50)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        // 6. Backfill historical STP actions into ews_stp_approvals
        try {
            // First backfill from ews_flat_audit_logs where action involves DTP or STP
            if (Schema::hasTable('ews_flat_audit_logs')) {
                $auditEntries = DB::table('ews_flat_audit_logs')
                    ->where(function($q) {
                        $q->where('action', 'LIKE', '%DTP%')
                          ->orWhere('action', 'LIKE', '%STP%');
                    })
                    ->get();

                foreach ($auditEntries as $entry) {
                    $flat = DB::table('ews_builder_flats')->where('id', $entry->flat_id)->first();
                    $isApproved = str_contains($entry->action, 'APPROVED');

                    DB::table('ews_stp_approvals')->insert([
                        'flat_id' => $entry->flat_id,
                        'flat_code' => $entry->flat_code ?? ($flat ? $flat->flat_code : null),
                        'stp_user_id' => $entry->user_id,
                        'stp_user_name' => $entry->user_name,
                        'stp_role' => $entry->user_role ?? 'ews_stp',
                        'zone_id' => $flat ? $flat->zone_id : null,
                        'zone_name' => $flat ? $flat->zone_name : null,
                        'district_id' => $flat ? $flat->district_id : null,
                        'district_name' => $flat ? $flat->district_name : null,
                        'action' => $entry->action,
                        'status' => $isApproved ? 1 : 0,
                        'remarks' => $entry->summary,
                        'metadata' => $entry->changed_fields,
                        'ip_address' => $entry->ip_address,
                        'user_agent' => $entry->user_agent,
                        'created_at' => $entry->created_at,
                        'updated_at' => $entry->updated_at,
                    ]);
                }
            }

            // Also check if any flat in ews_builder_flats is marked approved but has no entry in ews_stp_approvals
            $flatsWithApproval = DB::table('ews_builder_flats')
                ->where(function($q) {
                    $q->where('is_approved_by_dtp', 1)
                      ->orWhereNotNull('dtp_approved_at');
                })
                ->get();

            foreach ($flatsWithApproval as $flat) {
                $exists = DB::table('ews_stp_approvals')->where('flat_id', $flat->id)->exists();
                if (!$exists) {
                    DB::table('ews_stp_approvals')->insert([
                        'flat_id' => $flat->id,
                        'flat_code' => $flat->flat_code,
                        'stp_user_id' => $flat->dtp_approved_by,
                        'stp_user_name' => $flat->dtp_user_name ?? 'STP Officer',
                        'stp_role' => 'ews_stp',
                        'zone_id' => $flat->zone_id,
                        'zone_name' => $flat->zone_name,
                        'district_id' => $flat->district_id,
                        'district_name' => $flat->district_name,
                        'action' => 'DTP_APPROVED',
                        'status' => 1,
                        'remarks' => $flat->dtp_approval_remarks ?? 'Approved by STP Competent Authority',
                        'metadata' => json_encode(['migrated' => true]),
                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'System Migration Backfill',
                        'created_at' => $flat->dtp_approved_at ?: now(),
                        'updated_at' => $flat->dtp_approved_at ?: now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ews_builder_flats')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_builder_flats', 'dist_id')) {
                    $table->unsignedBigInteger('dist_id')->nullable()->after('district_id');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'dist_name')) {
                    $table->string('dist_name', 255)->nullable()->after('district_name');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'is_aproved_by_dtp')) {
                    $table->tinyInteger('is_aproved_by_dtp')->default(0)->index()->after('is_approved_by_dtp');
                }
            });
        }

        Schema::dropIfExists('ews_stp_approvals');
    }
};

