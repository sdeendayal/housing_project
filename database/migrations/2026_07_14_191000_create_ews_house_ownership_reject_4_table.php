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
        Schema::create('ews_house_ownership_reject_4', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->nullable();
            $table->string('full_name')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('secure_id', 32)->nullable()->unique();
            $table->string('dist_name')->nullable();
            $table->unsignedBigInteger('dist_id')->nullable();
            $table->string('property_type', 100)->default('flat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ews_house_ownership_reject_4');
    }
};
