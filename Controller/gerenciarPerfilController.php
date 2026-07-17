<?php 

require_once __DIR__ . "/../Model/conexao.php";
require_once __DIR__ . "/../Model/Database/GerenciarPerfil.php";

class gerenciarPerfilController
{
    private $gerenciarPerfil;

    public function __construct()
    {
        global $conexao;
        $this->gerenciarPerfil = new GerenciarPerfil($conexao);
    }

    public function listar()
    {
        return $this->gerenciarPerfil->listar();
    }

    public function cadastrar()
    {
        $nome = addslashes($_POST['nome'] ?? '');

        if (empty($nome)){
            return false;
        }

        return $this->gerenciarPerfil->cadastrar($nome);
    }

    public function excluir($id)
    {
        $this->gerenciarPerfil->excluir((int) $id);
    }

    public function alterarStatus($id, $status)
    {
        $this->gerenciarPerfil->alterarStatus((int) $id, (int) $status);
    }
}