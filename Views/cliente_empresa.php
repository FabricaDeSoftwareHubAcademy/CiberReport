<?php
require_once "../Model/Database/Endereco.php";


$endereco = new Endereco();


$mensagem_erro = "";

$dados_empresa_editar = [];
$dados_endereco_editar = [];
if (isset($_GET['id_empresa'])) {
    $id_empresa_editar = addslashes($_GET['id_empresa']);
    $dados_empresa_editar = $empresa->buscarDadosEmpresa($id_empresa_editar);
    $dados_endereco_editar = $endereco->buscarDadosEndereco($dados_empresa_editar['endereco_id']);
}


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

<?php $tituloPagina = 'Clientes'; include 'menu.php'; ?>
<main>


    <section class="listar-clientes">


        <div class="button-cadastro">
            <button class="btn-novo-cadastro btn-modal-novo-cadastro" data-modal-target="modalClientes">
                <i class="fa-solid fa-plus"></i><span class="texto">Novo Cadastro</span>
            </button>
        </div>

        <div class="modal-overlay" id="modalClientes"
            <?php if (!empty($mensagem_erro)) echo 'style="visibility:visible;opacity:1;"'; ?>>
            <div class="modal modal--xxl">

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
                            <div class="modal-grade modal-grade--4">

                                <div class="campo">
                                    <label class="campo__label">Nome da Empresa</label>
                                    <input type="text" name="nome_fantasia" class="campo__input" placeholder="Digite o nome da Empresa" />
                                </div>
                            </div>

                            <div class="modal-grade modal-grade--4"> <!-- Mudar aqui está errado para um modal-grade--4 -->
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

                            <div class="modal-grade modal-grade--4">
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
        
        <div class="modal-overlay" id="modalEditar"
            <?php if (!empty($dados_empresa_editar)) echo 'style="visibility:visible;opacity:1;"'; ?>>
            <div class="modal modal--xl">

                <div class="modal__header">
                    <div class="modal__header-icone">
                        <img src="../assets/img/icone_empresa.svg" alt="Empresa" />
                    </div>
                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Edição de Cadastro</h2>
                        <p class="modal__subtitulo">Informações da empresa contratante e do responsável técnico</p>
                    </div>
                    <a href="cliente_empresa.php" class="modal__fechar">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                </div>

                <form action="editar_empresa.php?id_empresa=<?php echo $dados_empresa_editar['id'] ?? ''; ?>" method="post">
                    <input type="hidden" name="id_empresa" value="<?php echo $dados_empresa_editar['id'] ?? ''; ?>" />
                    <input type="hidden" name="id_endereco" value="<?php echo $dados_empresa_editar['endereco_id'] ?? ''; ?>" />

                    <div class="modal__body">

                        <div class="modal-secao">
                            <div class="modal-secao__titulo">
                                <i class="fa-solid fa-file-lines modal-secao__titulo-icone"></i>
                                <h3>Dados da Empresa</h3>
                            </div>
                            <div class="modal-grade modal-grade--4">
                                <div class="campo">
                                    <label class="campo__label">Nome da Empresa</label>
                                    <input type="text" name="nome_fantasia" class="campo__input" value="<?php echo $dados_empresa_editar['nome_fantasia'] ?? ''; ?>" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Razão Social</label>
                                    <input type="text" name="razao_social" class="campo__input" value="<?php echo $dados_empresa_editar['razao_social'] ?? ''; ?>" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Telefone</label>
                                    <div class="campo__input-wrapper">
                                        <i class="fa fa-phone campo__input-icone"></i>
                                        <input type="text" name="telefone" class="campo__input campo__input--com-icone-esq" value="<?php echo $dados_empresa_editar['telefone'] ?? ''; ?>" />
                                    </div>
                                </div>
                                <div class="campo">
                                    <label class="campo__label">E-mail</label>
                                    <div class="campo__input-wrapper">
                                        <i class="fa-solid fa-envelope campo__input-icone"></i>
                                        <input type="email" name="email_contato" class="campo__input campo__input--com-icone-esq" value="<?php echo $dados_empresa_editar['email_contato'] ?? ''; ?>" />
                                    </div>
                                </div>
                                <div class="campo">
                                    <label class="campo__label">CNPJ</label>
                                    <input type="text" name="cnpj" class="campo__input" value="<?php echo $dados_empresa_editar['cnpj'] ?? ''; ?>" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">CEP</label>
                                    <input type="text" name="cep" class="campo__input" value="<?php echo $dados_endereco_editar['cep'] ?? ''; ?>" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Endereço</label>
                                    <input type="text" name="rua" class="campo__input" value="<?php echo $dados_endereco_editar['rua'] ?? ''; ?>" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Número</label>
                                    <input type="text" name="numero" class="campo__input" value="<?php echo $dados_endereco_editar['numero'] ?? ''; ?>" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Complemento</label>
                                    <input type="text" name="complemento" class="campo__input" value="<?php echo $dados_endereco_editar['complemento'] ?? ''; ?>" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Bairro</label>
                                    <input type="text" name="bairro" class="campo__input" value="<?php echo $dados_endereco_editar['bairro'] ?? ''; ?>" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Cidade</label>
                                    <input type="text" name="cidade" class="campo__input" value="<?php echo $dados_endereco_editar['cidade'] ?? ''; ?>" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Estado</label>
                                    <input type="text" name="estado" class="campo__input" value="<?php echo $dados_endereco_editar['estado'] ?? ''; ?>" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">País</label>
                                    <input type="text" name="pais" class="campo__input" value="<?php echo $dados_endereco_editar['pais'] ?? ''; ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="modal-secao">
                            <div class="modal-secao__titulo">
                                <i class="fa-solid fa-user modal-secao__titulo-icone"></i>
                                <h3>Dados do Responsável</h3>
                            </div>
                            <div class="modal-grade modal-grade--4">
                                <div class="campo">
                                    <label class="campo__label">Nome do Responsável</label>
                                    <input type="text" name="responsavel" class="campo__input" value="<?php echo $dados_empresa_editar['responsavel'] ?? ''; ?>" />
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
                        <a href="cliente_empresa.php" class="btn-cancelar">CANCELAR</a>
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
                            <span class="th-label">Nome do Pentest <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="2">
                            <span class="th-label">Breve Descrição</span>
                        </th>
                        <th data-col="3">
                            <span class="th-label">Categoria <i class="fa-solid fa-filter sort-icon"></i></span>
                        </th>
                        <th data-col="4">
                            <span class="th-label">Modelo <i class="fa-solid fa-filter sort-icon"></i></span>
                        </th>
                        <th data-col="5">
                            <span class="th-label">Técnica <i class="fa-solid fa-filter sort-icon"></i></span>
                        </th>
                        <th data-col="6">
                            <span class="th-label">Frameworks</span>
                        </th>
                        <th data-col="7">
                            <span class="th-label">Checklist</span>
                        </th>
                        <th data-col="8">
                            <span class="th-label">Status</span>
                        </th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pentests as $pentest): ?>
                        <?php
                        $frameworks = array_filter(array_map('trim', explode(',', $pentest['frameworks'] ?? '')));
                        $ativo = (bool) $pentest['habilitado'];
                        ?>
                        <tr>
                            <td><?= $pentest['id'] ?></td>
                            <td><?= htmlspecialchars($pentest['nome']) ?></td>
                            <td class="ger-pentest-col-descricao"><?= htmlspecialchars($pentest['descricao_breve']) ?></td>
                            <td>
                                <span class="ger-pentest-cat-badge">
                                    <?= htmlspecialchars($pentest['categoria']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($pentest['modelo']) ?></td>
                            <td><?= htmlspecialchars($pentest['tecnica']) ?></td>
                            <td>
                                <div class="ger-pentest-frameworks-list">
                                    <?php foreach (array_slice($frameworks, 0, 2) as $fw): ?>
                                        <span class="ger-pentest-framework-tag"><?= htmlspecialchars($fw) ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($frameworks) > 2): ?>
                                        <span class="ger-pentest-framework-tag ger-pentest-tag-mais">+<?= count($frameworks) - 2 ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($pentest['checklist'] ?? '') ?></td>
                            <td>
                                <label class="ger-pentest-toggle-switch">
                                    <input type="checkbox" <?= $ativo ? 'checked' : '' ?> data-id="<?= $pentest['id'] ?>" onchange="toggleHabilitado(this)">
                                    <span class="ger-pentest-toggle-slider"></span>
                                    <span class="ger-pentest-toggle-label"><?= $ativo ? 'Ativo' : 'Inativo' ?></span>
                                </label>
                            </td>
                            <td>
                                <div class="acoes">
                                    <button class="btn-editar" title="Editar" aria-label="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="gerenciarPentest.php?excluir=<?= $pentest['id'] ?>" class="btn-excluir" title="Excluir" aria-label="Excluir" onclick="return confirm('Excluir este pentest?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($pentests)): ?>
                        <tr>
                            <td colspan="10" style="text-align:center">Nenhum tipo de pentest cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10" class="rodape-tabela">
                            <div class="paginacao"></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>


    </section>
</main>
<!-- Fecha .main-content e .menu abertos pelo menu.php. Necessário para que o <main>
     fique dentro do flex row da sidebar, permitindo o comportamento de "empurrar"
     o conteúdo quando o menu lateral abre/fecha. -->
</div>
</div>


<script src="../assets/JS/componentes/modal.js"></script>

</html>