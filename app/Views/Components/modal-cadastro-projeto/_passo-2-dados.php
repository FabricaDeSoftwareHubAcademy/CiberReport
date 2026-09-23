<div class="modal__body stepper-conteudo" id="cp-passo-1">
    <div class="modal-grade">
        <!-- Escopo (esquerda) -->
        <div class="campo">
            <h3 class="modal-secao__titulo" style="margin-bottom: var(--espaco-sm)">
                <i class="fa-solid fa-file-lines modal-secao__titulo-icone"></i>
                Escopo do Projeto
            </h3>
            <label class="campo__label campo__label--obrigatorio" for="cp-escopo">Resumo do objeto do contrato</label>
            <textarea id="cp-escopo"
                name="escopo"
                class="campo__textarea"
                rows="6"
                placeholder="Descreva o objetivo e detalhe do projeto..."></textarea>
            <span class="campo__mensagem-erro" id="erro-escopo">O escopo é obrigatório.</span>
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
            <h3 class="modal-secao__titulo" style="margin-bottom: var(--espaco-sm)">
                <i class="fa-solid fa-paperclip modal-secao__titulo-icone"></i>
                Anexo de contrato
            </h3>
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
