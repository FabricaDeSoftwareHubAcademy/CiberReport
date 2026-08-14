document.addEventListener('DOMContentLoaded', () => {
    const openButtons = document.querySelectorAll('[data-modal-target]');
    openButtons.forEach(button => {
        button.addEventListener('click', () => {
            const modalId = button.getAttribute('data-modal-target');
            const modal = document.getElementById(modalId);
            if (!modal) return;
            modal.classList.add('active');

            if (modal.querySelector('.form-step') && typeof goToStep === 'function') {
                goToStep(0);
            }
        });
    });

    const closeButtons = document.querySelectorAll('[data-modal-close]');
    closeButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const modal = e.target.closest('.modal-overlay');
            if (modal) modal.classList.remove('active');
        });
    });

    const overlays = document.querySelectorAll('.modal-overlay');
    overlays.forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
            }
        });
    });
});