# Prettier — Formatação de Código

O Prettier é a ferramenta que garante que todo o time escreva código com a mesma indentação, espaçamento e estilo, sem precisar de acordo manual.

---

## 1. Instalar a extensão no VSCode

1. Abra o VSCode
2. Pressione `Ctrl + Shift + X` para abrir a aba de extensões
3. Busque por **Prettier - Code formatter**
4. Instale a extensão do publisher **Esben Petersen**

---

## 2. Definir o Prettier como formatter padrão

1. Pressione `Ctrl + Shift + P`
2. Digite **Open User Settings (JSON)** e pressione Enter
3. Adicione as linhas abaixo dentro do JSON:

```json
"editor.defaultFormatter": "esbenp.prettier-vscode",
"editor.formatOnSave": true,
"[css]": {
  "editor.defaultFormatter": "esbenp.prettier-vscode"
},
"[javascript]": {
  "editor.defaultFormatter": "esbenp.prettier-vscode"
},
"[html]": {
  "editor.defaultFormatter": "esbenp.prettier-vscode"
}
```

Com isso, o arquivo será formatado automaticamente sempre que você salvar.

---

## 3. Configuração do projeto

O arquivo `.prettierrc` na raiz do projeto já contém as regras definidas pelo time:

| Opção | Valor | O que faz |
|---|---|---|
| `printWidth` | `120` | Quebra a linha após 120 caracteres |
| `tabWidth` | `4` | Indenta com 4 espaços |
| `useTabs` | `false` | Usa espaços, não tabs |
| `semi` | `true` | Adiciona `;` ao final das linhas JS |
| `singleQuote` | `false` | Usa aspas duplas `"` |
| `trailingComma` | `"es5"` | Vírgula após o último item em arrays/objetos multilinha |
| `bracketSpacing` | `true` | Espaço dentro de objetos inline `{ chave: valor }` |
| `endOfLine` | `"lf"` | Padrão Unix de quebra de linha (evita conflitos entre Windows e Mac) |

Não altere o `.prettierrc` individualmente — qualquer mudança deve ser decidida em conjunto pelo time.

---

## 4. Formatar um arquivo manualmente

Com o arquivo aberto no VSCode, pressione:

- **Windows:** `Shift + Alt + F`

---

## 5. O que o Prettier NÃO formata

- Pastas listadas no `.prettierignore` (`ASSETS/`, `DOCS/`)
- Lógica, nomes de variáveis ou estrutura do código — só o estilo visual
