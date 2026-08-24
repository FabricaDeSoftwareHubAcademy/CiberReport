<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Analista</title>
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
      <h2></h2>
    </div>
  </section>
  </main>

</body>
</html>