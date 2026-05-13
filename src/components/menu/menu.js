const sideBar = document.getElementById('sideBar');
const suBmenus = document.getElementById('submenu');
const dropdown = document.getElementById('dropdown');

document.getElementById('open_btn').addEventListener('click', function () {
    const isOpen = sideBar.classList.contains('open-sidebar');

    if (isOpen) {
        // Fecha o submenu primeiro, depois fecha o sidebar
        suBmenus.classList.remove('open_submenu');
        dropdown.classList.remove('open_dropdown');

        setTimeout(() => {
            sideBar.classList.remove('open-sidebar');
        }, 400); // aguarda o submenu fechar antes
    } else {
        sideBar.classList.add('open-sidebar');
    }
});

document.getElementById('dropdown').addEventListener('click', function () {
    suBmenus.classList.toggle('open_submenu');
    dropdown.classList.toggle('open_dropdown');
});
// const popup = document.getElementById('popup_menu');
// const gatilho = document.querySelector('.pai_popup');

// gatilho.addEventListener('mouseenter', () => popup.style.display = 'block');
// gatilho.addEventListener('mouseleave', () => popup.style.display = 'none');