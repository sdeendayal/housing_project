<?php

use App\Models\User;
use App\Models\PhysicalPossessionApplication;
use App\Models\ApplicationStatusLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::beginTransaction();

try {
    echo "Starting Two-Step Verification Flow Test...\n";

    // 1. Get Panchkula Site Engineer and authenticate
    $officer = User::where('mobile', '9999900278')->first();
    if (!$officer) {
        throw new Exception("Officer 9999900278 not found!");
    }
    Auth::login($officer);
    echo "Logged in as: " . $officer->name . " (District ID: " . $officer->district_id . ")\n";

    // 2. Find the application in 'Slot Selected' status
    $application = PhysicalPossessionApplication::where('secure_id', 'e61135c519a6f6e311a4ae655d49fd92')->first();
    if (!$application) {
        throw new Exception("Application e61135c519a6f6e311a4ae655d49fd92 not found!");
    }
    echo "Initial Application Status: " . $application->physical_possession_status . "\n";

    // 3. Simulate Step 1: Site Verification Submission
    echo "\n--- Simulating Step 1: Site Verification Submission ---\n";
    
    // Simulate Request validation & save
    $application->latitude = '30.7333';
    $application->longitude = '76.7794';
    $application->image_capture_datetime = now();
    $application->remarks = 'Site verification test remarks. Plot dimensions matching.';
    $application->plot_image = 'possession_uploads/images/plot_test.jpg';
    $oldStatus = $application->physical_possession_status;
    $application->physical_possession_status = 'Site Verified';
    $application->save();

    ApplicationStatusLog::create([
        'application_id' => $application->id,
        'asset_id' => $application->asset_id,
        'old_status' => $oldStatus,
        'new_status' => 'Site Verified',
        'remarks' => 'Site verification details (GPS, Photo with Applicant) submitted by Site Engineer.',
        'changed_by_type' => 'officer',
        'changed_by_id' => $officer->id,
    ]);

    echo "Step 1 Status Saved: " . $application->physical_possession_status . "\n";
    echo "Latitude: " . $application->latitude . "\n";
    echo "Longitude: " . $application->longitude . "\n";
    echo "Remarks: " . $application->remarks . "\n";

    // 4. Simulate Step 2: E-Possession Verification Submission
    echo "\n--- Simulating Step 2: E-Possession Verification Submission ---\n";
    
    $currentStatus = $application->physical_possession_status;
    if ($currentStatus !== 'Site Verified') {
        throw new Exception("Application is not in Site Verified status!");
    }

    // Simulate Request validation & save
    $application->possession_certificate = 'possession_uploads/certificates/cert_test.pdf';
    $application->site_engineer_file = 'possession_uploads/site_engineer/site_test.pdf';
    $application->physical_possession_status = 'Verified';
    $application->status = 'approved';
    $application->verified_by = $officer->id;
    $application->verified_at = now();
    $application->save();

    ApplicationStatusLog::create([
        'application_id' => $application->id,
        'asset_id' => $application->asset_id,
        'old_status' => $currentStatus,
        'new_status' => 'Verified',
        'remarks' => 'Final physical possession documents (Citizen Signed & Site Engineer file) uploaded and verified.',
        'changed_by_type' => 'officer',
        'changed_by_id' => $officer->id,
    ]);

    echo "Step 2 Status Saved: " . $application->physical_possession_status . "\n";
    echo "Overall Application Status: " . $application->status . "\n";
    echo "Signed Cert Path: " . $application->possession_certificate . "\n";
    echo "Site Engg File Path: " . $application->site_engineer_file . "\n";
    echo "Verified By ID: " . $application->verified_by . "\n";

    // 5. Verify the logs
    echo "\n--- Verifying Application Status Logs ---\n";
    $logs = ApplicationStatusLog::where('application_id', $application->id)->get();
    foreach ($logs as $log) {
        echo "Log: " . $log->old_status . " -> " . $log->new_status . " | Remarks: " . $log->remarks . "\n";
    }

    echo "\nAll steps completed successfully in simulation! Rolling back database transaction...\n";
} catch (Exception $e) {
    echo "TEST FAILED: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "Database rolled back successfully.\n";
}
