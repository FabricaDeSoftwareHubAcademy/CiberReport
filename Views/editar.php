<?php
    require "../Model/Database/Empresa.php";
    require "../Model/Database/Endereco.php";

    $empresa = new Empresa();
    $endereco = new Endereco();

    $empresa->conectar("clientesbaikal","localhost","root","");
    $endereco->conectar("clientesbaikal","localhost","root","");

    if(isset($_GET['id_empresa']))
    {
        $id_update = addslashes($_GET['id_empresa']);
        $dados_empresa = $empresa->buscarDadosEmpresa($id_update);

        $id_endereco = $dados_empresa['endereco_id'];
        $dados_endereco = $endereco->buscarDadosEndereco($id_endereco);
    }

    if(isset($_POST['nome_fantasia']))
    {
        $nome_fantasia = addslashes($_POST['nome_fantasia']);
        $razao_social = addslashes($_POST['razao_social']);
        $telefone = addslashes($_POST['telefone']);
        $email_contato = addslashes($_POST['email_contato']);
        $cnpj = addslashes($_POST['cnpj']);
        $responsavel = addslashes($_POST['responsavel']);

        $cep = addslashes($_POST['cep']);
        $rua = addslashes($_POST['rua']);
        $numero = addslashes($_POST['numero']);
        $complemento = addslashes($_POST['complemento']);
        $bairro = addslashes($_POST['bairro']);
        $cidade = addslashes($_POST['cidade']);
        $estado = addslashes($_POST['estado']);
        $pais = addslashes($_POST['pais']);

        if(!empty($nome_fantasia) && !empty($razao_social) && !empty($telefone) && !empty($cnpj))
        {
            $empresa->atualizarDadosEmpresa($id_update,$nome_fantasia,$razao_social,$cnpj,$email_contato,$telefone,$responsavel);
            $endereco->atualizarDadosEndereco($id_endereco,$cep,$rua,$numero,$complemento,$bairro,$cidade,$estado,$pais);

            header("location:listar.php");
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDITAR Empresa</title>
</head>
<body>
    <h2 class="titulo-pagina">EDITAR EMPRESA</h2>
    <form method="post">
        <input type="text" name="nome_fantasia" value="<?php echo $dados_empresa['nome_fantasia'];?>">
        <input type="text" name="razao_social" value="<?php echo $dados_empresa['razao_social'];?>">
        <input type="tel" name="telefone" value="<?php echo $dados_empresa['telefone'];?>">
        <input type="email" name="email_contato" value="<?php echo $dados_empresa['email_contact'];?>">
        <input type="text" name="cnpj" value="<?php echo $dados_empresa['cnpj'];?>">
        <input type="text" name="cep" value="<?php echo $dados_endereco['cep'];?>">
        <input type="text" name="rua" value="<?php echo $dados_endereco['rua'];?>">
        <input type="text" name="numero" value="<?php echo $dados_endereco['numero'];?>">
        <input type="text" name="complemento" value="<?php echo $dados_endereco['complemento'];?>">
        <input type="text" name="bairro" value="<?php echo $dados_endereco['bairro'];?>">
        <input type="text" name="cidade" value="<?php echo $dados_endereco['cidade'];?>">
        <input type="text" name="estado" value="<?php echo $dados_endereco['estado'];?>">
        <input type="text" name="pais" value="<?php echo $dados_endereco['pais'];?>">
        <input type="text" name="responsavel" value="<?php echo $dados_empresa['responsavel'];?>">
        <input type="submit" value="ATUALIZAR">
    </form>
</body>
</html>