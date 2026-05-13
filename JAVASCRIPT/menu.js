const sideBar = document.getElementById('sideBar');

document.getElementById('open_btn').addEventListener('click', function() {
    sideBar.classList.toggle('open-sidebar');
});

const submenu = document.getElementById('submenu');

document.getElementById('dropdown').addEventListener('click', function(){
    submenu.classList.toggle('open_submenu');
})

const dropdown = document.getElementById("dropdown")

document.getElementById('dropdown').addEventListener('click', function(){
    dropdown.classList.toggle('open_dropdown');
})