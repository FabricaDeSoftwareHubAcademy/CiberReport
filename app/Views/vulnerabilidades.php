<!doctype html>
<html lang="pt-BR">
 
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vulnerabilidades</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Componentes/button.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/Pages/vulnerabilidades.css" />
    <style>
        .campo__label .obrigatorio {
            color: #e53935;
            margin-left: 2px;
        }
    </style>
</head>
 
<body>
    <?php $tituloPagina = 'Vulnerabilidades';
    $contagem = array_count_values(array_column($vulnerabilidades ?? [], 'severidade_vulnerabilidade'));
    include_once 'Components/menu.php'; ?>
    <main>
        <div class="barra-acoes">
            <button class="btn-novo-cadastro" data-modal-target="modalVulnerabilidade">
                <i class="fa-solid fa-plus"></i>Nova Vulnerabilidade
            </button>
        </div>
 
        <div class="cards-resumo">
            <div class="card-resumo">
                <span class="card-label">Críticas</span>
                <span class="card-valor critica" id="qtdCriticas"><?= $contagem['CRITICA'] ?? 0 ?></span>
            </div>
            <div class="card-resumo">
                <span class="card-label">Altas</span>
                <span class="card-valor alta" id="qtdAltas"><?= $contagem['ALTA'] ?? 0 ?></span>
            </div>
            <div class="card-resumo">
                <span class="card-label">Médias</span>
                <span class="card-valor media" id="qtdMedias"><?= $contagem['MEDIA'] ?? 0 ?></span>
            </div>
            <div class="card-resumo">
                <span class="card-label">Baixas</span>
                <span class="card-valor baixa" id="qtdBaixas"><?= $contagem['BAIXA'] ?? 0 ?></span>
            </div>
        </div>
 
        <div class="tabela-wrapper">
            <table class="tabela" id="tabela">
                <thead>
                    <tr>
                        <th data-col="0">
                            <span class="th-label">ID <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="1">
                            <span class="th-label">Título <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="2">
                            <span class="th-label">Ativo <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="3">
                            <span class="th-label">Resumo <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="4" data-tipo="risco" data-filtro="lista">
                            <span class="th-label">Severidade <i class="fa-solid fa-sort sort-icon"></i> <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por Severidade"></i></span>
                        </th>
                        <th data-col="5" data-tipo="risco" data-filtro="lista">
                            <span class="th-label">Criticidade <i class="fa-solid fa-sort sort-icon"></i> <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por Criticidade"></i></span>
                        </th>
                        <th data-col="6">
                            <span class="th-label">CVSS <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="corpoTabelaVulnerabilidades">
                    <?php foreach ($vulnerabilidades as $v):
                        $sev = strtolower($v['severidade_vulnerabilidade']);
                        $cvss = (float) $v['cvss'];
                        $crit = $cvss >= 9 ? 'critica' : ($cvss >= 7 ? 'alta' : ($cvss >= 4 ? 'media' : 'baixa'));
                        $rotulos = ['critica' => 'Crítica', 'alta' => 'Alta', 'media' => 'Média', 'baixa' => 'Baixa'];
                    ?>
                        <tr>
                            <td>
                                <div class="id-cell">
                                    <div class="id-info">
                                        <span class="id-num">VulnID - <?= str_pad((int) $v['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                        <?php if (!empty($v['cve'])): ?>
                                            <span class="contrib"><?= htmlspecialchars($v['cve']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($v['nome']) ?></td>
                            <td class="ativo-link"><?= htmlspecialchars($v['projeto_nome'] ?? '-') ?></td>
                            <td class="resumo-texto"><?= htmlspecialchars($v['descricao'] ?? '') ?></td>
                            <td><span class="badge badge-<?= $sev ?>"><?= $rotulos[$sev] ?? htmlspecialchars($v['severidade_vulnerabilidade']) ?></span></td>
                            <td><span class="badge badge-<?= $crit ?>"><?= $rotulos[$crit] ?></span></td>
                            <td class="cvss"><?= number_format($cvss, 1) ?></td>
                            <td>
                                <div class="acoes">
                                    <button title="Visualizar" aria-label="Visualizar" data-id="<?= (int) $v['id'] ?>">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                    <button class="tabela-btn-editar" title="Editar" aria-label="Editar" data-id="<?= (int) $v['id'] ?>"
                                        data-vuln="<?= htmlspecialchars(json_encode($v, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" class="rodape-tabela">
                            <div class="paginacao"></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
 
        <div class="modal-overlay" id="modalVulnerabilidade">
            <div class="modal modal--xl">
 
                <div class="modal__header">
                    <div class="modal__header-icone">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="modal__header-texto">
                        <h2 class="modal__titulo" id="tituloModalVuln">Nova Vulnerabilidade</h2>
                        <p class="modal__subtitulo">Informação da vulnerabilidade encontrada.</p>
                    </div>
                    <button type="button" class="modal__fechar" data-modal-close>&times;</button>
                </div>
 
                <div class="modal__body">
                    <div class="modal-secao__titulo">
                        <i class="modal-secao__titulo-icone fa-solid fa-circle-info"></i>
                        <strong>Dados da Vulnerabilidade</strong>
                    </div>
 
                    <!--
                        TODO: falta um campo para projeto_id.
                        O Model exige projeto_id no INSERT (INTO vulnerabilidade (projeto_id, ...)),
                        mas não existia nenhum input/select para isso no modal.
                        Se o projeto já é conhecido pelo contexto da página (ex: veio via URL
                        ?projeto_id=123), use um input hidden, exemplo:
                        <input type="hidden" id="projetoId" name="projeto_id" value="<?= (int) ($_GET['projeto_id'] ?? 0) ?>">
                        Caso contrário, adicione um <select> para o usuário escolher o projeto.
                    -->
 
                    <input type="hidden" id="vulnId" name="id" value="" />

                    <div class="campo">
                        <label class="campo__label campo__label--obrigatorio" for="projetoId">Projeto:</label>
                        <div class="campo__select-wrapper">
                            <select class="campo__select" id="projetoId" name="projeto_id" required>
                                <option value="" disabled selected>Selecione o projeto</option>
                                <?php foreach ($projetos as $p): ?>
                                    <option value="<?= (int) $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="campo__select-seta"><i class="fa-solid fa-chevron-down"></i></span>
                        </div>
                    </div>

                    <div class="modal-grade modal-grade--3">
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio" for="nomeVuln">Nome da Vulnerabilidade:</label>
                            <input class="campo__input" type="text" id="nomeVuln" name="nomeVuln" placeholder="Ex: SQL Injection" maxlength="80" required />
                        </div>
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio" for="cvssScore">CVSS Score:</label>
                            <input class="campo__input" type="text" id="cvssScore" name="cvssScore" placeholder="0.0 - 10.0" inputmode="decimal" maxlength="4" required />
                        </div>
                        <div class="campo">
                            <label class="campo__label" for="cve">CVE:</label>
                            <!--
                                CORRIGIDO: removido value="CVE-". Esse valor pré-preenchido
                                fazia o campo (opcional) chegar como "CVE-" no back-end quando
                                o usuário não mexia nele, o que falhava na validação de regex
                                (^CVE-\d{4}-\d{4,}$) e bloqueava o cadastro/edição inteiro.
                            -->
                            <input class="campo__input" type="text" id="cve" name="cve" placeholder="Ex: CVE-2024-0001" maxlength="20" pattern="CVE-\d{4}-\d{4,}" inputmode="numeric" title="formato: CVE-AAAA-NNNN" />
                        </div>
                    </div>
 
                    <div class="campo">
                        <label class="campo__label campo__label--obrigatorio" for="descricao">Descrição:</label>
                        <input class="campo__input" type="text" id="descricao" name="descricao" placeholder="Descreva a Vulnerabilidade" maxlength="100" required />
                    </div>
 
                    <div class="modal-grade">
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio" for="descTecnica">Descrição Técnica:</label>
                            <textarea class="campo__textarea" id="descTecnica" name="descTecnica" placeholder="Descreva a vulnerabilidade em detalhes" maxlength="150" required></textarea>
                        </div>
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio" for="impactos">Impactos:</label>
                            <textarea class="campo__textarea" id="impactos" name="impactos" placeholder="Descreva o impacto potencial" maxlength="100" required></textarea>
                        </div>
                    </div>
 
                    <div class="modal-grade">
                        <div class="campo">
                            <label class="campo__label" for="responsavel">Responsável:</label>
                            <input class="campo__input" type="text" id="responsavel" name="responsavel" placeholder="Nome do Responsável" maxlength="100" />
                        </div>
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio" for="severidade">Severidade:</label>
                            <div class="campo__select-wrapper">
                                <!--
                                    CORRIGIDO: os values agora batem exatamente (maiúsculas)
                                    com Vulnerabilidades::SEVERIDADES_VALIDAS = ['ALTA','BAIXA','CRITICA','MEDIA'].
                                    Antes eram minúsculos ("alta", "baixa"...) e o in_array()
                                    do Model usa comparação estrita, então nunca batia —
                                    isso derrubava qualquer tentativa de salvar com "Severidade inválida".
                                -->
                                <select class="campo__select" id="severidade" name="severidade" required>
                                    <option value="" disabled selected>Selecione a severidade</option>
                                    <option value="ALTA">Alta</option>
                                    <option value="BAIXA">Baixa</option>
                                    <option value="CRITICA">Crítica</option>
                                    <option value="MEDIA">Média</option>
                                </select>
                                <span class="campo__select-seta"><i class="fa-solid fa-chevron-down"></i></span>
                            </div>
                        </div>
                    </div>
 
                    <div class="modal-grade">
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio" for="categoria">Categoria:</label>
                            <div class="campo__select-wrapper">
                                <!--
                                    CORRIGIDO: mesmo problema da severidade. Values agora batem
                                    com Vulnerabilidades::CATEGORIAS_VALIDAS =
                                    ['API','Aplicação Web','Infraestrutura','Mobile','Rede'].
                                -->
                                <select class="campo__select" id="categoria" name="categoria" required>
                                    <option value="" disabled selected>Selecione a categoria</option>
                                    <option value="API">API</option>
                                    <option value="Aplicação Web">Aplicação Web</option>
                                    <option value="Infraestrutura">Infraestrutura</option>
                                    <option value="Mobile">Mobile</option>
                                    <option value="Rede">Rede</option>
                                </select>
                                <span class="campo__select-seta"><i class="fa-solid fa-chevron-down"></i></span>
                            </div>
                        </div>
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio" for="status">Status:</label>
                            <div class="campo__select-wrapper">
                                <!--
                                    ATENÇÃO: este campo "status" (aberta/aceita/corrigida/em-analise/
                                    falso-positivo) não existe em nenhum lugar do Model ou da tabela
                                    "vulnerabilidade" que foi enviada. Só existe a coluna "habilitado"
                                    (0/1), que é outra coisa (ativo/inativo).
                                    Se esse campo for necessário, é preciso:
                                    1) adicionar a coluna correspondente na tabela do banco, e
                                    2) o Model/Controller precisam ler e gravar esse valor.
                                    Do jeito que está, o valor selecionado aqui é descartado
                                    e nunca chega a ser salvo.
                                -->
                                <select class="campo__select" id="status" name="status" required>
                                    <option value="" disabled selected>Selecione o status</option>
                                    <option value="ABERTA">Aberta</option>
                                    <option value="ACEITA">Aceita</option>
                                    <option value="CORRIGIDA">Corrigida</option>
                                    <option value="EM_ANALISE">Em Análise</option>
                                    <option value="FALSO_POSITIVO">Falso Positivo</option>
                                </select>
                                <span class="campo__select-seta"><i class="fa-solid fa-chevron-down"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
 
                <footer class="modal__footer">
                    <button class="btn-cancelar" data-modal-close>Cancelar</button>
                    <button class="btn-botao-verde" id="btnSalvar">Salvar</button>
                </footer>
 
            </div>
        </div>
    </main>
 
    <script>
        window.baseUrl = <?= json_encode(BASE_URL) ?>;
    </script>
    <script src="<?= BASE_URL ?>app/assets/JS/componentes/tabela.js"></script>
    <script src="<?= BASE_URL ?>app/assets/JS/componentes/filtros-tabela.js"></script>
    <script src="<?= BASE_URL ?>app/assets/JS/componentes/modal.js"></script>
    <script src="<?= BASE_URL ?>app/assets/JS/Vulnerabilidades.js"></script>
</body>
 
</html>
