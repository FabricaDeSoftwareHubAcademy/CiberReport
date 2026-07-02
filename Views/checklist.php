<?php
require_once "../Controller/ChecklistController.php";

$controller = new ChecklistController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'alterarHabilitado') {
    $id = (int) ($_POST['id'] ?? 0);
    $habilitado = (int) ($_POST['habilitado'] ?? 0);

    $controller->alterarStatus($id, $habilitado);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'buscarChecklist') {
    $id = (int) ($_POST['id'] ?? 0);
    $checklist = $controller->buscarComItens($id);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($checklist);
    exit;
}

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
    $controller->excluir((int) $_GET['excluir']);

    header("Location: checklist.php");
    exit;
}

$checklists = $controller->listar();
$categorias = $controller->listarCategorias();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/CSS/style.css">
    <link rel="stylesheet" href="../assets/CSS/Pages/checklist.css">

    <title>Cadastro de Checklist</title>
</head>

<body>
    <?php
    $tituloPagina = 'Checklist';
    include 'Components/menu.php';
    ?>

    <main>
        <div class="button-cadastro-checklist">
            <button type="button" class="btn-novo-cadastro" data-modal-target="modalChecklist" onclick="limparFormularioChecklist()">
                <i class="fa-solid fa-plus"></i>
                <span class="texto">Novo Checklist</span>
            </button>
        </div>

        <div class="modal-overlay" id="modalChecklist">
            <div class="modal modal--xxl">
                <div class="modal__header">
                    <div class="modal__header-icone">
                        <img src="../assets/img/Icon_Checklist.png" alt="Cadastro de Checklist">
                    </div>

                    <div class="modal__header-texto">
                        <h2 class="modal__titulo" id="tituloModalChecklist">Cadastro de Checklist</h2>
                        <p class="modal__subtitulo">Crie e organize listas padrões de verificação</p>
                    </div>

                    <button type="button" class="modal__fechar" data-modal-close>
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form method="POST" action="checklist.php" class="form-checklist" id="formChecklist">
                    <input type="hidden" name="id" id="checklist_id">

                    <div class="modal__body">
                        <div class="modal-secao">
                            <div class="modal-secao__titulo">
                                <i class="fa-solid fa-clipboard-check modal-secao__titulo-icone"></i>
                                <h3>Dados do Checklist</h3>
                            </div>

                            <div class="checklist-dados-linha">
                                <div class="campo">
                                    <label for="checklist_nome" class="campo__label campo__label--obrigatorio">
                                        Nome do Checklist
                                    </label>

                                    <input
                                        type="text"
                                        name="nome"
                                        id="checklist_nome"
                                        class="campo__input"
                                        placeholder="Ex: Checklist Web"
                                        maxlength="80"
                                        required>
                                </div>

                                <div class="campo campo-categoria">
                                    <div class="categoria-label-linha">
                                        <label for="categoria_input" class="campo__label campo__label--obrigatorio">
                                            Categoria
                                        </label>

                                        <small class="campo__ajuda" id="ajuda_categoria">
                                            Digite uma categoria nova ou selecione uma existente.
                                        </small>
                                    </div>

                                    <div class="categoria-combobox">
                                        <input
                                            type="text"
                                            name="categoria"
                                            id="categoria_input"
                                            class="campo__input categoria-input"
                                            placeholder="Digite ou selecione uma categoria"
                                            autocomplete="off"
                                            maxlength="150"
                                            required
                                            aria-autocomplete="list"
                                            aria-controls="lista_categorias"
                                            aria-expanded="false">

                                        <button
                                            type="button"
                                            id="btn_abrir_categorias"
                                            class="categoria-seta"
                                            title="Mostrar categorias"
                                            aria-label="Mostrar categorias cadastradas">
                                            <i class="fa-solid fa-chevron-down"></i>
                                        </button>

                                        <div id="lista_categorias" class="categoria-dropdown" hidden></div>
                                    </div>
                                </div>
                            </div>

                            <div class="campo checklist-descricao">
                                <label for="descricao_checklist" class="campo__label">
                                    Descrição
                                </label>

                                <textarea
                                    name="descricao"
                                    id="descricao_checklist"
                                    class="campo__textarea"
                                    rows="3"
                                    maxlength="1000"
                                    placeholder="Descreva o objetivo e a finalidade deste checklist..."
                                    oninput="contarDescricaoChecklist()"></textarea>

                                <div class="checklist-contador">
                                    <span>Máximo de 1000 caracteres</span>
                                    <span id="contador_descricao">0 / 1000</span>
                                </div>
                            </div>
                        </div>

                        <div class="modal-secao">
                            <div class="modal-secao__titulo">
                                <i class="fa-solid fa-list-check modal-secao__titulo-icone"></i>
                                <h3>Itens do Checklist</h3>
                            </div>

                            <div id="lista-itens-checklist" class="modal-grade modal-grade--4">
                                <div class="campo">
                                    <label class="campo__label campo__label--obrigatorio">
                                        Item
                                    </label>

                                    <input
                                        type="text"
                                        name="itens[]"
                                        class="campo__input"
                                        placeholder="Ex: Verificar SQL Injection"
                                        required>
                                </div>
                            </div>

                            <button type="button" class="btn-botao-verde" onclick="adicionarItemChecklist()">
                                <i class="fa-solid fa-plus"></i>
                                Adicionar Item
                            </button>
                        </div>
                    </div>

                    <div class="modal__footer">
                        <button type="button" class="btn-cancelar" data-modal-close>
                            CANCELAR
                        </button>

                        <button type="submit" class="btn-botao-verde">
                            SALVAR
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="tabela-wrapper">
            <table>
                <thead>
                    <tr>
                        <th data-col="0">
                            <span class="th-label">
                                ID
                                <i class="fa-solid fa-sort sort-icon"></i>
                            </span>
                        </th>

                        <th data-col="1">
                            <span class="th-label">
                                Nome do Checklist
                                <i class="fa-solid fa-sort sort-icon"></i>
                            </span>
                        </th>

                        <th data-col="2">
                            <span class="th-label">Breve Descrição</span>
                        </th>

                        <th data-col="3">
                            <span class="th-label">
                                Categoria
                                <i class="fa-solid fa-filter sort-icon"></i>
                            </span>
                        </th>

                        <th data-col="4">
                            <span class="th-label">Status</span>
                        </th>

                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($checklists as $checklist): ?>
                        <?php $ativo = (bool) $checklist['habilitado']; ?>

                        <tr>
                            <td><?= (int) $checklist['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($checklist['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td class="checklist-desc">
                                <?= htmlspecialchars($checklist['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <span class="checklist-cat">
                                    <?= htmlspecialchars($checklist['categoria'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>

                            <td>
                                <div class="checklist-ativo">
                                    <label class="switch">
                                        <input
                                            type="checkbox"
                                            <?= $ativo ? 'checked' : '' ?>
                                            data-id="<?= (int) $checklist['id'] ?>"
                                            onchange="toggleHabilitado(this)">

                                        <span class="switch-slider"></span>
                                    </label>
                                </div>
                            </td>

                            <td>
                                <div class="acoes">
                                    <button
                                        type="button"
                                        class="btn-editar"
                                        title="Editar"
                                        aria-label="Editar"
                                        onclick="editarChecklist('<?= (int) $checklist['id'] ?>')">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <a
                                        href="checklist.php?excluir=<?= (int) $checklist['id'] ?>"
                                        class="btn-excluir"
                                        title="Excluir"
                                        aria-label="Excluir"
                                        onclick="return confirm('Excluir este checklist?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($checklists)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center">
                                Nenhum checklist cadastrado.
                            </td>
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

    <script>
        window.categoriasChecklist = <?= json_encode($categorias, JSON_UNESCAPED_UNICODE) ?>;
    </script>

    <script src="../assets/JS/componentes/modal.js"></script>
    <script src="../assets/JS/componentes/tabela.js"></script>
    <script src="../assets/JS/checklist.js"></script>
</body>

</html>