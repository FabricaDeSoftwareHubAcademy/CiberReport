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

const CAMPOS_MODAL_VULN = {
    projeto_id: "projetoId",
    nome: "nomeVuln",
    cvss: "cvssScore",
    cve: "cve",
    descricao: "descricao",
    descricao_tecnica: "descTecnica",
    impacto_negocio: "impactos",
    severidade_vulnerabilidade: "severidade",
    categoria: "categoria",
    responsavel: "responsavel",
    status: "status"
};

function abrirModalVulnerabilidade(vuln) {
    const modal = document.getElementById("modalVulnerabilidade");
    if (!modal) return;

    const editando = !!vuln;

    document.getElementById("tituloModalVuln").textContent =
        editando ? "Editar Vulnerabilidade" : "Nova Vulnerabilidade";
    document.getElementById("vulnId").value = editando ? vuln.id : "";
    document.getElementById("projetoId").disabled = editando;

    for (const [coluna, idCampo] of Object.entries(CAMPOS_MODAL_VULN)) {
        const campo = document.getElementById(idCampo);
        if (campo) campo.value = editando ? (vuln[coluna] ?? "") : "";
    }

    modal.classList.add("active");
}

document.addEventListener("click", function (e) {

    const btnEditar = e.target.closest(".tabela-btn-editar");
    if (btnEditar) {
        abrirModalVulnerabilidade(JSON.parse(btnEditar.dataset.vuln));
        return;
    }

    if (e.target.closest(".btn-novo-cadastro")) {
        abrirModalVulnerabilidade(null);
    }

});


document.getElementById("btnSalvar").addEventListener("click", async function () {

    const modal = document.getElementById("modalVulnerabilidade");
    const campos = modal.querySelectorAll("input[required], select[required], textarea[required]");

    for (const campo of campos) {
        if (!campo.disabled && !campo.checkValidity()) {
            campo.reportValidity();
            return;
        }
    }

    const valor = function (id) {
        return document.getElementById(id).value.trim();
    };

    const cve = valor("cve");
    const dados = new FormData();
    dados.append("id", valor("vulnId"));
    dados.append("projeto_id", valor("projetoId"));
    dados.append("nome", valor("nomeVuln"));
    dados.append("cvss", valor("cvssScore"));
    dados.append("cve", cve === "CVE-" ? "" : cve);
    dados.append("descricao", valor("descricao"));
    dados.append("descricao_tecnica", valor("descTecnica"));
    dados.append("impacto_negocio", valor("impactos"));
    dados.append("severidade_vulnerabilidade", valor("severidade"));
    dados.append("categoria", valor("categoria"));
    dados.append("responsavel", valor("responsavel"));
    dados.append("status", valor("status"));
    dados.append("habilitado", "1");

    try {
        const resposta = await fetch(`${window.baseUrl}vulnerabilidades/salvar`, {
            method: "POST",
            body: dados
        });
        const resultado = await resposta.json();

        if (resultado.status === 200) {
            window.location.reload();
        } else {
            alert(resultado.msg);
        }
    } catch (erro) {
        alert("Erro ao salvar a vulnerabilidade.");
    }

});
