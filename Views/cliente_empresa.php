<?php
require_once __DIR__ . "/../Controller/CadastroEmpresaController.php";

$controller = new CadastroEmpresaController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'alterarHabilitado') {
    $id = addslashes($_POST['id'] ?? '');
    $habilitado = addslashes($_POST['habilitado'] ?? '');
    $controller->alterarStatusClientes($id, $habilitado);
    exit;
}

$mensagem_erro = "";
if (isset($_GET['excluir'])){
    $controller->excluirClientes($_GET['excluir']);
    header("Location: cliente_empresa.php");
    exit;
}

$dados_empresa_editar = [];
$dados_endereco_editar = [];
if (isset($_GET['id_empresa'])) {
    $id_empresa_editar = addslashes($_GET['id_empresa']);
    $dados_empresa_editar = $controller->buscarDadosEmpresa($id_empresa_editar);
    $dados_endereco_editar = $controller->buscarDadosEnderecoEmpresa($dados_empresa_editar['endereco_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_empresa']) && $_POST['id_empresa'] !== ''){
    $controller->editarEmpresa();
    header("Location: cliente_empresa.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_fantasia'])){
    $resultado = $controller->cadastrarEmpresa();

    if ($resultado === true){
        header("Location: cliente_empresa.php");
        exit;
    }else{
        $mensagem_erro = $resultado;
    }
}

$dados = $controller->listarEmpresa();

?>

<link rel="stylesheet" href="../assets/CSS/style.css">
<link rel="stylesheet" href="../assets/CSS/Componentes/button.css">
<link rel="stylesheet" href="../assets/CSS/Pages/clientes.css">
<link rel="stylesheet" href="../assets/CSS/Componentes/tabela.css">
<link rel="stylesheet" href="../assets/CSS/Componentes/componentes-modal.css">
<link rel="stylesheet" href="../assets/CSS/Componentes/modal.css">

<?php $tituloPagina = 'Clientes'; include 'Components/menu.php'; ?>
<main>


    <section class="listar-clientes">


        <div class="button-cadastro">
            <button class="btn-novo-cadastro btn-modal-novo-cadastro" data-modal-target="modalClientes">
                <i class="fa-solid fa-plus"></i><span class="texto">Novo Cadastro</span>
            </button>
        </div>

        <div class="modal-overlay<?= !empty($mensagem_erro) ? ' active' : '' ?>" id="modalClientes">
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

                            <div class="modal-grade modal-grade--4">
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
<!-- ERRO NESSA LINHA, alterar o campo de obrigatoriedade -->
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
        
    
        <div class="modal-overlay<?= !empty($dados_empresa_editar) ? ' active' : '' ?>" id="modalEditar">
            <div class="modal modal--xxl">

                <div class="modal__header">
                    <div class="modal__header-icone">
                        <img src="../assets/img/icone_empresa.svg" alt="Empresa" />
                    </div>
                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Edição de Cadastro</h2>
                        <p class="modal__subtitulo">Informações da empresa contratante e do responsável técnico</p>
                    </div>
                    <button type="button" class="modal__fechar" data-modal-close="modalEditar">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form action="cliente_empresa.php" method="post">
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
                            </div>
                            <div class="modal-grade modal-grade--4">
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
                        <?php if (!empty($mensagem_erro)): ?>
                            <p class="campo__mensagem-erro" style="margin-right: auto;">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <?php echo $mensagem_erro; ?>
                            </p>
                        <?php endif; ?>
                        <button type="button" class="btn-cancelar" data-modal-close="modalEditar">CANCELAR</button>
                        <button type="submit" class="btn-botao-verde">SALVAR</button>
                    </div>

                </form>
            </div>
        </div>
        



       <div class="tabela-wrapper">
            <table id="tabela">
                <thead>
                    <tr>
                        <th data-col="0">
                            <span class="th-label">ID <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="1">
                            <span class="th-label">Nome da Empresa <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="2">
                            <span class="th-label">CNPJ</span>
                        </th>
                        <th data-col="3">
                            <span class="th-label">Responsável <i class="fa-solid fa-filter sort-icon"></i></span>
                        </th>
                        <th data-col="4">
                            <span class="th-label">Email <i class="fa-solid fa-filter sort-icon"></i></span>
                        </th>
                        <th data-col="5">
                            <span class="th-label">Telefone <i class="fa-solid fa-filter sort-icon"></i></span>
                        </th>
                        <th data-col="6">
                            <span class="th-label">Status</span>
                        </th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dados as $empresa): ?>
                        <tr>
                            <td>#<?= $empresa['id'] ?></td>
                            <td><?= htmlspecialchars($empresa['nome_fantasia']) ?></td>
                            <td><?= htmlspecialchars($empresa['cnpj']) ?></td>
                            <td><?= htmlspecialchars($empresa['responsavel']) ?></td>
                            <td><?= htmlspecialchars($empresa['email_contato'] ?? '---') ?></td>
                            <td><?= htmlspecialchars($empresa['telefone']) ?></td>
                            <td>
                                <div class="clientes-status-cell">
                                    <label class="switch">
                                        <input type="checkbox" <?= $empresa['habilitado'] ? 'checked' : '' ?> data-id="<?= $empresa['id'] ?>" onchange="toggleHabilitado(this)">
                                        <span class="switch-slider"></span>
                                    </label>
                                    <span class="clientes-toggle-label"><?= $empresa['habilitado'] ? 'Ativo' : 'Inativo' ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="acoes">
                                    <a href="cliente_empresa.php?id_empresa=<?= $empresa['id'] ?>" class="tabela-btn-editar" title="Editar" aria-label="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="cliente_empresa.php?excluir=<?= $empresa['id'] ?>" class="tabela-btn-excluir" title="Excluir" aria-label="Excluir" onclick="return confirm('Excluir esta empresa?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($dados)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center">Nenhuma empresa cadastrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" class="rodape-tabela">
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
<script src="../Assets/JS/componentes/barraDePesquisa.js"></script>
<script src="../assets/JS/componentes/tabela.js"></script>
<script src="../assets/JS/componentes/modal.js"></script>
<script src="../assets/JS/Buscarcep.js"></script>
<script>
    function toggleHabilitado(checkbox) {
        const label = checkbox.closest('.clientes-status-cell').querySelector('.clientes-toggle-label');
        label.textContent = checkbox.checked ? 'Ativo' : 'Inativo';
 
        const body = new FormData();
        body.append('action', 'alterarHabilitado');
        body.append('id', checkbox.dataset.id);
        body.append('habilitado', checkbox.checked ? '1' : '0');
 
        fetch('cliente_empresa.php', {
                method: 'POST',
                body
            })
            .catch(() => {
                checkbox.checked = !checkbox.checked;
                label.textContent = checkbox.checked ? 'Ativo' : 'Inativo';
            });
    }
    
    function limparIdEmpresaDaUrl() {
        if (window.location.search.includes('id_empresa')) {
            const url = new URL(window.location.href);
            url.searchParams.delete('id_empresa');
            window.history.replaceState({}, '', url);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const modalEditar = document.getElementById('modalEditar');
        if (!modalEditar) return;

        modalEditar.querySelectorAll('[data-modal-close]').forEach(botao => {
            botao.addEventListener('click', limparIdEmpresaDaUrl);
        });
        modalEditar.addEventListener('click', (e) => {
            if (e.target === modalEditar) {
                limparIdEmpresaDaUrl();
            }
        });
    });
</script>
</html>