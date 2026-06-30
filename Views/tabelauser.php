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
    <link rel="stylesheet" href="../Assets/CSS/style.css" />
</head>

<body>


    <?php $tituloPagina = 'Tabela User';
    include_once 'menu.php'; ?>

    <main>
        <button class="btn-novo-cadastro"><i class="fa-solid fa-plus"></i>Novo Cadastro</button>


        <div class="tabela-wrapper">
            <table>
                <thead>
                    <tr>
                        <th data-col="0">
                            <span class="th-label">Nome do Usuario <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="1">
                            <span class="th-label">Cargo <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="2">
                            <span class="th-label">Resp. Téc <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="3">
                            <span class="th-label">Email <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="4" data-tipo="data">
                            <span class="th-label">Telefone<i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="5">
                            <span class="th-label">Status <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="6">
                            <span class="th-label">Ações <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>User_01</td>
                        <td>Analista</td>
                        <td>André</td>
                        <td>user_1@exemplo.com</td>
                        <td>67 9999-9999</td>
                        <td>
                            <span class="status status-ativo">Ativo</span>
                        </td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>User_02</td>
                        <td>Analista</td>
                        <td>Mariana</td>
                        <td>user_2@exemplo.com</td>
                        <td>67 9999-9999</td>
                        <td>
                            <span class="status status-ativo">Ativo</span>
                        </td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>User_03</td>
                        <td>administrador</td>
                        <td>Rafael</td>
                        <td>User_03@example.com</td>
                        <td>67 9999-9999</td>
                        <td>
                            <span class="status status-inativo">Inativo</span>
                        </td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>User_04</td>
                        <td>junior</td>
                        <td>Beatriz</td>
                        <td>user_4@exemplo.com</td>
                        <td>67 9999-9999</td>
                        <td>
                            <span class="status status-ativo">Ativo</span>
                        </td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>User_05</td>
                        <td>administrador</td>
                        <td>Lucas</td>
                        <td>user_5@exemplo.com</td>
                        <td>67 9999-9999</td>
                        <td>
                            <span class="status status-inativo">Inativo</span>
                        </td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>User_06</td>
                        <td>Analista</td>
                        <td>Camila</td>
                        <td>user_6@exemplo.com</td>
                        <td>67 9999-9999</td>
                        <td>
                            <span class="status status-ativo">Ativo</span>
                        </td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>User_07</td>
                        <td>junior</td>
                        <td>Thiago</td>
                        <td>user_7@exemplo.com</td>
                        <td>67 9999-9999</td>

                        <td>
                            <span class="status status-ativo">Ativo</span>
                        </td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>User_08</td>
                        <td>administrador</td>
                        <td>Júlia</td>
                        <td>user_8@exemplo.com</td>
                        <td>67 9999-9999</td>
                        <td>
                            <span class="status status-ativo">Ativo</span>
                        </td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>User_09</td>
                        <td>administrador</td>
                        <td>Felipe</td>
                        <td>user_9@exemplo.com</td>
                        <td>67 9999-9999</td>
                        <td>
                            <span class="status status-ativo">Ativo</span>
                        </td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>User_10</td>
                        <td>Analista</td>
                        <td>Renata</td>
                        <td>user_10@example.com</td>
                        <td>67 0000-0000</td>
                        <td>
                            <span class="status status-ativo">Ativo</span>
                        </td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" class="rodape-tabela">
                            <div class="paginacao">
                                <button class="pag-btn" aria-label="Página anterior">Anterior</button>
                                <button class="pag-num ativo" aria-label="Página 1" aria-current="page">1</button>
                                <button class="pag-num" aria-label="Página 2">2</button>
                                <button class="pag-num" aria-label="Página 3">3</button>
                                <button class="pag-num" aria-label="Página 4">4</button>
                                <button class="pag-num" aria-label="Página 5">5</button>
                                <button class="pag-num" aria-label="Página 6">6</button>
                                <button class="pag-btn" aria-label="Próxima página">Próximo</button>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <script src="../Assets/JS/componentes/tabela.js"></script>
</body>

</html>
</main>
</div>
</div>

</body>
</div>

</html>
<script src="src\components\menu\menu.js"></script>