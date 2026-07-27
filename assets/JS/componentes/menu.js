const sideBar = document.getElementById('sideBar');
const dropdown = document.getElementById('dropdown');
const pesquisaMobileBtn = document.querySelector('.pesquisa-mobile button');
const overlayPesquisa = document.querySelector('.overlay-pesquisaMobile');
const logoMenuSuperior = document.querySelector('.logo-menuSuperior');
const menuOverlay = document.querySelector('.menuOverlay');

function persistenciaMenu(valor){
    document.cookie = `sidebarOpen=${valor}; path=/; max-age=900000`
}

document.getElementById('open_btn').addEventListener('click', function () {
    const isOpen = sideBar.classList.contains('open-sidebar');

    if (isOpen) {
        menuOverlay.classList.remove('active');
        persistenciaMenu(false);
        setTimeout(() => {
            sideBar.classList.remove('open-sidebar');
        }, 100);
    } else {
        sideBar.classList.add('open-sidebar');
        persistenciaMenu(true);        
    }
});

// Logo do menu superior abre sidebar (mobile)
logoMenuSuperior.addEventListener('click', () => {
    const menuAberto = sideBar.classList.toggle('open-sidebar');
    menuOverlay.classList.toggle('active', menuAberto);
    persistenciaMenu(menuAberto);
});

// Overlay do menu mobile fecha o sidebar
menuOverlay.addEventListener('click', () => {
    sideBar.classList.remove('open-sidebar');
    menuOverlay.classList.remove('active');
    persistenciaMenu(false);
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

// Fecha a pesquisa ao sair da largura mobile
window.addEventListener('resize', () => {
    if (window.innerWidth > 700) {
        overlayPesquisa.classList.remove('active');
    }
});
