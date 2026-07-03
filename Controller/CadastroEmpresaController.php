<?php
require_once __DIR__ . "/../Model/conexao.php";
require_once __DIR__ . "/../Model/Database/Empresa.php";
require_once __DIR__ . "/../Model/Database/Endereco.php";


class CadastroEmpresaController
{
    private $empresa;
    private $endereco;

    public function __construct()
    {
        global $conexao;
        $this->empresa = new Empresa($conexao);
        $this->endereco = new Endereco($conexao);
    }

    public function listarEmpresa()
    {
        return $this->empresa->ListarDadosEmpresa();
    }

    public function buscarDadosEmpresa($id_empresa)
    {
        return $this->empresa->buscarDadosEmpresa($id_empresa);
    }

    public function buscarDadosEnderecoEmpresa($id_endereco)
    {
        return $this->endereco->buscarDadosEnderecoEmpresa($id_endereco);
    }

    public function cadastrarEmpresa()
    {
        $nome_fantasia = addslashes($_POST['nome_fantasia'] ?? '');
        $razao_social = addslashes($_POST['razao_social'] ?? '');
        $telefone = addslashes($_POST['telefone'] ?? '');
        $email_contato = addslashes($_POST['email_contato'] ?? '');
        $cnpj = addslashes($_POST['cnpj'] ?? '');
        $responsavel = addslashes($_POST['responsavel'] ?? '');

        $cep = addslashes($_POST['cep'] ?? '');
        $rua = addslashes($_POST['rua'] ?? '');
        $numero = addslashes($_POST['numero'] ?? '');
        $complemento = addslashes($_POST['complemento'] ?? '');
        $bairro = addslashes($_POST['bairro'] ?? '');
        $cidade = addslashes($_POST['cidade'] ?? '');
        $estado = addslashes($_POST['estado'] ?? '');
        $pais = addslashes($_POST['pais'] ?? '');

        if (
            empty($nome_fantasia) || empty($razao_social) || empty($telefone) ||
            empty($email_contato) || empty($cnpj) || empty($responsavel) ||
            empty($cep) || empty($rua) || empty($numero) ||
            empty($bairro) || empty($cidade) || empty($estado)
        ) {
            return "Preencha todos os campos!";
        }

        if ($this->empresa->buscarPorCnpj($cnpj)) {
            return "Empresa já cadastrada!";
        }

        $id_endereco_novo = $this->endereco->cadastrarEnderecoEmpresa(
            $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $pais
        );

        $this->empresa->cadastrarEmpresa(
            $id_endereco_novo, $nome_fantasia, $razao_social, $cnpj,
            $email_contato, $telefone, $responsavel
        );

        return true;
    }

    public function editarEmpresa()
    {
        $id_empresa = addslashes($_POST['id_empresa'] ?? '');
        $id_endereco = addslashes($_POST['id_endereco'] ?? '');

        $nome_fantasia = addslashes($_POST['nome_fantasia'] ?? '');
        $razao_social = addslashes($_POST['razao_social'] ?? '');
        $telefone = addslashes($_POST['telefone'] ?? '');
        $email_contato = addslashes($_POST['email_contato'] ?? '');
        $cnpj = addslashes($_POST['cnpj'] ?? '');
        $responsavel = addslashes($_POST['responsavel'] ?? '');

        $cep = addslashes($_POST['cep'] ?? '');
        $rua = addslashes($_POST['rua'] ?? '');
        $numero = addslashes($_POST['numero'] ?? '');
        $complemento = addslashes($_POST['complemento'] ?? '');
        $bairro = addslashes($_POST['bairro'] ?? '');
        $cidade = addslashes($_POST['cidade'] ?? '');
        $estado = addslashes($_POST['estado'] ?? '');
        $pais = addslashes($_POST['pais'] ?? '');

        $this->empresa->atualizarDadosEmpresa(
            $id_empresa, $nome_fantasia, $razao_social, $cnpj,
            $email_contato, $telefone, $responsavel
        );

        $this->endereco->atualizarDadosEnderecoEmpresa(
            $id_endereco, $cep, $rua, $numero, $complemento,
            $bairro, $cidade, $estado, $pais
        );

        return true;
    }

    public function excluirClientes($id_empresa)
    {
        $dados_empresa = $this->empresa->buscarDadosEmpresa($id_empresa);
        $id_endereco = $dados_empresa['endereco_id'];

        $this->empresa->excluirEmpresa($id_empresa);
        $this->endereco->excluirEnderecoEmpresa($id_endereco);
    }

    public function alterarStatusClientes($id_empresa, $status)
    {
        $this->empresa->alterarStatusEmpresa($id_empresa, $status);
    }
}