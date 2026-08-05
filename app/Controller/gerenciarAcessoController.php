<?php

namespace Controller;

use Core\Controller;
use GerenciarAcesso;

require_once __DIR__ . "/../Model/GerenciarAcesso.php";

class GerenciarAcessoController extends Controller
{
    private $gerenciarAcesso;

    public function __construct()
    {
        $conexao = require __DIR__ . "/../Model/conexao.php";
        $this->gerenciarAcesso = new GerenciarAcesso($conexao);
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