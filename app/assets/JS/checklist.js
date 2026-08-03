(() => {
    const categoriasChecklist = Array.isArray(window.categoriasChecklist)
        ? window.categoriasChecklist
        : [];

    const catalogoChecklist = Array.isArray(window.itensCatalogoChecklist)
        ? window.itensCatalogoChecklist
        : [];

    const checklistsExistentesChecklist = Array.isArray(window.checklistsExistentesChecklist)
        ? window.checklistsExistentesChecklist
        : [];

    const selecionadosChecklist = new Map();
    const gerenciadosChecklist = new Map();

    function elementoChecklist(id) {
        return document.getElementById(id);
    }

    function normalizarChecklist(valor) {
        return String(valor ?? '').trim().toLocaleLowerCase('pt-BR');
    }

    function escaparHtmlChecklist(valor) {
        return String(valor ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatarTempoChecklist(valorChecklist) {
        const totalMinutosChecklist = Math.round(Number(valorChecklist ?? 0));

        if (totalMinutosChecklist <= 0) {
            return '0h';
        }

        const horasChecklist = Math.floor(totalMinutosChecklist / 60);
        const minutosChecklist = totalMinutosChecklist % 60;

        if (horasChecklist === 0) {
            return `${minutosChecklist}min`;
        }

        if (minutosChecklist === 0) {
            return `${horasChecklist}h`;
        }

        return `${horasChecklist}h${String(minutosChecklist).padStart(2, '0')}`;
    }

    function confirmarChecklist(mensagemChecklist) {
        return confirm(mensagemChecklist);
    }

    function notificarChecklist(mensagemChecklist, tipoChecklist = 'info') {
        alert(mensagemChecklist);
    }

    function abrirModalChecklist(id) {
        elementoChecklist(id)?.classList.add('active');
    }

    function fecharModalChecklist(id) {
        elementoChecklist(id)?.classList.remove('active');
    }

    async function enviarChecklist(acao, dados = {}) {
        const formularioChecklist = new FormData();
        formularioChecklist.append('action', acao);

        Object.entries(dados).forEach(([chave, valor]) => {
            formularioChecklist.append(chave, valor);
        });

        const respostaChecklist = await fetch('checklist.php', {
            method: 'POST',
            body: formularioChecklist
        });

        if (!respostaChecklist.ok) {
            throw new Error('Erro na comunicação com o servidor.');
        }

        return respostaChecklist.json();
    }

    async function buscarDadosChecklist(id) {
        const resultadoChecklist = await enviarChecklist('buscarChecklist', { id });

        if (!resultadoChecklist.ok || !resultadoChecklist.checklist) {
            throw new Error('Checklist não encontrado.');
        }

        return resultadoChecklist.checklist;
    }

    async function alternarStatusChecklist(campoChecklist) {
        const grupoChecklist = campoChecklist.closest('.checklist-status');
        const textoChecklist = grupoChecklist?.querySelector('.checklist-status-texto');
        const linhaChecklist = campoChecklist.closest('tr');
        const habilitadoChecklist = campoChecklist.checked ? 1 : 0;

        try {
            const resultadoChecklist = await enviarChecklist('alterarStatusChecklist', {
                id: campoChecklist.dataset.id,
                habilitado: habilitadoChecklist
            });

            if (!resultadoChecklist.ok) {
                throw new Error('Status não alterado.');
            }

            if (textoChecklist) {
                textoChecklist.textContent = habilitadoChecklist ? 'Ativo' : 'Inativo';
            }

            linhaChecklist?.classList.toggle('linha-inativa', !campoChecklist.checked);
        } catch (erroChecklist) {
            console.error(erroChecklist);
            campoChecklist.checked = !campoChecklist.checked;

            if (textoChecklist) {
                textoChecklist.textContent = campoChecklist.checked ? 'Ativo' : 'Inativo';
            }

            linhaChecklist?.classList.toggle('linha-inativa', !campoChecklist.checked);

            notificarChecklist('Não foi possível alterar o status.', 'erro');
        }
    }

    async function visualizarChecklist(id) {
        try {
            const registroChecklist = await buscarDadosChecklist(id);
            const ativoChecklist = Number(registroChecklist.habilitado) === 1;
            const statusChecklist = elementoChecklist('checklist-visualizar-status');

            elementoChecklist('checklist-visualizar-id').value = registroChecklist.id;
            elementoChecklist('checklist-visualizar-nome').textContent =
                registroChecklist.nome || 'Não informado';
            elementoChecklist('checklist-visualizar-categoria').textContent =
                registroChecklist.categoria || 'Não informada';
            elementoChecklist('checklist-visualizar-descricao').textContent =
                registroChecklist.descricao || 'Nenhuma descrição informada.';

            statusChecklist.textContent = ativoChecklist ? 'Ativo' : 'Inativo';
            statusChecklist.className = ativoChecklist
                ? 'checklist-visualizacao-status checklist-status--ativo'
                : 'checklist-visualizacao-status checklist-status--inativo';

            if (Array.isArray(registroChecklist.itens)) {
                registroChecklist.itens.forEach(itemChecklist => {
                    atualizarItemCatalogoChecklist(itemChecklist, false);
                });
            }

            renderizarVisualizacaoChecklist(registroChecklist.itens);
            abrirModalChecklist('checklist-modal-visualizar');
        } catch (erroChecklist) {
            console.error(erroChecklist);
            notificarChecklist('Não foi possível visualizar o checklist.', 'erro');
        }
    }

    function renderizarVisualizacaoChecklist(itensChecklist) {
        const listaChecklist = elementoChecklist('checklist-visualizar-itens');
        const contadorChecklist = elementoChecklist('checklist-visualizar-total');
        const registrosChecklist = Array.isArray(itensChecklist) ? itensChecklist : [];

        const tempoTotalChecklist = registrosChecklist.reduce(
            (somaChecklist, itemChecklist) => somaChecklist + Number(itemChecklist.tempo_estimado_minutos ?? 0),
            0
        );

        const textoQuantidadeChecklist = registrosChecklist.length === 1
            ? '1 item'
            : `${registrosChecklist.length} itens`;

        contadorChecklist.textContent = tempoTotalChecklist > 0
            ? `${textoQuantidadeChecklist} · ~${formatarTempoChecklist(tempoTotalChecklist)}`
            : textoQuantidadeChecklist;

        if (registrosChecklist.length === 0) {
            listaChecklist.innerHTML = `
                <div class="checklist-visualizacao-vazio">
                    <i class="fa-regular fa-rectangle-list"></i>
                    <span>Nenhum item vinculado.</span>
                </div>
            `;
            return;
        }

        listaChecklist.innerHTML = registrosChecklist.map((itemChecklist, indiceChecklist) => {
            const obrigatorioChecklist = Number(itemChecklist.obrigatorio) === 1;

            const idChecklist = Number(itemChecklist.id);

            return `
                <div
                    class="checklist-visualizacao-item"
                    data-checklist-visualizacao-item-id="${idChecklist}"
                >
                    <span class="checklist-visualizacao-numero">${indiceChecklist + 1}</span>

                    <div class="checklist-visualizacao-texto">
                        <strong>${escaparHtmlChecklist(itemChecklist.titulo)}</strong>
                        <span>${escaparHtmlChecklist(itemChecklist.referencia || 'Sem referência')} · ~${formatarTempoChecklist(itemChecklist.tempo_estimado_minutos)}</span>
                    </div>

                    <div class="checklist-visualizacao-item-acoes">
                        <span class="checklist-visualizacao-tipo ${obrigatorioChecklist ? 'checklist-tag--obrigatorio' : 'checklist-tag--opcional'}">
                            ${obrigatorioChecklist ? 'Obrigatório' : 'Opcional'}
                        </span>

                        <button
                            type="button"
                            class="checklist-btn-visualizar"
                            data-checklist-visualizacao-item-visualizar="${idChecklist}"
                            title="Visualizar item"
                            aria-label="Visualizar item"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }

    function editarDaVisualizacaoChecklist() {
        const idChecklist = Number(elementoChecklist('checklist-visualizar-id').value);

        if (idChecklist <= 0) {
            notificarChecklist('Checklist não encontrado.', 'aviso');
            return;
        }

        fecharModalChecklist('checklist-modal-visualizar');
        editarChecklist(idChecklist);
    }

    async function editarChecklist(id) {
        try {
            const registroChecklist = await buscarDadosChecklist(id);

            elementoChecklist('checklist-id').value = registroChecklist.id;
            elementoChecklist('checklist-nome').value = registroChecklist.nome ?? '';
            elementoChecklist('checklist-categoria').value = registroChecklist.categoria ?? '';
            elementoChecklist('checklist-descricao').value = registroChecklist.descricao ?? '';
            elementoChecklist('checklist-titulo-modal').textContent = 'Editar Checklist';

            selecionadosChecklist.clear();
            gerenciadosChecklist.clear();

            if (Array.isArray(registroChecklist.itens)) {
                registroChecklist.itens.forEach(itemChecklist => {
                    selecionadosChecklist.set(Number(itemChecklist.id), itemChecklist);
                    atualizarItemCatalogoChecklist(itemChecklist, false);
                });
            }

            fecharCategoriasChecklist();
            fecharNomesChecklist();
            atualizarValidacaoNomeChecklist();
            contarDescricaoChecklist();
            renderizarSelecionadosChecklist();
            abrirModalChecklist('checklist-modal-formulario');
        } catch (erroChecklist) {
            console.error(erroChecklist);
            notificarChecklist('Erro ao carregar checklist para edição.', 'erro');
        }
    }

    function limparFormularioChecklist() {
        elementoChecklist('checklist-formulario')?.reset();
        elementoChecklist('checklist-id').value = '';
        elementoChecklist('checklist-titulo-modal').textContent = 'Cadastro de Checklist';

        selecionadosChecklist.clear();
        gerenciadosChecklist.clear();

        fecharCategoriasChecklist();
        fecharNomesChecklist();
        atualizarValidacaoNomeChecklist();
        contarDescricaoChecklist();
        renderizarSelecionadosChecklist();
    }

    function contarDescricaoChecklist() {
        const descricaoChecklist = elementoChecklist('checklist-descricao');
        const contadorChecklist = elementoChecklist('checklist-contador-descricao');

        if (descricaoChecklist && contadorChecklist) {
            contadorChecklist.textContent = `${descricaoChecklist.value.length} / 1000`;
        }
    }

    function contarDescricaoResumidaItemChecklist() {
        const descricaoChecklist = elementoChecklist('checklist-item-descricao-resumida');
        const contadorChecklist = elementoChecklist('checklist-item-contador-descricao-resumida');

        if (descricaoChecklist && contadorChecklist) {
            const totalChecklist = descricaoChecklist.value.length;
            contadorChecklist.textContent = totalChecklist === 1
                ? '1 caractere'
                : `${totalChecklist} caracteres`;
        }
    }

    function nomeCategoriaChecklist(categoriaChecklist) {
        return typeof categoriaChecklist === 'string'
            ? categoriaChecklist
            : categoriaChecklist?.nome ?? '';
    }

    function renderizarCategoriasChecklist(filtroChecklist = '') {
        const listaChecklist = elementoChecklist('checklist-lista-categorias');
        const termoChecklist = normalizarChecklist(filtroChecklist);
        const filtradasChecklist = categoriasChecklist.filter(categoriaChecklist =>
            normalizarChecklist(nomeCategoriaChecklist(categoriaChecklist)).includes(termoChecklist)
        );

        if (filtradasChecklist.length === 0) {
            listaChecklist.innerHTML = `
                <div class="checklist-categoria-vazio">
                    Nenhuma categoria encontrada. Você pode cadastrar uma nova ao salvar.
                </div>
            `;
            return;
        }

        listaChecklist.innerHTML = filtradasChecklist.map(categoriaChecklist => {
            const nomeChecklist = nomeCategoriaChecklist(categoriaChecklist);

            return `
                <button
                    type="button"
                    class="checklist-categoria-opcao"
                    data-checklist-categoria="${escaparHtmlChecklist(nomeChecklist)}"
                >
                    ${escaparHtmlChecklist(nomeChecklist)}
                </button>
            `;
        }).join('');
    }

    function abrirCategoriasChecklist(filtroChecklist = '') {
        const listaChecklist = elementoChecklist('checklist-lista-categorias');
        const campoChecklist = elementoChecklist('checklist-categoria');

        renderizarCategoriasChecklist(filtroChecklist);
        listaChecklist.hidden = false;
        campoChecklist.setAttribute('aria-expanded', 'true');
    }

    function fecharCategoriasChecklist() {
        const listaChecklist = elementoChecklist('checklist-lista-categorias');
        const campoChecklist = elementoChecklist('checklist-categoria');

        if (listaChecklist) {
            listaChecklist.hidden = true;
        }

        if (campoChecklist) {
            campoChecklist.setAttribute('aria-expanded', 'false');
        }
    }

    function idAtualChecklist() {
        return Number(elementoChecklist('checklist-id').value) || 0;
    }

    function checklistPorNomeChecklist(nomeChecklist) {
        const termoChecklist = normalizarChecklist(nomeChecklist);
        const idChecklist = idAtualChecklist();

        return checklistsExistentesChecklist.find(itemChecklist =>
            Number(itemChecklist.id) !== idChecklist
            && normalizarChecklist(itemChecklist.nome) === termoChecklist
        );
    }

    function nomeDuplicadoChecklist(nomeChecklist) {
        return normalizarChecklist(nomeChecklist) !== '' && Boolean(checklistPorNomeChecklist(nomeChecklist));
    }

    function atualizarValidacaoNomeChecklist() {
        const campoChecklist = elementoChecklist('checklist-nome');
        const duplicadoChecklist = nomeDuplicadoChecklist(campoChecklist.value);

        campoChecklist.closest('.campo')?.classList.toggle('campo--erro', duplicadoChecklist);
    }

    function renderizarNomesChecklist(filtroChecklist = '') {
        const listaChecklist = elementoChecklist('checklist-lista-nomes');
        const termoChecklist = normalizarChecklist(filtroChecklist);
        const idChecklist = idAtualChecklist();

        if (termoChecklist === '') {
            listaChecklist.hidden = true;
            return;
        }

        const encontradosChecklist = checklistsExistentesChecklist.filter(itemChecklist =>
            Number(itemChecklist.id) !== idChecklist
            && normalizarChecklist(itemChecklist.nome).includes(termoChecklist)
        );

        if (encontradosChecklist.length === 0) {
            listaChecklist.hidden = true;
            return;
        }

        listaChecklist.innerHTML = encontradosChecklist.map(itemChecklist => `
            <button
                type="button"
                class="checklist-categoria-opcao"
                data-checklist-nome-id="${Number(itemChecklist.id)}"
            >
                ${escaparHtmlChecklist(itemChecklist.nome)}
            </button>
        `).join('');

        listaChecklist.hidden = false;
        elementoChecklist('checklist-nome').setAttribute('aria-expanded', 'true');
    }

    function fecharNomesChecklist() {
        const listaChecklist = elementoChecklist('checklist-lista-nomes');
        const campoChecklist = elementoChecklist('checklist-nome');

        if (listaChecklist) {
            listaChecklist.hidden = true;
        }

        if (campoChecklist) {
            campoChecklist.setAttribute('aria-expanded', 'false');
        }
    }

    function abrirGerenciamentoItensChecklist() {
        gerenciadosChecklist.clear();
        selecionadosChecklist.forEach((itemChecklist, idChecklist) => {
            gerenciadosChecklist.set(idChecklist, itemChecklist);
        });

        elementoChecklist('checklist-pesquisa-itens').value = '';
        renderizarGerenciamentoChecklist();
        abrirModalChecklist('checklist-modal-gerenciar-itens');

        requestAnimationFrame(() => {
            elementoChecklist('checklist-pesquisa-itens')?.focus();
        });
    }

    function aplicarItensGerenciadosChecklist() {
        selecionadosChecklist.clear();
        gerenciadosChecklist.forEach((itemChecklist, idChecklist) => {
            selecionadosChecklist.set(idChecklist, itemChecklist);
        });

        renderizarSelecionadosChecklist();
        fecharModalChecklist('checklist-modal-gerenciar-itens');
    }

    function catalogoCompletoChecklist() {
        const itensChecklist = new Map();

        catalogoChecklist.forEach(itemChecklist => {
            itensChecklist.set(Number(itemChecklist.id), itemChecklist);
        });

        gerenciadosChecklist.forEach((itemChecklist, idChecklist) => {
            itensChecklist.set(idChecklist, itemChecklist);
        });

        return Array.from(itensChecklist.values());
    }

    function filtrarCatalogoChecklist(filtroChecklist = '') {
        const termoChecklist = normalizarChecklist(filtroChecklist);

        return catalogoCompletoChecklist()
            .filter(itemChecklist =>
                normalizarChecklist(itemChecklist.titulo).includes(termoChecklist)
                || normalizarChecklist(itemChecklist.referencia).includes(termoChecklist)
            )
            .sort((itemAChecklist, itemBChecklist) => {
                const inativoAChecklist = Number(itemAChecklist.habilitado) === 0;
                const inativoBChecklist = Number(itemBChecklist.habilitado) === 0;

                if (inativoAChecklist !== inativoBChecklist) {
                    return inativoAChecklist ? 1 : -1;
                }

                if (!inativoAChecklist) {
                    const obrigatorioAChecklist = Number(itemAChecklist.obrigatorio) === 1;
                    const obrigatorioBChecklist = Number(itemBChecklist.obrigatorio) === 1;

                    if (obrigatorioAChecklist !== obrigatorioBChecklist) {
                        return obrigatorioAChecklist ? -1 : 1;
                    }
                }

                return normalizarChecklist(itemAChecklist.titulo)
                    .localeCompare(normalizarChecklist(itemBChecklist.titulo), 'pt-BR');
            });
    }

    function renderizarGerenciamentoChecklist(filtroChecklist = '') {
        const listaChecklist = elementoChecklist('checklist-lista-gerenciamento');
        const itensChecklist = filtrarCatalogoChecklist(filtroChecklist);

        if (itensChecklist.length === 0) {
            listaChecklist.innerHTML = `
                <div class="checklist-gerenciar-vazio">
                    <i class="fa-regular fa-folder-open"></i>
                    <span>Nenhum item encontrado.</span>
                </div>
            `;
            atualizarContadorChecklist();
            return;
        }

        listaChecklist.innerHTML = itensChecklist.map(itemChecklist => {
            const idChecklist = Number(itemChecklist.id);
            const selecionadoChecklist = gerenciadosChecklist.has(idChecklist);
            const obrigatorioChecklist = Number(itemChecklist.obrigatorio) === 1;
            const inativoChecklist = Number(itemChecklist.habilitado) === 0;

            return `
                <div
                    class="checklist-gerenciar-card ${selecionadoChecklist ? 'checklist-card--selecionado' : ''} ${inativoChecklist ? 'checklist-gerenciar-card--inativo' : ''}"
                    data-checklist-item-id="${idChecklist}"
                >
                    <input
                        type="checkbox"
                        class="meu-checkbox"
                        data-checklist-item-checkbox="${idChecklist}"
                        ${selecionadoChecklist ? 'checked' : ''}
                        aria-label="Selecionar ${escaparHtmlChecklist(itemChecklist.titulo)}"
                    >

                    <div class="checklist-gerenciar-conteudo">
                        <div class="checklist-gerenciar-tags">
                            <span class="checklist-gerenciar-tag ${obrigatorioChecklist ? 'checklist-tag--obrigatorio' : 'checklist-tag--opcional'}">
                                ${obrigatorioChecklist ? 'Obrigatório' : 'Opcional'}
                            </span>
                            <span class="checklist-gerenciar-tag checklist-tag--opcional">
                                <i class="fa-regular fa-clock"></i>
                                ~${formatarTempoChecklist(itemChecklist.tempo_estimado_minutos)}
                            </span>
                            ${inativoChecklist ? '<span class="checklist-gerenciar-tag checklist-status--inativo">Inativo</span>' : ''}
                        </div>

                        <div class="checklist-gerenciar-titulo-linha">
                            <strong>${escaparHtmlChecklist(itemChecklist.titulo)}</strong>
                            ${itemChecklist.referencia ? `<span>${escaparHtmlChecklist(itemChecklist.referencia)}</span>` : ''}
                        </div>
                        <span>${escaparHtmlChecklist(itemChecklist.descricao_resumida || 'Sem descrição resumida')}</span>
                    </div>

                    <div class="checklist-gerenciar-acoes">
                        <button
                            type="button"
                            class="checklist-btn-visualizar"
                            data-checklist-item-visualizar="${idChecklist}"
                            title="Visualizar item"
                            aria-label="Visualizar item"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>

                        <button
                            type="button"
                            class="btn-editar"
                            data-checklist-item-editar="${idChecklist}"
                            title="Editar item"
                            aria-label="Editar item"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>

                        ${inativoChecklist ? `
                            <button
                                type="button"
                                class="checklist-btn-ativar"
                                data-checklist-item-ativar-rapido="${idChecklist}"
                                title="Ativar item"
                                aria-label="Ativar item"
                            >
                                <i class="fa-solid fa-circle-check"></i>
                            </button>
                        ` : `
                            <button
                                type="button"
                                class="btn-excluir"
                                data-checklist-item-remover="${idChecklist}"
                                title="Excluir ou desativar item"
                                aria-label="Excluir ou desativar item"
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        `}
                    </div>
                </div>
            `;
        }).join('');

        atualizarContadorChecklist();
    }

    function visualizarItemChecklist(idChecklist) {
        const itemChecklist = catalogoCompletoChecklist().find(
            registroChecklist => Number(registroChecklist.id) === idChecklist
        );

        if (!itemChecklist) {
            notificarChecklist('Item não encontrado.', 'aviso');
            return;
        }

        const ativoChecklist = Number(itemChecklist.habilitado) !== 0;

        elementoChecklist('checklist-item-visualizar-id').value = idChecklist;
        elementoChecklist('checklist-item-visualizar-titulo').textContent = itemChecklist.titulo;
        elementoChecklist('checklist-item-visualizar-referencia').textContent =
            itemChecklist.referencia || 'Sem referência';
        elementoChecklist('checklist-item-visualizar-tempo').textContent =
            `~${formatarTempoChecklist(itemChecklist.tempo_estimado_minutos)}`;
        elementoChecklist('checklist-item-visualizar-descricao').textContent =
            itemChecklist.descricao_resumida || 'Nenhuma descrição informada.';

        const statusChecklist = elementoChecklist('checklist-item-visualizar-status');
        statusChecklist.textContent = ativoChecklist ? 'Ativo' : 'Inativo';
        statusChecklist.className = ativoChecklist
            ? 'checklist-visualizacao-status checklist-status--ativo'
            : 'checklist-visualizacao-status checklist-status--inativo';

        const botaoAtivarChecklist = elementoChecklist('checklist-item-btn-ativar');
        botaoAtivarChecklist.dataset.statusAlvo = ativoChecklist ? '0' : '1';
        botaoAtivarChecklist.classList.toggle('btn-botao-verde', !ativoChecklist);
        botaoAtivarChecklist.classList.toggle('btn-vermelho', ativoChecklist);
        elementoChecklist('checklist-item-btn-ativar-texto').textContent =
            ativoChecklist ? 'DESATIVAR ITEM' : 'ATIVAR ITEM';
        elementoChecklist('checklist-item-btn-ativar-icone').className =
            ativoChecklist ? 'fa-solid fa-ban' : 'fa-solid fa-check';

        abrirModalChecklist('checklist-modal-item-visualizar');
    }

    async function definirStatusItemCatalogoChecklist(idChecklist, statusAlvoChecklist) {
        const mensagemConfirmacaoChecklist = statusAlvoChecklist === 1
            ? 'Tem certeza que deseja ativar este item?'
            : 'Tem certeza que deseja desativar este item?';

        if (!confirmarChecklist(mensagemConfirmacaoChecklist)) {
            return false;
        }

        try {
            const resultadoChecklist = await enviarChecklist('alterarStatusItemCatalogoChecklist', {
                id: idChecklist,
                habilitado: statusAlvoChecklist
            });

            if (!resultadoChecklist.ok) {
                throw new Error('Status não alterado.');
            }

            const itemChecklist = catalogoCompletoChecklist().find(
                registroChecklist => Number(registroChecklist.id) === idChecklist
            );

            if (itemChecklist) {
                atualizarItemCatalogoChecklist({ ...itemChecklist, habilitado: statusAlvoChecklist }, false);
            }

            renderizarGerenciamentoChecklist(elementoChecklist('checklist-pesquisa-itens').value);
            renderizarSelecionadosChecklist();
            notificarChecklist(
                statusAlvoChecklist === 1 ? 'Item ativado com sucesso.' : 'Item desativado com sucesso.',
                'sucesso'
            );
            return true;
        } catch (erroChecklist) {
            console.error(erroChecklist);
            notificarChecklist('Não foi possível alterar o status do item.', 'erro');
            return false;
        }
    }

    async function alternarStatusItemCatalogoChecklist() {
        const idChecklist = Number(elementoChecklist('checklist-item-visualizar-id').value);
        const botaoAtivarChecklist = elementoChecklist('checklist-item-btn-ativar');
        const statusAlvoChecklist = Number(botaoAtivarChecklist.dataset.statusAlvo ?? 1);

        if (idChecklist <= 0) {
            return;
        }

        const sucessoChecklist = await definirStatusItemCatalogoChecklist(idChecklist, statusAlvoChecklist);

        if (sucessoChecklist) {
            fecharModalChecklist('checklist-modal-item-visualizar');
        }
    }

    async function ativarItemRapidoChecklist(idChecklist) {
        await definirStatusItemCatalogoChecklist(idChecklist, 1);
    }

    function alternarGerenciadoChecklist(idChecklist, marcadoChecklist) {
        const itemChecklist = catalogoCompletoChecklist().find(
            registroChecklist => Number(registroChecklist.id) === idChecklist
        );

        if (!itemChecklist) {
            return;
        }

        if (marcadoChecklist) {
            gerenciadosChecklist.set(idChecklist, itemChecklist);

            const jaExisteChecklist = catalogoChecklist.some(
                registroChecklist => Number(registroChecklist.id) === idChecklist
            );

            if (!jaExisteChecklist) {
                catalogoChecklist.push(itemChecklist);
            }
        } else {
            gerenciadosChecklist.delete(idChecklist);
        }

        atualizarContadorChecklist();
    }

    function atualizarContadorChecklist() {
        const totalChecklist = gerenciadosChecklist.size;
        elementoChecklist('checklist-contador-selecionados').textContent =
            totalChecklist === 1 ? '1 selecionado' : `${totalChecklist} selecionados`;
    }

    function atualizarTempoTotalChecklist() {
        const resumoChecklist = elementoChecklist('checklist-tempo-resumo');
        const totalChecklist = elementoChecklist('checklist-tempo-total');

        const tempoTotalChecklist = Array.from(selecionadosChecklist.values()).reduce(
            (somaChecklist, itemChecklist) => somaChecklist + Number(itemChecklist.tempo_estimado_minutos ?? 0),
            0
        );

        if (resumoChecklist) {
            resumoChecklist.hidden = selecionadosChecklist.size === 0;
        }

        if (totalChecklist) {
            totalChecklist.textContent = formatarTempoChecklist(tempoTotalChecklist);
        }
    }

    function ordemAtualChecklist() {
        return Array.from(selecionadosChecklist.keys());
    }

    function reordenarSelecionadosChecklist(novaOrdemChecklist) {
        const mapaAtualChecklist = new Map(selecionadosChecklist);
        selecionadosChecklist.clear();

        novaOrdemChecklist.forEach(idChecklist => {
            if (mapaAtualChecklist.has(idChecklist)) {
                selecionadosChecklist.set(idChecklist, mapaAtualChecklist.get(idChecklist));
            }
        });
    }

    function renderizarSelecionadosChecklist() {
        const listaChecklist = elementoChecklist('checklist-lista-selecionados');

        atualizarTempoTotalChecklist();

        if (selecionadosChecklist.size === 0) {
            listaChecklist.innerHTML = `
                <div class="checklist-itens-vazio">
                    <i class="fa-regular fa-rectangle-list"></i>
                    <div>
                        <strong>Nenhum item selecionado</strong>
                        <span>Use o botão “Gerenciar itens” para adicionar.</span>
                    </div>
                </div>
            `;
            return;
        }

        const itensChecklist = Array.from(selecionadosChecklist.values());

        listaChecklist.innerHTML = itensChecklist.map((itemChecklist, indiceChecklist) => {
            const idChecklist = Number(itemChecklist.id);

            return `
                <div
                    class="checklist-item-selecionado"
                    draggable="true"
                    data-checklist-selecionado-id="${idChecklist}"
                >
                    <span class="checklist-item-arrastar" title="Arraste para reordenar">
                        <i class="fa-solid fa-grip-vertical"></i>
                    </span>

                    <span class="checklist-visualizacao-numero">${indiceChecklist + 1}</span>

                    <div class="checklist-item-texto">
                        <strong>${escaparHtmlChecklist(itemChecklist.titulo)}</strong>
                        <span>${escaparHtmlChecklist(itemChecklist.referencia || 'Sem referência')} · ~${formatarTempoChecklist(itemChecklist.tempo_estimado_minutos)}</span>
                    </div>

                    <input type="hidden" name="itens_ids[]" value="${idChecklist}">

                    <button
                        type="button"
                        class="checklist-btn-visualizar"
                        data-checklist-selecionado-visualizar="${idChecklist}"
                        title="Visualizar item"
                        aria-label="Visualizar item"
                    >
                        <i class="fa-solid fa-eye"></i>
                    </button>

                    <button
                        type="button"
                        class="btn-excluir"
                        data-checklist-selecionado-remover="${idChecklist}"
                        title="Remover item do checklist"
                        aria-label="Remover item do checklist"
                    >
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            `;
        }).join('');
    }

    function abrirNovoItemChecklist() {
        elementoChecklist('checklist-item-id').value = '';
        elementoChecklist('checklist-item-titulo').value = '';
        elementoChecklist('checklist-item-referencia').value = '';
        elementoChecklist('checklist-item-obrigatorio').value = '1';
        elementoChecklist('checklist-item-descricao-resumida').value = '';
        elementoChecklist('checklist-item-tempo-estimado').value = '';
        elementoChecklist('checklist-titulo-modal-item').textContent = 'Cadastro de Item';
        elementoChecklist('checklist-subtitulo-modal-item').textContent =
            'Cadastre um novo item reutilizável';
        elementoChecklist('checklist-btn-salvar-item').textContent = 'SALVAR';
        contarDescricaoResumidaItemChecklist();

        abrirModalChecklist('checklist-modal-item');

        requestAnimationFrame(() => {
            elementoChecklist('checklist-item-titulo')?.focus();
        });
    }

    async function editarItemChecklist(idChecklist) {
        try {
            const resultadoChecklist = await enviarChecklist(
                'buscarItemCatalogoChecklist',
                { id: idChecklist }
            );

            if (!resultadoChecklist.ok || !resultadoChecklist.item) {
                throw new Error('Item não encontrado.');
            }

            const itemChecklist = resultadoChecklist.item;

            elementoChecklist('checklist-item-id').value = itemChecklist.id;
            elementoChecklist('checklist-item-titulo').value = itemChecklist.titulo ?? '';
            elementoChecklist('checklist-item-referencia').value = itemChecklist.referencia ?? '';
            elementoChecklist('checklist-item-obrigatorio').value =
                Number(itemChecklist.obrigatorio) === 1 ? '1' : '0';
            elementoChecklist('checklist-item-descricao-resumida').value =
                itemChecklist.descricao_resumida ?? '';
            elementoChecklist('checklist-item-tempo-estimado').value =
                Number(itemChecklist.tempo_estimado_minutos ?? 0) || '';
            elementoChecklist('checklist-titulo-modal-item').textContent = 'Editar Item';
            elementoChecklist('checklist-subtitulo-modal-item').textContent =
                'Altere os dados do item reutilizável';
            elementoChecklist('checklist-btn-salvar-item').textContent = 'SALVAR ALTERAÇÕES';
            contarDescricaoResumidaItemChecklist();

            abrirModalChecklist('checklist-modal-item');
        } catch (erroChecklist) {
            console.error(erroChecklist);
            notificarChecklist('Não foi possível carregar o item.', 'erro');
        }
    }

    async function salvarItemCatalogoChecklist() {
        const idChecklist = Number(elementoChecklist('checklist-item-id').value);
        const tituloChecklist = elementoChecklist('checklist-item-titulo').value.trim();
        const referenciaChecklist = elementoChecklist('checklist-item-referencia').value.trim();
        const obrigatorioChecklist = elementoChecklist('checklist-item-obrigatorio').value;
        const descricaoResumidaChecklist = elementoChecklist('checklist-item-descricao-resumida').value.trim();
        const tempoEstimadoChecklist = Number(elementoChecklist('checklist-item-tempo-estimado').value) || 0;

        if (tituloChecklist === '') {
            notificarChecklist('Informe o título do item.', 'aviso');
            elementoChecklist('checklist-item-titulo').focus();
            return;
        }

        if (tempoEstimadoChecklist < 0) {
            notificarChecklist('O tempo estimado não pode ser negativo.', 'aviso');
            elementoChecklist('checklist-item-tempo-estimado').focus();
            return;
        }

        try {
            const novoChecklist = idChecklist <= 0;
            const acaoChecklist = novoChecklist
                ? 'cadastrarItemCatalogoChecklist'
                : 'atualizarItemCatalogoChecklist';
            const dadosChecklist = {
                titulo: tituloChecklist,
                referencia: referenciaChecklist,
                obrigatorio: obrigatorioChecklist,
                descricao_resumida: descricaoResumidaChecklist,
                tempo_estimado_minutos: tempoEstimadoChecklist
            };

            if (!novoChecklist) {
                dadosChecklist.id = idChecklist;
            }

            const resultadoChecklist = await enviarChecklist(acaoChecklist, dadosChecklist);

            if (!resultadoChecklist.ok || !resultadoChecklist.item) {
                notificarChecklist(resultadoChecklist.mensagem || 'Não foi possível salvar o item.', 'erro');
                return;
            }

            atualizarItemCatalogoChecklist(resultadoChecklist.item, novoChecklist);
            fecharModalChecklist('checklist-modal-item');
            renderizarGerenciamentoChecklist(elementoChecklist('checklist-pesquisa-itens').value);
            renderizarSelecionadosChecklist();
            notificarChecklist(resultadoChecklist.mensagem, 'sucesso');
        } catch (erroChecklist) {
            console.error(erroChecklist);
            notificarChecklist('Erro ao salvar item.', 'erro');
        }
    }

    function atualizarItemCatalogoChecklist(itemChecklist, selecionarChecklist) {
        const idChecklist = Number(itemChecklist.id);
        const indiceChecklist = catalogoChecklist.findIndex(
            registroChecklist => Number(registroChecklist.id) === idChecklist
        );

        if (indiceChecklist === -1) {
            catalogoChecklist.push(itemChecklist);
        } else {
            catalogoChecklist[indiceChecklist] = itemChecklist;
        }

        if (selecionarChecklist || gerenciadosChecklist.has(idChecklist)) {
            gerenciadosChecklist.set(idChecklist, itemChecklist);
        }

        if (selecionadosChecklist.has(idChecklist)) {
            selecionadosChecklist.set(idChecklist, itemChecklist);
        }
    }

    async function removerItemCatalogoChecklist(idChecklist) {
        const confirmouChecklist = confirmarChecklist(
            'Deseja remover este item? Se estiver sendo utilizado em algum checklist, ele será apenas desativado.'
        );

        if (!confirmouChecklist) {
            return;
        }

        try {
            const resultadoChecklist = await enviarChecklist(
                'removerItemCatalogoChecklist',
                { id: idChecklist }
            );

            if (!resultadoChecklist.ok) {
                notificarChecklist(resultadoChecklist.mensagem || 'Não foi possível remover o item.', 'erro');
                return;
            }

            const indiceChecklist = catalogoChecklist.findIndex(
                itemChecklist => Number(itemChecklist.id) === idChecklist
            );

            if (indiceChecklist !== -1) {
                catalogoChecklist.splice(indiceChecklist, 1);
            }

            gerenciadosChecklist.delete(idChecklist);
            selecionadosChecklist.delete(idChecklist);

            renderizarGerenciamentoChecklist(elementoChecklist('checklist-pesquisa-itens').value);
            renderizarSelecionadosChecklist();
            notificarChecklist(resultadoChecklist.mensagem, 'sucesso');
        } catch (erroChecklist) {
            console.error(erroChecklist);
            notificarChecklist('Erro ao remover item.', 'erro');
        }
    }

    function configurarEventosChecklist() {
        elementoChecklist('checklist-categoria')?.addEventListener('input', eventoChecklist => {
            abrirCategoriasChecklist(eventoChecklist.target.value);
        });

        elementoChecklist('checklist-categoria')?.addEventListener('focus', eventoChecklist => {
            abrirCategoriasChecklist(eventoChecklist.target.value);
        });

        elementoChecklist('checklist-categoria')?.addEventListener('keydown', eventoChecklist => {
            if (eventoChecklist.key === 'Escape') {
                fecharCategoriasChecklist();
            }
        });

        elementoChecklist('checklist-btn-categorias')?.addEventListener('click', eventoChecklist => {
            eventoChecklist.stopPropagation();
            const listaChecklist = elementoChecklist('checklist-lista-categorias');

            if (listaChecklist.hidden) {
                abrirCategoriasChecklist('');
            } else {
                fecharCategoriasChecklist();
            }
        });

        elementoChecklist('checklist-lista-categorias')?.addEventListener('click', eventoChecklist => {
            const opcaoChecklist = eventoChecklist.target.closest('[data-checklist-categoria]');

            if (!opcaoChecklist) {
                return;
            }

            elementoChecklist('checklist-categoria').value = opcaoChecklist.dataset.checklistCategoria;
            fecharCategoriasChecklist();
        });

        elementoChecklist('checklist-visualizar-itens')?.addEventListener('click', eventoChecklist => {
            const botaoChecklist = eventoChecklist.target.closest('[data-checklist-visualizacao-item-visualizar]');
            const itemChecklist = eventoChecklist.target.closest('[data-checklist-visualizacao-item-id]');

            if (botaoChecklist) {
                visualizarItemChecklist(Number(botaoChecklist.dataset.checklistVisualizacaoItemVisualizar));
                return;
            }

            if (itemChecklist) {
                visualizarItemChecklist(Number(itemChecklist.dataset.checklistVisualizacaoItemId));
            }
        });

        elementoChecklist('checklist-nome')?.addEventListener('input', eventoChecklist => {
            renderizarNomesChecklist(eventoChecklist.target.value);
            atualizarValidacaoNomeChecklist();
        });

        elementoChecklist('checklist-nome')?.addEventListener('focus', eventoChecklist => {
            renderizarNomesChecklist(eventoChecklist.target.value);
        });

        elementoChecklist('checklist-nome')?.addEventListener('keydown', eventoChecklist => {
            if (eventoChecklist.key === 'Escape') {
                fecharNomesChecklist();
            }
        });

        elementoChecklist('checklist-lista-nomes')?.addEventListener('click', eventoChecklist => {
            const opcaoChecklist = eventoChecklist.target.closest('[data-checklist-nome-id]');

            if (!opcaoChecklist) {
                return;
            }

            fecharNomesChecklist();

            if (!confirmarChecklist(
                'Já existe um checklist com esse nome. Abrir ele para edição vai descartar o que '
                + 'você digitou neste formulário. Quer continuar?'
            )) {
                return;
            }

            editarChecklist(Number(opcaoChecklist.dataset.checklistNomeId));
        });

        elementoChecklist('checklist-pesquisa-itens')?.addEventListener('input', eventoChecklist => {
            renderizarGerenciamentoChecklist(eventoChecklist.target.value);
        });

        elementoChecklist('checklist-lista-gerenciamento')?.addEventListener('click', eventoChecklist => {
            const visualizarChecklistBotao = eventoChecklist.target.closest('[data-checklist-item-visualizar]');
            const editarChecklistBotao = eventoChecklist.target.closest('[data-checklist-item-editar]');
            const removerChecklistBotao = eventoChecklist.target.closest('[data-checklist-item-remover]');
            const ativarRapidoChecklistBotao = eventoChecklist.target.closest('[data-checklist-item-ativar-rapido]');
            const checkboxChecklist = eventoChecklist.target.closest('[data-checklist-item-checkbox]');
            const cardChecklist = eventoChecklist.target.closest('[data-checklist-item-id]');

            if (visualizarChecklistBotao) {
                visualizarItemChecklist(Number(visualizarChecklistBotao.dataset.checklistItemVisualizar));
                return;
            }

            if (editarChecklistBotao) {
                editarItemChecklist(Number(editarChecklistBotao.dataset.checklistItemEditar));
                return;
            }

            if (removerChecklistBotao) {
                removerItemCatalogoChecklist(Number(removerChecklistBotao.dataset.checklistItemRemover));
                return;
            }

            if (ativarRapidoChecklistBotao) {
                ativarItemRapidoChecklist(Number(ativarRapidoChecklistBotao.dataset.checklistItemAtivarRapido));
                return;
            }

            if (!cardChecklist) {
                return;
            }

            const idChecklist = Number(cardChecklist.dataset.checklistItemId);
            const campoChecklist = cardChecklist.querySelector('[data-checklist-item-checkbox]');

            if (!checkboxChecklist) {
                campoChecklist.checked = !campoChecklist.checked;
            }

            alternarGerenciadoChecklist(idChecklist, campoChecklist.checked);
            cardChecklist.classList.toggle('checklist-card--selecionado', campoChecklist.checked);
        });

        elementoChecklist('checklist-lista-selecionados')?.addEventListener('click', eventoChecklist => {
            const removerChecklist = eventoChecklist.target.closest('[data-checklist-selecionado-remover]');
            const visualizarChecklistBotao = eventoChecklist.target.closest('[data-checklist-selecionado-visualizar]');
            const cardChecklist = eventoChecklist.target.closest('[data-checklist-selecionado-id]');

            if (removerChecklist) {
                selecionadosChecklist.delete(Number(removerChecklist.dataset.checklistSelecionadoRemover));
                renderizarSelecionadosChecklist();
                return;
            }

            if (visualizarChecklistBotao) {
                visualizarItemChecklist(Number(visualizarChecklistBotao.dataset.checklistSelecionadoVisualizar));
                return;
            }

            if (cardChecklist) {
                visualizarItemChecklist(Number(cardChecklist.dataset.checklistSelecionadoId));
            }
        });

        let arrastandoIdChecklist = null;

        function limparIndicadoresArrastarChecklist() {
            elementoChecklist('checklist-lista-selecionados')
                ?.querySelectorAll('.checklist-item-selecionado--arrastar-antes, .checklist-item-selecionado--arrastar-depois')
                .forEach(itemChecklist => {
                    itemChecklist.classList.remove(
                        'checklist-item-selecionado--arrastar-antes',
                        'checklist-item-selecionado--arrastar-depois'
                    );
                });
        }

        elementoChecklist('checklist-lista-selecionados')?.addEventListener('dragstart', eventoChecklist => {
            const itemChecklist = eventoChecklist.target.closest('[data-checklist-selecionado-id]');

            if (!itemChecklist) {
                return;
            }

            arrastandoIdChecklist = Number(itemChecklist.dataset.checklistSelecionadoId);
            itemChecklist.classList.add('checklist-item-arrastando');
            eventoChecklist.dataTransfer.effectAllowed = 'move';
        });

        elementoChecklist('checklist-lista-selecionados')?.addEventListener('dragend', eventoChecklist => {
            eventoChecklist.target.closest('[data-checklist-selecionado-id]')
                ?.classList.remove('checklist-item-arrastando');
            limparIndicadoresArrastarChecklist();
            arrastandoIdChecklist = null;
        });

        elementoChecklist('checklist-lista-selecionados')?.addEventListener('dragover', eventoChecklist => {
            if (arrastandoIdChecklist === null) {
                return;
            }

            eventoChecklist.preventDefault();
            eventoChecklist.dataTransfer.dropEffect = 'move';

            const alvoChecklist = eventoChecklist.target.closest('[data-checklist-selecionado-id]');

            limparIndicadoresArrastarChecklist();

            if (!alvoChecklist || Number(alvoChecklist.dataset.checklistSelecionadoId) === arrastandoIdChecklist) {
                return;
            }

            const retanguloChecklist = alvoChecklist.getBoundingClientRect();
            const depoisChecklist = eventoChecklist.clientY > retanguloChecklist.top + retanguloChecklist.height / 2;

            alvoChecklist.classList.add(
                depoisChecklist
                    ? 'checklist-item-selecionado--arrastar-depois'
                    : 'checklist-item-selecionado--arrastar-antes'
            );
        });

        elementoChecklist('checklist-lista-selecionados')?.addEventListener('drop', eventoChecklist => {
            if (arrastandoIdChecklist === null) {
                return;
            }

            eventoChecklist.preventDefault();
            limparIndicadoresArrastarChecklist();

            const alvoChecklist = eventoChecklist.target.closest('[data-checklist-selecionado-id]');

            if (!alvoChecklist) {
                return;
            }

            const idAlvoChecklist = Number(alvoChecklist.dataset.checklistSelecionadoId);

            if (idAlvoChecklist === arrastandoIdChecklist) {
                return;
            }

            const retanguloChecklist = alvoChecklist.getBoundingClientRect();
            const depoisChecklist = eventoChecklist.clientY > retanguloChecklist.top + retanguloChecklist.height / 2;

            const ordemChecklist = ordemAtualChecklist()
                .filter(idChecklist => idChecklist !== arrastandoIdChecklist);
            const indiceAlvoChecklist = ordemChecklist.indexOf(idAlvoChecklist);

            ordemChecklist.splice(
                depoisChecklist ? indiceAlvoChecklist + 1 : indiceAlvoChecklist,
                0,
                arrastandoIdChecklist
            );

            reordenarSelecionadosChecklist(ordemChecklist);
            renderizarSelecionadosChecklist();
        });

        elementoChecklist('checklist-formulario')?.addEventListener('submit', eventoChecklist => {
            if (selecionadosChecklist.size === 0) {
                eventoChecklist.preventDefault();
                notificarChecklist('Selecione pelo menos um item para o checklist.', 'aviso');
                return;
            }

            if (nomeDuplicadoChecklist(elementoChecklist('checklist-nome').value)) {
                eventoChecklist.preventDefault();
                atualizarValidacaoNomeChecklist();
                notificarChecklist('Já existe um checklist com esse nome. Escolha outro nome ou edite o checklist existente.', 'aviso');
            }
        });

        document.addEventListener('click', eventoChecklist => {
            if (!eventoChecklist.target.closest('.checklist-categoria-combobox')) {
                fecharCategoriasChecklist();
            }

            if (!eventoChecklist.target.closest('.checklist-nome-combobox')) {
                fecharNomesChecklist();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        configurarEventosChecklist();
        contarDescricaoChecklist();
        renderizarSelecionadosChecklist();
    });

    window.confirmarChecklist = confirmarChecklist;
    window.notificarChecklist = notificarChecklist;
    window.alternarStatusChecklist = alternarStatusChecklist;
    window.visualizarChecklist = visualizarChecklist;
    window.editarChecklist = editarChecklist;
    window.editarDaVisualizacaoChecklist = editarDaVisualizacaoChecklist;
    window.limparFormularioChecklist = limparFormularioChecklist;
    window.contarDescricaoChecklist = contarDescricaoChecklist;
    window.contarDescricaoResumidaItemChecklist = contarDescricaoResumidaItemChecklist;
    window.abrirGerenciamentoItensChecklist = abrirGerenciamentoItensChecklist;
    window.aplicarItensGerenciadosChecklist = aplicarItensGerenciadosChecklist;
    window.abrirNovoItemChecklist = abrirNovoItemChecklist;
    window.salvarItemCatalogoChecklist = salvarItemCatalogoChecklist;
    window.alternarStatusItemCatalogoChecklist = alternarStatusItemCatalogoChecklist;
})();

