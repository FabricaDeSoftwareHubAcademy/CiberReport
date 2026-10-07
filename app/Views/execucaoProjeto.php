<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Execução do Projeto | Cyber Report</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Componentes/execucaoProjeto.css">
</head>
<body class="dashboard-projeto">
  <?php $tituloPagina = 'Execução do Projeto'; include __DIR__ . '/Components/menu.php'; ?>
      <main class="projeto-conteudo">
        <section class="projeto-resumo" aria-label="Resumo do projeto">
          <div class="projeto-card projeto-info">
            <dl>
              <div><dt>Projeto:</dt><dd id="proj-nome">E-commerce Platform Pentest</dd></div>
              <div><dt>Cliente:</dt><dd id="proj-cliente">Technet.</dd></div>
              <div><dt>Referência:</dt><dd id="proj-referencia">OWASP TOP 10</dd></div>
              <div><dt>Tipo de Teste:</dt><dd id="proj-tipo">WEB Application</dd></div>
              <div><dt>Restrições:</dt><dd id="proj-restricoes">—</dd></div>
              <div><dt>Cronograma:</dt><dd id="proj-cronograma">15/02/2026 → 10/08/2026</dd></div>
              <div><dt>Horas Contratadas:</dt><dd id="proj-horas-contratadas">10 horas</dd></div>
              <div><dt>Horas Consumidas:</dt><dd id="proj-horas-consumidas">2 horas</dd></div>
            </dl>
          </div>
          <figure class="projeto-card projeto-grafico">
            <div class="donut" id="grafico-vulns" role="img" aria-label="Vulnerabilidades: 5 críticas, 9 altas, 11 médias e 30 baixas"><div class="donut-centro">Vulnerabilidades<br>Encontradas</div></div>
            <figcaption class="projeto-legenda"><span class="severidade critica">CRÍTICA</span><span class="severidade alta">ALTA</span><span class="severidade media">MÉDIA</span><span class="severidade baixa">BAIXA</span></figcaption>
          </figure>
        </section>
        <section class="projeto-checklist" aria-label="Checklist do projeto">
          <div class="tabela-wrapper">
            <table class="tabela" aria-label="Itens do checklist">
              <thead><tr>
                <th data-col="0" data-filtro="lista"><span class="th-label">Item de checklist <i class="fa-solid fa-sort sort-icon"></i><i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar item"></i></span></th>
                <th data-col="1" data-filtro="numero"><span class="th-label">Horas Contratadas <i class="fa-solid fa-sort sort-icon"></i></span></th>
                <th data-col="2" data-filtro="numero"><span class="th-label">Horas consumidas <i class="fa-solid fa-sort sort-icon"></i></span></th>
                <th data-col="3"><span class="th-label">Hora Início <i class="fa-solid fa-sort sort-icon"></i></span></th>
                <th data-col="4"><span class="th-label">Hora Fim <i class="fa-solid fa-sort sort-icon"></i></span></th>
                <th data-col="5" data-filtro="lista"><span class="th-label">Alvo <i class="fa-solid fa-sort sort-icon"></i><i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar alvo"></i></span></th>
                <th data-col="6" data-filtro="lista"><span class="th-label">Status <i class="fa-solid fa-sort sort-icon"></i><i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar status"></i></span></th><th>Ações</th>
              </tr></thead>
              <tbody id="tabela-checklist">
                <tr data-item="injecao"><td><button class="checklist-expandir" aria-expanded="false" aria-controls="lista-testes"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i>Teste de Injeção</button></td><td>48 horas</td><td>48 horas</td><td>10:45 - 23/04/26</td><td>22:00 - 23/04/26</td><td>192.168.45.2</td><td><span class="projeto-status concluido">Concluído</span></td><td><div class="projeto-acoes"><button type="button" class="projeto-relogio" title="Controlar tempo do teste" aria-label="Controlar tempo do teste"><i class="fa-solid fa-stopwatch" aria-hidden="true"></i></button><button type="button" class="projeto-concluir" title="Concluir ou reabrir teste" aria-label="Concluir ou reabrir teste"><i class="fa-regular fa-square-check" aria-hidden="true"></i></button><a href="<?= BASE_URL ?>vulnerabilidades" title="Vulnerabilidades do teste" aria-label="Vulnerabilidades do teste de injeção"><i class="fa-solid fa-bug" aria-hidden="true"></i></a></div></td></tr>
                <tr data-item="rede"><td><button class="checklist-expandir" aria-expanded="false" aria-controls="lista-testes"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i>Varredura de Rede</button></td><td>120 horas</td><td>120 horas</td><td>10:45 - 23/04/26</td><td>10:45 - 23/04/26</td><td>192.168.1.2</td><td><span class="projeto-status concluido">Concluído</span></td><td><div class="projeto-acoes"><button type="button" class="projeto-relogio" title="Controlar tempo do teste" aria-label="Controlar tempo do teste"><i class="fa-solid fa-stopwatch" aria-hidden="true"></i></button><button type="button" class="projeto-concluir" title="Concluir ou reabrir teste" aria-label="Concluir ou reabrir teste"><i class="fa-regular fa-square-check" aria-hidden="true"></i></button><a href="<?= BASE_URL ?>vulnerabilidades" title="Vulnerabilidades do teste" aria-label="Vulnerabilidades da varredura de rede"><i class="fa-solid fa-bug" aria-hidden="true"></i></a></div></td></tr>
                <tr data-item="autenticacao"><td><button class="checklist-expandir" aria-expanded="true" aria-controls="lista-testes"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i>Teste de Autenticação</button></td><td>60 horas</td><td>2 horas</td><td>10:45 - 23/04/26</td><td>—</td><td>192.168.1.2</td><td><span class="projeto-status andamento">Em andamento</span></td><td><div class="projeto-acoes"><button type="button" class="projeto-relogio" title="Controlar tempo do teste" aria-label="Controlar tempo do teste"><i class="fa-solid fa-stopwatch" aria-hidden="true"></i></button><button type="button" class="projeto-concluir" title="Concluir ou reabrir teste" aria-label="Concluir ou reabrir teste"><i class="fa-regular fa-square-check" aria-hidden="true"></i></button><a href="<?= BASE_URL ?>vulnerabilidades" title="Vulnerabilidades do teste" aria-label="Vulnerabilidades do teste de autenticação"><i class="fa-solid fa-bug" aria-hidden="true"></i></a></div></td></tr>
              <tr class="tabela-linha-vazia projeto-detalhes" id="linha-testes"><td colspan="8"><div class="projeto-testes" id="lista-testes" role="group" aria-label="Etapas do teste de autenticação"></div></td></tr>
              </tbody>
              <tfoot><tr><td colspan="8" class="rodape-tabela"><div class="paginacao"></div></td></tr></tfoot>
            </table>
          </div>
          
        </section>
      </main>
    </div>
  </div>
  <dialog class="projeto-cronometro" id="modal-cronometro" aria-labelledby="cronometro-titulo">
    <div class="modal__header">
      <h2 id="cronometro-titulo">Iniciar Novo Teste</h2>
      <button type="button" class="modal__fechar" id="cronometro-fechar" aria-label="Fechar">&times;</button>
    </div>
    <div class="inicio-teste-corpo">
      <div class="inicio-teste-campos">
        <div class="campo"><label class="campo__label" for="cronometro-item">Nome do teste</label><select class="campo__select" id="cronometro-item"></select></div>
        <div class="campo"><label class="campo__label" for="cronometro-alvo">Alvo</label><select class="campo__select" id="cronometro-alvo"></select></div>
      </div>
      <output id="cronometro-tempo" aria-label="Tempo do teste" hidden>00:00:00</output>
      <button type="button" id="cronometro-alternar"><i class="fa-solid fa-play" aria-hidden="true"></i><span>Iniciar Teste</span></button>
      <p class="inicio-teste-info"><i class="fa-regular fa-circle-info" aria-hidden="true"></i><span>Os testes podem rodar em paralelo, cada um com seu próprio cronômetro.</span></p>
    </div>
  </dialog>
  <dialog class="modal-execucao-vuln" id="modal-execucao-vuln"><div class="vuln-shell"><h1>Cadastro de Vulnerabilidade</h1><form id="form-execucao-vuln"><section class="vuln-form"><div class="campos"><label>Item de checklist relacionado<select id="execucao-item"></select></label><label>Alvo<select id="execucao-alvo"></select></label><label class="full">Título da Vulnerabilidade<input required placeholder="SQL Injection no Endpoint de Login"></label><label>Nota CVSS<input required type="number" min="0" max="10" step="0.1"></label><label>Severidade<select required><option>Crítica</option><option>Alta</option><option>Média</option><option>Baixa</option></select></label><label class="full">Descrição Detalhada<textarea required></textarea></label><label class="full">Recomendações<textarea required></textarea></label></div></section><aside class="vuln-aside"><section><h2>Passos para Reproduzir</h2><textarea></textarea></section><section><h2>Evidências</h2><input type="file" multiple></section><section><h2>Notas e Observações da Execução</h2><textarea></textarea></section></aside><footer><button type="button" id="fechar-execucao-vuln">Cancelar</button><button type="submit">Salvar Vulnerabilidade</button></footer></form></div></dialog>
  <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Componentes/execucaoProjetoModal.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Componentes/execucaoProjetoModalForm.css"><link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Componentes/execucaoProjetoModalHeader.css"><link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Componentes/execucaoProjetoModalBorder.css"><link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Componentes/execucaoProjetoModalTamanhos.css">
  <script>window.baseUrl = <?= json_encode(BASE_URL) ?>;</script>
  <script src="<?= BASE_URL ?>app/assets/JS/componentes/modal.js"></script>
  <script src="<?= BASE_URL ?>app/assets/JS/componentes/tabela.js"></script>
  <script src="<?= BASE_URL ?>app/assets/JS/componentes/filtros-tabela.js"></script>
  <script src="<?= BASE_URL ?>app/assets/JS/execucaoVulnerabilidade.js"></script>
  <script src="<?= BASE_URL ?>app/assets/JS/execucaoProjeto.js"></script>
</body>
</html>
