const input = document.querySelector('#busca');
const form = document.querySelector('.input-pesquisaSuperior');
const table = document.querySelector('#tabela');

if (input && form && table) {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
    });

    const corpo = table.querySelector('tbody');
    const linhasOriginais = Array.from(corpo.querySelectorAll('tr'));

    input.addEventListener('input', (e) => {
        const busca = e.target.value.toLowerCase().trim();

        if (busca === '') {
            window.tabelaPaginacao?.limparFiltro();
            return;
        }

        const filtradas = linhasOriginais.filter((linha) =>
            linha.textContent.toLowerCase().includes(busca)
        );

        window.tabelaPaginacao?.filtrar(filtradas);
    });
}