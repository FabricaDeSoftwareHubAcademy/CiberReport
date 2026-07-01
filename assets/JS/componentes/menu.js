const sideBar = document.getElementById('sideBar');
const suBmenus = document.getElementById('submenu');
const dropdown = document.getElementById('dropdown');
const pesquisaMobileBtn = document.querySelector('.pesquisa-mobile button');
const overlayPesquisa = document.querySelector('.overlay-pesquisaMobile');
const logoMenuSuperior = document.querySelector('.logo-menuSuperior');
const menuOverlay = document.querySelector('.menuOverlay');

// Botão de abrir/fechar sidebar (desktop)
document.getElementById('open_btn').addEventListener('click', function () {
    const isOpen = sideBar.classList.contains('open-sidebar');

    if (isOpen) {
        suBmenus.classList.remove('open_submenu');
        dropdown.classList.remove('open_dropdown');
        menuOverlay.classList.remove('active');
        setTimeout(() => {
            sideBar.classList.remove('open-sidebar');
        }, 100);
    } else {
        sideBar.classList.add('open-sidebar');
    } 
    // suBmenus.classList.add('fechado');
});

// Dropdown de Gestão
document.getElementById('dropdown_item').addEventListener('click', function () {
    suBmenus.classList.toggle('open_submenu');
    dropdown.classList.toggle('open_dropdown');
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
    suBmenus.classList.remove('open_submenu');
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