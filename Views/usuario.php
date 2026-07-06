<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SideBar</title>
    <!-- scripts -->
    <script src="../Assets/JS/componentes/menu.js" defer></script>
    <!-- links -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/CSS/style.css" />
    <link rel="stylesheet" href="../assets/CSS/Pages/usuario.css" />
    
</head>

<body>

    <?php $tituloPagina = 'Usuarios';
    include_once 'menu.php'; ?>
    <main>
        <div class="corpo_card">
            <div class="area-modal">
                <div class="cabecalho">
                    <img class="icone-cabecalho" src="../assets/img/usercab.svg" alt="" />
                    <div class="texto-cabecalho">
                        <div class="elemento-cabecalho">
                            <div class="textos-caebecalho">
                                <h2>Gerenciamento de Usuário</h2>
                                <span>Informações da empresa contratante e do responsável técnico</span>
                            </div>
                        </div>
                        <span class="fechar">X</span>
                    </div>
                </div>
                <h4 style="padding-left: 12px">dados do funcionario</h4>
                <div class="itens_mid">
                    <div class="container-foto">
                        <div class="foto_do_user">
                            <img id="icon_ft" src="../assets/img/usermid.svg" />
                        </div>
                        <h4>alterar foto do usuario</h4>
                    </div>
                </div>

                <div class="campos">
                    <div class="container-infoA">
                        <label>Nome</label> <br />
                        <input class="imputA" type="text" placeholder="digite seu nome" />
                    </div>
                    <div class="posicionamento">
                        <div class="container-info">
                            <label>Email</label> <br />
                            <input class="imputB" type="tex" placeholder="empresa@gmail.com" />
                        </div>
                        <div class="container-info">
                            <label class="imputC">Telefone</label> <br />
                            <input class="imputC" type="tex" placeholder="(11)99999-9999" />
                        </div>
                    </div>
                    <div class="posicionamento">
                        <div class="container-info">
                            <label>CPF</label> <br />
                            <input class="imputD" type="text" placeholder="000.000.000-00" />
                        </div>
                        <div class="input-group">
                            <label id="cargo">Cargo</label> <br />
                            <select id="cargo">
                                <option selected disabled>Selecione um cargo</option>
                                <option>Analista</option>
                                <option>administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="input-groupA">
                        <label id="especialidade">Especialidades</label> <br />
                        <select id="especialidade">
                            <option selected disabled>Selecione uma especialidade</option>
                            <option>Mobile</option>
                            <option>Web</option>
                            <option>front</option>
                            <option>Back</option>
                        </select>
                    </div>
                </div>
                <div class="botoes">
                    <button id="botaoA">cancelar</button>
                    <button id="botaoB">Salvar</button>
                </div>
            </div>
        </div>
    </main>
    </div>
    </div>

</html>
</div>
<script src="src\components\menu\menu.js"></script>