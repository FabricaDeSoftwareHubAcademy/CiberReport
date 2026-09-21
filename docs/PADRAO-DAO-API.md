# Padrão DAO + API (baseado no Infotech)

Este documento explica o novo padrão de infraestrutura do projeto — camada
DAO genérica + API JSON + JS genérico — e como migrar as telas que ainda
não seguem esse padrão. O exemplo de referência completo é **Tipo de
Pentest** (`gerenciar-pentest`): use os arquivos dele como modelo.

## Por que mudou

Antes, cada tela reescrevia SQL cru dentro do Model, tratava `$_POST`
manualmente na própria *view* (com um `switch ($_POST['action'])` e uma
função `responderJson*()` própria por tela) e o JavaScript duplicava o
`fetch` em cada arquivo. O objetivo do padrão novo é reduzir essa
repetição com classes-pai genéricas, iguais em espírito às do projeto
Infotech do professor.

## As classes-pai (não mexer sem necessidade)

| Classe | Arquivo | Papel |
|---|---|---|
| `DAO\DAO` | `app/DAO/DAO.php` | Conexão PDO única por request (`DAO::conexao()`). Todo DAO estende esta classe. |
| `Core\Model` | `app/Core/Model.php` | Base das entidades. Só tem `public array $rows`. |
| `Core\Controller` | `app/Core/Controller.php` | `view()`, `redirect()` (já existiam) + `isPost()`, `post()`, `query()`, `json()` (novos). |
| `Core\Router` | `app/Core/Router.php` | Despacha as rotas de `app/routes/main.php`. Não muda seu jeito de usar. |

## O contrato de resposta da API

Todo endpoint JSON responde com o método `$this->json([...])` do
controller, sempre neste formato:

```php
// sucesso
$this->json(['status' => 200, 'data' => $algumaCoisa]);
$this->json(['status' => 200, 'msg' => 'Salvo com sucesso.', 'id' => 7]);

// erro
$this->json(['status' => 400, 'msg' => 'Preencha todos os campos obrigatórios!']);
$this->json(['status' => 404, 'msg' => 'Não encontrado.']);
```

`json()` já define o HTTP status real (`http_response_code`) a partir do
campo `status` e encerra a requisição (`exit`). No front, use sempre
`Api.get()`/`Api.post()` (`app/assets/JS/api.js`) e decida sucesso/erro
olhando `resultado.status`:

```js
const resultado = await Api.post('tipo-pentest/cadastro', dados);
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
6. **JS** — troque qualquer `fetch(...)` manual por `Api.get()`/`Api.post()`
   (inclua `app/assets/JS/api.js` antes do script da tela) e troque
   `if (!resultado.ok)` por `if (resultado.status !== 200)`,
   `resultado.mensagem` por `resultado.msg`, e o nome do payload (`tipo`,
   `item`, `cliente`...) por `resultado.data`.

## Antes × depois (Tipo de Pentest)

| | Antes | Depois |
|---|---|---|
| Dados | SQL cru dentro de `Model\TipoPentest` (recebia `$pdo` no construtor) | `app/DAO/TipoPentestDAO.php` — SQL isolado, conexão compartilhada |
| Entidade | Não existia (Model era um wrapper de PDO) | `Model\TipoPentest` com propriedades tipadas |
| API | `switch ($_POST['action'])` dentro da própria view, com `exit` | Rotas `/tipo-pentest/listar`, `/buscar`, `/cadastro`, `/exclusao`, `/status` no controller |
| Resposta | Contrato variável (`ok`, `tipo`, `mensagem`...) | `{status, data|msg}` sempre |
| JS | `enviarAcaoTipoPentest()` duplicado por tela | `Api.get()`/`Api.post()` genérico (`app/assets/JS/api.js`) |
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
