# Versionamento de Checklist e Tipo de Pentest

## O problema

O sistema tem uma cadeia de relacionamento N:N:

```
Checklist  --(N:N)-->  Tipo de Pentest  --(N:N)-->  Projeto
```

- Uma ou mais Checklists entram em um ou mais Tipos de Pentest.
- Um Tipo de Pentest pode estar em um ou mais Projetos.

**Comportamento indesejado hoje:** editar uma Checklist (ou um Tipo de Pentest) faz um
`UPDATE` direto na linha existente. Como os pivots apontam para o `id` dessa linha,
qualquer edição "vaza" instantaneamente para todo mundo que já usava aquele registro:

- Editar uma Checklist muda o conteúdo em todos os Tipos de Pentest que já a usavam.
- Editar um Tipo de Pentest muda o conteúdo em todos os Projetos que já o usavam.

**Comportamento desejado:**

- Tipos de Pentest já criados devem continuar com a versão da Checklist que tinham
  quando foram criados/editados. Só Tipos de Pentest criados **depois** da edição
  pegam a versão nova.
- O mesmo vale para Projetos em relação a Tipo de Pentest.

## Diagnóstico no schema atual

Projeto: PHP puro com PDO (sem Eloquent/Laravel, sem migrations). Schema em
`app/Model/Database/banco.sql` (autoritativo — `banco_exportado.sql` é dump antigo
e desatualizado, ignorar). Nenhuma FK está de fato ativa no banco (todas comentadas
no SQL), então nada impede tecnicamente a solução proposta.

Tabelas relevantes (nomes e colunas reais, resumidos):

```sql
checklist (id, nome, descricao, categoria, habilitado)
checklist_item_catalogo (id, titulo, referencia, obrigatorio, habilitado)
checklist_item_vinculo (id, checklist_id, item_id)

tipo_pentest (id, categoria_id, nome, descricao_breve, descricao_completa,
              tecnica, nv_risco_padrao, nivel_profundidade, habilitado)
categoria_pentest (id, nome, descricao, habilitado)
framework (id, nome, descricao, habilitado)

projeto (id, empresa_id, nome, data_inicio, data_fim_prevista, data_fim_real,
         horas_contratadas, horas_executadas, tipo, nivel_sigilo, escopo, alvo,
         contrato, restricao, habilitado)

-- pivots (chave composta, sem colunas extras além de habilitado quando indicado)
tipo_pentest_checklist (tipo_pentest_id, checklist_id)
tipo_pentest_framework (tipo_pentest_id, framework_id)
projeto_tipo_pentest (projeto_id, tipo_pentest_id, habilitado)
```

Models/Controllers envolvidos:

- `app/Model/Database/ChecklistModel.php` — CRUD de `checklist` +
  `checklist_item_catalogo`/`checklist_item_vinculo`. `atualizar()` faz `UPDATE`
  direto na linha da checklist.
- `app/Model/TipoPentest.php` — `atualizar()` faz `UPDATE` na linha de `tipo_pentest`
  e usa o padrão "excluir todos os vínculos e reinserir" (`excluirVinculos()` +
  `salvarFrameworks()`/`salvarChecklists()`) para as pivots — não preserva histórico.
- `app/Controller/ChecklistController.php`, `app/Controller/TipoPentestController.php`
  — espelham o Model, sem lógica extra.
- `app/Model/Projeto.php` / `app/Controller/ProjetoController.php` — CRUD simples,
  **ainda não implementam** o vínculo com Tipo de Pentest. A tabela
  `projeto_tipo_pentest` já existe no schema e tem seed em `insert.sql`, com um
  comentário do dev anterior ("Matheus Kill: usar esta tabela para gravar/ler os
  tipos escolhidos no cadastro de projeto"), mas nenhum código lê/grava nela ainda.
  Isso é uma oportunidade de implementar esse vínculo já seguindo o padrão de
  versionamento abaixo, em vez de implementar "certo" e depois ter que corrigir.

Não existe hoje nenhuma lógica de versionamento/cópia em nenhum Model/Controller.

## Solução: parar de dar UPDATE em linhas "em uso" — versionar por cópia

Ideia central (padrão comum em e-commerce para snapshot de preço/produto no
pedido, em CMS para versionamento de conteúdo etc.): **editar não faz UPDATE na
linha existente — cria uma linha nova, e só vínculos futuros apontam pra ela.**

### Mudança de schema

Adicionar 3 colunas em `checklist` e em `tipo_pentest`:

```sql
ALTER TABLE checklist
  ADD COLUMN grupo_id INT NOT NULL,
  ADD COLUMN versao INT NOT NULL DEFAULT 1,
  ADD COLUMN atual TINYINT NOT NULL DEFAULT 1;

ALTER TABLE tipo_pentest
  ADD COLUMN grupo_id INT NOT NULL,
  ADD COLUMN versao INT NOT NULL DEFAULT 1,
  ADD COLUMN atual TINYINT NOT NULL DEFAULT 1;

-- backfill: cada linha existente vira sua própria "família", versão 1
UPDATE checklist SET grupo_id = id;
UPDATE tipo_pentest SET grupo_id = id;
```

- `grupo_id`: apelido permanente da "família" — identifica logicamente a mesma
  checklist/tipo através de várias versões. Na primeira versão, `grupo_id = id`
  (aponta pra si mesma).
- `versao`: incrementa a cada edição.
- `atual`: só uma linha por `grupo_id` tem `atual = 1` — é essa que aparece nas
  telas de cadastro/edição e nos dropdowns de seleção.

Os pivots (`tipo_pentest_checklist`, `projeto_tipo_pentest`) **não mudam de
estrutura** — eles já apontam para um `id` específico, que é exatamente o que
precisamos. A mudança é parar de sobrescrever o que aquele `id` significa.

### Fluxo de edição (novo)

Em vez de `UPDATE checklist SET nome=?, descricao=? WHERE id=?`, o
`ChecklistModel::atualizar()` passa a:

1. Buscar a linha atual (`atual=1` do grupo) + seus `checklist_item_vinculo`.
2. `INSERT` uma linha nova em `checklist` com o conteúdo editado, `grupo_id` =
   grupo da antiga, `versao = antiga.versao + 1`, `atual = 1`.
3. `UPDATE checklist SET atual = 0 WHERE id = <antiga>`.
4. Clonar os `checklist_item_vinculo` para o novo `checklist_id` (repassando os
   `item_id` escolhidos/atualizados).

Nada mais muda — os `tipo_pentest_checklist` já gravados continuam apontando pro
`checklist_id` antigo, intocados.

Mesma lógica em `TipoPentest::atualizar()`: nova linha em `tipo_pentest` (novo
`id`, mesmo `grupo_id`, `versao+1`), clona `tipo_pentest_framework`, e ao clonar
`tipo_pentest_checklist` resolve para o `checklist_id` **atual** de cada grupo de
checklist naquele momento — é exatamente aqui que uma atualização de checklist
"aparece" para tipos de pentest editados depois, e "some" (fica congelada) para
os que não foram tocados.

`projeto_tipo_pentest`, quando for implementada, deve gravar o `tipo_pentest_id`
atual no momento em que o projeto for salvo/editado — nunca re-resolver depois.

**Ponto-chave:** a resolução "pegue a versão atual" só acontece no momento em que
um vínculo é criado/salvo (write-time) — nunca depois, automaticamente. Não existe
"toda vez que abrir esse registro, busque a versão mais nova". É resolvido uma vez,
na hora do INSERT, e fica congelado para sempre.

### Nas telas de listagem/seleção

Toda query que hoje faz `SELECT * FROM checklist` ou `SELECT * FROM tipo_pentest`
para popular listas/dropdowns passa a levar `WHERE atual = 1`. Histórico fica no
banco para os vínculos antigos, mas some da UI de seleção — o usuário só escolhe
entre versões atuais ao criar um vínculo novo.

## Exemplo passo a passo

**T1 — estado inicial:**

| tabela | linha |
|---|---|
| `checklist` | `id=5, nome="OWASP Top 10", grupo_id=5, versao=1, atual=1` |
| `tipo_pentest` | `id=10, nome="Pentest Web Básico"` |
| `tipo_pentest_checklist` | `tipo_pentest_id=10, checklist_id=5` |

**T2 — usuário edita a checklist 5:**

```sql
INSERT INTO checklist (nome, grupo_id, versao, atual)
VALUES ('OWASP Top 10 - atualizado', 5, 2, 1);  -- novo id gerado, ex: 42

UPDATE checklist SET atual = 0 WHERE id = 5;
```

| id | grupo_id | versao | atual | nome |
|---|---|---|---|---|
| 5 | 5 | 1 | 0 | OWASP Top 10 (original) |
| 42 | 5 | 2 | 1 | OWASP Top 10 - atualizado |

`tipo_pentest_checklist` não foi tocado — continua `(10, 5)`. O Tipo de Pentest 10
continua enxergando a versão antiga, para sempre.

**T3 — usuário cria um Tipo de Pentest novo** ("Pentest Web Avançado", id=99) e
escolhe uma checklist no dropdown. O dropdown lista `WHERE atual=1` → só aparece a
`id=42`. Ao salvar:

```sql
INSERT INTO tipo_pentest_checklist (tipo_pentest_id, checklist_id) VALUES (99, 42);
```

**Resultado final:**

| tipo_pentest | checklist vinculada | versão que enxerga |
|---|---|---|
| 10 (Pentest Web Básico — antigo) | id=5 | conteúdo original, congelado |
| 99 (Pentest Web Avançado — novo) | id=42 | conteúdo atualizado |

O vínculo do Tipo 10 só mudaria se alguém editasse o Tipo de Pentest 10
explicitamente — o que dispararia o mesmo mecanismo (cria `tipo_pentest` novo, e aí
sim ele re-resolve as checklists atuais na hora de clonar os vínculos).

## Pontos de atenção para quando for implementar

- **Itens de checklist são compartilhados via catálogo** (`checklist_item_catalogo`
  + `checklist_item_vinculo`). Editar um item do catálogo em si (`UPDATE
  checklist_item_catalogo ... WHERE id=X`) tem o mesmo risco de vazamento — vale
  decidir se o catálogo de itens também precisa desse tratamento, ou se será
  tratado como "conteúdo compartilhado e sempre mutável por design" (a decidir).
- `TipoPentest::atualizar()` hoje usa o padrão "excluir vínculos e reinserir"
  (`excluirVinculos()`) — isso precisa virar "clonar vínculos para a nova versão",
  não mexer nos vínculos da versão antiga.
- Implementar o vínculo `projeto_tipo_pentest` (ainda não existe em código) já
  seguindo este padrão desde o início.
- Decidir se `grupo_id` deve ter um índice/FK (mesmo que lógica, sem constraint
  real, como o resto do schema) para facilitar consultas "me dê o histórico de
  versões desta checklist".
- Definir como fica a exclusão (`habilitado=0`): provavelmente deve desabilitar a
  família inteira (todas as versões) ou só a versão atual — a decidir.
