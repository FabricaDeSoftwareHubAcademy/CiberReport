<?php
/*
    POPUP DE CONFIRMAÇÃO DE SALVAMENTO
    ===================================
    Popup pronto, sem variáveis — basta incluir uma vez por página,
    antes do </main>:

        include 'Components/popup_salvar.php';

    No botão que deve abrir o popup, aponte para ele e informe o nome
    de uma função JS global que será chamada ao confirmar:

        <button type="button"
                data-modal-target="popupSalvar"
                data-popup-callback="salvarChecklist">
            Salvar
        </button>

    A página também precisa carregar os scripts:
        <script src="../assets/JS/componentes/modal.js"></script>
        <script src="../assets/JS/componentes/popup-confirmacao.js"></script>
*/
?>
<div class="modal-overlay popup-alerta-overlay" id="popupSalvar">
    <div class="popup-alerta">
        <div class="popup-alerta__icone popup-alerta__icone--sucesso">
            <i class="fa-solid fa-floppy-disk"></i>
        </div>

        <h2 class="popup-alerta__titulo">Confirmar Salvamento</h2>
        <p class="popup-alerta__texto">
            Deseja salvar as alterações realizadas?
        </p>

        <div class="popup-alerta__acoes">
            <button type="button" class="btn-cancelar" data-modal-close>
                Não, voltar
            </button>
            <button type="button" class="btn-botao-verde" data-popup-confirmar>
                Sim, Salvar
            </button>
        </div>
    </div>
</div>
