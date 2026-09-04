(function () {
  // --- helpers de data ---
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

  // --- dados mock ---
  const projetosMock = [
    { id: 1, nome: 'Pentest WEB - Cliente Atacado', cliente: 'Cliente1', responsavel_tecnico: 'André', analistas_alocados: ['André'], status: 'EM_ANDAMENTO', data_inicio: d(-40), data_fim_prevista: d(20), data_fim_real: null, updated_at: d(-2), vulnerabilidades_total: 14, vulnerabilidades_criticas: 3, vulnerabilidades_altas: 2, dias_aviso_prazo: 15, checklist_itens_total: 24, checklist_itens_concluidos: 14 },
    { id: 2, nome: 'Pentest WEB - Cliente Magazine Luiza', cliente: 'Cliente2', responsavel_tecnico: 'Marcos', analistas_alocados: ['Marcos'], status: 'EM_ANDAMENTO', data_inicio: d(-35), data_fim_prevista: d(3), data_fim_real: null, updated_at: d(-1), vulnerabilidades_total: 9, vulnerabilidades_criticas: 1, vulnerabilidades_altas: 1, dias_aviso_prazo: 5, checklist_itens_total: 18, checklist_itens_concluidos: 16 },
    { id: 3, nome: 'Pentest WEB - Cliente Fort', cliente: 'Cliente3', responsavel_tecnico: 'José', analistas_alocados: ['José', 'João'], status: 'EM_ANDAMENTO', data_inicio: d(-50), data_fim_prevista: d(-4), data_fim_real: null, updated_at: d(-5), vulnerabilidades_total: 20, vulnerabilidades_criticas: 5, vulnerabilidades_altas: 3, dias_aviso_prazo: 10, checklist_itens_total: 30, checklist_itens_concluidos: 9 },
    { id: 4, nome: 'Pentest Mobile - Cliente Vuon', cliente: 'Cliente4', responsavel_tecnico: 'João', analistas_alocados: ['João'], status: 'EM_ANDAMENTO', data_inicio: d(-15), data_fim_prevista: d(45), data_fim_real: null, updated_at: d(-3), vulnerabilidades_total: 4, vulnerabilidades_criticas: 1, vulnerabilidades_altas: 0, dias_aviso_prazo: 20, checklist_itens_total: 12, checklist_itens_concluidos: 2 },
    { id: 5, nome: 'Pentest Infra - Cliente Solvex', cliente: 'Cliente5', responsavel_tecnico: 'André', analistas_alocados: ['André', 'Marcos'], status: 'EM_ANDAMENTO', data_inicio: d(-20), data_fim_prevista: d(10), data_fim_real: null, updated_at: d(-1), vulnerabilidades_total: 11, vulnerabilidades_criticas: 2, vulnerabilidades_altas: 1, dias_aviso_prazo: 10, checklist_itens_total: 20, checklist_itens_concluidos: 12 },
    { id: 6, nome: 'Pentest API - Cliente Norvax', cliente: 'Cliente6', responsavel_tecnico: 'Marcos', analistas_alocados: ['Marcos'], status: 'EM_ANDAMENTO', data_inicio: d(-8), data_fim_prevista: d(60), data_fim_real: null, updated_at: d(-1), vulnerabilidades_total: 2, vulnerabilidades_criticas: 0, vulnerabilidades_altas: 0, dias_aviso_prazo: 15, checklist_itens_total: 10, checklist_itens_concluidos: 1 },
    { id: 7, nome: 'Pentest WEB - Cliente Bramido', cliente: 'Cliente7', responsavel_tecnico: 'José', analistas_alocados: ['José'], status: 'EM_ANDAMENTO', data_inicio: d(-25), data_fim_prevista: d(14), data_fim_real: null, updated_at: d(-4), vulnerabilidades_total: 17, vulnerabilidades_criticas: 4, vulnerabilidades_altas: 2, dias_aviso_prazo: 3, checklist_itens_total: 22, checklist_itens_concluidos: 20 },
    { id: 8, nome: 'Pentest WEB - Cliente Oitava', cliente: 'Cliente8', responsavel_tecnico: 'João', analistas_alocados: ['João'], status: 'CONCLUIDO', data_inicio: d(-90), data_fim_prevista: d(-35), data_fim_real: d(-30), updated_at: d(-30), vulnerabilidades_total: 8, vulnerabilidades_criticas: 1, vulnerabilidades_altas: 1, dias_aviso_prazo: 15, checklist_itens_total: 16, checklist_itens_concluidos: 16 },
    { id: 9, nome: 'Pentest Infra - Cliente Nona', cliente: 'Cliente9', responsavel_tecnico: 'André', analistas_alocados: ['André'], status: 'CONCLUIDO', data_inicio: d(-110), data_fim_prevista: d(-58), data_fim_real: d(-60), updated_at: d(-60), vulnerabilidades_total: 6, vulnerabilidades_criticas: 1, vulnerabilidades_altas: 0, dias_aviso_prazo: 15, checklist_itens_total: 14, checklist_itens_concluidos: 14 },
    { id: 10, nome: 'Pentest API - Cliente Decima', cliente: 'Cliente10', responsavel_tecnico: 'Marcos', analistas_alocados: ['Marcos'], status: 'CONCLUIDO', data_inicio: d(-130), data_fim_prevista: d(-82), data_fim_real: d(-80), updated_at: d(-80), vulnerabilidades_total: 12, vulnerabilidades_criticas: 2, vulnerabilidades_altas: 2, dias_aviso_prazo: 15, checklist_itens_total: 20, checklist_itens_concluidos: 20 },
    { id: 11, nome: 'Pentest WEB - Cliente Onze', cliente: 'Cliente11', responsavel_tecnico: 'José', analistas_alocados: ['José'], status: 'CONCLUIDO', data_inicio: d(-450), data_fim_prevista: d(-402), data_fim_real: d(-400), updated_at: d(-400), vulnerabilidades_total: 10, vulnerabilidades_criticas: 2, vulnerabilidades_altas: 1, dias_aviso_prazo: 15, checklist_itens_total: 18, checklist_itens_concluidos: 18 },
    { id: 12, nome: 'Pentest Mobile - Cliente Doze', cliente: 'Cliente12', responsavel_tecnico: 'João', analistas_alocados: ['João'], status: 'CONCLUIDO', data_inicio: d(-470), data_fim_prevista: d(-422), data_fim_real: d(-420), updated_at: d(-420), vulnerabilidades_total: 5, vulnerabilidades_criticas: 0, vulnerabilidades_altas: 0, dias_aviso_prazo: 15, checklist_itens_total: 9, checklist_itens_concluidos: 9 }
  ];

  const analistasMock = [
    { id: 1, nome: 'André', limite_projetos: 2 },
    { id: 2, nome: 'José', limite_projetos: 3 },
    { id: 3, nome: 'Marcos', limite_projetos: 6 },
    { id: 4, nome: 'João', limite_projetos: 4 },
    { id: 5, nome: 'Ana', limite_projetos: 2 }
  ];

  // --- constantes ---
  const DIAS_PRAZO_PADRAO = 15;

  const RESUMO_NIVEL = {
    ok: 'dentro do limite',
    atencao: 'atenção: perto do limite',
    alerta: 'alerta: quase no limite',
    critico: 'no limite ou acima dele'
  };

  // --- lógica / cálculo ---
  function projetosAtivos() {
    return projetosMock.filter((projeto) => projeto.status === 'EM_ANDAMENTO');
  }

  function projetoVencido(projeto) {
    return new Date(projeto.data_fim_prevista) < hoje;
  }

  function projetoEmRisco(projeto) {
    const antecedencia = projeto.dias_aviso_prazo ?? DIAS_PRAZO_PADRAO;
    return new Date(projeto.data_fim_prevista) <= diasAPartirDeHoje(antecedencia);
  }

  function rotuloPrazo(projeto) {
    if (projetoVencido(projeto)) return { texto: 'Vencido', nivel: 'vencido' };
    if (projetoEmRisco(projeto)) return { texto: 'Em risco', nivel: 'risco' };
    return null;
  }

  function calcularDiasRestantes(projeto) {
    const prazo = new Date(projeto.data_fim_prevista);
    return Math.round((prazo - hoje) / (1000 * 60 * 60 * 24));
  }

  function calcularProgresso(projeto) {
    const total = projeto.checklist_itens_total || 0;
    const concluidos = projeto.checklist_itens_concluidos || 0;
    const percentual = total > 0 ? Math.round((concluidos / total) * 100) : 0;
    return { concluidos, total, percentual };
  }

  function nivelOcupacao(percentual) {
    if (percentual >= 100) return 'critico';
    if (percentual >= 80) return 'alerta';
    if (percentual >= 60) return 'atencao';
    return 'ok';
  }

  // TODO: trocar por window.location.href = BASE_URL + 'dashboard-projeto/' + idProjeto quando a rota existir.
  function irParaDashboardProjeto(idProjeto) {
    console.log('TODO: abrir Dashboard do Projeto para o projeto', idProjeto);
  }

  // --- filtros ---
  // Reaproveita a busca global de `tabela.js` para filtrar a tabela.
  function aplicarBuscaTabela(termo) {
    const campoBusca = document.querySelector('.input-pesquisaSuperior input[type="text"]');
    if (!campoBusca) return;
    campoBusca.value = termo;
    campoBusca.dispatchEvent(new Event('input', { bubbles: true }));
  }

  // `rotulo` preenche e exibe o chip; valor falso o esconde.
  function definirChipFiltro(rotulo) {
    const chip = document.getElementById('filtro-ativo-chip');
    const nomeSpan = document.getElementById('filtro-ativo-nome');
    if (!chip || !nomeSpan) return;
    if (rotulo) nomeSpan.textContent = rotulo;
    chip.hidden = !rotulo;
  }

  function filtrarTabela(termoBusca, rotuloChip) {
    aplicarBuscaTabela(termoBusca);
    definirChipFiltro(rotuloChip);
  }

  const limparFiltro = () => filtrarTabela('', null);
  const filtrarTabelaPorNome = (nome) => filtrarTabela(nome, nome);
  const filtrarTabelaPorPrazoRisco = () => filtrarTabela('em risco', 'Prazos em risco/vencidos');
  const filtrarTabelaPorVulnCritica = () => filtrarTabela('vulnerabilidade critica em aberto', 'Vulnerabilidades críticas em aberto');

  // --- renderização: cards ---
  function renderizarCards() {
    const ativos = projetosAtivos();

    const vulnsCriticas = ativos.reduce(
      (soma, projeto) => soma + projeto.vulnerabilidades_criticas,
      0
    );

    const prazosEmRisco = ativos.filter(projetoEmRisco).length;

    document.getElementById('valor-em-andamento').textContent = ativos.length;
    document.getElementById('valor-vulns-criticas').textContent = vulnsCriticas;
    document.getElementById('valor-prazos-risco').textContent = prazosEmRisco;

    const cardVulnsCriticas = document.getElementById('card-vulns-criticas');
    if (cardVulnsCriticas) {
      cardVulnsCriticas.classList.toggle('card-gestor--vulns-risco', vulnsCriticas > 0);
    }
  }

  // --- renderização: tabela ---
  function renderizarTabela() {
    const corpo = document.querySelector('#table tbody');
    if (!corpo) return;
    corpo.innerHTML = '';

    const ativos = projetosAtivos();

    if (!ativos.length) {
      corpo.innerHTML = '<tr><td colspan="7" style="text-align:center">Nenhum projeto em andamento no momento.</td></tr>';
      return;
    }

    ativos.forEach((projeto, indice) => {
      const linha = document.createElement('tr');
      linha.dataset.projetoId = String(projeto.id);

      const badgePrazo = rotuloPrazo(projeto);
      const temCritica = projeto.vulnerabilidades_criticas > 0;
      const analistas = projeto.analistas_alocados || [];
      const diasRestantes = calcularDiasRestantes(projeto);

      linha.innerHTML = `
        <td>${projeto.nome}</td>
        <td>${projeto.cliente}</td>
        <td>${projeto.responsavel_tecnico}</td>
        <td class="celula-analistas" data-valores='${JSON.stringify(analistas)}'>${analistas.join(', ') || '-'}</td>
        <td class="celula-prazo">
          <span class="celula-prazo__data">${formatarDataBR(projeto.data_fim_prevista)}</span>
          ${badgePrazo ? `<span class="badge-prazo badge-prazo--${badgePrazo.nivel}">${badgePrazo.texto}</span>` : ''}
          ${badgePrazo?.nivel === 'vencido' ? '<span class="oculto-visualmente" aria-hidden="true">em risco</span>' : ''}
        </td>
        <td class="celula-dias-restantes${badgePrazo ? ` celula-dias-restantes--${badgePrazo.nivel}` : ''}">${diasRestantes}</td>
        <td class="celula-vuln-critica">
          <input type="checkbox" class="checkbox-vuln-critica" ${temCritica ? 'checked' : ''} disabled title="${temCritica ? 'Vulnerabilidade crítica em aberto' : 'Nenhuma vulnerabilidade crítica em aberto'}" />
          ${temCritica ? '<span class="oculto-visualmente" aria-hidden="true">vulnerabilidade critica em aberto</span>' : ''}
        </td>
      `;
      linha.addEventListener('click', () => irParaDashboardProjeto(projeto.id));
      linha.addEventListener('mouseenter', () => destacarBarraProjeto(indice));
      linha.addEventListener('mouseleave', limparDestaqueBarra);
      corpo.appendChild(linha);
    });
  }

  // --- renderização: gráficos ---
  const CORES_GRAFICO = ['#0a6bb5', '#00a9d1', '#00966f', '#4cc61e', '#c8e61e', '#ffd200', '#ff8c00', '#ff0000', '#ff1f6b', '#e6007e', '#7b2d8e', '#4b3f96'];

  const obterContainerGrafico = () => document.querySelector('#grafico-progresso');

  // Percorre barras e rótulos do gráfico (elementos com atributo `j` = índice do ponto).
  function paraCadaElementoGrafico(callback) {
    const container = obterContainerGrafico();
    if (!container) return;
    container.querySelectorAll('.apexcharts-bar-area, .apexcharts-data-labels').forEach(callback);
  }

  let barraComEventoSimulado = null;

  // Simula eventos de mouse sobre a barra para o ApexCharts abrir/fechar o tooltip dela.
  function dispararEventoMouseNaBarra(barra, tipoEvento) {
    if (!barra) return;
    const area = barra.getBoundingClientRect();
    barra.dispatchEvent(new MouseEvent(tipoEvento, {
      bubbles: true,
      cancelable: true,
      view: window,
      clientX: area.left + area.width / 2,
      clientY: area.top + area.height / 2
    }));
  }

  function esconderTooltipGrafico() {
    const container = obterContainerGrafico();
    if (!container) return;
    if (barraComEventoSimulado) {
      dispararEventoMouseNaBarra(barraComEventoSimulado, 'mouseout');
      dispararEventoMouseNaBarra(barraComEventoSimulado, 'mouseleave');
      barraComEventoSimulado = null;
    }
    // Fallback: força o fechamento removendo a classe que o ApexCharts usa para exibir o tooltip.
    container.querySelectorAll('.apexcharts-tooltip.apexcharts-active').forEach((tooltip) => {
      tooltip.classList.remove('apexcharts-active');
    });
  }

  function destacarBarraProjeto(indiceProjeto) {
    let barraAlvo = null;
    paraCadaElementoGrafico((elemento) => {
      if (!elemento.hasAttribute('j')) return;
      const ehAlvo = Number(elemento.getAttribute('j')) === indiceProjeto;
      elemento.style.transition = 'opacity 0.15s ease';
      elemento.style.opacity = ehAlvo ? '1' : '0.2';
      if (ehAlvo && elemento.classList.contains('apexcharts-bar-area')) barraAlvo = elemento;
    });
    if (barraAlvo) {
      barraComEventoSimulado = barraAlvo;
      dispararEventoMouseNaBarra(barraAlvo, 'mousemove');
    }
  }

  function limparDestaqueBarra() {
    paraCadaElementoGrafico((elemento) => { elemento.style.opacity = ''; });
    esconderTooltipGrafico();
  }

  function inicializarGraficoProgresso() {
    const container = obterContainerGrafico();
    if (!container) return;

    const ativos = projetosAtivos();
    const percentuais = ativos.map((projeto) => calcularProgresso(projeto).percentual);

    const grafico = new ApexCharts(container, {
      chart: {
        type: 'bar',
        height: 220,
        fontFamily: 'var(--fonte-familia-corpo)',
        toolbar: { show: false },
        events: {
          dataPointSelection: (evento, contextoGrafico, config) => {
            const projeto = ativos[config.dataPointIndex];
            if (projeto) filtrarTabelaPorNome(projeto.nome);
          }
        }
      },
      series: [{ name: 'Progresso', data: percentuais }],
      xaxis: {
        // Categoria como array: ApexCharts quebra em várias linhas em vez de truncar/rotacionar.
        categories: ativos.map((projeto) => projeto.nome.split(' - ')),
        labels: { rotate: 0, trim: false, style: { fontSize: '11px' } }
      },
      yaxis: { max: 100, labels: { formatter: (valor) => `${valor}%` } },
      colors: CORES_GRAFICO,
      dataLabels: {
        enabled: true,
        formatter: (valor) => `${valor}%`,
        style: { colors: ['#fff'] }
      },
      tooltip: {
        y: {
          formatter: (valor, { dataPointIndex }) => {
            const projeto = ativos[dataPointIndex];
            if (!projeto) return `${valor}%`;
            const dias = calcularDiasRestantes(projeto);
            const rotuloDias = dias < 0 ? `Vencido há ${Math.abs(dias)} dia(s)` : dias === 0 ? 'Vence hoje' : `Faltam ${dias} dia(s)`;
            return `${valor}% · ${rotuloDias}`;
          }
        }
      },
      legend: { show: false },
      plotOptions: { bar: { borderRadius: 4, columnWidth: '55%', distributed: true } }
    });

    grafico.render();
  }

  // --- renderização: alocação de analistas ---
  function renderizarAlocacaoAnalistas() {
    const lista = document.getElementById('lista-alocacao');
    if (!lista) return;
    lista.innerHTML = '';

    const contagemPorAnalista = {};
    projetosAtivos().forEach((projeto) => {
      contagemPorAnalista[projeto.responsavel_tecnico] = (contagemPorAnalista[projeto.responsavel_tecnico] || 0) + 1;
    });

    const analistas = analistasMock
      .map((analista) => {
        const alocados = contagemPorAnalista[analista.nome] || 0;
        const percentual = analista.limite_projetos > 0 ? (alocados / analista.limite_projetos) * 100 : 0;
        return { ...analista, alocados, percentual };
      })
      .sort((a, b) => b.percentual - a.percentual);

    analistas.forEach((analista) => {
      const nivel = nivelOcupacao(analista.percentual);
      const larguraBarra = Math.min(analista.percentual, 100);

      const item = document.createElement('li');
      item.className = 'alocacao-item';
      item.dataset.nivel = nivel;
      item.title = `${analista.nome}: ${analista.alocados} de ${analista.limite_projetos} projetos em andamento (${RESUMO_NIVEL[nivel]}). Clique para filtrar a tabela.`;
      item.innerHTML = `
        <div class="alocacao-item__cabecalho">
          <span class="alocacao-item__nome">${analista.nome}</span>
          <span class="alocacao-item__contagem">${analista.alocados}/${analista.limite_projetos}</span>
        </div>
        <div class="alocacao-item__trilha">
          <div class="alocacao-item__barra" style="width: ${larguraBarra}%"></div>
        </div>
      `;
      item.addEventListener('click', () => filtrarTabelaPorNome(analista.nome));
      lista.appendChild(item);
    });
  }

  // --- init ---
  renderizarTabela();
  renderizarCards();

  const vincularClique = (id, acao) => document.getElementById(id)?.addEventListener('click', acao);
  vincularClique('btn-limpar-filtro', limparFiltro);
  vincularClique('card-em-andamento', limparFiltro);
  vincularClique('card-vulns-criticas', filtrarTabelaPorVulnCritica);
  vincularClique('card-prazos-risco', filtrarTabelaPorPrazoRisco);

  renderizarAlocacaoAnalistas();

  if (window.ApexCharts) {
    inicializarGraficoProgresso();
  } else {
    console.warn('ApexCharts não carregou (CDN indisponível?) — o gráfico de progresso não será exibido.');
    const mensagemFallback = '<p style="text-align:center;color:var(--cor-texto-secundario);padding:2rem 0;">Gráfico indisponível no momento.</p>';
    obterContainerGrafico().innerHTML = mensagemFallback;
  }
})();
