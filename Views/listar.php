<?php
    require "../Model/Database/Empresa.php";

    $empresa = new Empresa();
    $empresa->conectar("clientesbaikal","localhost","root","");

    $dados = $empresa->ListarDados();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Empresas</title>
</head>
<body>
    <h2 class="titulo-pagina">LISTAR EMPRESAS</h2>

    <a href="cadastro.php"><button>CADASTRAR</button></a>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>NOME FANTASIA</th>
                <th>CNPJ</th>
                <th>RESPONSÁVEL</th>
                <th>CIDADE/UF</th>
                <th>TELEFONE</th>
                <th>AÇÕES</th>
            </tr>
        </thead>
        <?php
            foreach($dados as $pessoa)
            {
        ?>
        <tbody>
            <tr>
                <td>
                    <?php echo $pessoa['id'] ?>
                </td>

                <td>
                    <?php echo $pessoa['nome_fantasia'] ?>
                </td>

                <td>
                    <?php echo $pessoa['cnpj'] ?>
                </td>

                <td>
                    <?php echo $pessoa['responsavel'] ?>
                </td>

                <td>
                    <?php echo $pessoa['cidade'] . " / " . $pessoa['estado'] ?>
                </td>

                <td>
                    <?php echo $pessoa['telefone'] ?>
                </td>

                <td>
                    <a href="editar.php?id_empresa=<?php echo $pessoa['id']; ?>">EDITAR</a>
                    <a href="excluir.php?id_empresa=<?php echo $pessoa['id']; ?>">EXCLUIR</a>
                </td>
            </tr>
        </tbody>
        <?php
            }
        ?>
    </table>
</body>
</html>