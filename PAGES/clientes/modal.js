function buscarCep() {

    let cep = document.getElementById("cep").value;

    fetch("https://viacep.com.br/ws/" + cep + "/json/")
    
    .then(function(resposta) {
        return resposta.json();
    })

    .then(function(dados) {

        document.getElementById("endereco").value = dados.logradouro;

        document.getElementById("bairro").value = dados.bairro;

        document.getElementById("cidade").value = dados.localidade;

        document.getElementById("estado").value = dados.uf;

    });

}
 

