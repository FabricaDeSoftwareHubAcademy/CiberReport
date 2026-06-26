<header id="menu-superior">
    <div class="logo-menuSuperior">
        <img src="../assets/img/logo-baikal-icone.svg" alt="" />
    </div>
    <h1><?= $tituloPagina ?? 'Título da Página' ?></h1>
    <div class="input-pesquisaSuperior">
        <input type="text" placeholder="Buscar..." />
        <button>
            <i class="fa-brands fa-sistrix"></i>
        </button>
    </div>
    <div class="pesquisa-mobile">
        <button>
            <i class="fa-brands fa-sistrix"></i>
        </button>
    </div>
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
            <p>Marcos Antonio</p>
            <p>Gerente</p>
        </div>
    </div>
</header>