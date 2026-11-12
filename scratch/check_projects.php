<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "--- ews_projects columns ---\n";
$cols = Schema::getColumnListing('ews_projects');
foreach ($cols as $col) {
    echo $col . "\n";
}

