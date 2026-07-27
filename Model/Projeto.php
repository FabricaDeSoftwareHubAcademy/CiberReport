<?php

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
            $sql = $this->pdo->prepare("INSERT INTO projeto (empresa_id, nome, data_inicio, data_fim_prevista, data_fim_real, horas_contratadas, horas_executadas, tipo, nivel_sigilo, escopo, alvo, contrato, restricao, habilitado) VALUES (:empresa_id, :nome, :data_inicio, :data_fim_prevista, :data_fim_real, :horas_contratadas, :horas_executadas, :tipo, :nivel_sigilo, :escopo, :alvo, :contrato, :restricao, :habilitado)");
            $sql->bindValue(":empresa_id", $dados['empresa_id']);
            $sql->bindValue(":nome", $dados['nome']);
            $sql->bindValue(":data_inicio", $dados['data_inicio']);
            $sql->bindValue(":data_fim_prevista", $dados['data_fim_prevista']);
            $sql->bindValue(":data_fim_real", $dados['data_fim_real']);
            $sql->bindValue(":horas_contratadas", $dados['horas_contratadas']);
            $sql->bindValue(":horas_executadas", $dados['horas_executadas']);
            $sql->bindValue(":tipo", $dados['tipo']);
            $sql->bindValue(":nivel_sigilo", $dados['nivel_sigilo']);
            $sql->bindValue(":escopo", $dados['escopo']);
            $sql->bindValue(":alvo", $dados['alvo']);
            $sql->bindValue(":contrato", $dados['contrato']);
            $sql->bindValue(":restricao", $dados['restricao']);
            $sql->bindValue(":habilitado", $dados['habilitado']);
            $sql->execute();
            return true;
        } catch (PDOException $e) {
            $this->msgErro = $e->getMessage();
            return false;
        }
    }
    public function listarDados()
    {
        $sql = $this->pdo->prepare("SELECT projeto.*, empresa.nome_fantasia FROM projeto INNER JOIN empresa ON projeto.empresa_id = empresa.id ORDER BY projeto.nome");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
