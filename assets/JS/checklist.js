function criarCampoItemChecklist(valor = '', permitirRemover = true) {
    const campo = document.createElement('div');
    campo.classList.add('campo');

    const label = document.createElement('label');
    label.classList.add(
        'campo__label',
        'campo__label--obrigatorio'
    );
    label.textContent = 'Item';

    const grupo = document.createElement('div');
    grupo.classList.add('campo__multi-busca');

    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'itens[]';
    input.classList.add('campo__input');
    input.placeholder = 'Novo item do checklist';
    input.value = valor;
    input.required = true;

    grupo.appendChild(input);

    if (permitirRemover) {
        const botaoRemover = document.createElement('button');

        botaoRemover.type = 'button';
        botaoRemover.classList.add('btn-cancelar');
        botaoRemover.textContent = 'Remover';

        botaoRemover.addEventListener('click', () => {
            campo.remove();
        });

        grupo.appendChild(botaoRemover);
    }

    campo.appendChild(label);
    campo.appendChild(grupo);

    return campo;
}

function adicionarItemChecklist() {
    const lista = document.getElementById(
        'lista-itens-checklist'
    );

    if (!lista) {
        return;
    }

    const campo = criarCampoItemChecklist('', true);

    lista.appendChild(campo);

    const input = campo.querySelector('input[name="itens[]"]');

    if (input) {
        input.focus();
    }
}

function toggleHabilitado(elemento) {
    const id = elemento.dataset.id;
    const habilitado = elemento.checked ? 1 : 0;

    const dados = new FormData();

    dados.append('action', 'alterarHabilitado');
    dados.append('id', id);
    dados.append('habilitado', habilitado);

    fetch('checklist.php', {
        method: 'POST',
        body: dados
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao alterar status.');
            }

            return response.json();
        })
        .then(resultado => {
            if (!resultado.ok) {
                throw new Error('Status não alterado.');
            }
        })
        .catch(error => {
            console.error(error);

            elemento.checked = !elemento.checked;

            alert('Não foi possível alterar o status.');
        });
}

function editarChecklist(id) {
    const dados = new FormData();

    dados.append('action', 'buscarChecklist');
    dados.append('id', id);

    fetch('checklist.php', {
        method: 'POST',
        body: dados
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao buscar checklist.');
            }

            return response.json();
        })
        .then(checklist => {
            if (!checklist || !checklist.id) {
                throw new Error('Checklist não encontrado.');
            }

            const campoId =
                document.getElementById('checklist_id');

            const campoNome =
                document.getElementById('checklist_nome');

            const campoCategoria =
                document.getElementById('categoria_input');

            const campoDescricao =
                document.getElementById('descricao_checklist');

            const tituloModal =
                document.getElementById('tituloModalChecklist');

            const listaItens =
                document.getElementById('lista-itens-checklist');

            const modal =
                document.getElementById('modalChecklist');

            campoId.value = checklist.id;
            campoNome.value = checklist.nome ?? '';
            campoCategoria.value = checklist.categoria ?? '';
            campoDescricao.value = checklist.descricao ?? '';

            fecharDropdownCategorias();

            if (tituloModal) {
                tituloModal.textContent = 'Editar Checklist';
            }

            contarDescricaoChecklist();

            listaItens.innerHTML = '';

            if (
                Array.isArray(checklist.itens)
                && checklist.itens.length > 0
            ) {
                checklist.itens.forEach(item => {
                    listaItens.appendChild(
                        criarCampoItemChecklist(
                            item.titulo ?? '',
                            true
                        )
                    );
                });
            } else {
                listaItens.appendChild(
                    criarCampoItemChecklist('', false)
                );
            }

            if (modal) {
                modal.classList.add('active');
            }
        })
        .catch(error => {
            console.error(error);

            alert('Erro ao carregar checklist para edição.');
        });
}

function limparFormularioChecklist() {
    const campoId =
        document.getElementById('checklist_id');

    const campoNome =
        document.getElementById('checklist_nome');

    const campoCategoria =
        document.getElementById('categoria_input');

    const campoDescricao =
        document.getElementById('descricao_checklist');

    const contador =
        document.getElementById('contador_descricao');

    const tituloModal =
        document.getElementById('tituloModalChecklist');

    const listaItens =
        document.getElementById('lista-itens-checklist');

    campoId.value = '';
    campoNome.value = '';
    campoCategoria.value = '';
    campoDescricao.value = '';

    fecharDropdownCategorias();

    if (contador) {
        contador.textContent = '0 / 1000';
    }

    if (tituloModal) {
        tituloModal.textContent = 'Cadastro de Checklist';
    }

    listaItens.innerHTML = '';

    listaItens.appendChild(
        criarCampoItemChecklist('', false)
    );
}

function contarDescricaoChecklist() {
    const descricao =
        document.getElementById('descricao_checklist');

    const contador =
        document.getElementById('contador_descricao');

    if (!descricao || !contador) {
        return;
    }

    contador.textContent =
        `${descricao.value.length} / 1000`;
}

/*
|--------------------------------------------------------------------------
| Categorias
|--------------------------------------------------------------------------
*/

const categoriasChecklist = Array.isArray(
    window.categoriasChecklist
)
    ? window.categoriasChecklist
    : [];

function normalizarCategoria(valor) {
    return String(valor ?? '')
        .trim()
        .toLocaleLowerCase('pt-BR');
}

function obterNomeCategoria(categoria) {
    if (typeof categoria === 'string') {
        return categoria;
    }

    return categoria?.nome ?? '';
}

function filtrarCategoriasChecklist(filtro = '') {
    const filtroNormalizado = normalizarCategoria(filtro);

    return categoriasChecklist.filter(categoria => {
        const nome = obterNomeCategoria(categoria);

        return normalizarCategoria(nome)
            .includes(filtroNormalizado);
    });
}

function abrirDropdownCategorias(filtro = '') {
    const lista =
        document.getElementById('lista_categorias');

    const campo =
        document.getElementById('categoria_input');

    if (!lista || !campo) {
        return;
    }

    renderizarCategoriasChecklist(filtro);

    lista.hidden = false;

    campo.setAttribute('aria-expanded', 'true');
}

function fecharDropdownCategorias() {
    const lista =
        document.getElementById('lista_categorias');

    const campo =
        document.getElementById('categoria_input');

    if (lista) {
        lista.hidden = true;
    }

    if (campo) {
        campo.setAttribute('aria-expanded', 'false');
    }
}

function selecionarCategoriaChecklist(nome) {
    const campo =
        document.getElementById('categoria_input');

    if (!campo) {
        return;
    }

    campo.value = nome;

    fecharDropdownCategorias();

    campo.focus();
}

function renderizarCategoriasChecklist(filtro = '') {
    const lista =
        document.getElementById('lista_categorias');

    if (!lista) {
        return;
    }

    const categoriasFiltradas =
        filtrarCategoriasChecklist(filtro);

    lista.innerHTML = '';

    if (categoriasFiltradas.length === 0) {
        const mensagem = document.createElement('div');

        mensagem.classList.add(
            'categoria-dropdown-vazio'
        );

        mensagem.textContent =
            'Nenhuma categoria encontrada. Você pode cadastrar uma nova ao salvar.';

        lista.appendChild(mensagem);

        return;
    }

    categoriasFiltradas.forEach(categoria => {
        const nome = obterNomeCategoria(categoria);

        const opcao = document.createElement('button');

        opcao.type = 'button';
        opcao.classList.add('categoria-dropdown-item');
        opcao.textContent = nome;

        opcao.addEventListener('click', () => {
            selecionarCategoriaChecklist(nome);
        });

        lista.appendChild(opcao);
    });
}

function configurarCategoriaChecklist() {
    const campo =
        document.getElementById('categoria_input');

    const botaoSeta =
        document.getElementById('btn_abrir_categorias');

    const lista =
        document.getElementById('lista_categorias');

    if (!campo || !botaoSeta || !lista) {
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

    botaoSeta.addEventListener('click', evento => {
        evento.stopPropagation();

        if (lista.hidden) {
            abrirDropdownCategorias('');
        } else {
            fecharDropdownCategorias();
        }
    });

    document.addEventListener('click', evento => {
        if (!evento.target.closest('.categoria-combobox')) {
            fecharDropdownCategorias();
        }
    });
}

document.addEventListener(
    'DOMContentLoaded',
    configurarCategoriaChecklist
);
