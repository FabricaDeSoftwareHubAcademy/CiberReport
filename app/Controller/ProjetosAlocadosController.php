<?php

namespace Controller;

require_once __DIR__ . "/../Model/ProjetosAlocadosModel.php";

use Core\Controller;
use Model\ProjetosAlocadosModel;
use Core\DAO;

class ProjetosAlocadosController extends Controller
{
    private $model;

    public function __construct()
    {
        $conexao = DAO::conexao();
        $this->model = new ProjetosAlocadosModel($conexao);
    }

    public function index()
    {
        $this->view('projetos-alocados');
    }

    public function listarProjetosAlocados()
    {
        return $this->model->listarProjetosAlocados();
    }

    public function buscarProjetoDetalhado($id)
    {
        return $this->model->buscarProjetoDetalhado((int) $id);
    }

    public function buscarVulnerabilidadesDoProjeto($id)
    {
        return $this->model->buscarVulnerabilidadesDoProjeto((int) $id);
    }

    public function contarProjetosAlocados()
    {
        return $this->model->contarProjetosAlocados();
    }

    public function contarProjetosAtrasados()
    {
        return $this->model->contarProjetosAtrasados();
    }
}
