<?php

namespace http;

class Route
{
    private static array $routes = [];
    private static array $groupMiddlewares = [];

    private static function add(
        string $method,
        string $path,
        string $action,
        array $middlewares = []
    ): void {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'action' => $action,
            'middlewares' => array_merge(
                self::$groupMiddlewares,
                $middlewares
            ),
        ];
    }

    public static function GET(
        string $path,
        string $action,
        array $middlewares = []
    ): void {
        self::add('GET', $path, $action, $middlewares);
    }

    public static function POST(
        string $path,
        string $action,
        array $middlewares = []
    ): void {
        self::add('POST', $path, $action, $middlewares);
    }

    public static function PUT(
        string $path,
        string $action,
        array $middlewares = []
    ): void {
        self::add('PUT', $path, $action, $middlewares);
    }

    public static function DELETE(
        string $path,
        string $action,
        array $middlewares = []
    ): void {
        self::add('DELETE', $path, $action, $middlewares);
    }

    public static function middleware(
        array $middlewares,
        callable $routes
    ): void {
        $previousMiddlewares = self::$groupMiddlewares;

        self::$groupMiddlewares = array_merge(
            self::$groupMiddlewares,
            $middlewares
        );

        try {
            $routes();
        } finally {
            self::$groupMiddlewares = $previousMiddlewares;
        }
    }

    public static function routes(): array
    {
        return self::$routes;
    }
}