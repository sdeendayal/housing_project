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
        Schema::create('mmgay_possession_bdo_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('mmgay_possession_applications')->cascadeOnDelete();
            $table->unsignedBigInteger('possession_id')->nullable();
            $table->string('ppp_id')->nullable();
            $table->string('member_id')->nullable();
            $table->string('mobile', 15)->nullable();
            $table->unsignedBigInteger('flat_id')->nullable();
            $table->string('application_number');
            $table->unsignedBigInteger('bdo_user_id');
            $table->string('bdo_name');
            $table->string('bdo_email')->nullable();
            $table->string('bdo_mobile')->nullable();
            $table->string('status');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mmgay_possession_bdo_status');
    }
};
