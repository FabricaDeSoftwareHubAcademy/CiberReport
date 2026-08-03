<!doctype html>
<html lang="pt-BR">
 
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vulnerabilidades</title>
    <link rel="stylesheet" href="../assets/CSS/style.css">
    <link rel="stylesheet" href="../assets/CSS/Componentes/button.css">
    <link rel="stylesheet" href="../assets/CSS/Pages/vulnerabilidades.css" />
    <style>
        .campo__label .obrigatorio {
            color: #e53935;
            margin-left: 2px;
        }
    </style>
</head>
 
<body>
    <?php $tituloPagina = 'Vulnerabilidades';
    include_once 'Components/menu.php'; ?>
    <main>
        <div class="barra-acoes">
            <button class="btn-novo-cadastro" data-modal-target="modalVulnerabilidade">
                <i class="fa-solid fa-square-plus"></i>
                Nova Vulnerabilidade
            </button>
        </div>
 
        <div class="cards-resumo">
            <div class="card-resumo">
                <span class="card-label">Críticas</span>
                <span class="card-valor critica" id="qtdCriticas">0</span>
            </div>
            <div class="card-resumo">
                <span class="card-label">Altas</span>
                <span class="card-valor alta" id="qtdAltas">0</span>
            </div>
            <div class="card-resumo">
                <span class="card-label">Médias</span>
                <span class="card-valor media" id="qtdMedias">0</span>
            </div>
            <div class="card-resumo">
                <span class="card-label">Baixas</span>
                <span class="card-valor baixa" id="qtdBaixas">0</span>
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
                    <?php /*
                        Linhas removidas — dados mocados eram só placeholder de layout.
                        Aqui entra o loop PHP puxando do banco (PDO), algo como:
 
                        <?php foreach ($vulnerabilidades as $v): ?>
                            <tr>
                                <td>...<?= htmlspecialchars($v['id']) ?>...</td>
                                <td><?= htmlspecialchars($v['titulo']) ?></td>
                                <td class="ativo-link"><?= htmlspecialchars($v['ativo']) ?></td>
                                <td class="resumo-texto"><?= htmlspecialchars($v['resumo']) ?></td>
                                <td><span class="badge badge-<?= $v['severidade'] ?>"><?= ucfirst($v['severidade']) ?></span></td>
                                <td><span class="badge badge-<?= $v['criticidade'] ?>"><?= ucfirst($v['criticidade']) ?></span></td>
                                <td class="cvss"><?= htmlspecialchars($v['cvss']) ?></td>
                                <td>...botões de ação...</td>
                            </tr>
                        <?php endforeach; ?>
                    */ ?>
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
                        <h2 class="modal__titulo">Nova Vulnerabilidade</h2>
                        <p class="modal__subtitulo">Informação da vulnerabilidade encontrada.</p>
                    </div>
                    <button class="modal__fechar" data-modal-close>&#x2715;</button>
                </div>
 
                <div class="modal__body">
                    <div class="modal-secao__titulo">
                        <i class="modal-secao__titulo-icone fa-solid fa-circle-info"></i>
                        <strong>Dados da Vulnerabilidade</strong>
                    </div>
 
                    <div class="modal-grade modal-grade--3">
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio" for="nomeVuln">Nome da Vulnerabilidade:</label>
                            <input class="campo__input" type="text" id="nomeVuln" name="nomeVuln" placeholder="Ex: SQL Injection" maxlength="150" required />
                        </div>
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio" for="cvssScore">CVSS Score:</label>
                            <input class="campo__input" type="text" id="cvssScore" name="cvssScore" placeholder="0.0 - 10.0" inputmode="decimal" maxlength="4" required />
                        </div>
                        <div class="campo">
                            <label class="campo__label" for="cve">CVE:</label>
                            <input class="campo__input" type="text" id="cve" name="cve" placeholder="Ex: CVE-2024-0001" maxlength="20" pattern="CVE-\d{4}-\d{4,}" inputmode="numeric" title="formato: CVE-AAAA-NNNN" value="CVE-" />
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
                            <textarea class="campo__textarea" id="impactos" name="impactos" placeholder="Descreva o impacto potencial" maxlength="200" required></textarea>
                        </div>
                    </div>
 
                    <div class="modal-grade">
                        <div class="campo">
                            <label class="campo__label" for="responsavel">Responsável:</label>
                            <input class="campo__input" type="text" id="responsavel" name="responsavel" placeholder="Nome do Responsável" maxlength="20" />
                        </div>
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio" for="severidade">Severidade:</label>
                            <div class="campo__select-wrapper">
                                <select class="campo__select" id="severidade" name="severidade" required>
                                    <option value="" disabled selected>Selecione a severidade</option>
                                    <option value="alta">Alta</option>
                                    <option value="baixa">Baixa</option>
                                    <option value="critica">Crítica</option>
                                    <option value="media">Média</option>
                                </select>
                                <span class="campo__select-seta"><i class="fa-solid fa-chevron-down"></i></span>
                            </div>
                        </div>
                    </div>
 
                    <div class="modal-grade">
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio" for="categoria">Categoria:</label>
                            <div class="campo__select-wrapper">
                                <select class="campo__select" id="categoria" name="categoria" required>
                                    <option value="" disabled selected>Selecione a categoria</option>
                                    <option value="api">API</option>
                                    <option value="web">Aplicação Web</option>
                                    <option value="infra">Infraestrutura</option>
                                    <option value="mobile">Mobile</option>
                                    <option value="rede">Rede</option>
                                </select>
                                <span class="campo__select-seta"><i class="fa-solid fa-chevron-down"></i></span>
                            </div>
                        </div>
                        <div class="campo">
                            <label class="campo__label campo__label--obrigatorio" for="status">Status:</label>
                            <div class="campo__select-wrapper">
                                <select class="campo__select" id="status" name="status" required>
                                    <option value="" disabled selected>Selecione o status</option>
                                    <option value="aberta">Aberta</option>
                                    <option value="aceita">Aceita</option>
                                    <option value="corrigida">Corrigida</option>
                                    <option value="em-analise">Em Análise</option>
                                    <option value="falso-positivo">Falso Positivo</option>
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
 
    <script src="../assets/JS/componentes/tabela.js"></script>
    <script src="../assets/JS/componentes/modal.js"></script>
    <script src="app/JS/Vulnerabilidades.js"></script>
</body>