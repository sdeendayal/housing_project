<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$flat = App\Models\EwsBuilderFlat::first();
echo "Flat ID: " . $flat->id . "\n";
echo "developer_plan: " . var_export($flat->developer_plan, true) . "\n";
echo "address_of_project: " . var_export($flat->address_of_project, true) . "\n";
echo "tcp_licence_data: " . var_export(substr((string)$flat->tcp_licence_data, 0, 200), true) . "\n";

