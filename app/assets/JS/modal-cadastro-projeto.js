/**
 * modal-cadastro-projeto.js
 *
 * Responsabilidades:
 *  1. Stepper: avançar/voltar entre os 4 passos, atualizar marcadores visuais.
 *  2. Validação por passo: só avança se os campos obrigatórios estiverem preenchidos.
 *  3. Combobox de Cliente: filtra a lista de empresas embutida via data-attribute.
 *  4. Combobox de Tipo de Pentest (multi-select): cada seleção vira um chip;
 *     ao selecionar o primeiro tipo, preenche "Horas de execução".
 *  5. Chips de Alvos: input livre + botão adicionar, remover chip.
 *  6. Combobox de Líder Técnico: seleção única entre usuários.
 *  7. Chips de Analistas: busca na lista de usuários + botão adicionar, remover chip.
 *  8. Dropzone: drag & drop + click, exibe nome do arquivo selecionado.
 *  9. Revisão: ao entrar no passo 4, preenche todos os campos de revisão.
 * 10. Reset: ao fechar o modal, limpa todo o estado.
 */

document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------------------
    // 0. REFERÊNCIAS GLOBAIS
    // -------------------------------------------------------------------------
    const overlay = document.getElementById('modal-cadastro-projeto');
    if (!overlay) return; // Modal não existe nesta página

    // Dados do backend (serializados no data-attribute pelo PHP)
    const empresas      = JSON.parse(overlay.dataset.empresas      || '[]');
    const tiposPentest  = JSON.parse(overlay.dataset.tiposPentest  || '[]');
    const usuarios      = JSON.parse(overlay.dataset.usuarios       || '[]');

    // Estado do módulo
    let passoAtual      = 0;
    const TOTAL_PASSOS  = 4;
    let modoEdicaoOuVisualizacao = false; // true quando o modal foi aberto via Editar/Visualizar

    // IDs selecionados
    let clienteSelecionado    = { id: null, nome: '' };
    let liderSelecionado      = { id: null, nome: '' };
    const tiposSelecionados   = []; // [{ id, nome, horas_execucao }]
    const alvos               = []; // strings
    const analistasSelecionados = []; // [{ id, nome }]

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
        // Esconde passo atual
        passos[passoAtual].classList.remove('ativo');
        stepperItens[passoAtual].classList.remove('cad-projeto-stepper__item--ativo');

        // Marca passo anterior como concluído se estamos avançando
        if (novoPasso > passoAtual) {
            stepperItens[passoAtual].classList.add('cad-projeto-stepper__item--concluido');
        } else if (!modoEdicaoOuVisualizacao) {
            // Ao voltar, desconclui o passo que estávamos — só faz sentido no
            // cadastro (progresso real). Em editar/visualizar os 4 passos já
            // estão com dados válidos desde o início, então ficam marcados.
            stepperItens[novoPasso].classList.remove('cad-projeto-stepper__item--concluido');
        }

        passoAtual = novoPasso;

        // Ativa novo passo
        passos[passoAtual].classList.add('ativo');
        stepperItens[passoAtual].classList.add('cad-projeto-stepper__item--ativo');

        // Preenche revisão ao chegar no último passo
        if (passoAtual === 3) preencherRevisao();

        atualizarBotoes();

        // Scroll para o topo do body do modal ao mudar de passo
        passos[passoAtual].scrollTop = 0;
    }

    function atualizarBotoes() {
        // Voltar: oculto no passo 0
        btnVoltar.style.display = passoAtual === 0 ? 'none' : '';
        // Avançar: oculto no último passo
        btnAvancar.style.display = passoAtual === TOTAL_PASSOS - 1 ? 'none' : '';
        // Salvar: visível só no último passo
        btnSalvar.style.display = passoAtual === TOTAL_PASSOS - 1 ? '' : 'none';
    }

    btnAvancar.addEventListener('click', () => {
        if (!validarPasso(passoAtual)) return;
        if (passoAtual < TOTAL_PASSOS - 1) irParaPasso(passoAtual + 1);
    });

    btnVoltar.addEventListener('click', () => {
        if (passoAtual > 0) irParaPasso(passoAtual - 1);
    });

    // Clique direto no indicador do passo: pula pra lá sem validar o passo
    // atual (Avançar/Voltar continuam validando, pra não deixar pular campo
    // obrigatório no cadastro; clicar no stepper é navegação livre, útil
    // sobretudo ao editar/visualizar um projeto já preenchido).
    stepperItens.forEach((item, idx) => {
        item.style.cursor = 'pointer';
        item.addEventListener('click', () => {
            if (idx !== passoAtual) irParaPasso(idx);
        });
    });

    // -------------------------------------------------------------------------
    // 2. VALIDAÇÃO POR PASSO
    // -------------------------------------------------------------------------
    function marcarErro(campoId, erroId, condicaoErro) {
        const campo = document.getElementById(campoId)?.closest('.campo');
        const erro  = document.getElementById(erroId);
        if (!campo || !erro) return false;

        if (condicaoErro) {
            campo.classList.add('campo--erro');
            return true;
        } else {
            campo.classList.remove('campo--erro');
            return false;
        }
    }

    function validarPasso(passo) {
        let temErro = false;

        if (passo === 0) {
            // Passo 1: Cliente, Nome do Projeto, ao menos um tipo de pentest, modalidade, sigilo, horas
            if (!clienteSelecionado.id) {
                document.getElementById('campo-cliente')?.classList.add('campo--erro');
                temErro = true;
            } else {
                document.getElementById('campo-cliente')?.classList.remove('campo--erro');
            }

            const nomeProj = document.getElementById('cp-nome-projeto');
            if (!nomeProj.value.trim()) {
                nomeProj.closest('.campo').classList.add('campo--erro');
                temErro = true;
            } else {
                nomeProj.closest('.campo').classList.remove('campo--erro');
            }

            const campoTipo = document.getElementById('campo-tipo-pentest');
            if (tiposSelecionados.length === 0) {
                campoTipo?.classList.add('campo--erro');
                temErro = true;
            } else {
                campoTipo?.classList.remove('campo--erro');
            }

            const modalidade = document.getElementById('cp-modalidade');
            if (!modalidade.value) {
                modalidade.closest('.campo').classList.add('campo--erro');
                temErro = true;
            } else {
                modalidade.closest('.campo').classList.remove('campo--erro');
            }

            const sigilo = document.getElementById('cp-sigilo');
            if (!sigilo.value) {
                sigilo.closest('.campo').classList.add('campo--erro');
                temErro = true;
            } else {
                sigilo.closest('.campo').classList.remove('campo--erro');
            }

            const horas = document.getElementById('cp-horas-contratadas');
            if (!horas.value.trim() || !/^\d{1,3}:\d{2}:\d{2}$/.test(horas.value.trim())) {
                horas.closest('.campo').classList.add('campo--erro');
                temErro = true;
            } else {
                horas.closest('.campo').classList.remove('campo--erro');
            }
        }

        if (passo === 1) {
            // Passo 2: Escopo obrigatório
            const escopo = document.getElementById('cp-escopo');
            if (!escopo.value.trim()) {
                escopo.closest('.campo').classList.add('campo--erro');
                temErro = true;
            } else {
                escopo.closest('.campo').classList.remove('campo--erro');
            }
        }

        if (passo === 2) {
            // Passo 3: Líder técnico obrigatório
            if (!liderSelecionado.id) {
                document.getElementById('campo-lider')?.classList.add('campo--erro');
                temErro = true;
            } else {
                document.getElementById('campo-lider')?.classList.remove('campo--erro');
            }
        }

        return !temErro;
    }

    // -------------------------------------------------------------------------
    // 3. COMBOBOX GENÉRICO — fábrica reutilizada pelos 3 comboboxes
    //    Parâmetros:
    //      inputEl    — input de busca visível
    //      listaEl    — div da lista dropdown
    //      btnToggle  — botão seta (alternar)
    //      itens      — array de objetos { id, label, extra? }
    //      onSelect   — callback(item) chamado ao selecionar
    // -------------------------------------------------------------------------
    function criarCombobox(inputEl, listaEl, btnToggle, itens, onSelect) {
        let itemFocado = -1;
        const opcoes = [];

        function renderizar(filtro = '') {
            listaEl.innerHTML = '';
            opcoes.length = 0;
            itemFocado = -1;

            const filtroLower = filtro.toLowerCase();
            const resultados = itens.filter(i =>
                i.label.toLowerCase().includes(filtroLower)
            );

            if (resultados.length === 0) {
                listaEl.innerHTML = '<p class="campo__combobox-vazio">Nenhum resultado.</p>';
                return;
            }

            resultados.forEach((item, idx) => {
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

        // Eventos
        inputEl.addEventListener('input', () => {
            abrir();
        });

        inputEl.addEventListener('focus', () => {
            abrir();
        });

        btnToggle.addEventListener('click', () => {
            if (listaEl.hasAttribute('hidden')) {
                abrir();
                inputEl.focus();
            } else {
                fechar();
            }
        });

        // Teclado: setas + Enter + Escape
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

        // Fecha ao clicar fora
        document.addEventListener('click', (e) => {
            if (!inputEl.closest('.campo__combobox-campo').contains(e.target)) {
                fechar();
            }
        });

        return { abrir, fechar, renderizar };
    }

    // -------------------------------------------------------------------------
    // 4. COMBOBOX DE CLIENTE
    // -------------------------------------------------------------------------
    const clienteInput  = document.getElementById('cp-cliente-busca');
    const clienteLista  = document.getElementById('cp-cliente-lista');
    const clienteToggle = clienteInput?.nextElementSibling;

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
        });
    }

    // -------------------------------------------------------------------------
    // 5. COMBOBOX DE TIPO DE PENTEST (multi-select com chips)
    // -------------------------------------------------------------------------
    const tipoInput  = document.getElementById('cp-tipo-busca');
    const tipoLista  = document.getElementById('cp-tipo-lista');
    const tipoToggle = tipoInput?.nextElementSibling;
    const tiposChips = document.getElementById('cp-tipos-chips');
    const horasExec  = document.getElementById('cp-horas-execucao');

    if (tipoInput && tipoLista && tipoToggle) {
        const itensTipo = tiposPentest.map(t => ({
            id:            t.id,
            label:         t.nome,
            horas_execucao: t.horas_execucao ?? null,
        }));

        criarCombobox(tipoInput, tipoLista, tipoToggle, itensTipo, (item) => {
            // Evita duplicata
            if (tiposSelecionados.find(t => t.id === item.id)) {
                tipoInput.value = '';
                return;
            }

            tiposSelecionados.push(item);
            tipoInput.value = '';
            renderizarChipsTipo();

            // Preenche horas de execução com o primeiro tipo selecionado
            if (tiposSelecionados.length === 1 && item.horas_execucao) {
                horasExec.value = item.horas_execucao + ' Horas';
            }

            document.getElementById('campo-tipo-pentest')?.classList.remove('campo--erro');
        });
    }

    function renderizarChipsTipo() {
        if (!tiposChips) return;
        tiposChips.innerHTML = '';
        tiposSelecionados.forEach((item, idx) => {
            tiposChips.appendChild(criarChip(item.label, () => {
                tiposSelecionados.splice(idx, 1);
                renderizarChipsTipo();
                // Limpa horas se o primeiro tipo foi removido
                if (tiposSelecionados.length === 0) horasExec.value = '';
            }));
        });
    }

    // -------------------------------------------------------------------------
    // 6. CHIPS DE ALVOS
    // -------------------------------------------------------------------------
    const alvoInput = document.getElementById('cp-alvo-input');
    const alvoAdd   = document.getElementById('cp-alvo-add');
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
    // 7. COMBOBOX DE LÍDER TÉCNICO
    // -------------------------------------------------------------------------
    const liderInput  = document.getElementById('cp-lider-busca');
    const liderLista  = document.getElementById('cp-lider-lista');
    const liderToggle = liderInput?.nextElementSibling;

    if (liderInput && liderLista && liderToggle) {
        const itensLider = usuarios.map(u => ({
            id:    u.id,
            label: u.nome,
        }));

        criarCombobox(liderInput, liderLista, liderToggle, itensLider, (item) => {
            liderSelecionado = { id: item.id, nome: item.label };
            liderInput.value = item.label;
            document.getElementById('cp-lider-id').value = item.id;
            document.getElementById('campo-lider')?.classList.remove('campo--erro');
        });
    }

    // -------------------------------------------------------------------------
    // 8. CHIPS DE ANALISTAS (busca na lista de usuários)
    // -------------------------------------------------------------------------
    const analistaBusca  = document.getElementById('cp-analista-busca');
    const analistaAdd    = document.getElementById('cp-analista-add');
    const analistasChips = document.getElementById('cp-analistas-chips');

    function adicionarAnalista() {
        const query = analistaBusca?.value.trim().toLowerCase();
        if (!query) return;

        const encontrado = usuarios.find(u =>
            u.nome.toLowerCase().includes(query) &&
            !analistasSelecionados.find(a => a.id === u.id)
        );

        if (!encontrado) return;

        analistasSelecionados.push({ id: encontrado.id, nome: encontrado.nome });
        analistaBusca.value = '';
        renderizarChipsAnalistas();
    }

    analistaAdd?.addEventListener('click', adicionarAnalista);
    analistaBusca?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); adicionarAnalista(); }
    });

    function renderizarChipsAnalistas() {
        if (!analistasChips) return;
        analistasChips.innerHTML = '';
        analistasSelecionados.forEach((analista, idx) => {
            analistasChips.appendChild(criarChip(analista.nome, () => {
                analistasSelecionados.splice(idx, 1);
                renderizarChipsAnalistas();
            }));
        });
    }

    // -------------------------------------------------------------------------
    // 9. DROPZONE
    // -------------------------------------------------------------------------
    const dropzone     = document.getElementById('cp-dropzone');
    const contratoInput = document.getElementById('cp-contrato-input');
    const dropzoneTxt   = document.getElementById('cp-dropzone-texto');

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

    contratoInput?.addEventListener('change', () => {
        atualizarDropzone(contratoInput.files[0]);
    });

    dropzone?.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = 'var(--cor-azul-destaques)';
    });

    dropzone?.addEventListener('dragleave', () => {
        dropzone.style.borderColor = '';
    });

    dropzone?.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '';
        const arquivo = e.dataTransfer.files[0];
        if (arquivo) {
            // Atribui o arquivo ao input via DataTransfer
            const dt = new DataTransfer();
            dt.items.add(arquivo);
            contratoInput.files = dt.files;
            atualizarDropzone(arquivo);
        }
    });

    // -------------------------------------------------------------------------
    // 10. PREENCHER REVISÃO (passo 4)
    // -------------------------------------------------------------------------
    function preencherRevisao() {
        setText('rev-cliente',      clienteSelecionado.nome || '—');
        setText('rev-nome-projeto', document.getElementById('cp-nome-projeto')?.value || '—');
        setText('rev-sigilo',       document.getElementById('cp-sigilo')?.selectedOptions[0]?.text || '—');
        setText('rev-tipos',        tiposSelecionados.map(t => t.label).join(', ') || '—');

        const modalidadeEl = document.getElementById('cp-modalidade');
        setText('rev-modalidade', modalidadeEl?.selectedOptions[0]?.text || '—');

        setText('rev-escopo',   document.getElementById('cp-escopo')?.value || '—');
        setText('rev-alvos',    alvos.length ? alvos.join(', ') : '—');
        setText('rev-restricoes', document.getElementById('cp-restricao')?.value || '—');

        // Datas: formatar para pt-BR
        const dataInicio = document.getElementById('cp-data-inicio')?.value;
        const dataFim    = document.getElementById('cp-data-fim')?.value;
        setText('rev-data-inicio', dataInicio ? formatarData(dataInicio) : '—');
        setText('rev-data-fim',    dataFim    ? formatarData(dataFim)    : '—');
        setText('rev-horas',  document.getElementById('cp-horas-contratadas')?.value || '—');

        setText('rev-lider',     liderSelecionado.nome || '—');
        setText('rev-analistas', analistasSelecionados.map(a => a.nome).join(', ') || '—');
    }

    function setText(id, texto) {
        const el = document.getElementById(id);
        if (el) el.textContent = texto;
    }

    function formatarData(isoStr) {
        // Converte "YYYY-MM-DD" para "DD/MM/YYYY"
        if (!isoStr) return '—';
        const [y, m, d] = isoStr.split('-');
        return `${d}/${m}/${y}`;
    }

    // -------------------------------------------------------------------------
    // 11. SUBMIT — injeta campos dinâmicos antes de enviar
    // -------------------------------------------------------------------------
    const form = document.getElementById('form-cadastro-projeto');
    form?.addEventListener('submit', (e) => {
        if (!validarPasso(3)) { e.preventDefault(); return; }

        // Remove inputs dinâmicos anteriores para não duplicar
        form.querySelectorAll('[data-dinamico]').forEach(el => el.remove());

        // Alvos como array
        alvos.forEach(alvo => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'alvos[]';
            inp.value = alvo;
            inp.dataset.dinamico = '1';
            form.appendChild(inp);
        });

        // Tipos de pentest como array
        tiposSelecionados.forEach(tipo => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'tipos_pentest_ids[]';
            inp.value = tipo.id;
            inp.dataset.dinamico = '1';
            form.appendChild(inp);
        });

        // Analistas como array
        analistasSelecionados.forEach(analista => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'analistas_ids[]';
            inp.value = analista.id;
            inp.dataset.dinamico = '1';
            form.appendChild(inp);
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
    });

    // -------------------------------------------------------------------------
    // 12. RESET AO FECHAR O MODAL
    // -------------------------------------------------------------------------
    overlay.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', resetModal);
    });

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) resetModal();
    });

    function resetModal() {
        // Volta ao passo 0
        passos.forEach(p => p.classList.remove('ativo'));
        stepperItens.forEach(item => {
            item.classList.remove(
                'cad-projeto-stepper__item--ativo',
                'cad-projeto-stepper__item--concluido'
            );
        });
        passoAtual = 0;
        passos[0].classList.add('ativo');
        stepperItens[0].classList.add('cad-projeto-stepper__item--ativo');
        atualizarBotoes();

        // Limpa estado
        clienteSelecionado   = { id: null, nome: '' };
        liderSelecionado     = { id: null, nome: '' };
        tiposSelecionados.length = 0;
        alvos.length = 0;
        analistasSelecionados.length = 0;

        // Limpa chips renderizados
        if (tiposChips)    tiposChips.innerHTML    = '';
        if (alvosChips)    alvosChips.innerHTML    = '';
        if (analistasChips) analistasChips.innerHTML = '';

        // Limpa inputs
        form?.reset();
        if (clienteInput)  clienteInput.value  = '';
        if (tipoInput)     tipoInput.value     = '';
        if (horasExec)     horasExec.value     = '';
        if (liderInput)    liderInput.value    = '';
        if (dropzoneTxt)   dropzoneTxt.textContent = 'Arraste e solte seu arquivo aqui';

        // Limpa erros
        overlay.querySelectorAll('.campo--erro').forEach(el => el.classList.remove('campo--erro'));

        // Limpa inputs dinâmicos que foram injetados no submit
        form?.querySelectorAll('[data-dinamico]').forEach(el => el.remove());

        // Volta ao modo de cadastro (pode ter sido aberto em modo editar/visualizar)
        definirSomenteLeitura(false);
        modoEdicaoOuVisualizacao = false;
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
    // 13. UTILITÁRIO — criar chip reutilizável
    // -------------------------------------------------------------------------
    function criarChip(texto, onRemover) {
        const chip = document.createElement('span');
        chip.className = 'chip';
        chip.textContent = texto;

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
    // 14. MÁSCARA SIMPLES PARA CAMPO hh:mm:ss
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
    // 15. ABRIR EM MODO EDITAR/VISUALIZAR (a partir dos botões da tabela)
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

    function definirSomenteLeitura(somenteLeitura) {
        overlay.classList.toggle('modal--somente-leitura', somenteLeitura);

        overlay.querySelectorAll('.modal__body input, .modal__body textarea, .modal__body select, .modal__body button').forEach(el => {
            el.disabled = somenteLeitura;
        });

        if (somenteLeitura) {
            btnSalvar.style.display = 'none';
        } else {
            atualizarBotoes();
        }
    }

    function preencherModal(projeto, modo) {
        resetModal();

        document.getElementById('cp-action').value = 'editar';
        document.getElementById('cp-projeto-id').value = projeto.id;

        const tituloEl = document.getElementById('cadastro-projeto-titulo');
        if (tituloEl) tituloEl.textContent = modo === 'visualizar' ? 'Detalhes do Projeto' : 'Editar Projeto';

        // Passo 1 — Cadastro
        const empresa = empresas.find(e => String(e.id) === String(projeto.empresa_id));
        clienteSelecionado = { id: projeto.empresa_id, nome: empresa ? (empresa.nome_fantasia || empresa.razao_social) : '' };
        if (clienteInput) clienteInput.value = clienteSelecionado.nome;
        document.getElementById('cp-empresa-id').value = projeto.empresa_id ?? '';

        document.getElementById('cp-nome-projeto').value = projeto.nome ?? '';

        (projeto.tipos_pentest_ids || []).forEach(idTipo => {
            const tipo = tiposPentest.find(t => String(t.id) === String(idTipo));
            if (tipo && !tiposSelecionados.find(t => t.id === tipo.id)) {
                tiposSelecionados.push({ id: tipo.id, label: tipo.nome, horas_execucao: tipo.horas_execucao ?? null });
            }
        });
        renderizarChipsTipo();

        document.getElementById('cp-modalidade').value = projeto.modalidade ?? '';
        document.getElementById('cp-sigilo').value = projeto.nivel_sigilo ?? '';
        document.getElementById('cp-data-inicio').value = projeto.data_inicio ?? '';
        document.getElementById('cp-data-fim').value = projeto.data_fim_prevista ?? '';
        document.getElementById('cp-horas-contratadas').value = projeto.horas_contratadas
            ? horasDecimalParaTexto(projeto.horas_contratadas)
            : '';

        // Passo 2 — Dados do Projeto
        document.getElementById('cp-escopo').value = projeto.escopo ?? '';
        (projeto.alvos || []).forEach(valor => alvos.push(valor));
        renderizarChipsAlvos();
        document.getElementById('cp-restricao').value = projeto.restricao ?? '';

        // Passo 3 — Alocar Equipe
        if (projeto.lider_id) {
            const lider = usuarios.find(u => String(u.id) === String(projeto.lider_id));
            if (lider) {
                liderSelecionado = { id: lider.id, nome: lider.nome };
                if (liderInput) liderInput.value = lider.nome;
                document.getElementById('cp-lider-id').value = lider.id;
            }
        }
        (projeto.especialistas_ids || []).forEach(idUsuario => {
            const usuario = usuarios.find(u => String(u.id) === String(idUsuario));
            if (usuario && !analistasSelecionados.find(a => a.id === usuario.id)) {
                analistasSelecionados.push({ id: usuario.id, nome: usuario.nome });
            }
        });
        renderizarChipsAnalistas();

        definirSomenteLeitura(modo === 'visualizar');

        // Os 4 passos já têm dado válido desde a abertura — marca todos como
        // concluídos, exceto o que está sendo exibido agora (passo 0).
        modoEdicaoOuVisualizacao = true;
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
    // 16. EXCLUIR PROJETO (soft delete, com confirmação)
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

    // Inicialização: garante que os botões estejam corretos ao carregar
    atualizarBotoes();
});
