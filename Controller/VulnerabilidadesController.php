<?php
require_once __DIR__ . "/../bootstrap.php";
require_once __DIR__ . "/../Model/Database/Vulnerabilidades.php";

class VulnerabilidadesController
{
    private $Vulnerabilidades;

    public function __construct()
    {
        $this->Vulnerabilidades = new Vulnerabilidades();
        $this->Vulnerabilidades->conectar(
            $_ENV['DB_NAME'],
            $_ENV['DB_HOST'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']
        );
    }

    public function listar()
    {
        return $this->Vulnerabilidades->ListarVulnerabilidade();
    }

    public function cadastrarVulnerabilidade()
    {
        $id              = addslashes($_POST['id'] ?? '');
        $projeto_id   = addslashes($_POST['projeto_id'] ?? '');
        $nome = addslashes($_POST['nome'] ?? '');
        $cvss         = addslashes($_POST['cvss'] ?? '');
        $cve            = addslashes($_POST['cve'] ?? '');
        $descricao           = addslashes($_POST['descricao'] ?? '');
        $descricao_tecnica        = addslashes($_POST['descricao_tecnica'] ?? '');
        $categoria         = addslashes($_POST['categoria'] ?? '');
        $severidade_vulnerabilidade = addslashes($_POST['severidade_vulnerabilidade'] ?? '');
        $habilitado    = (int) ($_POST['habilitado'] ?? 0);
        $impacto_negocio = addslashes($_POST['impacto_negocio'] ?? '');

        if (empty($nome) || empty($descricao_breve) || empty($categoria) || empty($modelo) || empty($tecnica)) {
            return false;
        }

        return $this->Vulnerabilidades->cadastrarVulnerabilidade(
            $id,$nome, $projeto_id, $nome,
            $cvss, $cve, $descricao,
            $descricao_tecnica, $categoria, $severidade_vulnerabilidade, $habilitado, $impacto_negocio
        );
    }

    public function excluir($id)
    {
        $this->Vulnerabilidades->excluirVulnerabilidades((int) $id);
    }

    public function alterarStatus($id, $habilitado)
    {
        $this->Vulnerabilidades->alterarStatus((int) $id, (int) $habilitado);
    }
}
