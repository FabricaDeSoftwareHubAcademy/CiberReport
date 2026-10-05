<div class="modal__body stepper-conteudo" id="cp-passo-2">
    <!-- Cadastro -->
    <div class="revisao-secao">
        <div class="revisao-secao__cabecalho">
            <i class="fa-solid fa-building" style="color: var(--cor-azul-primaria)"></i>
            <span class="revisao-secao__titulo">Cliente</span>
        </div>
        <div class="revisao-secao__corpo">
            <div class="revisao-campo">
                <span class="revisao-campo__rotulo">Cliente</span>
                <span class="revisao-campo__valor" id="rev-cliente">—</span>
            </div>
            <div class="revisao-campo">
                <span class="revisao-campo__rotulo">Nome do Projeto</span>
                <span class="revisao-campo__valor" id="rev-nome-projeto">—</span>
            </div>
            <div class="revisao-campo">
                <span class="revisao-campo__rotulo">Nível de Sigilo</span>
                <span class="revisao-campo__valor" id="rev-sigilo">—</span>
            </div>
        </div>
    </div>
    <!-- Escopo do Projeto -->
    <div class="revisao-secao">
        <div class="revisao-secao__cabecalho">
            <i class="fa-solid fa-file-lines" style="color: var(--cor-azul-primaria)"></i>
            <span class="revisao-secao__titulo">Escopo do Projeto</span>
        </div>
        <div class="revisao-secao__corpo">
            <div class="revisao-campo revisao-campo--full">
                <span class="revisao-campo__rotulo">Resumo do projeto contratado</span>
                <span class="revisao-campo__valor" id="rev-escopo">—</span>
            </div>
            <div class="revisao-campo revisao-campo--full">
                <span class="revisao-campo__rotulo">Alvos/IPs/Domínios</span>
                <span class="revisao-campo__valor" id="rev-alvos">—</span>
            </div>
            <div class="revisao-campo revisao-campo--full">
                <span class="revisao-campo__rotulo">Restrições</span>
                <span class="revisao-campo__valor" id="rev-restricoes">—</span>
            </div>
        </div>
    </div>
    <!-- Cronograma -->
    <div class="revisao-secao">
        <div class="revisao-secao__cabecalho">
            <i class="fa-solid fa-calendar-days" style="color: var(--cor-azul-primaria)"></i>
            <span class="revisao-secao__titulo">Cronograma</span>
        </div>
        <div class="revisao-secao__corpo">
            <div class="revisao-campo">
                <span class="revisao-campo__rotulo">Data de início</span>
                <span class="revisao-campo__valor" id="rev-data-inicio">—</span>
            </div>
            <div class="revisao-campo">
                <span class="revisao-campo__rotulo">Data de Término</span>
                <span class="revisao-campo__valor" id="rev-data-fim">—</span>
            </div>
            <div class="revisao-campo">
                <span class="revisao-campo__rotulo">Horas totais Contratadas</span>
                <span class="revisao-campo__valor" id="rev-horas">—</span>
            </div>
        </div>
    </div>

    <!-- Um bloco de revisão por pentest, montado dinamicamente pelo JS -->
    <div id="rev-pentests-lista"></div>
</div>
