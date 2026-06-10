<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_due', function (Blueprint $table) {
            $table->integer('DueInstallmentId')->primary();
            $table->integer('PropertyAuctionId');
            $table->integer('AssetId');
            $table->date('OfferOfPossessionDate')->nullable();
            $table->integer('InstallmentNumber');
            $table->date('DueDate');
            $table->integer('RunningBalance')->default(0);
            $table->integer('EMIAmount')->default(0);
            $table->integer('PrincipleAmount')->default(0);
            $table->integer('InterestAmount')->default(0);
            $table->integer('GSTAmount')->default(0);
            $table->integer('InsuranceAmout')->default(0);
            $table->decimal('DueAmount', 15, 2)->default(0);
            $table->integer('RunningClosingBalance')->default(0);
            $table->date('LastSettledDate')->nullable();
            $table->integer('CompanyId');
            $table->dateTime('CreatedDate')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->dateTime('ModifiedDate')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->boolean('IsDeleted')->default(0);
            $table->boolean('IsActive')->default(1);
            $table->float('InstallmentPhase')->nullable();
            $table->float('PrincipalBalance')->nullable();

            $table->foreign('PropertyAuctionId')->references('PropertyAuctionId')->on('property_auction_detail')->onDelete('cascade');
            $table->foreign('AssetId')->references('AssetId')->on('property_registration')->onDelete('cascade');
        });

        Schema::create('ledger', function (Blueprint $table) {
            $table->integer('Id')->primary();
            $table->integer('InstallmentNumber');
            $table->date('DueDate');
            $table->integer('PrincipalAmount')->default(0);
            $table->integer('InterestAmount')->default(0);
            $table->integer('GSTAmount')->default(0);
            $table->integer('InsuranceAmount')->default(0);
            $table->integer('EMIAmount')->default(0);
            $table->integer('CalculatedAmount')->default(0);
            $table->integer('PenaltyAmount')->default(0);
            $table->integer('PenaltyRate')->default(0);
            $table->integer('GSTonPenalty')->default(0);
            $table->integer('Payment')->default(0);
            $table->integer('CumulativePenalty')->default(0);
            $table->integer('CumulativeGST')->default(0);
            $table->integer('RemainingBalance')->default(0);
            $table->integer('ConsecutiveMissedPayments')->default(0);
            $table->integer('Payable_amount')->default(0);
            $table->integer('total_gst')->default(0);
            $table->integer('gst_running_bal')->default(0);
            $table->integer('int_on_gst')->default(0);
            $table->integer('int_running_bal')->default(0);
            $table->integer('total_gst_int_payable')->default(0);
            $table->integer('gst_payment')->default(0);
            $table->integer('balance_amount')->default(0);
            $table->integer('CompanyId');
            $table->boolean('Is_Active')->default(1);
            $table->boolean('Is_Deleted')->default(0);
            $table->integer('CreatedBy')->nullable();
            $table->dateTime('CreateDate')->nullable();
            $table->integer('AuthorizedBy')->nullable();
            $table->dateTime('AuthorizedDate')->nullable();
            $table->integer('AssetId');
            $table->float('PaneltyOnAmount')->nullable();
            $table->float('InstallmentPhase')->nullable();
            $table->float('PrincipalBalance')->nullable();

            $table->foreign('AssetId')->references('AssetId')->on('property_registration')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger');
        Schema::dropIfExists('installment_due');
    }
};
