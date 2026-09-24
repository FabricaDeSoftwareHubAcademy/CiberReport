<?php

namespace Controller;

use Core\Controller;
use GerenciarAcessoModel;

require_once __DIR__ . "/../Model/GerenciarAcessoModel.php";

class GerenciarAcessoController extends Controller
{
    private $gerenciarAcesso;

    public function __construct()
    {
        require_once __DIR__ . '/../DAO/DAO.php';
        $conexao = \DAO\DAO::conexao();
        $this->gerenciarAcesso = new GerenciarAcessoModel($conexao);
    }

    public function index()
    {
        $this->view('gerenciamento_acesso');
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