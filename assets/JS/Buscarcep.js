

function buscarCep() {
    const inputCep = document.getElementById('cep');
    const inputEndereco = document.getElementById('endereco');
    const inputBairro = document.getElementById('bairro');
    const inputCidade = document.getElementById('cidade');
    const inputEstado = document.getElementById('estado');
    const inputPais = document.getElementById('pais');

    if (!inputCep) return;

    const cep = inputCep.value.replace(/\D/g, '');

    if (cep.length !== 8) {
        return;
    }

    limparMensagemErroCep();
    inputCep.classList.add('campo__input--carregando');

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(resposta => {
            if (!resposta.ok) {
                throw new Error('Falha na comunicação com o serviço de CEP.');
            }
            return resposta.json();
        })
        .then(dados => {
            if (dados.erro) {
                mostrarMensagemErroCep('CEP não encontrado.');
                limparCamposEndereco();
                return;
            }

            if (inputEndereco) inputEndereco.value = dados.logradouro || '';
            if (inputBairro) inputBairro.value = dados.bairro || '';
            if (inputCidade) inputCidade.value = dados.localidade || '';
            if (inputEstado) inputEstado.value = dados.uf || '';
            if (inputPais && !inputPais.value) inputPais.value = 'Brasil';

            const inputNumero = document.getElementsByName('numero')[0];
            if (inputNumero) inputNumero.focus();
        })
        .catch(() => {
            mostrarMensagemErroCep('Não foi possível buscar o CEP. Tente novamente.');
        })
        .finally(() => {
            inputCep.classList.remove('campo__input--carregando');
        });
}

function limparCamposEndereco() {
    const ids = ['endereco', 'bairro', 'cidade', 'estado'];
    ids.forEach(id => {
        const campo = document.getElementById(id);
        if (campo) campo.value = '';
    });
}

function mostrarMensagemErroCep(mensagem) {
    const inputCep = document.getElementById('cep');
    if (!inputCep) return;

    limparMensagemErroCep();

    const erro = document.createElement('span');
    erro.className = 'campo__mensagem-erro campo__mensagem-erro--cep';
    erro.textContent = mensagem;

    inputCep.insertAdjacentElement('afterend', erro);
}

function limparMensagemErroCep() {
    const erroExistente = document.querySelector('.campo__mensagem-erro--cep');
    if (erroExistente) erroExistente.remove();
}