<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Get BDO user with Block Name Rewari
$bdo = DB::table('users')->where('block_name', 'like', '%REWARI%')->first();
if (!$bdo) {
    echo "No BDO user found with block REWARI\n";
    exit;
}

echo "BDO User: {$bdo->name}, Block Name: {$bdo->block_name}, Block ID: {$bdo->block_id}\n\n";

// Count all unique villages in villagemaster for this BDO's BlockId
$allVillagesInBlock = DB::table('villagemaster')
    ->where('BlockId', $bdo->block_id)
    ->get();

echo "Total unique villages defined in villagemaster for this block: " . $allVillagesInBlock->count() . "\n";
foreach ($allVillagesInBlock as $v) {
    echo "  - Village ID: {$v->VillageId}, Name: {$v->VillageName}\n";
}

echo "\n--- Registered Owners count by Village and Phase in this block ---\n";
$ownersCount = DB::table('ownermaster as o')
    ->join('villagemaster as v', 'o.VillageId', '=', 'v.VillageId')
    ->where('o.BlockId', $bdo->block_id)
    ->select('v.VillageId', 'v.VillageName', 'o.Phase', DB::raw('count(*) as total'))
    ->groupBy('v.VillageId', 'v.VillageName', 'o.Phase')
    ->get();

foreach ($ownersCount as $row) {
    echo "Village: {$row->VillageName} (ID: {$row->VillageId}) | Phase: {$row->Phase} | Beneficiaries: {$row->total}\n";
}
