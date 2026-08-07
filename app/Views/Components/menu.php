<?php

namespace Components;

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
    $rotaAtual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $itemMenuAtivo = static function (string ...$rotas) use ($rotaAtual): string {
        return in_array($rotaAtual, $rotas, true) ? ' active' : '';
    };
    ?>
    <nav id="sideBar" class="<?= $classeMenu ?>">
        <div class="sidebar_content">
            <div class="logo">
                <div class="logo-nome">
                    <img src="<?= BASE_URL ?>app/assets/img/logo-cyber-report.svg" alt="" class="logo-cyber" />
                    <img src="<?= BASE_URL ?>app/assets/img/logo-baikal.svg" alt="" class="logo-baikal" />
                </div>
                <div class="logo-imagem">
                    <img src="<?= BASE_URL ?>app/assets/img/logo-baikal-icone.svg" alt="" />
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
                    <a href="<?= BASE_URL ?>cliente-empresa" class="margin-lef">
                        <i class="fa-solid fa-address-book"></i>
                        <span class="item_description">Clientes</span>
                    </a>
                    <div class="tooltip-item"><span>Clientes</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('gerenciamento_projeto.php') ?>">
                    <a href="<?= BASE_URL ?>gerenciamento-projeto">
                        <i class="fa-solid fa-terminal"></i>
                        <span class="item_description">Projetos</span>
                    </a>
                    <div class="tooltip-item"><span>Projetos</span></div>

                </li>
                <li class="side_item<?= $itemMenuAtivo('gerenciar-tipo-pentest.php') ?>">
                    <a href="<?= BASE_URL ?>gerenciar-pentest">
                        <i class="fa-solid fa-user-secret"></i>
                        <span class="item_description">Pentest</span>
                    </a>
                    <div class="tooltip-item"><span>Pentest</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('checklist.php') ?>">
                    <a href="<?= BASE_URL ?>checklist">
                        <i class="fa-solid fa-list-check"></i>
                        <span class="item_description">Checklist</span>
                    </a>
                    <div class="tooltip-item"><span>Checklist</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('vulnerabilidades.php') ?>">
                    <a href="<?= BASE_URL ?>vulnerabilidades">
                        <i class="fa-solid fa-bug"></i>
                        <span class="item_description">Vulnerabilidades</span>
                    </a>
                    <div class="tooltip-item"><span>Vulnerabilidades</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('conhecimento.php') ?>">
                    <a href="<?= BASE_URL ?>conhecimento.php">
                        <i class="fa-solid fa-book-open"></i>
                        <span class="item_description">Conhecimento</span>
                    </a>
                    <div class="tooltip-item"><span>Conhecimento</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('usuario.php') ?>">
                    <a href="<?= BASE_URL ?>usuario" class="margin-lef">
                        <i class="fa-solid fa-users"></i>
                        <span class="item_description">Usuários</span>
                    </a>
                    <div class="tooltip-item"><span>Usuários</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('gerenciamento_acesso.php') ?>">
                    <a href="<?= BASE_URL ?>gerenciamento-acesso">
                        <i class="fa-solid fa-gear"></i>
                        <span class="item_description">Perfis de Acesso</span>
                    </a>
                    <div class="tooltip-item"><span>Perfis de Acesso</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('relatorios.php') ?>">
                    <a href="<?= BASE_URL ?>relatorio.php">
                        <i class="fa-solid fa-file-lines"></i>
                        <span class="item_description">Relatórios</span>
                    </a>
                    <div class="tooltip-item"><span>Relatórios</span></div>
                </li>
                <li class="side_item<?= $itemMenuAtivo('logs.php') ?>">
                    <a href="<?= BASE_URL ?>logs.php">
                        <i class="fa-solid fa-clipboard"></i>
                        <span class="item_description">Logs</span>
                    </a>
                    <div class="tooltip-item"><span>Logs</span></div>
                </li>
            </ul>
            <button id="open_btn" type="button" aria-label="Abrir ou fechar menu" aria-expanded="<?= $sidebarAberto ? 'true' : 'false' ?>">
                <i class="fa-solid fa-chevron-right" id="open_btn_icon"></i>
            </button>
        </div>
        <div id="logout">
            <a id="logout_btn" href="<?= BASE_URL ?>login">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span class="item_description">Logout</span>
            </a>
        </div>
    </nav>
    <div class="menuOverlay"></div>
    <!-- <p></p> -->
    <div class="main-content">
        <header id="menu-superior">
            <div class="logo-menuSuperior">
                <img src="<?= BASE_URL ?>app/assets/img/logo-baikal-icone.svg" alt="" />
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
                <img class="imagem-usuario" src="<?= BASE_URL ?>app/assets/img/foto-perfil.jpg" alt="" />
                <div class="description-user">
                    <p><?= $nomeUsuario ?></p>
                    <p>Gerente</p>
                </div>
            </div>
        </header>

        <script src="<?= BASE_URL ?>app/assets/JS/componentes/menu.js"></script>