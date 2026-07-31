<?php 

require_once __DIR__ . "/../Model/conexao.php";
require_once __DIR__ . "/../Model/GerenciarAcesso.php";

class gerenciarAcessoController
{
    private $gerenciarAcesso;

    public function __construct()
    {
        global $conexao;
        $this->gerenciarAcesso = new GerenciarAcesso($conexao);
    }

    public function listar()
    {
        return $this->gerenciarAcesso->listar();
    }

    public function cadastrar()
    {
        $nome = addslashes($_POST['nome'] ?? '');

        if (empty($nome)){
            return false;
        }

        return $this->gerenciarAcesso->cadastrar($nome);
    }

    public function excluir($id)
    {
        $this->gerenciarAcesso->excluir((int) $id);
    }
}