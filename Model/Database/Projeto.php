<?php

class Projeto{
    private $pdo;

    public function conectar($nome_banco, $host, $usuario, $senha) 
    {
        $this->pdo = new PDO("mysql:host=$host;dbname=$nome_banco", $usuario, $senha);
    }

}