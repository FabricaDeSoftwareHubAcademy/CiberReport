const sideBar = document.getElementById('sideBar');
const suBmenus = document.getElementById('submenu');
const dropdown = document.getElementById('dropdown');
const overlay = document. getElementById('overlay-pesquisaMobile');
const pesquisaMobileBtn = document.querySelector('.pesquisa-mobile button');
const overlayPesquisa = document.querySelector('.overlay-pesquisaMobile');
const logoMenuSuperior = document.querySelector('.logo-menuSuperior');

document.getElementById('open_btn').addEventListener('click', function () {
    const isOpen = sideBar.classList.contains('open-sidebar');

    if (isOpen) {
        // Fecha o submenu primeiro, depois fecha o sidebar
        suBmenus.classList.remove('open_submenu');
        dropdown.classList.remove('open_dropdown');

        setTimeout(() => {
            sideBar.classList.remove('open-sidebar');
        }, 100); // aguarda o submenu fechar antes
    } else {
        sideBar.classList.add('open-sidebar');
    }
});

pesquisaMobileBtn.addEventListener('click', () => {
    overlayPesquisa.classList.add('active');
});

overlayPesquisa.addEventListener('click', (e) => {
    if (!e.target.closest('.barra-pesquisa')) {
        overlayPesquisa.classList.remove('active');
    }
});

logoMenuSuperior.addEventListener('click', () => {
    sideBar.classList.toggle('open-sidebar');
});

document.getElementById('dropdown').addEventListener('click', function () {
    suBmenus.classList.toggle('open_submenu');
    dropdown.classList.toggle('open_dropdown');
});
// const popup = document.getElementById('popup_menu');
// const gatilho = document.querySelector('.pai_popup');

// gatilho.addEventListener('mouseenter', () => popup.style.display = 'block');
// gatilho.addEventListener('mouseleave', () => popup.style.display = 'none');