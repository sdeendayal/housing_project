<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

$user = User::where('mobile', '9999999999')->first();
echo "User: {$user->name} | Zone ID: {$user->zone_id} | Zone Name: {$user->zone_name} | District Name: {$user->district_name}\n";

$zone = DB::table('ews_stp_districts')->where('id', $user->zone_id)->first();
echo "Zone in DB: ID {$zone->id} | Name: {$zone->name}\n";

$districts = DB::table('ews_districts')->where('zone_id', $zone->id)->get(['id', 'name']);
echo "Districts in this zone:\n";
foreach ($districts as $d) {
    echo "  - ID {$d->id}: {$d->name}\n";
}
