document.addEventListener('DOMContentLoaded', () => {
    // Liga o botão/link "confirmar" do popup ao alvo (link ou função) indicado pelo gatilho que o abriu
    const gatilhos = document.querySelectorAll('[data-popup-href], [data-popup-callback]');
    gatilhos.forEach((gatilho) => {
        gatilho.addEventListener('click', () => {
            const popupId = gatilho.getAttribute('data-modal-target');
            const popup = document.getElementById(popupId);
            if (!popup) return;

            const botaoConfirmar = popup.querySelector('[data-popup-confirmar]');
            if (!botaoConfirmar) return;

            const href = gatilho.getAttribute('data-popup-href');
            if (href !== null) {
                botaoConfirmar.setAttribute('href', href);
            }

            botaoConfirmar.dataset.popupCallback = gatilho.getAttribute('data-popup-callback') ?? '';
        });
    });

    // Ao confirmar, executa a função JS informada (quando houver) e fecha o popup
    document.addEventListener('click', (evento) => {
        const botaoConfirmar = evento.target.closest('[data-popup-confirmar]');
        if (!botaoConfirmar) return;

        const nomeCallback = botaoConfirmar.dataset.popupCallback;
        if (nomeCallback && typeof window[nomeCallback] === 'function') {
            window[nomeCallback]();
            botaoConfirmar.closest('.modal-overlay')?.classList.remove('active');
        }
    });
});
