<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET SESSION innodb_strict_mode=0;');

        Schema::create('all_ews_data_544', function (Blueprint $table) {
            $table->engine = 'InnoDB ROW_FORMAT=DYNAMIC';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
            $table->id();

            // Unique fields from the Excel file
            $table->text('LocationnName')->nullable();
            $table->text('BranchId')->nullable();
            $table->string('PrivatePurchaserId', 100)->nullable()->index();
            $table->text('Flat_PlotNo')->nullable();
            $table->string('ApplicationNo', 100)->nullable()->index();
            $table->text('PrivatePurchaserName')->nullable();
            $table->text('PurchaserFatherName')->nullable();
            $table->text('PPP_Id')->nullable();
            $table->text('Member_ID')->nullable();
            $table->string('MobileNo', 100)->nullable()->index();
            $table->text('BookingOpen')->nullable();
            $table->string('Paid', 50)->nullable()->index();
            $table->string('Allotment', 50)->nullable()->index();
            $table->text('AccountName')->nullable();
            $table->text('AccountNumber')->nullable();
            $table->text('IFSCCode')->nullable();
            $table->text('BankName')->nullable();
            $table->text('Id_2')->nullable();
            $table->text('InvoiceId')->nullable();
            $table->text('InvoiceNo')->nullable();
            $table->text('CustomerId')->nullable();
            $table->text('CompanyId')->nullable();
            $table->text('Amount')->nullable();
            $table->text('GstAmount')->nullable();
            $table->text('RegularAmount')->nullable();
            $table->text('PaymentUniqueRefNo')->nullable();
            $table->text('PaymentStatus')->nullable();
            $table->text('PaymentResponse')->nullable();
            $table->text('PaymentMode')->nullable();
            $table->text('PaymentInitiateDate')->nullable();
            $table->text('SettlementDate')->nullable();
            $table->text('UTRNo')->nullable();
            $table->text('CreatedDate')->nullable();

            $table->text('FLAG')->nullable();
            $table->text('ApplicationID')->nullable();
            $table->text('DateOfBenefit')->nullable();
            $table->text('DepartmentName')->nullable();
            $table->text('District')->nullable();
            $table->text('JobLoanName')->nullable();
            $table->text('Member_ID_2')->nullable();
            $table->text('membername')->nullable();
            $table->text('PPP_ID_2')->nullable();
            $table->text('ProjectName')->nullable();
            $table->text('AmountOfBenefit')->nullable();
            $table->text('BenefitDetailsInCash')->nullable();
            $table->text('ServiceSchemeName')->nullable();
            $table->text('CenterState')->nullable();
            $table->text('EligibilityStatus')->nullable();
            $table->text('NatureOfBenefit')->nullable();
            $table->text('ServiceScheme')->nullable();
            $table->text('ServiceSchemeCode')->nullable();
            $table->text('Status')->nullable();
            $table->text('Flat_plotno_2')->nullable();
            $table->text('Builder_Name')->nullable();
            $table->text('Builder_Addres')->nullable();
            $table->text('DateOfApproval')->nullable();
            $table->text('AllocationMonth')->nullable();
            $table->text('AllocationYear')->nullable();
            $table->text('BenefitDetailsInKind')->nullable();
            $table->text('UnitOfBenefit')->nullable();
            $table->text('AmountOfBenefitInKind')->nullable();
            $table->text('commcode')->nullable();
            $table->text('SrnNo')->nullable();
            $table->text('SessionYear')->nullable();

            $table->text('IsPushed')->nullable();
            $table->text('PushedDate')->nullable();
            $table->text('IsActive')->nullable();
            $table->text('SourceBankId')->nullable();
            $table->text('SourceType')->nullable();
            $table->text('SourceId')->nullable();
            $table->text('BankId')->nullable();
            $table->text('BankBranch')->nullable();
            $table->text('BankAddress')->nullable();
            $table->text('AccountNumber_2')->nullable();
            $table->text('AccountName_2')->nullable();
            $table->text('AadhaarNo')->nullable();
            $table->text('EFT')->nullable();
            $table->text('IFSCcode_2')->nullable();
            $table->text('DirectDebit')->nullable();
            $table->text('RefundPayment')->nullable();
            $table->text('GPFAccountNo')->nullable();
            $table->text('NPSAccountNo')->nullable();
            $table->text('IsDefaultBank')->nullable();
            $table->text('IsEnabled')->nullable();

            $table->text('AccountType')->nullable();
            $table->text('IsDemandDraft')->nullable();
            $table->text('AccountSubType')->nullable();
            $table->text('BankLedgerId')->nullable();
            $table->text('OverdraftInterestRate')->nullable();
            $table->text('OverdraftLimit')->nullable();

            $table->text('IsBankOnlinePayment')->nullable();
            $table->text('BankCode')->nullable();
            $table->text('BankUId')->nullable();
            $table->text('BankPwd')->nullable();

            $table->text('IFSCCode_3')->nullable();
            $table->text('BranchName')->nullable();
            $table->text('BranchAddress')->nullable();
            $table->text('ContactNo')->nullable();

            $table->text('IsSysGenerated')->nullable();
            $table->text('AssetId')->nullable();
            $table->text('AssetTypeId')->nullable();
            $table->text('AssetName')->nullable();

            $table->text('IsAssetVerified')->nullable();
            $table->text('IsAssetAuctioned')->nullable();
            $table->text('PaymentStatus_2')->nullable();
            $table->text('BalanceAmount')->nullable();
            $table->text('AssetSizeId')->nullable();
            $table->text('AssetSize')->nullable();
            $table->text('PaymentClosingDate')->nullable();
            $table->text('InstatllmentDueAmount')->nullable();
            $table->text('ReasonForClosing')->nullable();
            $table->text('PurchaserType')->nullable();
            $table->text('PurchaserId')->nullable();
            $table->text('PurchaserName')->nullable();

            $table->text('StreetName')->nullable();
            $table->text('ZipCode')->nullable();
            $table->string('MobileNo_2', 100)->nullable()->index();
            $table->text('IsPropertyColonized')->nullable();
            $table->text('TotalIncurredInterestAmount')->nullable();
            $table->text('TotalIncurredPenaltyAmount')->nullable();
            $table->text('TotalinitialAmount')->nullable();
            $table->text('TotalInstallmentPaymentAmount')->nullable();
            $table->text('TotalSaleAmount')->nullable();

            $table->text('ConveyanceDeedDate')->nullable();
            $table->text('ConveyanceDeedDocument')->nullable();
            $table->text('IsConveyanceDeedSubmitted')->nullable();
            $table->text('IsAssetResumed')->nullable();
            $table->text('IsAssetSurrendered')->nullable();
            $table->text('IsLocked')->nullable();
            $table->text('IsDefaulter')->nullable();
            $table->text('AssetCode')->nullable();
            $table->text('IsDistributed')->nullable();
            $table->text('IsAnyComplaint')->nullable();
            $table->text('IsNDCIssued')->nullable();
            $table->text('IsNDCGenerated')->nullable();
            $table->text('IsLedgerServiceRestricted')->nullable();
            $table->text('IsExtensionFeePaid')->nullable();
            $table->text('IsSaleAmountIncrease')->nullable();
            $table->text('IsSaleAmountDecrease')->nullable();
            $table->text('IsPrincipalCheck')->nullable();
            $table->text('SchemeId')->nullable();
            $table->text('IsFSSPartialPayment')->nullable();
            $table->text('IsFSSFullPaymentPaid')->nullable();
            $table->text('ExtensionFeeUnderFss')->nullable();
            $table->text('EAuctionPropertyTypeCheck')->nullable();
            $table->text('TotalRegAmount')->nullable();
            $table->text('ApplicantCategory')->nullable();
            $table->text('TotalInsuranceAmount')->nullable();
            $table->text('TotalGSTAmount')->nullable();
            $table->text('PropertyCategory')->nullable();
            $table->text('PropertySubCategory')->nullable();
            $table->text('OriginalReservationCategory_id')->nullable();
            $table->text('CurrentReservationCategory_id')->nullable();
            $table->text('mode_of_alotment_id')->nullable();
            $table->text('SchemeNumber')->nullable();
            $table->text('Flat_ID')->nullable();
            $table->text('LocationStatus')->nullable();
            $table->text('BuildingPlanSanctionDate')->nullable();
            $table->text('FloorStatus')->nullable();
            $table->text('FloorId')->nullable();
            $table->text('CurrentArea')->nullable();
            $table->text('IncidentalArea')->nullable();
            $table->text('CityBranchidId')->nullable();
            $table->text('IsUnderMortgageStatus')->nullable();
            $table->text('Iscourtcase')->nullable();
            $table->text('CourtCaseDetail')->nullable();
            $table->text('CompanyId_3')->nullable();
            $table->text('IsledgerVerfied')->nullable();
            $table->text('CumulativePenalty')->nullable();
            $table->text('CumulativeGST')->nullable();
            $table->text('Cummulativebalance')->nullable();
            $table->text('ledgerVerfiedModifiedDate')->nullable();
            $table->text('DueDate')->nullable();
            $table->text('IsBalanceDue')->nullable();
            $table->text('ledgerVerfiedBy')->nullable();
            $table->text('PropertyStatus')->nullable();
            $table->string('ApplicationNo_2', 100)->nullable()->index();
            $table->text('UniqueId')->nullable();
            $table->text('LOI_Status')->nullable();
            $table->text('Allotment_Status')->nullable();
            $table->text('Allotment_Date')->nullable();
            $table->text('DocUploadSatatus')->nullable();

            // Custom required columns
            $table->string('secure_id', 32)->nullable()->unique();
            $table->string('dist', 255)->nullable();
            $table->unsignedBigInteger('dist_id')->nullable()->index();
            $table->string('property_type', 100)->default('flat');

            $table->timestamps();
        });

        DB::statement('SET SESSION innodb_strict_mode=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_ews_data_544');
    }
};
