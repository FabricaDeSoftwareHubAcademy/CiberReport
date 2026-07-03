const sideBar = document.getElementById('sideBar');
const dropdown = document.getElementById('dropdown');
const pesquisaMobileBtn = document.querySelector('.pesquisa-mobile button');
const overlayPesquisa = document.querySelector('.overlay-pesquisaMobile');
const logoMenuSuperior = document.querySelector('.logo-menuSuperior');
const menuOverlay = document.querySelector('.menuOverlay');

// Botão de abrir/fechar sidebar (desktop)
document.getElementById('open_btn').addEventListener('click', function () {
    const isOpen = sideBar.classList.contains('open-sidebar');

    if (isOpen) {
        menuOverlay.classList.remove('active');
        setTimeout(() => {
            sideBar.classList.remove('open-sidebar');
        }, 100);
    } else {
        sideBar.classList.add('open-sidebar');
    }
    // suBmenus.classList.add('fechado');
});

// Logo do menu superior abre sidebar (mobile)
logoMenuSuperior.addEventListener('click', () => {
    sideBar.classList.toggle('open-sidebar');
    menuOverlay.classList.toggle('active');
});

// Overlay do menu mobile fecha o sidebar
menuOverlay.addEventListener('click', () => {
    sideBar.classList.remove('open-sidebar');
    menuOverlay.classList.remove('active');
    dropdown.classList.remove('open_dropdown');
});

// Botão de pesquisa mobile abre overlay de busca
pesquisaMobileBtn.addEventListener('click', () => {
    overlayPesquisa.classList.add('active');
});

// Clicar fora da barra fecha o overlay de busca
overlayPesquisa.addEventListener('click', (e) => {
    if (!e.target.closest('.barra-pesquisa')) {
        overlayPesquisa.classList.remove('active');
    }
});
