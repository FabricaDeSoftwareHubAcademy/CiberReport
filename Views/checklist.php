<?php

require_once "../Controller/ChecklistController.php";

$controller = new ChecklistController();
$erroFormulario = '';

function responderJson(array $dados): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'cadastrarItemCatalogo') {
        $item = $controller->cadastrarItemCatalogo();

        responderJson([
            'ok' => (bool) $item,
            'item' => $item,
            'mensagem' => $item
                ? 'Item cadastrado com sucesso.'
                : 'Informe um título válido para o item.'
        ]);
    }

    if ($action === 'buscarItemCatalogo') {
        $item = $controller->buscarItemCatalogo();

        responderJson([
            'ok' => (bool) $item,
            'item' => $item,
            'mensagem' => $item
                ? ''
                : 'Item não encontrado.'
        ]);
    }

    if ($action === 'atualizarItemCatalogo') {
        $item = $controller->atualizarItemCatalogo();

        responderJson([
            'ok' => (bool) $item,
            'item' => $item,
            'mensagem' => $item
                ? 'Item atualizado com sucesso.'
                : 'Não foi possível atualizar. Verifique se o título já existe.'
        ]);
    }

    if ($action === 'removerItemCatalogo') {
        $resultado = $controller->removerItemCatalogo();

        responderJson([
            'ok' => (bool) $resultado,
            'resultado' => $resultado,
            'mensagem' => $resultado['mensagem']
                ?? 'Não foi possível remover o item.'
        ]);
    }

    if ($action === 'alterarHabilitado') {
        $id = (int) ($_POST['id'] ?? 0);
        $habilitado = (int) ($_POST['habilitado'] ?? 0);

        responderJson([
            'ok' => $controller->alterarStatus($id, $habilitado)
        ]);
    }

    if ($action === 'buscarChecklist') {
        $id = (int) ($_POST['id'] ?? 0);
        $checklist = $controller->buscarComItens($id);

        responderJson([
            'ok' => (bool) $checklist,
            'checklist' => $checklist
        ]);
    }

    if (isset($_POST['nome'])) {
        $resultado = !empty($_POST['id'])
            ? $controller->atualizar()
            : $controller->cadastrar();

        if ($resultado) {
            header('Location: checklist.php');
            exit;
        }

        $erroFormulario = 'Preencha os campos obrigatórios e selecione pelo menos um item.';
    }
}

if (isset($_GET['excluir'])) {
    $controller->excluir((int) $_GET['excluir']);

    header('Location: checklist.php');
    exit;
}

$checklists = $controller->listar();
$categorias = $controller->listarCategorias();
$itensCatalogo = $controller->listarItensCatalogo();

$jsonSeguro = JSON_UNESCAPED_UNICODE
    | JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT;
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
            <button
                type="button"
                class="btn-novo-cadastro"
                data-modal-target="modalChecklist"
                onclick="limparFormularioChecklist()">
                <i class="fa-solid fa-plus"></i>
                <span class="texto">Novo Checklist</span>
            </button>
        </div>

        <div class="modal-overlay" id="modalChecklist">
            <div class="modal modal--xxl">
                <div class="modal__header">
                    <div class="modal__header-icone">
                        <img
                            src="../assets/img/Icon_Checklist.png"
                            alt="Cadastro de Checklist">
                    </div>

                    <div class="modal__header-texto">
                        <h2
                            class="modal__titulo"
                            id="tituloModalChecklist">
                            Cadastro de Checklist
                        </h2>

                        <p class="modal__subtitulo">
                            Crie e organize listas padrões de verificação
                        </p>
                    </div>

                    <button
                        type="button"
                        class="modal__fechar"
                        data-modal-close
                        aria-label="Fechar">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form
                    method="POST"
                    action="checklist.php"
                    class="form-checklist"
                    id="formChecklist">
                    <input type="hidden" name="id" id="checklist_id">

                    <div class="modal__body">
                        <section class="modal-secao">
                            <div class="modal-secao__titulo">
                                <i class="fa-solid fa-clipboard-check modal-secao__titulo-icone"></i>
                                <h3>Dados do Checklist</h3>
                            </div>

                            <div class="checklist-dados-linha">
                                <div class="campo">
                                    <label
                                        for="checklist_nome"
                                        class="campo__label campo__label--obrigatorio">
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

                                <div class="campo">
                                    <div class="categoria-label-linha">
                                        <label
                                            for="categoria_input"
                                            class="campo__label campo__label--obrigatorio">
                                            Categoria
                                        </label>

                                        <small class="campo__ajuda">
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

                                        <div
                                            id="lista_categorias"
                                            class="categoria-dropdown"
                                            hidden></div>
                                    </div>
                                </div>
                            </div>

                            <div class="campo checklist-descricao">
                                <label
                                    for="descricao_checklist"
                                    class="campo__label">
                                    Descrição
                                </label>

                                <textarea
                                    name="descricao"
                                    id="descricao_checklist"
                                    class="campo__textarea"
                                    maxlength="1000"
                                    placeholder="Descreva o objetivo e a finalidade deste checklist..."
                                    oninput="contarDescricaoChecklist()"></textarea>

                                <div class="checklist-contador">
                                    <span>Máximo de 1000 caracteres</span>
                                    <span id="contador_descricao">0 / 1000</span>
                                </div>
                            </div>
                        </section>

                        <section class="modal-secao">
                            <div class="checklist-itens-cabecalho">
                                <div class="modal-secao__titulo">
                                    <i class="fa-solid fa-list-check modal-secao__titulo-icone"></i>
                                    <h3>Itens do Checklist</h3>
                                </div>

                                <button
                                    type="button"
                                    class="btn-botao-verde checklist-btn-gerenciar"
                                    onclick="abrirModalGerenciarItens()">
                                    <i class="fa-solid fa-list-check"></i>
                                    Gerenciar itens
                                </button>
                            </div>

                            <div
                                id="lista-itens-checklist"
                                class="lista-itens-selecionados"></div>
                        </section>
                    </div>

                    <div class="modal__footer">
                        <button
                            type="button"
                            class="btn-cancelar"
                            data-modal-close>
                            CANCELAR
                        </button>

                        <button type="submit" class="btn-botao-verde">
                            SALVAR
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal-overlay" id="modalVisualizarChecklist">
            <div class="modal modal--lg">
                <div class="modal__header">
                    <div class="modal__header-icone">
                        <i class="fa-solid fa-eye"></i>
                    </div>

                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Visualizar Checklist</h2>
                        <p class="modal__subtitulo">Informações e itens vinculados</p>
                    </div>

                    <button
                        type="button"
                        class="modal__fechar"
                        onclick="fecharVisualizacaoChecklist()"
                        aria-label="Fechar">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="modal__body visualizar-checklist-body">
                    <input type="hidden" id="visualizar_checklist_id">

                    <section class="visualizacao-secao">
                        <div class="visualizacao-secao-titulo">
                            <i class="fa-solid fa-clipboard-check"></i>
                            <h3>Dados do Checklist</h3>
                        </div>

                        <div class="visualizacao-checklist-dados">
                            <div class="visualizacao-campo">
                                <span class="visualizacao-campo-label">Nome</span>
                                <strong id="visualizar_checklist_nome"></strong>
                            </div>

                            <div class="visualizacao-campo">
                                <span class="visualizacao-campo-label">Categoria</span>
                                <strong id="visualizar_checklist_categoria"></strong>
                            </div>

                            <div class="visualizacao-campo visualizacao-campo--completo">
                                <span class="visualizacao-campo-label">Descrição</span>
                                <p id="visualizar_checklist_descricao"></p>
                            </div>

                            <div class="visualizacao-campo">
                                <span class="visualizacao-campo-label">Status</span>
                                <span
                                    id="visualizar_checklist_status"
                                    class="visualizacao-status"></span>
                            </div>
                        </div>
                    </section>

                    <section class="visualizacao-secao">
                        <div class="visualizacao-secao-cabecalho">
                            <div class="visualizacao-secao-titulo">
                                <i class="fa-solid fa-list-check"></i>
                                <h3>Itens do Checklist</h3>
                            </div>

                            <span
                                id="visualizar_total_itens"
                                class="visualizacao-total-itens">
                                0 itens
                            </span>
                        </div>

                        <div
                            id="visualizar_checklist_itens"
                            class="visualizacao-lista-itens"></div>
                    </section>
                </div>

                <div class="modal__footer">
                    <button
                        type="button"
                        class="btn-cancelar"
                        onclick="fecharVisualizacaoChecklist()">
                        FECHAR
                    </button>

                    <button
                        type="button"
                        class="btn-botao-verde"
                        onclick="editarChecklistDaVisualizacao()">
                        <i class="fa-solid fa-pen-to-square"></i>
                        EDITAR CHECKLIST
                    </button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="modalGerenciarItens">
            <div class="modal modal--lg">
                <div class="modal__header">
                    <div class="modal__header-icone">
                        <i class="fa-solid fa-list-check"></i>
                    </div>

                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Gerenciar Itens</h2>

                        <p class="modal__subtitulo">
                            Selecione os itens que farão parte deste checklist
                        </p>
                    </div>

                    <button
                        type="button"
                        class="modal__fechar"
                        onclick="fecharModalGerenciarItens()"
                        aria-label="Fechar">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="modal__body">
                    <div class="gerenciar-itens-topo">
                        <div class="campo gerenciar-itens-pesquisa">
                            <label
                                for="pesquisa_itens_catalogo"
                                class="campo__label">
                                Pesquisar itens
                            </label>

                            <div class="gerenciar-itens-campo-pesquisa">
                                <i class="fa-solid fa-magnifying-glass"></i>

                                <input
                                    type="text"
                                    id="pesquisa_itens_catalogo"
                                    class="campo__input"
                                    placeholder="Pesquise pelo título ou referência"
                                    autocomplete="off">
                            </div>
                        </div>

                        <button
                            type="button"
                            class="btn-botao-verde gerenciar-itens-novo"
                            onclick="abrirModalNovoItem()">
                            <i class="fa-solid fa-plus"></i>
                            Cadastrar novo item
                        </button>
                    </div>

                    <div class="gerenciar-itens-resumo">
                        <span>Itens disponíveis</span>

                        <strong id="contador_itens_selecionados">
                            0 selecionados
                        </strong>
                    </div>

                    <div
                        id="lista_itens_gerenciamento"
                        class="gerenciar-itens-lista"></div>
                </div>

                <div class="modal__footer">
                    <button
                        type="button"
                        class="btn-cancelar"
                        onclick="fecharModalGerenciarItens()">
                        CANCELAR
                    </button>

                    <button
                        type="button"
                        class="btn-botao-verde"
                        onclick="aplicarItensGerenciados()">
                        APLICAR ITENS
                    </button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="modalNovoItem">
            <div class="modal modal--lg">
                <div class="modal__header">
                    <div class="modal__header-icone">
                        <i class="fa-solid fa-list-check"></i>
                    </div>

                    <div class="modal__header-texto">
                        <h2
                            class="modal__titulo"
                            id="tituloModalItem">
                            Cadastro de Item
                        </h2>

                        <p
                            class="modal__subtitulo"
                            id="subtituloModalItem">
                            Cadastre um novo item reutilizável
                        </p>
                    </div>

                    <button
                        type="button"
                        class="modal__fechar"
                        onclick="fecharModalNovoItem()"
                        aria-label="Fechar">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="modal__body">
                    <input type="hidden" id="item_catalogo_id">

                    <div class="campo">
                        <label
                            for="novo_item_titulo"
                            class="campo__label campo__label--obrigatorio">
                            Título
                        </label>

                        <input
                            type="text"
                            id="novo_item_titulo"
                            class="campo__input"
                            placeholder="Ex: Verificar SQL Injection"
                            maxlength="150">
                    </div>

                    <div class="campo">
                        <label
                            for="novo_item_referencia"
                            class="campo__label">
                            Referência
                        </label>

                        <input
                            type="text"
                            id="novo_item_referencia"
                            class="campo__input"
                            placeholder="Ex: OWASP A03:2021"
                            maxlength="255">
                    </div>

                    <div class="campo">
                        <label
                            for="novo_item_obrigatorio"
                            class="campo__label">
                            Obrigatório
                        </label>

                        <select
                            id="novo_item_obrigatorio"
                            class="campo__select">
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                </div>

                <div class="modal__footer">
                    <button
                        type="button"
                        class="btn-cancelar"
                        onclick="fecharModalNovoItem()">
                        CANCELAR
                    </button>

                    <button
                        type="button"
                        class="btn-botao-verde"
                        id="btnSalvarItemCatalogo"
                        onclick="salvarItemCatalogo()">
                        SALVAR
                    </button>
                </div>
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
                            <span class="th-label">
                                Breve Descrição
                            </span>
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
                                <?= htmlspecialchars(
                                    $checklist['nome'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td class="checklist-desc">
                                <?= htmlspecialchars(
                                    $checklist['descricao'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td>
                                <span class="checklist-cat">
                                    <?= htmlspecialchars(
                                        $checklist['categoria'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
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
                                        class="btn-visualizar"
                                        title="Visualizar"
                                        aria-label="Visualizar checklist"
                                        onclick="visualizarChecklist(<?= (int) $checklist['id'] ?>)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <button
                                        type="button"
                                        class="tabela-btn-editar"
                                        title="Editar"
                                        aria-label="Editar checklist"
                                        onclick="editarChecklist(<?= (int) $checklist['id'] ?>)">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <a
                                        href="checklist.php?excluir=<?= (int) $checklist['id'] ?>"
                                        class="tabela-btn-excluir"
                                        title="Excluir"
                                        aria-label="Excluir checklist"
                                        onclick="return confirm('Excluir este checklist?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($checklists)): ?>
                        <tr>
                            <td colspan="6" class="tabela-vazia">
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

    <script>
        window.categoriasChecklist = <?= json_encode($categorias, $jsonSeguro) ?>;
        window.itensCatalogo = <?= json_encode($itensCatalogo, $jsonSeguro) ?>;
        window.erroChecklist = <?= json_encode($erroFormulario, $jsonSeguro) ?>;
    </script>

    <script src="../assets/JS/componentes/modal.js"></script>
    <script src="../assets/JS/componentes/tabela.js"></script>
    <script src="../assets/JS/checklist.js"></script>

    <?php if ($erroFormulario !== ''): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                alert(window.erroChecklist);

                document
                    .getElementById('modalChecklist')
                    ?.classList.add('active');
            });
        </script>
    <?php endif; ?>
</body>

</html>