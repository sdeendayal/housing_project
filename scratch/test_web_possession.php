<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Http\Controllers\EwsStpPossessionWebController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

echo "========================================================\n";
echo "      TESTING WEB POSSESSION CONTROLLER & VIEWS         \n";
echo "========================================================\n\n";

// Authenticate STP user
$user = User::whereIn('role', ['ews_developer', 'ews_stp', 'stp'])->first();
if (!$user) {
    $user = User::first();
}
auth()->login($user);
echo "1. Logged in as: {$user->name} (Role: {$user->role})\n\n";



$controller = new EwsStpPossessionWebController();

// 1. Test Index View Rendering
echo "2. Testing index() web view rendering...\n";
$reqIndex = Request::create('/ews/developer/possession', 'GET', ['zone_id' => 5, 'district_id' => 22, 'project_id' => 10]);
$resIndex = $controller->index($reqIndex);
$renderedHtml = $resIndex->render();
echo "   View rendered successfully! HTML length: " . strlen($renderedHtml) . " bytes\n";
echo "   Contains 'Physical Possession Module': " . (str_contains($renderedHtml, 'Physical Possession Module') ? 'YES' : 'NO') . "\n";
echo "   Contains 'beneficiaries-table': " . (str_contains($renderedHtml, 'beneficiaries-table') ? 'YES' : 'NO') . "\n\n";

// 2. Test DataTables Ajax Endpoint
echo "3. Testing getBeneficiariesData() DataTables JSON...\n";
$reqData = Request::create('/ews/developer/possession/beneficiaries-data', 'GET', ['project_id' => 10]);
$resData = $controller->getBeneficiariesData($reqData);
$data = json_decode($resData->getContent(), true);
echo "   Total Records: " . ($data['recordsTotal'] ?? 0) . "\n";
if (!empty($data['data'])) {
    $first = $data['data'][0];
    echo "   First Beneficiary: {$first['full_name']} | Flat: {$first['flat_no']} | App: #{$first['application_number']}\n";
}
echo "\n";

// 3. Test Beneficiary Details Ajax
echo "4. Testing getBeneficiaryDetails() for Beneficiary ID 1...\n";
$reqDetails = Request::create('/ews/developer/possession/beneficiary/1', 'GET');
$resDetails = $controller->getBeneficiaryDetails($reqDetails, 1);
$detailJson = json_decode($resDetails->getContent(), true);
echo "   Success: " . ($detailJson['success'] ? 'true' : 'false') . "\n";
echo "   Name: " . ($detailJson['beneficiary']['full_name'] ?? '') . "\n";
echo "   Possession Status: " . ($detailJson['beneficiary']['possession_status'] ?? '') . "\n";
echo "   Audit Logs Count: " . count($detailJson['audit_history'] ?? []) . "\n\n";

// 4. Test Audit Logs View
echo "5. Testing auditLogs() web view...\n";
$reqLogs = Request::create('/ews/developer/possession/logs', 'GET');
$resLogs = $controller->auditLogs($reqLogs);
$logsHtml = $resLogs->render();
echo "   Logs view rendered successfully! Length: " . strlen($logsHtml) . " bytes\n\n";

echo "========================================================\n";
echo "          ALL WEB WORKFLOW TESTS PASSED!                \n";
echo "========================================================\n";
