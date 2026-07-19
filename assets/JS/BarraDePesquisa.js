const input = document.querySelector('#busca');
const form = document.querySelector('.input-pesquisaSuperior');
const table = document.querySelector('#tabela');
const LINHAS_POR_PAGINA = 10;

if (input && form && table) {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
    });

    const corpo = table.querySelector('tbody');
    const linhasOriginais = Array.from(corpo.querySelectorAll('tr'));
    const containerPaginacao = table.querySelector('tfoot .paginacao');

    input.addEventListener('input', (e) => {
        const busca = e.target.value.toLowerCase().trim();

        if (busca === '') {
            if (containerPaginacao) containerPaginacao.style.visibility = 'visible';
            const btnPagina1 = containerPaginacao ? containerPaginacao.querySelector('.pag-num') : null;
            if (btnPagina1) {
                btnPagina1.click(); 
            } else {
                linhasOriginais.forEach((linha, i) => {
                    linha.style.display = i < LINHAS_POR_PAGINA ? '' : 'none';
                });
            }
            return;
        }

        const filtradas = linhasOriginais.filter((linha) =>
            linha.textContent.toLowerCase().includes(busca)
        );
       
        linhasOriginais.forEach((linha) => (linha.style.display = 'none'));
        filtradas.slice(0, LINHAS_POR_PAGINA).forEach((linha) => (linha.style.display = ''));

        if (containerPaginacao) containerPaginacao.style.visibility = 'hidden';
    });
}