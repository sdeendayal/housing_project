<?php
/**
 * Script to verify all mobile API endpoints for the Site Engineer (officer) module.
 * It dynamically selects an existing PhysicalPossessionApplication belonging to the officer
 * with mobile number 9999900278 and performs the same verification flow as test_api_flow.php.
 */

// Bootstrap Laravel environment
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\PhysicalPossessionApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

DB::beginTransaction();

try {
    echo "Starting Site Engineer Mobile API Verification...\n";
    // Retrieve officer (Site Engineer) by mobile number
    $officer = User::where('mobile', '9999900278')->first();
    if (!$officer) {
        throw new Exception('Officer with mobile 9999900278 not found!');
    }
    Auth::login($officer);
    echo "Logged in as: {$officer->name}\n";

    // Find an application assigned to this officer
    $application = PhysicalPossessionApplication::where('officer_id', $officer->id)->first();
    if (!$application) {
        throw new Exception('No Physical Possession Application found for this officer.');
    }
    // Ensure the application is in a state suitable for testing
    $application->physical_possession_status = 'Slot Selected';
    $application->possession_date = '2026-07-10';
    $application->meeting_slot = '10:00 AM';
    $application->save();
    echo "Testing application secure_id: {$application->secure_id}\n";

    $apiController = new App\Http\Controllers\Api\PpOfficerApiController();

    // 1. Reschedule API test
    echo "\n--- Reschedule API test ---\n";
    $requestReschedule = Request::create('/api/possession/officer/verify/' . $application->secure_id, 'POST', [
        'action' => 'reschedule'
    ]);
    $responseReschedule = $apiController->verifySave($requestReschedule, $application);
    $dataReschedule = json_decode($responseReschedule->getContent(), true);
    echo "Reschedule response: " . json_encode($dataReschedule, JSON_PRETTY_PRINT) . "\n";
    $application->refresh();
    echo "Status after Reschedule: {$application->physical_possession_status}\n";

    // 2. Step 1 Site Verification API test
    echo "\n--- Step 1 (Site Verification) API test ---\n";
    // Reset status
    $application->physical_possession_status = 'Slot Selected';
    $application->save();
    // Mock image upload
    $tempImg = tempnam(sys_get_temp_dir(), 'img');
    imagejpeg(imagecreatetruecolor(10, 10), $tempImg);
    $uploadedImg = new \Illuminate\Http\UploadedFile($tempImg, 'plot.jpg', 'image/jpeg', null, true);
    $requestStep1 = Request::create('/api/possession/officer/verify/' . $application->secure_id, 'POST', [
        'latitude' => '30.7333',
        'longitude' => '76.7794',
        'remarks' => 'Step 1 test'
    ], [], [
        'plot_image' => $uploadedImg
    ]);
    $responseStep1 = $apiController->verifySave($requestStep1, $application);
    $dataStep1 = json_decode($responseStep1->getContent(), true);
    echo "Step 1 response: " . json_encode($dataStep1, JSON_PRETTY_PRINT) . "\n";
    $application->refresh();
    echo "Status after Step 1: {$application->physical_possession_status}\n";

    // 3. Step 2 (E-Possession) API test
    echo "\n--- Step 2 (E-Possession) API test ---\n";
    $tempPdf1 = tempnam(sys_get_temp_dir(), 'pdf');
    file_put_contents($tempPdf1, '%PDF-1.4');
    $certFile = new \Illuminate\Http\UploadedFile($tempPdf1, 'signed_cert.pdf', 'application/pdf', null, true);
    $tempPdf2 = tempnam(sys_get_temp_dir(), 'pdf');
    file_put_contents($tempPdf2, '%PDF-1.4');
    $seFile = new \Illuminate\Http\UploadedFile($tempPdf2, 'site_engineer.pdf', 'application/pdf', null, true);
    $requestStep2 = Request::create('/api/possession/officer/verify/' . $application->secure_id, 'POST', [], [], [
        'possession_certificate' => $certFile,
        'site_engineer_file' => $seFile
    ]);
    $responseStep2 = $apiController->verifySave($requestStep2, $application);
    $dataStep2 = json_decode($responseStep2->getContent(), true);
    echo "Step 2 response: " . json_encode($dataStep2, JSON_PRETTY_PRINT) . "\n";
    $application->refresh();
    echo "Status after Step 2: {$application->physical_possession_status}\n";

    // 4. Download certificate API test
    echo "\n--- Download Certificate API test ---\n";
    $requestDownload = Request::create('/api/possession/officer/download-certificate/' . $application->secure_id, 'GET', ['base64' => 1]);
    $responseDownload = $apiController->downloadCertificate($requestDownload, $application);
    $dataDownload = json_decode($responseDownload->getContent(), true);
    echo "Download response success: " . ($dataDownload['success'] ? 'TRUE' : 'FALSE') . "\n";
    echo "Base64 length: " . strlen($dataDownload['pdf_base64'] ?? '') . "\n";

    echo "\nAll Site Engineer mobile API steps completed successfully.\n";
} catch (Exception $e) {
    echo "TEST FAILED: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "Database rolled back.\n";
}
?>
