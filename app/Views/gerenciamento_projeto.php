<?php
use Controller\ProjetoController;

$controller = new ProjetoController();
$dados = $controller->listar();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Projetos</title>
    <link rel="stylesheet" href="../assets/CSS/style.css">
    <link rel="stylesheet" href="../assets/CSS/Pages/gerenciamento-projeto.css">
    <script src="../assets/JS/componentes/tabela.js" defer></script>
    <script src="../assets/JS/componentes/modal.js" defer></script>
    <script src="../assets/JS/barraDePesquisa.js" defer></script>
</head>
<body class="corpo-ger-projetos">
    <?php
        $tituloPagina = 'Gerenciamento de Projeto';
        include 'Components/menu.php';
    ?>
    <main class="main-gerenciamento-projeto">
        <div class="group-btn-projeto">
            <button class="btn-novo-cadastro btn-projeto" data-modal-target="modal-cadastro-projeto"><span>+</span>Novo Projeto</button>
        </div>

        <div class="group-card-tabela">
            <div class="group-card-projeto">
                <div class="container-card-projetos" id="projeto-ativo">
                    <h2 class="title-card-projeto">Projetos Ativos</h2>
                    <span class="subtitle-card-projeto">12</span>
                </div>
                <div class="container-card-projetos" id="projeto-ativo">
                    <h2 class="title-card-projeto">Aguardando Início</h2>
                    <span class="subtitle-card-projeto">04</span>
                </div>
                <div class="container-card-projetos" id="projeto-ativo">
                    <h2 class="title-card-projeto">Relatório Pendentes</h2>
                    <span class="subtitle-card-projeto">03</span>
                </div>
                <div class="container-card-projetos" id="projeto-ativo">
                    <h2 class="title-card-projeto">Vulnerabilidades Críticas</h2>
                    <span class="subtitle-card-projeto">07</span>
                </div>
            </div>
    
            <div class="tabela-wrapper tabela-gerenciamento-projeto">
                <table id="table">
                    <thead>
                        <tr>
                            <th data-col="0">
                                <span class="th-label">ID <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="1">
                                <span class="th-label">Projeto <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="2">
                                <span class="th-label">Resp. Téc <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="3">
                                <span class="th-label">Tipo do teste <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="4" data-tipo="data">
                                <span class="th-label">Data Inicio <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="5" data-tipo="data">
                                <span class="th-label">Data Fim <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="6">
                                <span class="th-label">Status <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dados as $projeto): ?>
                            <tr>
                                <td><?= htmlspecialchars($projeto['nome_fantasia']) ?></td>
                                <td><?= htmlspecialchars($projeto['nome']) ?></td>
                                <td><?= '-' ?></td>
                                <td><?= htmlspecialchars($projeto['habilitado']) ?></td>
                                <td><?= htmlspecialchars($projeto['data_inicio']) ?></td>
                                <td><?= htmlspecialchars($projeto['data_fim_prevista']) ?></td>
                                <td><?= htmlspecialchars($projeto['data_fim_prevista']) ?></td>
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
                        <?php endforeach; ?>
                        <?php if (empty($dados)): ?>
                            <tr>
                                <td colspan="8" style="text-align:center"> Não há nenhum registro.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8" class="rodape-tabela">
                                <div class="paginacao"></div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
    </main>

    <div class="modal-overlay" id="modal-cadastro-projeto">
        <div class="modal modal--md">
            <div class="modal__header">
                <div class="modal__header-icone"><i class="fa-solid fa-shield"></i></div>
                <div class="modal__header-texto">
                    <h2 class="modal__titulo">Cadastro de Projeto</h2>
                    <p class="modal__subtitulo">Informações da empresa contratante e do projeto</p>
                </div>
                <button class="modal__fechar" data-modal-close="modal-cadastro-projeto">X</button>
            </div>
            <form class="modal__body">
                <div class="form-step">
                    <div class="modal-grade">
                        <div class="campo">
                            <label class="campo__label" for="nome-cleinte">cliente</label>
                            <select name="cliente" id="cliente" aria-placeholder="Selecione um cliente" class="campo__select">
    
                            </select>
                            <button class="btn-novo-cadastro">+</button>
                        </div>
                </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
