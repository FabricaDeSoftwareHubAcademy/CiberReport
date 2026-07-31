const input = document.querySelector('#busca');
const form = document.querySelector('.input-pesquisaSuperior');
const table = document.querySelector('#tabela');

if (input && form && table) {

    input.addEventListener('input', (evento) => {
        const busca = evento.target.value.trim().toLowerCase();

        const linhas = Array.from(table.querySelectorAll("tbody tr"));

        linhas.forEach((linha) => {
            const textoLinha = linha.textContent.toLowerCase();
            linha.style.display = textoLinha.includes(busca) ? "" : "none";
        });
    });

    form.addEventListener('submit', (evento) => {
        evento.preventDefault();
    });
}