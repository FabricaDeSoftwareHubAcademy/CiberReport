<?php

class Projeto{
    private $pdo;

    public function conectar($nome_banco, $host, $usuario, $senha) 
    {
        $this->pdo = new PDO("mysql:host=$host;dbname=$nome_banco", $usuario, $senha);
    }

    public function listarDados()
    {
        $sql = $this->pdo->prepare("SELECT projeto.*, empresa.nome_fantasia FROM projeto INNER JOIN empresa ON projeto.empresa_id = empresa.id ORDER BY projeto.nome");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}