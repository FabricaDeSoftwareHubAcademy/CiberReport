<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>app/assets/CSS/style.css">
    <title>Banco de Conhecimento</title>
</head>
<body>
    <main>
        <div class="banco-conhecimento">
            <button class="btn-novo-cadastro" data-modal-target="modalNovaContribuiçao">
                <i class=" fa-solid fa-plus"></i>Conhecimento
            </button>
        </div>

        
        <div class="tabela-wrapper">
                    <table id="tabela">
                        <thead>
                            <tr>
                                <th data-col="0" data-filtro="numero">
                                    <span class="th-label">ID <i class="sort-icon"></i></span>
                                </th>
                                <th data-col="1" data-filtro="alfabetica">
                                    <span class="th-label">Nome do Pentest <i class="sort-icon"></i></span>
                                </th>
                                <th data-col="2">
                                    <span class="th-label">Breve Descrição <i class="sort-icon"></i></span>
                                </th>
                                <th data-col="3" data-filtro="alfabetica">
                                    <span class="th-label">Categoria <i class="sort-icon"></i></span>
                                </th>
                                <th data-col="4" data-filtro="alfabetica">
                                    <span class="th-label">Técnica <i class="sort-icon"></i></span>
                                </th>
                                <th data-col="5" data-tipo="risco">
                                    <span class="th-label">Nível de Risco <i class="sort-icon"></i></span>
                                </th>
                                <th data-col="6" data-filtro="alfabetica">
                                    <span class="th-label">Profundidade <i class="sort-icon"></i></span>
                                </th>
                                <th data-col="7" data-filtro="lista">
                                    <span class="th-label">Frameworks <i class="sort-icon"></i> <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por Frameworks"></i></span>
                                </th>
                                <th data-col="8" data-filtro="lista">
                                    <span class="th-label">Checklists <i class="sort-icon"></i> <i class="fa-solid fa-filter filtro-icon" role="button" aria-label="Filtrar por Checklists"></i></span>
                                </th>
                                <th data-col="9" class="col-status">
                                    <span class="th-label">Status <i class="sort-icon"></i></span>
                                </th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        
    </main>
</body>
</html>





                