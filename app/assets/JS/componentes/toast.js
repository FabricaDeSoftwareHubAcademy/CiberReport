(() => {
    const DURACAO_PADRAO_TOAST = 2000;

    const CONFIG_TIPO_TOAST = {
        sucesso: { icone: 'fa-solid fa-check', titulo: 'Sucesso!' },
        info: { icone: 'fa-solid fa-exclamation', titulo: 'Informação' },
        aviso: { icone: 'fa-solid fa-triangle-exclamation', titulo: 'Atenção!' },
        erro: { icone: 'fa-solid fa-xmark', titulo: 'Algo deu errado!' }
    };

    function fecharToast(toast) {
        if (!toast || toast.classList.contains('toast--saindo')) return;

        toast.classList.add('toast--saindo');
        toast.addEventListener('animationend', () => toast.remove(), { once: true });
    }

    function exibirToast(tipo, mensagem, titulo, duracao = DURACAO_PADRAO_TOAST) {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const tipoValido = CONFIG_TIPO_TOAST[tipo] ? tipo : 'info';
        const config = CONFIG_TIPO_TOAST[tipoValido];

        const toast = document.createElement('div');
        toast.className = `toast toast--${tipoValido}`;
        toast.setAttribute('role', 'status');

        toast.innerHTML = `
            <div class="toast__icone">
                <i class="${config.icone}"></i>
            </div>
            <div class="toast__conteudo">
                <strong class="toast__titulo"></strong>
                <p class="toast__mensagem"></p>
            </div>
            <button type="button" class="toast__fechar" aria-label="Fechar notificação">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;

        toast.querySelector('.toast__titulo').textContent = titulo || config.titulo;
        toast.querySelector('.toast__mensagem').textContent = mensagem || '';
        toast.querySelector('.toast__fechar').addEventListener('click', () => fecharToast(toast));

        container.appendChild(toast);

        if (duracao > 0) {
            setTimeout(() => fecharToast(toast), duracao);
        }

        return toast;
    }

    window.exibirToast = exibirToast;
})();
