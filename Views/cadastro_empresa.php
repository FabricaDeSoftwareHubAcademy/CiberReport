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