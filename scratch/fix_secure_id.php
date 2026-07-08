<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$affected = DB::statement("UPDATE ownermaster SET secure_id = MD5(CONCAT(OwnerId, RAND(), UUID())) WHERE secure_id IS NULL OR secure_id = ''");
echo "Update status: " . ($affected ? 'Success' : 'Failure') . "\n";

$nullCount = DB::table('ownermaster')->whereNull('secure_id')->orWhere('secure_id', '')->count();
echo "Remaining empty secure_ids: " . $nullCount . "\n";
