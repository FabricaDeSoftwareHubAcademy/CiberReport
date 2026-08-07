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
            <div class="card-gestor">
                <h2 class="card-gestor__titulo">Projetos Concluídos (<?= date('Y') ?>)</h2>
                <span class="card-gestor__valor" id="valor-concluidos-ano">--</span>
            </div>
            <div class="card-gestor">
                <h2 class="card-gestor__titulo">Projetos em Andamento</h2>
                <span class="card-gestor__valor" id="valor-em-andamento">--</span>
            </div>
            <div class="card-gestor">
                <h2 class="card-gestor__titulo">Analistas Não Alocados</h2>
                <span class="card-gestor__valor" id="valor-analistas-nao-alocados">--</span>
            </div>
            <div class="card-gestor card-gestor--alerta">
                <h2 class="card-gestor__titulo">Vulnerabilidades Críticas/Altas em Aberto</h2>
                <span class="card-gestor__valor" id="valor-vulns-criticas">--</span>
            </div>
            <div class="card-gestor card-gestor--prazo">
                <h2 class="card-gestor__titulo">
                    Prazos Vencendo em
                    <input type="number" id="input-dias-prazo" class="card-gestor__input-dias" min="1" step="1" value="15" aria-label="Quantidade de dias para considerar prazo em risco">
                    dias
                </h2>
                <span class="card-gestor__valor" id="valor-prazos-risco">--</span>
            </div>
        </section>

        <section class="group-graficos-gestor">
            <div class="grafico-card">
                <h2 class="grafico-card__titulo">Vulnerabilidades por Projeto</h2>
                <div id="grafico-vulnerabilidades"></div>
            </div>
            <div class="grafico-card grafico-card--pizza">
                <h2 class="grafico-card__titulo">Projetos Alocados</h2>
                <div id="grafico-alocacao"></div>
                <ul class="legenda-alocacao" id="legenda-alocacao"></ul>
            </div>
        </section>

        <section class="group-tabela-prazos">
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
                            <th data-col="3" data-filtro="data">
                                <span class="th-label">Data Início <i class="fa-solid fa-sort sort-icon"></i> <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por data de início"></i></span>
                            </th>
                            <th data-col="4" data-filtro="data">
                                <span class="th-label">Data Fim Prevista <i class="fa-solid fa-sort sort-icon"></i> <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por data fim prevista"></i></span>
                            </th>
                            <th data-col="5" data-filtro="numero">
                                <span class="th-label">Vulnerabilidades <i class="fa-solid fa-sort sort-icon"></i></span>
                            </th>
                            <th data-col="6" data-filtro="data">
                                <span class="th-label">Editado em <i class="fa-solid fa-sort sort-icon"></i> <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por data de edição"></i></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- linhas geradas por dashboardGestor.js a partir do mock, antes do tabela.js rodar -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="rodape-tabela">
                                <div class="paginacao"></div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <aside class="proximos-prazos-card">
                <h2 class="proximos-prazos-card__titulo">Próximos Prazos</h2>
                <ul class="lista-proximos-prazos" id="lista-proximos-prazos"></ul>
            </aside>
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
