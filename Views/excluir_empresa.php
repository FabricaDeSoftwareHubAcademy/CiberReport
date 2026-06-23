<?php
    require "../Model/Database/conexao.php";
    require "../Model/Database/Empresa.php";
    require "../Model/Database/Endereco.php";

    $empresa = new Empresa();
    $endereco = new Endereco();

    $empresa->conectar($nome_banco, $host, $usuario_bd, $senha_bd);
    $endereco->conectar($nome_banco, $host, $usuario_bd, $senha_bd);

    if(isset($_GET['id_empresa']))
    {
        $id_empresa = addslashes($_GET['id_empresa']);

        $dados_empresa = $empresa->buscarDadosEmpresa($id_empresa);
        $id_endereco = $dados_empresa['endereco_id'];

        $empresa->excluirEmpresa($id_empresa);
        $endereco->excluirEndereco($id_endereco);
    }

    header("location:clientes.php"); 
    exit;
?>