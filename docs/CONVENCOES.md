# Convenções do Projeto CyberReport

## Nomes de arquivos e pastas
- Sempre **minúsculas** com **kebab-case**: `menu-lateral.css`, `foto-perfil.jpg`
- Nunca usar espaços ou maiúsculas em nomes de arquivo (Linux/git diferenciam maiúsculo de minúsculo)

## JavaScript
- Variáveis e funções: **camelCase** → `listarProjetos`, `dataCriacao`
- Constantes fixas: **UPPER_SNAKE_CASE** → `URL_API`

## CSS — classes
- Usar **BEM** (Block__Element--Modifier):
  - Block: `.sidebar`, `.tabela`, `.topbar`
  - Element: `.sidebar__item`, `.tabela__linha`
  - Modifier: `.sidebar__item--ativo`, `.status--concluido`
- **Nunca** usar valores de cor, fonte ou espaçamento direto — sempre variáveis CSS definidas em `src/styles/style.css`

## Estrutura de pastas
```
PAGES/          → uma pasta por tela
src/
  components/   → componentes reutilizáveis (menu,tabela...)
  models/       → dados e regras de negócio
  utils/        → funções puras sem dependência de DOM
  styles/       → CSS global (style.css importa tudo)
ASSETS/         → imagens, ícones e fontes
DOCS/           → documentação do time
```

## Criando uma nova página
1. Criar pasta em `pages/nome-da-tela/`
2. Criar `nome-da-tela.html`, `nome-da-tela.css` e `nome-da-tela.js`
3. No HTML, copiar estrutura dos menus em `src/components/menu/`
4. Importar só `style.css` (global) + o CSS da própria página

## CSS — ordem de imports em style.css
1. Fontes e ícones externos (Google Fonts, Font Awesome)
2. reset.css
3. Componentes em ordem alfabética (sidebar, tabela, topbar...)
