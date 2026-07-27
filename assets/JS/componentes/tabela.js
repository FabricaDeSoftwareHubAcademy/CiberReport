(function () {
  const corpo = document.querySelector('table tbody');
  if (!corpo) return;

  const cabecalhos = document.querySelectorAll('table thead th[data-col]');
  let colunaOrdenada = null;
  let ordemAscendente = true;

  // --- paginação ---
  const COOKIE_LINHAS_POR_PAGINA = 'linhasPorPagina';

  function obterCookie(nome) {
    const linha = document.cookie.split('; ').find((item) => item.startsWith(nome + '='));
    return linha ? decodeURIComponent(linha.split('=')[1]) : null;
  }

  function definirCookie(nome, valor) {
    const expira = new Date();
    expira.setFullYear(expira.getFullYear() + 1);
    document.cookie = `${nome}=${encodeURIComponent(valor)}; expires=${expira.toUTCString()}; path=/; samesite=lax`;
  }

  const OPCOES_LINHAS_POR_PAGINA = [10, 20, 30, 50, 100];
  const linhasPorPaginaSalvas = parseInt(obterCookie(COOKIE_LINHAS_POR_PAGINA), 10);
  let linhasPorPagina = !isNaN(linhasPorPaginaSalvas) && linhasPorPaginaSalvas > 0
    ? linhasPorPaginaSalvas
    : OPCOES_LINHAS_POR_PAGINA[0];
  let paginaAtual = 1;
  const tabela = corpo.closest('table');
  const containerPaginacao = tabela ? tabela.querySelector('tfoot .paginacao') : null;
  const celulaRodape = containerPaginacao ? containerPaginacao.closest('.rodape-tabela') : null;

  let containerItensPorPagina = null;
  let containerIrPara = null;
  if (celulaRodape) {
    const wrapperRodape = document.createElement('div');
    wrapperRodape.className = 'rodape-tabela-conteudo';
    celulaRodape.insertBefore(wrapperRodape, containerPaginacao);

    containerIrPara = document.createElement('div');
    containerIrPara.className = 'ir-para';

    containerItensPorPagina = document.createElement('div');
    containerItensPorPagina.className = 'itens-por-pagina';

    wrapperRodape.appendChild(containerIrPara);
    wrapperRodape.appendChild(containerItensPorPagina);
    wrapperRodape.appendChild(containerPaginacao);
  }

  function todasLinhas() {
    return Array.from(corpo.querySelectorAll('tr'));
  }

  function linhasFiltradas() {
    return todasLinhas().filter(linhaPassaFiltros);
  }

  function totalPaginas() {
    return Math.max(1, Math.ceil(linhasFiltradas().length / linhasPorPagina));
  }

  function exibirPagina(pagina) {
    const filtradas = new Set(linhasFiltradas());
    const inicio = (pagina - 1) * linhasPorPagina;
    const fim = inicio + linhasPorPagina;
    let indiceVisivel = 0;
    todasLinhas().forEach((linha) => {
      if (!filtradas.has(linha)) {
        linha.style.display = 'none';
        return;
      }
      linha.style.display = (indiceVisivel >= inicio && indiceVisivel < fim) ? '' : 'none';
      indiceVisivel++;
    });
    paginaAtual = Math.min(pagina, totalPaginas());
    renderizarBotoes();
  }

  function renderizarSeletorItensPorPagina() {
    if (!containerItensPorPagina) return;
    containerItensPorPagina.innerHTML = '';

    const label = document.createElement('label');
    label.className = 'itens-por-pagina__label';
    label.textContent = 'Linhas por página:';
    label.setAttribute('for', 'itens-por-pagina-select');

    const select = document.createElement('select');
    select.className = 'itens-por-pagina__select';
    select.id = 'itens-por-pagina-select';

    const opcoesExibidas = OPCOES_LINHAS_POR_PAGINA.includes(linhasPorPagina)
      ? OPCOES_LINHAS_POR_PAGINA
      : [...OPCOES_LINHAS_POR_PAGINA, linhasPorPagina].sort((a, b) => a - b);

    opcoesExibidas.forEach((valor) => {
      const opcao = document.createElement('option');
      opcao.value = String(valor);
      opcao.textContent = String(valor);
      if (valor === linhasPorPagina) opcao.selected = true;
      select.appendChild(opcao);
    });

    const opcaoOutro = document.createElement('option');
    opcaoOutro.value = 'outro';
    opcaoOutro.textContent = 'Outro...';
    select.appendChild(opcaoOutro);

    const inputCustom = document.createElement('input');
    inputCustom.type = 'number';
    inputCustom.min = '1';
    inputCustom.step = '1';
    inputCustom.className = 'itens-por-pagina__input';
    inputCustom.placeholder = 'Qtd.';
    inputCustom.setAttribute('aria-label', 'Quantidade de linhas por página');
    inputCustom.hidden = true;

    function aplicarValorCustom() {
      const valor = parseInt(inputCustom.value, 10);
      if (!isNaN(valor) && valor > 0) {
        linhasPorPagina = valor;
        definirCookie(COOKIE_LINHAS_POR_PAGINA, linhasPorPagina);
        renderizarSeletorItensPorPagina();
        exibirPagina(1);
      } else {
        select.value = String(linhasPorPagina);
        inputCustom.hidden = true;
      }
    }

    select.addEventListener('change', () => {
      if (select.value === 'outro') {
        inputCustom.hidden = false;
        inputCustom.value = '';
        inputCustom.focus();
      } else {
        inputCustom.hidden = true;
        linhasPorPagina = parseInt(select.value, 10);
        definirCookie(COOKIE_LINHAS_POR_PAGINA, linhasPorPagina);
        exibirPagina(1);
      }
    });

    inputCustom.addEventListener('keydown', (evento) => {
      if (evento.key === 'Enter') {
        evento.preventDefault();
        aplicarValorCustom();
      }
    });
    inputCustom.addEventListener('blur', aplicarValorCustom);

    containerItensPorPagina.appendChild(label);
    containerItensPorPagina.appendChild(select);
    containerItensPorPagina.appendChild(inputCustom);
  }

  // --- tooltip rápido para células com conteúdo cortado ---
  // Substitui o title nativo do navegador (aparece com atraso e sem estilo)
  // por um balão fixo posicionado via getBoundingClientRect. Como usa
  // position: fixed e fica anexado ao <body>, escapa de qualquer ancestral
  // com overflow: hidden/auto (ex.: o wrapper da tabela tem overflow-x: auto,
  // que também clipa o eixo Y e cortaria um tooltip posicionado com
  // position: absolute nas primeiras linhas). Detecta overflow dinamicamente
  // no hover, então funciona em qualquer <th>/<td> de qualquer tabela que use
  // este componente, com qualquer forma de truncamento (nowrap+ellipsis,
  // line-clamp, max-width em elemento interno etc.), sem precisar recalcular
  // a cada renderização/paginação.
  // Elementos com atributo data-tooltip="texto" (ex.: badge "+N" de uma lista
  // de tags) mostram esse texto customizado em vez do textContent da célula.
  let tooltipCelula = null;
  let gatilhoTooltipAtivo = null;

  function obterTooltipCelula() {
    if (!tooltipCelula) {
      tooltipCelula = document.createElement('div');
      tooltipCelula.className = 'tabela-tooltip';
      document.body.appendChild(tooltipCelula);
    }
    return tooltipCelula;
  }

  function elementoTransbordou(elemento) {
    return elemento.scrollWidth > elemento.clientWidth + 1 || elemento.scrollHeight > elemento.clientHeight + 1;
  }

  function encontrarConteudoTruncado(celula) {
    if (elementoTransbordou(celula)) return celula;
    return Array.from(celula.querySelectorAll('*')).find(elementoTransbordou) || null;
  }

  // Posiciona no canto inferior direito do cursor (não centralizado nem acima
  // dele), para tooltips de células largas ficarem coladas em onde o mouse
  // realmente está.
  const DESLOCAMENTO_CURSOR = 14;

  function posicionarTooltipCelula(x, y, tooltip) {
    const largura = tooltip.offsetWidth;
    const altura = tooltip.offsetHeight;

    const esquerda = Math.min(x + DESLOCAMENTO_CURSOR, window.innerWidth - largura - 8);
    const topo = Math.min(y + DESLOCAMENTO_CURSOR, window.innerHeight - altura - 8);

    tooltip.style.left = `${esquerda}px`;
    tooltip.style.top = `${topo}px`;
  }

  function mostrarTooltipCelula(alvo, texto, x, y) {
    const tooltip = obterTooltipCelula();
    tooltip.textContent = texto;
    tooltip.classList.add('tabela-tooltip--visivel');
    posicionarTooltipCelula(x, y, tooltip);
    gatilhoTooltipAtivo = alvo;
  }

  function esconderTooltipCelula() {
    tooltipCelula?.classList.remove('tabela-tooltip--visivel');
    gatilhoTooltipAtivo = null;
  }

  function aoPassarMouseCelula(evento) {
    const gatilhoCustom = evento.target.closest('[data-tooltip]');
    if (gatilhoCustom) {
      if (gatilhoCustom !== gatilhoTooltipAtivo) {
        mostrarTooltipCelula(gatilhoCustom, gatilhoCustom.dataset.tooltip, evento.clientX, evento.clientY);
      }
      return;
    }

    const celula = evento.target.closest('th, td');
    if (!celula || celula === gatilhoTooltipAtivo) return;
    if (celula.querySelector('[data-tooltip]')) return; // tratado quando o mouse alcançar o gatilho

    const truncado = encontrarConteudoTruncado(celula);
    if (!truncado) return;
    mostrarTooltipCelula(celula, celula.textContent.trim(), evento.clientX, evento.clientY);
  }

  function aoMoverMouseCelula(evento) {
    if (!gatilhoTooltipAtivo || !tooltipCelula) return;
    posicionarTooltipCelula(evento.clientX, evento.clientY, tooltipCelula);
  }

  function aoSairMouseCelula(evento) {
    if (!gatilhoTooltipAtivo) return;
    if (evento.relatedTarget && gatilhoTooltipAtivo.contains(evento.relatedTarget)) return;
    esconderTooltipCelula();
  }

  if (tabela) {
    tabela.addEventListener('mouseover', aoPassarMouseCelula);
    tabela.addEventListener('mousemove', aoMoverMouseCelula);
    tabela.addEventListener('mouseout', aoSairMouseCelula);
    window.addEventListener('scroll', esconderTooltipCelula, true);
    window.addEventListener('resize', esconderTooltipCelula);
  }

  function paginasParaExibir(atual, total) {
    const paginas = [1];
    const intervaloEsquerda = Math.max(2, atual - 2);
    const intervaloDireita = Math.min(total - 1, atual + 2);

    if (intervaloEsquerda > 2) paginas.push('...');
    for (let p = intervaloEsquerda; p <= intervaloDireita; p++) paginas.push(p);
    if (intervaloDireita < total - 1) paginas.push('...');
    if (total > 1) paginas.push(total);

    return paginas;
  }

  function renderizarIrPara(total) {
    if (!containerIrPara) return;
    containerIrPara.innerHTML = '';
    if (total <= 1) return;

    const inputIrPara = document.createElement('input');
    inputIrPara.type = 'number';
    inputIrPara.min = '1';
    inputIrPara.max = String(total);
    inputIrPara.className = 'ir-para__input';
    inputIrPara.placeholder = 'Ir para';
    inputIrPara.setAttribute('aria-label', 'Ir para a página');

    function irParaPagina() {
      const valor = parseInt(inputIrPara.value, 10);
      if (!isNaN(valor) && valor >= 1 && valor <= total) {
        exibirPagina(valor);
      }
      inputIrPara.value = '';
    }

    inputIrPara.addEventListener('keydown', (evento) => {
      if (evento.key === 'Enter') {
        evento.preventDefault();
        irParaPagina();
      }
    });

    containerIrPara.appendChild(inputIrPara);
  }

  function renderizarBotoes() {
    if (!containerPaginacao) return;
    const total = totalPaginas();
    containerPaginacao.innerHTML = '';
    renderizarIrPara(total);

    if (total > 1) {
      const btnAnterior = document.createElement('button');
      btnAnterior.className = 'pag-btn';
      btnAnterior.setAttribute('aria-label', 'Página anterior');
      btnAnterior.innerHTML = '<i class="fa-solid fa-arrow-left"></i>';
      btnAnterior.disabled = paginaAtual === 1;
      btnAnterior.addEventListener('click', () => exibirPagina(paginaAtual - 1));
      containerPaginacao.appendChild(btnAnterior);
    }

    paginasParaExibir(paginaAtual, total).forEach((p) => {
      if (p === '...') {
        const reticencias = document.createElement('span');
        reticencias.className = 'pag-reticencias';
        reticencias.textContent = '...';
        reticencias.setAttribute('aria-hidden', 'true');
        containerPaginacao.appendChild(reticencias);
        return;
      }

      const btn = document.createElement('button');
      btn.className = 'pag-num' + (p === paginaAtual ? ' ativo' : '');
      btn.setAttribute('aria-label', `Página ${p}`);
      if (p === paginaAtual) btn.setAttribute('aria-current', 'page');
      btn.textContent = p;
      btn.addEventListener('click', () => exibirPagina(p));
      containerPaginacao.appendChild(btn);
    });

    if (total > 1) {
      const btnProximo = document.createElement('button');
      btnProximo.className = 'pag-btn';
      btnProximo.setAttribute('aria-label', 'Próxima página');
      btnProximo.innerHTML = '<i class="fa-solid fa-arrow-right"></i>';
      btnProximo.disabled = paginaAtual === total;
      btnProximo.addEventListener('click', () => exibirPagina(paginaAtual + 1));
      containerPaginacao.appendChild(btnProximo);
    }
  }

  // --- ordenação ---
  // data-filtro="alfabetica"|"numero" no <th> força o tipo de comparação usado
  // ao ordenar a coluna (em vez da auto-detecção texto/número abaixo).
  const colunasAlfabetica = new Set(
    Array.from(cabecalhos)
      .filter((cabecalho) => cabecalho.dataset.filtro === 'alfabetica')
      .map((cabecalho) => parseInt(cabecalho.dataset.col))
  );

  const colunasNumero = new Set(
    Array.from(cabecalhos)
      .filter((cabecalho) => cabecalho.dataset.filtro === 'numero')
      .map((cabecalho) => parseInt(cabecalho.dataset.col))
  );

  // data-filtro="data" no <th> soma ordenação cronológica ao filtro de lista
  // (ver seção "filtro de coluna" abaixo) — por isso também entra aqui.
  const colunasFiltroData = new Set(
    Array.from(cabecalhos)
      .filter((cabecalho) => cabecalho.dataset.filtro === 'data')
      .map((cabecalho) => parseInt(cabecalho.dataset.col))
  );

  const colunasData = new Set(
    Array.from(cabecalhos)
      .filter((cabecalho) => cabecalho.dataset.tipo === 'data')
      .map((cabecalho) => parseInt(cabecalho.dataset.col))
  );

  const colunasRisco = new Set(
    Array.from(cabecalhos)
      .filter((cabecalho) => cabecalho.dataset.tipo === 'risco')
      .map((cabecalho) => parseInt(cabecalho.dataset.col))
  );

  const ORDEM_RISCO = { baixo: 0, baixa: 0, medio: 1, media: 1, alto: 2, alta: 2, critico: 3, critica: 3 };

  function removerAcentos(texto) {
    return texto.normalize('NFD').replace(/[̀-ͯ]/g, '');
  }

  // Aceita tanto "dd/mm/aaaa[ hh:mm]" quanto "aaaa-mm-dd[Thh:mm]" (formato que
  // o PDO devolve para colunas DATE/DATETIME do MySQL, ex.: gerenciamento_projeto.php).
  function analisarDataBR(texto) {
    const valor = texto.trim();

    const iso = valor.match(/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}:\d{2}))?/);
    if (iso) {
      const [, a, m, d, hora = '00:00'] = iso;
      return new Date(`${a}-${m}-${d}T${hora}`).getTime();
    }

    const br = valor.match(/^(\d{2})\/(\d{2})\/(\d{4})(?:\s+(\d{2}:\d{2}))?/);
    if (br) {
      const [, d, m, a, hora = '00:00'] = br;
      return new Date(`${a}-${m}-${d}T${hora}`).getTime();
    }

    return NaN;
  }

  // Chave normalizada "aaaa-mm-dd" (mesmo formato do valor de <input type="date">)
  // usada para comparar filtro de data independente de como o texto original
  // está formatado na célula. Usa getters locais (não toISOString) para não
  // deslocar o dia por causa de fuso horário.
  function chaveDataISO(timestamp) {
    const data = new Date(timestamp);
    const ano = data.getFullYear();
    const mes = String(data.getMonth() + 1).padStart(2, '0');
    const dia = String(data.getDate()).padStart(2, '0');
    return `${ano}-${mes}-${dia}`;
  }

  function lerValorCelula(celula, coluna) {
    const texto = celula.textContent.trim();
    if (colunasAlfabetica.has(coluna)) {
      return removerAcentos(texto.toLowerCase());
    }
    if (colunasNumero.has(coluna)) {
      const numero = Number(texto);
      return isNaN(numero) ? texto.toLowerCase() : numero;
    }
    if (colunasData.has(coluna) || colunasFiltroData.has(coluna)) {
      return analisarDataBR(texto);
    }
    if (colunasRisco.has(coluna)) {
      const rank = ORDEM_RISCO[removerAcentos(texto.toLowerCase())];
      return rank !== undefined ? rank : -1;
    }
    const numero = Number(texto);
    if (!isNaN(numero) && texto !== '') return numero;
    return texto.toLowerCase();
  }

  function ordenarLinhas(coluna) {
    if (colunaOrdenada === coluna) {
      ordemAscendente = !ordemAscendente;
    } else {
      colunaOrdenada = coluna;
      ordemAscendente = true;
    }

    const linhas = todasLinhas();
    linhas.sort((a, b) => {
      const valorA = lerValorCelula(a.cells[coluna], coluna);
      const valorB = lerValorCelula(b.cells[coluna], coluna);
      if (valorA < valorB) return ordemAscendente ? -1 : 1;
      if (valorA > valorB) return ordemAscendente ? 1 : -1;
      return 0;
    });

    linhas.forEach((linha) => corpo.appendChild(linha));
    atualizarIcones();
    exibirPagina(1);
  }

  function atualizarIcones() {
    cabecalhos.forEach((cabecalho) => {
      const icone = cabecalho.querySelector('.sort-icon');
      if (!icone) return;
      const coluna = parseInt(cabecalho.dataset.col);
      if (coluna === colunaOrdenada) {
        icone.className = ordemAscendente
          ? 'fa-solid fa-sort-up sort-icon'
          : 'fa-solid fa-sort-down sort-icon';
      } else {
        icone.className = 'sort-icon';
      }
    });
  }

  // Colunas com data-filtro="lista" continuam ordenáveis normalmente: o clique
  // no ícone de funil chama evento.stopPropagation() (ver wiring do filtro-icon
  // abaixo), então só o clique no texto/sort-icon chega até aqui.
  cabecalhos.forEach((cabecalho) => {
    const coluna = parseInt(cabecalho.dataset.col);
    const rotulo = cabecalho.querySelector('.th-label');
    if (rotulo) {
      rotulo.addEventListener('click', () => ordenarLinhas(coluna));
    }
  });

  // --- filtro de coluna (seleção de valores) ---
  // Colunas com data-filtro="lista" ganham um painel de checkboxes com os
  // valores únicos da coluna. Cada <td> pode declarar data-valores='["a","b"]'
  // com a lista completa a ser filtrada (útil quando a célula exibe só um
  // resumo, ex.: tags truncadas); na ausência do atributo, usa-se o texto da
  // célula. Colunas data-filtro="data" ganham um painel diferente — só um
  // período "De/Até" (ver montarPainelFiltroData), sem lista de checkboxes.
  const TIPOS_FILTRO_PAINEL = new Set(['lista', 'data']);
  const filtrosAtivos = new Map();
  let painelFiltroAberto = null;

  function valoresCelula(celula) {
    if (celula.dataset.valores) {
      try {
        const valores = JSON.parse(celula.dataset.valores);
        if (Array.isArray(valores)) {
          return valores.map((valor) => String(valor).trim()).filter(Boolean);
        }
      } catch (erro) {
        // ignora JSON inválido e cai no fallback abaixo
      }
    }
    const texto = celula.textContent.trim();
    return texto ? [texto] : [];
  }

  // Normaliza o texto da célula de uma coluna data-filtro="data" para
  // "aaaa-mm-dd" (mesmo formato do <input type="date">), para comparar com o
  // período "De/Até" independente de a célula estar em dd/mm/aaaa ou aaaa-mm-dd.
  function chaveFiltroValor(valorBruto, coluna) {
    if (!colunasFiltroData.has(coluna)) return valorBruto;
    const timestamp = analisarDataBR(valorBruto);
    return isNaN(timestamp) ? valorBruto : chaveDataISO(timestamp);
  }

  function valoresUnicosColuna(coluna) {
    const valores = new Set();
    todasLinhas().forEach((linha) => {
      valoresCelula(linha.cells[coluna]).forEach((valor) => valores.add(valor));
    });
    return Array.from(valores).sort((a, b) => a.localeCompare(b, 'pt-BR'));
  }

  function linhaPassaFiltroData(linha, coluna, periodo) {
    if (!periodo.de && !periodo.ate) return true;
    const chaves = valoresCelula(linha.cells[coluna]).map((valor) => chaveFiltroValor(valor, coluna));
    return chaves.some((chave) => {
      if (periodo.de && chave < periodo.de) return false;
      if (periodo.ate && chave > periodo.ate) return false;
      return true;
    });
  }

  function linhaPassaFiltros(linha) {
    for (const [coluna, filtro] of filtrosAtivos) {
      if (colunasFiltroData.has(coluna)) {
        if (!linhaPassaFiltroData(linha, coluna, filtro)) return false;
        continue;
      }
      if (!filtro.size) continue;
      const valoresLinha = valoresCelula(linha.cells[coluna]);
      if (!valoresLinha.some((valor) => filtro.has(valor))) return false;
    }
    return true;
  }

  function aoClicarForaDoPainel(evento) {
    if (painelFiltroAberto && !painelFiltroAberto.contains(evento.target)) {
      fecharPainelFiltro();
    }
  }

  function aoRolarForaDoPainel(evento) {
    // 'scroll' não borbulha, mas é capturado na fase de captura do window mesmo
    // assim — inclusive quando a rolagem acontece dentro da própria lista de
    // opções (que tem overflow-y). Sem esse filtro, rolar a lista fecha o painel.
    if (painelFiltroAberto && !painelFiltroAberto.contains(evento.target)) {
      fecharPainelFiltro();
    }
  }

  function fecharPainelFiltro() {
    if (!painelFiltroAberto) return;
    painelFiltroAberto.remove();
    painelFiltroAberto = null;
    document.removeEventListener('click', aoClicarForaDoPainel, true);
    window.removeEventListener('resize', fecharPainelFiltro);
    window.removeEventListener('scroll', aoRolarForaDoPainel, true);
  }

  function atualizarIconeFiltro(cabecalho, coluna) {
    const icone = cabecalho.querySelector('.filtro-icon');
    if (!icone) return;
    const filtro = filtrosAtivos.get(coluna);
    const ativo = colunasFiltroData.has(coluna)
      ? Boolean(filtro && (filtro.de || filtro.ate))
      : (filtro?.size ?? 0) > 0;
    icone.classList.toggle('filtro-icon--ativo', ativo);
  }

  function aplicarFiltro(coluna, selecionados, cabecalho) {
    filtrosAtivos.set(coluna, selecionados);
    atualizarIconeFiltro(cabecalho, coluna);
    exibirPagina(1);
  }

  // data-filtro="data": só um período "De/Até" com dois <input type="date">
  // nativos (calendário do navegador). Sem lista de checkboxes, sem botão de
  // aplicar — a tabela reage assim que uma das duas datas muda ('change').
  // Só "De" preenchido = a partir dessa data; só "Até" = até essa data; os
  // dois = período fechado. As chaves são sempre "aaaa-mm-dd", então dá pra
  // comparar como string (ordem lexicográfica == ordem cronológica nesse formato).
  function montarPainelFiltroData(painel, cabecalho, coluna) {
    const filtroAtual = filtrosAtivos.get(coluna) || { de: '', ate: '' };
    filtrosAtivos.set(coluna, filtroAtual);

    function aplicarPeriodo() {
      filtroAtual.de = campoDe.input.value;
      filtroAtual.ate = campoAte.input.value;
      aplicarFiltro(coluna, filtroAtual, cabecalho);
    }

    function criarCampoData(rotuloTexto, valorInicial) {
      const label = document.createElement('label');
      label.className = 'tabela-filtro-painel__intervalo-campo';
      const rotulo = document.createElement('span');
      rotulo.textContent = rotuloTexto;
      const input = document.createElement('input');
      input.type = 'date';
      input.className = 'tabela-filtro-painel__input-data';
      input.value = valorInicial || '';
      input.addEventListener('change', aplicarPeriodo);
      label.append(rotulo, input);
      return { label, input };
    }

    const campoDe = criarCampoData('De', filtroAtual.de);
    const campoAte = criarCampoData('Até', filtroAtual.ate);

    const intervalo = document.createElement('div');
    intervalo.className = 'tabela-filtro-painel__intervalo';
    intervalo.append(campoDe.label, campoAte.label);

    const acoes = document.createElement('div');
    acoes.className = 'tabela-filtro-painel__acoes';
    const btnLimpar = document.createElement('button');
    btnLimpar.type = 'button';
    btnLimpar.className = 'tabela-filtro-painel__link';
    btnLimpar.textContent = 'Limpar';
    btnLimpar.addEventListener('click', () => {
      campoDe.input.value = '';
      campoAte.input.value = '';
      aplicarPeriodo();
    });
    acoes.appendChild(btnLimpar);

    painel.append(intervalo, acoes);
  }

  function montarPainelFiltroLista(painel, cabecalho, coluna) {
    const valores = valoresUnicosColuna(coluna);
    const selecionados = filtrosAtivos.get(coluna) || new Set();
    filtrosAtivos.set(coluna, selecionados);

    const busca = document.createElement('input');
    busca.type = 'text';
    busca.className = 'tabela-filtro-painel__busca';
    busca.placeholder = 'Buscar...';

    const acoes = document.createElement('div');
    acoes.className = 'tabela-filtro-painel__acoes';
    const btnTodos = document.createElement('button');
    btnTodos.type = 'button';
    btnTodos.className = 'tabela-filtro-painel__link';
    btnTodos.textContent = 'Selecionar todos';
    const btnLimpar = document.createElement('button');
    btnLimpar.type = 'button';
    btnLimpar.className = 'tabela-filtro-painel__link';
    btnLimpar.textContent = 'Limpar';
    acoes.append(btnTodos, btnLimpar);

    const lista = document.createElement('div');
    lista.className = 'tabela-filtro-painel__lista';

    function renderizarLista(termoBusca) {
      const termo = termoBusca.trim().toLowerCase();
      lista.innerHTML = '';

      valores
        .filter((valor) => valor.toLowerCase().includes(termo))
        .forEach((valor) => {
          const item = document.createElement('label');
          item.className = 'tabela-filtro-painel__item';

          const checkbox = document.createElement('input');
          checkbox.type = 'checkbox';
          checkbox.checked = selecionados.has(valor);
          checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
              selecionados.add(valor);
            } else {
              selecionados.delete(valor);
            }
            aplicarFiltro(coluna, selecionados, cabecalho);
          });

          const texto = document.createElement('span');
          texto.textContent = valor;

          item.append(checkbox, texto);
          lista.appendChild(item);
        });

      if (!lista.children.length) {
        const vazio = document.createElement('p');
        vazio.className = 'tabela-filtro-painel__vazio';
        vazio.textContent = 'Nenhum valor encontrado.';
        lista.appendChild(vazio);
      }
    }

    busca.addEventListener('input', () => renderizarLista(busca.value));

    btnTodos.addEventListener('click', () => {
      valores.forEach((valor) => selecionados.add(valor));
      aplicarFiltro(coluna, selecionados, cabecalho);
      renderizarLista(busca.value);
    });

    btnLimpar.addEventListener('click', () => {
      selecionados.clear();
      aplicarFiltro(coluna, selecionados, cabecalho);
      renderizarLista(busca.value);
    });

    renderizarLista('');

    painel.append(busca, acoes, lista);
  }

  function posicionarPainelFiltro(painel, botao) {
    const retangulo = botao.getBoundingClientRect();
    const larguraMaxima = window.innerWidth - 16;
    let esquerda = retangulo.left;
    if (esquerda + painel.offsetWidth > larguraMaxima) {
      esquerda = Math.max(8, retangulo.right - painel.offsetWidth);
    }
    painel.style.top = `${retangulo.bottom + 4}px`;
    painel.style.left = `${esquerda}px`;
  }

  function abrirPainelFiltro(cabecalho, coluna, botao) {
    const jaEstavaAbertoNestaColuna = painelFiltroAberto?.dataset.coluna === String(coluna);
    fecharPainelFiltro();
    if (jaEstavaAbertoNestaColuna) return;

    const painel = document.createElement('div');
    painel.className = 'tabela-filtro-painel';
    painel.dataset.coluna = String(coluna);

    if (cabecalho.dataset.filtro === 'data') {
      montarPainelFiltroData(painel, cabecalho, coluna);
    } else {
      montarPainelFiltroLista(painel, cabecalho, coluna);
    }

    document.body.appendChild(painel);
    posicionarPainelFiltro(painel, botao);
    painelFiltroAberto = painel;

    setTimeout(() => {
      document.addEventListener('click', aoClicarForaDoPainel, true);
      window.addEventListener('resize', fecharPainelFiltro);
      window.addEventListener('scroll', aoRolarForaDoPainel, true);
    }, 0);
  }

  cabecalhos.forEach((cabecalho) => {
    if (!TIPOS_FILTRO_PAINEL.has(cabecalho.dataset.filtro)) return;
    const coluna = parseInt(cabecalho.dataset.col);
    const botaoFiltro = cabecalho.querySelector('.filtro-icon');
    if (!botaoFiltro) return;
    botaoFiltro.addEventListener('click', (evento) => {
      evento.stopPropagation();
      abrirPainelFiltro(cabecalho, coluna, botaoFiltro);
    });
  });

  // inicializa na página 1
  renderizarSeletorItensPorPagina();
  atualizarIcones();
  exibirPagina(1);
})();
