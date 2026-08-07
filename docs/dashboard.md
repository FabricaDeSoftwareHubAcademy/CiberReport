# Dashboard do Gestor — ajustes de usabilidade

Este documento registra os ajustes feitos na tela `dashboard-gestor` (`Views/dashboard_gestor.php`, `assets/JS/dashboardGestor.js`, `assets/CSS/Pages/dashboard_gestor.css`) a partir de uma análise pelas 10 heurísticas de Nielsen, e o que **não** foi corrigido nesta rodada — para não ficar perdido depois que a conversa que gerou a análise não estiver mais disponível.

Contexto: esta tela hoje é **frontend com dados mockados** (`projetosMock`/`analistasMock` em `dashboardGestor.js`) — não há Model, Controller com lógica de negócio, nem integração com o banco. Isso limita o que dá para corrigir de verdade nesta etapa.

## O que foi corrigido

- **Clique em linha da tabela / barra do gráfico não fazia nada.** Agora dispara um toast ("Em breve — A Dashboard do Projeto ainda está em desenvolvimento.") em vez de silêncio total. Continua sendo um placeholder — só passou a dar feedback em vez de parecer quebrado.
- **Tabela sem estado vazio.** `renderizarTabela()` agora mostra "Nenhum projeto ativo encontrado." quando `projetosAtivos()` está vazio, em vez de deixar o `<tbody>` em branco.
- **Filtro por analista (clique na pizza/legenda) era silencioso.** Agora aparece um chip "Filtrado por **Nome** ×" acima da tabela, com botão para limpar o filtro.
- **Valor de "dias para prazo em risco" não persistia.** Agora é salvo em cookie (`dashboardGestorDiasPrazo`, mesmo padrão de `linhasPorPagina` do `tabela.js`) e recarregado automaticamente na próxima visita.
- **Regras de negócio dos cards não estavam documentadas na tela.** Adicionados ícones `(i)` com tooltip nos cards "Vulnerabilidades Críticas/Altas em Aberto" e "Prazos Vencendo em N dias" explicando exatamente o que é contado.
- **Donut sem total.** Centro do gráfico de pizza agora mostra "Ativos / 7".
- **Sem limite máximo no input de dias.** Adicionado `max="180"`, com o mesmo teto aplicado no cálculo em JS (`DIAS_PRAZO_MAXIMO`).
- **Sem fallback se o ApexCharts não carregar** (CDN indisponível). Agora exibe "Gráfico indisponível no momento." nos dois containers em vez de ficar em branco.

## O que não foi corrigido agora (e por quê)

- **Navegação real para a Dashboard do Projeto.** A tela ainda não existe — é a próxima etapa do trabalho (ver as outras 2 dashboards planejadas: Gestor, Analista, Projeto). O toast "Em breve" é o paliativo até essa rota existir.
- **Dados de alocação analista↔projeto são mockados.** A tabela `projeto_usuario` existe no schema (`Model/Database/banco.sql`) mas não tem Model nem é lida/escrita em nenhum lugar do código hoje — não há tela de alocação real. Por decisão consciente (ver histórico do planejamento desta feature), esta etapa ficou só no frontend; os cards "Analistas Não Alocados" e o gráfico de pizza continuam usando `analistasMock`/`projetosMock` até essa integração existir.
- **Sem guard de acesso por perfil (`Administrador`).** Nenhuma rota do CiberReport hoje valida `perfil_acesso` — login só grava `usuario_id`/`usuario_nome` na sessão. Adicionar isso só nesta tela criaria um comportamento inconsistente com o resto do sistema; fica para quando o controle de acesso for implementado de forma centralizada (`Core\Controller` ou um `Auth` dedicado).
- **`tabela.js` não tem estado "nenhum resultado encontrado" quando busca global ou filtro de coluna zera as linhas.** Esse é um gap do componente compartilhado (`assets/JS/componentes/tabela.js`), usado por Checklist, Vulnerabilidades, Usuários, Gerenciamento de Projeto, etc. Corrigir só na Dashboard do Gestor criaria comportamento divergente entre telas — precisa ser uma mudança no componente central, não um patch local.
- **Sino de notificações sem contagem/badge.** Não é uma funcionalidade implementada em nenhuma tela do sistema hoje (é só um ícone estático em `Components/menu.php`) — fora do escopo desta dashboard.
- **Sem indicador de carregamento ("loading").** Não se aplica agora porque os dados são mockados e renderizam de forma síncrona. Vira necessário quando os cards/gráficos passarem a buscar dados de um endpoint real (ver próxima etapa de backend).
- **Filtro por analista não persiste entre reloads** (diferente do "dias", que agora persiste). Decisão de manter o escopo pequeno — persistir filtro de tabela é um padrão que também deveria nascer no `tabela.js`, não só aqui.
