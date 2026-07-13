const input = document.querySelector('#busca');
const form = document.querySelector('.input-pesquisaSuperior');
const table = document.querySelector('#tabela');

// só ativa a busca se todos os elementos existirem nessa página
if (input && form && table) {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
    });

    input.addEventListener('input', (e) => {
        const busca = e.target.value.toLowerCase().trim();
        const linhas = table.querySelectorAll('tbody tr');

        linhas.forEach((linha) => {
            const textoLinha = linha.textContent.toLowerCase();
            linha.style.display = textoLinha.includes(busca) ? '' : 'none';
        });
    });
}