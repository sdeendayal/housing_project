<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$zones = DB::table('ews_stp_districts')->get();
echo "Total zones: " . count($zones) . "\n";
foreach ($zones as $z) {
    $dists = DB::table('ews_districts')->where('zone_id', $z->id)->pluck('name')->toArray();
    echo "Zone [{$z->id}] {$z->name} => Districts: " . implode(', ', $dists) . "\n";
}
