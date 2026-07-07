<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$paidCount = DB::table('ownermaster')->where('IsPaid', 1)->count();
echo "IsPaid = 1 Count: {$paidCount}\n";

$otherPaidCount = DB::table('ownermaster')->where('IsPaid', '!=', 0)->count();
echo "IsPaid != 0 Count: {$otherPaidCount}\n";

$distinctPaid = DB::table('ownermaster')->select('IsPaid')->distinct()->pluck('IsPaid');
echo "Distinct IsPaid values: " . implode(', ', $distinctPaid->toArray()) . "\n";
