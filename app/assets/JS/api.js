/*
    Cliente HTTP genérico do projeto (padrão DAO/API — ver docs/PADRAO-DAO-API.md).
    Qualquer tela pode usar Api.get()/Api.post() para falar com as rotas
    /recurso/acao da API, sem duplicar fetch em cada arquivo JS.

    Contrato de resposta esperado do backend: { status, data?, msg? }.
    Api.get()/Api.post() sempre devolvem esse objeto (mesmo em erro HTTP),
    lendo o corpo antes de decidir sucesso/falha. O chamador decide o que
    fazer olhando resultado.status (200 = sucesso; !== 200 = erro/msg).

    Uso:
        const resultado = await Api.get('tipo-pentest/listar');
        const resultado = await Api.get('tipo-pentest/buscar', { id });
        const resultado = await Api.post('tipo-pentest/cadastro', { nome, categoria_id, frameworks_ids: [1, 2] });
*/
(() => {
    function baseUrl() {
        return window.baseUrl || window.BASE_URL || '/';
    }

    function montarQueryString(parametros = {}) {
        const query = new URLSearchParams();

        Object.entries(parametros).forEach(([chave, valor]) => {
            if (valor === undefined || valor === null) {
                return;
            }

            if (Array.isArray(valor)) {
                valor.forEach(item => query.append(`${chave}[]`, item));
            } else {
                query.append(chave, valor);
            }
        });

        const texto = query.toString();
        return texto ? `?${texto}` : '';
    }

    function montarFormData(dados = {}) {
        const formulario = new FormData();

        Object.entries(dados).forEach(([chave, valor]) => {
            if (valor === undefined || valor === null) {
                return;
            }

            if (Array.isArray(valor)) {
                valor.forEach(item => formulario.append(`${chave}[]`, item));
            } else {
                formulario.append(chave, valor);
            }
        });

        return formulario;
    }

    /** Sempre devolve { status, data?, msg? } — lê o JSON mesmo quando o HTTP status não é 2xx. */
    async function lerResposta(resposta) {
        try {
            return await resposta.json();
        } catch (erro) {
            return { status: resposta.status, msg: 'Resposta inválida do servidor.' };
        }
    }

    async function get(rota, parametros = {}) {
        const resposta = await fetch(`${baseUrl()}${rota}${montarQueryString(parametros)}`);
        return lerResposta(resposta);
    }

    async function post(rota, dados = {}) {
        const resposta = await fetch(`${baseUrl()}${rota}`, {
            method: 'POST',
            body: montarFormData(dados)
        });
        return lerResposta(resposta);
    }

    window.Api = { get, post };
})();
