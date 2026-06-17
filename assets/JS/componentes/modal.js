document.addEventListener('DOMContentLoaded', () => {
    
    // Escuta eventos de clique em todo o documento
    document.addEventListener('click', (event) => {
        
        // 1. ABERTURA DE MODAL
        const triggerBtn = event.target.closest('[data-modal-target]');
        if (triggerBtn) {
            const modalId = triggerBtn.getAttribute('data-modal-target');
            const modalElement = document.getElementById(modalId);
            
            if (modalElement) {
                modalElement.classList.add('active');
                document.body.style.overflow = 'hidden'; // Evita o scroll duplo
            }
        }

        // 2. FECHAMENTO DE MODAL
        const closeBtn = event.target.closest('[data-close-modal]');
        const isOverlayClick = event.target.classList.contains('modal-overlay');

        if (closeBtn || isOverlayClick) {
            const activeModal = document.querySelector('.modal-overlay.active');
            if (activeModal) {
                activeModal.classList.remove('active');
                document.body.style.overflow = ''; // Retorna o scroll padrão
            }
        }
    });

    // 3. FECHAMENTO VIA 'ESC' (Acessibilidade)
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            const activeModal = document.querySelector('.modal-overlay.active');
            if (activeModal) {
                activeModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    });
});