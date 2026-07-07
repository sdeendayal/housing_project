<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Find a block in Panipat (DistrictId = 2) which has owners
$sampleOwner = DB::table('ownermaster')
    ->where('DistrictId', 2)
    ->first();

if (!$sampleOwner) {
    echo "No owners found in Panipat (DistrictId = 2). Checking other districts...\n";
    $sampleOwner = DB::table('ownermaster')
        ->where('DistrictId', '!=', 3) // non-Rewari
        ->first();
}

if (!$sampleOwner) {
    echo "No non-Rewari owners found in database.\n";
    exit(1);
}

$districtId = $sampleOwner->DistrictId;
$blockId = $sampleOwner->BlockId;

$districtName = DB::table('districtmaster')->where('DistrictId', $districtId)->value('DistrictName') ?? 'PANIPAT';
$blockName = DB::table('blockmaster')->where('BlockId', $blockId)->value('BlockName') ?? 'Panipat';

echo "Selected District: {$districtName} (ID: {$districtId}) | Block: {$blockName} (ID: {$blockId})\n";

// 2. Mark some owners in this block as IsPaid = 1 so the BDO has eligibility list candidates
$ownersToMark = DB::table('ownermaster')
    ->where('DistrictId', $districtId)
    ->where('BlockId', $blockId)
    ->take(5)
    ->get();

foreach ($ownersToMark as $o) {
    DB::table('ownermaster')
        ->where('OwnerId', $o->OwnerId)
        ->update(['IsPaid' => 1]);
    echo "Marked Owner as Paid: {$o->OwnerName} | Mobile: {$o->MobileNo}\n";
}

// 3. Create or update BDO user in database
$bdoEmail = 'bdo.' . strtolower(str_replace(' ', '', $blockName)) . '@mmgay.com';
$bdoUser = \App\Models\User::updateOrCreate(
    ['email' => $bdoEmail],
    [
        'name' => 'BDO ' . $blockName,
        'email' => $bdoEmail,
        'mobile' => '999888' . rand(1000, 9999),
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        'role' => 'mmgay_bdo',
        'scheme' => 'MMGAY',
        'Is_Active' => '1',
        'Is_Deleted' => '0',
        'district_id' => $districtId,
        'district_name' => strtoupper($districtName),
        'block_id' => $blockId,
        'block_name' => $blockName,
    ]
);

// 4. Attach BDO Role Group mapping
$departmentGroup = \App\Models\RoleGroup::whereIn('slug', ['department', 'departmental'])->first();
$bdoRole = \App\Models\Role::where('slug', 'mmgay_bdo')->first();

if ($departmentGroup && $bdoRole) {
    \App\Models\RoleType::updateOrCreate(
        ['user_id' => $bdoUser->id],
        [
            'role_id' => $bdoRole->id,
            'role_group_id' => $departmentGroup->id,
        ]
    );
}

echo "SUCCESS: BDO User Created/Updated!\n";
echo "Email: {$bdoEmail}\n";
echo "Password: password123\n";
echo "District: {$districtName}\n";
echo "Block: {$blockName}\n";
