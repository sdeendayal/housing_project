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
        Schema::create('mmsay_eligible_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('mc_name', 100)->nullable()->index();
            $table->string('application_number', 100)->nullable()->index();
            $table->string('registration_number', 100)->nullable()->index();
            $table->string('pmay_id', 150)->nullable()->index();
            $table->string('family_id', 100)->nullable()->index();
            $table->string('full_name', 255)->nullable();
            $table->string('father_husband_name', 255)->nullable();
            $table->string('spouse_name', 255)->nullable();
            $table->string('mobile_number', 100)->nullable()->index();
            $table->string('marital_status', 100)->nullable();
            $table->string('caste', 255)->nullable();
            $table->string('category', 255)->nullable();
            $table->text('sector')->nullable();
            $table->string('plot_number', 255)->nullable();
            $table->string('ward_no', 255)->nullable();
            $table->string('town_city', 255)->nullable();
            $table->string('district_name', 255)->nullable();
            $table->string('branch_name', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('pmay_benefit', 255)->nullable();
            $table->string('own_residence', 255)->nullable();
            $table->text('physical_verification')->nullable();
            $table->text('status_reason')->nullable();
            $table->text('remarks')->nullable();
            $table->string('secure_id', 32)->nullable()->unique();
            $table->integer('phase')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mmsay_eligible_beneficiaries');
    }
};
