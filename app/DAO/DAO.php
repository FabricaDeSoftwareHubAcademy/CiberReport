<?php

namespace DAO;

use PDO;
abstract class DAO
{
    protected static ?PDO $connection = null;

    public function __construct()
    {
        self::conexao();
    }


    public static function conexao(): PDO
    {
        if (self::$connection === null) {
            require_once __DIR__ . '/../../bootstrap.php';

            self::$connection = new PDO(
                "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'] . ";charset=utf8mb4",
                $_ENV['DB_USER'],
                $_ENV['DB_PASS'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }

        return self::$connection;
    }
}
