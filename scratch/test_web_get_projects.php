<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\EwsDeveloperDashboardController;

$user = User::whereIn('role', ['ews_developer', 'ews_stp', 'stp'])->first();
auth()->login($user);

$controller = new EwsDeveloperDashboardController();

echo "Testing getProjects for ROHTAK (district_id=20):\n";
$req = Request::create('/ews/developer/projects', 'GET', ['district_id' => 20]);
$res = $controller->getProjects($req);
print_r(json_decode($res->getContent(), true));

echo "\nTesting getProjects for ROHTAK with town_id=76 (Meham):\n";
$req2 = Request::create('/ews/developer/projects', 'GET', ['district_id' => 20, 'town_id' => 76]);
$res2 = $controller->getProjects($req2);
print_r(json_decode($res2->getContent(), true));

echo "\nTesting getProjects for SONIPAT (district_id=22):\n";
$req3 = Request::create('/ews/developer/projects', 'GET', ['district_id' => 22]);
$res3 = $controller->getProjects($req3);
print_r(json_decode($res3->getContent(), true));
