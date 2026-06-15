<?php
$host  = "localhost";
$user  = "root";
$pass  = "";
$banco = "cyber-report"; // troque pelo nome do seu banco

$conexao = new mysqli($host, $user, $pass, $banco);

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}
?>