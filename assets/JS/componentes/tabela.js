(function () {
  const corpo = document.querySelector('table tbody');
  if (!corpo) return;

  const cabecalhos = document.querySelectorAll('table thead th[data-col]');
  let colunaOrdenada = null;
  let ordemAscendente = true;

  // --- paginação ---
  const LINHAS_POR_PAGINA = 10;
  let paginaAtual = 1;
  const tabela = corpo.closest('table');
  const containerPaginacao = tabela ? tabela.querySelector('tfoot .paginacao') : null;

  function todasLinhas() {
    return Array.from(corpo.querySelectorAll('tr'));
  }

  function totalPaginas() {
    return Math.max(1, Math.ceil(todasLinhas().length / LINHAS_POR_PAGINA));
  }

  function exibirPagina(pagina) {
    const linhas = todasLinhas();
    const inicio = (pagina - 1) * LINHAS_POR_PAGINA;
    const fim = inicio + LINHAS_POR_PAGINA;
    linhas.forEach((linha, i) => {
      linha.style.display = (i >= inicio && i < fim) ? '' : 'none';
    });
    paginaAtual = pagina;
    renderizarBotoes();
    aplicarTooltipsOverflow();
  }

  // --- tooltip para conteúdo truncado ---
  function aplicarTooltipsOverflow() {
    const celulas = [
      ...cabecalhos,
      ...todasLinhas()
        .filter((linha) => linha.style.display !== 'none')
        .flatMap((linha) => Array.from(linha.cells)),
    ];
    celulas.forEach((celula) => {
      if (celula.scrollWidth > celula.clientWidth) {
        celula.title = celula.textContent.trim();
      } else {
        celula.removeAttribute('title');
      }
    });
  }

  function renderizarBotoes() {
    if (!containerPaginacao) return;
    const total = totalPaginas();
    containerPaginacao.innerHTML = '';

    const btnAnterior = document.createElement('button');
    btnAnterior.className = 'pag-btn';
    btnAnterior.setAttribute('aria-label', 'Página anterior');
    btnAnterior.innerHTML = '<i class="fa-solid fa-arrow-left"></i>';
    btnAnterior.disabled = paginaAtual === 1;
    btnAnterior.addEventListener('click', () => exibirPagina(paginaAtual - 1));
    containerPaginacao.appendChild(btnAnterior);

    for (let p = 1; p <= total; p++) {
      const btn = document.createElement('button');
      btn.className = 'pag-num' + (p === paginaAtual ? ' ativo' : '');
      btn.setAttribute('aria-label', `Página ${p}`);
      if (p === paginaAtual) btn.setAttribute('aria-current', 'page');
      btn.textContent = p;
      btn.addEventListener('click', () => exibirPagina(p));
      containerPaginacao.appendChild(btn);
    }

    const btnProximo = document.createElement('button');
    btnProximo.className = 'pag-btn';
    btnProximo.setAttribute('aria-label', 'Próxima página');
    btnProximo.innerHTML = '<i class="fa-solid fa-arrow-right"></i>';
    btnProximo.disabled = paginaAtual === total;
    btnProximo.addEventListener('click', () => exibirPagina(paginaAtual + 1));
    containerPaginacao.appendChild(btnProximo);
  }

  // --- ordenação ---
  const colunasData = new Set(
    Array.from(cabecalhos)
      .filter((cabecalho) => cabecalho.dataset.tipo === 'data')
      .map((cabecalho) => parseInt(cabecalho.dataset.col))
  );

  function lerValorCelula(celula, coluna) {
    const texto = celula.textContent.trim();
    if (colunasData.has(coluna)) {
      const [parteData, parteHora = '00:00'] = texto.split(' ');
      const [d, m, a] = parteData.split('/');
      return new Date(`${a}-${m}-${d}T${parteHora}`).getTime();
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

  cabecalhos.forEach((cabecalho) => {
    const coluna = parseInt(cabecalho.dataset.col);
    const rotulo = cabecalho.querySelector('.th-label');
    if (rotulo) {
      rotulo.addEventListener('click', () => ordenarLinhas(coluna));
    }
  });

  // inicializa na página 1
  atualizarIcones();
  exibirPagina(1);
})();
