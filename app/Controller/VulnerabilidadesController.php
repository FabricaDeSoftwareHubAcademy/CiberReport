<?php

namespace Controller;

use Core\Controller;
use Core\DAO;
use Vulnerabilidades;

require_once __DIR__ . "/../Model/VulnerabilidadesModel.php";

class VulnerabilidadesController extends Controller
{
    private $Vulnerabilidades;

    public function __construct()
    {
        $conexao = DAO::conexao();
        $this->Vulnerabilidades = new Vulnerabilidades($conexao);
    }

    public function index()
    {
        $vulnerabilidades = $this->Vulnerabilidades->listarVulnerabilidade();
        $projetos = $this->Vulnerabilidades->listarProjetos();
        $this->view('vulnerabilidades', ['vulnerabilidades' => $vulnerabilidades, 'projetos' => $projetos]);
    }

    public function salvar()
    {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $ok = $this->atualizar($id);
        } else {
            if ((int) ($_POST['projeto_id'] ?? 0) <= 0) {
                $this->json(['status' => 400, 'msg' => 'Selecione o projeto.']);
            }
            $ok = $this->cadastrarVulnerabilidade();
        }

        if ($ok) {
            $this->json(['status' => 200, 'msg' => 'Vulnerabilidade salva com sucesso.']);
        }

        $this->json(['status' => 400, 'msg' => $this->Vulnerabilidades->msgErro ?: 'Preencha todos os campos obrigatórios.']);
    }

    public function listar()
    {
        return $this->Vulnerabilidades->listarVulnerabilidade();
    }
    public function buscar($id)
    {

        return $this->Vulnerabilidades->buscarDadosVulnerabilidades((int) $id);
    }

    public function cadastrarVulnerabilidade()
    {
        $id                         = trim($_POST['id'] ?? '');
        $projeto_id                 = trim($_POST['projeto_id'] ?? '');
        $nome                       = trim($_POST['nome'] ?? '');
        $cvss                       = trim($_POST['cvss'] ?? '');
        $cve                        = trim($_POST['cve'] ?? '');
        $descricao                  = trim($_POST['descricao'] ?? '');
        $descricao_tecnica          = trim($_POST['descricao_tecnica'] ?? '');
        $categoria                  = trim($_POST['categoria'] ?? '');
        $severidade_vulnerabilidade = trim($_POST['severidade_vulnerabilidade'] ?? '');
        $habilitado                 = (int) ($_POST['habilitado'] ?? 0);
        $impacto_negocio            = trim($_POST['impacto_negocio'] ?? '');
        $responsavel                = trim($_POST['responsavel'] ?? '');
        $status                     = trim($_POST['status'] ?? 'ABERTA');

        if (empty($nome) || empty($descricao) || empty($categoria) || empty($severidade_vulnerabilidade)) {
            return false;
        }

        $resultado = $this->Vulnerabilidades->cadastrarVulnerabilidade(
            $id, $projeto_id, $nome,
            $cvss, $cve, $descricao,
            $descricao_tecnica, $categoria, $severidade_vulnerabilidade, $habilitado, $impacto_negocio, $responsavel, $status
        );


        return $resultado;
    }

    public function atualizar($id)
    {
        $nome                       = trim($_POST['nome'] ?? '');
        $cvss                       = trim($_POST['cvss'] ?? '');
        $cve                        = trim($_POST['cve'] ?? '');
        $descricao                  = trim($_POST['descricao'] ?? '');
        $descricao_tecnica          = trim($_POST['descricao_tecnica'] ?? '');
        $categoria                  = trim($_POST['categoria'] ?? '');
        $severidade_vulnerabilidade = trim($_POST['severidade_vulnerabilidade'] ?? '');
        $habilitado                 = (int) ($_POST['habilitado'] ?? 0);
        $impacto_negocio            = trim($_POST['impacto_negocio'] ?? '');
        $responsavel                = trim($_POST['responsavel'] ?? '');
        $status                     = trim($_POST['status'] ?? 'ABERTA');

        if (empty($nome) || empty($descricao) || empty($categoria) || empty($severidade_vulnerabilidade)) {
            return false;
        }

        return $this->Vulnerabilidades->atualizarDadosVulnerabilidades(
            (int) $id, $nome, $cvss, $cve, $descricao,
            $descricao_tecnica, $categoria, $severidade_vulnerabilidade, $habilitado, $impacto_negocio, $responsavel, $status
        );
    }

    public function excluir($id)
    {
        return $this->Vulnerabilidades->excluirVulnerabilidades((int) $id);
    }

    public function alterarStatus($id, $status)
    {
        return $this->Vulnerabilidades->alterarStatus((int) $id, (int) $status);
    }
}
