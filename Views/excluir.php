<?php
    require "../Model/Database/Empresa.php";
    require "../Model/Database/Endereco.php";

    $empresa = new Empresa();
    $endereco = new Endereco();

    $empresa->conectar("clientesbaikal","localhost","root","");
    $endereco->conectar("clientesbaikal","localhost","root","");

    if(isset($_GET['id_empresa']))
    {
        $id_empresa = addslashes($_GET['id_empresa']);

        
        $dados_empresa = $empresa->buscarDadosEmpresa($id_empresa);
        $id_endereco = $dados_empresa['endereco_id'];

        
        $empresa->excluirEmpresa($id_empresa);
        $endereco->excluirEndereco($id_endereco);
    }
    header("location:listar.php");
?>