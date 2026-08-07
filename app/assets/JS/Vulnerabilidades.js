// ==============================
// CVSS
// ==============================

function sanitizarCVSS(valor) {
    // Permite apenas números e ponto
    valor = valor.replace(/[^\d.]/g, "");

    // Apenas um ponto
    const partes = valor.split(".");
    if (partes.length > 2) {
        valor = partes[0] + "." + partes.slice(1).join("");
    }

    // Máximo uma casa decimal
    if (valor.includes(".")) {
        let [inteiro, decimal] = valor.split(".");
        decimal = decimal.substring(0, 1);
        valor = inteiro + "." + decimal;
    }

    return valor;
}

function bloquearNaoNumericoCVSS(campo) {

    campo.addEventListener("keydown", function (e) {

        const teclasPermitidas = [
            "Backspace",
            "Delete",
            "Tab",
            "ArrowLeft",
            "ArrowRight",
            "ArrowUp",
            "ArrowDown",
            "Home",
            "End"
        ];

        if (teclasPermitidas.includes(e.key)) return;

        if (e.ctrlKey || e.metaKey) return;

        if (/^[0-9]$/.test(e.key)) return;

        if (e.key === "." && !campo.value.includes(".")) return;

        e.preventDefault();

    });

    campo.addEventListener("input", function () {

        let valor = sanitizarCVSS(campo.value);

        if (valor !== "") {

            let numero = parseFloat(valor);

            if (!isNaN(numero)) {

                if (numero > 10) {
                    valor = "10";
                }

                if (numero < 0) {
                    valor = "0";
                }

            }

        }

        campo.value = valor;

    });

    campo.addEventListener("paste", function () {

        setTimeout(function () {

            campo.value = sanitizarCVSS(campo.value);

        }, 0);

    });

}


// ==============================
// CVE
// ==============================

function mascararCVE(campo) {

    const prefixo = "CVE-";

    let numeros = campo.value
        .replace(prefixo, "")
        .replace(/\D/g, "")
        .substring(0, 12);

    let ano = numeros.substring(0, 4);
    let codigo = numeros.substring(4);

    let valor = prefixo;

    if (ano.length > 0) {
        valor += ano;
    }

    if (codigo.length > 0) {
        valor += "-" + codigo;
    }

    campo.value = valor;

}

function protegerPrefixoCVE(campo) {

    const prefixo = "CVE-";

    campo.addEventListener("focus", function () {

        if (campo.value.trim() === "") {
            campo.value = prefixo;
        }

        campo.setSelectionRange(campo.value.length, campo.value.length);

    });

    campo.addEventListener("click", function () {

        if (campo.selectionStart < prefixo.length) {

            campo.setSelectionRange(prefixo.length, prefixo.length);

        }

    });

    campo.addEventListener("keydown", function (e) {

        const teclasPermitidas = [
            "Backspace",
            "Delete",
            "Tab",
            "ArrowLeft",
            "ArrowRight",
            "ArrowUp",
            "ArrowDown",
            "Home",
            "End"
        ];

        if (teclasPermitidas.includes(e.key)) {

            if (
                (e.key === "Backspace" && campo.selectionStart <= prefixo.length) ||
                (e.key === "Delete" && campo.selectionStart < prefixo.length)
            ) {
                e.preventDefault();
            }

            return;
        }

        if (e.ctrlKey || e.metaKey) return;

        // Apenas números
        if (!/^[0-9]$/.test(e.key)) {
            e.preventDefault();
        }

    });

    campo.addEventListener("input", function () {

        mascararCVE(campo);

    });

    campo.addEventListener("paste", function () {

        setTimeout(function () {

            mascararCVE(campo);

        }, 0);

    });

}


// ==============================
// Inicialização
// ==============================

document.addEventListener("DOMContentLoaded", function () {

    const campoCvss = document.getElementById("cvssScore");

    if (campoCvss) {
        bloquearNaoNumericoCVSS(campoCvss);
    }

    const campoCve = document.getElementById("cve");

    if (campoCve) {
        protegerPrefixoCVE(campoCve);
    }

});