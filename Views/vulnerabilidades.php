<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vulnerabilidades</title>
    <link rel="stylesheet" href="../assets/CSS/style.css">
    <link rel="stylesheet" href="../assets/CSS/Componentes/button.css">
    <link rel="stylesheet" href="../assets/CSS/Pages/vulnerabilidades.css" />
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
                <span class="card-valor critica">0.3</span>
            </div>
            <div class="card-resumo">
                <span class="card-label">Altas</span>
                <span class="card-valor alta">0.0</span>
            </div>
            <div class="card-resumo">
                <span class="card-label">Médias</span>
                <span class="card-valor media">0.1</span>
            </div>
            <div class="card-resumo">
                <span class="card-label">Baixas</span>
                <span class="card-valor baixa">0.0</span>
            </div>
        </div>

        <div class="tabela-wrapper">
            <table class="tabela">
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
                        <th data-col="4" data-tipo="data">
                            <span class="th-label">Severidade <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="5" data-tipo="data">
                            <span class="th-label">Criticidade <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="6">
                            <span class="th-label">CVSS <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="id-cell">
                                <div class="folder-icon critica-bg">
                                    <i class="fa-solid fa-folder"></i>
                                </div>
                                <div class="id-info">
                                    <span class="id-num">VulnID - 001</span>
                                    <span class="contrib">9 Contribuições</span>
                                    <div class="avatares">
                                        <img src="https://i.pravatar.cc/20?img=1" alt="" />
                                        <img src="https://i.pravatar.cc/20?img=2" alt="" />
                                        <img src="https://i.pravatar.cc/20?img=3" alt="" />
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>Injeção de SQL baseado em tempo (SQL Injection)</td>
                        <td class="ativo-link">https://sistema.xpto.com.br</td>
                        <td class="resumo-texto">O problema identificado na aplicação foi...</td>
                        <td><span class="badge badge-critica">Crítica</span></td>
                        <td><span class="badge badge-critica">Crítica</span></td>
                        <td class="cvss">9.8</td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                                <button class="tabela-btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                                <button class="tabela-btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="id-cell">
                                <div class="folder-icon critica-bg">
                                    <i class="fa-solid fa-folder"></i>
                                </div>
                                <div class="id-info">
                                    <span class="id-num">VulnID - 002</span>
                                    <span class="contrib">6 Contribuições</span>
                                    <div class="avatares">
                                        <img src="https://i.pravatar.cc/20?img=4" alt="" />
                                        <img src="https://i.pravatar.cc/20?img=5" alt="" />
                                        <img src="https://i.pravatar.cc/20?img=6" alt="" />
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>Script entre sites armazenados (XSS)</td>
                        <td class="ativo-link">https://sistema.xpto.com.br</td>
                        <td class="resumo-texto">O problema identificado na aplicação foi...</td>
                        <td><span class="badge badge-alta">Alta</span></td>
                        <td><span class="badge badge-critica">Crítica</span></td>
                        <td class="cvss">9.3</td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                                <button class="tabela-btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                                <button class="tabela-btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="id-cell">
                                <div class="folder-icon media-bg">
                                    <i class="fa-solid fa-folder"></i>
                                </div>
                                <div class="id-info">
                                    <span class="id-num">VulnID - 003</span>
                                    <span class="contrib">4 Contribuições</span>
                                    <div class="avatares">
                                        <img src="https://i.pravatar.cc/20?img=7" alt="" />
                                        <img src="https://i.pravatar.cc/20?img=8" alt="" />
                                        <img src="https://i.pravatar.cc/20?img=9" alt="" />
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>Possibilidade de falsificação.</td>
                        <td class="ativo-link">https://sistema.xpto.com.br</td>
                        <td class="resumo-texto">O problema identificado na aplicação foi...</td>
                        <td><span class="badge badge-media">Média</span></td>
                        <td><span class="badge badge-media">Médio</span></td>
                        <td class="cvss">7.2</td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                                <button class="tabela-btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                                <button class="tabela-btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="id-cell">
                                <div class="folder-icon critica-bg">
                                    <i class="fa-solid fa-folder"></i>
                                </div>
                                <div class="id-info">
                                    <span class="id-num">VulnID - 004</span>
                                    <span class="contrib">3 Contribuições</span>
                                    <div class="avatares">
                                        <img src="https://i.pravatar.cc/20?img=10" alt="" />
                                        <img src="https://i.pravatar.cc/20?img=11" alt="" />
                                        <img src="https://i.pravatar.cc/20?img=12" alt="" />
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>Fraqueza na proteção contra ataques de força bruta.</td>
                        <td class="ativo-link">https://sistema.xpto.com.br</td>
                        <td class="resumo-texto">O problema identificado na aplicação foi...</td>
                        <td><span class="badge badge-alta">Alta</span></td>
                        <td><span class="badge badge-alta">Alta</span></td>
                        <td class="cvss">7.9</td>
                        <td>
                            <div class="acoes">
                                <button title="Visualizar" aria-label="Visualizar">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                                <button class="tabela-btn-editar" title="Editar" aria-label="Editar">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                                <button class="tabela-btn-excluir" title="Excluir" aria-label="Excluir">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" class="rodape-tabela">
                            <div class="paginacao">
                                <button class="pag-btn" aria-label="Página anterior">Anterior</button>
                                <button class="pag-num ativo" aria-label="Página 1" aria-current="page">1</button>
                                <button class="pag-num" aria-label="Página 2">2</button>
                                <button class="pag-num" aria-label="Página 3">3</button>
                                <button class="pag-num" aria-label="Página 4">4</button>
                                <button class="pag-num" aria-label="Página 5">5</button>
                                <button class="pag-num" aria-label="Página 6">6</button>
                                <button class="pag-btn" aria-label="Próxima página">Próximo</button>
                            </div>
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
                            <label class="campo__label" for="nomeVuln">Nome da Vulnerabilidade:</label>
                            <input class="campo__input" type="text" id="nomeVuln" name="nomeVuln" placeholder="Ex: SQL Injection" />
                        </div>
                        <div class="campo">
                            <label class="campo__label" for="cvssScore">CVSS Score:</label>
                            <input class="campo__input" type="text" id="cvssScore" name="cvssScore" placeholder="0.0 - 10.0" />
                        </div>
                        <div class="campo">
                            <label class="campo__label" for="cve">CVE:</label>
                            <input class="campo__input" type="text" id="cve" name="cve" placeholder="Ex: CVE-2024-0001" />
                        </div>
                    </div>

                    <div class="campo">
                        <label class="campo__label" for="descricao">Descrição:</label>
                        <input class="campo__input" type="text" id="descricao" name="descricao" placeholder="Descreva a Vulnerabilidade" />
                    </div>

                    <div class="modal-grade">
                        <div class="campo">
                            <label class="campo__label" for="descTecnica">Descrição Técnica:</label>
                            <textarea class="campo__textarea" id="descTecnica" name="descTecnica" placeholder="Descreva a vulnerabilidade em detalhes"></textarea>
                        </div>
                        <div class="campo">
                            <label class="campo__label" for="impactos">Impactos:</label>
                            <textarea class="campo__textarea" id="impactos" name="impactos" placeholder="Descreva o impacto potencial"></textarea>
                        </div>
                    </div>

                    <div class="modal-grade">
                        <div class="campo">
                            <label class="campo__label" for="responsavel">Responsável:</label>
                            <input class="campo__input" type="text" id="responsavel" name="responsavel" placeholder="Nome do Responsável" />
                        </div>
                        <div class="campo">
                            <label class="campo__label" for="severidade">Severidade:</label>
                            <div class="campo__select-wrapper">
                                <select class="campo__select" id="severidade" name="severidade">
                                    <option value="" disabled selected>Selecione a severidade</option>
                                    <option value="critica">Crítica</option>
                                    <option value="alta">Alta</option>
                                    <option value="media">Média</option>
                                    <option value="baixa">Baixa</option>
                                </select>
                                <span class="campo__select-seta"><i class="fa-solid fa-chevron-down"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-grade">
                        <div class="campo">
                            <label class="campo__label" for="categoria">Categoria:</label>
                            <div class="campo__select-wrapper">
                                <select class="campo__select" id="categoria" name="categoria">
                                    <option value="" disabled selected>Selecione a categoria</option>
                                    <option value="web">Aplicação Web</option>
                                    <option value="rede">Rede</option>
                                    <option value="infra">Infraestrutura</option>
                                    <option value="mobile">Mobile</option>
                                    <option value="api">API</option>
                                </select>
                                <span class="campo__select-seta"><i class="fa-solid fa-chevron-down"></i></span>
                            </div>
                        </div>
                        <div class="campo">
                            <label class="campo__label" for="status">Status:</label>
                            <div class="campo__select-wrapper">
                                <select class="campo__select" id="status" name="status">
                                    <option value="" disabled selected>Selecione o status</option>
                                    <option value="aberta">Aberta</option>
                                    <option value="em-analise">Em Análise</option>
                                    <option value="corrigida">Corrigida</option>
                                    <option value="aceita">Aceita</option>
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
</body>

</html>
