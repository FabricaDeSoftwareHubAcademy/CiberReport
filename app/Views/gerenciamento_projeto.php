<?php
use Controller\ProjetoController;

$controller = new ProjetoController();
$resultadoCadastro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cadastrar') {
    $resultadoCadastro = $controller->cadastrar();
}

$dados = $controller->listar();
$empresas = $controller->listarEmpresasAtivas();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Projetos</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Pages/gerenciamento-projeto.css">
    <script src="<?= BASE_URL ?>app/assets/JS/componentes/tabela.js" defer></script>
    <script src="<?= BASE_URL ?>app/assets/JS/componentes/modal.js" defer></script>
    <script src="<?= BASE_URL ?>app/assets/JS/barraDePesquisa.js" defer></script>
</head>

<body class="corpo-ger-projetos">
    <?php
        $tituloPagina = 'Gerenciamento de Projeto';
        include 'Components/menu.php';
    ?>
    <main class="main-gerenciamento-projeto">
        <?php if ($resultadoCadastro !== null): ?>
            <p role="status"><?= htmlspecialchars((string) $resultadoCadastro) ?></p>
        <?php endif; ?>


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

            <div class="group-btn-projeto">
                <button class="btn-novo-cadastro" data-modal-target="modal-cadastro-projeto"><i class="fa-solid fa-plus"></i>Novo Projeto</button>
            </div>

            <div class="tabela-wrapper tabela-gerenciamento-projeto">
                <table id="table">
                    <thead>
                        <tr>
                            <th data-col="0">
                                <span class="th-label">Empresa <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="1">
                                <span class="th-label">Projeto <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="2">
                                <span class="th-label">Modalidade <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="3">
                                <span class="th-label">Sigilo <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="4" data-filtro="data">
                                <span class="th-label">Data Inicio <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="5" data-filtro="data">
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
                                <td><?= htmlspecialchars($projeto['modalidade']) ?></td>
                                <td><?= htmlspecialchars($projeto['nivel_sigilo']) ?></td>
                                <td><?= htmlspecialchars($projeto['data_inicio'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($projeto['data_fim_prevista'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($projeto['status']) ?></td>
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

    <div class="modal-overlay" data-modal-target="modal-cadastro-projeto" id="modal-cadastro-projeto">
        <div class="modal modal--xl">
            <div class="modal__header">
                <div class="modal__header-icone"><i class=""></i></div>
                <div class="modal__header-texto">
                    <h2 class="modal__titulo">Cadastro de Projeto</h2>
                    <p class="modal__subtitulo">Informações da empresa contratante e do projeto</p>
                </div>
                <button class="modal__fechar" data-modal-close="modal-cadastro-projeto"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal__body">
                <form action="" method="post">
                    <section class="modal__secao">
                        <h3 class="modal__secao-titulo">Informações do Cliente</h3>
                        <div class="modal__secao-conteudo">
                            <div class="input-group">
                                <label for="nome_cliente">Nome da Empresa</label>
                                <input type="text" name="nome_cliente" id="nome_cliente" placeholder="Digite o nome da empresa" required>
                            </div>
                            <div class="input-group">
                                <label for="nome_projeto">Nome do Projeto</label>
                                <input type="text" name="nome_projeto" id="nome_projeto" placeholder="Digite o nome do projeto" required>
                            </div>
                        </div>
                    </section>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
