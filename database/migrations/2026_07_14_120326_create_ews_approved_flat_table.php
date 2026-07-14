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
        Schema::create('ews_approved_flat', function (Blueprint $table) {
            $table->unsignedBigInteger('Id')->primary();
            $table->string('FLAG')->nullable();
            $table->string('ApplicationID')->nullable();
            $table->string('DateOfBenefit')->nullable();
            $table->text('DepartmentName')->nullable();
            $table->string('District')->nullable();
            $table->text('JobLoanName')->nullable();
            $table->string('Member_ID')->nullable();
            $table->string('membername')->nullable();
            $table->string('PPP_ID')->nullable();
            $table->string('ProjectName')->nullable();
            $table->string('AmountOfBenefit')->nullable();
            $table->string('BenefitDetailsInCash')->nullable();
            $table->text('ServiceSchemeName')->nullable();
            $table->string('CenterState')->nullable();
            $table->string('EligibilityStatus')->nullable();
            $table->string('NatureOfBenefit')->nullable();
            $table->string('ServiceScheme')->nullable();
            $table->string('ServiceSchemeCode')->nullable();
            $table->string('Status')->nullable();
            $table->text('Flat_plotno')->nullable();
            $table->text('Builder_Name')->nullable();
            $table->text('Builder_Addres')->nullable();
            $table->string('DateOfApproval')->nullable();
            $table->string('AllocationMonth')->nullable();
            $table->string('AllocationYear')->nullable();
            $table->text('BenefitDetailsInKind')->nullable();
            $table->string('UnitOfBenefit')->nullable();
            $table->string('AmountOfBenefitInKind')->nullable();
            $table->string('commcode')->nullable();
            $table->string('SrnNo')->nullable();
            $table->string('SessionYear')->nullable();
            $table->string('companyid')->nullable();
            $table->string('createddate')->nullable();
            $table->string('createdby')->nullable();
            $table->string('new_status')->nullable();
            $table->string('IsPushed')->nullable();
            $table->string('PushedDate')->nullable();
            $table->string('IsActive')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ews_approved_flat');
    }
};
