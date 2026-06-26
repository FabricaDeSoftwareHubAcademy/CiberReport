<?php
$host  = "localhost";
$user  = "root";
$pass  = "";
$banco = "crud544";

$conexao = new mysqli($host, $user, $pass, $banco);

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}
?>