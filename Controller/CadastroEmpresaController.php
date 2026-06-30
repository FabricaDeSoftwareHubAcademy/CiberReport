<?php
require_once __DIR__ . "/../bootstrap.php";
require_once __DIR__ . "/../Model/Database/Empresa.php";

class CadastroEmpresaController
{
    private $empresa;

    public function __construct()
    {
        $this->empresa = new Empresa();
        $this->empresa->conectar(
            $_ENV['DB_NAME'],
            $_ENV['DB_HOST'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']
        );
    }
    public function __listardados()
    {
        
    }
}