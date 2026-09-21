# Padrão DAO + API (baseado no Infotech)

Este documento explica o novo padrão de infraestrutura do projeto — camada
DAO genérica + endpoints JSON, no mesmo espírito do projeto Infotech do
professor — e como migrar as telas que ainda não seguem esse padrão. O
exemplo de referência completo é **Tipo de Pentest** (`gerenciar-pentest`):
use os arquivos dele como modelo.

## Por que mudou

Antes, cada tela reescrevia SQL cru dentro do Model, tratava `$_POST`
manualmente na própria *view* (com um `switch ($_POST['action'])` e uma
função `responderJson*()` própria por tela). O objetivo do padrão novo é
reduzir essa repetição com classes-pai genéricas — iguais em espírito às
do Infotech: `DAO`, `Model` e `Controller` base, e endpoints JSON
dedicados no lugar do `switch` embutido na view.

O JavaScript **não** ganhou uma camada genérica — o Infotech também não
tem uma (não existe `api.js`/`crud.js` lá). Cada tela continua fazendo seu
próprio `fetch(...)` direto, igual ao `categoria.js` do Infotech.

## As classes-pai (não mexer sem necessidade)

| Classe | Arquivo | Papel |
|---|---|---|
| `DAO\DAO` | `app/DAO/DAO.php` | Conexão PDO única por request (`DAO::conexao()`). Todo DAO estende esta classe. |
| `Core\Model` | `app/Core/Model.php` | Base das entidades. Só tem `public array $rows`. |
| `Core\Controller` | `app/Core/Controller.php` | `view()`, `redirect()` (já existiam) + `isPost()`, `post()`, `query()`, `json()` (novos). |
| `Core\Router` | `app/Core/Router.php` | Despacha as rotas de `app/routes/main.php`. Não muda seu jeito de usar. |

## O contrato de resposta da API

Todo endpoint JSON responde com o método `$this->json([...])` do
controller, sempre neste formato — igual ao `jsonResponse()` do Infotech,
inclusive no detalhe de que o **HTTP continua sempre 200**: quem diz se
deu certo ou errado é o campo `status` dentro do corpo, nunca o status
HTTP real.

```php
// sucesso
$this->json(['status' => 200, 'data' => $algumaCoisa]);
$this->json(['status' => 200, 'msg' => 'Salvo com sucesso.', 'id' => 7]);

// erro (convenção do Infotech: 400 para validação, 500 para erro de banco)
$this->json(['status' => 400, 'msg' => 'Preencha todos os campos obrigatórios!']);
$this->json(['status' => 500, 'msg' => 'Erro ao salvar.']);
```

No front, não existe cliente HTTP genérico — cada tela faz seu próprio
`fetch(...)` e decide sucesso/erro olhando `resultado.status` (nunca o
HTTP status da resposta), igual ao `categoria.js`:

```js
const response = await fetch(`${window.baseUrl}tipo-pentest/cadastro`, {
    method: 'POST',
    body: formulario // FormData, nunca JSON no corpo
});
const resultado = await response.json();

if (resultado.status !== 200) {
    mostrarErro(resultado.msg);
    return;
}
```

## Regras

- **SQL só existe no DAO.** Nem Model, nem Controller, nem View escrevem SQL.
- **Uma tabela = um DAO.** Se uma tela usa duas tabelas, tem dois DAOs (ver `TipoPentestDAO` + `CategoriaPentestDAO` + `FrameworkDAO`).
- **Model é entidade.** Propriedades tipadas (`public ?int $id`, `public string $nome`, ...) e métodos que delegam para o DAO: `getAllRows()`, `getById()` (estático), `save()`, `delete()`.
- **Controller nunca escreve SQL nem HTML.** Ele lê `$_POST`/`$_GET` (via `$this->post()`/`$this->query()`), valida, chama o Model e responde com `$this->view()` (página) ou `$this->json()` (API).
- **View nunca trata POST.** Nada de `switch ($_POST['action'])` dentro de arquivo de `Views/`. Todo processamento fica no Controller, nas rotas certas.
- **Uma requisição, uma conexão.** Nunca faça `require conexao.php` fora de um DAO — use `DAO::conexao()` (ou deixe o construtor do DAO cuidar disso).
- **JS sem abstração.** Nada de cliente HTTP genérico reutilizável entre telas — cada função de cada tela faz seu próprio `fetch(...)`, igual ao Infotech.

## Receita para migrar uma tela (passo a passo)

Usando `Cliente/Empresa` como exemplo (troque pelo nome do seu recurso):

1. **DAO** — `app/DAO/EmpresaDAO.php`, `class EmpresaDAO extends DAO`, com
   `select()`, `selectById($id)`, `insert()`, `update()`, `save()`,
   `delete()`, `existeNome()` etc. — copie a estrutura de
   `app/DAO/TipoPentestDAO.php` e adapte os campos.
2. **Model** — reescreva `app/Model/EmpresaModel.php` (ou onde estiver)
   como entidade: propriedades tipadas + `getAllRows()`/`getById()`/`save()`/`delete()`
   delegando para o DAO. Veja `app/Model/TipoPentest.php`.
3. **Controller** — no controller existente, troque os métodos que hoje
   leem `$_POST` cru e devolvem string/bool ambíguo por métodos que:
   - `index()`: só monta a view com os dados já prontos (sem tratar POST);
   - `listar()`, `buscar()`, `cadastro()`, `exclusao()`, `status()`: cada
     um chama `$this->json([...])` no final. Veja
     `app/Controller/TipoPentestController.php`.
4. **Rotas** — em `app/routes/main.php`, dentro do grupo `AuthMiddleware`,
   adicione as rotas `GET /recurso/listar`, `GET /recurso/buscar`,
   `POST /recurso/cadastro`, `POST /recurso/exclusao` (e `POST /recurso/status`
   se houver toggle de ativo/inativo). Mantenha a rota de página
   (`GET /recurso`) apontando para `index()`.
5. **View** — remova o bloco `switch ($_POST['action'])` do topo do
   arquivo. O `foreach` que monta a tabela HTML continua igual, só passa a
   usar as variáveis que o controller já entrega via `$this->view('nome', [...])`.
6. **JS** — troque o `switch`/dispatcher de ações por uma função por
   operação (ex.: `buscarEmpresa(id)`, `salvarEmpresa()`), cada uma com seu
   próprio `fetch(...)` direto para a rota certa — sem passar por nenhum
   cliente genérico. Troque `if (!resultado.ok)` por
   `if (resultado.status !== 200)`, `resultado.mensagem` por
   `resultado.msg`, e o nome do payload (`tipo`, `item`, `cliente`...) por
   `resultado.data`. Veja `app/assets/JS/componentes/modal-tipo-pentest.js`.

## Antes × depois (Tipo de Pentest)

| | Antes | Depois |
|---|---|---|
| Dados | SQL cru dentro de `Model\TipoPentest` (recebia `$pdo` no construtor) | `app/DAO/TipoPentestDAO.php` — SQL isolado, conexão compartilhada |
| Entidade | Não existia (Model era um wrapper de PDO) | `Model\TipoPentest` com propriedades tipadas |
| API | `switch ($_POST['action'])` dentro da própria view, com `exit` | Rotas `/tipo-pentest/listar`, `/buscar`, `/cadastro`, `/exclusao`, `/status` no controller |
| Resposta | Contrato variável (`ok`, `tipo`, `mensagem`...) | `{status, data|msg}` sempre, HTTP sempre 200 (igual Infotech) |
| JS | `enviarAcaoTipoPentest(acao, dados)` — um dispatcher genérico por nome de ação | Uma função por operação (`buscarTipoPentest`, `salvarTipoPentest`...), cada uma com seu próprio `fetch(...)` — igual ao Infotech |
| Conexões | Até 4 PDO por página (`require` sem `_once`) | 1 conexão por request (`DAO::conexao()`) |

## Ordem sugerida para migrar as telas restantes

Da mais simples para a mais trabalhosa:

1. `gerenciamento-acesso` (menor controller do projeto)
2. `usuario`
3. `cliente-empresa`
4. `vulnerabilidades`
5. `gerenciamento-projeto`
6. `projetos-alocados`
7. `checklist` (a maior — `ChecklistModel.php` tem 865 linhas — deixe por último)

Ao migrar `checklist`, dá para remover a "ponte temporária"
`TipoPentestController::listarChecklistsAtivos()` e criar um `ChecklistDAO`
de verdade, seguindo a mesma receita.
