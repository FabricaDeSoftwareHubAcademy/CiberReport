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
        return $this->Vulnerabilidades->listar();
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
        $checklist         = addslashes($_POST['checklist'] ?? '');
        $nivel_profundidade = addslashes($_POST['nivel_profundidade'] ?? '');
        $horas_execucao    = (int) ($_POST['horas_execucao'] ?? 0);

        if (empty($nome) || empty($descricao_breve) || empty($categoria) || empty($modelo) || empty($tecnica)) {
            return false;
        }

        return $this->Vulnerabilidades->cadastrar(
            $nome, $descricao_breve, $descricao_completa,
            $categoria, $modelo, $tecnica,
            $frameworks, $checklist, $nivel_profundidade, $horas_execucao
        );
    }

    public function excluir($id)
    {
        $this->Vulnerabilidades->excluir((int) $id);
    }

    public function alterarStatus($id, $status)
    {
        $this->Vulnerabilidades->alterarStatus((int) $id, (int) $status);
    }
}
