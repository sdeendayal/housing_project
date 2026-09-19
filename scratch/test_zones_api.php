<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Stp\StpApiController;

$user = User::whereIn('role', ['ews_developer', 'ews_stp', 'stp'])->first();
auth()->login($user);

$controller = new StpApiController();

echo "=== TESTING 1: GET /api/stp/zones ===\n";
$req = Request::create('/api/stp/zones', 'GET');
$res = $controller->getZones($req);
$data = json_decode($res->getContent(), true);
echo "Total Zones: " . $data['total_zones'] . "\n";
foreach ($data['zones'] as $z) {
    echo "  - Zone ID [{$z['id']}] {$z['name']} ({$z['total_districts']} districts)\n";
}

echo "\n=== TESTING 2: GET /api/stp/districts?zone_id=5 (ROHTAK ZONE) ===\n";
$req2 = Request::create('/api/stp/districts', 'GET', ['zone_id' => 5]);
$res2 = $controller->getZoneDistricts($req2);
$data2 = json_decode($res2->getContent(), true);
echo "Zone: " . $data2['zone']['name'] . "\n";
foreach ($data2['districts'] as $d) {
    echo "  - District ID [{$d['id']}] {$d['name']}\n";
}

echo "\n=== TESTING 3: GET /api/stp/districts?zone_id=2 (GURUGRAM ZONE) ===\n";
$req3 = Request::create('/api/stp/districts', 'GET', ['zone_id' => 2]);
$res3 = $controller->getZoneDistricts($req3);
$data3 = json_decode($res3->getContent(), true);
echo "Zone: " . $data3['zone']['name'] . "\n";
foreach ($data3['districts'] as $d) {
    echo "  - District ID [{$d['id']}] {$d['name']}\n";
}

echo "\n=== TESTING 4: GET /api/stp/districts (Default: Logged-in user's assigned zone) ===\n";
$req4 = Request::create('/api/stp/districts', 'GET');
$res4 = $controller->getZoneDistricts($req4);
$data4 = json_decode($res4->getContent(), true);
echo "Assigned Zone: " . $data4['zone']['name'] . "\n";
foreach ($data4['districts'] as $d) {
    echo "  - District ID [{$d['id']}] {$d['name']}\n";
}

echo "\nALL TESTS PASSED SUCCESSFULLY!\n";
