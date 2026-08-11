<div class="modal-overlay" id="modalSobrePerfil">
    <div class="modal modal--md">
        <div class="modal__header">
            <div class="modal__header-icone"><i class="fa-solid fa-shield"></i></div>
            <div class="modal__header-texto">
                <h2 class="modal__titulo">Editar Perfil</h2>
                <p class="modal__subtitulo">Informações do perfil de acesso</p>
            </div>
            <button class="modal__fechar" data-modal-close="modalNovoPerfil" type="button">&times;</button>
        </div>

        <form class="modal__body" method="post" action="<?= BASE_URL ?>gerenciamento-acesso" id="formPerfil">
            <div class="foto_sobre">
                <img src="<?= BASE_URL ?>app/assets/img/foto-perfil.jpg" alt="" />
            </div>
        </form>
    </div>
</div>
