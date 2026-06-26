<?php
require_once "../Model/conexao.php";
require_once "../Model/Database/Empresa.php";
require_once "../Model/Database/Endereco.php";

$empresa = new Empresa();
$endereco = new Endereco();

$empresa->conectar($banco, $host, $user, $pass);
$endereco->conectar($banco, $host, $user, $pass);


$dados = $empresa->ListarDados();

$mensagem_erro = "";

if (isset($_POST['nome_fantasia'])) {
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

    if (!empty($nome_fantasia) && !empty($razao_social) && !empty($telefone) && !empty($email_contato) && !empty($cnpj) && !empty($responsavel) && !empty($cep) && !empty($rua) && !empty($numero) && !empty($bairro) && !empty($cidade) && !empty($estado)) {
        $id_endereco_novo = $endereco->cadastrarEndereco($cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $pais);

        if ($empresa->cadastrarEmpresa($id_endereco_novo, $nome_fantasia, $razao_social, $cnpj, $email_contato, $telefone, $responsavel)) {
            header("location:clientes.php");
            exit;
        } else {
            $mensagem_erro = "Empresa já cadastrada!";
        }
    } else {
        $mensagem_erro = "Preencha todos os campos!";
    }
}



?>


<link rel="stylesheet" href="../assets/CSS/Componentes/button.css">
<link rel="stylesheet" href="../assets/CSS/Pages/clientes.css">
<link rel="stylesheet" href="../assets/CSS/Componentes/tabela.css">
<link rel="stylesheet" href="../assets/CSS/Componentes/componentes-modal.css">
<link rel="stylesheet" href="../assets/CSS/Componentes/modal.css">

<?php include 'menu.php'; ?>
<main>


    <section class="listar-clientes">


        <div class="button-cadastro">
            <button class="btn-novo-cadastro btn-modal-novo-cadastro" data-modal-target="modalClientes">
                <i class="fa-solid fa-plus"></i><span class="texto">Novo Cadastro</span>
            </button>
        </div>

        <div class="modal-overlay" id="modalClientes"
            <?php if (!empty($mensagem_erro)) echo 'style="visibility:visible;opacity:1;"'; ?>>
            <div class="modal modal--xl">

                <div class="modal__header">
                    <div class="modal__header-icone">
                        <img src="../assets/img/icone_empresa.svg" alt="Empresa" />
                    </div>
                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Cadastro de Empresa</h2>
                        <p class="modal__subtitulo">Informações da empresa contratante e do responsável técnico</p>
                    </div>
                    <button type="button" class="modal__fechar" data-modal-close="modalClientes">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form action="" method="post">
                    <div class="modal__body">

                        <div class="modal-secao">
                            <div class="modal-secao__titulo">
                                <i class="fa-solid fa-file-lines modal-secao__titulo-icone"></i>
                                <h3>Dados da Empresa</h3>
                            </div>

                            <div class="modal-grade modal-grade--3"> <!-- Mudar aqui está errado para um modal-grade--4 -->
                                <div class="campo">
                                    <label class="campo__label">Nome da Empresa</label>
                                    <input type="text" name="nome_fantasia" class="campo__input" placeholder="Digite o nome da Empresa" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Razão Social</label>
                                    <input type="text" name="razao_social" class="campo__input" placeholder="Digite a razão social" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Telefone</label>
                                    <div class="campo__input-wrapper">
                                        <i class="fa fa-phone campo__input-icone"></i>
                                        <input type="text" name="telefone" class="campo__input campo__input--com-icone-esq" placeholder="(11) 9999-9999" />
                                    </div>
                                </div>
                                <div class="campo">
                                    <label class="campo__label">E-mail</label>
                                    <div class="campo__input-wrapper">
                                        <i class="fa-solid fa-envelope campo__input-icone"></i>
                                        <input type="email" name="email_contato" class="campo__input campo__input--com-icone-esq" placeholder="email@.com" />
                                    </div>
                                </div>
                                <div class="campo">
                                    <label class="campo__label">CNPJ</label>
                                    <input type="text" name="cnpj" class="campo__input" placeholder="23.456.789/0001-01" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">CEP</label>
                                    <input type="text" name="cep" id="cep" class="campo__input" placeholder="12345-678" onblur="buscarCep()" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Endereço</label>
                                    <input type="text" name="rua" id="endereco" class="campo__input" placeholder="Digite o endereço" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Número</label>
                                    <input type="text" name="numero" class="campo__input" placeholder="0000" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Complemento</label>
                                    <input type="text" name="complemento" class="campo__input" placeholder="Complemento" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Bairro</label>
                                    <input type="text" name="bairro" id="bairro" class="campo__input" placeholder="Digite o bairro" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Cidade</label>
                                    <input type="text" name="cidade" id="cidade" class="campo__input" placeholder="Selecione uma Cidade" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Estado</label>
                                    <input type="text" name="estado" id="estado" class="campo__input" placeholder="Selecione um Estado" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">País</label>
                                    <input type="text" name="pais" id="pais" class="campo__input" value="Brasil" required />
                                </div>
                            </div>
                        </div>

                        <div class="modal-secao">
                            <div class="modal-secao__titulo">
                                <i class="fa-solid fa-user modal-secao__titulo-icone"></i>
                                <h3>Dados do Responsável</h3>
                            </div>

                            <div class="modal-grade modal-grade--3">
                                <div class="campo">
                                    <label class="campo__label">Nome do Responsável</label>
                                    <input type="text" name="responsavel" class="campo__input" placeholder="Digite o nome" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Telefone</label>
                                    <div class="campo__input-wrapper">
                                        <i class="fa fa-phone campo__input-icone"></i>
                                        <input type="text" name="telefone_responsavel" class="campo__input campo__input--com-icone-esq" placeholder="(11)99999-9999" />
                                    </div>
                                </div>
                                <div class="campo">
                                    <label class="campo__label">E-mail</label>
                                    <div class="campo__input-wrapper">
                                        <i class="fa-solid fa-envelope campo__input-icone"></i>
                                        <input type="email" name="email_responsavel" class="campo__input campo__input--com-icone-esq" placeholder="email@.com" />
                                    </div>
                                </div>
                                <div class="campo">
                                    <label class="campo__label">CPF</label>
                                    <input type="text" name="cpf" class="campo__input" placeholder="000.000.000-00" />
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="modal__footer">
                        <?php if (!empty($mensagem_erro)): ?>
                            <p class="campo__mensagem-erro" style="margin-right: auto;">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <?php echo $mensagem_erro; ?>
                            </p>
                        <?php endif; ?>
                        <button type="button" class="btn-cancelar" data-modal-close="modalClientes">CANCELAR</button>
                        <button type="submit" class="btn-botao-verde">SALVAR</button>
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
                    <?php foreach ($dados as $empresa) { ?>
                        <tr>
                            <td>#<?php echo $empresa['id']; ?></td>
                            <td><?php echo $empresa['nome_fantasia']; ?></td>
                            <td><?php echo $empresa['cnpj']; ?></td>
                            <td><?php echo $empresa['responsavel']; ?></td>
                            <td><?php echo isset($empresa['email_contato']) ? $empresa['email_contato'] : '---'; ?></td>
                            <td><?php echo $empresa['telefone']; ?></td>
                            <td>

                                <span class="status status-concluido">Ativo</span>
                            </td>
                            <td>
                                <div class="acoes">

                                    <a href="editar_empresa.php?id_empresa=<?php echo $empresa['id']; ?>" class="btn-editar" title="Editar" aria-label="Editar">
                                        <button><i class="fa-solid fa-pen-to-square"></i></button>
                                    </a>
                                    <a href="excluir_empresa.php?id_empresa=<?php echo $empresa['id']; ?>" class="btn-excluir" title="Excluir" aria-label="Excluir">
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
    </section>
</main>

<script src="../Assets/JS/componentes/menu.js"></script>
<script src="../assets/JS/componentes/modal.js"></script>

</html>