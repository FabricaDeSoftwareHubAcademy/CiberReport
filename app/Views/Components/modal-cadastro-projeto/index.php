<?php
/**
 * Components/modal-cadastro-projeto/index.php
 *
 * Modal de cadastro de projeto com stepper de 3 passos: Informações do
 * Cliente (dados do projeto), Informações do Pentest (N blocos
 * repetíveis, um por pentest contratado, cada um com sua própria equipe)
 * e Revisão (resumo de tudo antes de salvar).
 *
 * Variáveis esperadas (injetadas pela view pai antes do include):
 *   $dadosModal['empresas']     — JSON string com empresas ativas
 *   $dadosModal['tiposPentest'] — JSON string com tipos de pentest ativos (id, nome, categoria_id, categoria_nome)
 *   $dadosModal['frameworks']   — JSON string com frameworks/metodologias ativos
 *   $dadosModal['usuarios']     — JSON string com usuários ativos
 */
?>
<div class="modal-overlay" id="modal-cadastro-projeto"
    data-empresas="<?= $dadosModal['empresas'] ?>"
    data-tipos-pentest="<?= $dadosModal['tiposPentest'] ?>"
    data-frameworks="<?= $dadosModal['frameworks'] ?>"
    data-usuarios="<?= $dadosModal['usuarios'] ?>"
    data-projetos="<?= $dadosModal['projetos'] ?>">
    <div class="modal modal--xl modal--com-stepper">

        <div class="modal__header">
            <div class="modal__header-icone">
                <img src="<?= BASE_URL ?>app/assets/img/logo-baikal-icone.svg" alt="Logo CiberReport">
            </div>
            <div class="modal__header-texto">
                <h2 class="modal__titulo" id="cadastro-projeto-titulo">Cadastro de Projeto</h2>
                <p class="modal__subtitulo" id="cadastro-projeto-subtitulo">Informações da empresa contratante e do projeto</p>
            </div>
            <button type="button" class="modal__fechar" data-modal-close aria-label="Fechar modal">&times;</button>
        </div>

        <div class="modal__stepper">
            <ol class="cad-projeto-stepper" id="cad-projeto-stepper" aria-label="Etapas do cadastro">
                <li class="cad-projeto-stepper__item cad-projeto-stepper__item--ativo" data-passo="0">
                    <span class="cad-projeto-stepper__numero">1</span>
                    <span class="cad-projeto-stepper__label">Informações do Projeto</span>
                </li>
                <li class="cad-projeto-stepper__separador" aria-hidden="true"></li>
                <li class="cad-projeto-stepper__item" data-passo="1">
                    <span class="cad-projeto-stepper__numero">2</span>
                    <span class="cad-projeto-stepper__label">Informações do Pentest</span>
                </li>
                <li class="cad-projeto-stepper__separador" aria-hidden="true"></li>
                <li class="cad-projeto-stepper__item" data-passo="2">
                    <span class="cad-projeto-stepper__numero">3</span>
                    <span class="cad-projeto-stepper__label">Revisão</span>
                </li>
            </ol>
        </div>

        <form id="form-cadastro-projeto"
            action="<?= BASE_URL ?>gerenciamento-projeto"
            method="post"
            enctype="multipart/form-data"
            novalidate>
            <input type="hidden" name="action"     id="cp-action" value="cadastrar">
            <input type="hidden" name="id"          id="cp-projeto-id">
            <input type="hidden" name="empresa_id" id="cp-empresa-id">
            <input type="hidden" name="lider_id"   id="cp-lider-id">

            <?php include __DIR__ . '/_passo-1-cliente.php'; ?>
            <?php include __DIR__ . '/_passo-2-pentest.php'; ?>
            <?php include __DIR__ . '/_passo-3-revisao.php'; ?>

            <footer class="modal__footer" id="cp-footer">
                <button type="button" id="cp-btn-voltar" class="btn btn--secundario" style="display:none">
                    ← Voltar
                </button>
                <button type="button" id="cp-btn-avancar" class="btn btn--primario">
                    Avançar →
                </button>
                <button type="submit" id="cp-btn-salvar" class="btn btn--primario" style="display:none">
                    Salvar Projeto
                </button>
            </footer>
        </form>
    </div>
</div>

<!-- Confirmação ao fechar o modal com dados preenchidos (fica fora do overlay do modal de propósito) -->
<div class="modal-overlay popup-alerta-overlay" id="popupCancelarProjeto">
    <div class="popup-alerta">
        <div class="popup-alerta__icone popup-alerta__icone--perigo">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <h2 class="popup-alerta__titulo">Cancelar preenchimento?</h2>
        <p class="popup-alerta__texto">
            Os dados informados neste formulário serão perdidos.
        </p>

        <div class="popup-alerta__acoes">
            <button type="button" class="btn-cancelar" data-modal-close>
                Não, continuar
            </button>
            <button type="button" class="btn-vermelho" id="cp-confirmar-cancelamento">
                Sim, cancelar
            </button>
        </div>
    </div>
</div>
