// Filtro de coluna e busca global da tabela compartilhada.
// Depende de componentes/tabela.js (window.TabelaCore) já ter rodado — inclua
// este script DEPOIS dele. Se tabela.js não encontrou uma tabela na página
// (TabelaCore ausente), este arquivo simplesmente não faz nada.
(function () {
  const core = window.TabelaCore;
  if (!core) return;

  const {
    cabecalhos,
    todasLinhas,
    colunaDoCabecalho,
    colunasComDataset,
    removerAcentos,
    analisarDataBR,
    chaveDataISO,
    recarregar,
    definirFiltroDeLinha,
  } = core;

  let termoBuscaGlobal = '';

  // ================================================================
  // Filtro de coluna (seleção de valores)
  // ================================================================
  // Colunas com data-filtro="lista" ganham um painel de checkboxes com os
  // valores únicos da coluna. Cada <td> pode declarar data-valores='["a","b"]'
  // com a lista completa a ser filtrada (útil quando a célula exibe só um
  // resumo, ex.: tags truncadas); na ausência do atributo, usa-se o texto da
  // célula. Colunas data-filtro="data" ganham um painel diferente — só um
  // período "De/Até" (ver montarPainelFiltroData), sem lista de checkboxes.
  const colunasFiltroData = colunasComDataset('filtro', 'data');
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

  function normalizarTextoBusca(texto) {
    return removerAcentos(texto).toLowerCase().trim();
  }

  function linhaPassaFiltros(linha) {
    if (termoBuscaGlobal && !normalizarTextoBusca(linha.textContent).includes(termoBuscaGlobal)) {
      return false;
    }

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

  // ================================================================
  // Busca global (campo de pesquisa no topo / overlay mobile)
  // ================================================================
  function inicializarBuscaGlobal() {
    const formularios = Array.from(document.querySelectorAll(
      '.input-pesquisaSuperior, .overlay-pesquisaMobile'
    ));
    const campos = formularios
      .map((formulario) => formulario.querySelector('input[type="text"]'))
      .filter(Boolean);

    if (!campos.length) return;

    formularios.forEach((formulario) => {
      formulario.addEventListener('submit', (evento) => evento.preventDefault());
    });

    campos.forEach((campo) => {
      campo.addEventListener('input', () => {
        termoBuscaGlobal = normalizarTextoBusca(campo.value);

        campos.forEach((outroCampo) => {
          if (outroCampo !== campo) outroCampo.value = campo.value;
        });

        recarregar();
      });
    });
  }

  // ================================================================
  // Painel de filtro (popover)
  // ================================================================

  // 'scroll' não borbulha, mas é capturado na fase de captura do window mesmo
  // assim — inclusive quando a rolagem acontece dentro da própria lista de
  // opções (que tem overflow-y). O contains() evita fechar o painel nesse caso.
  function fecharPainelSeForaDele(evento) {
    if (painelFiltroAberto && !painelFiltroAberto.contains(evento.target)) {
      fecharPainelFiltro();
    }
  }

  function fecharPainelFiltro() {
    if (!painelFiltroAberto) return;
    painelFiltroAberto.remove();
    painelFiltroAberto = null;
    document.removeEventListener('click', fecharPainelSeForaDele, true);
    window.removeEventListener('resize', fecharPainelFiltro);
    window.removeEventListener('scroll', fecharPainelSeForaDele, true);
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
    recarregar();
  }

  // Link de ação dentro de um painel de filtro ("Limpar", "Selecionar todos").
  function criarBotaoLinkPainel(texto, aoClicar) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'tabela-filtro-painel__link';
    btn.textContent = texto;
    btn.addEventListener('click', aoClicar);
    return btn;
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
    acoes.appendChild(criarBotaoLinkPainel('Limpar', () => {
      campoDe.input.value = '';
      campoAte.input.value = '';
      aplicarPeriodo();
    }));

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

    const acoes = document.createElement('div');
    acoes.className = 'tabela-filtro-painel__acoes';
    acoes.append(
      criarBotaoLinkPainel('Selecionar todos', () => {
        valores.forEach((valor) => selecionados.add(valor));
        aplicarFiltro(coluna, selecionados, cabecalho);
        renderizarLista(busca.value);
      }),
      criarBotaoLinkPainel('Limpar', () => {
        selecionados.clear();
        aplicarFiltro(coluna, selecionados, cabecalho);
        renderizarLista(busca.value);
      })
    );

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
      document.addEventListener('click', fecharPainelSeForaDele, true);
      window.addEventListener('resize', fecharPainelFiltro);
      window.addEventListener('scroll', fecharPainelSeForaDele, true);
    }, 0);
  }

  cabecalhos.forEach((cabecalho) => {
    if (!TIPOS_FILTRO_PAINEL.has(cabecalho.dataset.filtro)) return;
    const coluna = colunaDoCabecalho(cabecalho);
    const botaoFiltro = cabecalho.querySelector('.filtro-icon');
    if (!botaoFiltro) return;
    botaoFiltro.addEventListener('click', (evento) => {
      evento.stopPropagation();
      abrirPainelFiltro(cabecalho, coluna, botaoFiltro);
    });
  });

  // ================================================================
  // Inicialização
  // ================================================================
  definirFiltroDeLinha(linhaPassaFiltros);
  inicializarBuscaGlobal();
  recarregar();
})();
