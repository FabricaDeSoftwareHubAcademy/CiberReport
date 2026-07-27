<?php
session_start();
$nomeUsuario = $_SESSION['usuario_nome'] ?? 'NOME';
?>

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
<?php 
$sidebarAberto = ($_COOKIE['sidebarOpen'] ?? 'true') === 'true';
$classeMenu = $sidebarAberto ? 'open-sidebar' : '';
$paginaAtual = basename($_SERVER['PHP_SELF'] ?? '');
$itemMenuAtivo = static function (string ...$paginas) use ($paginaAtual): string {
    return in_array($paginaAtual, $paginas, true) ? ' active' : '';
};
?>
    <nav id="sideBar" class="<?= $classeMenu ?>">
        <div class="sidebar_content">
            <div class="logo">
                <div class="logo-nome">
                    <img src="../assets/img/logo-cyber-report.svg" alt="" class="logo-cyber" />
                    <img src="../assets/img/logo-baikal.svg" alt="" class="logo-baikal" />
                </div>
                <div class="logo-imagem">
                    <img src="../assets/img/logo-baikal-icone.svg" alt="" />
                </div>
            </div>
            <ul id="side_itens">
                <li class="side_item<?= $itemMenuAtivo('dashboard.php') ?>">
                    <a href="#">
                        <i class="fa-solid fa-chart-column"></i>
                        <span class="item_description">Dashboard</span>
                    </a>
                    <div class="tooltip-item"><span>Dashboard</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('cliente_empresa.php') ?>">
                    <a href="cliente_empresa.php" class="margin-lef">
                        <i class="fa-solid fa-address-book"></i>
                        <span class="item_description">Clientes</span>
                    </a>
                    <div class="tooltip-item"><span>Clientes</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('gerenciamento_projeto.php') ?>">
                    <a href="gerenciamento_projeto.php">
                        <i class="fa-solid fa-terminal"></i>
                        <span class="item_description">Projetos</span>
                    </a>
                    <div class="tooltip-item"><span>Projetos</span></div>

                </li>
                <li class="side_item<?= $itemMenuAtivo('gerenciar_pentest.php') ?>">
                    <a href="gerenciar_pentest.php">
                        <i class="fa-solid fa-user-secret"></i>
                        <span class="item_description">Pentest</span>
                    </a>
                    <div class="tooltip-item"><span>Pentest</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('checklist.php') ?>">
                    <a href="checklist.php">
                        <i class="fa-solid fa-list-check"></i>
                        <span class="item_description">Checklist</span>
                    </a>
                    <div class="tooltip-item"><span>Checklist</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('vulnerabilidades.php') ?>">
                    <a href="vulnerabilidades.php">
                        <i class="fa-solid fa-bug"></i>
                        <span class="item_description">Vulnerabilidades</span>
                    </a>
                    <div class="tooltip-item"><span>Vulnerabilidades</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('conhecimento.php') ?>">
                    <a href="#">
                        <i class="fa-solid fa-book-open"></i>
                        <span class="item_description">Conhecimento</span>
                    </a>
                    <div class="tooltip-item"><span>Conhecimento</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('usuario.php') ?>">
                    <a href="usuario.php" class="margin-lef">
                        <i class="fa-solid fa-users"></i>
                        <span class="item_description">Usuários</span>
                    </a>
                    <div class="tooltip-item"><span>Usuários</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('gerenciamento_acesso.php') ?>">
                    <a href="gerenciamento_acesso.php">
                        <i class="fa-solid fa-gear"></i>
                        <span class="item_description">Perfis de Acesso</span>
                    </a>
                    <div class="tooltip-item"><span>Perfis de Acesso</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('relatorios.php') ?>">
                    <a href="#">
                        <i class="fa-solid fa-file-lines"></i>
                        <span class="item_description">Relatórios</span>
                    </a>
                    <div class="tooltip-item"><span>Relatórios</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('logs.php') ?>">
                    <a href="#">
                        <i class="fa-solid fa-clipboard"></i>
                        <span class="item_description">Logs</span>
                    </a>
                    <div class="tooltip-item"><span>Logs</span></div>
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
            <form class="input-pesquisaSuperior">
                <input type="text" placeholder="Buscar..." id="busca" />
                <button>
                    <i class="fa-brands fa-sistrix"></i>
                </button>
            </form>

            <!-- pesquisa para formato mobile -->
            <div class="pesquisa-mobile">
                <button>
                    <i class="fa-brands fa-sistrix" class='color: #9ca3af;'></i>
                </button>
            </div>
            <!-- overlay-pesquisa -->
            <form class="overlay-pesquisaMobile">
                <div class="barra-pesquisa" id="busca-mobile">
                    <input type="text" placeholder="Buscar..." />
                    <button>
                        <i class="fa-brands fa-sistrix"></i>
                    </button>
                </div>
            </form>
            <div class="perfis">
                <i class="fa-regular fa-bell notificacao"></i>
                <img class="imagem-usuario" src="../assets/img/foto-perfil.jpg" alt="" />
                <div class="description-user">
                    <p><?= $nomeUsuario?></p>
                    <p>Gerente</p>
                </div>
            </div>
        </header>

        <script src="../assets/JS/componentes/menu.js"></script>
