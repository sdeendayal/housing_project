<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('mobile', '7082839767')->first(); // Kusum's mobile in screenshot
if (!$user) {
    echo "Kusum user not found by mobile. Searching by name...\n";
    $user = \App\Models\User::where('name', 'like', '%KUSUM%')->first();
}

if (!$user) {
    echo "User KUSUM not found.\n";
    exit(1);
}

echo "User found: {$user->name} | ID: {$user->id} | Mobile: {$user->mobile}\n";

$purchaser = DB::table('property_private_purchasers')
    ->where('MobileNo', $user->mobile)
    ->first();

if (!$purchaser) {
    echo "Purchaser record not found for KUSUM.\n";
    exit(1);
}

$auction = DB::table('property_auction_detail')
    ->where('PurchaserID', $purchaser->PrivatePurchaserId)
    ->first();

if (!$auction) {
    echo "Auction record not found for KUSUM.\n";
    exit(1);
}

$assetId = $auction->AssetId;
echo "Asset ID: {$assetId} | Flat Cost: {$auction->FlatCost} | Received Amount: {$auction->ReceivedAmount}\n";

// Query all installments for this asset
$installments = DB::table('installment_due')
    ->where('AssetId', $assetId)
    ->where('IsDeleted', 0)
    ->where('IsActive', 1)
    ->orderBy('InstallmentNumber')
    ->get();

echo "Total seeded installments: " . $installments->count() . "\n";
foreach ($installments->take(5) as $inst) {
    echo "  - Installment #{$inst->InstallmentNumber} | EMIAmount: {$inst->EMIAmount} | DueAmount: {$inst->DueAmount}\n";
}

// Simulate 10000 paid:
$totalPaid = 10000.0;
$allocatedArray = [];
$remaining = $totalPaid;

echo "\n--- Simulating allocation of ₹10,000 paid shuru me ---\n";
foreach ($installments as $inst) {
    $due = (float) $inst->DueAmount;
    if ($remaining >= $due) {
        $allocatedArray[$inst->InstallmentNumber] = [
            'allocated' => $due,
            'status' => 'paid',
            'due_left' => 0
        ];
        $remaining -= $due;
    } else {
        if ($remaining > 0) {
            $allocatedArray[$inst->InstallmentNumber] = [
                'allocated' => $remaining,
                'status' => 'partial',
                'due_left' => $due - $remaining
            ];
            $remaining = 0;
        } else {
            $allocatedArray[$inst->InstallmentNumber] = [
                'allocated' => 0,
                'status' => 'unpaid',
                'due_left' => $due
            ];
        }
    }
}

// Print results of simulation
foreach ($allocatedArray as $num => $res) {
    if ($res['status'] !== 'paid') {
        echo "First Unpaid/Partial Installment: #{$num}\n";
        echo "Status: {$res['status']}\n";
        echo "Original DueAmount: " . $installments->where('InstallmentNumber', $num)->first()->DueAmount . "\n";
        echo "Allocated from ₹10k: {$res['allocated']}\n";
        echo "Remaining Kist Amount (Due Left): ₹" . $res['due_left'] . "\n";
        break;
    }
}
