<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestão</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Pages/dashboard_gestor.css">
</head>

<body class="corpo-dashboard-gestor">
    <?php
    $tituloPagina = 'Dashboard - Gestão';
    include 'Components/menu.php';
    ?>
    <main class="main-dashboard-gestor">

        <section class="group-cards-gestor">
            <div class="card-gestor card-gestor--clicavel" id="card-vulns-criticas">
                <h2 class="card-gestor__titulo">
                    Vulnerabilidades Críticas em Aberto
                    <i class="fa-solid fa-circle-info card-gestor__info" title="Soma somente severidade Crítica, apenas dos projetos em andamento"></i>
                </h2>
                <span class="card-gestor__valor" id="valor-vulns-criticas">--</span>
            </div>
            <div class="card-gestor card-gestor--clicavel" id="card-em-andamento">
                <h2 class="card-gestor__titulo">Projetos em Andamento</h2>
                <span class="card-gestor__valor" id="valor-em-andamento">--</span>
            </div>
            <div class="card-gestor card-gestor--prazo">
                <h2 class="card-gestor__titulo">
                    Prazos em Risco
                    <i class="fa-solid fa-circle-info card-gestor__info" title="Inclui projetos em andamento já vencidos e os que estão dentro do prazo de aviso configurado em cada projeto."></i>
                </h2>
                <span class="card-gestor__valor" id="valor-prazos-risco">--</span>
            </div>
        </section>

        <section class="group-graficos-gestor">
            <div class="grafico-card">
                <h2 class="grafico-card__titulo">
                    Progresso por Projeto
                    <i class="fa-solid fa-circle-info card-gestor__info" title="Percentual de itens do checklist já concluídos em cada projeto em andamento. Clique numa barra para abrir o projeto."></i>
                </h2>
                <div id="grafico-progresso"></div>
            </div>
            <div class="grafico-card grafico-card--alocacao">
                <h2 class="grafico-card__titulo">
                    Ocupação por Analista
                    <i class="fa-solid fa-circle-info card-gestor__info" title="Projetos em andamento de cada analista em relação ao limite máximo de alocação simultânea definido no cadastro dele. Clique em um analista para filtrar a tabela."></i>
                </h2>
                <ul class="lista-alocacao" id="lista-alocacao"></ul>
            </div>
        </section>

        <section class="group-tabela-prazos">
            <div class="tabela-coluna">
                <div class="filtro-ativo-chip" id="filtro-ativo-chip" hidden>
                    <i class="fa-solid fa-filter"></i>
                    Filtrado por <strong id="filtro-ativo-nome"></strong>
                    <button type="button" id="btn-limpar-filtro" aria-label="Limpar filtro">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="tabela-wrapper tabela-dashboard-gestor">
                    <table id="table">
                        <thead>
                            <tr>
                                <th data-col="0">
                                    <span class="th-label">Projeto <i class="fa-solid fa-sort sort-icon"></i></span>
                                </th>
                                <th data-col="1" data-filtro="lista">
                                    <span class="th-label">Cliente <i class="fa-solid fa-sort sort-icon"></i> <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por cliente"></i></span>
                                </th>
                                <th data-col="2" data-filtro="lista">
                                    <span class="th-label">Resp. Técnico <i class="fa-solid fa-sort sort-icon"></i> <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por responsável técnico"></i></span>
                                </th>
                                <th data-col="3" data-filtro="lista">
                                    <span class="th-label">Analistas Alocados <i class="fa-solid fa-sort sort-icon"></i> <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por analista alocado"></i></span>
                                </th>
                                <th data-col="4" data-filtro="data">
                                    <span class="th-label">Data Fim Prevista <i class="fa-solid fa-sort sort-icon"></i> <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por data fim prevista"></i></span>
                                </th>
                                <th data-col="5" data-filtro="lista">
                                    <span class="th-label">Crítica<i class="fa-solid fa-sort sort-icon"></i> <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por vulnerabilidade crítica em aberto"></i></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- linhas geradas por dashboardGestor.js a partir do mock, antes do tabela.js rodar -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="rodape-tabela">
                                    <div class="paginacao"></div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>

    </main>
    </div>
    </div>

    <!-- ApexCharts precisa carregar antes de dashboardGestor.js, que por sua vez
         precisa popular <tbody id="table"> antes de tabela.js rodar (ele se
         auto-inicializa ao ser executado e lê as linhas existentes no DOM). -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="<?= BASE_URL ?>app/assets/JS/dashboardGestor.js"></script>
    <script src="<?= BASE_URL ?>app/assets/JS/componentes/tabela.js"></script>
</body>

</html>
