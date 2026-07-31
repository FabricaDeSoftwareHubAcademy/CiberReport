document.addEventListener('DOMContentLoaded', () => {
    const gatilhos = document.querySelectorAll('[data-popup-href]');
    gatilhos.forEach((gatilho) => {
        gatilho.addEventListener('click', () => {
            const popupId = gatilho.getAttribute('data-modal-target');
            const popup = document.getElementById(popupId);
            if (!popup) return;

            const botaoConfirmar = popup.querySelector('[data-popup-confirmar]');
            if (botaoConfirmar) {
                botaoConfirmar.setAttribute('href', gatilho.getAttribute('data-popup-href'));
            }
        });
    });
});
