(function () {
  const hoje = new Date();
  hoje.setHours(0, 0, 0, 0);

  function diasAPartirDeHoje(deslocamentoDias) {
    const data = new Date(hoje);
    data.setDate(data.getDate() + deslocamentoDias);
    return data;
  }

  function paraISO(data) {
    const ano = data.getFullYear();
    const mes = String(data.getMonth() + 1).padStart(2, '0');
    const dia = String(data.getDate()).padStart(2, '0');
    return `${ano}-${mes}-${dia}`;
  }

  function d(deslocamentoDias) {
    return paraISO(diasAPartirDeHoje(deslocamentoDias));
  }

  function formatarDataBR(dataISO) {
    if (!dataISO) return '-';
    const [ano, mes, dia] = dataISO.split('-');
    return `${dia}/${mes}/${ano}`;
  }

  // --- dados mockados ---
  // Estrutura espelha os campos reais de `projeto`/`empresa`/`vulnerabilidade`
  // (ver app/Model/Database/banco.sql) para facilitar a troca por dados de um
  // endpoint real no futuro, quando a alocação analista<->projeto existir.
  const projetosMock = [
    { id: 1, nome: 'Pentest WEB - Cliente Atacado', cliente: 'Cliente1', responsavel_tecnico: 'André', status: 'EM_ANDAMENTO', data_inicio: d(-40), data_fim_prevista: d(20), data_fim_real: null, updated_at: d(-2), vulnerabilidades_total: 14, vulnerabilidades_criticas_altas: 5 },
    { id: 2, nome: 'Pentest WEB - Cliente Magazine Luiza', cliente: 'Cliente2', responsavel_tecnico: 'Marcos', status: 'EM_ANDAMENTO', data_inicio: d(-35), data_fim_prevista: d(3), data_fim_real: null, updated_at: d(-1), vulnerabilidades_total: 9, vulnerabilidades_criticas_altas: 2 },
    { id: 3, nome: 'Pentest WEB - Cliente Fort', cliente: 'Cliente3', responsavel_tecnico: 'José', status: 'EM_ANDAMENTO', data_inicio: d(-50), data_fim_prevista: d(-4), data_fim_real: null, updated_at: d(-5), vulnerabilidades_total: 20, vulnerabilidades_criticas_altas: 8 },
    { id: 4, nome: 'Pentest Mobile - Cliente Vuon', cliente: 'Cliente4', responsavel_tecnico: 'João', status: 'EM_ANDAMENTO', data_inicio: d(-15), data_fim_prevista: d(45), data_fim_real: null, updated_at: d(-3), vulnerabilidades_total: 4, vulnerabilidades_criticas_altas: 1 },
    { id: 5, nome: 'Pentest Infra - Cliente Solvex', cliente: 'Cliente5', responsavel_tecnico: 'André', status: 'EM_ANDAMENTO', data_inicio: d(-20), data_fim_prevista: d(10), data_fim_real: null, updated_at: d(-1), vulnerabilidades_total: 11, vulnerabilidades_criticas_altas: 3 },
    { id: 6, nome: 'Pentest API - Cliente Norvax', cliente: 'Cliente6', responsavel_tecnico: 'Marcos', status: 'EM_ANDAMENTO', data_inicio: d(-8), data_fim_prevista: d(60), data_fim_real: null, updated_at: d(-1), vulnerabilidades_total: 2, vulnerabilidades_criticas_altas: 0 },
    { id: 7, nome: 'Pentest WEB - Cliente Bramido', cliente: 'Cliente7', responsavel_tecnico: 'José', status: 'EM_ANDAMENTO', data_inicio: d(-25), data_fim_prevista: d(14), data_fim_real: null, updated_at: d(-4), vulnerabilidades_total: 17, vulnerabilidades_criticas_altas: 6 },
    { id: 8, nome: 'Pentest WEB - Cliente Oitava', cliente: 'Cliente8', responsavel_tecnico: 'João', status: 'CONCLUIDO', data_inicio: d(-90), data_fim_prevista: d(-35), data_fim_real: d(-30), updated_at: d(-30), vulnerabilidades_total: 8, vulnerabilidades_criticas_altas: 2 },
    { id: 9, nome: 'Pentest Infra - Cliente Nona', cliente: 'Cliente9', responsavel_tecnico: 'André', status: 'CONCLUIDO', data_inicio: d(-110), data_fim_prevista: d(-58), data_fim_real: d(-60), updated_at: d(-60), vulnerabilidades_total: 6, vulnerabilidades_criticas_altas: 1 },
    { id: 10, nome: 'Pentest API - Cliente Decima', cliente: 'Cliente10', responsavel_tecnico: 'Marcos', status: 'CONCLUIDO', data_inicio: d(-130), data_fim_prevista: d(-82), data_fim_real: d(-80), updated_at: d(-80), vulnerabilidades_total: 12, vulnerabilidades_criticas_altas: 4 },
    { id: 11, nome: 'Pentest WEB - Cliente Onze', cliente: 'Cliente11', responsavel_tecnico: 'José', status: 'CONCLUIDO', data_inicio: d(-450), data_fim_prevista: d(-402), data_fim_real: d(-400), updated_at: d(-400), vulnerabilidades_total: 10, vulnerabilidades_criticas_altas: 3 },
    { id: 12, nome: 'Pentest Mobile - Cliente Doze', cliente: 'Cliente12', responsavel_tecnico: 'João', status: 'CONCLUIDO', data_inicio: d(-470), data_fim_prevista: d(-422), data_fim_real: d(-420), updated_at: d(-420), vulnerabilidades_total: 5, vulnerabilidades_criticas_altas: 0 }
  ];

  const analistasMock = [
    { id: 1, nome: 'André' },
    { id: 2, nome: 'José' },
    { id: 3, nome: 'Marcos' },
    { id: 4, nome: 'João' },
    { id: 5, nome: 'Ana' } // sem projeto EM_ANDAMENTO no mock -> aparece como não alocada
  ];

  function projetosAtivos() {
    return projetosMock.filter((projeto) => projeto.status === 'EM_ANDAMENTO');
  }

  const DIAS_PRAZO_MAXIMO = 180;
  const DIAS_PRAZO_PADRAO = 15;
  const COOKIE_DIAS_PRAZO = 'dashboardGestorDiasPrazo';

  function obterCookie(nome) {
    const linha = document.cookie.split('; ').find((item) => item.startsWith(nome + '='));
    return linha ? decodeURIComponent(linha.split('=')[1]) : null;
  }

  function definirCookie(nome, valor) {
    const expira = new Date();
    expira.setFullYear(expira.getFullYear() + 1);
    document.cookie = `${nome}=${encodeURIComponent(valor)}; expires=${expira.toUTCString()}; path=/; samesite=lax`;
  }

  function lerDiasPrazo() {
    const valor = parseInt(document.getElementById('input-dias-prazo').value, 10);
    if (isNaN(valor) || valor <= 0) return DIAS_PRAZO_PADRAO;
    return Math.min(valor, DIAS_PRAZO_MAXIMO);
  }

  // --- placeholder de navegação ---
  // A Dashboard do Projeto ainda não existe (é a próxima etapa). Por ora só
  // registramos qual projeto foi clicado; quando a rota existir, trocar por
  // window.location.href = BASE_URL + 'dashboard-projeto/' + idProjeto.
  function irParaDashboardProjeto(idProjeto) {
    console.log('TODO: abrir Dashboard do Projeto para o projeto', idProjeto);
  }

  function filtrarTabelaPorAnalista(nomeAnalista) {
    const campoBusca = document.querySelector('.input-pesquisaSuperior input[type="text"]');
    if (!campoBusca) return;
    campoBusca.value = nomeAnalista;
    campoBusca.dispatchEvent(new Event('input', { bubbles: true }));
    exibirChipFiltro(nomeAnalista);
  }

  function exibirChipFiltro(nomeAnalista) {
    const chip = document.getElementById('filtro-ativo-chip');
    const nomeSpan = document.getElementById('filtro-ativo-nome');
    if (!chip || !nomeSpan) return;
    nomeSpan.textContent = nomeAnalista;
    chip.hidden = false;
  }

  function limparFiltroAnalista() {
    const campoBusca = document.querySelector('.input-pesquisaSuperior input[type="text"]');
    const chip = document.getElementById('filtro-ativo-chip');
    if (campoBusca) {
      campoBusca.value = '';
      campoBusca.dispatchEvent(new Event('input', { bubbles: true }));
    }
    if (chip) chip.hidden = true;
  }

  // --- cards ---
  function renderizarCards() {
    const anoAtual = hoje.getFullYear();

    const concluidosNoAno = projetosMock.filter(
      (projeto) => projeto.status === 'CONCLUIDO'
        && projeto.data_fim_real
        && new Date(projeto.data_fim_real).getFullYear() === anoAtual
    ).length;

    const ativos = projetosAtivos();

    const nomesAlocados = new Set(ativos.map((projeto) => projeto.responsavel_tecnico));
    const naoAlocados = analistasMock.filter((analista) => !nomesAlocados.has(analista.nome)).length;

    const vulnsCriticasAltas = ativos.reduce(
      (soma, projeto) => soma + projeto.vulnerabilidades_criticas_altas,
      0
    );

    const dias = lerDiasPrazo();
    const limite = diasAPartirDeHoje(dias);
    const prazosEmRisco = ativos.filter(
      (projeto) => new Date(projeto.data_fim_prevista) <= limite
    ).length;

    document.getElementById('valor-concluidos-ano').textContent = concluidosNoAno;
    document.getElementById('valor-em-andamento').textContent = ativos.length;
    document.getElementById('valor-analistas-nao-alocados').textContent = naoAlocados;
    document.getElementById('valor-vulns-criticas').textContent = vulnsCriticasAltas;
    document.getElementById('valor-prazos-risco').textContent = prazosEmRisco;
  }

  // --- tabela ---
  function renderizarTabela() {
    const corpo = document.querySelector('#table tbody');
    if (!corpo) return;
    corpo.innerHTML = '';

    const ativos = projetosAtivos();

    if (!ativos.length) {
      corpo.innerHTML = '<tr><td colspan="7" style="text-align:center">Nenhum projeto em andamento no momento.</td></tr>';
      return;
    }

    ativos.forEach((projeto) => {
      const linha = document.createElement('tr');
      linha.dataset.projetoId = String(projeto.id);
      const classeVuln = projeto.vulnerabilidades_criticas_altas > 0 ? ' class="celula-vuln-grave"' : '';
      linha.innerHTML = `
        <td>${projeto.nome}</td>
        <td>${projeto.cliente}</td>
        <td>${projeto.responsavel_tecnico}</td>
        <td>${formatarDataBR(projeto.data_inicio)}</td>
        <td>${formatarDataBR(projeto.data_fim_prevista)}</td>
        <td${classeVuln}>${projeto.vulnerabilidades_total}</td>
        <td>${formatarDataBR(projeto.updated_at)}</td>
      `;
      linha.addEventListener('click', () => irParaDashboardProjeto(projeto.id));
      corpo.appendChild(linha);
    });
  }

  // --- próximos prazos ---
  function renderizarProximosPrazos() {
    const lista = document.getElementById('lista-proximos-prazos');
    if (!lista) return;
    lista.innerHTML = '';

    const dias = lerDiasPrazo();
    const limite = diasAPartirDeHoje(dias);

    const proximos = projetosAtivos()
      .slice()
      .sort((a, b) => new Date(a.data_fim_prevista) - new Date(b.data_fim_prevista))
      .slice(0, 5);

    if (!proximos.length) {
      lista.innerHTML = '<li class="prazo-item__vazio">Nenhum projeto em andamento.</li>';
      return;
    }

    proximos.forEach((projeto) => {
      const dataFim = new Date(projeto.data_fim_prevista);
      const vencido = dataFim < hoje;
      const emRisco = !vencido && dataFim <= limite;
      const classeData = vencido
        ? 'prazo-item__data--vencido'
        : (emRisco ? 'prazo-item__data--em-risco' : '');

      const item = document.createElement('li');
      item.dataset.projetoId = String(projeto.id);
      item.innerHTML = `
        <span class="prazo-item__nome">${projeto.nome}</span>
        <span class="prazo-item__cliente">${projeto.cliente} · ${projeto.responsavel_tecnico}</span>
        <span class="prazo-item__data ${classeData}">${formatarDataBR(projeto.data_fim_prevista)}${vencido ? ' (vencido)' : ''}</span>
      `;
      item.addEventListener('click', () => irParaDashboardProjeto(projeto.id));
      lista.appendChild(item);
    });
  }

  // --- gráficos ---
  function obterCoresTokens() {
    return ['#006ab5', '#00a38c', '#0080bd', '#00af5d', '#ffa600', '#0093ae', '#61b525'];
  }

  function inicializarGraficoVulnerabilidades() {
    const container = document.querySelector('#grafico-vulnerabilidades');
    if (!container) return;

    const ativos = projetosAtivos();

    const grafico = new ApexCharts(container, {
      chart: {
        type: 'bar',
        height: 220,
        fontFamily: 'var(--fonte-familia-corpo)',
        toolbar: { show: false },
        events: {
          dataPointSelection: (evento, contextoGrafico, config) => {
            const projeto = ativos[config.dataPointIndex];
            if (projeto) irParaDashboardProjeto(projeto.id);
          }
        }
      },
      series: [{ name: 'Vulnerabilidades', data: ativos.map((projeto) => projeto.vulnerabilidades_total) }],
      xaxis: { categories: ativos.map((projeto) => projeto.cliente) },
      colors: ['#006ab5'],
      dataLabels: { enabled: false },
      plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } }
    });

    grafico.render();
  }

  function renderizarLegendaAlocacao(nomes, valores, cores) {
    const legenda = document.getElementById('legenda-alocacao');
    if (!legenda) return;
    legenda.innerHTML = '';

    nomes.forEach((nome, indice) => {
      const item = document.createElement('li');
      item.style.backgroundColor = cores[indice % cores.length];
      item.innerHTML = `<span class="legenda-ponto"></span>${nome} (${valores[indice]})`;
      item.addEventListener('click', () => filtrarTabelaPorAnalista(nome));
      legenda.appendChild(item);
    });
  }

  function inicializarGraficoAlocacao() {
    const container = document.querySelector('#grafico-alocacao');
    if (!container) return;

    const contagemPorAnalista = {};
    projetosAtivos().forEach((projeto) => {
      contagemPorAnalista[projeto.responsavel_tecnico] = (contagemPorAnalista[projeto.responsavel_tecnico] || 0) + 1;
    });

    const nomes = Object.keys(contagemPorAnalista);
    const valores = nomes.map((nome) => contagemPorAnalista[nome]);
    const cores = obterCoresTokens();

    const grafico = new ApexCharts(container, {
      chart: {
        type: 'donut',
        height: 210,
        fontFamily: 'var(--fonte-familia-corpo)',
        events: {
          dataPointSelection: (evento, contextoGrafico, config) => {
            const nomeAnalista = nomes[config.dataPointIndex];
            if (nomeAnalista) filtrarTabelaPorAnalista(nomeAnalista);
          }
        }
      },
      series: valores,
      labels: nomes,
      colors: nomes.map((_, indice) => cores[indice % cores.length]),
      legend: { show: false },
      dataLabels: { enabled: true },
      plotOptions: {
        pie: {
          donut: {
            labels: {
              show: true,
              total: {
                show: true,
                label: 'Ativos',
                formatter: () => String(valores.reduce((soma, valor) => soma + valor, 0))
              }
            }
          }
        }
      }
    });

    grafico.render();
    renderizarLegendaAlocacao(nomes, valores, cores);
  }

  // --- init ---
  const inputDiasPrazo = document.getElementById('input-dias-prazo');
  const diasSalvos = parseInt(obterCookie(COOKIE_DIAS_PRAZO), 10);
  if (!isNaN(diasSalvos) && diasSalvos > 0) {
    inputDiasPrazo.value = Math.min(diasSalvos, DIAS_PRAZO_MAXIMO);
  }

  renderizarTabela();
  renderizarCards();
  renderizarProximosPrazos();

  inputDiasPrazo.addEventListener('input', () => {
    definirCookie(COOKIE_DIAS_PRAZO, lerDiasPrazo());
    renderizarCards();
    renderizarProximosPrazos();
  });

  const botaoLimparFiltro = document.getElementById('btn-limpar-filtro');
  if (botaoLimparFiltro) {
    botaoLimparFiltro.addEventListener('click', limparFiltroAnalista);
  }

  if (window.ApexCharts) {
    inicializarGraficoVulnerabilidades();
    inicializarGraficoAlocacao();
  } else {
    console.warn('ApexCharts não carregou (CDN indisponível?) — gráficos da Dashboard do Gestor não serão exibidos.');
    const mensagemFallback = '<p style="text-align:center;color:var(--cor-texto-secundario);padding:2rem 0;">Gráfico indisponível no momento.</p>';
    document.querySelector('#grafico-vulnerabilidades').innerHTML = mensagemFallback;
    document.querySelector('#grafico-alocacao').innerHTML = mensagemFallback;
  }
})();
