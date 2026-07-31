function buscarCep() {

    var campoCep = document.getElementById("cep");
    var cepDigitado = campoCep.value;

    cepDigitado = cepDigitado.replace(/\D/g, "");

    if (cepDigitado.length != 8) {
        return;
    }

    var url = "https://viacep.com.br/ws/" + cepDigitado + "/json/";

    fetch(url)
        .then(function (resposta) {
            return resposta.json();
        })
        .then(function (dados) {

            if (dados.erro) {
                alert("CEP não encontrado!");
                return;
            }

            document.getElementById("endereco").value = dados.logradouro;
            document.getElementById("bairro").value = dados.bairro;
            document.getElementById("cidade").value = dados.localidade;
            document.getElementById("estado").value = dados.uf;

            var campoPais = document.getElementById("pais");
            if (campoPais.value == "") {
                campoPais.value = "Brasil";
            }

        })
        .catch(function (erro) {
            alert("Erro ao buscar o CEP. Tente novamente.");
            console.log(erro);
        });
}