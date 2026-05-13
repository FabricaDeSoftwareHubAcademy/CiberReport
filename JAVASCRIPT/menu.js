const sideBar = document.getElementById('sideBar');

document.getElementById('open_btn').addEventListener('click', function() {
    sideBar.classList.toggle('open-sidebar');
});

const suBmenus = document.getElementById('submenu');

document.getElementById('dropdown').addEventListener('click', function(){
    suBmenus.classList.toggle('open_submenu');
});

const dropdown = document.getElementById("dropdown")

document.getElementById('dropdown').addEventListener('click', function(){
    dropdown.classList.toggle('open_dropdown');
});

const popup = document.getElementById('popup_menu');
const gatilho = document.querySelector('.pai_popup');

gatilho.addEventListener('mouseenter', () => popup.style.display = 'block');
gatilho.addEventListener('mouseleave', () => popup.style.display = 'none');