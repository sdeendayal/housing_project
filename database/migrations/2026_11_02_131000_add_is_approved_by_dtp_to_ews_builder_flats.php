<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ews_builder_flats')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                if (!Schema::hasColumn('ews_builder_flats', 'is_aproved_by_dtp')) {
                    $table->tinyInteger('is_aproved_by_dtp')->default(0)->index()->after('remaining_flats_for_oc');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'is_approved_by_dtp')) {
                    $table->tinyInteger('is_approved_by_dtp')->default(0)->index()->after('is_aproved_by_dtp');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'dtp_approved_at')) {
                    $table->timestamp('dtp_approved_at')->nullable()->after('is_approved_by_dtp');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'dtp_approved_by')) {
                    $table->unsignedBigInteger('dtp_approved_by')->nullable()->after('dtp_approved_at');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'dtp_user_name')) {
                    $table->string('dtp_user_name', 255)->nullable()->after('dtp_approved_by');
                }
                if (!Schema::hasColumn('ews_builder_flats', 'dtp_approval_remarks')) {
                    $table->text('dtp_approval_remarks')->nullable()->after('dtp_user_name');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ews_builder_flats')) {
            Schema::table('ews_builder_flats', function (Blueprint $table) {
                $cols = ['is_aproved_by_dtp', 'is_approved_by_dtp', 'dtp_approved_at', 'dtp_approved_by', 'dtp_user_name', 'dtp_approval_remarks'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('ews_builder_flats', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};

