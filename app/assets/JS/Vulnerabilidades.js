function mascararCVSS(campo) {
 
    
    let digitos = campo.value.replace(/\D/g, "");
 
    let parteInteira;
    let parteDecimal;
 
    if (digitos.startsWith("10")) {
        
        digitos = digitos.substring(0, 3);
        parteInteira = digitos.substring(0, 2);
        parteDecimal = digitos.substring(2, 3);
    } else {
        digitos = digitos.substring(0, 2);
        parteInteira = digitos.substring(0, 1);
        parteDecimal = digitos.substring(1, 2);
    }
 
    let valor = parteInteira;
 
    if (parteDecimal.length > 0) {
        valor += "." + parteDecimal;
    }
 
    campo.value = valor;
 
}
 

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

const TECLAS_NAVEGACAO = [
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
 
document.addEventListener("keydown", function (e) {
 
    
    if (e.target.id === "cvssScore") {
 
        if (TECLAS_NAVEGACAO.includes(e.key)) return;
        if (e.ctrlKey || e.metaKey) return;
        if (/^[0-9]$/.test(e.key)) return;
 
        
        e.preventDefault();
        return;
    }
 
    
    if (e.target.id === "cve") {
 
        const prefixo = "CVE-";
 
        if (TECLAS_NAVEGACAO.includes(e.key)) {
            if (
                (e.key === "Backspace" && e.target.selectionStart <= prefixo.length) ||
                (e.key === "Delete" && e.target.selectionStart < prefixo.length)
            ) {
                e.preventDefault();
            }
            return;
        }
 
        if (e.ctrlKey || e.metaKey) return;
 
        
        if (!/^[0-9]$/.test(e.key)) {
            e.preventDefault();
        }
        return;
    }
 
});
 
document.addEventListener("input", function (e) {
 
    
    if (e.target.id === "cvssScore") {
        mascararCVSS(e.target);
        return;
    }
 
    
    if (e.target.id === "cve") {
        mascararCVE(e.target);
        return;
    }
 
});
 
document.addEventListener("paste", function (e) {
 
    if (e.target.id === "cvssScore") {
        setTimeout(function () {
            mascararCVSS(e.target);
        }, 0);
        return;
    }
 
    if (e.target.id === "cve") {
        setTimeout(function () {
            mascararCVE(e.target);
        }, 0);
        return;
    }
 
});
 
document.addEventListener("focus", function (e) {
 
    if (e.target.id === "cve") {
        const prefixo = "CVE-";
 
        if (e.target.value.trim() === "") {
            e.target.value = prefixo;
        }
 
        e.target.setSelectionRange(e.target.value.length, e.target.value.length);
    }
 
}, true); 
 
document.addEventListener("click", function (e) {
 
    if (e.target.id === "cve") {
        const prefixo = "CVE-";
 
        if (e.target.selectionStart < prefixo.length) {
            e.target.setSelectionRange(prefixo.length, prefixo.length);
        }
    }
 
});