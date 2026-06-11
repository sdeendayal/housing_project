<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_possession_applications', function (Blueprint $table) {
            $table->id();
            $table->char('secure_id', 32)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('private_purchaser_id')->nullable();
            $table->string('ppp_id', 50)->nullable();
            $table->string('member_id', 50)->nullable();
            $table->string('slip_id')->unique();
            $table->string('application_number')->unique();
            $table->integer('district_id')->nullable();
            $table->string('district_name')->nullable();
            $table->string('mobile', 15);
            $table->string('applicant_name');
            $table->string('father_name')->nullable();
            $table->text('address')->nullable();
            $table->text('registration_details')->nullable();
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('district_officers')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('district_id')->references('DistrictId')->on('districts')->nullOnDelete();
            $table->foreign('private_purchaser_id')->references('PrivatePurchaserId')->on('property_private_purchasers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_possession_applications');
    }
};
