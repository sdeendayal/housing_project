<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Http\Controllers\EwsStpPossessionWebController;
use Illuminate\Http\Request;

$user = User::whereIn('role', ['ews_developer', 'ews_stp', 'stp'])->first();
auth()->login($user);

$req = Request::create('http://127.0.0.1:8000/ews/developer/possession/533', 'GET');
$controller = new EwsStpPossessionWebController();
$view = $controller->show(533);
$html = $view->render();

// Check if img src contains 127.0.0.1:8000/storage/
preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $html, $imgMatch);
echo "Image src in rendered HTML: " . ($imgMatch[1] ?? 'NOT FOUND') . "\n";

preg_match('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>\s*<i[^>]*><\/i>\s*View PDF/', $html, $pdfMatch);
echo "PDF href in rendered HTML: " . ($pdfMatch[1] ?? 'NOT FOUND') . "\n";
