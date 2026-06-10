<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('district_officers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->integer('district_id')->nullable();
            $table->string('district_name');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('district_id')->references('DistrictId')->on('districts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('district_officers');
    }
};
