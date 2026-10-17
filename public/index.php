<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Cek status maintenance mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register autoloader Composer
require __DIR__.'/../vendor/autoload.php';

// Bootstrap aplikasi Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Jalankan aplikasi
$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();

$kernel->terminate($request, $response);