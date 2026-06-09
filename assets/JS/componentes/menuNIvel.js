let passoAtual = 1;
let totalPassos = 4;
//  ✓

function Stepper() {
    for (let i = 1; i <= totalPassos; i++) {
        let passo = document.getElementById(`numero-${i}`);
        let separador = document.getElementById(`separador-${i}`);

        if (passo) {
            if (i === passoAtual) {
                passo.classList.add("estado");
            } if (i < passoAtual) {
                passo.classList.add("active");
            } else {
                passo.classList.remove("active");
            }
        }

        if (separador) {
            if (i < passoAtual) {
                separador.classList.add("active");
            } else {
                separador.classList.remove("active");
            }
        }
    }
}

let btn_voltar = document.getElementById("btn-voltar");
let btn_avanca = document.getElementById("btn-avancar");

btn_avanca.addEventListener("click", function() {
    if (passoAtual < totalPassos) {
        passoAtual++;
        Stepper();
      }
});
btn_voltar.addEventListener("click", function(e) {
    e.preventDefault();
    if (passoAtual > 1) {
        passoAtual--;
        Stepper();
    }
});