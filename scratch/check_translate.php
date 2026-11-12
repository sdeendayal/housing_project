<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$flat = App\Models\EwsBuilderFlat::first();
$view = view('ews.tcp.flat_view', [
    'flat' => $flat,
    'project' => null,
    'creator' => null,
    'stats' => [],
    'user' => (object)['role' => 'ews_stp', 'district_name' => 'Rohtak', 'zone_name' => 'Rohtak'],
    'isStp' => true,
    'isDepartment' => false,
    'tcpLicenceData' => null
])->render();

if (stripos($view, 'Select Language') !== false) {
    echo "Found 'Select Language' in output\n";
} else {
    echo "NOT found in output\n";
}

if (stripos($view, 'translate') !== false) {
    echo "Found 'translate' in output\n";
} else {
    echo "NOT found translate in output\n";
}

