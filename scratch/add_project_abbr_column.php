<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

if (!Schema::hasColumn('ews_projects', 'project_abbr')) {
    Schema::table('ews_projects', function (Blueprint $table) {
        $table->string('project_abbr', 50)->nullable()->after('name');
    });
    echo "Added project_abbr column to ews_projects.\n";
} else {
    echo "project_abbr column already exists on ews_projects.\n";
}

$projects = DB::table('ews_projects')->get();
$updated = 0;
foreach ($projects as $p) {
    $abbr = DB::table('ews_flat_abbreviations')->where('project_name', $p->name)->value('project_abbr');
    if ($abbr) {
        DB::table('ews_projects')->where('id', $p->id)->update(['project_abbr' => $abbr]);
        $updated++;
    }
}
echo "Updated $updated projects with their abbreviations!\n";
