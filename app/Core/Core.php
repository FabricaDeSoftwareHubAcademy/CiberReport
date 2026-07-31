<?php

namespace Core;

class Core
{

    /**
     * Registra uma rota.
     *
     * @param string|null $permission  ex: 'user.read'  |  null = pública
     */
    

    public static function dispatch(array $routes): void
    {
        $config = require __DIR__ . '/../../config/app.php';
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri    = str_replace($config['base_folder'], '/', $uri);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($routes as $route) {
            if ($route['method'] === $method && $route['uri'] === $uri) {

                // ── Verificação por PERMISSION (não por role) ──
                // if ($route['permission'] !== null) {
                //     Auth::requirePermission($route['permission']);
                // }
                // ───────────────────────────────────────────────

                [$controllerName, $methodName] = explode('@', $route['controller']);
                $controllerClass = "Controllers\\{$controllerName}";

                if (class_exists($controllerClass)) {
                    $obj = new $controllerClass();
                    if (method_exists($obj, $methodName)) {
                        $obj->$methodName();
                        return;
                    }
                }
            }
        }

        http_response_code(404);
        echo "404 - Page Not Found";
    }
}