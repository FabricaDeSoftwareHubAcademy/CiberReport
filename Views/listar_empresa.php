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
    <link rel="stylesheet" href="../assets/CSS/Componentes/tabela.css">
</head>
<body>
<h2 class="titulo-pagina">LISTAR EMPRESAS</h2>

    <a href="cadastro.php"><button>CADASTRAR</button></a>

    
    <div class="tabela-wrapper">
        <table>
            <thead>
                <tr>
                    <th data-col="0">
                        <span class="th-label">ID <i class="fa-solid fa-sort sort-icon"></i></span>
                    </th>
                    <th data-col="1">
                        <span class="th-label">NOME FANTASIA <i class="fa-solid fa-sort sort-icon"></i></span>
                    </th>
                    <th data-col="2">
                        <span class="th-label">CNPJ <i class="fa-solid fa-sort sort-icon"></i></span>
                    </th>
                    <th data-col="3">
                        <span class="th-label">RESPONSÁVEL <i class="fa-solid fa-sort sort-icon"></i></span>
                    </th>
                    <th data-col="4">
                        <span class="th-label">CIDADE/UF <i class="fa-solid fa-sort sort-icon"></i></span>
                    </th>
                    <th data-col="5">
                        <span class="th-label">TELEFONE <i class="fa-solid fa-sort sort-icon"></i></span>
                    </th>
                    <th>AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    
                    foreach($dados as $pessoa)
                    {
                ?>
                <tr>
                    <td>
                        #<?php echo $pessoa['id'] ?>
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
                        <div class="acoes">
                            <a href="editar.php?id_empresa=<?php echo $pessoa['id']; ?>" class="btn-editar" title="Editar" aria-label="Editar">
                                <button><i class="fa-solid fa-pen-to-square"></i></button>
                            </a>
                            <a href="excluir.php?id_empresa=<?php echo $pessoa['id']; ?>" class="btn-excluir" title="Excluir" aria-label="Excluir">
                                <button><i class="fa-solid fa-trash"></i></button>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="rodape-tabela">
                        <div class="paginacao">
                            <button class="pag-btn" aria-label="Página anterior">Anterior</button>
                            <button class="pag-num ativo" aria-label="Página 1" aria-current="page">1</button>
                            <button class="pag-num" aria-label="Página 2">2</button>
                            <button class="pag-num" aria-label="Página 3">3</button>
                            <button class="pag-num" aria-label="Página 4">4</button>
                            <button class="pag-num" aria-label="Página 5">5</button>
                            <button class="pag-num" aria-label="Página 6">6</button>
                            <button class="pag-btn" aria-label="Próxima página">Próximo</button>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>


    <script src="../Assets/JS/componentes/tabela.js"></script>
</body>
</html>