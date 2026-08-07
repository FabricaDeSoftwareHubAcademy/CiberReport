const sideBar = document.getElementById('sideBar');
const sobrePerfil = document.getElementById('sobrePerfil');
const imagemUsuario = document.querySelector('.imagem-usuario');
const dropdown = document.getElementById('dropdown');
const pesquisaMobileBtn = document.querySelector('.pesquisa-mobile button');
const overlayPesquisa = document.querySelector('.overlay-pesquisaMobile');
const logoMenuSuperior = document.querySelector('.logo-menuSuperior');
const menuOverlay = document.querySelector('.menuOverlay');

function persistenciaMenu(valor){
    document.cookie = `sidebarOpen=${valor}; path=/; max-age=900000`
}

const botaoMenu = document.getElementById('open_btn');

function atualizarEstadoMenu(aberto) {
    sideBar.classList.toggle('open-sidebar', aberto);
    menuOverlay.classList.toggle('active', aberto && window.innerWidth <= 931);
    botaoMenu.setAttribute('aria-expanded', String(aberto));
    persistenciaMenu(aberto);
}

botaoMenu.addEventListener('click', function () {
    atualizarEstadoMenu(!sideBar.classList.contains('open-sidebar'));
});

// Logo do menu superior abre sidebar (mobile)
logoMenuSuperior.addEventListener('click', () => {
    atualizarEstadoMenu(!sideBar.classList.contains('open-sidebar'));
});

// Overlay do menu mobile fecha o sidebar
menuOverlay.addEventListener('click', () => {
    atualizarEstadoMenu(false);
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

imagemUsuario.addEventListener('click', (evento) => {
    evento.stopPropagation();
    sobrePerfil.classList.toggle('active');
});

sobrePerfil.addEventListener('mouseleave', () => {
    sobrePerfil.classList.remove('active');
});

document.addEventListener('click', () => {
    sobrePerfil.classList.remove('active');
});

// Fecha a pesquisa ao sair da largura mobile
window.addEventListener('resize', () => {
    if (window.innerWidth > 700) {
        overlayPesquisa.classList.remove('active');
    }
});
