<!--
    COMO USAR O MENU LATERAL
    ========================

    PASSO 1 — inclua o menu no topo do <body> com o título da página:
        $tituloPagina = 'Nome da Página'; include_once 'Components/menu.php'; Envolva essa linha com a tag do php

    PASSO 2 — adicione o conteúdo dentro de uma tag <main>:
        <main>
            conteúdo da página...
        </main>

    PASSO 3 — OBRIGATÓRIO: após o </main>, feche as divs abertas pelo menu:
        primeira  </div> → fecha .main-content
        segunda   </div> → fecha .menu

    Sem esse fechamento o <main> fica fora do flex row da sidebar
    e o efeito de "empurrar" o conteúdo não funciona.
-->

<div class="menu">
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
                        <a href="usuario.html" class="margin-lef">
                            <i class="fa-solid fa-users"></i>
                            <span class="item_description">Usuários</span>
                        </a>
                    </li>
                    <li class="item-submenu">
                        <a href="cliente_empresa.php"class="margin-lef">
                            <i class="fa-solid fa-address-book"></i>
                            <span class="item_description">Clientes</span>
                        </a>
                    </li>
                    <li class="item-submenu">
                        <a href="gerenciamento_projeto.php">
                            <i class="fa-solid fa-terminal"></i>
                            <span class="item_description">Projetos</span>
                        </a>
                    </li>
                    <li class="item-submenu">
                        <a href="gerenciar_pentest.php">
                            <i class="fa-solid fa-user-secret"></i>
                            <span class="item_description">Pentest</span>
                        </a>
                    </li>
                </ul>
                <li class="side_item">
                    <a href="checklist.php">
                        <i class="fa-solid fa-list-check"></i>
                        <span class="item_description">Checklist</span>
                    </a>
                    <div class="tooltip-item"><span>Checklist</span></div>
                </li>
                <li class="side_item">
                    <a href="vulnerabilidades.php">
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
                    <a href="gerenciamento_perfis">
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
            <a href="login.php">
                <button id="logout_btn">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span class="item_description"> Logout </span>
                </button>
            </a>
        </div>
    </nav>
    <div class="menuOverlay"></div>
    <!-- <p></p> -->
    <div class="main-content">
        <header id="menu-superior">
            <div class="logo-menuSuperior">
                <img src="../assets/img/logo-baikal-icone.svg" alt="" />
            </div>
            <h1><?= htmlspecialchars($tituloPagina ?? 'Título da Página') ?></h1>
            <!-- barra de pesquisa para desktop -->
            <div class="input-pesquisaSuperior">
                <input type="text" placeholder="Buscar..." />
                <button>
                    <i class="fa-brands fa-sistrix"></i>
                </button>
            </div>

            <!-- pesquisa para formato mobile -->
            <div class="pesquisa-mobile">
                <button>
                    <i class="fa-brands fa-sistrix"></i>
                </button>
            </div>
            <!-- overlay-pesquisa -->
            <div class="overlay-pesquisaMobile">
                <div class="barra-pesquisa">
                    <input type="text" placeholder="Buscar..." />
                    <button>
                        <i class="fa-brands fa-sistrix"></i>
                    </button>
                </div>
            </div>
            <div class="perfis">
                <i class="fa-regular fa-bell notificacao"></i>
                <img class="imagem-usuario" src="../assets/img/foto-perfil.jpg" alt="" />
                <div class="description-user">
                    <p>Marcos antonio</p>
                    <p>Gerente</p>
                </div>
            </div>
        </header>

        <script src="../assets/JS/componentes/menu.js"></script>