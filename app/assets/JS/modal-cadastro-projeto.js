/**
 * modal-cadastro-projeto.js
 *
 * Responsabilidades:
 *  1. Stepper: avançar/voltar entre os 2 passos, atualizar marcadores visuais.
 *  2. Validação por passo: só avança se os campos obrigatórios estiverem preenchidos.
 *  3. Combobox de Cliente: filtra a lista de empresas embutida via data-attribute.
 *  4. Chips de Alvos: input livre + botão adicionar, remover chip.
 *  5. Dropzone: drag & drop + click, exibe nome do arquivo selecionado.
 *  6. Passo 2: N blocos repetíveis de Pentest (Adicionar/Remover), cada um com
 *     seu próprio tipo de pentest, modalidade (derivada), abordagem,
 *     metodologia (frameworks), escopo, ambiente, equipe (líder + analistas)
 *     e referência.
 *  7. Submit: injeta campos dinâmicos (alvos[], pentests[idx][...]) antes de enviar.
 *  8. Reset: ao fechar o modal, limpa todo o estado.
 */

document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------------------
    // 0. REFERÊNCIAS GLOBAIS
    // -------------------------------------------------------------------------
    const overlay = document.getElementById('modal-cadastro-projeto');
    if (!overlay) return; // Modal não existe nesta página

    // Dados do backend (serializados no data-attribute pelo PHP)
    const empresas     = JSON.parse(overlay.dataset.empresas     || '[]');
    const tiposPentest = JSON.parse(overlay.dataset.tiposPentest || '[]');
    const frameworks   = JSON.parse(overlay.dataset.frameworks   || '[]');
    const usuarios     = JSON.parse(overlay.dataset.usuarios     || '[]');

    // Estado do módulo
    let passoAtual     = 0;
    const TOTAL_PASSOS = 3;
    let modoEdicaoOuVisualizacao = false; // true quando o modal foi aberto via Editar/Visualizar
    let somenteLeitura = false; // true só no modo Visualizar (Editar continua editável)

    // IDs selecionados (passo 1)
    let clienteSelecionado = { id: null, nome: '' };
    const alvos            = []; // strings

    // Blocos de Pentest (passo 2) — cada item é o estado de um bloco.
    const blocosPentest = [];
    let proximoUidBloco = 0;

    // Referências de elementos do stepper
    const stepperItens = overlay.querySelectorAll('.cad-projeto-stepper__item');
    const passos       = overlay.querySelectorAll('.stepper-conteudo');
    const btnVoltar    = document.getElementById('cp-btn-voltar');
    const btnAvancar   = document.getElementById('cp-btn-avancar');
    const btnSalvar    = document.getElementById('cp-btn-salvar');

    // -------------------------------------------------------------------------
    // 1. STEPPER — navegar entre passos
    // -------------------------------------------------------------------------
    function irParaPasso(novoPasso) {
        passos[passoAtual].classList.remove('ativo');
        stepperItens[passoAtual].classList.remove('cad-projeto-stepper__item--ativo');

        if (modoEdicaoOuVisualizacao) {
            stepperItens[passoAtual].classList.add('cad-projeto-stepper__item--concluido');
            stepperItens[novoPasso].classList.remove('cad-projeto-stepper__item--concluido');
        } else if (novoPasso > passoAtual) {
            stepperItens[passoAtual].classList.add('cad-projeto-stepper__item--concluido');
        } else {
            stepperItens[novoPasso].classList.remove('cad-projeto-stepper__item--concluido');
        }

        passoAtual = novoPasso;

        passos[passoAtual].classList.add('ativo');
        stepperItens[passoAtual].classList.add('cad-projeto-stepper__item--ativo');

        if (passoAtual === 2) preencherRevisao();

        atualizarBotoes();
        passos[passoAtual].scrollTop = 0;
    }

    function atualizarBotoes() {
        btnVoltar.style.display = passoAtual === 0 ? 'none' : '';
        btnAvancar.style.display = passoAtual === TOTAL_PASSOS - 1 ? 'none' : '';

        if (somenteLeitura) {
            btnSalvar.style.display = 'none';
        } else if (modoEdicaoOuVisualizacao) {
            btnSalvar.style.display = '';
        } else {
            btnSalvar.style.display = passoAtual === TOTAL_PASSOS - 1 ? '' : 'none';
        }
    }

    btnAvancar.addEventListener('click', () => {
        if (!validarPasso(passoAtual)) return;
        if (passoAtual < TOTAL_PASSOS - 1) irParaPasso(passoAtual + 1);
    });

    btnVoltar.addEventListener('click', () => {
        if (passoAtual > 0) irParaPasso(passoAtual - 1);
    });

    stepperItens.forEach((item, idx) => {
        item.style.cursor = 'pointer';
        item.addEventListener('click', () => {
            if (idx === passoAtual) return;

            if (modoEdicaoOuVisualizacao) {
                irParaPasso(idx);
                return;
            }

            if (idx < passoAtual) {
                irParaPasso(idx);
                return;
            }

            for (let passo = passoAtual; passo < idx; passo++) {
                if (!validarPasso(passo)) {
                    irParaPasso(passo);
                    return;
                }
            }
            irParaPasso(idx);
        });
    });

    // -------------------------------------------------------------------------
    // 2. VALIDAÇÃO POR PASSO
    // -------------------------------------------------------------------------
    function marcarErroCampo(campoId, condicaoErro) {
        const campo = document.getElementById(campoId)?.closest('.campo');
        if (!campo) return;
        campo.classList.toggle('campo--erro', !!condicaoErro);
    }

    function validarPasso(passo) {
        let temErro = false;

        if (passo === 0) {
            if (!clienteSelecionado.id) {
                document.getElementById('campo-cliente')?.classList.add('campo--erro');
                temErro = true;
            } else {
                document.getElementById('campo-cliente')?.classList.remove('campo--erro');
            }

            const nomeProj = document.getElementById('cp-nome-projeto');
            const nomeInvalido = !nomeProj.value.trim();
            marcarErroCampo('cp-nome-projeto', nomeInvalido);
            temErro = temErro || nomeInvalido;

            const sigilo = document.getElementById('cp-sigilo');
            const sigiloInvalido = !sigilo.value;
            marcarErroCampo('cp-sigilo', sigiloInvalido);
            temErro = temErro || sigiloInvalido;

            const escopo = document.getElementById('cp-escopo');
            const escopoInvalido = !escopo.value.trim();
            marcarErroCampo('cp-escopo', escopoInvalido);
            temErro = temErro || escopoInvalido;

            const horas = document.getElementById('cp-horas-contratadas');
            const horasInvalidas = !horas.value.trim() || !/^\d{1,3}:\d{2}:\d{2}$/.test(horas.value.trim());
            marcarErroCampo('cp-horas-contratadas', horasInvalidas);
            temErro = temErro || horasInvalidas;
        }

        if (passo === 1) {
            const semBlocos = blocosPentest.length === 0;
            document.getElementById('campo-pentests')?.classList.toggle('campo--erro', semBlocos);
            temErro = temErro || semBlocos;

            blocosPentest.forEach(bloco => temErro = !validarBlocoPentest(bloco) || temErro);
        }

        return !temErro;
    }

    function validarBlocoPentest(bloco) {
        let ok = true;

        const tipoInvalido = !bloco.tipoPentestId;
        bloco.raiz.querySelector('[data-campo="tipo"]').classList.toggle('campo--erro', tipoInvalido);
        ok = ok && !tipoInvalido;

        const horasEl = bloco.raiz.querySelector('[data-campo="horas"] input');
        const horasInvalidas = !horasEl.value || parseFloat(horasEl.value) <= 0;
        bloco.raiz.querySelector('[data-campo="horas"]').classList.toggle('campo--erro', horasInvalidas);
        ok = ok && !horasInvalidas;

        const abordagemEl = bloco.raiz.querySelector('[data-campo="abordagem"] select');
        const abordagemInvalida = !abordagemEl.value;
        bloco.raiz.querySelector('[data-campo="abordagem"]').classList.toggle('campo--erro', abordagemInvalida);
        ok = ok && !abordagemInvalida;

        const ambienteEl = bloco.raiz.querySelector('[data-campo="ambiente"] select');
        const ambienteInvalido = !ambienteEl.value;
        bloco.raiz.querySelector('[data-campo="ambiente"]').classList.toggle('campo--erro', ambienteInvalido);
        ok = ok && !ambienteInvalido;

        const escopoEl = bloco.raiz.querySelector('[data-campo="escopo"] textarea');
        const escopoInvalido = !escopoEl.value.trim();
        bloco.raiz.querySelector('[data-campo="escopo"]').classList.toggle('campo--erro', escopoInvalido);
        ok = ok && !escopoInvalido;

        const metodologiaInvalida = bloco.frameworksSelecionados.length === 0;
        bloco.raiz.querySelector('[data-campo="metodologia"]').classList.toggle('campo--erro', metodologiaInvalida);
        ok = ok && !metodologiaInvalida;

        const semLider = !bloco.equipe.some(m => m.lider);
        bloco.raiz.querySelector('[data-campo="analistas"]').classList.toggle('campo--erro', semLider);
        ok = ok && !semLider;

        return ok;
    }

    // -------------------------------------------------------------------------
    // 3. COMBOBOX GENÉRICO — fábrica reutilizada por todos os comboboxes
    // -------------------------------------------------------------------------
    function criarCombobox(inputEl, listaEl, btnToggle, itens, onSelect) {
        let itemFocado = -1;
        const opcoes = [];

        function renderizar(filtro = '') {
            listaEl.innerHTML = '';
            opcoes.length = 0;
            itemFocado = -1;

            const filtroLower = filtro.toLowerCase();
            const resultados = itens.filter(i => i.label.toLowerCase().includes(filtroLower));

            if (resultados.length === 0) {
                listaEl.innerHTML = '<p class="campo__combobox-vazio">Nenhum resultado.</p>';
                return;
            }

            resultados.forEach((item) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'campo__combobox-opcao';
                btn.role = 'option';
                btn.dataset.id = item.id;
                btn.textContent = item.label;
                btn.addEventListener('click', () => selecionar(item));
                listaEl.appendChild(btn);
                opcoes.push(btn);
            });
        }

        function abrir() {
            renderizar(inputEl.value);
            listaEl.removeAttribute('hidden');
            inputEl.setAttribute('aria-expanded', 'true');
        }

        function fechar() {
            listaEl.setAttribute('hidden', '');
            inputEl.setAttribute('aria-expanded', 'false');
            itemFocado = -1;
        }

        function selecionar(item) {
            onSelect(item);
            fechar();
        }

        inputEl.addEventListener('input', () => abrir());
        inputEl.addEventListener('focus', () => abrir());

        btnToggle.addEventListener('click', () => {
            if (listaEl.hasAttribute('hidden')) {
                abrir();
                inputEl.focus();
            } else {
                fechar();
            }
        });

        inputEl.addEventListener('keydown', (e) => {
            if (listaEl.hasAttribute('hidden')) return;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                itemFocado = Math.min(itemFocado + 1, opcoes.length - 1);
                opcoes[itemFocado]?.focus();
            } else if (e.key === 'Escape') {
                fechar();
            }
        });

        listaEl.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                itemFocado = Math.min(itemFocado + 1, opcoes.length - 1);
                opcoes[itemFocado]?.focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                itemFocado = Math.max(itemFocado - 1, 0);
                if (itemFocado === 0) inputEl.focus();
                else opcoes[itemFocado]?.focus();
            } else if (e.key === 'Escape') {
                fechar();
                inputEl.focus();
            }
        });

        document.addEventListener('click', (e) => {
            if (!inputEl.closest('.campo__combobox-campo').contains(e.target)) fechar();
        });

        return { abrir, fechar, renderizar };
    }

    // -------------------------------------------------------------------------
    // 4. COMBOBOX DE CLIENTE
    // -------------------------------------------------------------------------
    const clienteInput  = document.getElementById('cp-cliente-busca');
    const clienteLista  = document.getElementById('cp-cliente-lista');
    const clienteToggle = clienteInput?.nextElementSibling;
    const clienteLimpar = document.getElementById('cp-cliente-limpar');

    function atualizarLimparCliente() {
        if (!clienteLimpar) return;
        const temSelecao = !!clienteSelecionado.id;
        clienteLimpar.hidden = !temSelecao;
        clienteInput?.closest('.campo__combobox-campo')?.classList.toggle('campo__combobox-campo--com-limpar', temSelecao);
    }

    clienteLimpar?.addEventListener('click', () => {
        clienteSelecionado = { id: null, nome: '' };
        clienteInput.value = '';
        document.getElementById('cp-empresa-id').value = '';
        atualizarLimparCliente();
        clienteInput.focus();
    });

    if (clienteInput && clienteLista && clienteToggle) {
        const itensCliente = empresas.map(e => ({
            id:    e.id,
            label: e.nome_fantasia || e.razao_social || `Empresa #${e.id}`,
        }));

        criarCombobox(clienteInput, clienteLista, clienteToggle, itensCliente, (item) => {
            clienteSelecionado = { id: item.id, nome: item.label };
            clienteInput.value = item.label;
            document.getElementById('cp-empresa-id').value = item.id;
            document.getElementById('campo-cliente')?.classList.remove('campo--erro');
            atualizarLimparCliente();
        });
    }

    // -------------------------------------------------------------------------
    // 5. CHIPS DE ALVOS
    // -------------------------------------------------------------------------
    const alvoInput  = document.getElementById('cp-alvo-input');
    const alvoAdd    = document.getElementById('cp-alvo-add');
    const alvosChips = document.getElementById('cp-alvos-chips');

    function adicionarAlvo() {
        const valor = alvoInput?.value.trim();
        if (!valor || alvos.includes(valor)) {
            alvoInput.value = '';
            return;
        }
        alvos.push(valor);
        alvoInput.value = '';
        renderizarChipsAlvos();
    }

    alvoAdd?.addEventListener('click', adicionarAlvo);
    alvoInput?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); adicionarAlvo(); }
    });

    function renderizarChipsAlvos() {
        if (!alvosChips) return;
        alvosChips.innerHTML = '';
        alvos.forEach((alvo, idx) => {
            alvosChips.appendChild(criarChip(alvo, () => {
                alvos.splice(idx, 1);
                renderizarChipsAlvos();
            }));
        });
    }

    // -------------------------------------------------------------------------
    // 6. DROPZONE
    // -------------------------------------------------------------------------
    const dropzone      = document.getElementById('cp-dropzone');
    const contratoInput = document.getElementById('cp-contrato-input');
    const dropzoneTxt    = document.getElementById('cp-dropzone-texto');

    function atualizarDropzone(arquivo) {
        if (!arquivo) return;
        if (arquivo.type !== 'application/pdf') {
            dropzoneTxt.textContent = '⚠ Apenas arquivos PDF são permitidos.';
            contratoInput.value = '';
            return;
        }
        if (arquivo.size > 5 * 1024 * 1024) {
            dropzoneTxt.textContent = '⚠ Arquivo excede 5MB.';
            contratoInput.value = '';
            return;
        }
        dropzoneTxt.textContent = `✔ ${arquivo.name}`;
        dropzone?.classList.add('campo__dropzone--selecionado');
    }

    contratoInput?.addEventListener('change', () => atualizarDropzone(contratoInput.files[0]));

    dropzone?.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = 'var(--cor-azul-destaques)';
    });

    dropzone?.addEventListener('dragleave', () => { dropzone.style.borderColor = ''; });

    dropzone?.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '';
        const arquivo = e.dataTransfer.files[0];
        if (arquivo) {
            const dt = new DataTransfer();
            dt.items.add(arquivo);
            contratoInput.files = dt.files;
            atualizarDropzone(arquivo);
        }
    });

    // -------------------------------------------------------------------------
    // 7. BLOCOS DE PENTEST (passo 2)
    // -------------------------------------------------------------------------
    const listaPentests = document.getElementById('cp-pentests-lista');
    const btnAdicionarPentest = document.getElementById('cp-btn-adicionar-pentest');

    const ABORDAGENS = [
        { value: 'BLACK BOX', label: 'Black Box' },
        { value: 'GRAY BOX',  label: 'Gray Box' },
        { value: 'WHITE BOX', label: 'White Box' },
    ];

    const AMBIENTES = [
        { value: 'DESENVOLVIMENTO', label: 'Desenvolvimento' },
        { value: 'HOMOLOGACAO',     label: 'Homologação' },
        { value: 'PRODUCAO',        label: 'Produção' },
    ];

    function montarOpcoesSelect(select, opcoes, placeholder) {
        select.innerHTML = '';
        const optPlaceholder = document.createElement('option');
        optPlaceholder.value = '';
        optPlaceholder.disabled = true;
        optPlaceholder.selected = true;
        optPlaceholder.textContent = placeholder;
        select.appendChild(optPlaceholder);

        opcoes.forEach(op => {
            const opt = document.createElement('option');
            opt.value = op.value;
            opt.textContent = op.label;
            select.appendChild(opt);
        });
    }

    function criarBlocoPentest() {
        const uid = proximoUidBloco++;

        const raiz = document.createElement('div');
        raiz.className = 'bloco-pentest';
        raiz.dataset.uid = String(uid);
        raiz.innerHTML = `
            <div class="bloco-pentest__cabecalho">
                <h3 class="bloco-pentest__titulo">
                    <i class="fa-solid fa-shield-halved bloco-pentest__titulo-icone"></i>
                    <span data-papel="numero">Pentest</span>
                </h3>
                <button type="button" class="bloco-pentest__remover" aria-label="Remover pentest">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
            <div class="modal-grade">
                <div class="campo" data-campo="tipo">
                    <label class="campo__label campo__label--obrigatorio">Tipo de Pentest</label>
                    <div class="campo__combobox">
                        <div class="campo__combobox-linha">
                            <div class="campo__combobox-campo">
                                <input type="text" class="campo__input campo__combobox-input"
                                    placeholder="Selecione o tipo de pentest" role="combobox"
                                    aria-autocomplete="list" aria-expanded="false" autocomplete="off">
                                <button type="button" class="campo__combobox-alternar" aria-label="Mostrar tipos de pentest">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                                <div class="campo__combobox-lista" role="listbox" hidden></div>
                            </div>
                        </div>
                    </div>
                    <span class="campo__mensagem-erro">Selecione o tipo de pentest.</span>
                </div>
                <div class="campo" data-campo="horas">
                    <label class="campo__label campo__label--obrigatorio">Horas de Pentest contratadas</label>
                    <input type="number" class="campo__input" min="0" step="0.5" placeholder="Ex: 40">
                    <span class="campo__mensagem-erro">Informe as horas contratadas para este pentest.</span>
                </div>
                <div class="campo">
                    <label class="campo__label">Modalidade</label>
                    <input type="text" class="campo__input campo__input--readonly" readonly placeholder="Derivada do tipo de pentest">
                </div>
                <div class="campo" data-campo="abordagem">
                    <label class="campo__label campo__label--obrigatorio">Abordagem</label>
                    <div class="campo__select-wrapper">
                        <select class="campo__select"></select>
                        <i class="fa-solid fa-chevron-down campo__select-seta"></i>
                    </div>
                    <span class="campo__mensagem-erro">Selecione a abordagem.</span>
                </div>
                <div class="campo" data-campo="ambiente">
                    <label class="campo__label campo__label--obrigatorio">Ambiente</label>
                    <div class="campo__select-wrapper">
                        <select class="campo__select"></select>
                        <i class="fa-solid fa-chevron-down campo__select-seta"></i>
                    </div>
                    <span class="campo__mensagem-erro">Selecione o ambiente.</span>
                </div>
                <div class="campo" data-campo="referencia">
                    <label class="campo__label">Referência</label>
                    <input type="text" class="campo__input" placeholder="Ex: CVE-2024-1234, CWE-79...">
                </div>
            </div>
            <div class="campo" data-campo="metodologia">
                <label class="campo__label campo__label--obrigatorio">Metodologia</label>
                <div class="campo__multi">
                    <div class="campo__multi-busca">
                        <div class="campo__combobox" style="flex:1">
                            <div class="campo__combobox-linha">
                                <div class="campo__combobox-campo">
                                    <input type="text" class="campo__input campo__combobox-input"
                                        placeholder="Pesquise e adicione uma metodologia..." role="combobox"
                                        aria-autocomplete="list" aria-expanded="false" autocomplete="off">
                                    <button type="button" class="campo__combobox-alternar" aria-label="Mostrar metodologias">
                                        <i class="fa-solid fa-chevron-down"></i>
                                    </button>
                                    <div class="campo__combobox-lista" role="listbox" hidden></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="campo__multi-chips" aria-label="Metodologias selecionadas"></div>
                </div>
                <span class="campo__mensagem-erro">Selecione ao menos uma metodologia.</span>
            </div>
            <div class="campo" data-campo="escopo">
                <label class="campo__label campo__label--obrigatorio">Escopo</label>
                <textarea class="campo__textarea" rows="4" placeholder="Descreva o escopo deste pentest..."></textarea>
                <span class="campo__mensagem-erro">O escopo deste pentest é obrigatório.</span>
            </div>
            <div class="campo" data-campo="analistas">
                <label class="campo__label campo__label--obrigatorio">Analista(s)</label>
                <div class="campo__multi">
                    <div class="campo__multi-busca">
                        <input type="text" class="campo__multi-input" placeholder="Pesquise e adicione um analista...">
                        <button type="button" class="campo__botao-adicionar" aria-label="Adicionar analista">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                    <div class="campo__multi-chips" aria-label="Equipe deste pentest"></div>
                </div>
                <span class="campo__mensagem-erro">Marque um analista como líder deste pentest clicando na estrela do chip.</span>
                <p class="campo__ajuda">Clique na <i class="fa-solid fa-star"></i> de um chip para marcá-lo como líder técnico deste pentest.</p>
            </div>
            <div class="campo">
                <label class="campo__label">Checklist</label>
                <p class="campo__ajuda">Vinculado automaticamente a partir do tipo de pentest selecionado.</p>
            </div>
        `;

        const bloco = {
            uid,
            raiz,
            tipoPentestId: null,
            frameworksSelecionados: [], // [{id, nome}]
            equipe: [], // [{id, nome, lider}]
        };

        // Tipo de pentest (combobox único)
        const tipoCampo    = raiz.querySelector('[data-campo="tipo"]');
        const tipoInput    = tipoCampo.querySelector('.campo__combobox-input');
        const tipoLista    = tipoCampo.querySelector('.campo__combobox-lista');
        const tipoToggle   = tipoCampo.querySelector('.campo__combobox-alternar');
        const modalidadeEl = raiz.querySelectorAll('.campo__input--readonly')[0];

        const itensTipo = tiposPentest.map(t => ({ id: t.id, label: t.nome, categoria_nome: t.categoria_nome }));
        criarCombobox(tipoInput, tipoLista, tipoToggle, itensTipo, (item) => {
            bloco.tipoPentestId = item.id;
            tipoInput.value = item.label;
            modalidadeEl.value = item.categoria_nome || '';
            tipoCampo.classList.remove('campo--erro');
        });

        // Abordagem / Ambiente
        const abordagemSelect = raiz.querySelector('[data-campo="abordagem"] select');
        montarOpcoesSelect(abordagemSelect, ABORDAGENS, 'Selecione a abordagem...');
        abordagemSelect.addEventListener('change', () => {
            raiz.querySelector('[data-campo="abordagem"]').classList.remove('campo--erro');
        });

        const ambienteSelect = raiz.querySelector('[data-campo="ambiente"] select');
        montarOpcoesSelect(ambienteSelect, AMBIENTES, 'Selecione o ambiente...');
        ambienteSelect.addEventListener('change', () => {
            raiz.querySelector('[data-campo="ambiente"]').classList.remove('campo--erro');
        });

        // Horas / Escopo: só tira o erro quando o usuário mexe
        raiz.querySelector('[data-campo="horas"] input').addEventListener('input', () => {
            raiz.querySelector('[data-campo="horas"]').classList.remove('campo--erro');
        });
        raiz.querySelector('[data-campo="escopo"] textarea').addEventListener('input', () => {
            raiz.querySelector('[data-campo="escopo"]').classList.remove('campo--erro');
        });

        // Metodologia (multi-select de frameworks com chips)
        const metodologiaCampo  = raiz.querySelector('[data-campo="metodologia"]');
        const metodologiaInput  = metodologiaCampo.querySelector('.campo__combobox-input');
        const metodologiaLista  = metodologiaCampo.querySelector('.campo__combobox-lista');
        const metodologiaToggle = metodologiaCampo.querySelector('.campo__combobox-alternar');
        const metodologiaChips  = metodologiaCampo.querySelector('.campo__multi-chips');

        function renderizarChipsMetodologia() {
            metodologiaChips.innerHTML = '';
            bloco.frameworksSelecionados.forEach((item, idx) => {
                metodologiaChips.appendChild(criarChip(item.nome, () => {
                    bloco.frameworksSelecionados.splice(idx, 1);
                    renderizarChipsMetodologia();
                }));
            });
        }

        const itensFramework = frameworks.map(f => ({ id: f.id, label: f.nome }));
        criarCombobox(metodologiaInput, metodologiaLista, metodologiaToggle, itensFramework, (item) => {
            if (bloco.frameworksSelecionados.find(f => f.id === item.id)) {
                metodologiaInput.value = '';
                return;
            }
            bloco.frameworksSelecionados.push({ id: item.id, nome: item.label });
            metodologiaInput.value = '';
            renderizarChipsMetodologia();
            metodologiaCampo.classList.remove('campo--erro');
        });

        // Analistas (multi-select com marcação de líder)
        const analistasCampo = raiz.querySelector('[data-campo="analistas"]');
        const analistaBusca  = analistasCampo.querySelector('.campo__multi-input');
        const analistaAdd    = analistasCampo.querySelector('.campo__botao-adicionar');
        const analistasChips = analistasCampo.querySelector('.campo__multi-chips');

        function renderizarChipsAnalistas() {
            analistasChips.innerHTML = '';
            bloco.equipe.forEach((membro, idx) => {
                const chip = criarChip(membro.nome, () => {
                    bloco.equipe.splice(idx, 1);
                    renderizarChipsAnalistas();
                });
                chip.classList.toggle('chip--lider-ativo', membro.lider);

                const btnLider = document.createElement('button');
                btnLider.type = 'button';
                btnLider.className = 'chip__lider-marcar';
                btnLider.innerHTML = '<i class="fa-solid fa-star"></i>';
                btnLider.setAttribute('aria-label', `Marcar ${membro.nome} como líder técnico`);
                btnLider.addEventListener('click', () => {
                    bloco.equipe.forEach(m => m.lider = false);
                    membro.lider = true;
                    renderizarChipsAnalistas();
                    analistasCampo.classList.remove('campo--erro');
                });
                chip.insertBefore(btnLider, chip.firstChild);

                analistasChips.appendChild(chip);
            });
        }

        function adicionarAnalista() {
            const query = analistaBusca.value.trim().toLowerCase();
            if (!query) return;

            const encontrado = usuarios.find(u =>
                u.nome.toLowerCase().includes(query) &&
                !bloco.equipe.find(m => m.id === u.id)
            );
            if (!encontrado) return;

            bloco.equipe.push({ id: encontrado.id, nome: encontrado.nome, lider: bloco.equipe.length === 0 });
            analistaBusca.value = '';
            renderizarChipsAnalistas();
            analistasCampo.classList.remove('campo--erro');
        }

        analistaAdd.addEventListener('click', adicionarAnalista);
        analistaBusca.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); adicionarAnalista(); }
        });

        // Remover bloco
        raiz.querySelector('.bloco-pentest__remover').addEventListener('click', () => {
            const idx = blocosPentest.findIndex(b => b.uid === uid);
            if (idx === -1) return;
            blocosPentest.splice(idx, 1);
            raiz.remove();
            atualizarNumerosDosBlocos();
        });

        bloco.renderizarChipsMetodologia = renderizarChipsMetodologia;
        bloco.renderizarChipsAnalistas   = renderizarChipsAnalistas;
        bloco.abordagemSelect = abordagemSelect;
        bloco.ambienteSelect  = ambienteSelect;
        bloco.tipoInput       = tipoInput;
        bloco.modalidadeEl    = modalidadeEl;

        return bloco;
    }

    function atualizarNumerosDosBlocos() {
        blocosPentest.forEach((bloco, idx) => {
            bloco.raiz.querySelector('[data-papel="numero"]').textContent = `Pentest ${idx + 1}`;
            const btnRemover = bloco.raiz.querySelector('.bloco-pentest__remover');
            btnRemover.style.display = blocosPentest.length > 1 ? '' : 'none';
        });
    }

    function adicionarBlocoPentest() {
        const bloco = criarBlocoPentest();
        blocosPentest.push(bloco);
        listaPentests.appendChild(bloco.raiz);
        atualizarNumerosDosBlocos();
        document.getElementById('campo-pentests')?.classList.remove('campo--erro');
        return bloco;
    }

    btnAdicionarPentest?.addEventListener('click', () => adicionarBlocoPentest());

    // -------------------------------------------------------------------------
    // 7b. PREENCHER REVISÃO (passo 3)
    // -------------------------------------------------------------------------
    function setText(id, texto) {
        const el = document.getElementById(id);
        if (el) el.textContent = texto;
    }

    function formatarData(isoStr) {
        if (!isoStr) return '—';
        const [y, m, d] = isoStr.split('-');
        return `${d}/${m}/${y}`;
    }

    const revPentestsLista = document.getElementById('rev-pentests-lista');

    function escapeHtml(texto) {
        const div = document.createElement('div');
        div.textContent = texto ?? '';
        return div.innerHTML;
    }

    function rotuloAbordagem(valor) {
        return ABORDAGENS.find(a => a.value === valor)?.label || '—';
    }

    function rotuloAmbiente(valor) {
        return AMBIENTES.find(a => a.value === valor)?.label || '—';
    }

    function preencherRevisao() {
        setText('rev-cliente',      clienteSelecionado.nome || '—');
        setText('rev-nome-projeto', document.getElementById('cp-nome-projeto')?.value || '—');
        setText('rev-sigilo',       document.getElementById('cp-sigilo')?.selectedOptions[0]?.text || '—');

        setText('rev-escopo',     document.getElementById('cp-escopo')?.value || '—');
        setText('rev-alvos',      alvos.length ? alvos.join(', ') : '—');
        setText('rev-restricoes', document.getElementById('cp-restricao')?.value || '—');

        const dataInicio = document.getElementById('cp-data-inicio')?.value;
        const dataFim    = document.getElementById('cp-data-fim')?.value;
        setText('rev-data-inicio', dataInicio ? formatarData(dataInicio) : '—');
        setText('rev-data-fim',    dataFim    ? formatarData(dataFim)    : '—');
        setText('rev-horas',       document.getElementById('cp-horas-contratadas')?.value || '—');

        if (!revPentestsLista) return;
        revPentestsLista.innerHTML = '';

        blocosPentest.forEach((bloco, idx) => {
            const lider     = bloco.equipe.find(m => m.lider);
            const analistas = bloco.equipe.filter(m => !m.lider).map(m => m.nome);

            const secao = document.createElement('div');
            secao.className = 'revisao-secao';
            secao.innerHTML = `
                <div class="revisao-secao__cabecalho">
                    <i class="fa-solid fa-shield-halved" style="color: var(--cor-azul-primaria)"></i>
                    <span class="revisao-secao__titulo">Pentest ${idx + 1} — ${escapeHtml(bloco.tipoInput.value) || '—'}</span>
                </div>
                <div class="revisao-secao__corpo">
                    <div class="revisao-campo">
                        <span class="revisao-campo__rotulo">Modalidade</span>
                        <span class="revisao-campo__valor">${escapeHtml(bloco.modalidadeEl.value) || '—'}</span>
                    </div>
                    <div class="revisao-campo">
                        <span class="revisao-campo__rotulo">Horas contratadas</span>
                        <span class="revisao-campo__valor">${escapeHtml(bloco.raiz.querySelector('[data-campo="horas"] input').value) || '—'}</span>
                    </div>
                    <div class="revisao-campo">
                        <span class="revisao-campo__rotulo">Abordagem</span>
                        <span class="revisao-campo__valor">${escapeHtml(rotuloAbordagem(bloco.abordagemSelect.value))}</span>
                    </div>
                    <div class="revisao-campo">
                        <span class="revisao-campo__rotulo">Ambiente</span>
                        <span class="revisao-campo__valor">${escapeHtml(rotuloAmbiente(bloco.ambienteSelect.value))}</span>
                    </div>
                    <div class="revisao-campo">
                        <span class="revisao-campo__rotulo">Metodologia</span>
                        <span class="revisao-campo__valor">${escapeHtml(bloco.frameworksSelecionados.map(f => f.nome).join(', ')) || '—'}</span>
                    </div>
                    <div class="revisao-campo">
                        <span class="revisao-campo__rotulo">Referência</span>
                        <span class="revisao-campo__valor">${escapeHtml(bloco.raiz.querySelector('[data-campo="referencia"] input').value) || '—'}</span>
                    </div>
                    <div class="revisao-campo revisao-campo--full">
                        <span class="revisao-campo__rotulo">Escopo</span>
                        <span class="revisao-campo__valor">${escapeHtml(bloco.raiz.querySelector('[data-campo="escopo"] textarea').value) || '—'}</span>
                    </div>
                    <div class="revisao-campo">
                        <span class="revisao-campo__rotulo">Líder Técnico</span>
                        <span class="revisao-campo__valor">${escapeHtml(lider ? lider.nome : '') || '—'}</span>
                    </div>
                    <div class="revisao-campo revisao-campo--full">
                        <span class="revisao-campo__rotulo">Analista(s)</span>
                        <span class="revisao-campo__valor">${escapeHtml(analistas.join(', ')) || '—'}</span>
                    </div>
                </div>
            `;
            revPentestsLista.appendChild(secao);
        });
    }

    // -------------------------------------------------------------------------
    // 8. SUBMIT — injeta campos dinâmicos antes de enviar
    // -------------------------------------------------------------------------
    const form = document.getElementById('form-cadastro-projeto');

    function prepararCamposDinamicos() {
        form.querySelectorAll('[data-dinamico]').forEach(el => el.remove());

        function adicionarHidden(nome, valor) {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = nome;
            inp.value = valor;
            inp.dataset.dinamico = '1';
            form.appendChild(inp);
        }

        alvos.forEach(alvo => adicionarHidden('alvos[]', alvo));

        blocosPentest.forEach((bloco, idx) => {
            adicionarHidden(`pentests[${idx}][tipo_pentest_id]`, bloco.tipoPentestId ?? '');
            adicionarHidden(`pentests[${idx}][horas_contratadas]`, bloco.raiz.querySelector('[data-campo="horas"] input').value);
            adicionarHidden(`pentests[${idx}][abordagem]`, bloco.abordagemSelect.value);
            adicionarHidden(`pentests[${idx}][ambiente]`, bloco.ambienteSelect.value);
            adicionarHidden(`pentests[${idx}][escopo]`, bloco.raiz.querySelector('[data-campo="escopo"] textarea').value);
            adicionarHidden(`pentests[${idx}][referencia]`, bloco.raiz.querySelector('[data-campo="referencia"] input').value);

            bloco.frameworksSelecionados.forEach(fw => adicionarHidden(`pentests[${idx}][frameworks_ids][]`, fw.id));

            const lider = bloco.equipe.find(m => m.lider);
            adicionarHidden(`pentests[${idx}][lider_id]`, lider ? lider.id : '');
            bloco.equipe.filter(m => !m.lider).forEach(m => adicionarHidden(`pentests[${idx}][analistas_ids][]`, m.id));
        });

        // Converte horas_contratadas de hh:mm:ss para decimal (80:00:00 → 80.00)
        const horasEl = document.getElementById('cp-horas-contratadas');
        if (horasEl) {
            const partes = horasEl.value.split(':');
            if (partes.length === 3) {
                const decimal = parseInt(partes[0]) + parseInt(partes[1]) / 60 + parseInt(partes[2]) / 3600;
                horasEl.value = decimal.toFixed(2);
            }
        }
    }

    form?.addEventListener('submit', (e) => {
        if (!validarPasso(1)) { e.preventDefault(); return; }

        if (modoEdicaoOuVisualizacao) {
            e.preventDefault();
            prepararCamposDinamicos();
            document.getElementById('popupSalvar')?.classList.add('active');
            return;
        }

        prepararCamposDinamicos();
    });

    // -------------------------------------------------------------------------
    // 9. CONFIRMAÇÃO ANTES DE SALVAR (só em modo editar)
    // -------------------------------------------------------------------------
    const popupSalvar = document.getElementById('popupSalvar');
    popupSalvar?.querySelector('[data-popup-confirmar]')?.addEventListener('click', () => {
        popupSalvar.classList.remove('active');
        form?.submit();
    });

    // -------------------------------------------------------------------------
    // 10. RESET AO FECHAR O MODAL
    // -------------------------------------------------------------------------
    overlay.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', resetModal);
    });

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) resetModal();
    });

    function resetModal() {
        passos.forEach(p => p.classList.remove('ativo'));
        stepperItens.forEach(item => {
            item.classList.remove('cad-projeto-stepper__item--ativo', 'cad-projeto-stepper__item--concluido');
        });
        passoAtual = 0;
        passos[0].classList.add('ativo');
        stepperItens[0].classList.add('cad-projeto-stepper__item--ativo');
        atualizarBotoes();

        clienteSelecionado = { id: null, nome: '' };
        alvos.length = 0;

        blocosPentest.length = 0;
        if (listaPentests) listaPentests.innerHTML = '';

        if (alvosChips) alvosChips.innerHTML = '';

        form?.reset();
        if (clienteInput) clienteInput.value = '';
        atualizarLimparCliente();
        if (dropzoneTxt)  dropzoneTxt.textContent = 'Arraste e solte seu arquivo aqui';

        overlay.querySelectorAll('.campo--erro').forEach(el => el.classList.remove('campo--erro'));
        form?.querySelectorAll('[data-dinamico]').forEach(el => el.remove());

        modoEdicaoOuVisualizacao = false;
        definirSomenteLeitura(false);
        const acaoInput = document.getElementById('cp-action');
        const projetoIdInput = document.getElementById('cp-projeto-id');
        if (acaoInput) acaoInput.value = 'cadastrar';
        if (projetoIdInput) projetoIdInput.value = '';

        const tituloEl = document.getElementById('cadastro-projeto-titulo');
        const subtituloEl = document.getElementById('cadastro-projeto-subtitulo');
        if (tituloEl) tituloEl.textContent = 'Cadastro de Projeto';
        if (subtituloEl) subtituloEl.textContent = 'Informações da empresa contratante e do projeto';

        adicionarBlocoPentest();
    }

    // -------------------------------------------------------------------------
    // 11. UTILITÁRIO — criar chip reutilizável
    // -------------------------------------------------------------------------
    function criarChip(texto, onRemover) {
        const chip = document.createElement('span');
        chip.className = 'chip';
        chip.append(document.createTextNode(texto));

        const btnRemover = document.createElement('button');
        btnRemover.type = 'button';
        btnRemover.className = 'chip__remover';
        btnRemover.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        btnRemover.setAttribute('aria-label', `Remover ${texto}`);
        btnRemover.addEventListener('click', onRemover);

        chip.appendChild(btnRemover);
        return chip;
    }

    // -------------------------------------------------------------------------
    // 12. MÁSCARA SIMPLES PARA CAMPO hh:mm:ss (horas totais do projeto)
    // -------------------------------------------------------------------------
    const horasContratadas = document.getElementById('cp-horas-contratadas');
    horasContratadas?.addEventListener('input', (e) => {
        let v = e.target.value.replace(/[^\d]/g, '');
        if (v.length > 6) v = v.slice(0, 6);
        if (v.length >= 5) {
            v = v.slice(0, 2) + ':' + v.slice(2, 4) + ':' + v.slice(4);
        } else if (v.length >= 3) {
            v = v.slice(0, 2) + ':' + v.slice(2);
        }
        e.target.value = v;
    });

    // -------------------------------------------------------------------------
    // 13. ABRIR EM MODO EDITAR/VISUALIZAR (a partir dos botões da tabela)
    // -------------------------------------------------------------------------
    const projetos = JSON.parse(overlay.dataset.projetos || '[]');

    function horasDecimalParaTexto(decimal) {
        const totalSegundos = Math.round(parseFloat(decimal) * 3600) || 0;
        const h = Math.floor(totalSegundos / 3600);
        const m = Math.floor((totalSegundos % 3600) / 60);
        const s = totalSegundos % 60;
        const pad = (n) => String(n).padStart(2, '0');
        return `${pad(h)}:${pad(m)}:${pad(s)}`;
    }

    function definirSomenteLeitura(valor) {
        somenteLeitura = valor;
        overlay.classList.toggle('modal--somente-leitura', valor);

        overlay.querySelectorAll('.modal__body input, .modal__body textarea, .modal__body select, .modal__body button').forEach(el => {
            el.disabled = valor;
        });

        atualizarBotoes();
    }

    function preencherModal(projeto, modo) {
        resetModal();
        blocosPentest.length = 0;
        if (listaPentests) listaPentests.innerHTML = '';

        document.getElementById('cp-action').value = 'editar';
        document.getElementById('cp-projeto-id').value = projeto.id;

        const tituloEl = document.getElementById('cadastro-projeto-titulo');
        if (tituloEl) tituloEl.textContent = modo === 'visualizar' ? 'Detalhes do Projeto' : 'Editar Projeto';

        // Passo 1 — Informações do Cliente
        const empresa = empresas.find(e => String(e.id) === String(projeto.empresa_id));
        clienteSelecionado = { id: projeto.empresa_id, nome: empresa ? (empresa.nome_fantasia || empresa.razao_social) : '' };
        if (clienteInput) clienteInput.value = clienteSelecionado.nome;
        atualizarLimparCliente();
        document.getElementById('cp-empresa-id').value = projeto.empresa_id ?? '';

        document.getElementById('cp-nome-projeto').value = projeto.nome ?? '';
        document.getElementById('cp-sigilo').value = projeto.nivel_sigilo ?? '';
        document.getElementById('cp-data-inicio').value = projeto.data_inicio ?? '';
        document.getElementById('cp-data-fim').value = projeto.data_fim_prevista ?? '';
        document.getElementById('cp-horas-contratadas').value = projeto.horas_contratadas
            ? horasDecimalParaTexto(projeto.horas_contratadas)
            : '';
        document.getElementById('cp-escopo').value = projeto.escopo ?? '';
        document.getElementById('cp-restricao').value = projeto.restricao ?? '';

        (projeto.alvos || []).forEach(valor => alvos.push(valor));
        renderizarChipsAlvos();

        // Passo 2 — Informações do Pentest (N blocos)
        const pentestsProjeto = projeto.pentests || [];
        pentestsProjeto.forEach(p => {
            const bloco = adicionarBlocoPentest();

            bloco.tipoPentestId = p.tipo_pentest_id ? Number(p.tipo_pentest_id) : null;
            bloco.tipoInput.value = p.tipo_pentest_nome ?? '';
            bloco.modalidadeEl.value = p.modalidade ?? '';

            bloco.raiz.querySelector('[data-campo="horas"] input').value = p.horas_contratadas ?? '';
            bloco.abordagemSelect.value = p.abordagem ?? '';
            bloco.ambienteSelect.value = p.ambiente ?? '';
            bloco.raiz.querySelector('[data-campo="escopo"] textarea').value = p.escopo ?? '';
            bloco.raiz.querySelector('[data-campo="referencia"] input').value = p.referencia ?? '';

            (p.frameworks_ids || []).forEach(idFw => {
                const fw = frameworks.find(f => String(f.id) === String(idFw));
                if (fw && !bloco.frameworksSelecionados.find(f => f.id === fw.id)) {
                    bloco.frameworksSelecionados.push({ id: fw.id, nome: fw.nome });
                }
            });
            bloco.renderizarChipsMetodologia();

            if (p.lider_id) {
                const lider = usuarios.find(u => String(u.id) === String(p.lider_id));
                if (lider) bloco.equipe.push({ id: lider.id, nome: lider.nome, lider: true });
            }
            (p.analistas_ids || []).forEach(idUsuario => {
                const usuario = usuarios.find(u => String(u.id) === String(idUsuario));
                if (usuario && !bloco.equipe.find(m => m.id === usuario.id)) {
                    bloco.equipe.push({ id: usuario.id, nome: usuario.nome, lider: false });
                }
            });
            bloco.renderizarChipsAnalistas();
        });

        if (pentestsProjeto.length === 0) adicionarBlocoPentest();

        modoEdicaoOuVisualizacao = true;
        definirSomenteLeitura(modo === 'visualizar');

        stepperItens.forEach((item, idx) => {
            if (idx !== passoAtual) item.classList.add('cad-projeto-stepper__item--concluido');
        });
    }

    document.querySelectorAll('[data-projeto-id][data-modo]').forEach(botao => {
        botao.addEventListener('click', () => {
            const projeto = projetos.find(p => String(p.id) === String(botao.dataset.projetoId));
            if (!projeto) return;
            preencherModal(projeto, botao.dataset.modo);
        });
    });

    // -------------------------------------------------------------------------
    // 14. EXCLUIR PROJETO (soft delete, com confirmação)
    // -------------------------------------------------------------------------
    const formExcluir = document.getElementById('form-excluir-projeto');
    document.querySelectorAll('.btn-excluir[data-projeto-id]').forEach(botao => {
        botao.addEventListener('click', () => {
            const nome = botao.dataset.projetoNome || 'este projeto';
            if (!confirm(`Tem certeza que deseja excluir "${nome}"? Essa ação pode ser desfeita apenas por um administrador.`)) {
                return;
            }
            document.getElementById('excluir-projeto-id').value = botao.dataset.projetoId;
            formExcluir.submit();
        });
    });

    // Inicialização: primeiro bloco de pentest já visível no cadastro, e
    // garante que os botões estejam corretos ao carregar.
    adicionarBlocoPentest();
    atualizarBotoes();
});
