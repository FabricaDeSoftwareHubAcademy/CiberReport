let passoAtual = 1;
let totalPassos = 3;

function Stepper() {
    for (let i = 1; i <= totalPassos; i++) {
        const circulo = document.getElementById('numero-' + i);
        const separador = document.getElementById('separador-' + i);

        if (i < passoAtual) {
            circulo.classList.add('active');
            circulo.querySelector('span').textContent = '✓';
            if (separador) separador.classList.add('active');

        } else if (i === passoAtual) {
            circulo.classList.add('active');
            circulo.querySelector('span').textContent = i;

        } else {
            circulo.classList.remove('active');
            circulo.querySelector('span').textContent = i;
            if (separador) separador.classList.remove('active');
        }
    }
}

const btn_voltar = document.getElementById('btn-voltar');
const btn_avancar = document.getElementById('btn-avancar');

btn_voltar.addEventListener('click', (evento) => {
    evento.preventDefault();
    if (passoAtual === 1) {
        return;
    } else {
        passoAtual--;
        Stepper();
    }
});

btn_avancar.addEventListener('click', (evento) => {
    evento.preventDefault();
    if (passoAtual < totalPassos) {
        passoAtual++;
        Stepper();
    }
});

Stepper();