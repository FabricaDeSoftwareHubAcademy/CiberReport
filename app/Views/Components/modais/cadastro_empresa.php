<?php
/*
    MODAL DE CADASTRO DE EMPRESA (CLIENTE)
    =======================================
    Extraído de app/Views/cliente_empresa.php para poder ser incluído também
    em outras telas (ex.: atalho "+" do campo Cliente no cadastro de projeto).

    Inclua uma vez por página:

        include 'Components/modais/cadastro_empresa.php';

    A página também precisa carregar modal.js, mascaras.js e Buscarcep.js.

    $mensagem_erro é opcional: a tela de Clientes define quando o cadastro
    falha, e o modal já abre mostrando a mensagem. Sem ela, abre fechado.

    O <form> envia por POST para a própria página. Quem inclui o modal fora
    da tela de Clientes deve interceptar o submit e salvar por fetch.
*/
$mensagem_erro = $mensagem_erro ?? '';
?>
        <div class="modal-overlay<?= !empty($mensagem_erro) ? ' active' : '' ?>" id="modalClientes">
            <div class="modal modal--xxl">

                <div class="modal__header">
                    <div class="modal__header-icone">
                        <img src="<?= BASE_URL ?>app/assets/img/icone_empresa.svg" alt="Empresa" />
                    </div>
                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Cadastro de Empresa</h2>
                        <p class="modal__subtitulo">Informações da empresa contratante e do responsável técnico</p>
                    </div>
                    <button type="button" class="modal__fechar" data-modal-close>&times;</button>
                </div>

                <form action="" method="post">
                    <div class="modal__body">

                        <div class="modal-secao">
                            <div class="modal-secao__titulo">
                                <i class="fa-solid fa-file-lines modal-secao__titulo-icone"></i>
                                <h3>Dados da Empresa</h3>
                            </div>
                            <div class="modal-grade modal-grade--4">

                                <div class="campo">
                                    <label class="campo__label">Nome da Empresa</label>
                                    <input type="text" name="nome_fantasia" class="campo__input" placeholder="Digite o nome da Empresa" />
                                </div>
                            </div>

                            <div class="modal-grade modal-grade--4">
                                <div class="campo">
                                    <label class="campo__label">Razão Social</label>
                                    <input type="text" name="razao_social" class="campo__input" placeholder="Digite a razão social" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Telefone</label>
                                    <div class="campo__input-wrapper">
                                        <i class="fa fa-phone campo__input-icone"></i>
                                        <input type="text" name="telefone" class="campo__input campo__input--com-icone-esq" placeholder="(11) 9999-9999" oninput="mascararTelefone(this)" />
                                    </div>
                                </div>
                                <div class="campo">
                                    <label class="campo__label">E-mail</label>
                                    <div class="campo__input-wrapper">
                                        <i class="fa-solid fa-envelope campo__input-icone"></i>
                                        <input type="email" name="email_contato" class="campo__input campo__input--com-icone-esq" placeholder="email@.com" />
                                    </div>
                                </div>
                                <div class="campo">
                                    <label class="campo__label">CNPJ</label>
                                    <input type="text" name="cnpj" class="campo__input" placeholder="23.456.789/0001-01" oninput="mascararCNPJ(this)" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">CEP</label>
                                    <input type="text" name="cep" id="cep" class="campo__input" placeholder="12345-678" onblur="buscarCep()" oninput="mascararCEP(this)"  />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Endereço</label>
                                    <input type="text" name="rua" id="endereco" class="campo__input" placeholder="Digite o endereço" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Número</label>
                                    <input type="text" name="numero" class="campo__input" placeholder="0000" oninput="apenasNumeros(this)" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Complemento</label>
                                    <input type="text" name="complemento" class="campo__input" placeholder="Complemento" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Bairro</label>
                                    <input type="text" name="bairro" id="bairro" class="campo__input" placeholder="Digite o bairro" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Cidade</label>
                                    <input type="text" name="cidade" id="cidade" class="campo__input" placeholder="Selecione uma Cidade" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Estado</label>
                                    <input type="text" name="estado" id="estado" class="campo__input" placeholder="Selecione um Estado" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">País</label>
                                    <input type="text" name="pais" id="pais" class="campo__input" value="Brasil" required />
                                </div>
                            </div>
                        </div>

                        <div class="modal-secao">
                            <div class="modal-secao__titulo">
                                <i class="fa-solid fa-user modal-secao__titulo-icone"></i>
                                <h3>Dados do Responsável</h3>
                            </div>

                            <div class="modal-grade modal-grade--4">
                                <div class="campo">
                                    <label class="campo__label">Nome do Responsável</label>
                                    <input type="text" name="responsavel" class="campo__input" placeholder="Digite o nome" />
                                </div>
                                <div class="campo">
                                    <label class="campo__label">Telefone</label>
                                    <div class="campo__input-wrapper">
                                        <i class="fa fa-phone campo__input-icone"></i>
                                        <input type="text" name="telefone_responsavel" class="campo__input campo__input--com-icone-esq" placeholder="(11)99999-9999" oninput="mascararTelefone(this)" />
                                    </div>
                                </div>
                                <div class="campo">
                                    <label class="campo__label">E-mail</label>
                                    <div class="campo__input-wrapper">
                                        <i class="fa-solid fa-envelope campo__input-icone"></i>
                                        <input type="email" name="email_responsavel" class="campo__input campo__input--com-icone-esq" placeholder="email@.com" />
                                    </div>
                                </div>
                                <div class="campo">
                                    <label class="campo__label">CPF</label>
                                    <input type="text" name="cpf_responsavel" class="campo__input" placeholder="000.000.000-00" oninput="mascararCPF(this)" />
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal__footer modal__footer-cliente"> 
                        <?php if (!empty($mensagem_erro)): ?>
                            <p class="campo__mensagem-erro" style="margin-right: auto;">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <?php echo $mensagem_erro; ?>
                            </p>
                        <?php endif; ?>
                        <button type="button" class="btn-cancelar" data-modal-close="modalClientes">CANCELAR</button>
                        <button type="submit" class="btn-botao-verde">SALVAR</button>
                    </div>

                </form>

            </div>
        </div>
