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
        Schema::create('adc_not_verified', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->nullable()->index();
            $table->string('full_name')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->string('mobile_number')->nullable()->index();
            $table->string('status')->nullable();
            $table->string('priority')->nullable();
            $table->string('category')->nullable();
            $table->string('secure_id', 32)->nullable()->unique();
            $table->string('dist_name')->nullable();
            $table->unsignedBigInteger('dist_id')->nullable();
            $table->string('property_type', 100)->default('flat');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adc_not_verified');
    }
};
