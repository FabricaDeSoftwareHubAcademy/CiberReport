<div class="modal__body stepper-conteudo ativo" id="cp-passo-0">
    <!-- Seção: Informações do Cliente -->
    <div class="modal-secao">
        <h3 class="modal-secao__titulo">
            <i class="fa-solid fa-building modal-secao__titulo-icone"></i>
            Informações do Cliente
        </h3>
        <div class="modal-grade">
            <!-- Cliente (combobox) -->
            <div class="campo" id="campo-cliente">
                <label class="campo__label campo__label--obrigatorio" for="cp-cliente-busca">Cliente</label>
                <div class="campo__combobox">
                    <div class="campo__combobox-linha">
                        <div class="campo__combobox-campo">
                            <input type="text"
                                id="cp-cliente-busca"
                                class="campo__input campo__combobox-input"
                                placeholder="Selecione um cliente"
                                role="combobox"
                                aria-autocomplete="list"
                                aria-controls="cp-cliente-lista"
                                aria-expanded="false"
                                autocomplete="off">
                            <button type="button" class="campo__combobox-alternar" aria-label="Mostrar clientes">
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                            <button type="button" id="cp-cliente-limpar" class="campo__combobox-limpar" aria-label="Limpar cliente selecionado" hidden>
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            <div id="cp-cliente-lista" class="campo__combobox-lista" role="listbox" hidden></div>
                        </div>
                    </div>
                </div>
                <span class="campo__mensagem-erro" id="erro-cliente">Selecione um cliente.</span>
            </div>
            <!-- Nome do Projeto -->
            <div class="campo" id="campo-nome-projeto">
                <label class="campo__label campo__label--obrigatorio" for="cp-nome-projeto">Nome do Projeto</label>
                <input type="text"
                    id="cp-nome-projeto"
                    name="nome"
                    class="campo__input"
                    placeholder="Ex: Pentest WEB - Gazin"
                    maxlength="80">
                <span class="campo__mensagem-erro" id="erro-nome-projeto">Informe o nome do projeto.</span>
            </div>
            <!-- Nível de Sigilo -->
            <div class="campo" id="campo-sigilo">
                <label class="campo__label campo__label--obrigatorio" for="cp-sigilo">Nível de Sigilo (Confidencialidade)</label>
                <div class="campo__select-wrapper">
                    <select id="cp-sigilo" name="nivel_sigilo" class="campo__select">
                        <option value="" disabled selected>Selecione...</option>
                        <option value="INTERNO">Interno</option>
                        <option value="EXTERNO">Externo</option>
                    </select>
                    <i class="fa-solid fa-chevron-down campo__select-seta"></i>
                </div>
                <span class="campo__mensagem-erro" id="erro-sigilo">Selecione o nível de sigilo.</span>
            </div>
        </div>
    </div>
    <!-- Seção: Cronograma -->
    <div class="modal-secao">
        <h3 class="modal-secao__titulo">
            <i class="fa-solid fa-calendar-days modal-secao__titulo-icone"></i>
            Cronograma
        </h3>
        <div class="modal-grade modal-grade--3">
            <!-- Data de Início -->
            <div class="campo">
                <label class="campo__label" for="cp-data-inicio">Data de Início</label>
                <div class="campo__input-wrapper">
                    <input type="date" id="cp-data-inicio" name="data_inicio" class="campo__input campo__input--data">
                    <button type="button" class="campo__input-botao-icone"
                        onclick="document.getElementById('cp-data-inicio').showPicker()"
                        aria-label="Abrir calendário">
                        <i class="fa-solid fa-calendar"></i>
                    </button>
                </div>
            </div>
            <!-- Data de Término -->
            <div class="campo">
                <label class="campo__label" for="cp-data-fim">Data de Término</label>
                <div class="campo__input-wrapper">
                    <input type="date" id="cp-data-fim" name="data_fim_prevista" class="campo__input campo__input--data">
                    <button type="button" class="campo__input-botao-icone"
                        onclick="document.getElementById('cp-data-fim').showPicker()"
                        aria-label="Abrir calendário">
                        <i class="fa-solid fa-calendar"></i>
                    </button>
                </div>
            </div>
            <!-- Horas totais contratadas -->
            <div class="campo" id="campo-horas-contratadas">
                <label class="campo__label campo__label--obrigatorio" for="cp-horas-contratadas">Horas totais contratadas</label>
                <div class="campo__input-wrapper">
                    <input type="text"
                        id="cp-horas-contratadas"
                        name="horas_contratadas"
                        class="campo__input campo__input--hora"
                        placeholder="hh:mm:ss"
                        maxlength="8"
                        inputmode="numeric">
                    <button type="button" class="campo__input-botao-icone" aria-label="Campo de hora">
                        <i class="fa-solid fa-clock"></i>
                    </button>
                </div>
                <span class="campo__mensagem-erro" id="erro-horas">Informe as horas contratadas (ex: 80:00:00).</span>
            </div>
        </div>
    </div>
    <!-- Seção: Escopo e Contrato -->
    <div class="modal-secao">
        <h3 class="modal-secao__titulo">
            <i class="fa-solid fa-file-lines modal-secao__titulo-icone"></i>
            Escopo e Contrato
        </h3>
        <div class="modal-grade">
            <!-- Resumo do projeto contratado (esquerda) -->
            <div class="campo" id="campo-escopo">
                <label class="campo__label campo__label--obrigatorio" for="cp-escopo">Resumo do projeto contratado</label>
                <textarea id="cp-escopo"
                    name="escopo"
                    class="campo__textarea"
                    rows="6"
                    placeholder="Descreva o objetivo e detalhe do projeto..."></textarea>
                <span class="campo__mensagem-erro" id="erro-escopo">O resumo do projeto contratado é obrigatório.</span>
                <!-- Alvos/IPs/URL/Domínio -->
                <div class="campo" style="margin-top: var(--espaco-md)">
                    <label class="campo__label" for="cp-alvo-input">Ativos/Alvos/IPs/URL/Domínio</label>
                    <div class="campo__multi">
                        <div class="campo__multi-busca">
                            <input type="text"
                                id="cp-alvo-input"
                                class="campo__multi-input"
                                placeholder="Escreva um ativo e adicione...">
                            <button type="button" id="cp-alvo-add" class="campo__botao-adicionar" aria-label="Adicionar ativo">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                        <div class="campo__multi-chips" id="cp-alvos-chips" aria-label="Alvos adicionados"></div>
                    </div>
                </div>
            </div>
            <!-- Contrato + Restrições (direita) -->
            <div class="campo">
                <label class="campo__label" style="display:block; margin-bottom: var(--espaco-xs)">Anexo do contrato</label>
                <!-- Dropzone -->
                <div class="campo__dropzone" id="cp-dropzone" role="button" tabindex="0" aria-label="Arraste ou selecione o contrato PDF">
                    <i class="fa-solid fa-cloud-arrow-down campo__dropzone-icone"></i>
                    <p class="campo__dropzone-titulo" id="cp-dropzone-texto">Arraste e solte seu arquivo aqui</p>
                    <span class="campo__dropzone-ou">ou</span>
                    <label class="campo__dropzone-botao" for="cp-contrato-input" style="cursor:pointer">Selecionar Arquivo</label>
                    <input type="file" id="cp-contrato-input" name="contrato" class="campo__dropzone-input" accept="application/pdf">
                </div>
                <!-- Restrições -->
                <div class="campo" style="margin-top: var(--espaco-md)">
                    <label class="campo__label" for="cp-restricao">Restrições</label>
                    <textarea id="cp-restricao"
                        name="restricao"
                        class="campo__textarea"
                        rows="5"
                        placeholder="Descreva as restrições..."></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
