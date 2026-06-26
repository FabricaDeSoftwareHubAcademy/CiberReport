# Variáveis de Ambiente (.env)

## O que é o arquivo `.env`?

O `.env` é um arquivo de configuração que fica na raiz do projeto e guarda informações sensíveis — como senha do banco de dados — **fora do código-fonte**.

Isso evita que credenciais reais sejam expostas no GitHub acidentalmente.

---

## Por que ele não aparece no repositório?

O `.gitignore` do projeto já tem a linha `.env`, o que impede que ele seja enviado ao GitHub.

Por isso, **cada pessoa da equipe precisa criar o próprio `.env` na sua máquina.**

---

## Como criar o seu `.env`

1. Na raiz do projeto, copie o arquivo de exemplo:

   .env.example
   
   E cole o conteudo em um novo arquivo chamado .env na raiz do projeto 



2. Abra o `.env` e preencha com as credenciais do seu ambiente local:

   ```env
   DB_HOST=localhost
   DB_NAME=cyber_report
   DB_USER=root
   DB_PASS=sua_senha_aqui
   ```

3. Salve o arquivo. Pronto.

---

## O que cada variável significa

| Variável  | Descrição                                     | Valor padrão (local) |
|-----------|-----------------------------------------------|----------------------|
| `DB_HOST` | Endereço do servidor do banco de dados        | `localhost`          |
| `DB_NAME` | Nome do banco de dados                        | `cyber_report`       |
| `DB_USER` | Usuário do banco de dados                     | `root`               |
| `DB_PASS` | Senha do banco de dados                       | _(varia por máquina)_ |

---

## Como o projeto lê o `.env`

O arquivo `bootstrap.php` na raiz do projeto é responsável por ler o `.env` e disponibilizar os valores para o restante do código via `$_ENV`.

```php
// bootstrap.php
$linhas = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($linhas as $linha) {
    if (str_starts_with(trim($linha), '#') || !str_contains($linha, '=')) continue;
    [$chave, $valor] = explode('=', $linha, 2);
    $_ENV[trim($chave)] = trim($valor);
}
```

O que ele faz, linha a linha:

1. Lê todas as linhas do `.env` ignorando linhas em branco.
2. Pula linhas que começam com `#` (comentários) ou que não têm `=`.
3. Separa cada linha em `CHAVE=VALOR` e salva em `$_ENV`.

---

## Como usar o `bootstrap.php` nos arquivos PHP

Todo arquivo que precisar acessar variáveis de ambiente deve incluir o `bootstrap.php` com `require_once` **antes** de usá-las.

```php
<?php
require_once __DIR__ . '/../bootstrap.php';

$host  = $_ENV['DB_HOST'];
$banco = $_ENV['DB_NAME'];
$user  = $_ENV['DB_USER'];
$pass  = $_ENV['DB_PASS'];

$conexao = new mysqli($host, $user, $pass, $banco);
```


### Exemplo prático com o `conexao.php`

Antes (credenciais fixas no código — ruim):
```php
$host  = "localhost";
$user  = "root";
$pass  = "minha_senha";
$banco = "cyber_report";
```

Depois (lendo do `.env` — correto):
```php
require_once __DIR__ . '/../bootstrap.php';

$host  = $_ENV['DB_HOST'];
$user  = $_ENV['DB_USER'];
$pass  = $_ENV['DB_PASS'];
$banco = $_ENV['DB_NAME'];
```

### Exemplo real do projeto: `Views/cliente_empresa.php`

Este arquivo fica dentro da pasta `Views/`, então o caminho para a raiz sobe um nível com `../`:

```php
<?php
require "../bootstrap.php";               // carrega o .env e popula $_ENV
require_once "../Model/Database/Empresa.php";
require_once "../Model/Database/Endereco.php";

$empresa  = new Empresa();
$endereco = new Endereco();

// as credenciais vêm do .env, não estão escritas aqui
$empresa->conectar($_ENV['DB_NAME'], $_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
$endereco->conectar($_ENV['DB_NAME'], $_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
```

> **Atenção ao caminho:** o `require` usa um caminho relativo ao arquivo atual.
> - Arquivo em `Views/` → `require "../bootstrap.php"`
> - Arquivo em `Model/Database/` → `require "../../bootstrap.php"`
> - Arquivo na raiz → `require "bootstrap.php"`

---

## Regras importantes

- **Nunca commite o `.env`** — ele contém sua senha real.
- **Nunca coloque credenciais reais no `.env.example`** — esse arquivo é público.
- Se você criar uma variável nova no `.env`, adicione ela também no `.env.example` com o valor em branco ou um exemplo fictício, e avise a equipe.

---

## Adicionando uma variável nova

Exemplo: adicionar a URL base da aplicação.

1. Adicione no seu `.env`:
   ```env
   APP_URL=http://localhost/CiberReport
   ```

2. Adicione no `.env.example` (sem valor real):
   ```env
   APP_URL=http://localhost/seu-projeto
   ```

3. No PHP, acesse com:
   ```php
   $url = $_ENV['APP_URL'];
   ```

4. Avise a equipe para atualizar o `.env` local delas.
