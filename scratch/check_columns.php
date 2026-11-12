<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "--- ews_builder_flats columns ---\n";
$cols = Schema::getColumnListing('ews_builder_flats');
foreach ($cols as $col) {
    echo $col . "\n";
}

echo "\n--- Related tables ---\n";
$tables = DB::select("SHOW TABLES LIKE '%ews%'");
foreach ($tables as $t) {
    $arr = (array)$t;
    echo reset($arr) . "\n";
}

