# Dashboard do Gestor — ajustes de usabilidade

Este documento registra os ajustes feitos na tela `dashboard-gestor` (`Views/dashboard_gestor.php`, `assets/JS/dashboardGestor.js`, `assets/CSS/Pages/dashboard_gestor.css`) a partir de uma análise pelas 10 heurísticas de Nielsen, e o que **não** foi corrigido nesta rodada — para não ficar perdido depois que a conversa que gerou a análise não estiver mais disponível.

Contexto: esta tela hoje é **frontend com dados mockados** (`projetosMock`/`analistasMock` em `dashboardGestor.js`) — não há Model, Controller com lógica de negócio, nem integração com o banco. Isso limita o que dá para corrigir de verdade nesta etapa.

## O que foi corrigido

- **Tabela sem estado vazio.** `renderizarTabela()` agora mostra "Nenhum projeto em andamento no momento." quando `projetosAtivos()` está vazio, em vez de deixar o `<tbody>` em branco.
- **Filtro por analista (clique na pizza/legenda) era silencioso.** Agora aparece um chip "Filtrado por **Nome** ×" acima da tabela, com botão para limpar o filtro.
- ~~**Valor de "dias para prazo em risco" não persistia.** Agora é salvo em cookie (`dashboardGestorDiasPrazo`, mesmo padrão de `linhasPorPagina` do `tabela.js`) e recarregado automaticamente na próxima visita.~~ Revertido: o input de dias e a persistência em cookie foram removidos depois — ver [Prazo em risco — antecedência por projeto (mockada)](#prazo-em-risco--antecedência-por-projeto-mockada).
- **Regras de negócio dos cards não estavam documentadas na tela.** Adicionados ícones `(i)` com tooltip nos cards "Vulnerabilidades Críticas em Aberto" e "Prazos em Risco" explicando exatamente o que é contado.
- **Donut sem total.** Centro do gráfico de pizza agora mostra "Ativos / 7".
- ~~**Sem limite máximo no input de dias.** Adicionado `max="180"`, com o mesmo teto aplicado no cálculo em JS (`DIAS_PRAZO_MAXIMO`).~~ Obsoleto: o input foi removido (ver seção citada acima).
- **Sem fallback se o ApexCharts não carregar** (CDN indisponível). Agora exibe "Gráfico indisponível no momento." nos dois containers em vez de ficar em branco.
- **Títulos/microcopy revisados** a partir de uma análise de texto: ~~"Vulnerabilidades Críticas/Altas em Aberto" → "Vulnerabilidades Graves em Aberto"~~ (revertido depois, ver seção "Card de Vulnerabilidades — critério mudou de Crítica+Alta para só Crítica" abaixo); "Prazos Vencendo em N dias" virou título "Prazos em Risco" com o controle de dias numa linha secundária separada; gráfico "Projetos Alocados" → "Projetos por Analista" (alinhado ao padrão "X por Y" do gráfico de barras); terminologia "ativo"/"em andamento" padronizada para "em andamento" em toda a tela; coluna "Vulnerabilidades" da tabela fica destacada em vermelho quando o projeto tem vulnerabilidade crítica ou alta em aberto.

## Cards de KPI — ordem atual e trabalho futuro

Os cards "Projetos Concluídos (ano)" e "Analistas Não Alocados" foram removidos da tela (dependiam de dados que hoje só existem mockados — ver seção abaixo sobre alocação analista↔projeto). A ordem atual dos 3 cards restantes é:

1. **Vulnerabilidades Críticas em Aberto**
2. **Projetos em Andamento**
3. **Prazos em Risco**

Comportamento de clique nos cards:

- **Vulnerabilidades Críticas em Aberto** (`#card-vulns-criticas`): hoje o clique não faz nada (só um `console.log('TODO...')` em `dashboardGestor.js`). **Trabalho futuro:** ao clicar, deve filtrar a tabela pelos projetos que ainda têm vulnerabilidade crítica em aberto (não corrigida).
- **Projetos em Andamento** (`#card-em-andamento`): como a tabela já só lista projetos com `status === 'EM_ANDAMENTO'`, o clique hoje apenas limpa qualquer filtro ativo (ex.: filtro por analista vindo do gráfico de pizza), voltando à visão padrão — que já é "somente projetos ativos".
- **Prazos em Risco**: sem comportamento de clique — voltou a ser um card de KPI só leitura. Chegou a ser feito um popover ancorado ao card com a lista de projetos em risco, mas foi removido: a equipe decidiu que esse detalhamento vai aparecer como colunas novas na própria tabela de projetos (ex.: prazo de aviso do projeto, status vencido/em risco), em vez de uma interação separada no card. A lista fixa "Próximos Prazos" que existia antes do popover também não volta — o bloco lateral continua removido e a tabela ocupa a largura toda (ver seção abaixo sobre a regra de "em risco" por projeto, que segue valendo para a contagem do card e vai alimentar essas colunas futuras). **Feito:** ver [Tabela de projetos — colunas revisadas](#tabela-de-projetos--colunas-revisadas) — a coluna "Data Fim Prevista" ganhou o badge vencido/em risco linha a linha.

## Card de Vulnerabilidades — critério mudou de Crítica+Alta para só Crítica

O card, renomeado para **"Vulnerabilidades Críticas em Aberto"**, agora soma **apenas** vulnerabilidades de severidade `CRITICA` dos projetos em andamento — vulnerabilidades `ALTA` não entram mais nessa soma (antes era Crítica + Alta). Quando o total é 0, o card fica com o fundo padrão (igual aos demais cards); quando há pelo menos 1, o fundo vira vermelho sólido (`--cor-vermelho-destaque`, texto branco) para transmitir urgência, via classe `card-gestor--vulns-risco` aplicada em `renderizarCards()` (`dashboardGestor.js`).

Por causa disso, o mock `projetosMock` trocou o campo único `vulnerabilidades_criticas_altas` por dois campos separados, `vulnerabilidades_criticas` e `vulnerabilidades_altas` — mais fiel ao schema real (`severidade_vulnerabilidade ENUM('BAIXA','MEDIA','ALTA','CRITICA')` em `Model/Database/banco.sql`). ~~O destaque em vermelho da coluna "Vulnerabilidades" na tabela (`celula-vuln-grave`) continua somando os dois campos (Crítica + Alta) — só o card de KPI ficou restrito a Crítica.~~ A coluna "Vulnerabilidades" (contagem total) foi removida da tabela depois — ver [Tabela de projetos — colunas revisadas](#tabela-de-projetos--colunas-revisadas): agora a tabela usa o mesmo critério do card (só Crítica), como um indicador Sim/Não por projeto.

## Prazo em risco — antecedência por projeto (mockada)

O campo "Alertar com antecedência de N dias" foi **removido** do card "Prazos em Risco" (não existe mais como input na tela) e a persistência em cookie (`dashboardGestorDiasPrazo`) foi removida junto.

A antecedência deixou de ser uma configuração global da dashboard: a equipe aprovou que cada projeto define seu próprio prazo de aviso (ex.: um projeto avisa com 3 dias de antecedência, outro com 10 ou 20). Como o campo real ainda não existe em `projeto` (`Model/Database/banco.sql`) nem na tela de gerenciamento de projeto, ele foi **mockado**: `projetosMock` em `dashboardGestor.js` ganhou o campo `dias_aviso_prazo` (valores variados entre os projetos, só para demonstrar a customização). `DIAS_PRAZO_PADRAO` (15 dias) virou só um fallback, usado apenas se um projeto não tiver `dias_aviso_prazo` definido — provável valor padrão sugerido ao cadastrar um projeto novo.

`projetoEmRisco(projeto)` (`dashboardGestor.js`) centraliza a regra: vencido, ou `data_fim_prevista <= hoje + (projeto.dias_aviso_prazo ?? DIAS_PRAZO_PADRAO)`. Além da contagem do card "Prazos em Risco", `rotuloPrazo(projeto)` reaproveita essa função (e `projetoVencido`) para decidir o badge "Vencido"/"Em risco" exibido ao lado da data na tabela — ver [Tabela de projetos — colunas revisadas](#tabela-de-projetos--colunas-revisadas).

**Trabalho futuro:** quando o campo `dias_aviso_prazo` (ou nome equivalente) existir de verdade em `projeto` e for editável no cadastro/gerenciamento de projeto, basta trocar a origem do valor no mock por dado real — a regra de cálculo já está pronta para isso.

## O que não foi corrigido agora (e por quê)

- **Navegação real para a Dashboard do Projeto.** A tela ainda não existe — é a próxima etapa do trabalho (ver as outras 2 dashboards planejadas: Gestor, Analista, Projeto). Um toast de aviso ("Em breve") chegou a ser adicionado ao clique em linha/barra, mas foi removido a pedido — hoje o clique volta a não dar nenhum feedback visível (só `console.log('TODO...')` em `irParaDashboardProjeto`).
- **Dados de alocação analista↔projeto são mockados.** A tabela `projeto_usuario` existe no schema (`Model/Database/banco.sql`) mas não tem Model nem é lida/escrita em nenhum lugar do código hoje — não há tela de alocação real. Por decisão consciente (ver histórico do planejamento desta feature), esta etapa ficou só no frontend; o gráfico de pizza continua usando `analistasMock`/`projetosMock` até essa integração existir. O card "Analistas Não Alocados", que também dependia desses mocks, foi removido da tela (ver [Cards de KPI — ordem atual e trabalho futuro](#cards-de-kpi--ordem-atual-e-trabalho-futuro)).
- **Sem guard de acesso por perfil (`Administrador`).** Nenhuma rota do CiberReport hoje valida `perfil_acesso` — login só grava `usuario_id`/`usuario_nome` na sessão. Adicionar isso só nesta tela criaria um comportamento inconsistente com o resto do sistema; fica para quando o controle de acesso for implementado de forma centralizada (`Core\Controller` ou um `Auth` dedicado).
- **`tabela.js` não tem estado "nenhum resultado encontrado" quando busca global ou filtro de coluna zera as linhas.** Esse é um gap do componente compartilhado (`assets/JS/componentes/tabela.js`), usado por Checklist, Vulnerabilidades, Usuários, Gerenciamento de Projeto, etc. Corrigir só na Dashboard do Gestor criaria comportamento divergente entre telas — precisa ser uma mudança no componente central, não um patch local.
- **Sino de notificações sem contagem/badge.** Não é uma funcionalidade implementada em nenhuma tela do sistema hoje (é só um ícone estático em `Components/menu.php`) — fora do escopo desta dashboard.
- **Sem indicador de carregamento ("loading").** Não se aplica agora porque os dados são mockados e renderizam de forma síncrona. Vira necessário quando os cards/gráficos passarem a buscar dados de um endpoint real (ver próxima etapa de backend).
- **Filtro por analista não persiste entre reloads.** Decisão de manter o escopo pequeno — persistir filtro de tabela é um padrão que também deveria nascer no `tabela.js`, não só aqui.

## Tabela de projetos — colunas revisadas

A partir da pergunta "quais dados importam pra decisão rápida do gestor", as colunas da tabela (`Views/dashboard_gestor.php`, `renderizarTabela()` em `dashboardGestor.js`) foram revistas — de 7 para 6 colunas:

- **Removidas: "Data Início" e "Editado em".** Não ajudam numa decisão rápida — quem precisar dessas datas vai abrir o projeto (quando a Dashboard do Projeto existir).
- **Nova: "Analistas Alocados".** Antes só existia "Resp. Técnico" (responsável principal). Esta coluna lista todos os analistas trabalhando no projeto — mockada em `analistas_alocados` (array), mesma pendência de integração com `projeto_usuario` já registrada para `analistasMock` (ver [O que não foi corrigido agora](#o-que-não-foi-corrigido-agora-e-por-quê)). A célula mostra os nomes separados por vírgula (truncando com reticências + tooltip se não couber) e usa `data-valores` (suportado por `tabela.js`) para o filtro de coluna funcionar por analista individual, não pela string concatenada.
- **"Data Fim Prevista" ganhou um badge.** Reaproveita `rotuloPrazo()`/`projetoEmRisco()`/`projetoVencido()` (ver seção acima) para mostrar "Vencido" (vermelho) ou "Em risco" (laranja) ao lado da data, célula a célula — antes essa informação só existia agregada no card "Prazos em Risco".
- **"Vulnerabilidades" (contagem total) virou "Vulnerabilidade Crítica em Aberto" (indicador Sim/Não).** Ícone de checkbox marcado em vermelho (`fa-square-check`) quando `vulnerabilidades_criticas > 0`, vazio quando não há — mesmo critério do card de KPI (só `CRITICA`, não soma mais `ALTA`), pra tabela e card nunca discordarem sobre o que conta como urgente. O texto "Sim"/"Não" fica em `.oculto-visualmente` pra leitor de tela e pro filtro de coluna (`data-filtro="lista"`) funcionarem sem depender só do ícone.

Colunas finais: Projeto, Cliente, Resp. Técnico, Analistas Alocados, Data Fim Prevista (com badge), Vulnerabilidade Crítica em Aberto (checkbox) — `colspan` do rodapé/estado vazio da tabela ajustado de 7 para 6.

## Gráfico "Vulnerabilidades por Projeto" → "Progresso por Projeto"

O gráfico de barras que mostrava o total de vulnerabilidades por projeto foi substituído por um gráfico de **progresso do checklist** (`inicializarGraficoProgresso()`, container `#grafico-progresso`), mesmo espaço no grid (2fr) e mesmo comportamento de clique (abre a Dashboard do Projeto). Motivo: a contagem total de vulnerabilidades por projeto deixou de ter um lugar dedicado, mas o que sobra de "vulnerabilidade" já está coberto pelo card de KPI (críticas em aberto, agregado) e pela nova coluna da tabela (críticas em aberto, por projeto) — manter os três ao mesmo tempo seria repetir a mesma leitura em lugares diferentes. Progresso, por outro lado, não tinha nenhum lugar até então.

Cada projeto ganhou os campos mockados `checklist_itens_total`/`checklist_itens_concluidos` em `projetosMock` (espelham a tabela real `checklist_item` — ver `Model/Database/banco.sql`), e `calcularProgresso(projeto)` centraliza o cálculo do percentual (`concluidos / total`, arredondado).

Progresso foi decidido como **gráfico**, não como coluna extra na tabela: é um dado por-projeto que já cabe no padrão "barra por projeto" que o gráfico de vulnerabilidades antigo usava (categorias = cliente), então trocar o conteúdo da barra aproveitou o mesmo espaço sem criar uma terceira superfície de leitura (cards + 2 gráficos + tabela já é bastante coisa pra escanear de uma vez).

**Trabalho futuro:** quando existir Model para `checklist_item`/`checklist_item_vinculo`, trocar `checklist_itens_total`/`checklist_itens_concluidos` do mock por dado real por projeto.

## Gráfico "Projetos por Analista" (donut) → "Ocupação por Analista" (lista de barras)

O donut de projetos por analista foi substituído por uma lista de barras horizontais, uma por analista (`renderizarAlocacaoAnalistas()` em `dashboardGestor.js`, seção `.lista-alocacao`/`.alocacao-item` em `dashboard_gestor.css`). Motivo: donut não escala — com muitos analistas as fatias ficam ilegíveis — e o que a tela precisa mostrar não é só "quantos projetos cada um tem", é **quão perto cada analista está do próprio limite de alocação simultânea**.

Cada analista ganhou um campo mockado `limite_projetos` em `analistasMock` (ex.: estagiário = 2, sênior = 6) — no cadastro real isso vira um campo setado pelo gestor ao criar/editar o analista, mesma pendência de integração já registrada para `analistasMock`/`projetosMock` (ver seção acima). A barra de cada linha é normalizada pelo limite individual (`alocados / limite_projetos`), não por um teto fixo, já que os limites variam por pessoa.

A cor da barra segue uma **escala fixa de 4 degraus** (não um gradiente contínuo pixel a pixel), reaproveitando os tokens de status que já existiam em `style.css` (`--cor-verde`, `--cor-amarelo`, `--cor-laranja`, `--cor-vermelho-destaque`):

| % do limite | Nível (`data-nivel`) | Cor |
|---|---|---|
| < 60% | `ok` | verde |
| 60–79% | `atencao` | amarelo |
| 80–99% | `alerta` | laranja |
| ≥ 100% | `critico` | vermelho |

Um gradiente verde→vermelho contínuo foi descartado de propósito: fica difícil decodificar visualmente um tom intermediário exato, e verde/vermelho puro é o pior par de cores para quem tem daltonismo (~8% dos homens). Por isso a contagem "X/Y" sempre aparece ao lado da barra como rótulo direto — a informação nunca depende só da cor — e a lista fica ordenada da maior para a menor ocupação, para o analista mais no limite aparecer primeiro.

O clique em uma linha continua chamando `filtrarTabelaPorAnalista()`, mesmo comportamento que a legenda do donut antigo tinha (chip "Filtrado por Nome" acima da tabela).
