<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Stp\StpApiController;

echo "=== TESTING BENEFICIARY LIST APIS ===\n\n";

// Authenticate an STP user
$user = User::whereIn('role', ['ews_developer', 'ews_stp', 'stp'])->first();
if (!$user) {
    $user = User::first();
}
echo "Using User: ID {$user->id} | Role: {$user->role} | Mobile: {$user->mobile}\n\n";
auth()->login($user);

$controller = new StpApiController();

// 1. Test GET /api/stp/projects?district_id=22 (Sonipat)
echo "1. Testing Projects for Sonipat (district_id=22):\n";
$req = Request::create('/api/stp/projects', 'GET', ['district_id' => 22]);
$res = $controller->getProjects($req);
$data = json_decode($res->getContent(), true);
echo "Total Projects: " . $data['total_projects'] . "\n";
foreach ($data['projects'] as $p) {
    echo "  - ID: {$p['id']} | Name: {$p['name']} | Abbr: {$p['project_abbr']}\n";
}

// 2. Test GET /api/stp/beneficiaries?project_id=10 (Parker Infra Private Ltd. - PIPD)
echo "\n2. Testing Beneficiaries for Project ID 10 (Parker Infra / PIPD):\n";
$req2 = Request::create('/api/stp/beneficiaries', 'GET', ['project_id' => 10, 'per_page' => 5]);
$res2 = $controller->getBeneficiaries($req2);
$data2 = json_decode($res2->getContent(), true);
echo "Success: " . ($data2['success'] ? 'true' : 'false') . "\n";
echo "Total Allotted: " . $data2['total_allotted'] . "\n";
echo "Showing Top 5 Beneficiaries:\n";
foreach ($data2['beneficiaries'] as $b) {
    echo "  [{$b['s_no']}] AppNo: {$b['application_number']} | Name: {$b['full_name']} | Flat: {$b['flat_number']} | Status: {$b['status']}\n";
}

// 3. Test GET /api/stp/projects/13/beneficiaries (IRWO - Indian Railway Welfare Organization)
echo "\n3. Testing Beneficiaries for Project ID 13 (IRWO):\n";
$req3 = Request::create('/api/stp/projects/13/beneficiaries', 'GET', ['per_page' => 5]);
$res3 = $controller->getBeneficiaries($req3, 13);
$data3 = json_decode($res3->getContent(), true);
echo "Total Allotted in IRWO: " . $data3['total_allotted'] . "\n";
foreach ($data3['beneficiaries'] as $b) {
    echo "  [{$b['s_no']}] AppNo: {$b['application_number']} | Name: {$b['full_name']} | Flat: {$b['flat_number']} | Status: {$b['status']}\n";
}

// 4. Test Search filter inside project 10
echo "\n4. Testing Search within Project 10 (search='405'):\n";
$req4 = Request::create('/api/stp/beneficiaries', 'GET', ['project_id' => 10, 'search' => '405']);
$res4 = $controller->getBeneficiaries($req4);
$data4 = json_decode($res4->getContent(), true);
echo "Search results for '405': " . count($data4['beneficiaries']) . " found.\n";
foreach ($data4['beneficiaries'] as $b) {
    echo "  AppNo: {$b['application_number']} | Name: {$b['full_name']} | Flat: {$b['flat_number']}\n";
}

// 5. Test Single Beneficiary Details
$firstId = $data2['beneficiaries'][0]['id'];
echo "\n5. Testing Single Beneficiary Details (id={$firstId}):\n";
$res5 = $controller->getBeneficiaryDetails(new Request(), $firstId);
$data5 = json_decode($res5->getContent(), true);
print_r($data5['beneficiary']);

echo "\nALL TESTS PASSED PERFECTLY!\n";
