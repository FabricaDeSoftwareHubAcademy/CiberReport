<?php
/**
 * Components/modal-andamento-projeto/index.php
 *
 * Modal "Andamento do Projeto" — visão só-leitura com abas (Horas,
 * Vulnerabilidade, Equipe, Checklist, Log de Atividades), aberto pelo
 * botão "Visualizar" da tabela de gerenciamento de projetos.
 *
 * Variável esperada: $dadosAndamento — JSON string, objeto indexado por
 * id do projeto, com os dados agregados (ver ProjetoController::listarAndamentoCompleto).
 */
?>
<div class="modal-overlay" id="modal-andamento-projeto" data-andamento="<?= $dadosAndamento ?>">
    <div class="modal modal--xl">
        <div class="modal__header">
            <div class="modal__header-icone"><i class="fa-solid fa-chart-simple"></i></div>
            <div class="modal__header-texto">
                <h2 class="modal__titulo">Andamento do Projeto</h2>
                <p class="modal__subtitulo">Informações detalhadas sobre o projeto</p>
            </div>
            <button type="button" class="modal__fechar" data-modal-close aria-label="Fechar modal">&times;</button>
        </div>

        <div class="modal-meta-grid">
            <div class="modal-meta-celula">
                <span class="modal-meta-celula__rotulo">Nome/ID</span>
                <span class="modal-meta-celula__valor" id="and-nome">—</span>
                <span class="modal__footer-info" id="and-id"></span>
            </div>
            <div class="modal-meta-celula">
                <span class="modal-meta-celula__rotulo">Tipo de Teste</span>
                <span class="modal-meta-celula__valor" id="and-tipos">—</span>
            </div>
            <div class="modal-meta-celula">
                <span class="modal-meta-celula__rotulo">Confidencialidade</span>
                <span class="modal-meta-celula__valor" id="and-sigilo">—</span>
            </div>
            <div class="modal-meta-celula">
                <span class="modal-meta-celula__rotulo">Modalidade</span>
                <span class="modal-meta-celula__valor" id="and-modalidade">—</span>
            </div>
        </div>

        <div class="modal-meta-grid">
            <div class="modal-meta-celula">
                <span class="modal-meta-celula__rotulo">Vulnerabilidades Encontradas</span>
                <span class="modal-meta-celula__valor" id="and-vulns-total">—</span>
                <span class="modal__footer-info" id="and-vulns-extra"></span>
            </div>
            <div class="modal-meta-celula">
                <span class="modal-meta-celula__rotulo" id="and-horas-restantes-rotulo">Horas Restantes</span>
                <span class="modal-meta-celula__valor" id="and-horas-restantes">—</span>
            </div>
            <div class="modal-meta-celula">
                <span class="modal-meta-celula__rotulo">Itens de Checklist Pendentes</span>
                <span class="modal-meta-celula__valor" id="and-checklist-pendentes">—</span>
                <span class="modal__footer-info" id="and-checklist-extra"></span>
            </div>
            <div class="modal-meta-celula">
                <span class="modal-meta-celula__rotulo">Prazo Final</span>
                <span class="modal-meta-celula__valor" id="and-prazo">—</span>
                <span class="modal__footer-info" id="and-inicio"></span>
            </div>
        </div>

        <nav class="modal-tabs" id="andamento-abas">
            <button type="button" class="modal-tabs__item modal-tabs__item--ativo" data-aba="horas">Horas</button>
            <button type="button" class="modal-tabs__item" data-aba="vulnerabilidade">Vulnerabilidade <span class="modal-tabs__badge" id="and-badge-vulns">0</span></button>
            <button type="button" class="modal-tabs__item" data-aba="equipe">Equipe <span class="modal-tabs__badge" id="and-badge-equipe">0</span></button>
            <button type="button" class="modal-tabs__item" data-aba="checklist">Checklist <span class="modal-tabs__badge" id="and-badge-checklist">0</span></button>
            <button type="button" class="modal-tabs__item" data-aba="log">Log de Atividades</button>
        </nav>

        <div class="modal__body">
            <?php include __DIR__ . '/_aba-horas.php'; ?>
            <?php include __DIR__ . '/_aba-vulnerabilidade.php'; ?>
            <?php include __DIR__ . '/_aba-equipe.php'; ?>
            <?php include __DIR__ . '/_aba-checklist.php'; ?>
            <?php include __DIR__ . '/_aba-log.php'; ?>
        </div>

        <footer class="modal__footer modal__footer--com-info">
            <span class="modal__footer-info" id="and-atualizacao">Última atualização: —</span>
            <button type="button" class="btn btn--secundario" data-modal-close>Fechar</button>
        </footer>
    </div>
</div>
