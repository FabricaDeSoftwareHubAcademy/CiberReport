<?php
/*
    POPUP DE CONFIRMAÇÃO DE EXCLUSÃO
    ================================
    Popup pronto, sem variáveis — basta incluir uma vez por página,
    antes do </main>:

        include 'Components/popup_excluir.php';

    Em cada botão de excluir, aponte para o popup e informe o link
    que deve ser seguido ao confirmar:

        <a href="checklist.php?excluir=ID_AQUI"
           data-modal-target="popupExcluir"
           data-popup-href="checklist.php?excluir=ID_AQUI"
           onclick="return false;">
            <i class="fa-solid fa-trash"></i>
        </a>

    A página também precisa carregar os scripts:
        <script src="../assets/JS/componentes/modal.js"></script>
        <script src="../assets/JS/componentes/popup-confirmacao.js"></script>
*/
?>
<div class="modal-overlay popup-alerta-overlay" id="popupExcluir">
    <div class="popup-alerta">
        <div class="popup-alerta__icone popup-alerta__icone--perigo">
            <i class="fa-solid fa-trash"></i>
        </div>

        <h2 class="popup-alerta__titulo">Confirmar Exclusão</h2>
        <p class="popup-alerta__texto">
            Você deseja excluir? Essa ação não pode ser desfeita após a exclusão.
        </p>

        <div class="popup-alerta__acoes">
            <button type="button" class="btn-cancelar" data-modal-close>
                Não, voltar
            </button>
            <a href="#" class="btn-vermelho" data-popup-confirmar>
                Sim, Excluir
            </a>
        </div>
    </div>
</div>
