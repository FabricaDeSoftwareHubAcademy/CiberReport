<?php
    require "../Model/Database/conexao.php";
    require "../Model/Database/Empresa.php";
    require "../Model/Database/Endereco.php";

    $empresa = new Empresa();
    $endereco = new Endereco();

    $empresa->conectar($nome_banco, $host, $usuario_bd, $senha_bd);
    $endereco->conectar($nome_banco, $host, $usuario_bd, $senha_bd);


    $dados_empresa = [];
    $dados_endereco = [];

    if(isset($_GET['id_empresa']))
    {
        $id_empresa = addslashes($_GET['id_empresa']);
        $dados_empresa = $empresa->buscarDadosEmpresa($id_empresa);
        $dados_endereco = $endereco->buscarDadosEndereco($dados_empresa['endereco_id']);
    }


    if(isset($_POST['nome_fantasia']))
    {
        $id_empresa = addslashes($_POST['id_empresa']);
        $id_endereco = addslashes($_POST['id_endereco']);
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

        $empresa->atualizarDadosEmpresa($id_empresa,$nome_fantasia,$razao_social,$cnpj,$email_contato,$telefone,$responsavel);
        $endereco->atualizarDadosEndereco($id_endereco,$cep,$rua,$numero,$complemento,$bairro,$cidade,$estado,$pais);

        header("location:clientes.php");
        exit;
    }

    $dados = $empresa->ListarDados();
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/CSS/Componentes/menu-superior.css">
    <link rel="stylesheet" href="../assets/CSS/Componentes/menu-lateral.css">
    <link rel="stylesheet" href="../assets/CSS/style.css">
    <link rel="stylesheet" href="../assets/CSS/Componentes/button.css">
    <link rel="stylesheet" href="../assets/CSS/Componentes/tabela.css">
    <link rel="stylesheet" href="../assets/CSS/Pages/clientes.css">
</head>
<body>

    <nav id="sideBar">

    </nav>

    <div class="main-content">
        <header id="menu-superior">
 
        </header>

        <main>

            <input type="checkbox" id="abrir-modal" style="display: none" />
            <div class="button-cadastro">
                <label for="abrir-modal" class="modal-clientes-btn-abrir-sistema">
                    <i class="fa-solid fa-plus"></i><span class="texto">Novo Cadastro</span>
                </label>
            </div>


            <input type="checkbox" id="abrir-modal-edicao" style="display: none" checked />
            <div class="modal-clientes-modal-edicao">
                <div class="modal-clientes-box">
                    <div class="modal-clientes-area-titulo">
                        <div class="modal-clientes-titulo-flex">
                            <img src="../assets/img/icone_empresa.svg" alt="Empresa" class="modal-clientes-icone-topo" />
                            <div class="modal-clientes-titulo-modal">
                                <h2>Edição de Cadastro</h2>
                                <p>Informações da empresa contratante e do responsável técnico</p>
                            </div>
                        </div>
                            <label for="abrir-modal-edicao" class="modal-clientes-fechar"
                                ><i class="fa-solid fa-xmark"></i
                            ></label>
                    </div>

                    <form action="editar.php" method="post" class="modal-clientes-div-form">

                        <input type="hidden" name="id_empresa" value="<?php echo $dados_empresa['id']; ?>" />
                        <input type="hidden" name="id_endereco" value="<?php echo $dados_empresa['endereco_id']; ?>" />

                        <div class="modal-clientes-corpo-form">
                            <div class="modal-clientes-secao-label">
                                <i class="fa-solid fa-file-lines"></i>
                                <h3>Dados da Empresa</h3>
                            </div>

                            <div class="modal-clientes-form-row">
                                <div class="modal-clientes-area-dados-cliente-empresa">
                                    <label>Nome da Empresa</label>
                                    <input type="text" name="nome_fantasia" value="<?php echo $dados_empresa['nome_fantasia']; ?>" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente-racao-empresa">
                                    <label>Razão Social</label>
                                    <input type="text" name="razao_social" value="<?php echo $dados_empresa['razao_social']; ?>" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente-tel">
                                    <label>Telefone</label>
                                    <div class="modal-clientes-campo-telefone">
                                        <i class="fa fa-phone"></i>
                                        <input type="text" name="telefone" value="<?php echo $dados_empresa['telefone']; ?>" />
                                    </div>
                                </div>
                                <div class="modal-clientes-area-dados-cliente-email">
                                    <label>E-mail</label>
                                    <div class="modal-clientes-campo-email">
                                        <i class="fa-solid fa-envelope"></i>
                                        <input type="email" name="email_contato" value="<?php echo $dados_empresa['email_contato']; ?>" />
                                    </div>
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>CNPJ</label>
                                    <input type="text" name="cnpj" value="<?php echo $dados_empresa['cnpj']; ?>" />
                                </div>
                            </div>

                            <div class="modal-clientes-form-row">
                                <div class="modal-clientes-area-dados-cliente area-numero">
                                    <label>CEP</label>
                                    <input type="text" name="cep" value="<?php echo $dados_endereco['cep']; ?>" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>Endereço</label>
                                    <input type="text" name="rua" value="<?php echo $dados_endereco['rua']; ?>" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente area-numero">
                                    <label>Número</label>
                                    <input type="text" name="numero" value="<?php echo $dados_endereco['numero']; ?>" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>Complemento</label>
                                    <input type="text" name="complemento" value="<?php echo $dados_endereco['complemento']; ?>" />
                                </div>
                            </div>

                            <div class="modal-clientes-form-row">
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>Bairro</label>
                                    <input type="text" name="bairro" value="<?php echo $dados_endereco['bairro']; ?>" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>Cidade</label>
                                    <input type="text" name="cidade" value="<?php echo $dados_endereco['cidade']; ?>" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>Estado</label>
                                    <input type="text" name="estado" value="<?php echo $dados_endereco['estado']; ?>" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>País</label>
                                    <input type="text" name="pais" value="<?php echo $dados_endereco['pais']; ?>" />
                                </div>
                            </div>
                            <div class="modal-clientes-secao-label">
                                <i class="fa-solid fa-user"></i>
                                <h3>Dados do Responsável</h3>
                            </div>
                            <div class="modal-clientes-form-row">
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>Nome do Responsável</label>
                                    <input type="text" name="responsavel" value="<?php echo $dados_empresa['responsavel']; ?>" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente-tel">
                                    <label>Telefone</label>
                                    <div class="modal-clientes-campo-telefone">
                                        <i class="fa fa-phone"></i>
                                        <input type="text" name="telefone_responsavel" placeholder="(11)99999-9999" />
                                    </div>
                                </div>
                                <div class="modal-clientes-area-dados-cliente-email">
                                    <label>E-mail</label>
                                    <div class="modal-clientes-campo-email">
                                        <i class="fa-solid fa-envelope"></i>
                                        <input type="email" name="email_responsavel" placeholder="email@.com" />
                                    </div>
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>CPF</label>
                                    <input type="text" name="cpf" placeholder="000.000.000-00" />
                                </div>
                            </div>
                            <div class="modal-clientes-botoes-form">
                                <label href="clientes.php" for="abrir-modal" class="modal-clientes-btn-cancelar">CANCELAR</label>
                                <button type="submit" class="modal-clientes-btn-salvar">SALVAR</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <div class="tabela-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th data-col="0">
                                <span class="th-label">ID <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="1">
                                <span class="th-label">Nome da Empresa <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="2">
                                <span class="th-label">CNPJ <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="3">
                                <span class="th-label">Responsável <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="4">
                                <span class="th-label">Email <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="5">
                                <span class="th-label">Telefone <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="6">
                                <span class="th-label">Status <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($dados as $emp){ ?>
                        <tr>
                            <td>#<?php echo $emp['id']; ?></td>
                            <td><?php echo $emp['nome_fantasia']; ?></td>
                            <td><?php echo $emp['cnpj']; ?></td>
                            <td><?php echo $emp['responsavel']; ?></td>
                            <td><?php echo isset($emp['email_contato']) ? $emp['email_contato'] : '---'; ?></td>
                            <td><?php echo $emp['telefone']; ?></td>
                            <td>
                                <span class="status status-concluido">Ativo</span>
                            </td>
                            <td>
                                <div class="acoes">
                                    <a href="editar.php?id_empresa=<?php echo $emp['id']; ?>" class="btn-editar" title="Editar" aria-label="Editar">
                                        <button><i class="fa-solid fa-pen-to-square"></i></button>
                                    </a>
                                    <a href="excluir.php?id_empresa=<?php echo $emp['id']; ?>" class="btn-excluir" title="Excluir" aria-label="Excluir">
                                        <button><i class="fa-solid fa-trash"></i></button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8" class="rodape-tabela">
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

        </main>
    </div>
    <script src="../Assets/JS/componentes/tabela.js"></script>

</body>
</html>