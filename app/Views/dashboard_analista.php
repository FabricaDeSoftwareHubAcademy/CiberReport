<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Analista</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Pages/dashboard_analista.css">
</head>

<body class="corpo-dasboard-analista">
  <?php
    $tituloPagina = 'Dashboard - Analista';
    include 'Components/menu.php';
  ?>

  <main class="dasboard-analista-main">

  <section class="cards-analista">
    <div class="card-analista">
      <h2 class="card-analista_titulo">Projetos Alocados (<?= date('Y') ?>)</h2>
      <span></span>
    </div>
    <div class="card-analista">
      <h2 class="card-analista_titulo">Em Andamento</h2>
    </div>
    <div class="card-analista">
      <h2 class="card-analista_titulo">Placeholder</h2>
    </div>
    <div class="card-analista">
      <h2 class="card-analista_titulo">Concluidos</h2>
    </div>
  </section>

  <div class="dashboard-cards">

    <!-- CARD DO PROJETO -->
    <div class="project-card">

        <div class="project-header">

            <div class="project-icon">
                🛡️
            </div>

            <div class="project-info">
                <h2>Pentest WEB - Cliente Atacadão</h2>
                <p>Avaliação de segurança do programa dos caixas</p>
            </div>

            <span class="status">Em andamento</span>

        </div>

        <div class="project-details">

            <div>
                <span>Responsável</span>
                <strong>André</strong>
            </div>

            <div>
                <span>Prazo</span>
                <strong>13/03</strong>
            </div>

            <div>
                <span>Vulns. críticas</span>
                <strong class="critical">3 abertas</strong>
            </div>

        </div>

        <div class="progress-section">

            <div class="progress-title">
                <span>Progresso do relatório</span>
                <strong>62%</strong>
            </div>

            <div class="progress-bar">
                <div class="progress-value"></div>
            </div>

        </div>

        <a href="#" class="project-link">
            Abrir projeto <span>→</span>
        </a>

    </div>


    <!-- CARD DE TAREFAS -->
    <div class="tasks-card">

        <div class="tasks-header">
            <h2>Minhas tarefas</h2>
            <span>4 pendentes</span>
        </div>

        <div class="task-list">

            <div class="task">
                <input type="checkbox" id="task1">

                <label for="task1">
                    <strong>Validar vuln. crítica - Cliente Atacadão</strong>
                    <small class="today">Hoje</small>
                </label>
            </div>

            <div class="task">
                <input type="checkbox" id="task2">

                <label for="task2">
                    <strong>Escrever relatório - Cliente Magazine Luiza</strong>
                    <small>Amanhã</small>
                </label>
            </div>

            <div class="task">
                <input type="checkbox" id="task3">

                <label for="task3">
                    <strong>Reunião de alinhamento - Cliente Fort</strong>
                    <small>21/01</small>
                </label>
            </div>

            <div class="task">
                <input type="checkbox" id="task4">

                <label for="task4">
                    <strong>Rodar novo scan - Cliente Comper</strong>
                    <small>23/01</small>
                </label>
            </div>

        </div>

        <a href="#" class="tasks-link">
            Ver todas as tarefas <span>→</span>
        </a>

    </div>

</div>
  </main>

</body>
</html>