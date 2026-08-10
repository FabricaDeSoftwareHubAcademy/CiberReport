<div class="modal-overlay" id="modalSobrePerfil">
    <div class="modal modal-dl">
        <div class="modal__header">
            <div class="modal__header-icone"><i class="fa-solid fa-shield"></i></div>
            <div class="modal__header-texto">
                <h2 class="modal__titulo">Cadastro de Perfil</h2>
                <p class="modal__subtitulo">Informações do perfil de acesso</p>
            </div>
            <button class="modal__fechar" data-modal-close="modalNovoPerfil" type="button">X</button>
        </div>

        <form class="modal__body" method="post" action="<?= BASE_URL ?>gerenciamento-acesso" id="formPerfil">
            <aside>
                <img src="<?= BASE_URL ?>app/assets/img/logo-baikal-icone.svg" alt="" />
            </aside>
        </form>
    </div>
</div>
