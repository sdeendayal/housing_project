<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$projects = DB::table('ews_projects')->get();
$unmatched = 0;
foreach ($projects as $p) {
    $abbr = DB::table('ews_flat_abbreviations')->where('project_name', $p->name)->value('project_abbr');
    if ($abbr) {
        echo "ID: {$p->id} | Dist: {$p->district_name} | Name: {$p->name} => [{$abbr}]\n";
    } else {
        $unmatched++;
        echo "ID: {$p->id} | Dist: {$p->district_name} | Name: {$p->name} => *** NOT FOUND ***\n";
    }
}
echo "\nTotal projects: " . count($projects) . " | Unmatched: $unmatched\n";
