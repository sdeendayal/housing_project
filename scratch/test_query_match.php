<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$projects = DB::table('ews_projects')->whereNotNull('name')->get();

$totalMatchedSum = 0;
foreach ($projects as $p) {
    $abbr = DB::table('ews_flat_abbreviations')->where('project_name', $p->name)->value('project_abbr');
    if (!$abbr) continue;

    $count = DB::table('ews_allotted_8')
        ->where('flat_no', 'LIKE', "%-{$abbr}-%")
        ->count();
    
    $totalMatchedSum += $count;
    echo "Proj ID {$p->id} | {$p->name} [{$abbr}]: {$count} beneficiaries\n";
}

echo "\nTotal Beneficiaries Matched Across All Projects: {$totalMatchedSum} / 4211\n";
