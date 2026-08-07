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
        Schema::create('all_ews_data_1', function (Blueprint $table) {
            $table->id();
            $table->string('aadhar_no', 50)->nullable()->index();
            $table->text('address')->nullable();
            $table->text('age')->nullable();
            $table->text('application_number')->nullable();
            $table->text('are_youapensioner')->nullable();
            $table->text('bt_name')->nullable();
            $table->text('capture_photo_of_family_and_house')->nullable();
            $table->text('capture_video_of_family_and_house')->nullable();
            $table->text('coordinates_of_current_house_address')->nullable();
            $table->text('date_of_birth')->nullable();
            $table->text('do_you_have_an_air_conditioner')->nullable();
            $table->text('do_you_own_any_property_or_house_across_india')->nullable();
            $table->text('electricity_bill_account_no')->nullable();
            $table->text('father_aadhaar_number')->nullable();
            $table->text('father_age')->nullable();
            $table->text('father_is_alive')->nullable();
            $table->text('father_monthly_income')->nullable();
            $table->text('father_name')->nullable();
            $table->text('father_occupation')->nullable();
            $table->text('father_vehicle_registration_number')->nullable();
            $table->text('father_vehicle_type')->nullable();
            $table->text('fathers_full_name')->nullable();
            $table->text('full_name')->nullable();
            $table->text('gender')->nullable();
            $table->text('house_ownership')->nullable();
            $table->text('mobile_number')->nullable();
            $table->text('monthly_income')->nullable();
            $table->text('mother_aadhaar_number')->nullable();
            $table->text('mother_age')->nullable();
            $table->text('mother_is_alive')->nullable();
            $table->text('mother_monthly_income')->nullable();
            $table->text('mother_name')->nullable();
            $table->text('mother_occupation')->nullable();
            $table->text('mother_vehicle_registration_number')->nullable();
            $table->text('mother_vehicle_type')->nullable();
            $table->text('number_of_daughters')->nullable();
            $table->text('number_of_family_members')->nullable();
            $table->text('number_of_sisters')->nullable();
            $table->text('number_of_sons')->nullable();
            $table->text('number_of_unmarried_brothers')->nullable();
            $table->text('occupation_source_of_income')->nullable();
            $table->text('property_id')->nullable();
            $table->text('relation_of_father_with_vehicle_owner')->nullable();
            $table->text('relation_of_mother_with_vehicle_owner')->nullable();
            $table->text('relation_of_wife_with_vehicle_owner')->nullable();
            $table->text('relation_with_electricity_owner')->nullable();
            $table->text('relation_with_landlord')->nullable();
            $table->text('relation_with_property_owner')->nullable();
            $table->text('relation_with_vehicle_owner')->nullable();
            $table->text('rent_amount')->nullable();
            $table->text('rent_paid')->nullable();
            $table->text('spouse_aadhaar_number')->nullable();
            $table->text('spouse_age')->nullable();
            $table->text('spouse_monthly_income')->nullable();
            $table->text('spouse_name')->nullable();
            $table->text('spouse_occupation')->nullable();
            $table->text('spouse_vehicle_registration_number')->nullable();
            $table->text('spouse_vehicle_type')->nullable();
            $table->text('status')->nullable();
            $table->text('surveyor_map_key')->nullable();
            $table->text('type_of_house')->nullable();
            $table->text('type_of_vehicle')->nullable();
            $table->text('vehicle_ownership')->nullable();
            $table->text('vehicle_registration_number')->nullable();
            $table->text('ward_no')->nullable();
            $table->text('do_you_have_spouce')->nullable();
            $table->text('exclusion')->nullable();
            $table->text('IncomeVerified')->nullable();
            $table->text('MaritalStatus')->nullable();
            $table->text('caste')->nullable();
            $table->string('secure_id', 32)->nullable()->unique();
            $table->string('dist_name')->nullable();
            $table->unsignedBigInteger('dist_id')->nullable();
            $table->string('member_id', 50)->nullable()->index();
            $table->unsignedBigInteger('ppt_member_id')->nullable()->index();
            $table->string('property_type', 100)->default('flat');
            $table->string('verify_In_survey_app', 10)->nullable()->index();
            $table->tinyInteger('ppp_exclusion')->nullable()->index();
            $table->tinyInteger('property_in_india')->nullable()->index();
            $table->tinyInteger('house_ownership')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_ews_data_1');
    }
};
