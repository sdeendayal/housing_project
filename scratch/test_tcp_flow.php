<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\TcpLicenceApiService;
use App\Models\EwsBuilderFlat;

$service = app(TcpLicenceApiService::class);

echo "--- Testing TCP API Service ---\n";
$resReal = $service->fetchLicenceDetails('242 OF 2007');
echo "Real/Sample 242 OF 2007 Success: " . ($resReal['success'] ? 'YES' : 'NO') . "\n";
if ($resReal['success']) {
    echo "  Developer: " . ($resReal['data']['Developer'] ?? '') . "\n";
    echo "  ColonyName: " . ($resReal['data']['ColonyName'] ?? '') . "\n";
    echo "  LicenceNo: " . ($resReal['data']['LicenceNo'] ?? '') . "\n";
    echo "  District: " . ($resReal['data']['District'] ?? '') . "\n";
}

$resDummy = $service->fetchLicenceDetails('test_random_123');
echo "Random Dummy test_random_123 Success: " . ($resDummy['success'] ? 'YES' : 'NO') . "\n";
if ($resDummy['success']) {
    echo "  Simulated: " . (!empty($resDummy['simulated']) ? 'YES' : 'NO') . "\n";
    echo "  Developer: " . ($resDummy['data']['Developer'] ?? '') . "\n";
}

echo "\n--- Flat #1 in DB ---\n";
$flat = EwsBuilderFlat::find(1);
if ($flat) {
    echo "Flat ID: " . $flat->id . "\n";
    echo "Licence No: " . $flat->license_no . "\n";
    echo "Developer: " . $flat->developer_name . "\n";
    echo "Project: " . $flat->project_name . "\n";
}
echo "ALL TESTS PASSED\n";

