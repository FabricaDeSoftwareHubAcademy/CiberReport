<?php
require_once "../Controller/ChecklistController.php";

$controller = new ChecklistController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
    $controller->cadastrar();
    header("Location: gerenciarPentest.php");
    exit;
}

if (isset($_GET['excluir'])) {
    $controller->excluir($_GET['excluir']);
    header("Location: gerenciarPentest.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'alterarHabilitado') {
    $id        = (int) ($_POST['id'] ?? 0);
    $habilitado = (int) ($_POST['habilitado'] ?? 0);
    $controller->alterarStatus($id, $habilitado);
    echo json_encode(['ok' => true]);
    exit;
}

$pentests = $controller->listar();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/CSS/style.css" />
    <title>CADASTRO DE CHECKLIST</title>
</head>

<body>
    <!-- chama o menu rafael -->
    <?php $tituloPagina = 'Checklist';
    include 'menu.php'; ?>

    <main>
        <!-- chama novo botão de cadastro -->
        <div class="button-cadastro">
            <button class="btn-novo-cadastro" data-modal-target="modalChecklist">
                <i class="fa-solid fa-plus"></i><span class="texto">Novo Cadastro</span>
            </button>
        </div>
        <!-- final botão -->
        <!-- chama o modal matheus-->
        <div class="modal-overlay" id="modalChecklist"
            <?php if (!empty($mensagem_erro)) echo 'style="visibility:visible;opacity:1;"'; ?>>
            <div class="modal modal--xl">

                <div class="modal__header">
                    <div class="modal__header-icone">
                        <img src="../assets/img/Icon_Checklist.png" alt="Cadastro CheckList" />
                    </div>
                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Cadastro de Checklist</h2>
                        <p class="modal__subtitulo">Criar tabelas padrões para Checklist</p>
                    </div>
                    <button type="button" class="modal__fechar" data-modal-close="modalClientes">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

            </div>
        </div>
        <!-- chama a tabela fabio -->
        <div class="tabela-wrapper">
            <!-- Criar nome da coluna da tabela -->
            <table>
                <thead>
                    <tr>
                        <th data-col="0">
                            <span class="th-label">ID <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="1">
                            <span class="th-label">Nome do Checklist <i class="fa-solid fa-sort sort-icon"></i></span>
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
                            <span class="th-label">Status</span>
                        </th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- conectado ao banco de dados -->
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
                            <td>
                                <div class="ger-pentest-status-cell">
                                    <label class="switch">
                                        <input type="checkbox" <?= $ativo ? 'checked' : '' ?> data-id="<?= $pentest['id'] ?>" onchange="toggleHabilitado(this)">
                                        <span class="switch-slider"></span>
                                    </label>
                                    <span class="ger-pentest-toggle-label"><?= $ativo ? 'Ativo' : 'Inativo' ?></span>
                                </div>
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

    </main>
    </div>
    </div>
</body>

<script src="../assets/JS/componentes/modal.js"></script>
<script src="../assets/js/componentes/tabela.js"></script>

</html>