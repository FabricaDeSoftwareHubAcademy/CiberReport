const modal = document.querySelector("#modal");
const btnAbrir = document.querySelector("#button");
const btnSalvar = document.querySelector("#btnSalvar");
const btnCancelar = document.querySelector("#btnCancelar");


btnAbrir.onclick = function() {
    modal.showModal();
}


function fecharModal() {
    modal.close();
}


btnSalvar.onclick = function(event) {
    event.preventDefault();
    console.log("Dados salvos com sucesso!");
    
    fecharModal();
}

btnCancelar.onclick = function() {
    fecharModal();
}
