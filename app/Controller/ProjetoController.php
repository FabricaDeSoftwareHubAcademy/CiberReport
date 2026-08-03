<?php
namespace Controller;

use Core\Controller;
use Model\Projeto;

class ProjetoController extends Controller
{
    private $projeto;

    public function __construct()
    {
        $conexao = require __DIR__ . '/../Model/conexao.php';
        $this->projeto = new Projeto($conexao);
    }

    public function index()
    {
        $this->view('gerenciamento_projeto');
    }

    public function listar()
    {
        return $this->projeto->listarDados();
    }
}
