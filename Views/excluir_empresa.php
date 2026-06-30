<?php
require_once __DIR__ . "/../Controller/CadastroEmpresaController.php";

$controller = new CadastroEmpresaController();

if (isset($_GET['id_empresa'])) {
    $id_empresa = addslashes($_GET['id_empresa']);
    $controller->excluir($id_empresa);
}

header("Location: cliente_empresa.php");
exit;

