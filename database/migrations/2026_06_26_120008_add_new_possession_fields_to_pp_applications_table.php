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
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->string('physical_possession_status')->nullable()->after('status');
            $table->date('possession_date')->nullable()->after('physical_possession_status');
            $table->string('meeting_slot')->nullable()->after('possession_date');
            $table->string('plot_image')->nullable()->after('meeting_slot');
            $table->string('latitude')->nullable()->after('plot_image');
            $table->string('longitude')->nullable()->after('latitude');
            $table->dateTime('image_capture_datetime')->nullable()->after('longitude');
            $table->string('possession_certificate')->nullable()->after('image_capture_datetime');
            $table->unsignedBigInteger('verified_by')->nullable()->after('possession_certificate');
            $table->dateTime('verified_at')->nullable()->after('verified_by');

            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'physical_possession_status',
                'possession_date',
                'meeting_slot',
                'plot_image',
                'latitude',
                'longitude',
                'image_capture_datetime',
                'possession_certificate',
                'verified_by',
                'verified_at',
            ]);
        });
    }
};
