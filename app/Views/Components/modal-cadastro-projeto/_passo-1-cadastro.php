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
        </div>
    </div>
    <!-- Seção: Informações do Pentest -->
    <div class="modal-secao">
        <h3 class="modal-secao__titulo">
            <i class="fa-solid fa-shield-halved modal-secao__titulo-icone"></i>
            Informações do Pentest
        </h3>
        <div class="modal-grade">
            <!-- Tipo de Pentest (combobox multi-seleção) -->
            <div class="campo" id="campo-tipo-pentest">
                <label class="campo__label campo__label--obrigatorio" for="cp-tipo-busca">Nome do Pentest</label>
                <div class="campo__multi">
                    <div class="campo__multi-busca">
                        <div class="campo__combobox" style="flex:1">
                            <div class="campo__combobox-linha">
                                <div class="campo__combobox-campo">
                                    <input type="text"
                                        id="cp-tipo-busca"
                                        class="campo__input campo__combobox-input"
                                        placeholder="Mobile Application"
                                        role="combobox"
                                        aria-autocomplete="list"
                                        aria-controls="cp-tipo-lista"
                                        aria-expanded="false"
                                        autocomplete="off">
                                    <button type="button" class="campo__combobox-alternar" aria-label="Mostrar tipos de pentest">
                                        <i class="fa-solid fa-chevron-down"></i>
                                    </button>
                                    <div id="cp-tipo-lista" class="campo__combobox-lista" role="listbox" hidden></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="campo__multi-chips" id="cp-tipos-chips" aria-label="Tipos de pentest selecionados"></div>
                </div>
                <span class="campo__mensagem-erro" id="erro-tipo-pentest">Selecione ao menos um tipo de pentest.</span>
            </div>
            <!-- Horas de execução (readonly) -->
            <div class="campo">
                <label class="campo__label" for="cp-horas-execucao">Horas de execução de Pentest</label>
                <input type="text"
                    id="cp-horas-execucao"
                    class="campo__input campo__input--readonly"
                    placeholder="80 Horas"
                    readonly>
            </div>
            <!-- Modalidade -->
            <div class="campo" id="campo-modalidade">
                <label class="campo__label campo__label--obrigatorio" for="cp-modalidade">Tipo de Pentest</label>
                <div class="campo__select-wrapper">
                    <select id="cp-modalidade" name="modalidade" class="campo__select">
                        <option value="" disabled selected>Selecione a modalidade...</option>
                        <option value="BLACK BOX">Black Box</option>
                        <option value="GRAY BOX">Gray Box</option>
                        <option value="WHITE BOX">White Box</option>
                    </select>
                    <i class="fa-solid fa-chevron-down campo__select-seta"></i>
                </div>
                <span class="campo__mensagem-erro" id="erro-modalidade">Selecione a modalidade.</span>
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
            <!-- Horas Contratadas -->
            <div class="campo" id="campo-horas-contratadas">
                <label class="campo__label campo__label--obrigatorio" for="cp-horas-contratadas">Horas Contratadas</label>
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
</div>
