<?php
namespace http;

class Route
{
    private static array $routes = [];

    private static function add(string $method, string $path, string $action): void
    {
        self::$routes[] = compact('method', 'path', 'action');
    }
    public static function GET(string $path, string $action): void
    {
        self::add('GET', $path, $action);
    }
    public static function POST(string $path, string $action): void
    {
        self::add('POST', $path, $action);
    }
    public static function PUT(string $path, string $action): void
    {
        self::add('PUT', $path, $action);
    }
    public static function DELETE(string $path, string $action): void
    {
        self::add('DELETE', $path, $action);
    }

    public static function routes(): array
    {
        return self::$routes;
    }

}