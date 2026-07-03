<?php

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../Model/Database/ChecklistModel.php';

class ChecklistController
{
    private ChecklistModel $model;

    public function __construct()
    {
        $this->model = new ChecklistModel();

        $this->model->conectar(
            $_ENV['DB_NAME'],
            $_ENV['DB_HOST'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']
        );
    }

    public function listar(): array
    {
        return $this->model->listar();
    }

    public function cadastrar(): int|false
    {
        $dados = $this->obterDadosChecklist();

        if (!$dados) {
            return false;
        }

        return $this->model->cadastrar(
            $dados['nome'],
            $dados['descricao'],
            $dados['categoria'],
            $dados['itens_ids']
        );
    }

    public function atualizar(): bool
    {
        $id = (int) ($_POST['id'] ?? 0);
        $dados = $this->obterDadosChecklist();

        if ($id <= 0 || !$dados) {
            return false;
        }

        return $this->model->atualizar(
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

    public function excluir(int $id): bool
    {
        return $id > 0 && $this->model->excluir($id);
    }

    public function alterarStatus(int $id, int $status): bool
    {
        if ($id <= 0) {
            return false;
        }

        $status = $status === 1 ? 1 : 0;

        return $this->model->alterarStatus($id, $status);
    }

    public function buscarComItens(int $id): array|false
    {
        if ($id <= 0) {
            return false;
        }

        return $this->model->buscarComItens($id);
    }

    public function listarCategorias(): array
    {
        return $this->model->listarCategorias();
    }

    public function listarItensCatalogo(): array
    {
        return $this->model->listarItensCatalogo();
    }

    public function buscarItemCatalogo(): array|false
    {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            return false;
        }

        return $this->model->buscarItemCatalogo($id);
    }

    public function cadastrarItemCatalogo(): array|false
    {
        $dados = $this->obterDadosItemCatalogo();

        if (!$dados) {
            return false;
        }

        return $this->model->cadastrarItemCatalogo(
            $dados['titulo'],
            $dados['referencia'],
            $dados['obrigatorio']
        );
    }

    public function atualizarItemCatalogo(): array|false
    {
        $id = (int) ($_POST['id'] ?? 0);
        $dados = $this->obterDadosItemCatalogo();

        if ($id <= 0 || !$dados) {
            return false;
        }

        return $this->model->atualizarItemCatalogo(
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

    public function removerItemCatalogo(): array|false
    {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            return false;
        }

        return $this->model->removerItemCatalogo($id);
    }
}
