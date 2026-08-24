<?php

namespace Components;
$nomeUsuario = $_SESSION['usuario_nome'] ?? 'NOME';
require_once __DIR__ . '/sobre_perfil.php';
?>

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
                <li class="side_item<?= $itemMenuAtivo('dashboard_gestor.php') ?>">
                    <a href="<?= BASE_URL ?>dashboard-gestor">
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
                <li class="side_item<?= $itemMenuAtivo('gerenciar_tipo_pentest.php') ?>">
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
            <form action="<?= BASE_URL ?>logout" method="post">
                <button id="logout_btn" type="submit">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span class="item_description">Logout</span>
                </button>
            </form>
        </div>
    </nav>
    <div class="menuOverlay"></div>
    <!-- <p></p> -->
    <div class="main-content">
        <header id="menu-superior">
           <div class="logo-menuSuperior">
                <img src="<?= BASE_URL ?>app/assets/img/logo-baikal-icone.svg" alt="" />
            </div> 
            <div class="tituloMenuSuperior">
                <h1><?= htmlspecialchars($tituloPagina ?? 'Título da Página') ?></h1>
            </div>
            <!-- barra de pesquisa para desktop -->
            <form class="input-pesquisaSuperior">
                <input type="text" placeholder="Buscar..." id="busca" />
                <button>
                    <i class="fa-brands fa-sistrix"></i>
                </button>
            </form>
            
            <div class="perfis">
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
                <i class="fa-regular fa-bell notificacao"></i>
                <img class="imagem-usuario" src="<?= BASE_URL ?>app/assets/img/foto-perfil.jpg" alt="" />
                <div id="sobrePerfil">
                    <div class="sobre-perfil__dados">
                        <div class="sobre-perfil__identificacao">
                            <strong><?= htmlspecialchars($nomeUsuario) ?></strong>
                            <span>Gerente</span>
                        </div>
                    </div>
                    <button type="button" class="sobre-perfil__editar" data-modal-target="modalSobrePerfil">
                        <i class="fa-regular fa-pen-to-square"></i>
                        Editar Perfil
                    </button>
                </div>
                <div class="description-user">
                    <p><?= $nomeUsuario ?></p>
                    <p>Gerente</p>
                </div>
            </div>
        </header>

        <script src="<?= BASE_URL ?>app/assets/JS/componentes/menu.js"></script>
