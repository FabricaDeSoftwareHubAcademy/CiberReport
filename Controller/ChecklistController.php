<?php
require_once __DIR__ . "/../Model/conexao.php";
require_once __DIR__ . '/../Model/Database/ChecklistModel.php';

class ChecklistController
{
    private $checklistModel;

    public function __construct()
    {
        global $conexao;
        $this->checklistModel= new ChecklistModel($conexao);
    }

    public function listarChecklist(): array
    {
        return $this->checklistModel->listarChecklist();
    }

    public function cadastrarChecklist(): int|false
    {
        $dados = $this->obterDadosChecklist();

        if (!$dados) {
            return false;
        }

        return $this->checklistModel->cadastrarChecklist(
            $dados['nome'],
            $dados['descricao'],
            $dados['categoria'],
            $dados['itens_ids']
        );
    }

    public function atualizarChecklist(): bool
    {
        $id = (int) ($_POST['id'] ?? 0);
        $dados = $this->obterDadosChecklist();

        if ($id <= 0 || !$dados) {
            return false;
        }

        return $this->checklistModel->atualizarChecklist(
            $id,
            $dados['nome'],
            $dados['descricao'],
            $dados['categoria'],
            $dados['itens_ids']
        );
    }

    private function obterDadosChecklist(): array|false
    {
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $itensIds = $this->normalizarIds($_POST['itens_ids'] ?? []);

        if ($nome === '' || $categoria === '' || empty($itensIds)) {
            return false;
        }

        return [
            'nome' => $nome,
            'descricao' => $descricao,
            'categoria' => $categoria,
            'itens_ids' => $itensIds
        ];
    }

    private function normalizarIds(mixed $ids): array
    {
        if (!is_array($ids)) {
            return [];
        }

        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, fn(int $id) => $id > 0);

        return array_values(array_unique($ids));
    }

    public function excluirChecklist(int $id): bool
    {
        return $id > 0 && $this->checklistModel->excluirChecklist($id);
    }

    public function alterarStatusChecklist(int $id, int $status): bool
    {
        if ($id <= 0) {
            return false;
        }

        $status = $status === 1 ? 1 : 0;

        return $this->checklistModel->alterarStatusChecklist($id, $status);
    }

    public function buscarComItensChecklist(int $id): array|false
    {
        if ($id <= 0) {
            return false;
        }

        return $this->checklistModel->buscarComItensChecklist($id);
    }

    public function listarCategoriasChecklist(): array
    {
        return $this->checklistModel->listarCategoriasChecklist();
    }

    public function listarItensCatalogoChecklist(): array
    {
        return $this->checklistModel->listarItensCatalogoChecklist();
    }

    public function buscarItemCatalogo(): array|false
    {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            return false;
        }

        return $this->checklistModel->buscarItemCatalogo($id);
    }

    public function cadastrarItemCatalogoChecklist(): array|false
    {
        $dados = $this->obterDadosItemCatalogo();

        if (!$dados) {
            return false;
        }

        return $this->checklistModel->cadastrarItemCatalogoChecklist(
            $dados['titulo'],
            $dados['referencia'],
            $dados['obrigatorio']
        );
    }

    public function atualizarItemCatalogoChecklist(): array|false
    {
        $id = (int) ($_POST['id'] ?? 0);
        $dados = $this->obterDadosItemCatalogo();

        if ($id <= 0 || !$dados) {
            return false;
        }

        return $this->checklistModel->atualizarItemCatalogoCheckList(
            $id,
            $dados['titulo'],
            $dados['referencia'],
            $dados['obrigatorio']
        );
    }

    private function obterDadosItemCatalogo(): array|false
    {
        $titulo = trim($_POST['titulo'] ?? '');
        $referencia = trim($_POST['referencia'] ?? '');
        $obrigatorio = (int) ($_POST['obrigatorio'] ?? 1);

        if ($titulo === '') {
            return false;
        }

        return [
            'titulo' => $titulo,
            'referencia' => $referencia,
            'obrigatorio' => $obrigatorio === 1 ? 1 : 0
        ];
    }

    public function removerItemCatalogoChecklist(): array|false
    {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            return false;
        }

        return $this->checklistModel->removerItemCatalogoChecklist($id);
    }
}
