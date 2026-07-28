<?php

class GerenciarAcesso
{
    private $pdo;
    public $msgerro = '';
    
    public  function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function  listar()
    {
        $sql = $this->pdo->prepare("SELECT * FROM perfil_acesso ORDER BY nome");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cadastrar($nome){
        $sql = $this->pdo->prepare("INSERT INTO perfil_acesso (nome) VALUES (:nome)");
        $sql->bindValue(":nome", $nome);
        return $this->pdo->lastInsertId();
    }

    public function buscar($id)
    {
        $sql = $this->pdo->prepare("SELECT * FROM perfil_acesso WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function excluir($id)
    {
        $sql = $this->pdo->prepare("DELETE FROM perfil_acesso WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();
    }
}