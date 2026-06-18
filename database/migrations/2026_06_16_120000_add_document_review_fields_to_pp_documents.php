<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('physical_possession_documents', function (Blueprint $table) {
            $table->string('review_status', 20)->default('pending')->after('verified_at');
            $table->text('officer_remarks')->nullable()->after('review_status');
            $table->dateTime('returned_at')->nullable()->after('officer_remarks');
            $table->foreignId('returned_by')->nullable()->after('returned_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('physical_possession_documents', function (Blueprint $table) {
            $table->dropForeign(['returned_by']);
            $table->dropColumn(['review_status', 'officer_remarks', 'returned_at', 'returned_by']);
        });
    }
};
