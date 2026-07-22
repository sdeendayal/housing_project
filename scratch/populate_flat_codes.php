<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Helpers\EwsHelper;

DB::table('ews_builder_flats')->get()->each(function($flat) {
    $user = DB::table('users')->where('id', $flat->created_by)->first();
    $userName = $user ? $user->name : 'Developer Login';
    $code = EwsHelper::generateFlatCode(
        $flat->town_name,
        $userName,
        $flat->floor,
        $flat->block_tower_number,
        $flat->flat_number
    );
    DB::table('ews_builder_flats')->where('id', $flat->id)->update(['flat_code' => $code]);
    echo "Updated flat ID {$flat->id} with code {$code}\n";
});
