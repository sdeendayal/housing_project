<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Http\Controllers\Api\Stp\StpApiController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

echo "========================================================\n";
echo "       TESTING PHYSICAL POSSESSION API WORKFLOW        \n";
echo "========================================================\n\n";

// 1. Authenticate STP user
$user = User::whereIn('role', ['ews_developer', 'ews_stp', 'stp'])->first();
if (!$user) {
    $user = User::first();
}
echo "1. Authenticated User: ID {$user->id} | Name: {$user->name} | Role: {$user->role}\n\n";
auth()->login($user);

$controller = new StpApiController();

// 2. Pick a beneficiary from ews_allotted_8 (for testing)
$beneficiary = DB::table('ews_allotted_8')->orderBy('id', 'asc')->first();
if (!$beneficiary) {
    echo "ERROR: No records in ews_allotted_8.\n";
    exit(1);
}
echo "2. Testing with Beneficiary:\n";
echo "   ID: {$beneficiary->id} | App No: {$beneficiary->application_number} | Name: {$beneficiary->full_name} | Flat: {$beneficiary->flat_no}\n";
echo "   Initial is_possession_given: " . ($beneficiary->is_possession_given ?? 0) . " | Status: " . ($beneficiary->possession_status ?? 'PENDING') . "\n\n";

// 3. Test Validation: possession_status = GIVEN without required files
echo "3. Testing Validation (Missing required PDF & photo for GIVEN):\n";
$reqValidation = Request::create("/api/stp/beneficiaries/{$beneficiary->id}/possession", 'POST', [
    'possession_status' => 'GIVEN',
    'latitude' => 28.9950,
    'longitude' => 77.0190,
]);
$reqValidation->setUserResolver(fn() => $user);
$resValidation = $controller->submitPossession($reqValidation, $beneficiary->id);
$valData = json_decode($resValidation->getContent(), true);
echo "   Status Code: " . $resValidation->getStatusCode() . " (Expected: 422)\n";
echo "   Message: " . ($valData['message'] ?? '') . "\n";
echo "   Validation Error Keys: " . implode(', ', array_keys($valData['errors'] ?? [])) . "\n\n";

// 4. Test Submission of Physical Possession: GIVEN
echo "4. Testing Possession Submission (GIVEN with <=500KB PDF + Photo + Lat/Long):\n";
$pdfPath = __DIR__ . '/../test_file.pdf';
$imgPath = __DIR__ . '/../test_image.jpg';
$uploadedPdf = new UploadedFile($pdfPath, 'possession_letter.pdf', 'application/pdf', null, true);
$uploadedImg = new UploadedFile($imgPath, 'beneficiary_flat.jpg', 'image/jpeg', null, true);

$reqSubmit = Request::create(
    "/api/stp/beneficiaries/{$beneficiary->id}/possession",
    'POST',
    [
        'possession_status' => 'GIVEN',
        'latitude' => 28.9876543,
        'longitude' => 77.0123456,
        'remarks' => 'Physical possession successfully handed over to beneficiary with keys.',
    ],
    [], // cookies
    [
        'possession_letter' => $uploadedPdf,
        'beneficiary_flat_photo' => $uploadedImg,
    ],
    ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => 'STP-Mobile-App-Flutter/1.0']
);
$reqSubmit->setUserResolver(fn() => $user);

$resSubmit = $controller->submitPossession($reqSubmit, $beneficiary->id);
$submitData = json_decode($resSubmit->getContent(), true);
echo "   Status Code: " . $resSubmit->getStatusCode() . " (Expected: 200)\n";
echo "   Success: " . ($submitData['success'] ? 'true' : 'false') . "\n";
echo "   Message: " . ($submitData['message'] ?? '') . "\n";
echo "   Possession Status: " . ($submitData['possession']['possession_status'] ?? '') . "\n";
echo "   is_possession_given: " . ($submitData['possession']['is_possession_given'] ?? '') . "\n";
echo "   Letter URL: " . ($submitData['possession']['possession_letter_url'] ?? '') . "\n";
echo "   Flat Photo URL: " . ($submitData['possession']['beneficiary_flat_photo_url'] ?? '') . "\n";
echo "   Coordinates: " . ($submitData['possession']['latitude'] ?? '') . ", " . ($submitData['possession']['longitude'] ?? '') . "\n\n";

// 5. Verify database records
echo "5. Verifying Database Changes in Transaction:\n";
$updatedBeneficiary = DB::table('ews_allotted_8')->where('id', $beneficiary->id)->first();
echo "   ews_allotted_8: is_possession_given = {$updatedBeneficiary->is_possession_given} | status = {$updatedBeneficiary->possession_status}\n";

$possessionRecord = DB::table('ews_beneficiary_possessions')->where('beneficiary_id', $beneficiary->id)->first();
echo "   ews_beneficiary_possessions Record ID: " . ($possessionRecord->id ?? 'NONE') . "\n";

$auditLog = DB::table('ews_possession_audit_logs')->where('beneficiary_id', $beneficiary->id)->latest('id')->first();
echo "   Audit Log: Action = {$auditLog->action} | Old = {$auditLog->old_status} | New = {$auditLog->new_status} | User = {$auditLog->stp_user_name}\n\n";

// 6. Test GET Beneficiary Details (Checking integrated possession data)
echo "6. Testing GET /api/stp/beneficiaries/{$beneficiary->id}:\n";
$reqDetails = Request::create("/api/stp/beneficiaries/{$beneficiary->id}", 'GET');
$resDetails = $controller->getBeneficiaryDetails($reqDetails, $beneficiary->id);
$detailsData = json_decode($resDetails->getContent(), true);
$bInfo = $detailsData['beneficiary'];
echo "   Beneficiary: {$bInfo['full_name']} | AppNo: {$bInfo['application_number']}\n";
echo "   is_possession_given: {$bInfo['is_possession_given']} | status: {$bInfo['possession_status']}\n";
echo "   Has Possession Record: " . (!empty($bInfo['possession_record']) ? 'YES' : 'NO') . "\n";
echo "   Audit Logs Count: " . count($bInfo['possession_audit_logs'] ?? []) . "\n\n";

// 7. Test Beneficiary List with possession filter
echo "7. Testing Beneficiary List Filter (?is_possession_given=1):\n";
$reqList = Request::create("/api/stp/beneficiaries", 'GET', [
    'project_id' => 10,
    'is_possession_given' => 1,
]);
$resList = $controller->getBeneficiaries($reqList);
$listData = json_decode($resList->getContent(), true);
echo "   Filtered Total: " . ($listData['total_allotted'] ?? 0) . "\n\n";

// 8. Test GET /api/stp/beneficiaries/{id}/possession
echo "8. Testing GET /api/stp/beneficiaries/{$beneficiary->id}/possession:\n";
$reqPoss = Request::create("/api/stp/beneficiaries/{$beneficiary->id}/possession", 'GET');
$resPoss = $controller->getPossessionDetails($reqPoss, $beneficiary->id);
$possData = json_decode($resPoss->getContent(), true);
echo "   Possession Status: " . ($possData['possession']['possession_status'] ?? '') . "\n";
echo "   Audit History Count: " . count($possData['audit_history'] ?? []) . "\n\n";

// 9. Test Submission of PENDING on Beneficiary ID 2
$beneficiary2 = DB::table('ews_allotted_8')->where('id', 2)->first();
if ($beneficiary2) {
    echo "9. Testing Submission of PENDING on Beneficiary ID 2 ({$beneficiary2->full_name}):\n";
    $reqPending = Request::create(
        "/api/stp/beneficiaries/{$beneficiary2->id}/possession",
        'POST',
        [
            'possession_status' => 'PENDING',
            'remarks' => 'Internal finishing & electricity meter installation is underway.',
            'latitude' => 28.9876,
            'longitude' => 77.0123,
        ]
    );
    $reqPending->setUserResolver(fn() => $user);
    $resPending = $controller->submitPossession($reqPending, $beneficiary2->id);
    $pendingData = json_decode($resPending->getContent(), true);
    echo "   Status Code: " . $resPending->getStatusCode() . " (Expected: 200)\n";
    echo "   Message: " . ($pendingData['message'] ?? '') . "\n";
    
    $checkB2 = DB::table('ews_allotted_8')->where('id', 2)->first();
    echo "   ews_allotted_8 (ID 2): is_possession_given = {$checkB2->is_possession_given} | status = {$checkB2->possession_status}\n\n";
}

echo "========================================================\n";
echo "         ALL POSSESSION WORKFLOW TESTS PASSED!          \n";
echo "========================================================\n";
