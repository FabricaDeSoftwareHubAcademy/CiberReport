<?php

namespace Controller;

use Core\Controller;
use DAO\ChecklistItemDAO;
use Model\ChecklistItemModel;

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../DAO/DAO.php';
require_once __DIR__ . '/../DAO/ChecklistItemDAO.php';
require_once __DIR__ . '/../Core/Model.php';
require_once __DIR__ . '/../Model/ChecklistItemModel.php';

class ChecklistItemController extends Controller
{
    private ChecklistItemDAO $itemDAO;

    public function __construct()
    {
        $this->itemDAO = new ChecklistItemDAO();
    }

    public function listar(): array
    {
        return $this->itemDAO->listarItensCatalogo();
    }

    public function buscar(): array|false
    {
        $id = (int) ($_POST['id'] ?? 0);
        return $id > 0 ? $this->itemDAO->buscarItem($id) : false;
    }

    public function cadastrar(): array|false
    {
        $item = $this->obterDados();
        return $item === false ? false : $this->itemDAO->cadastrar(
            $item->titulo,
            $item->referencia,
            $item->obrigatorio,
            $item->descricao_resumida
        );
    }

    public function atualizar(): array|false
    {
        $id = (int) ($_POST['id'] ?? 0);
        $item = $this->obterDados();

        return $id <= 0 || $item === false ? false : $this->itemDAO->atualizar(
            $id,
            $item->titulo,
            $item->referencia,
            $item->obrigatorio,
            $item->descricao_resumida
        );
    }

    public function remover(): array|false
    {
        $id = (int) ($_POST['id'] ?? 0);
        return $id > 0 ? $this->itemDAO->remover($id) : false;
    }

    public function alterarStatus(int $id, int $status): bool
    {
        return $id > 0 && $this->itemDAO->alterarStatus($id, $status === 1 ? 1 : 0);
    }

    private function obterDados(): ChecklistItemModel|false
    {
        $titulo = trim($_POST['titulo'] ?? '');
        if ($titulo === '') {
            return false;
        }

        $item = new ChecklistItemModel();
        $item->titulo = $titulo;
        $item->referencia = trim($_POST['referencia'] ?? '');
        $item->obrigatorio = (int) ($_POST['obrigatorio'] ?? 1) === 1 ? 1 : 0;
        $item->descricao_resumida = trim($_POST['descricao_resumida'] ?? '');

        return $item;
    }
}
