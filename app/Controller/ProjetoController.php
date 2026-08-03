<?php
namespace Controller;

use Model\Projeto;

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
