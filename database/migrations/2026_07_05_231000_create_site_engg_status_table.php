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
        Schema::create('site_engg_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('physical_possession_applications')->cascadeOnDelete();
            $table->unsignedBigInteger('possession_id')->nullable();
            $table->unsignedBigInteger('asset_id')->nullable();
            $table->unsignedBigInteger('private_purchaser_id')->nullable();
            $table->string('ppp_id')->nullable();
            $table->string('member_id')->nullable();
            $table->string('mobile', 15)->nullable();
            $table->unsignedBigInteger('flat_id')->nullable();
            $table->string('application_number');
            $table->unsignedBigInteger('site_engg_user_id');
            $table->string('site_engg_name');
            $table->string('site_engg_email')->nullable();
            $table->string('site_engg_mobile')->nullable();
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
        Schema::dropIfExists('site_engg_status');
    }
};
