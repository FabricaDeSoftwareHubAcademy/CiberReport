<?php

use Core\Router;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/mailer.php';

session_start();

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/app/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

$config = require __DIR__ . '/config/app.php';

$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$dominio   = $_SERVER['HTTP_HOST'];
define("BASE_URL", $protocolo . $dominio . $config['base_folder']);

require_once __DIR__ . "/app/routes/main.php";
Router::dispatch();