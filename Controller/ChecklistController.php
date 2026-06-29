<?php
require_once __DIR__ . "/../bootstrap.php";
require_once __DIR__ . "/../Model/Database/ChecklistModel.php";

class ChecklistController
{
    private $ChecklistModel;

    public function __construct()
    {
        $this->ChecklistModel = new ChecklistModel();
        $this->ChecklistModel->conectar(
            $_ENV['DB_NAME'],
            $_ENV['DB_HOST'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']
        );
    }

    public function listar()
    {
        return $this->ChecklistModel->listar();
    }

    public function cadastrar()
    {
        $nome              = addslashes($_POST['nome'] ?? '');
        $descricao_breve   = addslashes($_POST['descricao_breve'] ?? '');
        $descricao_completa = addslashes($_POST['descricao_completa'] ?? '');
        $categoria         = addslashes($_POST['categoria'] ?? '');
        $modelo            = addslashes($_POST['modelo'] ?? '');
        $tecnica           = addslashes($_POST['tecnica'] ?? '');
        $frameworks        = addslashes($_POST['frameworks'] ?? '');
        $nivel_profundidade = addslashes($_POST['nivel_profundidade'] ?? '');
        $horas_execucao    = (int) ($_POST['horas_execucao'] ?? 0);

        if (empty($nome) || empty($descricao_breve) || empty($categoria) || empty($modelo) || empty($tecnica)) {
            return false;
        }

        return $this->ChecklistModel->cadastrar(
            $nome, $descricao_breve, $descricao_completa,
            $categoria, $modelo, $tecnica,
            $frameworks,$nivel_profundidade, $horas_execucao
        );
    }

    public function excluir($id)
    {
        $this->ChecklistModel->excluir((int) $id);
    }

    public function alterarStatus($id, $status)
    {
        $this->ChecklistModel->alterarStatus((int) $id, (int) $status);
    }
}
