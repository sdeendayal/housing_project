<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('all_ews_data_1');
$sampleFamilyID = '3NSL5060'; // alphanumeric, length 8

echo "Scanning all_ews_data_1 columns for 8-char alphanumeric strings...\n";

foreach ($columns as $column) {
    // Check if any row has an 8-char alphanumeric value in this column
    $sampleValues = DB::table('all_ews_data_1')
        ->where($column, 'REGEXP', '^[a-zA-Z0-9]{8}$')
        ->take(3)
        ->pluck($column)
        ->toArray();
        
    if (!empty($sampleValues)) {
        echo "Column '{$column}' has 8-char alphanumeric values: " . implode(', ', $sampleValues) . "\n";
    }
}
