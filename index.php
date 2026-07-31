<?php

use \Core\Core;
use \http\Route;

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/app/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

session_start();

$config = require __DIR__ . '/config/app.php';

define("BASE_URL", $config['base_folder']);

require_once __DIR__ . "/app/routes/main.php";

Core::dispatch(Route::routes());

