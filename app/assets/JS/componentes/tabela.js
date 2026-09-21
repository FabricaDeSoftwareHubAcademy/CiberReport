// Componente de tabela: paginação, ordenação e tooltip para células com
// conteúdo cortado. Ativa-se sozinho se existir `.tabela-wrapper table tbody`
// na página.
//
// Filtro de coluna e busca global ficam em componentes/filtros-tabela.js —
// um script separado e opcional, carregado DEPOIS deste. A ponte entre os
// dois é `window.TabelaCore` (ver o fim deste arquivo): se filtros-tabela.js
// não estiver presente na página, a tabela funciona normalmente, só sem
// filtro/busca.
(function () {
  const corpo = document.querySelector('.tabela-wrapper table tbody');
  if (!corpo) return;

  const cabecalhos = document.querySelectorAll('.tabela-wrapper table thead th[data-col]');
  let colunaOrdenada = null;
  let ordemAscendente = true;

  function colunaDoCabecalho(cabecalho) {
    return parseInt(cabecalho.dataset.col, 10);
  }

  // Conjunto de índices de coluna cujo <th> tem dataset[atributo] === valor.
  function colunasComDataset(atributo, valor) {
    return new Set(
      Array.from(cabecalhos)
        .filter((cabecalho) => cabecalho.dataset[atributo] === valor)
        .map(colunaDoCabecalho)
    );
  }

  // ================================================================
  // Paginação — cookies e containers do rodapé
  // ================================================================
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
    return Array.from(corpo.querySelectorAll('tr:not(.tabela-linha-vazia)'));
  }

  // filtros-tabela.js registra aqui o predicado de filtro/busca via
  // TabelaCore.definirFiltroDeLinha(). Sem ele, todas as linhas "passam".
  let filtroDeLinha = null;

  function linhasFiltradas() {
    return filtroDeLinha ? todasLinhas().filter(filtroDeLinha) : todasLinhas();
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
    ajustarEspacamento();
  }

  // ================================================================
  // Espaçamento em branco (linha espaçadora no fim da tabela)
  // ================================================================
  let linhaEspacadora = null;

  function obterLinhaEspacadora() {
    if (!linhaEspacadora) {
      linhaEspacadora = document.createElement('tr');
      linhaEspacadora.className = 'tabela-linha-vazia';
      linhaEspacadora.setAttribute('aria-hidden', 'true');
      const celula = document.createElement('td');
      celula.innerHTML = '&nbsp;';
      linhaEspacadora.appendChild(celula);
    }
    return linhaEspacadora;
  }

  function containerComScroll() {
    if (['auto', 'scroll'].includes(window.getComputedStyle(corpo).overflowY)) {
      return corpo;
    }
    return tabela.closest('.tabela-wrapper');
  }

  function alturaConteudoAtual(container) {
    if (container === corpo) return corpo.scrollHeight;
    return tabela.getBoundingClientRect().height;
  }

  function ajustarEspacamento() {
    if (!tabela) return;
    const container = containerComScroll();
    if (!container) return;

    const espacadora = obterLinhaEspacadora();
    espacadora.remove();

    const alturaContainer = container === corpo ? corpo.clientHeight : container.getBoundingClientRect().height;
    const diferenca = Math.floor(alturaContainer - alturaConteudoAtual(container)) - 1;

    espacadora.querySelector('td').colSpan = tabela.querySelectorAll('thead th').length || 1;
    espacadora.style.height = diferenca > 0 ? `${diferenca}px` : '0px';
    corpo.appendChild(espacadora);
  }

  let redimensionamentoTimeout = null;
  window.addEventListener('resize', () => {
    clearTimeout(redimensionamentoTimeout);
    redimensionamentoTimeout = setTimeout(ajustarEspacamento, 150);
  });

  // ================================================================
  // Seletor de "linhas por página"
  // ================================================================
  function renderizarSeletorItensPorPagina() {
    if (!containerItensPorPagina) return;
    containerItensPorPagina.innerHTML = '';

    const label = document.createElement('label');
    label.className = 'itens-por-pagina__label';
    label.textContent = 'Linhas por página:';
    label.setAttribute('for', 'itens-por-pagina-select');

    const seletorWrapper = document.createElement('div');
    seletorWrapper.className = 'itens-por-pagina__select-wrapper';

    const select = document.createElement('select');
    select.className = 'itens-por-pagina__select';
    select.id = 'itens-por-pagina-select';

    const seta = document.createElement('i');
    seta.className = 'fa-solid fa-chevron-down itens-por-pagina__seta';
    seta.setAttribute('aria-hidden', 'true');

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

    seletorWrapper.appendChild(select);
    seletorWrapper.appendChild(seta);

    containerItensPorPagina.appendChild(label);
    containerItensPorPagina.appendChild(seletorWrapper);
    containerItensPorPagina.appendChild(inputCustom);
  }

  // ================================================================
  // Tooltip rápido para células com conteúdo cortado
  // ================================================================
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

  // ================================================================
  // Botões de paginação
  // ================================================================
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

  // Botão de seta "anterior"/"próximo" da paginação.
  function criarBotaoSeta(rotulo, iconeClasse, desabilitado, aoClicar) {
    const btn = document.createElement('button');
    btn.className = 'pag-btn';
    btn.setAttribute('aria-label', rotulo);
    btn.innerHTML = `<i class="fa-solid ${iconeClasse}"></i>`;
    btn.disabled = desabilitado;
    btn.addEventListener('click', aoClicar);
    return btn;
  }

  function renderizarBotoes() {
    if (!containerPaginacao) return;
    const total = totalPaginas();
    containerPaginacao.innerHTML = '';
    renderizarIrPara(total);

    if (total > 1) {
      containerPaginacao.appendChild(
        criarBotaoSeta('Página anterior', 'fa-arrow-left', paginaAtual === 1, () => exibirPagina(paginaAtual - 1))
      );
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
      containerPaginacao.appendChild(
        criarBotaoSeta('Próxima página', 'fa-arrow-right', paginaAtual === total, () => exibirPagina(paginaAtual + 1))
      );
    }
  }

  // ================================================================
  // Ordenação
  // ================================================================
  // data-filtro="alfabetica"|"numero" no <th> força o tipo de comparação usado
  // ao ordenar a coluna (em vez da auto-detecção texto/número abaixo).
  const colunasAlfabetica = colunasComDataset('filtro', 'alfabetica');
  const colunasNumero = colunasComDataset('filtro', 'numero');
  // data-filtro="data" no <th> soma ordenação cronológica ao filtro de lista
  // (ver componentes/filtros-tabela.js) — por isso também entra aqui.
  const colunasFiltroData = colunasComDataset('filtro', 'data');
  const colunasData = colunasComDataset('tipo', 'data');
  const colunasRisco = colunasComDataset('tipo', 'risco');

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

  // Chave normalizada "aaaa-mm-dd" (mesmo formato do valor de <input type="date">),
  // usada pelo filtro de período em filtros-tabela.js para comparar independente
  // de como o texto original está formatado na célula. Usa getters locais (não
  // toISOString) para não deslocar o dia por causa de fuso horário.
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
      const coluna = colunaDoCabecalho(cabecalho);
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
  // no ícone de funil (em filtros-tabela.js) chama evento.stopPropagation(),
  // então só o clique no texto/sort-icon chega até aqui.
  cabecalhos.forEach((cabecalho) => {
    const coluna = colunaDoCabecalho(cabecalho);
    const rotulo = cabecalho.querySelector('.th-label');
    if (rotulo) {
      rotulo.addEventListener('click', () => ordenarLinhas(coluna));
    }
  });

  // ================================================================
  // Ponte com componentes/filtros-tabela.js (opcional)
  // ================================================================
  window.TabelaCore = {
    tabela,
    cabecalhos,
    todasLinhas,
    colunaDoCabecalho,
    colunasComDataset,
    removerAcentos,
    analisarDataBR,
    chaveDataISO,
    recarregar: () => exibirPagina(1),
    definirFiltroDeLinha: (fn) => { filtroDeLinha = fn; },
  };

  // inicializa na página 1
  renderizarSeletorItensPorPagina();
  atualizarIcones();
  exibirPagina(1);
})();
