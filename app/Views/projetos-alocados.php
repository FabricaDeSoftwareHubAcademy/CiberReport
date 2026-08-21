<?php
require_once __DIR__ . "/../Controller/ProjetosAlocadosController.php";
use Controller\ProjetosAlocadosController;

$controller = new ProjetosAlocadosController();

$dados = $controller->listarProjetosAlocados();
$totalAlocados = $controller->contarProjetosAlocados();
$totalAtrasados = $controller->contarProjetosAtrasados();

$dados_visualizar = [];
if (isset($_GET['id_projeto_alocado_visualizar'])) {
    $id_visualizar = addslashes($_GET['id_projeto_alocado_visualizar']);
    $dados_visualizar = $controller->buscarProjetoDetalhado($id_visualizar);
}

$vulnerabilidades_do_projeto = [];
if (isset($_GET['id_projeto_alocado_vulnerabilidades'])) {
    $id_vulnerabilidades = addslashes($_GET['id_projeto_alocado_vulnerabilidades']);
    $vulnerabilidades_do_projeto = $controller->buscarVulnerabilidadesDoProjeto($id_vulnerabilidades);
}
?>

<link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Componentes/button.css">
<link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Componentes/tabela.css">
<link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Componentes/componentes-modal.css">
<link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Componentes/modal.css">
<link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Pages/projetos_alocados.css">

<?php $tituloPagina = 'Projetos Alocados'; include 'Components/menu.php'; ?>
<main>

    <div class="projetos-alocados-container">

        <div class="projetos-alocados-cards">
            <div class="projetos-alocados-card">
                <span class="projetos-alocados-card-label">Quantidade de Projetos Alocados</span>
                <span class="projetos-alocados-card-valor projetos-alocados-valor-alocados"><?= $totalAlocados ?></span>
            </div>
            <div class="projetos-alocados-card">
                <span class="projetos-alocados-card-label">Projetos Atrasados</span>
                <span class="projetos-alocados-card-valor projetos-alocados-valor-atrasados"><?= $totalAtrasados ?></span>
            </div>
        </div>

        <div class="modal-overlay<?= !empty($dados_visualizar) ? ' active' : '' ?>" id="modalProjetosAlocadosVisualizar">
            <div class="modal modal--lg">

                <div class="modal__header">
                    <div class="modal__header-icone">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Detalhes do Projeto</h2>
                        <p class="modal__subtitulo">Escopo, cronograma e status do teste</p>
                    </div>
                    <button type="button" class="modal__fechar" data-modal-close="modalProjetosAlocadosVisualizar">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="modal__body">

                    <div class="modal-secao">
                        <div class="modal-secao__titulo">
                            <i class="fa-solid fa-file-lines modal-secao__titulo-icone"></i>
                            <h3>Escopo do Teste</h3>
                        </div>
                        <div class="modal-grade modal-grade--4">
                            <div class="campo">
                                <label class="campo__label">Cliente</label>
                                <input type="text" class="campo__input" value="<?= htmlspecialchars($dados_visualizar['nome_fantasia'] ?? '') ?>" readonly />
                            </div>
                            <div class="campo">
                                <label class="campo__label">Projeto</label>
                                <input type="text" class="campo__input" value="<?= htmlspecialchars($dados_visualizar['nome'] ?? '') ?>" readonly />
                            </div>
                            <div class="campo">
                                <label class="campo__label">Resp. Téc</label>
                                <input type="text" class="campo__input" value="<?= htmlspecialchars($dados_visualizar['responsavel_tecnico'] ?? '---') ?>" readonly />
                            </div>
                            <div class="campo">
                                <label class="campo__label">Tipo do Teste</label>
                                <input type="text" class="campo__input" value="<?= htmlspecialchars($dados_visualizar['tipo_teste'] ?? '---') ?>" readonly />
                            </div>
                        </div>
                        <div class="modal-grade modal-grade--4">
                            <div class="campo">
                                <label class="campo__label">Data Início</label>
                                <input type="text" class="campo__input" value="<?= !empty($dados_visualizar['data_inicio']) ? date('d/m/Y', strtotime($dados_visualizar['data_inicio'])) : '---' ?>" readonly />
                            </div>
                            <div class="campo">
                                <label class="campo__label">Data Fim</label>
                                <input type="text" class="campo__input" value="<?= !empty($dados_visualizar['data_fim_prevista']) ? date('d/m/Y', strtotime($dados_visualizar['data_fim_prevista'])) : '---' ?>" readonly />
                            </div>
                            <div class="campo">
                                <label class="campo__label">Status</label>
                                <input type="text" class="campo__input" value="<?= htmlspecialchars($dados_visualizar['status_exibicao'] ?? '') ?>" readonly />
                            </div>
                            <div class="campo">
                                <label class="campo__label">Modalidade</label>
                                <input type="text" class="campo__input" value="<?= htmlspecialchars($dados_visualizar['modalidade'] ?? '') ?>" readonly />
                            </div>
                        </div>
                    </div>

                    <div class="modal-secao">
                        <div class="modal-secao__titulo">
                            <i class="fa-solid fa-align-left modal-secao__titulo-icone"></i>
                            <h3>Descrição</h3>
                        </div>
                        <p class="projetos-alocados-descricao">
                            <?= nl2br(htmlspecialchars($dados_visualizar['escopo'] ?? 'Sem descrição cadastrada.')) ?>
                        </p>
                    </div>

                </div>

                <div class="modal__footer modal__footer-cliente">
                    <a href="<?= BASE_URL ?>projetos-alocados?id_projeto_alocado_vulnerabilidades=<?= $dados_visualizar['id'] ?? '' ?>" class="btn-cancelar" style="text-decoration:none;">
                        Ver Vulnerabilidades Encontradas
                    </a>
                    <button type="button" class="btn-botao-verde" disabled title="Exportação ainda não implementada">
                        Exportar Relatório
                    </button>
                </div>

            </div>
        </div>

        <div class="modal-overlay<?= !empty($vulnerabilidades_do_projeto) ? ' active' : '' ?>" id="modalProjetosAlocadosVulnerabilidades">
            <div class="modal modal--lg">

                <div class="modal__header">
                    <div class="modal__header-icone">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="modal__header-texto">
                        <h2 class="modal__titulo">Vulnerabilidades Encontradas</h2>
                        <p class="modal__subtitulo"><?= count($vulnerabilidades_do_projeto) ?> vulnerabilidade(s) identificada(s) neste projeto</p>
                    </div>
                    <button type="button" class="modal__fechar" data-modal-close="modalProjetosAlocadosVulnerabilidades">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="modal__body">
                    <?php foreach ($vulnerabilidades_do_projeto as $vulnerabilidade): ?>
                        <?php
                            $severidadeClasse = [
                                'CRITICA' => 'projetos-alocados-badge-atrasado',
                                'ALTA'    => 'projetos-alocados-badge-atrasado',
                                'MEDIA'   => 'projetos-alocados-badge-aguardando',
                                'BAIXA'   => 'projetos-alocados-badge-concluido',
                            ][$vulnerabilidade['severidade_vulnerabilidade']] ?? 'projetos-alocados-badge-aguardando';
                        ?>
                        <div class="projetos-alocados-vulnerabilidade-card">
                            <div class="projetos-alocados-vulnerabilidade-topo">
                                <span class="projetos-alocados-badge <?= $severidadeClasse ?>">
                                    <?= htmlspecialchars($vulnerabilidade['severidade_vulnerabilidade']) ?>
                                </span>
                                <span class="projetos-alocados-vulnerabilidade-titulo">
                                    <?= htmlspecialchars($vulnerabilidade['nome']) ?>
                                </span>
                                <span class="projetos-alocados-vulnerabilidade-cvss">
                                    CVSS <?= htmlspecialchars($vulnerabilidade['cvss'] ?? '---') ?>
                                </span>
                            </div>
                            <p class="projetos-alocados-vulnerabilidade-texto">
                                <?= nl2br(htmlspecialchars($vulnerabilidade['descricao_tecnica'])) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($vulnerabilidades_do_projeto)): ?>
                        <p style="text-align:center; color:#666;">Nenhuma vulnerabilidade cadastrada para este projeto.</p>
                    <?php endif; ?>
                </div>

                <div class="modal__footer">
                    <button type="button" class="btn-cancelar" data-modal-close="modalProjetosAlocadosVulnerabilidades">FECHAR</button>
                </div>

            </div>
        </div>

        <div class="tabela-wrapper">
            <table id="tabela">
                <thead>
                    <tr>
                        <th data-col="0">
                            <span class="th-label">ID <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="1">
                            <span class="th-label">Projeto</span>
                        </th>
                        <th data-col="2">
                            <span class="th-label">Resp. Téc</span>
                        </th>
                        <th data-col="3">
                            <span class="th-label">Tipo do teste <i class="fa-solid fa-filter sort-icon"></i></span>
                        </th>
                        <th data-col="4">
                            <span class="th-label">Data Início</span>
                        </th>
                        <th data-col="5">
                            <span class="th-label">Data Fim</span>
                        </th>
                        <th data-col="6" class="col-status">
                            <span class="th-label">Status <i class="fa-solid fa-filter sort-icon"></i></span>
                        </th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dados as $projeto): ?>
                        <?php
                            $statusClasse = [
                                'Atrasado'   => 'projetos-alocados-badge-atrasado',
                                'Concluído'  => 'projetos-alocados-badge-concluido',
                                'Aguardando' => 'projetos-alocados-badge-aguardando',
                            ][$projeto['status_exibicao']] ?? 'projetos-alocados-badge-aguardando';
                        ?>
                        <tr>
                            <td>#PT-<?= $projeto['id'] ?></td>
                            <td><?= htmlspecialchars($projeto['nome']) ?></td>
                            <td><?= htmlspecialchars($projeto['responsavel_tecnico'] ?? '---') ?></td>
                            <td><?= htmlspecialchars($projeto['tipo_teste'] ?? '---') ?></td>
                            <td><?= $projeto['data_inicio'] ? date('d/m/Y', strtotime($projeto['data_inicio'])) : '---' ?></td>
                            <td><?= $projeto['data_fim_prevista'] ? date('d/m/Y', strtotime($projeto['data_fim_prevista'])) : '---' ?></td>
                            <td class="col-status">
                                <span class="projetos-alocados-badge <?= $statusClasse ?>"><?= $projeto['status_exibicao'] ?></span>
                            </td>
                            <td>
                                <div class="acoes">
                                    <a href="<?= BASE_URL ?>projetos-alocados?id_projeto_alocado_visualizar=<?= $projeto['id'] ?>" class="tabela-btn-visualizar" title="Visualizar" aria-label="Visualizar">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>dashboard-analista?id_projeto=<?= $projeto['id'] ?>" class="tabela-btn-editar" title="Editar no Dashboard do Analista" aria-label="Editar no Dashboard do Analista">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($dados)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center">Nenhum projeto alocado.</td>
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

</div>
</div>
<script src="<?= BASE_URL ?>app/assets/JS/barraDePesquisa.js"></script>
<script src="<?= BASE_URL ?>app/assets/JS/componentes/tabela.js"></script>
<script src="<?= BASE_URL ?>app/assets/JS/componentes/modal.js"></script>
<script>
    function limparParametrosDeModalDaUrl() {
        const parametrosParaLimpar = ['id_projeto_alocado_visualizar', 'id_projeto_alocado_vulnerabilidades'];
        const url = new URL(window.location.href);
        let precisaLimpar = false;

        parametrosParaLimpar.forEach(function (parametro) {
            if (url.searchParams.has(parametro)) {
                url.searchParams.delete(parametro);
                precisaLimpar = true;
            }
        });

        if (precisaLimpar) {
            window.history.replaceState({}, '', url);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const modaisDaTela = [
            document.getElementById('modalProjetosAlocadosVisualizar'),
            document.getElementById('modalProjetosAlocadosVulnerabilidades'),
        ];

        modaisDaTela.forEach(function (modal) {
            if (!modal) return;

            modal.querySelectorAll('[data-modal-close]').forEach(function (botao) {
                botao.addEventListener('click', limparParametrosDeModalDaUrl);
            });

            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    limparParametrosDeModalDaUrl();
                }
            });
        });
    });
</script>