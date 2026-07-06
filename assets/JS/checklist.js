const categoriasChecklist = Array.isArray(window.categoriasChecklist)
    ? window.categoriasChecklist
    : [];

const itensCatalogo = Array.isArray(window.itensCatalogo)
    ? window.itensCatalogo
    : [];

const itensSelecionados = new Map();
const itensGerenciamento = new Map();

function normalizarTexto(valor) {
    return String(valor ?? '')
        .trim()
        .toLocaleLowerCase('pt-BR');
}

async function enviarFormularioAjax(dados) {
    const resposta = await fetch('checklist.php', {
        method: 'POST',
        body: dados
    });

    if (!resposta.ok) {
        throw new Error('Erro na comunicação com o servidor.');
    }

    return resposta.json();
}

async function buscarChecklist(id) {
    const dados = new FormData();

    dados.append('action', 'buscarChecklist');
    dados.append('id', id);

    const resultado = await enviarFormularioAjax(dados);

    if (!resultado.ok || !resultado.checklist) {
        throw new Error('Checklist não encontrado.');
    }

    return resultado.checklist;
}

async function toggleHabilitado(elemento) {
    const dados = new FormData();

    dados.append('action', 'alterarHabilitado');
    dados.append('id', elemento.dataset.id);
    dados.append('habilitado', elemento.checked ? 1 : 0);

    try {
        const resultado = await enviarFormularioAjax(dados);

        if (!resultado.ok) {
            throw new Error('Status não alterado.');
        }
    } catch (erro) {
        console.error(erro);

        elemento.checked = !elemento.checked;

        alert('Não foi possível alterar o status.');
    }
}

async function visualizarChecklist(id) {
    try {
        const checklist = await buscarChecklist(id);

        document.getElementById('visualizar_checklist_id').value =
            checklist.id;

        document.getElementById('visualizar_checklist_nome').textContent =
            checklist.nome || 'Não informado';

        document.getElementById('visualizar_checklist_categoria').textContent =
            checklist.categoria || 'Não informada';

        document.getElementById('visualizar_checklist_descricao').textContent =
            checklist.descricao || 'Nenhuma descrição informada.';

        const status = document.getElementById(
            'visualizar_checklist_status'
        );

        const ativo = Number(checklist.habilitado) === 1;

        status.textContent = ativo ? 'Ativo' : 'Inativo';

        status.className = ativo
            ? 'visualizacao-status ativo'
            : 'visualizacao-status inativo';

        renderizarVisualizacaoItens(checklist.itens);

        document
            .getElementById('modalVisualizarChecklist')
            ?.classList.add('active');
    } catch (erro) {
        console.error(erro);

        alert('Não foi possível visualizar o checklist.');
    }
}

function renderizarVisualizacaoItens(itens) {
    const lista = document.getElementById(
        'visualizar_checklist_itens'
    );

    const contador = document.getElementById(
        'visualizar_total_itens'
    );

    if (!lista || !contador) {
        return;
    }

    lista.innerHTML = '';

    const total = Array.isArray(itens)
        ? itens.length
        : 0;

    contador.textContent = total === 1
        ? '1 item'
        : `${total} itens`;

    if (total === 0) {
        lista.innerHTML = `
            <div class="visualizacao-itens-vazio">
                <i class="fa-regular fa-rectangle-list"></i>
                <span>Nenhum item vinculado.</span>
            </div>
        `;

        return;
    }

    itens.forEach((item, indice) => {
        const linha = document.createElement('div');
        const numero = document.createElement('span');
        const informacoes = document.createElement('div');
        const titulo = document.createElement('strong');
        const referencia = document.createElement('span');
        const tipo = document.createElement('span');

        linha.className = 'visualizacao-item';
        numero.className = 'visualizacao-item-numero';
        informacoes.className = 'visualizacao-item-informacoes';

        numero.textContent = String(indice + 1);
        titulo.textContent = item.titulo;
        referencia.textContent =
            item.referencia || 'Sem referência';

        tipo.className = Number(item.obrigatorio) === 1
            ? 'visualizacao-item-tipo obrigatorio'
            : 'visualizacao-item-tipo opcional';

        tipo.textContent = Number(item.obrigatorio) === 1
            ? 'Obrigatório'
            : 'Opcional';

        informacoes.appendChild(titulo);
        informacoes.appendChild(referencia);

        linha.appendChild(numero);
        linha.appendChild(informacoes);
        linha.appendChild(tipo);

        lista.appendChild(linha);
    });
}

function fecharVisualizacaoChecklist() {
    document
        .getElementById('modalVisualizarChecklist')
        ?.classList.remove('active');
}

function editarChecklistDaVisualizacao() {
    const id = Number(
        document.getElementById('visualizar_checklist_id').value
    );

    if (id <= 0) {
        alert('Checklist não encontrado.');
        return;
    }

    fecharVisualizacaoChecklist();
    editarChecklist(id);
}

async function editarChecklist(id) {
    try {
        const checklist = await buscarChecklist(id);

        document.getElementById('checklist_id').value =
            checklist.id;

        document.getElementById('checklist_nome').value =
            checklist.nome ?? '';

        document.getElementById('categoria_input').value =
            checklist.categoria ?? '';

        document.getElementById('descricao_checklist').value =
            checklist.descricao ?? '';

        document.getElementById('tituloModalChecklist').textContent =
            'Editar Checklist';

        itensSelecionados.clear();
        itensGerenciamento.clear();

        if (Array.isArray(checklist.itens)) {
            checklist.itens.forEach(item => {
                itensSelecionados.set(Number(item.id), item);
            });
        }

        fecharDropdownCategorias();
        contarDescricaoChecklist();
        renderizarItensSelecionados();

        document
            .getElementById('modalChecklist')
            ?.classList.add('active');
    } catch (erro) {
        console.error(erro);

        alert('Erro ao carregar checklist para edição.');
    }
}

function limparFormularioChecklist() {
    document.getElementById('formChecklist')?.reset();
    document.getElementById('checklist_id').value = '';

    document.getElementById('tituloModalChecklist').textContent =
        'Cadastro de Checklist';

    itensSelecionados.clear();
    itensGerenciamento.clear();

    fecharDropdownCategorias();
    contarDescricaoChecklist();
    renderizarItensSelecionados();
}

function contarDescricaoChecklist() {
    const descricao = document.getElementById(
        'descricao_checklist'
    );

    const contador = document.getElementById(
        'contador_descricao'
    );

    if (!descricao || !contador) {
        return;
    }

    contador.textContent =
        `${descricao.value.length} / 1000`;
}

function obterNomeCategoria(categoria) {
    return typeof categoria === 'string'
        ? categoria
        : categoria?.nome ?? '';
}

function filtrarCategoriasChecklist(filtro = '') {
    const termo = normalizarTexto(filtro);

    return categoriasChecklist.filter(categoria => {
        return normalizarTexto(
            obterNomeCategoria(categoria)
        ).includes(termo);
    });
}

function abrirDropdownCategorias(filtro = '') {
    const lista = document.getElementById('lista_categorias');
    const campo = document.getElementById('categoria_input');

    if (!lista || !campo) {
        return;
    }

    renderizarCategoriasChecklist(filtro);

    lista.hidden = false;
    campo.setAttribute('aria-expanded', 'true');
}

function fecharDropdownCategorias() {
    const lista = document.getElementById('lista_categorias');
    const campo = document.getElementById('categoria_input');

    if (lista) {
        lista.hidden = true;
    }

    if (campo) {
        campo.setAttribute('aria-expanded', 'false');
    }
}

function selecionarCategoriaChecklist(nome) {
    const campo = document.getElementById('categoria_input');

    if (!campo) {
        return;
    }

    campo.value = nome;

    fecharDropdownCategorias();
    campo.focus();
}

function renderizarCategoriasChecklist(filtro = '') {
    const lista = document.getElementById('lista_categorias');

    if (!lista) {
        return;
    }

    const categoriasFiltradas =
        filtrarCategoriasChecklist(filtro);

    lista.innerHTML = '';

    if (categoriasFiltradas.length === 0) {
        const mensagem = document.createElement('div');

        mensagem.className =
            'categoria-dropdown-vazio';

        mensagem.textContent =
            'Nenhuma categoria encontrada. Você pode cadastrar uma nova ao salvar.';

        lista.appendChild(mensagem);
        return;
    }

    categoriasFiltradas.forEach(categoria => {
        const nome = obterNomeCategoria(categoria);
        const opcao = document.createElement('button');

        opcao.type = 'button';
        opcao.className = 'categoria-dropdown-item';
        opcao.textContent = nome;

        opcao.addEventListener('click', () => {
            selecionarCategoriaChecklist(nome);
        });

        lista.appendChild(opcao);
    });
}

function configurarCategoriaChecklist() {
    const campo = document.getElementById('categoria_input');
    const botao = document.getElementById(
        'btn_abrir_categorias'
    );

    const lista = document.getElementById(
        'lista_categorias'
    );

    if (!campo || !botao || !lista) {
        return;
    }

    campo.addEventListener('input', () => {
        abrirDropdownCategorias(campo.value);
    });

    campo.addEventListener('focus', () => {
        abrirDropdownCategorias(campo.value);
    });

    campo.addEventListener('keydown', evento => {
        if (evento.key === 'Escape') {
            fecharDropdownCategorias();
        }
    });

    botao.addEventListener('click', evento => {
        evento.stopPropagation();

        if (lista.hidden) {
            abrirDropdownCategorias('');
        } else {
            fecharDropdownCategorias();
        }
    });
}

function abrirModalGerenciarItens() {
    itensGerenciamento.clear();

    itensSelecionados.forEach((item, id) => {
        itensGerenciamento.set(id, item);
    });

    const pesquisa = document.getElementById(
        'pesquisa_itens_catalogo'
    );

    if (pesquisa) {
        pesquisa.value = '';
    }

    document
        .getElementById('modalGerenciarItens')
        ?.classList.add('active');

    renderizarItensGerenciamento('');

    requestAnimationFrame(() => {
        pesquisa?.focus();
    });
}

function fecharModalGerenciarItens() {
    document
        .getElementById('modalGerenciarItens')
        ?.classList.remove('active');
}

function aplicarItensGerenciados() {
    itensSelecionados.clear();

    itensGerenciamento.forEach((item, id) => {
        itensSelecionados.set(id, item);
    });

    renderizarItensSelecionados();
    fecharModalGerenciarItens();
}

function filtrarItensCatalogo(filtro = '') {
    const termo = normalizarTexto(filtro);

    return itensCatalogo.filter(item => {
        const titulo = normalizarTexto(item.titulo);
        const referencia = normalizarTexto(item.referencia);

        return titulo.includes(termo)
            || referencia.includes(termo);
    });
}

function alternarItemGerenciamento(item, selecionado) {
    const id = Number(item.id);

    if (selecionado) {
        itensGerenciamento.set(id, item);
    } else {
        itensGerenciamento.delete(id);
    }

    atualizarContadorItensGerenciamento();
}

function atualizarContadorItensGerenciamento() {
    const contador = document.getElementById(
        'contador_itens_selecionados'
    );

    if (!contador) {
        return;
    }

    const total = itensGerenciamento.size;

    contador.textContent = total === 1
        ? '1 selecionado'
        : `${total} selecionados`;
}

function renderizarItensGerenciamento(filtro = '') {
    const lista = document.getElementById(
        'lista_itens_gerenciamento'
    );

    if (!lista) {
        return;
    }

    const itensFiltrados = filtrarItensCatalogo(filtro);

    lista.innerHTML = '';

    if (itensFiltrados.length === 0) {
        lista.innerHTML = `
            <div class="gerenciar-itens-vazio">
                <i class="fa-regular fa-folder-open"></i>
                <span>Nenhum item encontrado.</span>
            </div>
        `;

        atualizarContadorItensGerenciamento();
        return;
    }

    itensFiltrados.forEach(item => {
        const id = Number(item.id);
        const card = document.createElement('div');
        const checkbox = document.createElement('input');
        const conteudo = document.createElement('div');
        const topo = document.createElement('div');
        const tag = document.createElement('span');
        const titulo = document.createElement('strong');
        const referencia = document.createElement('span');
        const acoes = document.createElement('div');
        const botaoEditar = document.createElement('button');
        const botaoExcluir = document.createElement('button');

        card.className = 'gerenciar-item-opcao';

        if (itensGerenciamento.has(id)) {
            card.classList.add('selecionado');
        }

        checkbox.type = 'checkbox';
        checkbox.className = 'gerenciar-item-checkbox';
        checkbox.checked = itensGerenciamento.has(id);

        checkbox.setAttribute(
            'aria-label',
            `Selecionar ${item.titulo}`
        );

        conteudo.className = 'gerenciar-item-conteudo';
        topo.className = 'gerenciar-item-topo';

        tag.className = Number(item.obrigatorio) === 1
            ? 'gerenciar-item-obrigatorio obrigatorio'
            : 'gerenciar-item-obrigatorio';

        tag.textContent = Number(item.obrigatorio) === 1
            ? 'Obrigatório'
            : 'Opcional';

        titulo.textContent = item.titulo;
        referencia.textContent =
            item.referencia || 'Sem referência';

        topo.appendChild(tag);

        conteudo.appendChild(topo);
        conteudo.appendChild(titulo);
        conteudo.appendChild(referencia);

        acoes.className = 'gerenciar-item-acoes';

        botaoEditar.type = 'button';
        botaoEditar.className = 'btn-editar';
        botaoEditar.title = 'Editar item';

        botaoEditar.setAttribute(
            'aria-label',
            'Editar item'
        );

        botaoEditar.innerHTML =
            '<i class="fa-solid fa-pen-to-square"></i>';

        botaoExcluir.type = 'button';
        botaoExcluir.className = 'btn-excluir';
        botaoExcluir.title = 'Excluir ou desativar item';

        botaoExcluir.setAttribute(
            'aria-label',
            'Excluir ou desativar item'
        );

        botaoExcluir.innerHTML =
            '<i class="fa-solid fa-trash"></i>';

        function atualizarSelecao() {
            alternarItemGerenciamento(
                item,
                checkbox.checked
            );

            card.classList.toggle(
                'selecionado',
                checkbox.checked
            );
        }

        card.addEventListener('click', evento => {
            if (
                evento.target.closest('.gerenciar-item-acoes')
                || evento.target === checkbox
            ) {
                return;
            }

            checkbox.checked = !checkbox.checked;

            atualizarSelecao();
        });

        checkbox.addEventListener(
            'change',
            atualizarSelecao
        );

        botaoEditar.addEventListener('click', evento => {
            evento.stopPropagation();
            editarItemCatalogo(item.id);
        });

        botaoExcluir.addEventListener('click', evento => {
            evento.stopPropagation();
            removerItemCatalogo(item.id);
        });

        acoes.appendChild(botaoEditar);
        acoes.appendChild(botaoExcluir);

        card.appendChild(checkbox);
        card.appendChild(conteudo);
        card.appendChild(acoes);

        lista.appendChild(card);
    });

    atualizarContadorItensGerenciamento();
}

function removerItemSelecionado(id) {
    itensSelecionados.delete(Number(id));

    renderizarItensSelecionados();
}

function renderizarItensSelecionados() {
    const lista = document.getElementById(
        'lista-itens-checklist'
    );

    if (!lista) {
        return;
    }

    lista.innerHTML = '';

    if (itensSelecionados.size === 0) {
        lista.innerHTML = `
            <div class="checklist-itens-vazio">
                <i class="fa-regular fa-rectangle-list"></i>

                <div>
                    <strong>Nenhum item selecionado</strong>
                    <span>
                        Use o botão “Gerenciar itens” para adicionar.
                    </span>
                </div>
            </div>
        `;

        return;
    }

    itensSelecionados.forEach(item => {
        const linha = document.createElement('div');
        const informacoes = document.createElement('div');
        const titulo = document.createElement('strong');
        const referencia = document.createElement('span');
        const inputId = document.createElement('input');
        const botaoRemover = document.createElement('button');

        linha.className = 'checklist-item-selecionado';
        informacoes.className =
            'checklist-item-informacoes';

        titulo.textContent = item.titulo;
        referencia.textContent =
            item.referencia || 'Sem referência';

        inputId.type = 'hidden';
        inputId.name = 'itens_ids[]';
        inputId.value = item.id;

        botaoRemover.type = 'button';
        botaoRemover.className = 'btn-excluir';
        botaoRemover.title =
            'Remover item do checklist';

        botaoRemover.setAttribute(
            'aria-label',
            'Remover item do checklist'
        );

        botaoRemover.innerHTML =
            '<i class="fa-solid fa-trash"></i>';

        botaoRemover.addEventListener('click', () => {
            removerItemSelecionado(item.id);
        });

        informacoes.appendChild(titulo);
        informacoes.appendChild(referencia);

        linha.appendChild(informacoes);
        linha.appendChild(inputId);
        linha.appendChild(botaoRemover);

        lista.appendChild(linha);
    });
}

function abrirModalNovoItem() {
    document.getElementById('item_catalogo_id').value = '';
    document.getElementById('novo_item_titulo').value = '';
    document.getElementById('novo_item_referencia').value = '';
    document.getElementById('novo_item_obrigatorio').value = '1';

    document.getElementById('tituloModalItem').textContent =
        'Cadastro de Item';

    document.getElementById('subtituloModalItem').textContent =
        'Cadastre um novo item reutilizável';

    document.getElementById(
        'btnSalvarItemCatalogo'
    ).textContent = 'SALVAR';

    document
        .getElementById('modalNovoItem')
        ?.classList.add('active');

    requestAnimationFrame(() => {
        document
            .getElementById('novo_item_titulo')
            ?.focus();
    });
}

function fecharModalNovoItem() {
    document
        .getElementById('modalNovoItem')
        ?.classList.remove('active');
}

async function editarItemCatalogo(id) {
    const dados = new FormData();

    dados.append('action', 'buscarItemCatalogo');
    dados.append('id', id);

    try {
        const resultado = await enviarFormularioAjax(dados);

        if (!resultado.ok || !resultado.item) {
            throw new Error('Item não encontrado.');
        }

        const item = resultado.item;

        document.getElementById('item_catalogo_id').value =
            item.id;

        document.getElementById('novo_item_titulo').value =
            item.titulo ?? '';

        document.getElementById(
            'novo_item_referencia'
        ).value = item.referencia ?? '';

        document.getElementById(
            'novo_item_obrigatorio'
        ).value = Number(item.obrigatorio) === 1
            ? '1'
            : '0';

        document.getElementById(
            'tituloModalItem'
        ).textContent = 'Editar Item';

        document.getElementById(
            'subtituloModalItem'
        ).textContent =
            'Altere os dados do item reutilizável';

        document.getElementById(
            'btnSalvarItemCatalogo'
        ).textContent = 'SALVAR ALTERAÇÕES';

        document
            .getElementById('modalNovoItem')
            ?.classList.add('active');

        requestAnimationFrame(() => {
            document
                .getElementById('novo_item_titulo')
                ?.focus();
        });
    } catch (erro) {
        console.error(erro);

        alert('Não foi possível carregar o item.');
    }
}

async function salvarItemCatalogo() {
    const id = Number(
        document.getElementById('item_catalogo_id').value
    );

    const campoTitulo = document.getElementById(
        'novo_item_titulo'
    );

    const titulo = campoTitulo.value.trim();

    const referencia = document
        .getElementById('novo_item_referencia')
        .value
        .trim();

    const obrigatorio = document
        .getElementById('novo_item_obrigatorio')
        .value;

    if (titulo === '') {
        alert('Informe o título do item.');

        campoTitulo.focus();
        return;
    }

    const dados = new FormData();

    dados.append(
        'action',
        id > 0
            ? 'atualizarItemCatalogoChecklist'
            : 'cadastrarItemCatalogoChecklist'
    );

    if (id > 0) {
        dados.append('id', id);
    }

    dados.append('titulo', titulo);
    dados.append('referencia', referencia);
    dados.append('obrigatorio', obrigatorio);

    try {
        const resultado = await enviarFormularioAjax(dados);

        if (!resultado.ok || !resultado.item) {
            alert(
                resultado.mensagem
                || 'Não foi possível salvar o item.'
            );

            return;
        }

        atualizarItemNasListas(resultado.item);
        fecharModalNovoItem();

        const pesquisa = document.getElementById(
            'pesquisa_itens_catalogo'
        );

        renderizarItensGerenciamento(
            pesquisa?.value ?? ''
        );

        renderizarItensSelecionados();
    } catch (erro) {
        console.error(erro);

        alert('Erro ao salvar item.');
    }
}

function atualizarItemNasListas(item) {
    const id = Number(item.id);

    const indice = itensCatalogo.findIndex(
        itemCatalogo => Number(itemCatalogo.id) === id
    );

    if (indice === -1) {
        itensCatalogo.push(item);
    } else {
        itensCatalogo[indice] = item;
    }

    itensGerenciamento.set(id, item);

    if (itensSelecionados.has(id)) {
        itensSelecionados.set(id, item);
    }
}

async function removerItemCatalogo(id) {
    const confirmar = confirm(
        'Deseja remover este item? Se estiver sendo utilizado em algum checklist, ele será apenas desativado.'
    );

    if (!confirmar) {
        return;
    }

    const dados = new FormData();

    dados.append('action', 'removerItemCatalogoChecklist');
    dados.append('id', id);

    try {
        const resultado = await enviarFormularioAjax(dados);

        if (!resultado.ok) {
            alert(
                resultado.mensagem
                || 'Não foi possível remover o item.'
            );

            return;
        }

        const itemId = Number(id);

        const indice = itensCatalogo.findIndex(
            item => Number(item.id) === itemId
        );

        if (indice !== -1) {
            itensCatalogo.splice(indice, 1);
        }

        itensGerenciamento.delete(itemId);
        itensSelecionados.delete(itemId);

        const pesquisa = document.getElementById(
            'pesquisa_itens_catalogo'
        );

        renderizarItensGerenciamento(
            pesquisa?.value ?? ''
        );

        renderizarItensSelecionados();

        alert(resultado.mensagem);
    } catch (erro) {
        console.error(erro);

        alert('Erro ao remover item.');
    }
}

function configurarGerenciamentoItens() {
    const pesquisa = document.getElementById(
        'pesquisa_itens_catalogo'
    );

    pesquisa?.addEventListener('input', () => {
        renderizarItensGerenciamento(
            pesquisa.value
        );
    });
}

function configurarFormularioChecklist() {
    const formulario = document.getElementById(
        'formChecklist'
    );

    formulario?.addEventListener('submit', evento => {
        if (itensSelecionados.size > 0) {
            return;
        }

        evento.preventDefault();

        alert(
            'Selecione pelo menos um item para o checklist.'
        );
    });
}

document.addEventListener('click', evento => {
    if (!evento.target.closest('.categoria-combobox')) {
        fecharDropdownCategorias();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    configurarCategoriaChecklist();
    configurarGerenciamentoItens();
    configurarFormularioChecklist();

    contarDescricaoChecklist();
    renderizarItensSelecionados();
});