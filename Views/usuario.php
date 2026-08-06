<?php
require_once "../Controller/gerenciamento_usuario.php";

$controller = new GerenciamentoUsuarioController();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cadastrar') {
    $controller->cadastrar();
    header("Location: gerenciamento_usuario.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'uploadFoto') {
    $id  = (int) ($_POST['id'] ?? 0);
    $foto = $_FILES['foto'] ?? null;
    $controller->uploadFoto($id, $foto);
    header("Location: gerenciamento_usuario.php");
    exit;
}

// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editar') {
//     $controller->atualizarDadosEmpresa();
//     header("Location: gerenciamento_usuario.php");
//     exit;
// }


if (isset($_GET['excluir'])) {
    $controller->excluir($_GET['excluir']);
    header("Location: gerenciamento_usuario.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'alterarHabilitado') {
    $id         = (int) ($_POST['id'] ?? 0);
    $habilitado = (int) ($_POST['habilitado'] ?? 0);
    $controller->alterarStatus($id, $habilitado);
    echo json_encode(['ok' => true]);
    exit;
}

$usuarios = $controller->listar();

?>




<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/CSS/style.css">
    <link rel="stylesheet" href="../assets/CSS/Pages/gerenciar_usuario.css">
    <title>Gerenciamento de Usuário</title>
</head>

<body>
    <?php $tituloPagina = 'Gerenciamento de Usuário';
    include_once 'Components/menu.php'; ?>
    <main>
        <div class="ger-pentest-topo">
            <button class="btn-novo-cadastro" data-modal-target="modalNovoUsuario">
                <i class="fa-solid fa-plus"></i>Novo Usuario
            </button>
        </div>

        <div class="tabela-wrapper">
            <table>
                <thead>
                    <tr>
                        <th data-col="0">
                            <span class="th-label">Nome <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="1">
                            <span class="th-label">Cargo <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="2">
                            <span class="th-label">Email <i class="fa-solid fa-filter sort-icon"></i></span>
                        </th>
                        <th data-col="3">
                            <span class="th-label">Telefone <i class="fa-solid fa-filter sort-icon"></i></span>
                        </th>
                        <th data-col="4">
                            <span class="th-label">Status</span>
                        </th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <?php $ativo = (bool) $usuario['habilitado']; ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['nome']) ?></td>
                            <td>
                                <span class="ger-pentest-cat-badge">
                                    <?= htmlspecialchars($usuario['cargo'] ?? '') ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td><?= htmlspecialchars($usuario['telefone'] ?? '') ?></td>
                            <td class="col-status">
                                <div class="ger-pentest-status-cell">
                                    <label class="switch">
                                        <input type="checkbox" <?= $ativo ? 'checked' : '' ?> data-id="<?= $usuario['id'] ?>" onchange="toggleHabilitado(this)">
                                        <span class="switch-slider"></span>
                                    </label>
                                    <span class="ger-pentest-toggle-label"><?= $ativo ? 'Ativo' : 'Inativo' ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="acoes">
                                    <button class="tabela-btn-visualizar" title="Visualizar" aria-label="Visualizar"
                                        data-modal-target="modalVisualizarUsuario"
                                        data-usuario='<?= htmlspecialchars(json_encode($usuario), ENT_QUOTES) ?>'>
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button class="tabela-btn-editar" title="Editar" aria-label="Editar"
                                        data-modal-target="modalEdicaoUsuario"
                                        data-usuario='<?= htmlspecialchars(json_encode($usuario), ENT_QUOTES) ?>'>
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="gerenciamento_usuario.php?excluir=<?= $usuario['id'] ?>" class="tabela-btn-excluir" title="Excluir" aria-label="Excluir" onclick="return confirm('Excluir este usuário?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center">Nenhum usuário cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="rodape-tabela">
                            <div class="paginacao"></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </main>
    <!-- Fecha .main-content e .menu abertos pelo menu.php. Necessário para que o <main>
         fique dentro do flex row da sidebar, permitindo o comportamento de "empurrar"
         o conteúdo quando o menu lateral abre/fecha. -->
    </div>
    </div>

    <div class="modal-overlay" id="modalNovoUsuario">
        <div class="modal modal--md">
            <form method="post" action="gerenciamento_usuario.php" autocomplete="off">
                <input type="hidden" name="action" value="cadastrar">

                <div class="modal__header">
                    <div class="modal__header-icone">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Cadastro Usuario</h2>
                        <p class="modal__subtitulo">Cadastro de informações do Usuário</p>
                    </div>
                    <button type="button" class="modal__fechar" data-modal-close>&times;</button>
                </div>
                <div class="foto-usuario-wrapper">
                    <label class="foto-usuario-label" for="fotoCadastro">
                        <div class="foto-usuario-preview" id="previewFotoCadastro">
                            <i class="fa-solid fa-user foto-usuario-icone-padrao"></i>
                            <div class="foto-usuario-overlay">
                                <i class="fa-solid fa-camera"></i>
                            </div>
                        </div>
                    </label>
                    <input type="file" name="foto" id="fotoCadastro" class="foto-usuario-input" accept="image/*">
                    <span class="foto-usuario-alterar">Alterar foto</span>
                </div>
                <div class="modal__body">
                    <div class="modal-grade__col-full">
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio">Nome do Usuario</label>
                            <input type="text" name="nome" class="campo__input" placeholder="Digite seu nome" required>
                        </div>
                    </div>
                    <div class="modal-grade">
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio">E-mail</label>
                            <input type="email" name="email" class="campo__input" placeholder="Digite seu email" required>
                        </div>
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio">Telefone</label>
                            <input type="tel" name="telefone" class="campo__input" required>
                        </div>
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio">CPF</label>
                            <input type="text" name="cpf" class="campo__input" placeholder="xxx.xxx.xxx-xx" required>
                        </div>
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio">Senha</label>
                            <input type="password" name="senha" class="campo__input" placeholder="Digite a senha" required minlength="6">
                        </div>
                        <div class="campo">
                            <label class="campo__label">Cargo</label>
                            <div class="campo__multi-busca">
                                <div class="campo__select-wrapper" style="flex: 1">
                                    <select class="campo__select" name="perfil_id">
                                        <option value="analista">Analista</option>
                                        <option value="administrador">Administrador</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down campo__select-seta"></i>
                                </div>
                                <button type="button" class="campo__botao-adicionar">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="campo">
                            <label class="campo__label">Especialidade</label>
                            <div class="campo__multi-busca">
                                <div class="campo__select-wrapper" style="flex: 1">
                                    <select class="campo__select" name="especialidade">
                                        <option value="mobile">Mobile</option>
                                        <option value="web">Web</option>
                                        <option value="front">Front</option>
                                        <option value="back">Back</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down campo__select-seta"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <footer class="modal__footer">
                        <button type="button" class="btn-cancelar" data-modal-close data-botao-passo="cancelar">Cancelar</button>
                        <button type="submit" class="btn-botao-verde" data-botao-passo="salvar">Salvar</button>
                    </footer>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modalVisualizarUsuario">
        <div class="modal modal--md">
            <div class="modal__header">
                <div class="modal__header-icone">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="modal__header-texto">
                    <h2 class="modal__titulo">Visualizar Usuário</h2>
                    <p class="modal__subtitulo">Visualizar as informações do Usuário</p>
                </div>
                <button type="button" class="modal__fechar" data-modal-close>&times;</button>
            </div>
            <div class="foto-usuario-wrapper foto-usuario-wrapper--readonly">
                <div class="foto-usuario-preview" id="previewFotoVisualizar">
                    <img data-campo="foto" class="foto-usuario-imagem" style="display:none;">
                    <i class="fa-solid fa-user foto-usuario-icone-padrao"></i>
                </div>
            </div>
            <div class="modal__body">
                <div class="modal-grade__col-full">
                    <div class="campo">
                        <label class="campo__label">Nome do Usuario</label>
                        <input type="text" class="campo__input" data-campo="nome" readonly>
                    </div>
                </div>
                <div class="modal-grade">
                    <div class="campo">
                        <label class="campo__label">E-mail</label>
                        <input type="email" class="campo__input" data-campo="email" readonly>
                    </div>
                    <div class="campo">
                        <label class="campo__label">Telefone</label>
                        <input type="tel" class="campo__input" data-campo="telefone" readonly>
                    </div>
                    <div class="campo">
                        <label class="campo__label">CPF</label>
                        <input type="text" class="campo__input" data-campo="cpf" readonly>
                    </div>
                    <div class="campo">
                        <label class="campo__label">Cargo</label>
                        <div class="campo__multi-busca">
                            <div class="campo__select-wrapper" style="flex: 1">
                                <select class="campo__select" data-campo="perfil_id" disabled>
                                    <option value="analista">Analista</option>
                                    <option value="administrador">Administrador</option>
                                </select>
                                <i class="fa-solid fa-chevron-down campo__select-seta"></i>
                            </div>
                        </div>
                    </div>
                    <div class="campo">
                        <label class="campo__label">Especialidade</label>
                        <div class="campo__multi-busca">
                            <div class="campo__select-wrapper" style="flex: 1">
                                <select class="campo__select" data-campo="especialidade" disabled>
                                    <option value="mobile">Mobile</option>
                                    <option value="web">Web</option>
                                    <option value="front">Front</option>
                                    <option value="back">Back</option>
                                </select>
                                <i class="fa-solid fa-chevron-down campo__select-seta"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="modal__footer">
                    <button type="button" class="btn-cancelar" data-modal-close data-botao-passo="cancelar">Fechar</button>
                </footer>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modalEdicaoUsuario">
        <div class="modal modal--md">
            <form method="post" action="gerenciamento_usuario.php" autocomplete="off">
                <input type="hidden" name="action" value="editar">
                <input type="hidden" name="id" data-campo="id">

                <div class="modal__header">
                    <div class="modal__header-icone">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Edição de Usuario</h2>
                        <p class="modal__subtitulo">Editar informações do Usuário</p>
                    </div>
                    <button type="button" class="modal__fechar" data-modal-close>&times;</button>
                </div>
                <div class="foto-usuario-wrapper">
                    <label class="foto-usuario-label" for="fotoEdicao">
                        <div class="foto-usuario-preview" id="previewFotoEdicao">
                            <img data-campo="foto" class="foto-usuario-imagem" style="display:none;">
                            <i class="fa-solid fa-user foto-usuario-icone-padrao"></i>
                            <div class="foto-usuario-overlay">
                                <i class="fa-solid fa-camera"></i>
                            </div>
                        </div>
                    </label>
                    <input type="file" name="foto" id="fotoEdicao" class="foto-usuario-input" accept="image/*">
                    <span class="foto-usuario-alterar">Alterar foto</span>
                </div>
                <div class="modal__body">
                    <div class="modal-grade__col-full">
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio">Nome do Usuario</label>
                            <input type="text" name="nome" class="campo__input" placeholder="Digite seu nome" data-campo="nome" required>
                        </div>
                    </div>
                    <div class="modal-grade">
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio">E-mail</label>
                            <input type="email" name="email" class="campo__input" placeholder="Digite seu email" data-campo="email" required>
                        </div>
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio">Telefone</label>
                            <input type="tel" name="telefone" class="campo__input" data-campo="telefone" required>
                        </div>
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio">CPF</label>
                            <input type="text" name="cpf" class="campo__input" placeholder="xxx.xxx.xxx-xx" data-campo="cpf" required>
                        </div>
                        <div class="campo">
                            <label class="campo__label">Cargo</label>
                            <div class="campo__multi-busca">
                                <div class="campo__select-wrapper" style="flex: 1">
                                    <select class="campo__select" name="perfil_id" data-campo="perfil_id">
                                        <option value="analista">Analista</option>
                                        <option value="administrador">Administrador</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down campo__select-seta"></i>
                                </div>
                                <button type="button" class="campo__botao-adicionar">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="campo">
                            <label class="campo__label">Especialidade</label>
                            <div class="campo__multi-busca">
                                <div class="campo__select-wrapper" style="flex: 1">
                                    <select class="campo__select" name="especialidade" data-campo="especialidade">
                                        <option value="mobile">Mobile</option>
                                        <option value="web">Web</option>
                                        <option value="front">Front</option>
                                        <option value="back">Back</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down campo__select-seta"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <footer class="modal__footer">
                        <button type="button" class="btn-cancelar" data-modal-close data-botao-passo="cancelar">Cancelar</button>
                        <button type="submit" class="btn-botao-verde" data-botao-passo="salvar">Salvar</button>
                    </footer>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/JS/componentes/tabela.js"></script>
    <script src="../assets/JS/componentes/modal.js"></script>
</body>

</html>