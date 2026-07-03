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
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $itens = $_POST['itens'] ?? [];

        if (empty($nome) || empty($categoria) || empty($itens)) {
            return false;
        }

        return $this->ChecklistModel->cadastrar(
            $nome,
            $descricao,
            $categoria,
            $itens
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

    public function atualizar()
{
    $id = (int) ($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $itens = $_POST['itens'] ?? [];

    if ($id <= 0 || empty($nome) || empty($categoria) || empty($itens)) {
        return false;
    }

    return $this->ChecklistModel->atualizar(
        $id,
        $nome,
        $descricao,
        $categoria,
        $itens
    );
}

public function buscarComItens($id)
{
    return $this->ChecklistModel->buscarComItens((int) $id);
}

public function listarCategorias()
{
    return $this->ChecklistModel->listarCategorias();
}

}
