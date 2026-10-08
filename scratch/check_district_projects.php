<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$districts = DB::table('ews_districts')->get();
$projects = DB::table('ews_projects')->get();

echo "=== PROJECTS DISTRICT WISE ===\n";
foreach ($districts as $d) {
    $projs = $projects->where('district_id', $d->id);
    if ($projs->count() > 0) {
        echo "\nDistrict [{$d->id}] {$d->name} ({$projs->count()} projects):\n";
        foreach ($projs as $p) {
            echo "  - ID: {$p->id} | Name: {$p->name} | Abbr: {$p->project_abbr} | Town: {$p->town_name} (ID: {$p->town_id})\n";
        }
    }
}
