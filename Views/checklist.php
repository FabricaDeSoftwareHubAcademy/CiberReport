<?php
require_once "../Controller/ChecklistController.php";

$controller = new ChecklistController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
    if (!empty($_POST['id'])) {
        $controller->atualizar();
    } else {
        $controller->cadastrar();
    }

    header("Location: checklist.php");
    exit;
}

if (isset($_GET['excluir'])) {
    $controller->excluir($_GET['excluir']);
    header("Location: checklist.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'alterarHabilitado') {
    $id        = (int) ($_POST['id'] ?? 0);
    $habilitado = (int) ($_POST['habilitado'] ?? 0);
    $controller->alterarStatus($id, $habilitado);
    echo json_encode(['ok' => true]);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'buscarChecklist') {
    $id = (int) ($_POST['id'] ?? 0);
    $checklist = $controller->buscarComItens($id);

    echo json_encode($checklist);
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
    include 'Components/menu.php'; ?>

    <main>
        <!-- chama novo botão de cadastro -->
        <div class="button-cadastro">
            <button type="button" class="btn-novo-cadastro" data-modal-target="modalChecklist" onclick="limparFormularioChecklist()">
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
                <form method="POST" action="checklist.php" class="form-checklist">
                    <input type="hidden" name="id" id="checklist_id">

                    <div class="form-grupo">
                        <label>Nome do Checklist</label>
                        <input type="text" name="nome" placeholder="Ex: Checklist Pentest Web" required>
                    </div>

                    <div class="form-grupo">
                        <label>Descrição</label>
                        <input type="text" name="descricao" placeholder="Ex: Checklist básico para testes web">
                    </div>

                    <div class="form-grupo">
                        <label>Categoria</label>
                        <input type="text" name="categoria" placeholder="Ex: Web, Rede, API" required>
                    </div>

                    <hr>

                    <h3>Itens do Checklist</h3>

                    <div id="lista-itens-checklist">
                        <div class="item-checklist">
                            <input type="text" name="itens[]" placeholder="Ex: Verificar SQL Injection" required>
                        </div>
                    </div>

                    <button type="button" onclick="adicionarItemChecklist()">
                        + Adicionar Item
                    </button>

                    <div class="modal__footer">
                        <button type="submit" class="btn-salvar">
                            Salvar Checklist
                        </button>
                    </div>

                </form>

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
                        $ativo = (bool) $pentest['habilitado'];
                        ?>
                        <tr>
                            <td><?= $pentest['id'] ?></td>
                            <td><?= htmlspecialchars($pentest['nome']) ?></td>
                            <td class="ger-pentest-col-descricao">
                                <?= htmlspecialchars($pentest['descricao'] ?? '') ?>
                            </td>

                            <td>
                                <span class="ger-pentest-cat-badge">
                                    <?= htmlspecialchars($pentest['categoria'] ?? '') ?>
                                </span>
                            </td>
                            <td>
                                <div class="ger-pentest-status-cell">
                                    <label class="switch">
                                        <input type="checkbox" <?= $ativo ? 'checked' : '' ?> data-id="<?= $pentest['id'] ?>" onchange="toggleHabilitado(this)">
                                        <span class="switch-slider"></span>
                                    </label>
                                    <span class="ger-pentest-toggle-label"></span>
                                </div>
                            </td>
                            <td>
                                <div class="acoes">
<button 
    type="button"
    class="btn-editar" 
    title="Editar" 
    aria-label="Editar"
    onclick="editarChecklist('<?= $pentest['id'] ?>')"
>
    <i class="fa-solid fa-pen-to-square"></i>
</button>
                                    <a href="checklist.php?excluir=<?= $pentest['id'] ?>" class="btn-excluir" title="Excluir" aria-label="Excluir" onclick="return confirm('Excluir este pentest?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($pentests)): ?>

                        <tr>
                            <td colspan="6" style="text-align:center">Nenhum tipo de pentest cadastrado.</td>
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
    </div>
    </div>
</body>

<script src="../assets/JS/componentes/modal.js"></script>
<script src="../assets/js/componentes/tabela.js"></script>
<script src="../assets/JS/checklist.js"></script>

</html>