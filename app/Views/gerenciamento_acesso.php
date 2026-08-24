<?php
use Controller\GerenciarAcessoController;

require_once __DIR__ . "/../Controller/GerenciarAcessoController.php";

$controller = new GerenciarAcessoController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
    $controller->cadastrar();
    header("Location: " . BASE_URL . "gerenciamento-acesso");
    exit;
}

if (isset($_GET['excluir'])) {
    $controller->excluir($_GET['excluir']);
    header("Location: " . BASE_URL . "gerenciamento-acesso");
    exit;
}

$perfil   = $controller->listar();
$clientes = method_exists($controller, 'listarClientes') ? $controller->listarClientes() : [];
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gerenciar Perfis de Acesso</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Componentes/button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Componentes/gerenciamento-acesso.css">
</head>

<body>
    <?php
    $tituloPagina = 'Gerenciamento perfil';
    include_once 'Components/menu.php';
    ?>

    <main>
        <div class="ger-acesso-topo">
            <button class="btn-novo-cadastro" data-modal-target="modalNovoPerfil">
                <i class="fa-solid fa-plus"></i>Novo perfil
            </button>
        </div>

        <div class="tabela-wrapper">
            <table>
                <thead>
                    <tr>
                        <th data-col="0">
                            <span class="th-label">ID <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="1">
                            <span class="th-label">Nome do Perfil <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($perfil as $perfis): ?>
                        <tr>
                            <td><?= $perfis['id'] ?></td>
                            <td><?= htmlspecialchars($perfis['nome']) ?></td>
                            <td>
                                <div class="acoes">
                                    <button
                                        class="tabela-btn-editar"
                                        title="Editar"
                                        aria-label="Editar"
                                        data-id="<?= $perfis['id'] ?>"
                                        data-nome="<?= htmlspecialchars($perfis['nome']) ?>"
                                        onclick="abrirEdicaoPerfil(this)">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a
                                        href="<?= BASE_URL ?>gerenciamento-acesso?excluir=<?= $perfis['id'] ?>"
                                        class="tabela-btn-excluir"
                                        title="Excluir"
                                        aria-label="Excluir"
                                        onclick="return confirm('Excluir este perfil?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($perfil)): ?>
                        <tr>
                            <td colspan="3" style="text-align:center">Nenhum tipo de perfil cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="rodape-tabela">
                            <div class="paginacao"></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Modal de cadastro / edição de perfil -->
        <div class="modal-overlay" id="modalNovoPerfil">
            <div class="modal modal--xxl">
                <div class="modal__header">
                    <div class="modal__header-icone"><i class="fa-solid fa-shield"></i></div>
                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Cadastro de Perfil</h2>
                        <p class="modal__subtitulo">Informações do perfil de acesso</p>
                    </div>
                    <button type="button" class="modal__fechar" data-modal-close>&times;</button>
                </div>

                <form class="modal__body" method="post" action="<?= BASE_URL ?>gerenciamento-acesso" id="formPerfil">
                    <input type="hidden" name="id" id="perfil-id" value="">

                    <div class="form-step form-step-active">
                        <div class="modal-grade">
                            <div class="campo">
                                <label class="campo__label" for="nome">Nome do perfil</label>
                                <input type="text" name="nome" id="nome" class="campo__input" required maxlength="100">
                            </div>

                            <?php if (!empty($clientes)): ?>
                                <div class="campo">
                                    <label class="campo__label" for="cliente">Cliente</label>
                                    <select name="cliente" id="cliente" class="campo__select">
                                        <option value="">Selecione um cliente</option>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <option value="<?= $cliente['id'] ?>">
                                                <?= htmlspecialchars($cliente['nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="perfis-grupo">
                            <span class="perfis-grupo-titulo">Relatórios</span>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Visualizar Relatórios</span>
                                    <span class="permissao-descricao">Ver relatórios de Segurança</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="vis_relatorios" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Editar Relatórios</span>
                                    <span class="permissao-descricao">Modificar relatórios existentes</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="edit_relatorios" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Criar Relatório</span>
                                    <span class="permissao-descricao">Criar novos Relatórios</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="criar_relatorios" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Excluir Relatórios</span>
                                    <span class="permissao-descricao">Remover Relatórios</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="excluir_relatorios">
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="perfis-grupo">
                            <span class="perfis-grupo-titulo">Vulnerabilidade</span>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Visualizar Vulnerabilidades</span>
                                    <span class="permissao-descricao">Acessar Lista de Vulnerabilidade</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="vis_vulnerabilidades" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Editar Vulnerabilidades</span>
                                    <span class="permissao-descricao">Editar Vulnerabilidades existentes</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="edit_vulnerabilidades" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Criar Vulnerabilidade</span>
                                    <span class="permissao-descricao">Criar novas vulnerabilidades</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="criar_vulnerabilidades" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Excluir Vulnerabilidade</span>
                                    <span class="permissao-descricao">Remover vulnerabilidades</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="excluir_vulnerabilidades">
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="perfis-grupo">
                            <span class="perfis-grupo-titulo">Logs</span>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Visualizar Logs</span>
                                    <span class="permissao-descricao">Acessar Logs do Sistema</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="vis_logs" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Editor Logs</span>
                                    <span class="permissao-descricao">Editar Logs do Sistema</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="edit_logs" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Exportar Logs</span>
                                    <span class="permissao-descricao">Exportar Logs do Sistema</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="exportar_logs" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Excluir Logs</span>
                                    <span class="permissao-descricao">Limpar Logs do Sistema</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="excluir_logs">
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="perfis-grupo">
                            <span class="perfis-grupo-titulo">Clientes</span>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Visualizar Clientes</span>
                                    <span class="permissao-descricao">Visualizar Dados dos Clientes</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="vis_clientes" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Editar Clientes</span>
                                    <span class="permissao-descricao">Editar Informações do Cliente</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="edit_clientes" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Criar Clientes</span>
                                    <span class="permissao-descricao">Criar novos clientes</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="criar_clientes" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Excluir Clientes</span>
                                    <span class="permissao-descricao">Remover clientes</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="excluir_clientes" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="perfis-grupo">
                            <span class="perfis-grupo-titulo">Usuários</span>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Visualizar Usuários</span>
                                    <span class="permissao-descricao">Visualizar Usuários Cadastrados</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="vis_usuarios" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Editar Clientes</span>
                                    <span class="permissao-descricao">Editar Usuários Cadastrados</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="edit_usuarios" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Criar Clientes</span>
                                    <span class="permissao-descricao">Criar Usuários Cadastrados</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="criar_usuarios" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Excluir Clientes</span>
                                    <span class="permissao-descricao">Remover Usuários Cadastrados</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="excluir_usuarios" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="perfis-grupo">
                            <span class="perfis-grupo-titulo">Administração</span>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Gerenciar Equipe</span>
                                    <span class="permissao-descricao">Adicionar/Editar/Remover Membros</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="ger_equipe" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <div class="permissao-item">
                                <div class="permissao-info">
                                    <span class="permissao-nome">Gerenciar Projetos</span>
                                    <span class="permissao-descricao">Adicionar/Editar/Excluir Projetos</span>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="ger_projetos" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="perfis-rodape">
                        <button type="submit" class="btn-botao-verde">Criar</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="<?= BASE_URL ?>app/assets/JS/componentes/tabela.js"></script>
    <script src="<?= BASE_URL ?>app/assets/JS/componentes/modal.js"></script>
</body>

</html>