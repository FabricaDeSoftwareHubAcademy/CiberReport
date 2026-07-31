

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
 
function mascararCVSS(campo) {
    var posicaoCursor = campo.selectionStart;
    var valorAntigo = campo.value;
    var digitosAntesCursor = contarDigitosAntes(valorAntigo, posicaoCursor);
 
    var valor = valorAntigo.replace(/[^0-9]/g, "");
 
    if (valor.substring(0, 2) === "10") {
        
        valor = valor.substring(0, 3);
        if (valor.length > 2) {
            valor = "10." + valor.substring(2);
        }
    } else {
        
        valor = valor.substring(0, 2);
        if (valor.length > 1) {
            valor = valor.substring(0, 1) + "." + valor.substring(1);
        }
    }
 
    campo.value = valor;
 
    var novaPosicao = posicaoAposNDigitos(valor, digitosAntesCursor);
    campo.setSelectionRange(novaPosicao, novaPosicao);
}
 
function mascararCVE(campo) {
    var posicaoCursor = campo.selectionStart;
    var valorAntigo = campo.value;
    var digitosAntesCursor = contarDigitosAntes(valorAntigo, posicaoCursor);
 
    var valor = valorAntigo.replace(/[^0-9]/g, "");
    valor = valor.substring(0, 11); 
 
    if (valor.length > 4) {
        valor = "CVE-" + valor.substring(0, 4) + "-" + valor.substring(4);
    } else if (valor.length > 0) {
        valor = "CVE-" + valor;
    }
 
    campo.value = valor;
 
    var novaPosicao = posicaoAposNDigitos(valor, digitosAntesCursor);
    campo.setSelectionRange(novaPosicao, novaPosicao);
}