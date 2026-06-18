<?php
    require "../Model/Database/Empresa.php";
    require "../Model/Database/Endereco.php";

    $empresa = new Empresa();
    $endereco = new Endereco();

    $empresa->conectar("clientesbaikal","localhost","root","");
    $endereco->conectar("clientesbaikal","localhost","root","");

    if(isset($_GET['id_empresa']))
    {
        $id_update = addslashes($_GET['id_empresa']);
        $dados_empresa = $empresa->buscarDadosEmpresa($id_update);

        $id_endereco = $dados_empresa['endereco_id'];
        $dados_endereco = $endereco->buscarDadosEndereco($id_endereco);
    }

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

        if(!empty($nome_fantasia) && !empty($razao_social) && !empty($telefone) && !empty($cnpj))
        {
            $empresa->atualizarDadosEmpresa($id_update,$nome_fantasia,$razao_social,$cnpj,$email_contato,$telefone,$responsavel);
            $endereco->atualizarDadosEndereco($id_endereco,$cep,$rua,$numero,$complemento,$bairro,$cidade,$estado,$pais);

            header("location:listar.php");
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDITAR Empresa</title>
    <link rel="stylesheet" href="../assets/CSS/Componentes/menu-superior.css">
    <link rel="stylesheet" href="../assets/CSS/Componentes/menu-lateral.css">
    <link rel="stylesheet" href="../assets/CSS/style.css">
    <link rel="stylesheet" href="../assets/CSS/Componentes/button.css">
    <link rel="stylesheet" href="../assets/CSS/Componentes/tabela.css">
    <link rel="stylesheet" href="../assets/CSS/Pages/clientes.css">
</head>
<body>
    <h2 class="titulo-pagina">EDITAR EMPRESA</h2>
    <form method="post" class="modal-clientes-div-form">

        <div class="modal-clientes-corpo-form">

            <div class="modal-clientes-secao-label">
                <i class="fa-solid fa-file-lines"></i>
                <h3>Dados da Empresa</h3>
            </div>

            <div class="modal-clientes-form-row">

                <div class="modal-clientes-area-dados-cliente">
                    <label>Nome da Empresa</label>
                    <input
                        type="text"
                        name="nome_fantasia"
                        value="<?php echo $dados_empresa['nome_fantasia']; ?>"
                    />
                </div>

                <div class="modal-clientes-area-dados-cliente">
                    <label>Razão Social</label>
                    <input
                        type="text"
                        name="razao_social"
                        value="<?php echo $dados_empresa['razao_social']; ?>"
                    />
                </div>

                <div class="modal-clientes-area-dados-cliente-tel">
                    <label>Telefone</label>
                    <div class="modal-clientes-campo-telefone">
                        <i class="fa fa-phone"></i>
                        <input
                            type="text"
                            name="telefone"
                            value="<?php echo $dados_empresa['telefone']; ?>"
                        />
                    </div>
                </div>

                <div class="modal-clientes-area-dados-cliente-email">
                    <label>E-mail</label>
                    <div class="modal-clientes-campo-email">
                        <input
                            type="email"
                            name="email_contato"
                            value="<?php echo $dados_empresa['email_contato']; ?>"
                        />
                    </div>
                </div>

                <div class="modal-clientes-area-dados-cliente">
                    <label>CNPJ</label>
                    <input
                        type="text"
                        name="cnpj"
                        value="<?php echo $dados_empresa['cnpj']; ?>"
                    />
                </div>

            </div>

            <div class="modal-clientes-form-row">

                <div class="modal-clientes-area-dados-cliente area-numero">
                    <label>CEP</label>
                    <input
                        type="text"
                        name="cep"
                        value="<?php echo $dados_endereco['cep']; ?>"
                    />
                </div>

                <div class="modal-clientes-area-dados-cliente">
                    <label>Endereço</label>
                    <input
                        type="text"
                        name="rua"
                        value="<?php echo $dados_endereco['rua']; ?>"
                    />
                </div>

                <div class="modal-clientes-area-dados-cliente area-numero">
                    <label>Número</label>
                    <input
                        type="text"
                        name="numero"
                        value="<?php echo $dados_endereco['numero']; ?>"
                    />
                </div>

                <div class="modal-clientes-area-dados-cliente">
                    <label>Complemento</label>
                    <input
                        type="text"
                        name="complemento"
                        value="<?php echo $dados_endereco['complemento']; ?>"
                    />
                </div>

            </div>

            <div class="modal-clientes-form-row">

                <div class="modal-clientes-area-dados-cliente">
                    <label>Bairro</label>
                    <input
                        type="text"
                        name="bairro"
                        value="<?php echo $dados_endereco['bairro']; ?>"
                    />
                </div>

                <div class="modal-clientes-area-dados-cliente">
                    <label>Cidade</label>
                    <input
                        type="text"
                        name="cidade"
                        value="<?php echo $dados_endereco['cidade']; ?>"
                    />
                </div>

                <div class="modal-clientes-area-dados-cliente">
                    <label>Estado</label>
                    <input
                        type="text"
                        name="estado"
                        value="<?php echo $dados_endereco['estado']; ?>"
                    />
                </div>

                <div class="modal-clientes-area-dados-cliente">
                    <label>País</label>
                    <input
                        type="text"
                        name="pais"
                        value="<?php echo $dados_endereco['pais']; ?>"
                    />
                </div>

            </div>

            <div class="modal-clientes-secao-label">
                <i class="fa-solid fa-user"></i>
                <h3>Dados do Responsável</h3>
            </div>

            <div class="modal-clientes-form-row">

                <div class="modal-clientes-area-dados-cliente">
                    <label>Nome do Responsável</label>
                    <input
                        type="text"
                        name="responsavel"
                        value="<?php echo $dados_empresa['responsavel']; ?>"
                    />
                </div>

            </div>

            <div class="modal-clientes-botoes-form">
                <a href="listar.php" class="modal-clientes-btn-cancelar">
                    CANCELAR
                </a>

                <button type="submit" class="modal-clientes-btn-salvar">
                    SALVAR
                </button>
            </div>

        </div>

    </form>
</body>
</html>