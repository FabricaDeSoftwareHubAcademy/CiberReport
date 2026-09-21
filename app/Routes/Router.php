<?php

namespace Routes;

use Core\MiddlewareInterface;

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
            if ($route['method'] !== $method) {
                continue;
            }

            $parametros = self::casar($route['path'], $uri);

            if ($parametros === null) {
                continue;
            }

            foreach ($route['middlewares'] as $middlewareClass) {
                if (!is_subclass_of($middlewareClass, MiddlewareInterface::class)) {
                    throw new \RuntimeException(
                        "{$middlewareClass} deve implementar MiddlewareInterface."
                    );
                }

                $middleware = new $middlewareClass();

                if (!$middleware->handle()) {
                    return;
                }
            }

            [$controllerName, $methodName] = explode('@', $route['action']);
            $controllerClass = "Controller\\{$controllerName}";

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();

                if (method_exists($controller, $methodName)) {
                    $controller->$methodName(...$parametros);
                    return;
                }
            }
        }

        http_response_code(404);
        echo '404 - Page Not Found';
    }

    /**
     * Compara o path cadastrado (que pode ter {parametros}) com a URI da
     * requisição. Devolve os valores capturados, na ordem em que aparecem
     * no path, ou null quando não bate. Rotas sem "{" continuam comparando
     * por igualdade exata de string, como sempre funcionou.
     */
    private static function casar(string $rotaPath, string $uri): ?array
    {
        if (!str_contains($rotaPath, '{')) {
            return $rotaPath === $uri ? [] : null;
        }

        $regex = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $rotaPath);

        if (!preg_match('#^' . $regex . '$#', $uri, $matches)) {
            return null;
        }

        array_shift($matches);

        return $matches;
    }
}
