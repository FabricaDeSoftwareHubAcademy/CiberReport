<?php
require "../Model/conexao.php";
require "../Model/Database/Empresa.php";
require "../Model/Database/Endereco.php";

$empresa = new Empresa();
$endereco = new Endereco();

$empresa->conectar($_ENV['DB_NAME'], $_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
$endereco->conectar($_ENV['DB_NAME'], $_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS']);

if (isset($_GET['id_empresa'])) {
    $id_empresa = addslashes($_GET['id_empresa']);

    $dados_empresa = $empresa->buscarDadosEmpresa($id_empresa);
    $id_endereco = $dados_empresa['endereco_id'];

    $empresa->excluirEmpresa($id_empresa);
    $endereco->excluirEndereco($id_endereco);
}

header("location:cliente_empresa.php");
exit;
