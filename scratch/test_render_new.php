<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EwsBuilderFlat;
use App\Models\EwsProject;
use App\Models\User;

$flat = EwsBuilderFlat::first();
if (!$flat) {
    echo "No flat found\n";
    exit(1);
}

$user = User::where('role', 'ews_stp')->first() ?? User::first();
$project = EwsProject::first();
$creator = $user;
$backUrl = '/ews/stp/dashboard';
$stats = ['total_flats' => 1];
$auditLogs = collect();
$displayZoneName = 'Rohtak Zone';
$tcpLicenceData = app(\App\Services\TcpLicenceApiService::class)->fetchLicenceDetails('242 OF 2007');

try {
    $html = view('ews.tcp.flat_view', compact(
        'user', 'flat', 'project', 'creator', 'backUrl', 'stats', 'auditLogs', 'displayZoneName', 'tcpLicenceData'
    ))->render();
    echo "RENDER_SUCCESS: Output length = " . strlen($html) . " bytes\n";
} catch (\Throwable $e) {
    echo "RENDER_ERROR: " . $e->getMessage() . "\nLine: " . $e->getLine() . "\nFile: " . $e->getFile() . "\n";
}

