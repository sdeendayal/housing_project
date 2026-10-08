<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Get all project abbreviations
$abbrMap = DB::table('ews_flat_abbreviations')
    ->select('project_name', 'project_abbr', 'dist_name', 'town_abbr')
    ->distinct()
    ->get();

$allotted = DB::table('ews_allotted_8')->get();
$matched = 0;
$unmatched = 0;
$byProject = [];
$unmatchedSamples = [];

foreach ($allotted as $row) {
    $flatNo = trim($row->flat_no);
    // Parts separated by '-'
    $parts = explode('-', $flatNo);
    $projAbbr = $parts[1] ?? '';
    
    // Find matching project
    $found = $abbrMap->firstWhere('project_abbr', $projAbbr);
    if ($found) {
        $matched++;
        $byProject[$found->project_name] = ($byProject[$found->project_name] ?? 0) + 1;
    } else {
        $unmatched++;
        if (count($unmatchedSamples) < 10) {
            $unmatchedSamples[] = "ID: {$row->id} | Flat: {$flatNo} | Dist: {$row->dist_name}";
        }
    }
}

echo "Total Allotted: " . count($allotted) . "\n";
echo "Matched to Projects: {$matched}\n";
echo "Unmatched: {$unmatched}\n";

if ($unmatched > 0) {
    echo "Unmatched Samples:\n" . implode("\n", $unmatchedSamples) . "\n";
}

echo "\nCounts per Project (Top 20):\n";
arsort($byProject);
foreach (array_slice($byProject, 0, 20) as $pName => $count) {
    echo "- {$pName}: {$count}\n";
}
