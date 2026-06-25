<!--
    COMO USAR ESTE INCLUDE:
    1. Seu arquivo deve ter extensão .php
    2. Inclua no início do <body>: 
    3. Envolva o conteúdo da sua página em <div class="main-content">

    No <head> da sua página adicione:
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" ... />
    <link rel="stylesheet" href="../Assets/CSS/style.css" />
-->
<nav id="sideBar">
    <div class="sidebar_content">
        <div class="logo">
            <div class="logo-nome">
                <img src="../assets/img/logo-cyber-report.svg" alt="" />
                <img src="../assets/img/logo-baikal.svg" alt="" />
            </div>
            <div class="logo-imagem">
                <img src="../assets/img/logo-baikal-icone.svg" alt="" />
            </div>
        </div>
        <ul id="side_itens">
            <li class="side_item">
                <a href="#">
                    <i class="fa-solid fa-chart-column"></i>
                    <span class="item_description">Dashboard</span>
                </a>
                <div class="tooltip-item"><span>Dashboard</span></div>
            </li>
            <li class="side_item">
                <a href="#">
                    <i class="fa-solid fa-file-lines"></i>
                    <span class="item_description">Relatórios</span>
                </a>
                <div class="tooltip-item"><span>Relatórios</span></div>
            </li>
            <li class="side_item">
                <a href="#" id="dropdown_item">
                    <i class="fa-solid fa-building-user"></i>
                    <span class="item_description">Gestão</span>
                    <i class="fa-solid fa-angle-down" id="dropdown"></i>
                </a>
                <div class="tooltip-item"><span>Gestão</span></div>
            </li>
            <ul id="submenu">
                <li class="item-submenu">
                    <a href="#">
                        <i class="fa-solid fa-users"></i>
                        <span class="item_description">Usuários</span>
                    </a>
                </li>
                <li class="item-submenu">
                    <a href="#">
                        <i class="fa-solid fa-address-book"></i>
                        <span class="item_description">Clientes</span>
                    </a>
                </li>
                <li class="item-submenu">
                    <a href="#">
                        <i class="fa-solid fa-terminal"></i>
                        <span class="item_description">Projetos</span>
                    </a>
                </li>
                <li class="item-submenu">
                    <a href="#">
                        <i class="fa-solid fa-user-secret"></i>
                        <span class="item_description">Pentest</span>
                    </a>
                </li>
            </ul>
            <li class="side_item">
                <a href="#">
                    <i class="fa-solid fa-list-check"></i>
                    <span class="item_description">Checklist</span>
                </a>
                <div class="tooltip-item"><span>Checklist</span></div>
            </li>
            <li class="side_item">
                <a href="#">
                    <i class="fa-solid fa-bug"></i>
                    <span class="item_description">Vulnerabilidades</span>
                </a>
                <div class="tooltip-item"><span>Vulnerabilidades</span></div>
            </li>
            <li class="side_item">
                <a href="#">
                    <i class="fa-solid fa-book-open"></i>
                    <span class="item_description">Conhecimento</span>
                </a>
                <div class="tooltip-item"><span>Conhecimento</span></div>
            </li>
            <li class="side_item">
                <a href="#">
                    <i class="fa-solid fa-clipboard"></i>
                    <span class="item_description">Logs</span>
                </a>
                <div class="tooltip-item"><span>Logs</span></div>
            </li>
            <li class="side_item">
                <a href="#">
                    <i class="fa-solid fa-gear"></i>
                    <span class="item_description">Perfis de Acesso</span>
                </a>
                <div class="tooltip-item"><span>Perfis de Acesso</span></div>
            </li>
        </ul>
        <button id="open_btn">
            <i class="fa-solid fa-chevron-right" id="open_btn_icon"></i>
        </button>
    </div>
    <div id="logout">
        <button id="logout_btn">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            <span class="item_description"> Logout </span>
        </button>
    </div>
</nav>
<div class="menuOverlay"></div>
<script src="../Assets/JS/componentes/menu.js" defer></script>