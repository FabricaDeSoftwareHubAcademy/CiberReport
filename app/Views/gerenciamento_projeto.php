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

        <div class="group-btn-projeto">
            <button class="btn-novo-cadastro" data-modal-target="modal-cadastro-projeto"><i class="fa-solid fa-plus"></i>Novo Projeto</button>
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
            <form class="modal__body" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="cadastrar">
                <div class="form-step">
                    <div class="modal-grade">
                        <div class="campo">
                            <label class="campo__label" for="empresa_id">Cliente</label>
                            <select name="empresa_id" id="empresa_id" class="campo__select" required>
                                <option value="">Selecione um cliente</option>
                                <?php foreach ($empresas as $empresa): ?>
                                    <option value="<?= (int) $empresa['id'] ?>">
                                        <?= htmlspecialchars($empresa['nome_fantasia'] ?: $empresa['razao_social']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__label" for="nome">Nome do projeto</label>
                            <input type="text" name="nome" id="nome" required>
                        </div>

                        <div class="campo">
                            <label class="campo__label" for="data_inicio">Data de início</label>
                            <input type="date" name="data_inicio" id="data_inicio">
                        </div>

                        <div class="campo">
                            <label class="campo__label" for="data_fim_prevista">Previsão de término</label>
                            <input type="date" name="data_fim_prevista" id="data_fim_prevista">
                        </div>

                        <div class="campo">
                            <label class="campo__label" for="horas_contratadas">Horas contratadas</label>
                            <input type="number" name="horas_contratadas" id="horas_contratadas" min="0.01" step="0.01" required>
                        </div>

                        <div class="campo">
                            <label class="campo__label" for="modalidade">Modalidade</label>
                            <select name="modalidade" id="modalidade" required>
                                <option value="">Selecione</option>
                                <option value="BLACK BOX">Black Box</option>
                                <option value="GRAY BOX">Gray Box</option>
                                <option value="WHITE BOX">White Box</option>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__label" for="nivel_sigilo">Nível de sigilo</label>
                            <select name="nivel_sigilo" id="nivel_sigilo" required>
                                <option value="">Selecione</option>
                                <option value="INTERNO">Interno</option>
                                <option value="EXTERNO">Externo</option>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__label" for="escopo">Escopo</label>
                            <textarea name="escopo" id="escopo" required></textarea>
                        </div>

                        <div class="campo">
                            <label class="campo__label" for="restricao">Restrições</label>
                            <textarea name="restricao" id="restricao"></textarea>
                        </div>

                        <div class="campo">
                            <label class="campo__label" for="contrato">Contrato em PDF</label>
                            <input type="file" name="contrato" id="contrato" accept="application/pdf">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-novo-cadastro">Cadastrar projeto</button>
            </form>
        </div>
    </div>
</body>

</html>
