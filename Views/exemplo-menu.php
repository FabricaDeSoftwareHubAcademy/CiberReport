<?php
    require "../Model/Database/Empresa.php";
    require "../Model/Database/Endereco.php";

    $empresa = new Empresa();
    $endereco = new Endereco();

    $empresa->conectar("clientesbaikal","localhost","root","");
    $endereco->conectar("clientesbaikal","localhost","root","");

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

        if(!empty($nome_fantasia) && !empty($razao_social) && !empty($telefone) && !empty($email_contato) && !empty($cnpj) && !empty($responsavel) && !empty($cep) && !empty($rua) && !empty($numero) && !empty($bairro) && !empty($cidade) && !empty($estado))
        {
        
            $id_endereco_novo = $endereco->cadastrarEndereco($cep,$rua,$numero,$complemento,$bairro,$cidade,$estado,$pais);

          
            if($empresa->cadastrarEmpresa($id_endereco_novo,$nome_fantasia,$razao_social,$cnpj,$email_contato,$telefone,$responsavel))
            {
                header("location:listar.php");
            }
            else
            {
                echo "Empresa já cadastrada!";
            }
        }
        else
        {
            echo "Preencha todos os campos!";
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" ... />
    <link rel="stylesheet" href="../Assets/CSS/style.css" />
    <title>CADASTRO Empresa</title>
</head>
<body>
    <?php include_once 'menu-lateral.php'; ?> <!-- o menu lateral vem aqui -->
    <div>
        <?php include_once 'menu-superior.php'; ?> <!-- O menu lateral separa posto dentro de uma div -->
        <main>
            <h2 class="titulo-pagina">CADASTRO DE EMPRESA</h2>
            <form method="post">
                <input type="text" name="nome_fantasia" placeholder="Nome da Empresa">
                <input type="text" name="razao_social" placeholder="Razão Social">
                <input type="tel" name="telefone" placeholder="Telefone">
                <input type="email" name="email_contato" placeholder="E-mail">
                <input type="text" name="cnpj" placeholder="CNPJ">
                <input type="text" name="cep" placeholder="CEP">
                <input type="text" name="rua" placeholder="Endereço">
                <input type="text" name="numero" placeholder="Número">
                <input type="text" name="complemento" placeholder="Complemento">
                <input type="text" name="bairro" placeholder="Bairro">
                <input type="text" name="cidade" placeholder="Cidade">
                <input type="text" name="estado" placeholder="Estado">
                <input type="text" name="pais" placeholder="País" value="Brasil">
                <input type="text" name="responsavel" placeholder="Nome do Responsável">
                <button type="submit" class="modal-clientes-btn-salvar">SALVAR</button>
            </form>
        </main>
    </div>
</body>
</html>