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
| `Routes\Router` | `app/Routes/Router.php` | Despacha as rotas de `app/Routes/Routes.php`. Não muda seu jeito de usar. |

## O contrato de resposta da API

Todo endpoint JSON responde com o método `$this->json([...])` do
controller, sempre neste formato, quem diz se
deu certo ou errado é o campo `status` dentro do corpo, nunca o status
HTTP real.

```php
// sucesso
$this->json(['status' => 200, 'data' => $algumaCoisa]);
$this->json(['status' => 200, 'msg' => 'Salvo com sucesso.', 'id' => 7]);

// erro (400 para validação, 500 para erro de banco)
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

Sempre com os 4 arquivos de `Tipo de Pentest` abertos do lado como modelo:
`TipoPentestDAO.php`, `TipoPentest.php`, `TipoPentestController.php` e
`modal-tipo-pentest.js`. A ideia é ir copiando cada um e só trocando nome
de tabela/campos.

Exemplo abaixo usando `Cliente/Empresa` — troque `Empresa`/`empresa` pelo
nome do seu recurso.

### Passo 1 — Criar o DAO

1. Crie `app/DAO/EmpresaDAO.php`.
2. Copie o conteúdo de `app/DAO/TipoPentestDAO.php` para dentro dele.
3. Troque `class TipoPentestDAO` por `class EmpresaDAO extends DAO`.
4. Troque o nome da tabela e das colunas em cada SQL (`select()`,
   `selectById($id)`, `insert()`, `update()`, `delete()`, `existeNome()`...).
5. Não mude a estrutura dos métodos, só os nomes de tabela/campo.

### Passo 2 — Reescrever o Model como entidade

1. Abra `app/Model/EmpresaModel.php` (ou onde a Model atual estiver).
2. Apague o SQL/PDO que tiver dentro dela.
3. Copie de `app/Model/TipoPentest.php`: propriedades tipadas
   (`public ?int $id`, `public string $nome`, ...) + os métodos
   `getAllRows()`, `getById()` (estático), `save()`, `delete()` —
   todos delegando para o `EmpresaDAO` que você acabou de criar.

### Passo 3 — Atualizar o Controller

Abra `app/Controller/TipoPentestController.php` ao lado do controller da
sua tela e, para cada método, faça o equivalente:

1. `index()` — só monta a view com os dados prontos. Não lê `$_POST` aqui.
2. `listar()` — busca tudo, termina com `$this->json(['status' => 200, 'data' => ...])`.
3. `buscar()` — busca um registro por id, mesmo padrão de `json()`.
4. `cadastro()` — lê os campos com `$this->post('campo')`, valida, chama
   `save()` da Model, responde `json()` de sucesso ou erro (400/500).
5. `exclusao()` — idem, chamando `delete()`.
6. `status()` — só se a tela tiver toggle de ativo/inativo.

Nenhum desses métodos deve ter `echo`, `header('Content-Type: ...')` nem
SQL — só chamadas para a Model e `$this->json(...)`.

### Passo 4 — Adicionar as rotas

Em `app/Routes/Routes.php`, dentro do grupo `AuthMiddleware`, adicione (no
mesmo formato das rotas de `tipo-pentest`):

- `GET /empresa/listar`
- `GET /empresa/buscar`
- `POST /empresa/cadastro`
- `POST /empresa/exclusao`
- `POST /empresa/status` (só se existir o toggle do passo 3.6)

Mantenha a rota de página (`GET /empresa`) apontando para `index()`, sem mudar.

### Passo 5 — Limpar a View

1. Abra a view da tela.
2. Apague o bloco `switch ($_POST['action']) { ... }` do topo do arquivo
   inteiro (isso não deve mais existir em nenhuma view).
3. Deixe o `foreach` que monta a tabela HTML como está — ele só passa a
   usar as variáveis que o `index()` do controller já entrega via
   `$this->view('nome', [...])`.

### Passo 6 — Ajustar o JS

Abra `app/assets/JS/componentes/modal-tipo-pentest.js` como referência e,
no JS da sua tela:

1. Apague o dispatcher genérico (`switch` de ações / `enviarAcao(...)`).
2. Crie uma função por operação: `buscarEmpresa(id)`, `salvarEmpresa()`,
   `excluirEmpresa(id)` — cada uma com seu próprio `fetch(...)` direto para
   a rota do passo 4. Nada de cliente HTTP genérico.
3. Em cada função, troque:
   - `if (!resultado.ok)` → `if (resultado.status !== 200)`
   - `resultado.mensagem` → `resultado.msg`
   - o nome antigo do payload (`tipo`, `item`, `cliente`...) → `resultado.data`

### Passo 7 — Testar

1. Testar listar, buscar, cadastrar, editar e excluir na tela migrada.
2. Conferir no Network do navegador que toda resposta vem com HTTP 200 e
   que o front decide sucesso/erro pelo campo `status` do JSON.
3. Rodar `php -l` nos arquivos alterados (ou o lint do projeto) antes de
   commitar.

## Antes × depois (Tipo de Pentest)

| | Antes | Depois |
|---|---|---|
| Dados | SQL cru dentro de `Model\TipoPentest` (recebia `$pdo` no construtor) | `app/DAO/TipoPentestDAO.php` — SQL isolado, conexão compartilhada |
| Entidade | Não existia (Model era um wrapper de PDO) | `Model\TipoPentest` com propriedades tipadas |
| API | `switch ($_POST['action'])` dentro da própria view, com `exit` | Rotas `/tipo-pentest/listar`, `/buscar`, `/cadastro`, `/exclusao`, `/status` no controller |
| Resposta | Contrato variável (`ok`, `tipo`, `mensagem`...) | `{status, data|msg}` sempre, HTTP sempre 200 (igual Infotech) |
| JS | `enviarAcaoTipoPentest(acao, dados)` — um dispatcher genérico por nome de ação | Uma função por operação (`buscarTipoPentest`, `salvarTipoPentest`...), cada uma com seu próprio `fetch(...)`|
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
