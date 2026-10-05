<?php

namespace Controller;

use Core\Controller;
use DAO\ChecklistDAO;
use Model\ChecklistModel;

require_once __DIR__ . '/../DAO/DAO.php';
require_once __DIR__ . '/../DAO/ChecklistDAO.php';
require_once __DIR__ . '/../Core/Model.php';
require_once __DIR__ . '/../Model/ChecklistModel.php';

class ChecklistController extends Controller
{
    private ChecklistDAO $checklistDAO;

    public function __construct()
    {
        $this->checklistDAO = new ChecklistDAO();
    }

    public function index()
    {
        $this->view('checklist');
    }

    public function listarChecklist(): array
    {
        return $this->checklistDAO->listarChecklist();
    }

    public function listarChecklistAtivos(): array
    {
        return $this->checklistDAO->listarChecklistAtivos();
    }

    public function cadastrarChecklist(): int|false
    {
        $dadosChecklist = $this->obterDadosChecklist();

        if ($dadosChecklist === false) {
            return false;
        }

        return $this->checklistDAO->cadastrarChecklist(
            $dadosChecklist->nome,
            $dadosChecklist->descricao,
            $dadosChecklist->itens_ids
        );
    }

    public function atualizarChecklist(): bool
    {
        $idChecklist = (int) ($_POST['id'] ?? 0);
        $dadosChecklist = $this->obterDadosChecklist();

        if ($idChecklist <= 0 || $dadosChecklist === false) {
            return false;
        }

        return $this->checklistDAO->atualizarChecklist(
            $idChecklist,
            $dadosChecklist->nome,
            $dadosChecklist->descricao,
            $dadosChecklist->itens_ids
        );
    }

    private function obterDadosChecklist(): ChecklistModel|false
    {
        $nomeChecklist = trim($_POST['nome'] ?? '');
        $descricaoChecklist = trim($_POST['descricao'] ?? '');
        $itensIdsChecklist = $this->normalizarIdsChecklist(
            $_POST['itens_ids'] ?? []
        );

        if (
            $nomeChecklist === ''
            || empty($itensIdsChecklist)
        ) {
            return false;
        }

        $checklist = new ChecklistModel();
        $checklist->nome = $nomeChecklist;
        $checklist->descricao = $descricaoChecklist;
        $checklist->itens_ids = $itensIdsChecklist;

        return $checklist;
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

        return $this->checklistDAO->excluirChecklist(
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

        return $this->checklistDAO->alterarStatusChecklist(
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

        return $this->checklistDAO->buscarComItensChecklist(
            $idChecklist
        );
    }

}
