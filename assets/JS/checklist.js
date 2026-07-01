function adicionarItemChecklist() {
    const lista = document.getElementById('lista-itens-checklist');

    const div = document.createElement('div');
    div.classList.add('campo');

    div.innerHTML = `
        <label class="campo__label campo__label--obrigatorio">Item</label>
        <div class="campo__multi-busca">
            <input type="text" name="itens[]" class="campo__input" placeholder="Novo item do checklist" required>
            <button type="button" class="btn-cancelar" onclick="this.closest('.campo').remove()">Remover</button>
        </div>
    `;

    lista.appendChild(div);
}

function toggleHabilitado(elemento) {
    const id = elemento.dataset.id;
    const habilitado = elemento.checked ? 1 : 0;

    const formData = new FormData();
    formData.append('action', 'alterarHabilitado');
    formData.append('id', id);
    formData.append('habilitado', habilitado);

    fetch('checklist.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.ok) {
            alert('Erro ao alterar status.');
            elemento.checked = !elemento.checked;
        }
    })
    .catch(() => {
        alert('Erro de conexão com o servidor.');
        elemento.checked = !elemento.checked;
    });
}

function editarChecklist(id) {
    const formData = new FormData();
    formData.append('action', 'buscarChecklist');
    formData.append('id', id);

    fetch('checklist.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(checklist => {
        if (!checklist || !checklist.id) {
            alert('Checklist não encontrado.');
            return;
        }

        document.getElementById('checklist_id').value = checklist.id;
        document.querySelector('input[name="nome"]').value = checklist.nome ?? '';
        document.querySelector('input[name="descricao"]').value = checklist.descricao ?? '';
        document.querySelector('input[name="categoria"]').value = checklist.categoria ?? '';

        const lista = document.getElementById('lista-itens-checklist');
        lista.innerHTML = '';

        if (checklist.itens && checklist.itens.length > 0) {
            checklist.itens.forEach(item => {
                const div = document.createElement('div');
                div.classList.add('campo');

                div.innerHTML = `
                    <label class="campo__label campo__label--obrigatorio">Item</label>
                    <div class="campo__multi-busca">
                        <input type="text" name="itens[]" class="campo__input" value="${item.titulo}" required>
                        <button type="button" class="btn-cancelar" onclick="this.closest('.campo').remove()">Remover</button>
                    </div>
                `;

                lista.appendChild(div);
            });
        } else {
            lista.innerHTML = `
                <div class="campo">
                    <label class="campo__label campo__label--obrigatorio">Item</label>
                    <input type="text" name="itens[]" class="campo__input" placeholder="Ex: Verificar SQL Injection" required>
                </div>
            `;
        }

        const modal = document.getElementById('modalChecklist');

        if (modal) {
            modal.classList.add('active');
        }
    })
    .catch(() => {
        alert('Erro ao carregar checklist para edição.');
    });
}

function limparFormularioChecklist() {
    document.getElementById('checklist_id').value = '';
    document.querySelector('input[name="nome"]').value = '';
    document.querySelector('input[name="descricao"]').value = '';
    document.querySelector('input[name="categoria"]').value = '';

    const lista = document.getElementById('lista-itens-checklist');

    lista.innerHTML = `
        <div class="campo">
            <label class="campo__label campo__label--obrigatorio">Item</label>
            <input type="text" name="itens[]" class="campo__input" placeholder="Ex: Verificar SQL Injection" required>
        </div>
    `;
}
