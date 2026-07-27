(() => {
    const categoriasChecklist = Array.isArray(window.categoriasChecklist)
        ? window.categoriasChecklist
        : [];
 
    const catalogoChecklist = Array.isArray(window.itensCatalogoChecklist)
        ? window.itensCatalogoChecklist
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
        } catch (erroChecklist) {
            console.error(erroChecklist);
            campoChecklist.checked = !campoChecklist.checked;
 
            if (textoChecklist) {
                textoChecklist.textContent = campoChecklist.checked ? 'Ativo' : 'Inativo';
            }
 
            alert('Não foi possível alterar o status.');
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
 
            renderizarVisualizacaoChecklist(registroChecklist.itens);
            abrirModalChecklist('checklist-modal-visualizar');
        } catch (erroChecklist) {
            console.error(erroChecklist);
            alert('Não foi possível visualizar o checklist.');
        }
    }
 
    function renderizarVisualizacaoChecklist(itensChecklist) {
        const listaChecklist = elementoChecklist('checklist-visualizar-itens');
        const contadorChecklist = elementoChecklist('checklist-visualizar-total');
        const registrosChecklist = Array.isArray(itensChecklist) ? itensChecklist : [];
 
        contadorChecklist.textContent = registrosChecklist.length === 1
            ? '1 item'
            : `${registrosChecklist.length} itens`;
 
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
 
            return `
                <div class="checklist-visualizacao-item">
                    <span class="checklist-visualizacao-numero">${indiceChecklist + 1}</span>
 
                    <div class="checklist-visualizacao-texto">
                        <strong>${escaparHtmlChecklist(itemChecklist.titulo)}</strong>
                        <span>${escaparHtmlChecklist(itemChecklist.referencia || 'Sem referência')}</span>
                    </div>
 
                    <span class="checklist-visualizacao-tipo ${obrigatorioChecklist ? 'checklist-tag--obrigatorio' : 'checklist-tag--opcional'}">
                        ${obrigatorioChecklist ? 'Obrigatório' : 'Opcional'}
                    </span>
                </div>
            `;
        }).join('');
    }
 
    function editarDaVisualizacaoChecklist() {
        const idChecklist = Number(elementoChecklist('checklist-visualizar-id').value);
 
        if (idChecklist <= 0) {
            alert('Checklist não encontrado.');
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
                });
            }
 
            fecharCategoriasChecklist();
            contarDescricaoChecklist();
            renderizarSelecionadosChecklist();
            abrirModalChecklist('checklist-modal-formulario');
        } catch (erroChecklist) {
            console.error(erroChecklist);
            alert('Erro ao carregar checklist para edição.');
        }
    }
 
    function limparFormularioChecklist() {
        elementoChecklist('checklist-formulario')?.reset();
        elementoChecklist('checklist-id').value = '';
        elementoChecklist('checklist-titulo-modal').textContent = 'Cadastro de Checklist';
 
        selecionadosChecklist.clear();
        gerenciadosChecklist.clear();
 
        fecharCategoriasChecklist();
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
                const obrigatorioAChecklist = Number(itemAChecklist.obrigatorio) === 1;
                const obrigatorioBChecklist = Number(itemBChecklist.obrigatorio) === 1;
 
                if (obrigatorioAChecklist !== obrigatorioBChecklist) {
                    return obrigatorioAChecklist ? -1 : 1;
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
                    class="checklist-gerenciar-card ${selecionadoChecklist ? 'checklist-card--selecionado' : ''}"
                    data-checklist-item-id="${idChecklist}"
                >
                    <input
                        type="checkbox"
                        class="checklist-gerenciar-checkbox"
                        data-checklist-item-checkbox="${idChecklist}"
                        ${selecionadoChecklist ? 'checked' : ''}
                        aria-label="Selecionar ${escaparHtmlChecklist(itemChecklist.titulo)}"
                    >
 
                    <div class="checklist-gerenciar-conteudo">
                        <div class="checklist-gerenciar-tags">
                            <span class="checklist-gerenciar-tag ${obrigatorioChecklist ? 'checklist-tag--obrigatorio' : 'checklist-tag--opcional'}">
                                ${obrigatorioChecklist ? 'Obrigatório' : 'Opcional'}
                            </span>
                            ${inativoChecklist ? '<span class="checklist-gerenciar-tag checklist-status--inativo">Inativo</span>' : ''}
                        </div>
 
                        <strong>${escaparHtmlChecklist(itemChecklist.titulo)}</strong>
                        <span>${escaparHtmlChecklist(itemChecklist.referencia || 'Sem referência')}</span>
                    </div>
 
                    <div class="checklist-gerenciar-acoes">
                        <button
                            type="button"
                            class="btn-editar"
                            data-checklist-item-editar="${idChecklist}"
                            title="Editar item"
                            aria-label="Editar item"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
 
                        <button
                            type="button"
                            class="btn-excluir"
                            data-checklist-item-remover="${idChecklist}"
                            title="Excluir ou desativar item"
                            aria-label="Excluir ou desativar item"
                        >
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
 
        atualizarContadorChecklist();
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
 
    function renderizarSelecionadosChecklist() {
        const listaChecklist = elementoChecklist('checklist-lista-selecionados');
 
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
 
        listaChecklist.innerHTML = Array.from(selecionadosChecklist.values()).map(itemChecklist => `
            <div class="checklist-item-selecionado">
                <div class="checklist-item-texto">
                    <strong>${escaparHtmlChecklist(itemChecklist.titulo)}</strong>
                    <span>${escaparHtmlChecklist(itemChecklist.referencia || 'Sem referência')}</span>
                </div>
 
                <input type="hidden" name="itens_ids[]" value="${Number(itemChecklist.id)}">
 
                <button
                    type="button"
                    class="btn-excluir"
                    data-checklist-selecionado-remover="${Number(itemChecklist.id)}"
                    title="Remover item do checklist"
                    aria-label="Remover item do checklist"
                >
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `).join('');
    }
 
    function abrirNovoItemChecklist() {
        elementoChecklist('checklist-item-id').value = '';
        elementoChecklist('checklist-item-titulo').value = '';
        elementoChecklist('checklist-item-referencia').value = '';
        elementoChecklist('checklist-item-obrigatorio').value = '1';
        elementoChecklist('checklist-titulo-modal-item').textContent = 'Cadastro de Item';
        elementoChecklist('checklist-subtitulo-modal-item').textContent =
            'Cadastre um novo item reutilizável';
        elementoChecklist('checklist-btn-salvar-item').textContent = 'SALVAR';
 
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
            elementoChecklist('checklist-titulo-modal-item').textContent = 'Editar Item';
            elementoChecklist('checklist-subtitulo-modal-item').textContent =
                'Altere os dados do item reutilizável';
            elementoChecklist('checklist-btn-salvar-item').textContent = 'SALVAR ALTERAÇÕES';
 
            abrirModalChecklist('checklist-modal-item');
        } catch (erroChecklist) {
            console.error(erroChecklist);
            alert('Não foi possível carregar o item.');
        }
    }
 
    async function salvarItemCatalogoChecklist() {
        const idChecklist = Number(elementoChecklist('checklist-item-id').value);
        const tituloChecklist = elementoChecklist('checklist-item-titulo').value.trim();
        const referenciaChecklist = elementoChecklist('checklist-item-referencia').value.trim();
        const obrigatorioChecklist = elementoChecklist('checklist-item-obrigatorio').value;
 
        if (tituloChecklist === '') {
            alert('Informe o título do item.');
            elementoChecklist('checklist-item-titulo').focus();
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
                obrigatorio: obrigatorioChecklist
            };
 
            if (!novoChecklist) {
                dadosChecklist.id = idChecklist;
            }
 
            const resultadoChecklist = await enviarChecklist(acaoChecklist, dadosChecklist);
 
            if (!resultadoChecklist.ok || !resultadoChecklist.item) {
                alert(resultadoChecklist.mensagem || 'Não foi possível salvar o item.');
                return;
            }
 
            atualizarItemCatalogoChecklist(resultadoChecklist.item, novoChecklist);
            fecharModalChecklist('checklist-modal-item');
            renderizarGerenciamentoChecklist(elementoChecklist('checklist-pesquisa-itens').value);
            renderizarSelecionadosChecklist();
        } catch (erroChecklist) {
            console.error(erroChecklist);
            alert('Erro ao salvar item.');
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
        const confirmouChecklist = confirm(
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
                alert(resultadoChecklist.mensagem || 'Não foi possível remover o item.');
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
            alert(resultadoChecklist.mensagem);
        } catch (erroChecklist) {
            console.error(erroChecklist);
            alert('Erro ao remover item.');
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
 
        elementoChecklist('checklist-pesquisa-itens')?.addEventListener('input', eventoChecklist => {
            renderizarGerenciamentoChecklist(eventoChecklist.target.value);
        });
 
        elementoChecklist('checklist-lista-gerenciamento')?.addEventListener('click', eventoChecklist => {
            const editarChecklistBotao = eventoChecklist.target.closest('[data-checklist-item-editar]');
            const removerChecklistBotao = eventoChecklist.target.closest('[data-checklist-item-remover]');
            const checkboxChecklist = eventoChecklist.target.closest('[data-checklist-item-checkbox]');
            const cardChecklist = eventoChecklist.target.closest('[data-checklist-item-id]');
 
            if (editarChecklistBotao) {
                editarItemChecklist(Number(editarChecklistBotao.dataset.checklistItemEditar));
                return;
            }
 
            if (removerChecklistBotao) {
                removerItemCatalogoChecklist(Number(removerChecklistBotao.dataset.checklistItemRemover));
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
            const botaoChecklist = eventoChecklist.target.closest('[data-checklist-selecionado-remover]');
 
            if (!botaoChecklist) {
                return;
            }
 
            selecionadosChecklist.delete(Number(botaoChecklist.dataset.checklistSelecionadoRemover));
            renderizarSelecionadosChecklist();
        });
 
        elementoChecklist('checklist-formulario')?.addEventListener('submit', eventoChecklist => {
            if (selecionadosChecklist.size === 0) {
                eventoChecklist.preventDefault();
                alert('Selecione pelo menos um item para o checklist.');
            }
        });
 
        document.addEventListener('click', eventoChecklist => {
            if (!eventoChecklist.target.closest('.checklist-categoria-combobox')) {
                fecharCategoriasChecklist();
            }
        });
    }
 
    document.addEventListener('DOMContentLoaded', () => {
        configurarEventosChecklist();
        contarDescricaoChecklist();
        renderizarSelecionadosChecklist();
    });
 
    window.alternarStatusChecklist = alternarStatusChecklist;
    window.visualizarChecklist = visualizarChecklist;
    window.editarChecklist = editarChecklist;
    window.editarDaVisualizacaoChecklist = editarDaVisualizacaoChecklist;
    window.limparFormularioChecklist = limparFormularioChecklist;
    window.contarDescricaoChecklist = contarDescricaoChecklist;
    window.abrirGerenciamentoItensChecklist = abrirGerenciamentoItensChecklist;
    window.aplicarItensGerenciadosChecklist = aplicarItensGerenciadosChecklist;
    window.abrirNovoItemChecklist = abrirNovoItemChecklist;
    window.salvarItemCatalogoChecklist = salvarItemCatalogoChecklist;
})();
 
 