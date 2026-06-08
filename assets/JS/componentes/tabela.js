(function () {
  const corpo = document.querySelector('table tbody');
  if (!corpo) return;

  const cabecalhos = document.querySelectorAll('table thead th[data-col]');
  let colunaOrdenada = null;
  let ordemAscendente = true;

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
    return texto.toLowerCase();
  }

  function ordenarLinhas(coluna) {
    if (colunaOrdenada === coluna) {
      ordemAscendente = !ordemAscendente;
    } else {
      colunaOrdenada = coluna;
      ordemAscendente = true;
    }

    const linhas = Array.from(corpo.querySelectorAll('tr'));

    linhas.sort((a, b) => {
      const valorA = lerValorCelula(a.cells[coluna], coluna);
      const valorB = lerValorCelula(b.cells[coluna], coluna);
      if (valorA < valorB) return ordemAscendente ? -1 : 1;
      if (valorA > valorB) return ordemAscendente ? 1 : -1;
      return 0;
    });

    linhas.forEach((linha) => corpo.appendChild(linha));
    atualizarIcones();
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
        icone.className = 'fa-solid fa-sort sort-icon';
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
})();
