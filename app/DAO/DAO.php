<?php

namespace DAO;

use PDO;

/**
 * Classe-pai de todos os DAOs do projeto.
 *
 * Mantém uma única conexão PDO por request (padrão singleton estático,
 * igual ao Infotech), compartilhada entre todos os DAOs filhos. Diferente
 * do Infotech, aqui a classe NÃO estende PDO: lá o construtor do PDO nunca
 * é chamado, então o objeto DAO em si não é uma conexão válida. Aqui é
 * composição — os DAOs filhos usam self::conexao()->prepare(...).
 */
abstract class DAO
{
    protected static ?PDO $connection = null;

    public function __construct()
    {
        self::conexao();
    }

    /**
     * Devolve a conexão PDO da request atual, criando-a na primeira
     * chamada. Chamada estática (DAO::conexao()) ou via instância.
     */
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
