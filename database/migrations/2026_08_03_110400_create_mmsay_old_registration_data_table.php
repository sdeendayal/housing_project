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
        Schema::create('mmsay_old_registration_data', function (Blueprint $table) {
            $table->id();
            
            $csvPath = database_path('seeders/data/mmsay_old_registration_data/sql.csv');
            if (file_exists($csvPath)) {
                $handle = fopen($csvPath, 'r');
                $headers = fgetcsv($handle);
                fclose($handle);
                
                foreach ($headers as $header) {
                    $header = trim($header, "\xEF\xBB\xBF \t\n\r\0\x0B");
                    if (empty($header) || in_array($header, ['id', 'created_at', 'updated_at'])) {
                        continue;
                    }
                    $table->text($header)->nullable();
                }
            } else {
                // Fallback list of headers if CSV doesn't exist
                $fallbackHeaders = [
                    'scheme_data_id', 'family_id', 'memberID', 'property_category', 'property_details',
                    'down_payment', 'loan_section_amount', 'emi_amount', 'application_number', 'is_approve',
                    'fullName', 'fatherFullName', 'motherFullName', 'gender', 'dob', 'age', 'mobileNo',
                    'aadhaarNo', 'ruralUrban', 'casteCategoryName', 'subCaste_name', 'occupationName',
                    'familyIncome', 'districtName', 'btName', 'wvName', 'pinCode'
                ];
                foreach ($fallbackHeaders as $header) {
                    $table->text($header)->nullable();
                }
            }
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mmsay_old_registration_data');
    }
};
