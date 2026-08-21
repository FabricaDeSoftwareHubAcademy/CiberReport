<?php

namespace Core;

use http\Route;

class Router
{
    public static function dispatch(): void
    {
        $config     = require __DIR__ . '/../../config/app.php';
        $uri        = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
        $baseFolder = '/' . trim($config['base_folder'], '/');
        $method     = $_SERVER['REQUEST_METHOD'];

        if ($baseFolder !== '/' && ($uri === $baseFolder || str_starts_with($uri, $baseFolder . '/'))) {
            $uri = substr($uri, strlen($baseFolder)) ?: '/';
        }

        $uri = '/' . trim($uri, '/');

        foreach (Route::routes() as $route) {
            if ($route['method'] !== $method || $route['path'] !== $uri) {
                continue;
            }

            [$controllerName, $methodName] = explode('@', $route['action']);
            $controllerClass = "Controller\\{$controllerName}";

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();

                if (method_exists($controller, $methodName)) {
                    $controller->$methodName();
                    return;
                }
            }
        }

        http_response_code(404);
        echo '404 - Page Not Found';
    }
}