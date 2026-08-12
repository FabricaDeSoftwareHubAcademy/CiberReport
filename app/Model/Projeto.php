<?php
namespace Model;

use PDO;
class Projeto
{
    private $pdo;

    public $msgErro = "";

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function buscarProjeto($id)
    {
        $sql = $this->pdo->prepare("SELECT id FROM projeto WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    public function cadastrarProjeto(array $dados)
    {
        try {
            $sql = $this->pdo->prepare("INSERT INTO projeto (empresa_id, nome, data_inicio, data_fim_prevista, data_fim_real, horas_contratadas, modalidade, nivel_sigilo, escopo, contrato, restricao, status) VALUES (:empresa_id, :nome, :data_inicio, :data_fim_prevista, :data_fim_real, :horas_contratadas, :modalidade, :nivel_sigilo, :escopo, :contrato, :restricao, :status)");
            $sql->bindValue(":empresa_id", $dados['empresa_id'], PDO::PARAM_INT);
            $sql->bindValue(":nome", $dados['nome']);
            $sql->bindValue(
                ":data_inicio",
                $dados['data_inicio'],
                $dados['data_inicio'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
            );
            $sql->bindValue(
                ":data_fim_prevista",
                $dados['data_fim_prevista'],
                $dados['data_fim_prevista'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
            );
            $sql->bindValue(
                ":data_fim_real",
                $dados['data_fim_real'],
                $dados['data_fim_real'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
            );
            $sql->bindValue(
                ":horas_contratadas",
                (string) $dados['horas_contratadas'],
                PDO::PARAM_STR
            );
            $sql->bindValue(":modalidade", $dados['modalidade']);
            $sql->bindValue(":nivel_sigilo", $dados['nivel_sigilo']);
            $sql->bindValue(":escopo", $dados['escopo']);
            $sql->bindValue(":contrato", $dados['contrato']);
            $sql->bindValue(":restricao", $dados['restricao']);
            $sql->bindValue(":status", $dados['status']);
            $sql->execute();
            return (int) $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            $this->msgErro = $e->getMessage();
            return false;
        }
    }
    public function listarDados()
    {
        $sql = $this->pdo->prepare("SELECT projeto.*, empresa.nome_fantasia FROM projeto INNER JOIN empresa ON projeto.empresa_id = empresa.id WHERE projeto.habilitado = 1 ORDER BY projeto.created_at DESC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    public function editarProjeto(array $dados)
    {
        try {
            $sql = $this->pdo->prepare("UPDATE projeto SET empresa_id = :empresa_id, nome = :nome, data_inicio = :data_inicio, data_fim_prevista = :data_fim_prevista, data_fim_real = :data_fim_real, horas_contratadas = :horas_contratadas, modalidade = :modalidade, nivel_sigilo = :nivel_sigilo, escopo = :escopo, contrato = :contrato, restricao = :restricao, status = :status WHERE id = :id");
            $sql->bindValue(":id", $dados['id'], PDO::PARAM_INT);
            $sql->bindValue(":empresa_id", $dados['empresa_id'], PDO::PARAM_INT);
            $sql->bindValue(":nome", $dados['nome']);
            $sql->bindValue(
                ":data_inicio",
                $dados['data_inicio'],
                $dados['data_inicio'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
            );
            $sql->bindValue(
                ":data_fim_prevista",
                $dados['data_fim_prevista'],
                $dados['data_fim_prevista'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
            );
            $sql->bindValue(
                ":data_fim_real",
                $dados['data_fim_real'],
                $dados['data_fim_real'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
            );
            $sql->bindValue(
                ":horas_contratadas",
                (string) $dados['horas_contratadas'],
                PDO::PARAM_STR
            );
            $sql->bindValue(":modalidade", $dados['modalidade']);
            $sql->bindValue(":nivel_sigilo", $dados['nivel_sigilo']);
            $sql->bindValue(":escopo", $dados['escopo']);
            $sql->bindValue(":contrato", $dados['contrato']);
            $sql->bindValue(":restricao", $dados['restricao']);
            $sql->bindValue(":status", $dados['status']);
            $sql->execute();
            return true;
        } catch (\PDOException $e) {
            $this->msgErro = $e->getMessage();
            return false;
        }
    }
    public function excluirProjeto($id)
    {
        try {
            $sql = $this->pdo->prepare("UPDATE projeto SET habilitado = 0 WHERE id = :id");
            $sql->bindValue(":id", $id);
            $sql->execute();
            return true;
        } catch (\PDOException $e) {
            $this->msgErro = $e->getMessage();
            return false;
        }
    }
    
}
