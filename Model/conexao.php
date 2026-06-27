<?php
require_once __DIR__ . "/../bootstrap.php";

$conexao = new mysqli(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    $_ENV['DB_NAME']
);

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}
