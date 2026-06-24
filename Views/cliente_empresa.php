<?php
require_once "../Model/Database/conexao.php";
require_once "../Model/Database/Empresa.php";
require_once "../Model/Database/Endereco.php";

$empresa = new Empresa();
$endereco = new Endereco();

$empresa->conectar($nome_banco, $host, $usuario_bd, $senha_bd);
$endereco->conectar($nome_banco, $host, $usuario_bd, $senha_bd);


$dados = $empresa->ListarDados();

$mensagem_erro = "";

if(isset($_POST['nome_fantasia']))
{
    $nome_fantasia = addslashes($_POST['nome_fantasia']);
    $razao_social = addslashes($_POST['razao_social']);
    $telefone = addslashes($_POST['telefone']);
    $email_contato = addslashes($_POST['email_contato']);
    $cnpj = addslashes($_POST['cnpj']);
    $responsavel = addslashes($_POST['responsavel']);

    $cep = addslashes($_POST['cep']);
    $rua = addslashes($_POST['rua']);
    $numero = addslashes($_POST['numero']);
    $complemento = addslashes($_POST['complemento']);
    $bairro = addslashes($_POST['bairro']);
    $cidade = addslashes($_POST['cidade']);
    $estado = addslashes($_POST['estado']);
    $pais = addslashes($_POST['pais']);

    if(!empty($nome_fantasia) && !empty($razao_social) && !empty($telefone) && !empty($email_contato) && !empty($cnpj) && !empty($responsavel) && !empty($cep) && !empty($rua) && !empty($numero) && !empty($bairro) && !empty($cidade) && !empty($estado))
    {
        $id_endereco_novo = $endereco->cadastrarEndereco($cep,$rua,$numero,$complemento,$bairro,$cidade,$estado,$pais);

        if($empresa->cadastrarEmpresa($id_endereco_novo,$nome_fantasia,$razao_social,$cnpj,$email_contato,$telefone,$responsavel))
        {
            header("location:clientes.php");
            exit;
        }
        else
        {
            $mensagem_erro = "Empresa já cadastrada!";
        }
    }
    else
    {
        $mensagem_erro = "Preencha todos os campos!";
    }
}



?>


<link rel="stylesheet" href="../assets/CSS/Componentes/button.css">
<link rel="stylesheet" href="../assets/CSS/Pages/clientes.css">
<link rel="stylesheet" href="../assets/CSS/Componentes/tabela.css">

<?php include 'menu.php'; ?>
<main>

    
    <section class="listar-clientes">
    
        <input type="checkbox" id="abrir-modal" style="display: none"
            <?php if(!empty($mensagem_erro)) echo 'checked'; ?> />
        <div class="button-cadastro">
            <label for="abrir-modal" class="modal-clientes-btn-abrir-sistema"><i class="fa-solid fa-plus"></i><span class="texto">Novo Cadastro</span></label>
        </div>
        <div class="modal-clientes-modal">
            <div class="modal-clientes-box">
                <div class="modal-clientes-area-titulo">
                    <div class="modal-clientes-titulo-flex">
                        <img src="../assets/img/icone_empresa.svg" alt="Empresa" class="modal-clientes-icone-topo" />
                        <div class="modal-clientes-titulo-modal">
                            <h2>Cadastro de Empresa</h2>
                            <p>Informações da empresa contratante e do responsável técnico</p>
                        </div>
                    </div>
                    <label for="abrir-modal" class="modal-clientes-fechar"><i class="fa-solid fa-xmark"></i></label>
                </div>
    
                <form action="" method="post" class="modal-clientes-div-form">
                    <div class="modal-clientes-corpo-form">
                        <div class="modal-clientes-secao-label">
                            <i class="fa-solid fa-file-lines"></i>
                            <h3>Dados da Empresa</h3>
                        </div>
    
                        <div class="modal-clientes-form-row">
                                <div class="modal-clientes-area-dados-cliente-empresa">
                                    <label>Nome da Empresa</label>
                                    <input type="text" name="nome_fantasia" placeholder="Digite o nome da Empresa" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente-racao-empresa">
                                    <label>Razão Social</label>
                                    <input type="text" name="razao_social" placeholder="Digite a razão social" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente-tel">
                                    <label>Telefone</label>
                                    <div class="modal-clientes-campo-telefone">
                                        <i class="fa fa-phone"></i>
                                        <input type="text" name="telefone" placeholder="(11) 9999-9999" />
                                    </div>
                                </div>
                                <div class="modal-clientes-area-dados-cliente-email">
                                    <label>E-mail</label>
                                    <div class="modal-clientes-campo-email">
                                        <i class="fa-solid fa-envelope"></i>
                                        <input type="email" name="email_contato" placeholder="email@.com" />
                                    </div>
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>CNPJ</label>
                                    <input type="text" name="cnpj" placeholder="23.456.789/0001-01" />
                                </div>
                        </div>
    
                        <div class="modal-clientes-form-row">
                                <div class="modal-clientes-area-dados-cliente area-numero">
                                    <label>CEP</label>
                                    <input type="text" name="cep" id="cep" placeholder="12345-678" onblur="buscarCep()" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>Endereço</label>
                                    <input type="text" name="rua" id="endereco" placeholder="Digite o endereço" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente area-numero">
                                    <label>Número</label>
                                    <input type="text" name="numero" placeholder="0000" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>Complemento</label>
                                    <input type="text" name="complemento" placeholder="Complemento" />
                                </div>
                        </div>
    
                        <div class="modal-clientes-form-row">
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>Bairro</label>
                                    <input type="text" name="bairro" id="bairro" placeholder="Digite o bairro" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>Cidade</label>
                                    <input type="text" name="cidade" id="cidade" placeholder="Selecione uma Cidade" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>Estado</label>
                                    <input type="text" name="estado" id="estado" placeholder="Selecione um Estado" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>País</label>
                                    <input type="text" name="pais" id="pais" value="Brasil" required/>
                                </div>
                        </div>
    
                        <div class="modal-clientes-secao-label">
                                <i class="fa-solid fa-user"></i>
                                <h3>Dados do Responsável</h3>
                        </div>
    
                        <div class="modal-clientes-form-row">
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>Nome do Responsável</label>
                                    <input type="text" name="responsavel" placeholder="Digite o nome" />
                                </div>
                                <div class="modal-clientes-area-dados-cliente-tel">
                                    <label>Telefone</label>
                                    <div class="modal-clientes-campo-telefone">
                                        <i class="fa fa-phone"></i>
                                        <input type="text" name="telefone_responsavel" placeholder="(11)99999-9999" />
                                    </div>
                                </div>
                                <div class="modal-clientes-area-dados-cliente-email">
                                    <label>E-mail</label>
                                    <div class="modal-clientes-campo-email">
                                        <i class="fa-solid fa-envelope"></i>
                                        <input type="email" name="email_responsavel" placeholder="email@.com" />
                                    </div>
                                </div>
                                <div class="modal-clientes-area-dados-cliente">
                                    <label>CPF</label>
                                    <input type="text" name="cpf" placeholder="000.000.000-00" />
                                </div>
                        </div>
                        <div class="modal-clientes-botoes-form">
                                <?php if(!empty($mensagem_erro)): ?>
                                    <p class="modal-clientes-msg-erro">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        <?php echo $mensagem_erro; ?>
                                    </p>
                                <?php endif; ?>
                                <label for="abrir-modal" class="modal-clientes-btn-cancelar">CANCELAR</label>
                                <button type="submit" class="modal-clientes-btn-salvar">SALVAR</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="tabela-wrapper">
            <table>
                <thead>
                    <tr>
                        <th data-col="0">
                            <span class="th-label">ID <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="1">
                            <span class="th-label">Nome da Empresa <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="2">
                            <span class="th-label">CNPJ <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="3">
                            <span class="th-label">Responsável <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="4">
                            <span class="th-label">Email <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="5">
                            <span class="th-label">Telefone <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th data-col="6">
                            <span class="th-label">Status <i class="fa-solid fa-sort sort-icon"></i></span>
                        </th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($dados as $empresa){ ?>
                    <tr>
                        <td>#<?php echo $empresa['id']; ?></td>
                        <td><?php echo $empresa['nome_fantasia']; ?></td>
                        <td><?php echo $empresa['cnpj']; ?></td>
                        <td><?php echo $empresa['responsavel']; ?></td>
                        <td><?php echo isset($empresa['email_contato']) ? $empresa['email_contato'] : '---'; ?></td>
                        <td><?php echo $empresa['telefone']; ?></td>
                        <td>
    
                            <span class="status status-concluido">Ativo</span>
                        </td>
                        <td>
                            <div class="acoes">
                                
                                <a href="editar_empresa.php?id_empresa=<?php echo $empresa['id']; ?>" class="btn-editar" title="Editar" aria-label="Editar">
                                    <button><i class="fa-solid fa-pen-to-square"></i></button>
                                </a>
                                <a href="excluir_empresa.php?id_empresa=<?php echo $empresa['id']; ?>" class="btn-excluir" title="Excluir" aria-label="Excluir">
                                    <button><i class="fa-solid fa-trash"></i></button>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
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
    </section>
</main>
    
    <script src="../Assets/JS/componentes/menu.js"></script> 
</html>




