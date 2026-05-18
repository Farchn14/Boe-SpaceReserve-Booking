<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\KontrolJadwalController;
use Illuminate\Http\Request;

$controller = new KontrolJadwalController();
$request = new Request(['fasilitas_id' => 1, 'year' => 2026, 'month' => 4]);
$response = $controller->publicCalendarData($request);
echo $response->getContent();
