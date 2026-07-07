<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$nullCount = DB::table('ownermaster')->whereNull('secure_id')->orWhere('secure_id', '')->count();
echo "Owners with null/empty secure_id: {$nullCount}\n";

if ($nullCount > 0) {
    echo "Updating secure_id for {$nullCount} records...\n";
    
    // We can use a direct SQL statement for fast batch update in MySQL
    $affected = DB::statement("UPDATE ownermaster SET secure_id = MD5(CONCAT(OwnerId, RAND(), UUID())) WHERE secure_id IS NULL OR secure_id = ''");
    
    $newNullCount = DB::table('ownermaster')->whereNull('secure_id')->orWhere('secure_id', '')->count();
    echo "New null/empty secure_id count: {$newNullCount}\n";
} else {
    echo "All records already have a secure_id.\n";
}
