<div class="modal__body stepper-conteudo" id="cp-passo-2">
    <div class="modal-secao">
        <h3 class="modal-secao__titulo">
            <i class="fa-solid fa-users modal-secao__titulo-icone"></i>
            Alocar Equipe
        </h3>
        <div class="modal-grade">
            <!-- Líder Técnico (seleção única) -->
            <div class="campo" id="campo-lider">
                <label class="campo__label campo__label--obrigatorio" for="cp-lider-busca">Líder Técnico</label>
                <div class="campo__combobox">
                    <div class="campo__combobox-linha">
                        <div class="campo__combobox-campo">
                            <input type="text"
                                id="cp-lider-busca"
                                class="campo__input campo__combobox-input"
                                placeholder="Selecione um especialista..."
                                role="combobox"
                                aria-autocomplete="list"
                                aria-controls="cp-lider-lista"
                                aria-expanded="false"
                                autocomplete="off">
                            <button type="button" class="campo__combobox-alternar" aria-label="Mostrar especialistas">
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                            <div id="cp-lider-lista" class="campo__combobox-lista" role="listbox" hidden></div>
                        </div>
                    </div>
                </div>
                <span class="campo__mensagem-erro" id="erro-lider">Selecione o líder técnico.</span>
            </div>
            <!-- Especialistas/Analistas (multi) -->
            <div class="campo">
                <label class="campo__label" for="cp-analista-busca">Especialista</label>
                <div class="campo__multi">
                    <div class="campo__multi-busca">
                        <input type="text"
                            id="cp-analista-busca"
                            class="campo__multi-input"
                            placeholder="Pesquise e adicione um especialista...">
                        <button type="button" id="cp-analista-add" class="campo__botao-adicionar" aria-label="Adicionar especialista">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                    <div class="campo__multi-chips" id="cp-analistas-chips" aria-label="Analistas selecionados"></div>
                </div>
            </div>
        </div>
    </div>
</div>
