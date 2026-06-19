<?php
require_once "../Model/Database/conexao.php";
require_once "../Model/Database/Empresa.php";
require_once "../Model/Database/Endereco.php";

$empresa = new Empresa();
$endereco = new Endereco();

$empresa->conectar($nome_banco, $host, $usuario_bd, $senha_bd);
$endereco->conectar($nome_banco, $host, $usuario_bd, $senha_bd);

$dados = $empresa->ListarDados();

$mensagem_erro = "";

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
            header("location:clientes.php");
            exit;
        }
        else
        {
            $mensagem_erro = "Empresa já cadastrada!";
        }
    }
    else
    {
        $mensagem_erro = "Preencha todos os campos!";
    }
}
?>


<!doctype html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Cadastro de Clientes</title>
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
            integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
        />
        <link rel="stylesheet" href="../assets/CSS/Componentes/menu-superior.css">
        <link rel="stylesheet" href="../assets/CSS/Componentes/menu-lateral.css">
        <link rel="stylesheet" href="../assets/CSS/style.css">
        <link rel="stylesheet" href="../assets/CSS/Componentes/button.css">
        <link rel="stylesheet" href="../assets/CSS/Componentes/tabela.css">
        <link rel="stylesheet" href="../assets/CSS/Pages/clientes.css">
        <link rel="stylesheet" href="../assets/CSS/Componentes/tabela.css">


    </head>

    <body>

        <nav id="sideBar">
            <div class="sidebar_content">
                <div class="logo">
                    <div class="logo-nome">
                        <img src="../IMG/image-removebg-preview.svg" alt="">
                        <img src="../IMG/Logo Baikal.svg" alt="">
                    </div>
                    <div class="logo-imagem">
                        <img src="../IMG/Logo Baikal-1.svg" alt="">
                    </div>

                </div>
                <ul id="side_itens">
                    <li class="side_item">
                        <a href="#">
                            <i class="fa-solid fa-chart-column"></i>
                            <span class="item_description">Dashboard</span>
                        </a>
                    </li>
                    <li class="side_item">
                        <a href="#">
                            <i class="fa-solid fa-file-lines"></i>
                            <span class="item_description">Relatórios</span>
                        </a>
                    </li>
                    <li class="side_item">
                        <a href="#">
                            <i class="fa-solid fa-building-user"></i>
                            <span class="item_description">Gestão</span>
                            <i class="fa-solid fa-angle-down"  id="dropdown"></i>
                        </a>
                    </li>
                        <ul id="submenu">
                            <li class="item-submenu">
                                <a href="#">
                                    <i class="fa-solid fa-users"></i>
                                    <span class="item_description">Usuários</span>
                                </a>
                            </li>
                            <li class="item-submenu">
                                <a href="#">
                                    <i class="fa-solid fa-address-book"></i>
                                    <span class="item_description">Clientes</span>
                                </a>
                            </li>
                            <li class="item-submenu">
                                <a href="#">
                                    <i class="fa-solid fa-terminal"></i>
                                    <span class="item_description">Projetos</span>
                                </a>
                            </li>
                            <li class="item-submenu ">
                                <a href="#">
                                    <i class="fa-solid fa-user-secret"></i>
                                    <span class="item_description">Pentest</span>
                                </a>
                            </li>
                        </ul>
                        <li class="side_item">
                            <a href="#">
                                <i class="fa-solid fa-list-check"></i>
                                <span class="item_description">Checklist</span>
                            </a>
                        </li>
                        <li class="side_item">
                            <a href="#">
                                <i class="fa-solid fa-bug"></i>
                                <span class="item_description">Vulnerabilidades</span>
                            </a>
                        </li>
                        <li class="side_item">
                            <a href="#">
                                <i class="fa-solid fa-book-open"></i>
                                <span class="item_description">Conhecimento</span>
                            </a>
                        </li>
                        <li class="side_item">
                            <a href="#">
                                <i class="fa-solid fa-clipboard"></i>
                                <span class="item_description">Logs</span>
                            </a>
                        </li>
                        <li class="side_item">
                            <a href="#">
                                <i class="fa-solid fa-gear"></i>
                                <span class="item_description">Perfis de acesso</span>
                            </a>
                        </li>
                    </ul>
                <button id="open_btn">
                    <i class="fa-solid fa-chevron-right" id="open_btn_icon"></i>
                </button>

                <!-- Menu escondido quando fechado -->
            </div>
            <div id="logout">
                <button id="logout_btn">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span class="item_description">
                        Logout
                    </span>
                </button>
            </div>
        </nav>

        <div class="main-content">
            <header id="menu-superior">
                <h1>Titulo da pagina</h1>
                <div class="input-container">
                    <input type="text" placeholder="">
                    <button>
                        <i class="fa-brands fa-sistrix"></i>
                    </button>
                </div>

                <div class="perfis">
                    <i class="fa-regular fa-bell notificacao"></i>
                    <img class="imagem-usuario" src="../IMG/fotoDePerfil.jpg" alt="">
                    <div class="description-user">
                        <p>Marcos antonio</p>
                        <p>Gerente</p>
                    </div>
                </div>
            </header>

            <main>


                <input type="checkbox" id="abrir-modal" style="display: none"
                    <?php if(!empty($mensagem_erro)) echo 'checked'; ?> />
                <div class="button-cadastro">
                    <label for="abrir-modal" class="modal-clientes-btn-abrir-sistema"><i class="fa-solid fa-plus"></i><span class="texto">Novo Cadastro</span></label>
                </div>
                <div class="modal-clientes-modal">
                    <div class="modal-clientes-box">
                        <div class="modal-clientes-area-titulo">
                            <div class="modal-clientes-titulo-flex">
                                <img src="../assets/img/icone_empresa.svg" alt="Empresa" class="modal-clientes-icone-topo" />
                                <div class="modal-clientes-titulo-modal">
                                    <h2>Cadastro de Empresa</h2>
                                    <p>Informações da empresa contratante e do responsável técnico</p>
                                </div>
                            </div>
                            <label for="abrir-modal" class="modal-clientes-fechar"><i class="fa-solid fa-xmark"></i></label>
                        </div>

                        <form action="" method="post" class="modal-clientes-div-form">
                            <div class="modal-clientes-corpo-form">
                                <div class="modal-clientes-secao-label">
                                    <i class="fa-solid fa-file-lines"></i>
                                    <h3>Dados da Empresa</h3>
                                </div>

                                <div class="modal-clientes-form-row">
                                        <div class="modal-clientes-area-dados-cliente-empresa">
                                            <label>Nome da Empresa</label>
                                            <input type="text" name="nome_fantasia" placeholder="Digite o nome da Empresa" />
                                        </div>
                                        <div class="modal-clientes-area-dados-cliente-racao-empresa">
                                            <label>Razão Social</label>
                                            <input type="text" name="razao_social" placeholder="Digite a razão social" />
                                        </div>
                                        <div class="modal-clientes-area-dados-cliente-tel">
                                            <label>Telefone</label>
                                            <div class="modal-clientes-campo-telefone">
                                                <i class="fa fa-phone"></i>
                                                <input type="text" name="telefone" placeholder="(11) 9999-9999" />
                                            </div>
                                        </div>
                                        <div class="modal-clientes-area-dados-cliente-email">
                                            <label>E-mail</label>
                                            <div class="modal-clientes-campo-email">
                                                <i class="fa-solid fa-envelope"></i>
                                                <input type="email" name="email_contato" placeholder="email@.com" />
                                            </div>
                                        </div>
                                        <div class="modal-clientes-area-dados-cliente">
                                            <label>CNPJ</label>
                                            <input type="text" name="cnpj" placeholder="23.456.789/0001-01" />
                                        </div>
                                </div>

                                <div class="modal-clientes-form-row">
                                        <div class="modal-clientes-area-dados-cliente area-numero">
                                            <label>CEP</label>
                                            <input type="text" name="cep" id="cep" placeholder="12345-678" onblur="buscarCep()" />
                                        </div>
                                        <div class="modal-clientes-area-dados-cliente">
                                            <label>Endereço</label>
                                            <input type="text" name="rua" id="endereco" placeholder="Digite o endereço" />
                                        </div>
                                        <div class="modal-clientes-area-dados-cliente area-numero">
                                            <label>Número</label>
                                            <input type="text" name="numero" placeholder="0000" />
                                        </div>
                                        <div class="modal-clientes-area-dados-cliente">
                                            <label>Complemento</label>
                                            <input type="text" name="complemento" placeholder="Complemento" />
                                        </div>
                                </div>

                                <div class="modal-clientes-form-row">
                                        <div class="modal-clientes-area-dados-cliente">
                                            <label>Bairro</label>
                                            <input type="text" name="bairro" id="bairro" placeholder="Digite o bairro" />
                                        </div>
                                        <div class="modal-clientes-area-dados-cliente">
                                            <label>Cidade</label>
                                            <input type="text" name="cidade" id="cidade" placeholder="Selecione uma Cidade" />
                                        </div>
                                        <div class="modal-clientes-area-dados-cliente">
                                            <label>Estado</label>
                                            <input type="text" name="estado" id="estado" placeholder="Selecione um Estado" />
                                        </div>
                                        <div class="modal-clientes-area-dados-cliente">
                                            <label>País</label>
                                            <input type="text" name="pais" id="pais" value="Brasil" required/>
                                        </div>
                                </div>

                                <div class="modal-clientes-secao-label">
                                        <i class="fa-solid fa-user"></i>
                                        <h3>Dados do Responsável</h3>
                                </div>

                                <div class="modal-clientes-form-row">
                                        <div class="modal-clientes-area-dados-cliente">
                                            <label>Nome do Responsável</label>
                                            <input type="text" name="responsavel" placeholder="Digite o nome" />
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
                                        <?php if(!empty($mensagem_erro)): ?>
                                            <p class="modal-clientes-msg-erro">
                                                <i class="fa-solid fa-circle-exclamation"></i>
                                                <?php echo $mensagem_erro; ?>
                                            </p>
                                        <?php endif; ?>
                                        <label for="abrir-modal" class="modal-clientes-btn-cancelar">CANCELAR</label>
                                        <button type="submit" class="modal-clientes-btn-salvar">SALVAR</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="section-tabela">
                    <table class="tabela-clientes">
                        <thead>
                            <tr>
                                <th>Nome da Empresa <i class="fa-solid fa-arrow-up-long sort-icon"></i><i class="fa-solid fa-filter filter-icon"></i></th>
                                <th>CNPJ <i class="fa-solid fa-filter filter-icon"></i></th>
                                <th>Responsável <i class="fa-solid fa-filter filter-icon"></i></th>
                                <th>Email <i class="fa-solid fa-filter filter-icon"></i></th>
                                <th>Telefone <i class="fa-solid fa-filter filter-icon"></i></th>
                                <th>Status <i class="fa-solid fa-filter filter-icon"></i></th>
                                <th>Ações <i class="fa-solid fa-filter filter-icon"></i></th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach($dados as $empresa){ ?>

                            <tr>

                                <td>
                                    <?php echo $empresa['nome_fantasia']; ?>
                                </td>

                                <td>
                                    <?php echo $empresa['cnpj']; ?>
                                </td>

                                <td>
                                    <?php echo $empresa['responsavel']; ?>
                                </td>

                                <td>
                                    <?php echo $empresa['email_contato']; ?>
                                </td>

                                <td>
                                    <?php echo $empresa['telefone']; ?>
                                </td>

                                <td>
                                    <span class="badge-status ativo">Ativo</span>
                                </td>

                                <td class="coluna-acoes">

                                    <a href="editar.php?id_empresa=<?php echo $empresa['id']; ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <a href="excluir.php?id_empresa=<?php echo $empresa['id']; ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>

                                </td>

                            </tr>

                            <?php } ?>

                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </body>
</html>