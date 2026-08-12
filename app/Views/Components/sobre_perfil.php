<div class="modal-overlay" id="modalSobrePerfil">
    <div class="modal modal--xl">
        <div class="modal__header">
            <div class="modal__header-icone"><i class="fa-solid fa-user-shield"></i></div>
            <div class="modal__header-texto">
                <h2 class="modal__titulo">Editar Perfil</h2>
                <p class="modal__subtitulo">Informações pessoais e segurança da conta</p>
            </div>
            <button class="modal__fechar" data-modal-close="modalSobrePerfil" type="button">&times;</button>
        </div>

        <form method="post" action="<?= BASE_URL ?>gerenciamento-acesso" id="formPerfil">
            <div class="modal__body sobre-perfil__corpo">
                <div class="foto_sobre">
                    <div class="foto_sobre__imagem">
                        <img src="<?= BASE_URL ?>app/assets/img/foto-perfil.jpg" alt="Foto de perfil" />
                        <button class="foto_sobre__editar" type="button" aria-label="Alterar foto de perfil">
                            <i class="fa-solid fa-camera"></i>
                        </button>
                    </div>
                    <span><?= htmlspecialchars($nomeUsuario) ?></span>
                    <small>Administrador</small>
                </div>

                <section class="sobre-perfil__secao">
                    <div class="sobre-perfil__secao-titulo">
                        <i class="fa-regular fa-user"></i>
                        <h3>Informações pessoais</h3>
                    </div>

                    <div class="modal-grade modal-grade--2">
                        <div class="campo">
                            <label class="campo__label" for="nomePerfil">Nome</label>
                            <input class="campo__input" type="text" name="nome" id="nomePerfil"
                                value="<?= htmlspecialchars($nomeUsuario) ?>" autocomplete="name">
                        </div>

                        <div class="campo">
                            <label class="campo__label" for="emailPerfil">E-mail</label>
                            <div class="campo__input-wrapper sobre-perfil__input-wrapper">
                                <input class="campo__input campo__input--readonly" type="email" name="email"
                                    id="emailPerfil" placeholder="email@empresa.com" autocomplete="email" readonly>
                                <i class="fa-regular fa-circle-check sobre-perfil__input-status sobre-perfil__input-status--ok"
                                    aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="sobre-perfil__secao">
                    <div class="sobre-perfil__secao-titulo">
                        <i class="fa-solid fa-lock"></i>
                        <h3>Alterar senha</h3>
                    </div>

                    <div class="modal-grade modal-grade--3">
                        <div class="campo">
                            <label class="campo__label" for="senhaAtual">Senha atual</label>
                            <input class="campo__input" type="password" name="senha_atual" id="senhaAtual"
                                placeholder="Digite sua senha atual" autocomplete="current-password">
                        </div>
                        <div class="campo">
                            <label class="campo__label" for="novaSenha">Nova senha</label>
                            <input class="campo__input" type="password" name="nova_senha" id="novaSenha"
                                placeholder="Digite a nova senha" autocomplete="new-password">
                        </div>
                        <div class="campo">
                            <label class="campo__label" for="confirmarSenha">Confirmar senha</label>
                            <input class="campo__input" type="password" name="confirmar_senha" id="confirmarSenha"
                                placeholder="Repita a nova senha" autocomplete="new-password">
                        </div>
                    </div>
                    <p class="sobre-perfil__ajuda"><i class="fa-solid fa-circle-info"></i> Use pelo menos 8 caracteres, incluindo letras e números.</p>
                </section>

                <section class="editar-perfil-item">
                    <i class="fa-regular fa-envelope editar-perfil-item__icone"></i>
                    <div class="Editar-email-usuario">
                        <span class="editar-nome-usuario">Autenticação em duas etapas por e-mail</span>
                        <span class="editar-descricao-usuario">Receba um código de verificação sempre que acessar a conta.</span>
                    </div>
                    <label class="switch" aria-label="Ativar autenticação em duas etapas">
                        <input type="checkbox" name="edit_perfil_usuario" checked>
                        <span class="switch-slider"></span>
                    </label>
                </section>

                <section class="sobre-perfil__secao">
                    <div class="sobre-perfil__secao-titulo">
                        <i class="fa-solid fa-shield-halved"></i>
                        <h3>Atividade de segurança recente</h3>
                    </div>

                    <div class="sobre-perfil__tabela-wrapper">
                        <table class="sobre-perfil__tabela">
                            <thead>
                                <tr>
                                    <th>Atividade</th>
                                    <th>Data e hora</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Senha alterada</td>
                                    <td>Hoje, 14h34</td>
                                    <td><span class="sobre-perfil__status sobre-perfil__status--sucesso">Sucesso</span></td>
                                </tr>
                                <tr>
                                    <td>2FA concluído</td>
                                    <td>Ontem, às 8h45</td>
                                    <td><span class="sobre-perfil__status sobre-perfil__status--verificado">Verificado</span></td>
                                </tr>
                                <tr>
                                    <td>Login em novo dispositivo</td>
                                    <td>Ontem, às 8h43</td>
                                    <td><span class="sobre-perfil__status sobre-perfil__status--sucesso">Sucesso</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <div class="modal__footer">
                <button type="button" class="btn-cancelar" data-modal-close="modalSobrePerfil">CANCELAR</button>
                <button type="button" class="btn-botao-verde">SALVAR ALTERAÇÕES</button>
            </div>
        </form>
    </div>
</div>
