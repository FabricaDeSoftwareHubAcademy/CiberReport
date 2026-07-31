function ehDigito(caractere) {
    return caractere >= "0" && caractere <= "9";
}

function contarDigitosAntes(texto, posicao) {
    var contador = 0;
    var i;
    for (i = 0; i < posicao; i++) {
        if (ehDigito(texto[i])) {
            contador++;
        }
    }
    return contador;
}

function posicaoAposNDigitos(texto, quantidadeDigitos) {
    if (quantidadeDigitos === 0) {
        var j = 0;
        while (j < texto.length && !ehDigito(texto[j])) {
            j++;
        }
        return j;
    }

    var contador = 0;
    var i;
    for (i = 0; i < texto.length; i++) {
        if (ehDigito(texto[i])) {
            contador++;
            if (contador === quantidadeDigitos) {
                return i + 1;
            }
        }
    }
    return texto.length;
}

function apenasNumeros(campo) {
    var posicaoCursor = campo.selectionStart;
    var valorAntigo = campo.value;
    var digitosAntesCursor = contarDigitosAntes(valorAntigo, posicaoCursor);

    var valor = valorAntigo.replace(/[^0-9]/g, "");
    campo.value = valor;

    var novaPosicao = posicaoAposNDigitos(valor, digitosAntesCursor);
    campo.setSelectionRange(novaPosicao, novaPosicao);
}

function mascararTelefone(campo) {
    var posicaoCursor = campo.selectionStart;
    var valorAntigo = campo.value;
    var digitosAntesCursor = contarDigitosAntes(valorAntigo, posicaoCursor);

    var valor = valorAntigo.replace(/[^0-9]/g, "");
    valor = valor.substring(0, 11);

    if (valor.length > 10) {
        valor = "(" + valor.substring(0, 2) + ") " + valor.substring(2, 7) + "-" + valor.substring(7);
    } else if (valor.length > 6) {
        valor = "(" + valor.substring(0, 2) + ") " + valor.substring(2, 6) + "-" + valor.substring(6);
    } else if (valor.length > 2) {
        valor = "(" + valor.substring(0, 2) + ") " + valor.substring(2);
    } else if (valor.length > 0) {
        valor = "(" + valor;
    }

    campo.value = valor;

    var novaPosicao = posicaoAposNDigitos(valor, digitosAntesCursor);
    campo.setSelectionRange(novaPosicao, novaPosicao);
}

function mascararCEP(campo) {
    var posicaoCursor = campo.selectionStart;
    var valorAntigo = campo.value;
    var digitosAntesCursor = contarDigitosAntes(valorAntigo, posicaoCursor);

    var valor = valorAntigo.replace(/[^0-9]/g, "");
    valor = valor.substring(0, 8);

    if (valor.length > 5) {
        valor = valor.substring(0, 5) + "-" + valor.substring(5);
    }

    campo.value = valor;

    var novaPosicao = posicaoAposNDigitos(valor, digitosAntesCursor);
    campo.setSelectionRange(novaPosicao, novaPosicao);
}

function mascararCPF(campo) {
    var posicaoCursor = campo.selectionStart;
    var valorAntigo = campo.value;
    var digitosAntesCursor = contarDigitosAntes(valorAntigo, posicaoCursor);

    var valor = valorAntigo.replace(/[^0-9]/g, "");
    valor = valor.substring(0, 11);

    if (valor.length > 9) {
        valor = valor.substring(0, 3) + "." + valor.substring(3, 6) + "." + valor.substring(6, 9) + "-" + valor.substring(9);
    } else if (valor.length > 6) {
        valor = valor.substring(0, 3) + "." + valor.substring(3, 6) + "." + valor.substring(6);
    } else if (valor.length > 3) {
        valor = valor.substring(0, 3) + "." + valor.substring(3);
    }

    campo.value = valor;

    var novaPosicao = posicaoAposNDigitos(valor, digitosAntesCursor);
    campo.setSelectionRange(novaPosicao, novaPosicao);
}

function mascararCNPJ(campo) {
    var posicaoCursor = campo.selectionStart;
    var valorAntigo = campo.value;
    var digitosAntesCursor = contarDigitosAntes(valorAntigo, posicaoCursor);

    var valor = valorAntigo.replace(/[^0-9]/g, "");
    valor = valor.substring(0, 14);

    if (valor.length > 12) {
        valor = valor.substring(0, 2) + "." + valor.substring(2, 5) + "." + valor.substring(5, 8) + "/" + valor.substring(8, 12) + "-" + valor.substring(12);
    } else if (valor.length > 8) {
        valor = valor.substring(0, 2) + "." + valor.substring(2, 5) + "." + valor.substring(5, 8) + "/" + valor.substring(8);
    } else if (valor.length > 5) {
        valor = valor.substring(0, 2) + "." + valor.substring(2, 5) + "." + valor.substring(5);
    } else if (valor.length > 2) {
        valor = valor.substring(0, 2) + "." + valor.substring(2);
    }

    campo.value = valor;

    var novaPosicao = posicaoAposNDigitos(valor, digitosAntesCursor);
    campo.setSelectionRange(novaPosicao, novaPosicao);
}