<?php
require_once __DIR__ . "/../bootstrap.php";
require_once __DIR__ . "/../Model/Database/Projeto.php";


class ProjetoController
{
    private $projeto;

    public function __construct()
    {
        $this->projeto = new Projeto();
        $this->projeto->conectar(
            $_ENV['DB_NAME'],
            $_ENV['DB_HOST'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS'],
        );
    }

    public function listar()
    {
        return $this->projeto->listarDados();
    }
}
