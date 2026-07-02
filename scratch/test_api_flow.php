<?php

use App\Models\User;
use App\Models\PhysicalPossessionApplication;
use App\Models\ApplicationStatusLog;
use App\Http\Controllers\Api\PpOfficerApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::beginTransaction();

try {
    echo "Starting Mobile API Verification Flow Test...\n";

    // 1. Get Panchkula Site Engineer and authenticate
    $officer = User::where('mobile', '9999900278')->first();
    if (!$officer) {
        throw new Exception("Officer 9999900278 not found!");
    }
    Auth::login($officer);
    echo "Logged in as: " . $officer->name . "\n";

    // 2. Get application
    $application = PhysicalPossessionApplication::where('secure_id', 'e61135c519a6f6e311a4ae655d49fd92')->first();
    if (!$application) {
        throw new Exception("Application e61135c519a6f6e311a4ae655d49fd92 not found!");
    }
    $application->physical_possession_status = 'Slot Selected';
    $application->possession_date = '2026-07-10';
    $application->meeting_slot = '10:00 AM';
    $application->save();

    echo "Initial API application status: " . $application->physical_possession_status . "\n";

    $apiController = new PpOfficerApiController();

    // 3. Test Reschedule API request
    echo "\n--- Testing Reschedule API request ---\n";
    $request1 = Request::create('/api/possession/officer/verify/' . $application->secure_id, 'POST', [
        'action' => 'reschedule'
    ]);
    
    $response1 = $apiController->verifySave($request1, $application);
    $resData1 = json_decode($response1->getContent(), true);

    echo "Reschedule API Response: " . json_encode($resData1, JSON_PRETTY_PRINT) . "\n";
    
    $application->refresh();
    echo "Status after Reschedule API: " . $application->physical_possession_status . "\n";

    // 4. Test Step 1 Site Verification API request
    echo "\n--- Testing Step 1 (Site Verification) API request ---\n";
    
    // Reset status to Slot Selected for Step 1 test
    $application->physical_possession_status = 'Slot Selected';
    $application->save();

    // Create mock plot image upload file
    $tempImagePath = tempnam(sys_get_temp_dir(), 'img');
    imagejpeg(imagecreatetruecolor(10, 10), $tempImagePath);
    $uploadedFile = new \Illuminate\Http\UploadedFile($tempImagePath, 'test_plot.jpg', 'image/jpeg', null, true);

    $request2 = Request::create('/api/possession/officer/verify/' . $application->secure_id, 'POST', [
        'latitude' => '30.7333',
        'longitude' => '76.7794',
        'remarks' => 'API Step 1 test remarks.'
    ], [], [
        'plot_image' => $uploadedFile
    ]);

    $response2 = $apiController->verifySave($request2, $application);
    $resData2 = json_decode($response2->getContent(), true);

    echo "Step 1 API Response: " . json_encode($resData2, JSON_PRETTY_PRINT) . "\n";
    
    $application->refresh();
    echo "Status after Step 1 API: " . $application->physical_possession_status . "\n";

    // 5. Test Step 2 E-Possession Verification API request
    echo "\n--- Testing Step 2 (E-Possession) API request ---\n";

    // Create mock PDFs
    $tempPdf1Path = tempnam(sys_get_temp_dir(), 'pdf');
    file_put_contents($tempPdf1Path, '%PDF-1.4');
    $certFile = new \Illuminate\Http\UploadedFile($tempPdf1Path, 'signed_cert.pdf', 'application/pdf', null, true);

    $tempPdf2Path = tempnam(sys_get_temp_dir(), 'pdf');
    file_put_contents($tempPdf2Path, '%PDF-1.4');
    $seFile = new \Illuminate\Http\UploadedFile($tempPdf2Path, 'site_engg.pdf', 'application/pdf', null, true);

    $request3 = Request::create('/api/possession/officer/verify/' . $application->secure_id, 'POST', [], [], [
        'possession_certificate' => $certFile,
        'site_engineer_file' => $seFile
    ]);

    $response3 = $apiController->verifySave($request3, $application);
    $resData3 = json_decode($response3->getContent(), true);

    echo "Step 2 API Response: " . json_encode($resData3, JSON_PRETTY_PRINT) . "\n";

    $application->refresh();
    echo "Status after Step 2 API: " . $application->physical_possession_status . "\n";
    echo "Overall Application Status: " . $application->status . "\n";

    // 6. Test PDF download certificate API request (base64)
    echo "\n--- Testing PDF Certificate Download API request ---\n";
    $request4 = Request::create('/api/possession/officer/download-certificate/' . $application->secure_id, 'GET', [
        'base64' => 1
    ]);
    $response4 = $apiController->downloadCertificate($request4, $application);
    $resData4 = json_decode($response4->getContent(), true);

    echo "Download API Response Success: " . ($resData4['success'] ? 'TRUE' : 'FALSE') . "\n";
    echo "Base64 Length: " . strlen($resData4['pdf_base64']) . "\n";

    echo "\nAll Mobile API steps completed successfully! Rolling back database transaction...\n";
} catch (Exception $e) {
    echo "TEST FAILED: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "Database rolled back successfully.\n";
}
