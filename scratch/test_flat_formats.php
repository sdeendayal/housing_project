<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$samples = DB::table('ews_allotted_8')->select('flat_no')->distinct()->limit(30)->pluck('flat_no');
foreach ($samples as $f) {
    $parts = explode('-', $f);
    $floor = $parts[2] ?? '-';
    if (count($parts) == 6) {
        $block = $parts[3] . '-' . $parts[4];
        $unit = $parts[5];
    } elseif (count($parts) == 5) {
        $block = $parts[3];
        $unit = $parts[4];
    } elseif (count($parts) == 4) {
        $block = '-';
        $unit = $parts[3];
    } else {
        $block = '-';
        $unit = '-';
    }
    echo "Flat: {$f} | Floor: {$floor} | Block: {$block} | Unit: {$unit}\n";
}
