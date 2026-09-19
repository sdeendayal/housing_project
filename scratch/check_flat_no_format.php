<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$samples = DB::table('ews_allotted_8')->select('application_number', 'full_name', 'dist_name', 'mobile_number', 'flat_no')->take(10)->get();
print_r($samples);

// Check if any flat_no doesn't have '-' or doesn't match format
$irregular = DB::table('ews_allotted_8')->where('flat_no', 'not like', '%-%')->get();
echo "Irregular flat_no count: " . $irregular->count() . "\n";
