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
 *     metodologia (frameworks), escopo, ambiente, analistas
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
    let projetoTemContrato = false; // no Editar/Visualizar: já existe contrato salvo no servidor
    const posicoesScroll = {}; // passo → scrollTop, para voltar a um passo na altura em que ele foi deixado
    let somenteLeitura = false; // true só no modo Visualizar (Editar continua editável)

    // IDs selecionados (passo 1)
    let clienteSelecionado = { id: null, nome: '' };
    let liderSelecionado   = { id: null, nome: '' }; // líder técnico é um só por projeto
    const alvos            = []; // strings

    // Rotas JSON do próprio módulo (mesma URL do form, que já traz a BASE_URL).
    const urlBaseProjeto = document.getElementById('form-cadastro-projeto').getAttribute('action');

    // Popup de confirmação de salvamento: compartilhado com o modal de tipo de pentest.
    const popupSalvar = document.getElementById('popupSalvar');
    let aguardandoConfirmacaoEdicao = false;

    // Itens do combobox de tipo de pentest: lista única para todos os blocos,
    // refeita quando um tipo é cadastrado pelo atalho "+".
    const itensTipo = [];

    function atualizarItensTipo() {
        itensTipo.length = 0;
        tiposPentest.forEach(t => itensTipo.push({
            id: t.id, label: t.nome, categoria_nome: t.categoria_nome, frameworks_ids: t.frameworks_ids || [],
        }));
    }

    atualizarItensTipo();

    const modalTipoPentest = document.getElementById('modalNovoPentest');
    let blocoAguardandoTipo = null; // bloco cujo "+" abriu o modal de tipo de pentest

    // Chamado pelo modal-tipo-pentest.js depois de salvar, no lugar do reload.
    window.aoSalvarTipoPentest = async (tipoSalvo) => {
        modalTipoPentest?.classList.remove('active');

        try {
            const resposta  = await fetch(`${urlBaseProjeto}/tipos-pentest`);
            const resultado = await resposta.json();

            tiposPentest.length = 0;
            resultado.data.forEach(t => tiposPentest.push(t));
            atualizarItensTipo();

            const novo = itensTipo.find(i => String(i.id) === String(tipoSalvo.id));
            if (novo && blocoAguardandoTipo) blocoAguardandoTipo.selecionarTipo(novo);

            window.exibirToast?.('sucesso', 'Tipo de pentest cadastrado e selecionado.');
        } catch (erro) {
            console.error(erro);
            window.exibirToast?.('aviso', 'Tipo de pentest cadastrado, mas a lista não foi atualizada. Recarregue a página para vê-lo.', undefined, 5000);
        } finally {
            blocoAguardandoTipo = null;
        }
    };

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
        posicoesScroll[passoAtual] = passos[passoAtual].scrollTop;
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
        passos[passoAtual].scrollTop = posicoesScroll[passoAtual] || 0;
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

    // A mensagem de erro só era reavaliada no "Avançar": some assim que o
    // usuário mexe no campo, e volta na próxima validação se ainda faltar algo.
    ['cp-nome-projeto', 'cp-escopo', 'cp-horas-contratadas', 'cp-sigilo'].forEach(id => {
        const el = document.getElementById(id);
        el?.addEventListener(el.tagName === 'SELECT' ? 'change' : 'input', () => marcarErroCampo(id, false));
    });

    function validarPasso(passo) {
        let temErro = false;

        if (passo === 0) {
            if (!clienteSelecionado.id) {
                document.getElementById('campo-cliente')?.classList.add('campo--erro');
                temErro = true;
            } else {
                document.getElementById('campo-cliente')?.classList.remove('campo--erro');
            }

            const semLider = !liderSelecionado.id;
            document.getElementById('campo-lider')?.classList.toggle('campo--erro', semLider);
            temErro = temErro || semLider;

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
            const horasInvalidas = !(horasTextoParaSegundos(horas.value) > 0);
            marcarErroCampo('cp-horas-contratadas', horasInvalidas);
            temErro = temErro || horasInvalidas;
        }

        if (passo === 1) {
            const semBlocos = blocosPentest.length === 0;
            document.getElementById('campo-pentests')?.classList.toggle('campo--erro', semBlocos);
            temErro = temErro || semBlocos;

            blocosPentest.forEach(bloco => temErro = !validarBlocoPentest(bloco) || temErro);

            temErro = !validarSomaHorasPentests() || temErro;
        }

        return !temErro;
    }

    // As horas dos pentests saem das horas totais do projeto: a soma não pode ultrapassar.
    function validarSomaHorasPentests() {
        const campo = document.getElementById('campo-horas-pentests');
        const total = horasTextoParaSegundos(document.getElementById('cp-horas-contratadas').value);
        const soma  = blocosPentest.reduce((acc, bloco) =>
            acc + (horasTextoParaSegundos(bloco.raiz.querySelector('[data-campo="horas"] input').value) || 0), 0);

        const ultrapassou = total !== null && soma > total;
        if (ultrapassou) {
            document.getElementById('erro-horas-pentests').textContent =
                `A soma das horas dos pentests (${segundosParaTexto(soma)}) ultrapassa as horas totais contratadas do projeto (${segundosParaTexto(total)}).`;
        }
        campo?.classList.toggle('campo--erro', ultrapassou);
        return !ultrapassou;
    }

    function validarBlocoPentest(bloco) {
        let ok = true;

        const tipoInvalido = !bloco.tipoPentestId;
        bloco.raiz.querySelector('[data-campo="tipo"]').classList.toggle('campo--erro', tipoInvalido);
        ok = ok && !tipoInvalido;

        const horasEl = bloco.raiz.querySelector('[data-campo="horas"] input');
        const horasInvalidas = !(horasTextoParaSegundos(horasEl.value) > 0);
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

        const semAnalista = bloco.equipe.length === 0;
        bloco.raiz.querySelector('[data-campo="analistas"]').classList.toggle('campo--erro', semAnalista);
        ok = ok && !semAnalista;

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

    // Itens do combobox de cliente: lista única, refeita quando uma empresa é
    // cadastrada pelo atalho "+".
    const itensCliente = [];

    function atualizarItensCliente() {
        itensCliente.length = 0;
        empresas.forEach(e => itensCliente.push({
            id:    e.id,
            label: e.nome_fantasia || e.razao_social || `Empresa #${e.id}`,
        }));
    }

    function selecionarCliente(item) {
        clienteSelecionado = { id: item.id, nome: item.label };
        clienteInput.value = item.label;
        document.getElementById('cp-empresa-id').value = item.id;
        document.getElementById('campo-cliente')?.classList.remove('campo--erro');
        atualizarLimparCliente();
    }

    atualizarItensCliente();

    // "+" do Cliente: abre o modal de cadastro de empresa do módulo de Clientes
    // (Components/modais/cadastro_empresa.php) por cima deste. O <form> dele
    // envia para a própria página e recarrega; aqui o envio é interceptado e
    // vai por fetch, para o projeto em preenchimento não se perder.
    const modalEmpresa = document.getElementById('modalClientes');
    const formEmpresa  = modalEmpresa?.querySelector('form');

    document.getElementById('cp-cliente-cadastrar')?.addEventListener('click', () => {
        if (!formEmpresa) {
            window.exibirToast?.('info', 'O cadastro de cliente não está disponível nesta tela. Use a tela de Clientes.');
            return;
        }
        formEmpresa.reset();
        modalEmpresa.classList.add('active');
    });

    formEmpresa?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const botaoSalvar = formEmpresa.querySelector('[type="submit"]');
        botaoSalvar.disabled = true;

        try {
            const resposta  = await fetch(`${urlBaseProjeto}/cadastrar-empresa`, { method: 'POST', body: new FormData(formEmpresa) });
            const resultado = await resposta.json();

            if (resultado.status !== 200) {
                window.exibirToast?.('erro', resultado.msg || 'Não foi possível cadastrar a empresa.', undefined, 4000);
                return;
            }

            empresas.length = 0;
            resultado.data.forEach(e => empresas.push(e));
            atualizarItensCliente();

            const nova = itensCliente.find(i => String(i.id) === String(resultado.id));
            if (nova) selecionarCliente(nova);

            modalEmpresa.classList.remove('active');
            formEmpresa.reset();
            window.exibirToast?.('sucesso', 'Empresa cadastrada e selecionada no projeto.');
        } catch (erro) {
            console.error(erro);
            window.exibirToast?.('erro', 'Não foi possível cadastrar a empresa. Tente novamente.', undefined, 4000);
        } finally {
            botaoSalvar.disabled = false;
        }
    });

    clienteLimpar?.addEventListener('click', () => {
        clienteSelecionado = { id: null, nome: '' };
        clienteInput.value = '';
        document.getElementById('cp-empresa-id').value = '';
        atualizarLimparCliente();
        clienteInput.focus();
    });

    if (clienteInput && clienteLista && clienteToggle) {
        criarCombobox(clienteInput, clienteLista, clienteToggle, itensCliente, selecionarCliente);
    }

    // -------------------------------------------------------------------------
    // 4b. COMBOBOX DE LÍDER TÉCNICO (um por projeto)
    // -------------------------------------------------------------------------
    const liderInput  = document.getElementById('cp-lider-busca');
    const liderLista  = document.getElementById('cp-lider-lista');
    const liderToggle = liderInput?.nextElementSibling;

    const liderLimpar = document.getElementById('cp-lider-limpar');

    function definirLider(id, nome) {
        liderSelecionado = { id, nome };
        if (liderInput) liderInput.value = nome;
        document.getElementById('cp-lider-id').value = id ?? '';

        if (liderLimpar) liderLimpar.hidden = !id;
        liderInput?.closest('.campo__combobox-campo')?.classList.toggle('campo__combobox-campo--com-limpar', !!id);
    }

    liderLimpar?.addEventListener('click', () => {
        definirLider(null, '');
        liderInput.focus();
    });

    if (liderInput && liderLista && liderToggle) {
        const itensLider = usuarios.map(u => ({ id: u.id, label: u.nome }));

        criarCombobox(liderInput, liderLista, liderToggle, itensLider, (item) => {
            definirLider(item.id, item.label);
            document.getElementById('campo-lider')?.classList.remove('campo--erro');
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
            const chip = criarChip(alvo, () => {
                alvos.splice(idx, 1);
                renderizarChipsAlvos();
            });
            // URLs longas estouravam a caixa: o chip mostra só o começo e
            // abre o valor completo (quebrando linha dentro da caixa) ao clicar.
            chip.classList.add('chip--truncado');
            const textoChip = chip.querySelector('.chip__texto');
            textoChip.title = 'Clique para ver o valor completo';
            textoChip.tabIndex = 0;
            const alternarChip = () => chip.classList.toggle('chip--expandido');
            textoChip.addEventListener('click', alternarChip);
            textoChip.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); alternarChip(); }
            });
            alvosChips.appendChild(chip);
        });
    }

    // Contador de caracteres dos textareas do passo 1 (resumo e restrições)
    const camposComContador = [
        ['cp-escopo', 'cp-escopo-contador'],
        ['cp-restricao', 'cp-restricao-contador'],
    ].map(([idCampo, idContador]) => ({
        campo: document.getElementById(idCampo),
        contador: document.getElementById(idContador),
    })).filter(c => c.campo && c.contador);

    function atualizarContadores() {
        camposComContador.forEach(({ campo, contador }) => {
            contador.textContent = `${campo.value.length} / ${campo.maxLength}`;
        });
    }

    camposComContador.forEach(({ campo }) => campo.addEventListener('input', atualizarContadores));

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
                            <button type="button" class="campo__botao-adicionar" data-acao="cadastrar-tipo-pentest"
                                aria-label="Cadastrar novo tipo de pentest" title="Cadastrar novo tipo de pentest (ainda não disponível nesta tela)">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <span class="campo__mensagem-erro">Selecione o tipo de pentest.</span>
                </div>
                <div class="campo" data-campo="horas">
                    <label class="campo__label campo__label--obrigatorio">Horas de Pentest contratadas</label>
                    <input type="text" class="campo__input" placeholder="hh:mm:ss" maxlength="9" inputmode="numeric">
                    <span class="campo__mensagem-erro">Informe as horas deste pentest no formato hh:mm:ss (ex: 40:00:00), com minutos e segundos até 59.</span>
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
                <textarea class="campo__textarea" rows="4" maxlength="2000" placeholder="Descreva o escopo deste pentest..."></textarea>
                <span class="campo__contador">0 / 2000</span>
                <span class="campo__mensagem-erro">O escopo deste pentest é obrigatório.</span>
            </div>
            <div class="campo" data-campo="analistas">
                <label class="campo__label campo__label--obrigatorio">Analista(s)</label>
                <div class="campo__multi">
                    <div class="campo__multi-busca">
                        <div class="campo__combobox" style="flex:1">
                            <div class="campo__combobox-linha">
                                <div class="campo__combobox-campo">
                                    <input type="text" class="campo__input campo__combobox-input"
                                        placeholder="Pesquise e selecione um analista..." role="combobox"
                                        aria-autocomplete="list" aria-expanded="false" autocomplete="off">
                                    <button type="button" class="campo__combobox-alternar" aria-label="Mostrar analistas">
                                        <i class="fa-solid fa-chevron-down"></i>
                                    </button>
                                    <button type="button" class="campo__combobox-limpar" aria-label="Limpar busca de analista" hidden>
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    <div class="campo__combobox-lista" role="listbox" hidden></div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="campo__botao-adicionar" data-acao="cadastrar-usuario"
                            aria-label="Cadastrar novo usuário" title="Cadastrar novo usuário (ainda não disponível nesta tela)">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                    <div class="campo__multi-chips" aria-label="Analistas deste pentest"></div>
                </div>
                <span class="campo__mensagem-erro">Adicione ao menos um analista a este pentest.</span>
            </div>
            <div class="campo" data-campo="checklist">
                <label class="campo__label">Checklist</label>
                <div data-papel="previa-checklist"></div>
            </div>
        `;

        const bloco = {
            uid,
            raiz,
            tipoPentestId: null,
            frameworksSelecionados: [], // [{id, nome}]
            equipe: [], // analistas do pentest: [{id, nome}]
        };

        // Tipo de pentest (combobox único)
        const tipoCampo    = raiz.querySelector('[data-campo="tipo"]');
        const tipoInput    = tipoCampo.querySelector('.campo__combobox-input');
        const tipoLista    = tipoCampo.querySelector('.campo__combobox-lista');
        const tipoToggle   = tipoCampo.querySelector('.campo__combobox-alternar');
        const modalidadeEl = raiz.querySelectorAll('.campo__input--readonly')[0];

        // Prévia (só leitura) dos checklists que o tipo de pentest traz junto.
        const previaChecklist = raiz.querySelector('[data-papel="previa-checklist"]');

        function renderizarPreviaChecklist() {
            previaChecklist.innerHTML = '';

            const tipo = tiposPentest.find(t => String(t.id) === String(bloco.tipoPentestId));
            const checklists = tipo?.checklists || [];

            if (checklists.length === 0) {
                const aviso = document.createElement('p');
                aviso.className = 'campo__ajuda';
                aviso.textContent = tipo
                    ? 'Este tipo de pentest não tem checklist vinculado.'
                    : 'Selecione o tipo de pentest para ver o checklist vinculado.';
                previaChecklist.appendChild(aviso);
                return;
            }

            checklists.forEach(checklist => {
                const detalhes = document.createElement('details');
                detalhes.className = 'previa-checklist';

                const titulo = document.createElement('summary');
                titulo.className = 'previa-checklist__titulo';
                titulo.append(checklist.nome + ' ');

                const total = checklist.itens.length;
                const contagem = document.createElement('span');
                contagem.className = 'previa-checklist__contagem';
                contagem.textContent = `(${total} ${total === 1 ? 'item' : 'itens'})`;
                titulo.appendChild(contagem);
                detalhes.appendChild(titulo);

                const lista = document.createElement('ul');
                lista.className = 'previa-checklist__itens';
                checklist.itens.forEach(tituloItem => {
                    const li = document.createElement('li');
                    li.textContent = tituloItem;
                    lista.appendChild(li);
                });
                detalhes.appendChild(lista);

                previaChecklist.appendChild(detalhes);
            });
        }

        bloco.renderizarPreviaChecklist = renderizarPreviaChecklist;
        renderizarPreviaChecklist();
        bloco.selecionarTipo = (item) => {
            bloco.tipoPentestId = item.id;
            tipoInput.value = item.label;
            modalidadeEl.value = item.categoria_nome || '';
            tipoCampo.classList.remove('campo--erro');

            // A metodologia passa a ser a vinculada ao tipo escolhido (o
            // usuário ainda pode tirar ou acrescentar depois). Tipo sem
            // vínculo não mexe no que já estava selecionado.
            renderizarPreviaChecklist();

            const vinculados = frameworks.filter(f => item.frameworks_ids.includes(Number(f.id)));
            if (vinculados.length > 0) {
                bloco.frameworksSelecionados.length = 0;
                vinculados.forEach(f => bloco.frameworksSelecionados.push({ id: f.id, nome: f.nome }));
                bloco.renderizarChipsMetodologia();
                raiz.querySelector('[data-campo="metodologia"]').classList.remove('campo--erro');
            }
        };

        criarCombobox(tipoInput, tipoLista, tipoToggle, itensTipo, bloco.selecionarTipo);

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
        raiz.querySelector('[data-campo="horas"] input').addEventListener('input', (e) => {
            aplicarMascaraHoras(e);
            raiz.querySelector('[data-campo="horas"]').classList.remove('campo--erro');
            document.getElementById('campo-horas-pentests')?.classList.remove('campo--erro');
        });
        const escopoPentest         = raiz.querySelector('[data-campo="escopo"] textarea');
        const escopoPentestContador = raiz.querySelector('[data-campo="escopo"] .campo__contador');
        bloco.atualizarContadorEscopo = () => {
            escopoPentestContador.textContent = `${escopoPentest.value.length} / ${escopoPentest.maxLength}`;
        };
        escopoPentest.addEventListener('input', bloco.atualizarContadorEscopo);

        // "+" do Tipo de Pentest: abre o modal de cadastro do módulo de Pentest
        // por cima deste. Ao salvar, window.aoSalvarTipoPentest (mais abaixo)
        // recarrega a lista e seleciona o tipo novo neste bloco.
        raiz.querySelector('[data-acao="cadastrar-tipo-pentest"]').addEventListener('click', () => {
            if (!modalTipoPentest || typeof window.limparFormularioPentest !== 'function') {
                window.exibirToast?.('info', 'O cadastro de tipo de pentest não está disponível nesta tela. Use a tela de Pentest.');
                return;
            }
            blocoAguardandoTipo = bloco;
            window.limparFormularioPentest();
            modalTipoPentest.classList.add('active');
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

        // Analistas (multi-select: escolher na lista já adiciona o chip)
        const analistasCampo  = raiz.querySelector('[data-campo="analistas"]');
        const analistaBusca   = analistasCampo.querySelector('.campo__combobox-input');
        const analistaLista   = analistasCampo.querySelector('.campo__combobox-lista');
        const analistaToggle  = analistasCampo.querySelector('.campo__combobox-alternar');
        const analistasChips  = analistasCampo.querySelector('.campo__multi-chips');

        function renderizarChipsAnalistas() {
            analistasChips.innerHTML = '';
            bloco.equipe.forEach((membro, idx) => {
                analistasChips.appendChild(criarChip(membro.nome, () => {
                    bloco.equipe.splice(idx, 1);
                    renderizarChipsAnalistas();
                }));
            });
        }

        const analistaLimpar = analistasCampo.querySelector('.campo__combobox-limpar');

        function atualizarLimparAnalista() {
            const temTexto = analistaBusca.value !== '';
            analistaLimpar.hidden = !temTexto;
            analistaBusca.closest('.campo__combobox-campo').classList.toggle('campo__combobox-campo--com-limpar', temTexto);
        }

        analistaBusca.addEventListener('input', atualizarLimparAnalista);
        analistaLimpar.addEventListener('click', () => {
            analistaBusca.value = '';
            atualizarLimparAnalista();
            analistaBusca.focus();
        });

        // PENDENTE: o "+" deve abrir o modal de cadastro de usuário aqui mesmo,
        // como já fazem os de Cliente e de Tipo de Pentest. Não foi ligado porque
        // o cadastro de usuário ainda não funciona no próprio módulo: o <select>
        // de perfil envia "analista"/"administrador" em vez do id do perfil, e
        // GerenciamentoUsuarioController::cadastrar() passa senha e perfil_id em
        // ordem trocada para GerenUsuario::cadastrarUsuario() (além de gravar a
        // senha sem hash). Ligar o atalho antes disso criaria usuários inválidos.
        analistasCampo.querySelector('[data-acao="cadastrar-usuario"]').addEventListener('click', () => {
            if (typeof exibirToast === 'function') {
                exibirToast('info', 'O cadastro de usuário por aqui ainda não está disponível. Use a tela de Usuários.');
            }
        });

        const itensAnalista = usuarios.map(u => ({ id: u.id, label: u.nome }));
        criarCombobox(analistaBusca, analistaLista, analistaToggle, itensAnalista, (item) => {
            analistaBusca.value = '';
            atualizarLimparAnalista();
            if (bloco.equipe.find(m => m.id === item.id)) return;

            bloco.equipe.push({ id: item.id, nome: item.label });
            renderizarChipsAnalistas();
            analistasCampo.classList.remove('campo--erro');
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
        setText('rev-lider',        liderSelecionado.nome || '—');

        setText('rev-escopo',     document.getElementById('cp-escopo')?.value || '—');
        const revAlvos = document.getElementById('rev-alvos');
        if (revAlvos) {
            revAlvos.textContent = alvos.length ? '' : '—';
            alvos.forEach(alvo => {
                const linha = document.createElement('span');
                linha.textContent = alvo;
                revAlvos.appendChild(linha);
            });
        }

        const arquivoContrato = contratoInput?.files?.[0];
        setText('rev-contrato', arquivoContrato
            ? arquivoContrato.name
            : (projetoTemContrato ? 'Contrato já anexado (mantido)' : 'Nenhum arquivo anexado'));
        setText('rev-restricoes', document.getElementById('cp-restricao')?.value || '—');

        const dataInicio = document.getElementById('cp-data-inicio')?.value;
        const dataFim    = document.getElementById('cp-data-fim')?.value;
        setText('rev-data-inicio', dataInicio ? formatarData(dataInicio) : '—');
        setText('rev-data-fim',    dataFim    ? formatarData(dataFim)    : '—');
        setText('rev-horas',       document.getElementById('cp-horas-contratadas')?.value || '—');

        if (!revPentestsLista) return;
        revPentestsLista.innerHTML = '';

        blocosPentest.forEach((bloco, idx) => {
            const analistas = bloco.equipe.map(m => m.nome);
            const tipoDoBloco = tiposPentest.find(t => String(t.id) === String(bloco.tipoPentestId));
            const checklists = (tipoDoBloco?.checklists || []).map(c => c.nome);

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
                    <div class="revisao-campo revisao-campo--full">
                        <span class="revisao-campo__rotulo">Analista(s)</span>
                        <span class="revisao-campo__valor">${escapeHtml(analistas.join(', ')) || '—'}</span>
                    </div>
                    <div class="revisao-campo revisao-campo--full">
                        <span class="revisao-campo__rotulo">Checklist</span>
                        <span class="revisao-campo__valor">${escapeHtml(checklists.join(', ')) || '—'}</span>
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
            adicionarHidden(`pentests[${idx}][horas_contratadas]`, horasTextoParaDecimal(bloco.raiz.querySelector('[data-campo="horas"] input').value));
            adicionarHidden(`pentests[${idx}][abordagem]`, bloco.abordagemSelect.value);
            adicionarHidden(`pentests[${idx}][ambiente]`, bloco.ambienteSelect.value);
            adicionarHidden(`pentests[${idx}][escopo]`, bloco.raiz.querySelector('[data-campo="escopo"] textarea').value);
            adicionarHidden(`pentests[${idx}][referencia]`, bloco.raiz.querySelector('[data-campo="referencia"] input').value);

            bloco.frameworksSelecionados.forEach(fw => adicionarHidden(`pentests[${idx}][frameworks_ids][]`, fw.id));

            bloco.equipe.forEach(m => adicionarHidden(`pentests[${idx}][analistas_ids][]`, m.id));
        });

        // O campo visível fica em hh:mm:ss e não tem name: o servidor recebe o
        // decimal (80:00:00 → 80.00) por este hidden. Converter o próprio campo
        // deixava "80.00" na tela se o usuário cancelasse a confirmação do Editar.
        adicionarHidden('horas_contratadas', horasTextoParaDecimal(document.getElementById('cp-horas-contratadas').value));
    }

    form?.addEventListener('submit', (e) => {
        // No Editar o Salvar aparece em qualquer passo: valida os dois e leva
        // o usuário ao primeiro que tiver pendência.
        const passoComErro = [0, 1].find(passo => !validarPasso(passo));
        if (passoComErro !== undefined) {
            e.preventDefault();
            if (passoComErro !== passoAtual) irParaPasso(passoComErro);
            return;
        }

        if (modoEdicaoOuVisualizacao) {
            e.preventDefault();
            prepararCamposDinamicos();

            // O popup de salvar é compartilhado com o modal de tipo de pentest,
            // que deixa o nome da função dele no botão de confirmar.
            const botaoConfirmar = popupSalvar?.querySelector('[data-popup-confirmar]');
            if (botaoConfirmar) botaoConfirmar.dataset.popupCallback = '';
            aguardandoConfirmacaoEdicao = true;
            popupSalvar?.classList.add('active');
            return;
        }

        prepararCamposDinamicos();
    });

    // -------------------------------------------------------------------------
    // 9. CONFIRMAÇÃO ANTES DE SALVAR (só em modo editar)
    // -------------------------------------------------------------------------
    popupSalvar?.querySelector('[data-popup-confirmar]')?.addEventListener('click', (e) => {
        // Só envia o projeto quando o popup foi aberto pelo Editar; se quem
        // abriu foi o modal de tipo de pentest, a confirmação é dele.
        const eDaEdicao = aguardandoConfirmacaoEdicao && !e.currentTarget.dataset.popupCallback;
        aguardandoConfirmacaoEdicao = false;
        if (!eDaEdicao) return;

        popupSalvar.classList.remove('active');
        form?.submit();
    });

    // -------------------------------------------------------------------------
    // 10. RESET AO FECHAR O MODAL
    // -------------------------------------------------------------------------
    // Fechar pelo "X" ou clicando fora só pede confirmação quando há algo a
    // perder. Quem fecha o modal é o modal.js (global), então a checagem roda
    // na fase de captura, antes dele: se houver dados, o clique é barrado e o
    // popup decide; se não houver, o clique segue e o modal fecha como antes.
    let estadoInicial = '';

    function capturarEstado() {
        return JSON.stringify({
            campos: [...overlay.querySelectorAll('.modal__body input, .modal__body textarea, .modal__body select')]
                .map(el => (el.type === 'file' ? (el.files[0]?.name || '') : el.value)),
            cliente: clienteSelecionado.id,
            lider: liderSelecionado.id,
            alvos,
            blocos: blocosPentest.map(b => ({
                tipo: b.tipoPentestId,
                frameworks: b.frameworksSelecionados.map(f => f.id),
                analistas: b.equipe.map(m => m.id),
            })),
        });
    }

    function registrarEstadoInicial() {
        estadoInicial = capturarEstado();
    }

    function temAlgoPreenchido() {
        const algumCampo = [...overlay.querySelectorAll('.modal__body input, .modal__body textarea, .modal__body select')]
            .some(el => (el.type === 'file' ? el.files.length > 0 : el.value.trim() !== ''));

        return algumCampo
            || !!clienteSelecionado.id
            || !!liderSelecionado.id
            || alvos.length > 0
            || blocosPentest.some(b => b.tipoPentestId || b.frameworksSelecionados.length > 0 || b.equipe.length > 0);
    }

    // Cadastro: qualquer coisa preenchida. Editar: só se algo mudou em relação
    // ao que veio do servidor. Visualizar: nunca.
    function haDadosAPerder() {
        if (somenteLeitura) return false;
        return modoEdicaoOuVisualizacao ? capturarEstado() !== estadoInicial : temAlgoPreenchido();
    }

    const popupCancelar = document.getElementById('popupCancelarProjeto');

    document.addEventListener('click', (e) => {
        if (!overlay.classList.contains('active')) return;

        const clicouFora   = e.target === overlay;
        const clicouFechar = overlay.contains(e.target) && !!e.target.closest('[data-modal-close]');
        if (!clicouFora && !clicouFechar) return;
        if (!popupCancelar || !haDadosAPerder()) return;

        e.preventDefault();
        e.stopPropagation();
        popupCancelar.classList.add('active');
    }, true);

    document.getElementById('cp-confirmar-cancelamento')?.addEventListener('click', () => {
        popupCancelar.classList.remove('active');
        overlay.classList.remove('active');
        resetModal();
        adicionarBlocoPentest();
    });

    overlay.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            resetModal();
            adicionarBlocoPentest();
        });
    });

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            resetModal();
            adicionarBlocoPentest();
        }
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
        definirLider(null, '');
        alvos.length = 0;

        blocosPentest.length = 0;
        if (listaPentests) listaPentests.innerHTML = '';

        if (alvosChips) alvosChips.innerHTML = '';

        form?.reset();
        if (clienteInput) clienteInput.value = '';
        atualizarLimparCliente();
        atualizarContadores();
        if (dropzoneTxt)  dropzoneTxt.textContent = 'Arraste e solte seu arquivo aqui';
        projetoTemContrato = false;
        Object.keys(posicoesScroll).forEach(passo => delete posicoesScroll[passo]);
        passos.forEach(p => { p.scrollTop = 0; });

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
    }

    // -------------------------------------------------------------------------
    // 11. UTILITÁRIO — criar chip reutilizável
    // -------------------------------------------------------------------------
    function criarChip(texto, onRemover) {
        const chip = document.createElement('span');
        chip.className = 'chip';
        const textoEl = document.createElement('span');
        textoEl.className = 'chip__texto';
        textoEl.textContent = texto;
        chip.appendChild(textoEl);

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
    // 12. CAMPOS hh:mm:ss (horas totais do projeto e horas de cada pentest)
    // -------------------------------------------------------------------------
    // Até 6 dígitos vira hh:mm:ss; o 7º dígito abre a terceira casa de hora
    // (1200000 → 120:00:00), para contratos acima de 99 horas.
    function aplicarMascaraHoras(e) {
        let v = e.target.value.replace(/[^\d]/g, '');
        if (v.length > 7) v = v.slice(0, 7);
        if (v.length === 7) {
            v = v.slice(0, 3) + ':' + v.slice(3, 5) + ':' + v.slice(5);
        } else if (v.length >= 5) {
            v = v.slice(0, 2) + ':' + v.slice(2, 4) + ':' + v.slice(4);
        } else if (v.length >= 3) {
            v = v.slice(0, 2) + ':' + v.slice(2);
        }
        e.target.value = v;
    }

    /** "40:30:00" → 145800; null quando o texto não está no formato hh:mm:ss. */
    function horasTextoParaSegundos(texto) {
        const partes = /^(\d{1,3}):(\d{2}):(\d{2})$/.exec((texto || '').trim());
        if (!partes) return null;

        const [horas, minutos, segundos] = partes.slice(1).map(Number);
        if (minutos > 59 || segundos > 59) return null; // 80:75:00 não é um horário válido

        return horas * 3600 + minutos * 60 + segundos;
    }

    function horasTextoParaDecimal(texto) {
        const segundos = horasTextoParaSegundos(texto);
        return segundos === null ? '' : (segundos / 3600).toFixed(2);
    }

    function segundosParaTexto(totalSegundos) {
        const pad = (n) => String(n).padStart(2, '0');
        return `${pad(Math.floor(totalSegundos / 3600))}:${pad(Math.floor((totalSegundos % 3600) / 60))}:${pad(totalSegundos % 60)}`;
    }

    document.getElementById('cp-horas-contratadas')?.addEventListener('input', aplicarMascaraHoras);

    // -------------------------------------------------------------------------
    // 13. ABRIR EM MODO EDITAR/VISUALIZAR (a partir dos botões da tabela)
    // -------------------------------------------------------------------------
    const projetos = JSON.parse(overlay.dataset.projetos || '[]');

    function horasDecimalParaTexto(decimal) {
        return segundosParaTexto(Math.round(parseFloat(decimal) * 3600) || 0);
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

        // Passo 1 — Informações do Projeto
        const empresa = empresas.find(e => String(e.id) === String(projeto.empresa_id));
        clienteSelecionado = { id: projeto.empresa_id, nome: empresa ? (empresa.nome_fantasia || empresa.razao_social) : '' };
        if (clienteInput) clienteInput.value = clienteSelecionado.nome;
        atualizarLimparCliente();
        document.getElementById('cp-empresa-id').value = projeto.empresa_id ?? '';

        document.getElementById('cp-nome-projeto').value = projeto.nome ?? '';

        const lider = usuarios.find(u => String(u.id) === String(projeto.lider_id));
        if (lider) definirLider(lider.id, lider.nome);
        document.getElementById('cp-sigilo').value = projeto.nivel_sigilo ?? '';
        document.getElementById('cp-data-inicio').value = projeto.data_inicio ?? '';
        document.getElementById('cp-data-fim').value = projeto.data_fim_prevista ?? '';
        document.getElementById('cp-horas-contratadas').value = projeto.horas_contratadas
            ? horasDecimalParaTexto(projeto.horas_contratadas)
            : '';
        document.getElementById('cp-escopo').value = projeto.escopo ?? '';
        document.getElementById('cp-restricao').value = projeto.restricao ?? '';
        projetoTemContrato = !!projeto.contrato;
        atualizarContadores();

        (projeto.alvos || []).forEach(valor => alvos.push(valor));
        renderizarChipsAlvos();

        // Passo 2 — Informações do Pentest (N blocos)
        const pentestsProjeto = projeto.pentests || [];
        pentestsProjeto.forEach(p => {
            const bloco = adicionarBlocoPentest();

            bloco.tipoPentestId = p.tipo_pentest_id ? Number(p.tipo_pentest_id) : null;
            bloco.tipoInput.value = p.tipo_pentest_nome ?? '';
            bloco.modalidadeEl.value = p.modalidade ?? '';
            bloco.renderizarPreviaChecklist();

            bloco.raiz.querySelector('[data-campo="horas"] input').value = p.horas_contratadas
                ? horasDecimalParaTexto(p.horas_contratadas)
                : '';
            bloco.abordagemSelect.value = p.abordagem ?? '';
            bloco.ambienteSelect.value = p.ambiente ?? '';
            bloco.raiz.querySelector('[data-campo="escopo"] textarea').value = p.escopo ?? '';
            bloco.atualizarContadorEscopo();
            bloco.raiz.querySelector('[data-campo="referencia"] input').value = p.referencia ?? '';

            (p.frameworks_ids || []).forEach(idFw => {
                const fw = frameworks.find(f => String(f.id) === String(idFw));
                if (fw && !bloco.frameworksSelecionados.find(f => f.id === fw.id)) {
                    bloco.frameworksSelecionados.push({ id: fw.id, nome: fw.nome });
                }
            });
            bloco.renderizarChipsMetodologia();

            (p.analistas_ids || []).forEach(idUsuario => {
                const usuario = usuarios.find(u => String(u.id) === String(idUsuario));
                if (usuario && !bloco.equipe.find(m => m.id === usuario.id)) {
                    bloco.equipe.push({ id: usuario.id, nome: usuario.nome });
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

        registrarEstadoInicial();
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
    const formExcluir  = document.getElementById('form-excluir-projeto');
    const popupExcluir = document.getElementById('popupExcluirProjeto');

    document.querySelectorAll('.btn-excluir[data-projeto-id]').forEach(botao => {
        botao.addEventListener('click', () => {
            document.getElementById('excluir-projeto-id').value = botao.dataset.projetoId;
            document.getElementById('excluir-projeto-nome').textContent = botao.dataset.projetoNome || 'selecionado';
            popupExcluir?.classList.add('active');
        });
    });

    document.getElementById('confirmar-exclusao-projeto')?.addEventListener('click', () => {
        popupExcluir.classList.remove('active');
        formExcluir.submit();
    });

    // Inicialização: primeiro bloco de pentest já visível no cadastro, e
    // garante que os botões estejam corretos ao carregar.
    adicionarBlocoPentest();
    atualizarBotoes();
});
