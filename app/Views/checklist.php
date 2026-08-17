<?php

use Controller\ChecklistController;

require_once __DIR__ . '/../Controller/ChecklistController.php';

$controllerChecklist = new ChecklistController();
$erroFormularioChecklist = '';

function responderJsonChecklist(array $dadosChecklist): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dadosChecklist, JSON_UNESCAPED_UNICODE);
    exit;
}

function escaparHtmlChecklist(mixed $valorChecklist): string
{
    return htmlspecialchars((string) $valorChecklist, ENT_QUOTES, 'UTF-8');
}

function formatarTempoChecklist(int $minutosChecklist): string
{
    if ($minutosChecklist <= 0) {
        return '0h';
    }

    $horasChecklist = intdiv($minutosChecklist, 60);
    $restanteChecklist = $minutosChecklist % 60;

    return $restanteChecklist === 0
        ? "{$horasChecklist}h"
        : sprintf('%dh%02d', $horasChecklist, $restanteChecklist);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acaoChecklist = $_POST['action'] ?? '';

    switch ($acaoChecklist) {
        case 'cadastrarItemCatalogoChecklist':
            $itemChecklist = $controllerChecklist->cadastrarItemCatalogoChecklist();

            responderJsonChecklist([
                'ok' => (bool) $itemChecklist,
                'item' => $itemChecklist,
                'mensagem' => $itemChecklist
                    ? 'Item cadastrado com sucesso.'
                    : 'Informe um título válido para o item.'
            ]);

        case 'buscarItemCatalogoChecklist':
            $itemChecklist = $controllerChecklist->buscarItemCatalogoChecklist();

            responderJsonChecklist([
                'ok' => (bool) $itemChecklist,
                'item' => $itemChecklist,
                'mensagem' => $itemChecklist ? '' : 'Item não encontrado.'
            ]);

        case 'atualizarItemCatalogoChecklist':
            $itemChecklist = $controllerChecklist->atualizarItemCatalogoChecklist();

            responderJsonChecklist([
                'ok' => (bool) $itemChecklist,
                'item' => $itemChecklist,
                'mensagem' => $itemChecklist
                    ? 'Item atualizado com sucesso.'
                    : 'Não foi possível atualizar. Verifique se o título já existe.'
            ]);

        case 'removerItemCatalogoChecklist':
            $resultadoChecklist = $controllerChecklist->removerItemCatalogoChecklist();

            responderJsonChecklist([
                'ok' => (bool) $resultadoChecklist,
                'resultado' => $resultadoChecklist,
                'mensagem' => is_array($resultadoChecklist)
                    ? ($resultadoChecklist['mensagem'] ?? 'Não foi possível remover o item.')
                    : 'Não foi possível remover o item.'
            ]);

        case 'alterarStatusChecklist':
            $idChecklist = (int) ($_POST['id'] ?? 0);
            $habilitadoChecklist = (int) ($_POST['habilitado'] ?? 0);

            responderJsonChecklist([
                'ok' => $controllerChecklist->alterarStatusChecklist(
                    $idChecklist,
                    $habilitadoChecklist
                )
            ]);

        case 'alterarStatusItemCatalogoChecklist':
            $idItemChecklist = (int) ($_POST['id'] ?? 0);
            $habilitadoItemChecklist = (int) ($_POST['habilitado'] ?? 0);

            responderJsonChecklist([
                'ok' => $controllerChecklist->alterarStatusItemCatalogoChecklist(
                    $idItemChecklist,
                    $habilitadoItemChecklist
                )
            ]);

        case 'buscarChecklist':
            $idChecklist = (int) ($_POST['id'] ?? 0);
            $registroChecklist = $controllerChecklist->buscarComItensChecklist($idChecklist);

            responderJsonChecklist([
                'ok' => (bool) $registroChecklist,
                'checklist' => $registroChecklist
            ]);
    }

    if (isset($_POST['nome'])) {
        $criandoChecklist = empty($_POST['id']);
        $resultadoChecklist = $criandoChecklist
            ? $controllerChecklist->cadastrarChecklist()
            : $controllerChecklist->atualizarChecklist();

        if ($resultadoChecklist) {
            $tipoSucessoChecklist = $criandoChecklist ? 'criado' : 'atualizado';
            header("Location: " . BASE_URL . "checklist?sucesso={$tipoSucessoChecklist}");
            exit;
        }

        $erroFormularioChecklist =
            'Preencha os campos obrigatórios, selecione pelo menos um item e verifique '
            . 'se já não existe um checklist com esse nome.';
    }
}

if (isset($_GET['excluir'])) {
    $controllerChecklist->excluirChecklist((int) $_GET['excluir']);
    header('Location: ' . BASE_URL . 'checklist');
    exit;
}

$mensagemSucessoChecklist = '';

if (($_GET['sucesso'] ?? '') === 'criado') {
    $mensagemSucessoChecklist = 'Checklist cadastrado com sucesso.';
} elseif (($_GET['sucesso'] ?? '') === 'atualizado') {
    $mensagemSucessoChecklist = 'Checklist atualizado com sucesso.';
}

$checklists = $controllerChecklist->listarChecklist();
$categoriasChecklist = $controllerChecklist->listarCategoriasChecklist();
$itensCatalogoChecklist = $controllerChecklist->listarItensCatalogoChecklist();

$nomesChecklist = array_map(
    static fn(array $checklist): array => [
        'id' => (int) $checklist['id'],
        'nome' => $checklist['nome']
    ],
    $checklists
);

$jsonSeguroChecklist = JSON_UNESCAPED_UNICODE
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

    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Pages/checklist.css">

    <title>Cadastro de Checklist</title>
</head>

<body>
    <?php
    $tituloPagina = 'Checklist';
    include 'Components/menu.php';
    ?>

    <main class="checklist-pagina">

        <div class="checklist-cadastro-acoes">
            <button
                type="button"
                class="btn-novo-cadastro"
                data-modal-target="checklist-modal-formulario"
                onclick="limparFormularioChecklist()">
                <i class="fa-solid fa-plus"></i>Novo Checklist
            </button>
        </div>

        <div class="tabela-wrapper">
            <table id="table">
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
                            <span class="th-label">
                                Tempo Estimado
                                <i class="fa-solid fa-sort sort-icon"></i>
                            </span>
                        </th>
                        <th data-col="5" class="col-status">
                            <span class="th-label">Status</span>
                        </th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($checklists as $checklist): ?>
                        <?php $ativoChecklist = (bool) $checklist['habilitado']; ?>

                        <tr class="<?= $ativoChecklist ? '' : 'linha-inativa' ?>">
                            <td><?= (int) $checklist['id'] ?></td>
                            <td><?= escaparHtmlChecklist($checklist['nome']) ?></td>
                            <td class="checklist-tabela-descricao">
                                <?= escaparHtmlChecklist($checklist['descricao'] ?? '') ?>
                            </td>
                            <td>
                                <span class="checklist-categoria-badge">
                                    <?= escaparHtmlChecklist($checklist['categoria'] ?? '') ?>
                                </span>
                            </td>
                            <td>
                                ~<?= formatarTempoChecklist((int) $checklist['tempo_estimado_total']) ?>
                            </td>
                            <td class="col-status">
                                <div class="checklist-status">
                                    <label class="switch">
                                        <input
                                            type="checkbox"
                                            <?= $ativoChecklist ? 'checked' : '' ?>
                                            data-id="<?= (int) $checklist['id'] ?>"
                                            onchange="alternarStatusChecklist(this)">
                                        <span class="switch-slider"></span>
                                    </label>

                                    <span class="checklist-status-texto">
                                        <?= $ativoChecklist ? 'Ativo' : 'Inativo' ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="acoes">
                                    <button
                                        type="button"
                                        class="checklist-btn-visualizar"
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
                                        href="<?= BASE_URL ?>checklist?excluir=<?= (int) $checklist['id'] ?>"
                                        class="tabela-btn-excluir"
                                        title="Excluir"
                                        aria-label="Excluir checklist"
                                        data-modal-target="popupExcluir"
                                        data-popup-href="<?= BASE_URL ?>checklist?excluir=<?= (int) $checklist['id'] ?>"
                                        onclick="return false;">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($checklists)): ?>
                        <tr>
                            <td colspan="7" class="checklist-tabela-vazia">
                                Nenhum checklist cadastrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="7" class="rodape-tabela">
                            <div class="paginacao"></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="modal-overlay" id="checklist-modal-formulario">
            <div class="modal modal--xxl">
                <div class="modal__header">
                    <div class="modal__header-icone">
                        <img src="<?= BASE_URL ?>app/assets/img/Icon_Checklist.png" alt="Cadastro de Checklist">
                    </div>

                    <div class="modal__header-texto">
                        <h2 class="modal__titulo" id="checklist-titulo-modal">
                            Cadastro de Checklist
                        </h2>
                        <p class="modal__subtitulo">
                            Crie e organize listas padrões de verificação
                        </p>
                    </div>

                    <button type="button" class="modal__fechar" data-modal-close>&times;</button>
                </div>

                <form
                    method="POST"
                    action="<?= BASE_URL ?>checklist"
                    class="checklist-formulario"
                    id="checklist-formulario">
                    <input type="hidden" name="id" id="checklist-id">

                    <div class="modal__body">
                        <section class="modal-secao">
                            <div class="modal-secao__titulo">
                                <i class="fa-solid fa-clipboard-check modal-secao__titulo-icone"></i>
                                <h3>Dados do Checklist</h3>
                            </div>

                            <div class="checklist-dados-grid">
                                <div class="campo">
                                    <label
                                        for="checklist-nome"
                                        class="campo__label campo__label--obrigatorio">
                                        Nome do Checklist
                                    </label>

                                    <div class="checklist-nome-combobox">
                                        <input
                                            type="text"
                                            name="nome"
                                            id="checklist-nome"
                                            class="campo__input"
                                            placeholder="Ex: Checklist Web"
                                            autocomplete="off"
                                            maxlength="80"
                                            required
                                            aria-autocomplete="list"
                                            aria-controls="checklist-lista-nomes"
                                            aria-expanded="false">

                                        <div
                                            id="checklist-lista-nomes"
                                            class="checklist-categoria-lista"
                                            hidden></div>
                                    </div>

                                    <small class="campo__mensagem-erro" id="checklist-erro-nome">
                                        Já existe um checklist com esse nome. Clique nele na lista para editar.
                                    </small>
                                </div>

                                <div class="campo">
                                    <div class="checklist-categoria-cabecalho">
                                        <label
                                            for="checklist-categoria"
                                            class="campo__label campo__label--obrigatorio">
                                            Categoria
                                        </label>
                                        <small class="campo__ajuda">
                                            Digite uma categoria nova ou selecione uma existente.
                                        </small>
                                    </div>

                                    <div class="checklist-categoria-combobox">
                                        <input
                                            type="text"
                                            name="categoria"
                                            id="checklist-categoria"
                                            class="campo__input checklist-categoria-input"
                                            placeholder="Digite ou selecione uma categoria"
                                            autocomplete="off"
                                            maxlength="150"
                                            required
                                            aria-autocomplete="list"
                                            aria-controls="checklist-lista-categorias"
                                            aria-expanded="false">

                                        <button
                                            type="button"
                                            id="checklist-btn-categorias"
                                            class="checklist-categoria-botao"
                                            title="Mostrar categorias"
                                            aria-label="Mostrar categorias cadastradas">
                                            <i class="fa-solid fa-chevron-down"></i>
                                        </button>

                                        <div
                                            id="checklist-lista-categorias"
                                            class="checklist-categoria-lista"
                                            hidden></div>
                                    </div>
                                </div>
                            </div>

                            <div class="campo checklist-descricao-campo">
                                <label for="checklist-descricao" class="campo__label">
                                    Descrição
                                </label>
                                <textarea
                                    name="descricao"
                                    id="checklist-descricao"
                                    class="campo__textarea"
                                    maxlength="1000"
                                    placeholder="Descreva o objetivo e a finalidade deste checklist..."
                                    oninput="contarDescricaoChecklist()"></textarea>

                                <div class="checklist-contador">
                                    <span>Máximo de 1000 caracteres</span>
                                    <span id="checklist-contador-descricao">0 / 1000</span>
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
                                    onclick="abrirGerenciamentoItensChecklist()">
                                    <i class="fa-solid fa-list-check"></i>
                                    Gerenciar itens
                                </button>
                            </div>

                            <div
                                class="checklist-gerenciar-resumo"
                                id="checklist-tempo-resumo"
                                hidden>
                                <span>Tempo estimado total</span>
                                <strong id="checklist-tempo-total">0h</strong>
                            </div>

                            <div
                                id="checklist-lista-selecionados"
                                class="checklist-lista-selecionados"></div>
                        </section>
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

        <div class="modal-overlay" id="checklist-modal-visualizar">
            <div class="modal modal--xxl">
                <div class="modal__header">
                    <div class="modal__header-icone">
                        <i class="fa-solid fa-eye"></i>
                    </div>

                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Visualizar Checklist</h2>
                        <p class="modal__subtitulo">Informações e itens vinculados</p>
                    </div>

                    <button type="button" class="modal__fechar" data-modal-close>&times;</button>
                </div>

                <div class="modal__body checklist-visualizacao">
                    <input type="hidden" id="checklist-visualizar-id">

                    <section class="checklist-visualizacao-secao">
                        <div class="checklist-visualizacao-titulo">
                            <i class="fa-solid fa-clipboard-check"></i>
                            <h3>Dados do Checklist</h3>
                        </div>

                        <div class="checklist-visualizacao-dados">
                            <div class="checklist-visualizacao-campo">
                                <span class="checklist-visualizacao-label">Nome</span>
                                <strong id="checklist-visualizar-nome"></strong>
                            </div>

                            <div class="checklist-visualizacao-campo">
                                <span class="checklist-visualizacao-label">Categoria</span>
                                <strong id="checklist-visualizar-categoria"></strong>
                            </div>

                            <div class="checklist-visualizacao-campo checklist-visualizacao-campo--largo">
                                <span class="checklist-visualizacao-label">Descrição</span>
                                <p id="checklist-visualizar-descricao"></p>
                            </div>

                            <div class="checklist-visualizacao-campo">
                                <span class="checklist-visualizacao-label">Status</span>
                                <span
                                    id="checklist-visualizar-status"
                                    class="checklist-visualizacao-status"></span>
                            </div>
                        </div>
                    </section>

                    <section class="checklist-visualizacao-secao">
                        <div class="checklist-visualizacao-cabecalho">
                            <div class="checklist-visualizacao-titulo">
                                <i class="fa-solid fa-list-check"></i>
                                <h3>Itens do Checklist</h3>
                            </div>
                            <span
                                id="checklist-visualizar-total"
                                class="checklist-visualizacao-total">
                                0 itens
                            </span>
                        </div>

                        <div
                            id="checklist-visualizar-itens"
                            class="checklist-visualizacao-lista"></div>
                    </section>
                </div>

                <div class="modal__footer">
                    <button type="button" class="btn-cancelar" data-modal-close>
                        FECHAR
                    </button>
                    <button
                        type="button"
                        class="btn-botao-verde"
                        onclick="editarDaVisualizacaoChecklist()">
                        <i class="fa-solid fa-pen-to-square"></i>
                        EDITAR CHECKLIST
                    </button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="checklist-modal-gerenciar-itens">
            <div class="modal modal--xxl">
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

                    <button type="button" class="modal__fechar" data-modal-close>&times;</button>
                </div>

                <div class="modal__body">
                    <div class="checklist-gerenciar-topo">
                        <div class="campo checklist-gerenciar-pesquisa">
                            <label for="checklist-pesquisa-itens" class="campo__label">
                                Pesquisar itens
                            </label>

                            <div class="checklist-gerenciar-campo-pesquisa">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input
                                    type="text"
                                    id="checklist-pesquisa-itens"
                                    class="campo__input"
                                    placeholder="Pesquise pelo título ou referência"
                                    autocomplete="off">
                            </div>
                        </div>

                        <button
                            type="button"
                            class="btn-botao-verde checklist-gerenciar-novo"
                            onclick="abrirNovoItemChecklist()">
                            <i class="fa-solid fa-plus"></i>
                            Cadastrar novo item
                        </button>
                    </div>

                    <div class="checklist-gerenciar-resumo">
                        <span>Itens disponíveis</span>
                        <strong id="checklist-contador-selecionados">0 selecionados</strong>
                    </div>

                    <div
                        id="checklist-lista-gerenciamento"
                        class="checklist-gerenciar-lista"></div>
                </div>

                <div class="modal__footer">
                    <button type="button" class="btn-cancelar" data-modal-close>
                        CANCELAR
                    </button>
                    <button
                        type="button"
                        class="btn-botao-verde"
                        onclick="aplicarItensGerenciadosChecklist()">
                        APLICAR ITENS
                    </button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="checklist-modal-item">
            <div class="modal modal--xxl">
                <div class="modal__header">
                    <div class="modal__header-icone">
                        <i class="fa-solid fa-list-check"></i>
                    </div>

                    <div class="modal__header-texto">
                        <h2 class="modal__titulo" id="checklist-titulo-modal-item">
                            Cadastro de Item
                        </h2>
                        <p class="modal__subtitulo" id="checklist-subtitulo-modal-item">
                            Cadastre um novo item reutilizável
                        </p>
                    </div>

                    <button type="button" class="modal__fechar" data-modal-close>&times;</button>
                </div>

                <div class="modal__body">
                    <input type="hidden" id="checklist-item-id">

                    <div class="campo">
                        <label
                            for="checklist-item-titulo"
                            class="campo__label campo__label--obrigatorio">
                            Título
                        </label>
                        <input
                            type="text"
                            id="checklist-item-titulo"
                            class="campo__input"
                            placeholder="Ex: Verificar SQL Injection"
                            maxlength="150">
                    </div>

                    <div class="campo">
                        <label for="checklist-item-referencia" class="campo__label">
                            Referência
                        </label>
                        <input
                            type="text"
                            id="checklist-item-referencia"
                            class="campo__input"
                            placeholder="Ex: OWASP A03:2021"
                            maxlength="255">
                    </div>

                    <div class="campo">
                        <label for="checklist-item-descricao-resumida" class="campo__label">
                            Descrição resumida
                        </label>
                        <textarea
                            id="checklist-item-descricao-resumida"
                            class="campo__textarea"
                            placeholder="Resumo do que este item verifica..."
                            oninput="contarDescricaoResumidaItemChecklist()"></textarea>

                        <div class="checklist-contador">
                            <span id="checklist-item-contador-descricao-resumida">0 caracteres</span>
                        </div>
                    </div>

                    <div class="checklist-dados-grid">
                        <div class="campo">
                            <label for="checklist-item-obrigatorio" class="campo__label">
                                Obrigatório
                            </label>
                            <div class="campo__select-wrapper">
                                <select id="checklist-item-obrigatorio" class="campo__select">
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                                <i class="fa-solid fa-chevron-down campo__select-seta"></i>
                            </div>
                        </div>

                        <div class="campo">
                            <label for="checklist-item-tempo-estimado" class="campo__label">
                                Tempo estimado de execução
                            </label>
                            <input
                                type="number"
                                id="checklist-item-tempo-estimado"
                                class="campo__input"
                                placeholder="Ex: 90"
                                min="0"
                                step="5"
                                inputmode="numeric">
                            <small class="campo__ajuda">Em minutos. Ex: 90 = 1h30.</small>
                        </div>
                    </div>
                </div>

                <div class="modal__footer">
                    <button type="button" class="btn-cancelar" data-modal-close>
                        CANCELAR
                    </button>
                    <button
                        type="button"
                        class="btn-botao-verde"
                        id="checklist-btn-salvar-item"
                        onclick="salvarItemCatalogoChecklist()">
                        SALVAR
                    </button>
                </div>
            </div>
        </div>

        <?php include 'Components/popup_excluir.php'; ?>
        <?php include 'Components/toast.php'; ?>
        
        <div class="modal-overlay" id="checklist-modal-item-visualizar">
            <div class="modal modal--lg">
                <div class="modal__header">
                    <div class="modal__header-icone">
                        <i class="fa-solid fa-eye"></i>
                    </div>

                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Visualizar Item</h2>
                        <p class="modal__subtitulo">Detalhes do item reutilizável</p>
                    </div>

                    <button type="button" class="modal__fechar" data-modal-close>&times;</button>
                </div>

                <div class="modal__body checklist-visualizacao">
                    <input type="hidden" id="checklist-item-visualizar-id">

                    <section class="checklist-visualizacao-secao">
                        <div class="checklist-visualizacao-cabecalho">
                            <div class="checklist-visualizacao-titulo">
                                <i class="fa-solid fa-list-check"></i>
                                <h3>Dados do Item</h3>
                            </div>
                            <span
                                id="checklist-item-visualizar-status"
                                class="checklist-visualizacao-status"></span>
                        </div>

                        <div class="checklist-visualizacao-dados">
                            <div class="checklist-visualizacao-campo checklist-visualizacao-campo--largo">
                                <span class="checklist-visualizacao-label">Título</span>
                                <strong id="checklist-item-visualizar-titulo"></strong>
                            </div>

                            <div class="checklist-visualizacao-campo">
                                <span class="checklist-visualizacao-label">Referência</span>
                                <strong id="checklist-item-visualizar-referencia"></strong>
                            </div>

                            <div class="checklist-visualizacao-campo">
                                <span class="checklist-visualizacao-label">Tempo estimado</span>
                                <strong id="checklist-item-visualizar-tempo"></strong>
                            </div>

                            <div class="checklist-visualizacao-campo checklist-visualizacao-campo--largo">
                                <span class="checklist-visualizacao-label">Descrição resumida</span>
                                <p id="checklist-item-visualizar-descricao"></p>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="modal__footer">
                    <button type="button" class="btn-cancelar" data-modal-close>
                        FECHAR
                    </button>
                    <button
                        type="button"
                        class="btn-botao-verde"
                        id="checklist-item-btn-ativar"
                        onclick="alternarStatusItemCatalogoChecklist()">
                        <i class="fa-solid fa-check" id="checklist-item-btn-ativar-icone"></i>
                        <span id="checklist-item-btn-ativar-texto">ATIVAR ITEM</span>
                    </button>
                </div>
            </div>
        </div>

    </main>

    <script>
        window.baseUrl = <?= json_encode(BASE_URL) ?>;
        window.categoriasChecklist = <?= json_encode(
                                            $categoriasChecklist,
                                            $jsonSeguroChecklist
                                        ) ?>;

        window.itensCatalogoChecklist = <?= json_encode(
                                            $itensCatalogoChecklist,
                                            $jsonSeguroChecklist
                                        ) ?>;

        window.checklistsExistentesChecklist = <?= json_encode(
                                            $nomesChecklist,
                                            $jsonSeguroChecklist
                                        ) ?>;

        window.erroFormularioChecklist = <?= json_encode(
                                                $erroFormularioChecklist,
                                                $jsonSeguroChecklist
                                            ) ?>;

        window.mensagemSucessoChecklist = <?= json_encode(
                                                $mensagemSucessoChecklist,
                                                $jsonSeguroChecklist
                                            ) ?>;
    </script>
    <script src="<?= BASE_URL ?>app/assets/JS/componentes/modal.js"></script>
    <script src="<?= BASE_URL ?>app/assets/JS/componentes/popup-confirmacao.js"></script>
    <script src="<?= BASE_URL ?>app/assets/JS/componentes/toast.js"></script>
    <script src="<?= BASE_URL ?>app/assets/JS/componentes/tabela.js"></script>
    <script src="<?= BASE_URL ?>app/assets/JS/checklist.js"></script>
    <?php if ($erroFormularioChecklist !== ''): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.notificarChecklist(window.erroFormularioChecklist, 'erro');
                document
                    .getElementById('checklist-modal-formulario')
                    ?.classList.add('active');
            });
        </script>
    <?php endif; ?>
    <?php if ($mensagemSucessoChecklist !== ''): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.notificarChecklist(window.mensagemSucessoChecklist, 'sucesso');
                history.replaceState(null, '', window.baseUrl + 'checklist');
            });
        </script>
    <?php endif; ?>

</body>

</html>
