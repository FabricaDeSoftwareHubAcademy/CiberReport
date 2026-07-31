<?php
namespace http;

class Route
{
    private static array $route = [];

    public static function GET(string $path, string $action)
    {
        self::$route[] = [
            'path' => $path,
            'action' => $action,
            'method' => 'GET' 
        ];
    }
    public static function POST(string $path, string $action)
    {
        self::$route[] = [
            'path' => $path,
            'action' => $action,
            'method' => 'POST' 
        ];
    }
    public static function PUT(string $path, string $action)
    {
        self::$route[] = [
            'path' => $path,
            'action' => $action,
            'method' => 'PUT' 
        ];
    }
    public static function DELETE(string $path, string $action)
    {
        self::$route[] = [
            'path' => $path,
            'action' => $action,
            'method' => 'DELETE' 
        ];
    }

    public static function routes()
    {
        return self::$route;
    }
}