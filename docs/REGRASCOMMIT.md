# 📝 Como fazer commits no projeto

> Seguimos o padrão **Conventional Commits** para manter o histórico do projeto organizado e legível para toda a equipe.

---

## Formato

```
tipo(escopo): descrição curta no imperativo
```

- **tipo** — categoria da mudança (ver tabela abaixo)
- **escopo** — parte do sistema afetada, entre parênteses (opcional, mas recomendado)
- **descrição** — frase curta, máximo 72 caracteres, no imperativo

---

## Tipos de commit

| Tipo | Quando usar | Exemplo |
|---|---|---|
| `feat` | Nova funcionalidade | `feat(pentester): adiciona filtro por pentester` |
| `fix` | Correção de bug | `fix(login): corrige redirecionamento após sessão expirar` |
| `style` | Formatação, CSS, sem alteração de lógica | `style(global): atualiza variáveis de cor no style.css` |
| `refactor` | Reorganização de código sem mudar comportamento | `refactor(api): simplifica função de busca de clientes` |
| `chore` | Configuração, dependências, arquivos de projeto | `chore(deps): atualiza versão do React para 18.3` |
| `docs` | Documentação, README, comentários | `docs(readme): adiciona instruções de instalação` |
| `test` | Testes automatizados | `test(agendamento): adiciona teste de validação de data` |

---

## Exemplos reais do projeto

```bash
feat(usuario): adiciona cadastro de novo usuario
fix(pentest): corrige cálculo de timer do teste
style(sidebar): ajusta espaçamento do menu lateral
refactor(auth): separa lógica de autenticação em serviço próprio
chore(env): adiciona variáveis de ambiente ao .env.example
docs(contributing): cria guia de contribuição da equipe
test(login): adiciona teste de erro para senha incorreta
```

---

## Regras de ouro

✅ **Escreva no imperativo**
- ✔️ `adiciona`, `corrige`, `atualiza`, `remove`
- ❌ `adicionado`, `corrigindo`, `atualizei`

✅ **Um commit = uma coisa só**
- Não misture correção de bug com nova funcionalidade no mesmo commit

✅ **Seja específico na descrição**
- ✔️ `fix(login): corrige redirecionamento após sessão expirar`
- ❌ `fix: ajustes`

✅ **Máximo 72 caracteres na descrição**
- Se precisar explicar mais, use o corpo do commit (veja abaixo)

---

## Commit com corpo (quando necessário)

Quando a mudança precisa de mais contexto, adicione uma linha em branco após a descrição:

```
refactor(cirurgia): separa etapas do fluxo em componentes

O componente anterior estava com mais de 400 linhas e dificultava
a manutenção. Cada etapa do fluxo agora é um componente
independente, facilitando testes e reutilização.
```

---

## ⚠️ Proibido

```bash
git commit -m "fix"
git commit -m "ajustes"
git commit -m "teste"
git commit -m "aaaaaaaa"
git commit -m "agora foi"
git commit -m "nao sei oque fiz"
```

---
