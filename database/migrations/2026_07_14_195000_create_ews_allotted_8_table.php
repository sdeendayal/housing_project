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
        Schema::create('ews_allotted_8', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->nullable();
            $table->string('full_name')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('flat_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ews_allotted_8');
    }
};
