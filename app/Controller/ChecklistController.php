<?php

namespace Controller;

use Core\Controller;
use ChecklistModel;

require_once __DIR__ . '/../Model/Database/ChecklistModel.php';

class ChecklistController extends Controller
{
    private ChecklistModel $checklistModel;

    public function __construct()
    {
        $conexao = require __DIR__ . '/../Model/conexao.php';
        $this->checklistModel = new ChecklistModel($conexao);
    }

    public function index()
    {
        $this->view('checklist');
    }

    public function listarChecklist(): array
    {
        return $this->checklistModel->listarChecklist();
    }

    public function listarChecklistAtivos(): array
    {
        return $this->checklistModel->listarChecklistAtivos();
    }

    public function cadastrarChecklist(): int|false
    {
        $dadosChecklist = $this->obterDadosChecklist();

        if ($dadosChecklist === false) {
            return false;
        }

        return $this->checklistModel->cadastrarChecklist(
            $dadosChecklist['nome'],
            $dadosChecklist['descricao'],
            $dadosChecklist['categoria'],
            $dadosChecklist['itens_ids']
        );
    }

    public function atualizarChecklist(): bool
    {
        $idChecklist = (int) ($_POST['id'] ?? 0);
        $dadosChecklist = $this->obterDadosChecklist();

        if ($idChecklist <= 0 || $dadosChecklist === false) {
            return false;
        }

        return $this->checklistModel->atualizarChecklist(
            $idChecklist,
            $dadosChecklist['nome'],
            $dadosChecklist['descricao'],
            $dadosChecklist['categoria'],
            $dadosChecklist['itens_ids']
        );
    }

    private function obterDadosChecklist(): array|false
    {
        $nomeChecklist = trim($_POST['nome'] ?? '');
        $descricaoChecklist = trim($_POST['descricao'] ?? '');
        $categoriaChecklist = trim($_POST['categoria'] ?? '');

        $itensIdsChecklist = $this->normalizarIdsChecklist(
            $_POST['itens_ids'] ?? []
        );

        if (
            $nomeChecklist === ''
            || $categoriaChecklist === ''
            || empty($itensIdsChecklist)
        ) {
            return false;
        }

        return [
            'nome' => $nomeChecklist,
            'descricao' => $descricaoChecklist,
            'categoria' => $categoriaChecklist,
            'itens_ids' => $itensIdsChecklist
        ];
    }

    private function normalizarIdsChecklist(mixed $idsChecklist): array
    {
        if (!is_array($idsChecklist)) {
            return [];
        }

        $idsChecklist = array_map('intval', $idsChecklist);

        $idsChecklist = array_filter(
            $idsChecklist,
            static fn(int $idChecklist): bool => $idChecklist > 0
        );

        return array_values(array_unique($idsChecklist));
    }

    public function excluirChecklist(int $idChecklist): bool
    {
        if ($idChecklist <= 0) {
            return false;
        }

        return $this->checklistModel->excluirChecklist(
            $idChecklist
        );
    }

    public function alterarStatusChecklist(
        int $idChecklist,
        int $statusChecklist
    ): bool {
        if ($idChecklist <= 0) {
            return false;
        }

        $statusChecklist = $statusChecklist === 1 ? 1 : 0;

        return $this->checklistModel->alterarStatusChecklist(
            $idChecklist,
            $statusChecklist
        );
    }

    public function buscarComItensChecklist(
        int $idChecklist
    ): array|false {
        if ($idChecklist <= 0) {
            return false;
        }

        return $this->checklistModel->buscarComItensChecklist(
            $idChecklist
        );
    }

    public function listarCategoriasChecklist(): array
    {
        return $this->checklistModel->listarCategoriasChecklist();
    }

    public function listarItensCatalogoChecklist(): array
    {
        return $this->checklistModel->listarItensCatalogoChecklist();
    }

    public function buscarItemCatalogoChecklist(): array|false
    {
        $idItemChecklist = (int) ($_POST['id'] ?? 0);

        if ($idItemChecklist <= 0) {
            return false;
        }

        return $this->checklistModel->buscarItemCatalogoChecklist(
            $idItemChecklist
        );
    }

    public function cadastrarItemCatalogoChecklist(): array|false
    {
        $dadosItemChecklist =
            $this->obterDadosItemCatalogoChecklist();

        if ($dadosItemChecklist === false) {
            return false;
        }

        return $this->checklistModel
            ->cadastrarItemCatalogoChecklist(
                $dadosItemChecklist['titulo'],
                $dadosItemChecklist['referencia'],
                $dadosItemChecklist['obrigatorio'],
                $dadosItemChecklist['descricao_resumida'],
                $dadosItemChecklist['tempo_estimado_minutos']
            );
    }

    public function atualizarItemCatalogoChecklist(): array|false
    {
        $idItemChecklist = (int) ($_POST['id'] ?? 0);

        $dadosItemChecklist =
            $this->obterDadosItemCatalogoChecklist();

        if (
            $idItemChecklist <= 0
            || $dadosItemChecklist === false
        ) {
            return false;
        }

        return $this->checklistModel
            ->atualizarItemCatalogoChecklist(
                $idItemChecklist,
                $dadosItemChecklist['titulo'],
                $dadosItemChecklist['referencia'],
                $dadosItemChecklist['obrigatorio'],
                $dadosItemChecklist['descricao_resumida'],
                $dadosItemChecklist['tempo_estimado_minutos']
            );
    }

    private function obterDadosItemCatalogoChecklist(): array|false
    {
        $tituloItemChecklist = trim($_POST['titulo'] ?? '');

        $referenciaItemChecklist = trim(
            $_POST['referencia'] ?? ''
        );

        $obrigatorioItemChecklist = (int) (
            $_POST['obrigatorio'] ?? 1
        );

        $descricaoResumidaItemChecklist = trim(
            $_POST['descricao_resumida'] ?? ''
        );

        $tempoEstimadoItemChecklist = (int) (
            $_POST['tempo_estimado_minutos'] ?? 0
        );

        if ($tempoEstimadoItemChecklist < 0) {
            $tempoEstimadoItemChecklist = 0;
        }

        if ($tituloItemChecklist === '') {
            return false;
        }

        return [
            'titulo' => $tituloItemChecklist,
            'referencia' => $referenciaItemChecklist,
            'obrigatorio' =>
            $obrigatorioItemChecklist === 1 ? 1 : 0,
            'descricao_resumida' => $descricaoResumidaItemChecklist,
            'tempo_estimado_minutos' => $tempoEstimadoItemChecklist
        ];
    }

    public function removerItemCatalogoChecklist(): array|false
    {
        $idItemChecklist = (int) ($_POST['id'] ?? 0);

        if ($idItemChecklist <= 0) {
            return false;
        }

        return $this->checklistModel
            ->removerItemCatalogoChecklist(
                $idItemChecklist
            );
    }

    public function alterarStatusItemCatalogoChecklist(
        int $idItemChecklist,
        int $statusItemChecklist
    ): bool {
        if ($idItemChecklist <= 0) {
            return false;
        }

        $statusItemChecklist = $statusItemChecklist === 1 ? 1 : 0;

        return $this->checklistModel->alterarStatusItemCatalogoChecklist(
            $idItemChecklist,
            $statusItemChecklist
        );
    }
}
