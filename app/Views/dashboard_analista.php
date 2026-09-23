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

  <main class="dashboard-analista-main">
    <section class="dashboard-analista-intro" aria-labelledby="dashboard-analista-boas-vindas">
      <p class="dashboard-analista-intro__eyebrow">Área do analista</p>
      <p>Acompanhe o último projeto acessado e as atividades que precisam da sua atenção.</p>
    </section>

    <section class="dashboard-cards" aria-label="Resumo do trabalho">
      <!-- CARD DO ÚLTIMO PROJETO -->
      <article class="project-card">
        <div class="project-header">
          <div class="project-icon" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></div>
          <div class="project-info">
            <p class="card-eyebrow">Último projeto acessado</p>
            <h2>Pentest WEB — Cliente Atacadão</h2>
            <p>Avaliação de segurança do programa dos caixas</p>
          </div>
          <span class="status">Em andamento</span>
        </div>

        <dl class="project-details">
          <div><dt>Responsável</dt><dd>André</dd></div>
          <div><dt>Prazo</dt><dd>13/03</dd></div>
          <div><dt>Vulns. críticas</dt><dd class="critical">3 abertas</dd></div>
        </dl>

        <div class="progress-section">
          <div class="progress-title"><span>Progresso do relatório</span><strong>62%</strong></div>
          <div class="progress-bar" role="progressbar" aria-label="Progresso do relatório" aria-valuemin="0" aria-valuemax="100" aria-valuenow="62">
            <div class="progress-value"></div>
          </div>
        </div>

        <a href="<?= BASE_URL ?>projetos-alocados" class="project-link">Abrir projeto <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
      </article>

      <!-- CARD DE TAREFAS -->
      <article class="tasks-card">
        <div class="tasks-header">
          <h2>Minhas tarefas</h2>
          <span class="tasks-counter">4 pendentes</span>
        </div>

        <div class="task-list" id="task-list">
          <div class="task">
            <input type="checkbox" id="task1">
            <label for="task1"><strong>Validar vuln. crítica — Cliente Atacadão</strong><small class="today">Hoje</small></label>
          </div>
          <div class="task">
            <input type="checkbox" id="task2">
            <label for="task2"><strong>Escrever relatório — Cliente Magazine Luiza</strong><small>Amanhã</small></label>
          </div>
          <div class="task">
            <input type="checkbox" id="task3">
            <label for="task3"><strong>Reunião de alinhamento — Cliente Fort</strong><small>21/01</small></label>
          </div>
          <div class="task">
            <input type="checkbox" id="task4">
            <label for="task4"><strong>Rodar novo scan — Cliente Comper</strong><small>23/01</small></label>
          </div>
        </div>

        <a href="#task-list" class="tasks-link">Ver todas as tarefas <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
      </article>
    </section>
    <script src="<?= BASE_URL ?>app/assets/JS/componentes/tabela.js"></script>
  </main>

</body>
</html>
