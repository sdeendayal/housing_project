<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$secureId = '331dc885cbc262b722fb3bc090a04cda';
$application = DB::table('physical_possession_applications')
    ->where('secure_id', $secureId)
    ->first();

if (!$application) {
    echo "Application with secure_id {$secureId} not found.\n";
    exit(1);
}

echo "Application ID: {$application->id}\n";
echo "Application Number: {$application->application_number}\n";
echo "Physical Possession Status: {$application->physical_possession_status}\n";

$files = [
    'plot_image' => $application->plot_image,
    'site_engineer_file' => $application->site_engineer_file,
    'possession_certificate' => $application->possession_certificate,
    'citizen_signed_report' => $application->citizen_signed_report ?? null,
];

foreach ($files as $name => $path) {
    if (!$path) {
        echo "{$name} path in DB: [EMPTY]\n";
        continue;
    }
    
    echo "{$name} path in DB: {$path}\n";
    
    // Check in storage/app/public
    $fullPath = storage_path('app/public/' . $path);
    if (file_exists($fullPath)) {
        echo "  - File exists at: {$fullPath}\n";
    } else {
        echo "  - FILE MISSING at: {$fullPath}\n";
    }
    
    // Check in storage/app/ (sometimes it's stored outside public)
    $appPath = storage_path('app/' . $path);
    if (file_exists($appPath)) {
        echo "  - File exists at: {$appPath}\n";
    } else {
        echo "  - FILE MISSING at: {$appPath}\n";
    }
}
