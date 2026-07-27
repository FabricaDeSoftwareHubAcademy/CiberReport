<?php
require_once __DIR__ . "/../Model/conexao.php";
require_once __DIR__ . "/../Model/Projeto.php";


class ProjetoController
{
    private $projeto;

    public function __construct()
    {
        global $conexao;
        $this->projeto = new Projeto($conexao);
    }

    public function listar()
    {
        return $this->projeto->listarDados();
    }
}
