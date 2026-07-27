# Filtros de coluna em tabelas

`assets/JS/componentes/tabela.js` é o componente de tabela compartilhado (paginação, ordenação e filtro). Ele é incluído por várias views (`Views/checklist.php`, `Views/cliente_empresa.php`, `Views/usuario.php`, `Views/gerenciamento_acesso.php`, `Views/gerenciamento_projeto.php`, `Views/vulnerabilidades.php`, `Views/gerenciar-tipo-pentest.php`), todas usando a mesma convenção `<th data-col="N">` no cabeçalho.

O comportamento de cada coluna ao clicar no cabeçalho é escolhido pelo atributo `data-filtro` no `<th>`. **Sem esse atributo**, a coluna mantém o comportamento legado: ordena por clique, auto-detectando texto vs. número (mais os casos especiais `data-tipo="data"` e `data-tipo="risco"`, usados só para colunas de data e de nível de risco). Isso é o que já existia antes deste sistema e continua funcionando sem nenhuma mudança de markup.

## Os 4 tipos

### `data-filtro="lista"` — selecionar valores a exibir

Adiciona um popover de checkboxes com os valores distintos da coluna (estilo filtro do Excel), **sem remover a ordenação por clique** — as duas ações convivem na mesma coluna: clicar no texto/`sort-icon` ordena (auto-detectando texto, já que `lista` não define um tipo explícito de comparação); clicar no `filtro-icon` (ícone de funil) abre o popover. Use em colunas com valores categóricos/repetidos — principalmente as que têm múltiplos valores por linha (tags).

```html
<th data-col="7" data-filtro="lista">
    <span class="th-label">
        Frameworks
        <i class="fa-solid fa-sort sort-icon"></i>
        <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por Frameworks"></i>
    </span>
</th>
```

O clique no `filtro-icon` chama `evento.stopPropagation()`, então não dispara a ordenação — os dois cliques (label vs. ícone de funil) não se atrapalham.

Cada `<td>` da coluna pode declarar `data-valores` com um array JSON contendo a lista **completa** de valores da linha:

```html
<td data-valores="[&quot;OWASP&quot;,&quot;NIST&quot;,&quot;ISO 27001&quot;]">
    <!-- célula pode exibir só um resumo (ex.: 2 tags + "+1") -->
</td>
```

Isso é necessário quando a célula trunca a exibição — sem `data-valores`, o filtro cai para o texto visível da célula inteira como um único valor. Ver `Views/gerenciar-tipo-pentest.php` (colunas Frameworks/Checklists) para o exemplo completo, incluindo o PHP que gera o JSON (`json_encode($valoresDaLinha, JSON_UNESCAPED_UNICODE)`).

### `data-filtro="alfabetica"` — ordenar como texto

Clique no cabeçalho ordena a coluna como texto (minúsculo, sem acento), alternando crescente/decrescente — igual ao comportamento automático de hoje para colunas de texto, só que explícito.

```html
<th data-col="1" data-filtro="alfabetica">
    <span class="th-label">Nome do Pentest <i class="fa-solid fa-sort sort-icon"></i></span>
</th>
```

### `data-filtro="numero"` — ordenar como número

Clique no cabeçalho ordena a coluna numericamente (crescente/decrescente). Valores não numéricos caem para comparação de texto.

```html
<th data-col="0" data-filtro="numero">
    <span class="th-label">ID <i class="fa-solid fa-sort sort-icon"></i></span>
</th>
```

### `data-filtro="data"` — filtrar por período "De/Até"

Popover diferente dos outros três: em vez de uma lista de checkboxes, mostra só dois `<input type="date">` nativos (calendário do próprio navegador, sem nenhuma lib nova) — "De" e "Até" — mais um botão "Limpar". Não tem lista de valores existentes, busca, nem botão de aplicar: a tabela filtra assim que uma das duas datas muda (evento `change`), sem precisar clicar em nada. A ordenação por clique no texto/`sort-icon` continua funcionando normalmente e ordena por data de verdade (não como texto).

```html
<th data-col="4" data-filtro="data">
    <span class="th-label">
        Data Início
        <i class="fa-solid fa-sort sort-icon"></i>
        <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por Data Início"></i>
    </span>
</th>
```

Regra do período: só "De" preenchido = a partir dessa data (em diante); só "Até" preenchido = até essa data; os dois preenchidos = período fechado (inclusive nas duas pontas); os dois vazios = sem filtro. "Limpar" zera os dois campos.

O texto da célula pode estar em `dd/mm/aaaa[ hh:mm]` ou `aaaa-mm-dd[Thh:mm]` (formato que o PDO devolve para colunas `DATE`/`DATETIME` do MySQL sem nenhuma formatação extra no PHP) — `analisarDataBR()` em `tabela.js` reconhece os dois, e a comparação com "De"/"Até" usa uma chave normalizada `aaaa-mm-dd` (mesmo formato do `<input type="date">`) por baixo dos panos, então funciona independente do formato original da célula. Ver `Views/gerenciamento_projeto.php` (colunas Data Início/Data Fim) para o exemplo em uso.

`data-filtro="data"` substitui o antigo `data-tipo="data"` (que só existe para dar ordenação cronológica, sem filtro) nas colunas onde os dois comportamentos — ordenar e filtrar — fazem sentido juntos. `data-tipo="data"` continua funcionando isoladamente em colunas que só precisam ordenar por data sem o popover.

## Quando usar cada um

- Coluna com valores repetidos entre linhas (categoria, status, tags) → `lista`.
- Coluna de texto livre (nome, descrição) → `alfabetica` (ou deixar sem `data-filtro`, que já auto-detecta texto).
- Coluna numérica (ID, quantidade) → `numero` (ou deixar sem `data-filtro`, que já auto-detecta número).
- Coluna de data → `data` (ordenação cronológica + filtro por período "De/Até"). Se só precisar ordenar por data, sem o popover, `data-tipo="data"` sozinho (sem `data-filtro`) já resolve.
- Coluna de nível de risco → continuar usando só `data-tipo="risco"` (mecanismo à parte, não usa `data-filtro`).

Só é preciso mexer em `tabela.js` se um tipo de filtro novo (diferente desses 4) for necessário — nenhuma página precisa de JS próprio para usar os tipos existentes.
