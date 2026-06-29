<?php
    // require "../Model/Database/Perfil.php";
    // $perfil = new Perfil();
    // $perfil->conectar("clientesbaikal","localhost","root","");
?>

<!doctype html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Gerenciar Perfis de Acesso</title>
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
            integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
        />
        <link rel="stylesheet" href="../assets/CSS/style.css">
        <link rel="stylesheet" href="../assets/CSS/Componentes/button.css">
        <link rel="stylesheet" href="../assets/CSS/Pages/gerenciamento perfis.css">
    </head>

    <body>
        <?php  $tituloPagina = 'Nome da Página'; include_once 'menu.php'; ?>
            <!-- <div class="main-content"> -->
                <main>
                        <div class="perfis-container">

                            <div class="perfis-header">
                                <button class="btn-novo-cadastro">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Novo Cadastro</span>
                                </button>
                            </div>

                            <div class="perfis-corpo">

                                <div class="perfis-lista-tabs">
                                    <button class="btn-opcoes-perfil ativo" onclick="trocarPerfil(this, 'administrador')">Administrador</button>
                                    <button class="btn-opcoes-perfil" onclick="trocarPerfil(this, 'analista')">Analista</button>
                                    <button class="btn-opcoes-perfil" onclick="trocarPerfil(this, 'cliente')">Cliente</button>
                                </div>

                                <form action="" method="post" class="perfis-permissoes">
                                    <div class="perfis-grade">

                                        <div class="perfis-grupo">
                                            <span class="perfis-grupo-titulo">Relatórios</span>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Visualizar Relatórios</span>
                                                    <span class="permissao-descricao">Ver relatórios de Segurança</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="vis_relatorios" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Editar  Relatórios</span>
                                                    <span class="permissao-descricao">Modificar relatórios existentes</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="edit_relatorios" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Criar Relatório</span>
                                                    <span class="permissao-descricao">Criar novos Relatórios</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="criar_relatorios" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Excluir Relatórios</span>
                                                    <span class="permissao-descricao">Remover Relatórios</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="excluir_relatorios">
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="perfis-grupo">
                                            <span class="perfis-grupo-titulo">Vulnerabilidade</span>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Visualizar Vulnerabilidades</span>
                                                    <span class="permissao-descricao">Acessar Lista de Vulnerabilidade</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="vis_vulnerabilidades" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Editar Vulnerabilidades</span>
                                                    <span class="permissao-descricao">Editar Vulnerabilidades existentes</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="edit_vulnerabilidades" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Criar Vulnerabilidade</span>
                                                    <span class="permissao-descricao">Criar novas vulnerabilidades</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="criar_vulnerabilidades" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Excluir Vulnerabilidade</span>
                                                    <span class="permissao-descricao">Remover vulnerabilidades</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="excluir_vulnerabilidades">
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="perfis-grupo">
                                            <span class="perfis-grupo-titulo">Logs</span>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Visualizar Logs</span>
                                                    <span class="permissao-descricao">Acessar Logs do Sistema</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="vis_logs" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Editor Logs</span>
                                                    <span class="permissao-descricao">Editar Logs do Sistema</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="edit_logs" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Exportar Logs</span>
                                                    <span class="permissao-descricao">Exportar Logs do Sistema</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="exportar_logs" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Excluir Logs</span>
                                                    <span class="permissao-descricao">Limpar Logs do Sistema</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="excluir_logs">
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="perfis-grupo">
                                            <span class="perfis-grupo-titulo">Clientes</span>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Visualizar Clientes</span>
                                                    <span class="permissao-descricao">Visualizar Dados dos Clientes</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="vis_clientes" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Editar Clientes</span>
                                                    <span class="permissao-descricao">Editar Informações do Cliente</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="edit_clientes" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Criar Clientes</span>
                                                    <span class="permissao-descricao">Criar novos clientes</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="criar_clientes" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Excluir Clientes</span>
                                                    <span class="permissao-descricao">Remover clientes</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="excluir_clientes" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="perfis-grupo">
                                            <span class="perfis-grupo-titulo">Usuários</span>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Visualizar Usuários</span>
                                                    <span class="permissao-descricao">Visualizar Usuários Cadastrados</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="vis_usuarios" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Editar Clientes</span>
                                                    <span class="permissao-descricao">Editar Usuários Cadastrados</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="edit_usuarios" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Criar Clientes</span>
                                                    <span class="permissao-descricao">Criar Usuários Cadastrados</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="criar_usuarios" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Excluir Clientes</span>
                                                    <span class="permissao-descricao">Remover Usuários Cadastrados</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="excluir_usuarios" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="perfis-grupo">
                                            <span class="perfis-grupo-titulo">Administração</span>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Gerenciar Equipe</span>
                                                    <span class="permissao-descricao">Adicionar/Editar/Remover Membros</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="ger_equipe" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div class="permissao-item">
                                                <div class="permissao-info">
                                                    <span class="permissao-nome">Gerenciar Projetos</span>
                                                    <span class="permissao-descricao">Adicionar/Editar/Excluir Projetos</span>
                                                </div>
                                                <label class="switch">
                                                    <input type="checkbox" name="ger_projetos" checked>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="perfis-rodape">
                                        <button type="submit" class="btn-botao-verde">Salvar</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                </main> 
            <!-- </div> -->
        </div>
        <script>
            function trocarPerfil(botao, perfil) {
                document.querySelectorAll('.btn-opcoes-perfil').forEach(btn => btn.classList.remove('ativo'));
                botao.classList.add('ativo');
            }
        </script>

    </body>
</html>