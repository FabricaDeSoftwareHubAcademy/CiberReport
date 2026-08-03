<?php

namespace Controller;

use Core\Controller;
use Empresa;
use Endereco;

require_once __DIR__ . "/../Model/EmpresaModel.php";
require_once __DIR__ . "/../Model/EnderecoModel.php";



class CadastroEmpresaController extends Controller
{
    private Empresa $empresa;
    private Endereco $endereco;

    public function __construct()
    {
        $conexao = require __DIR__ . "/../Model/conexao.php";
        $this->empresa = new Empresa($conexao);
        $this->endereco = new Endereco($conexao);
    }

    public function index()
    {
        $this->view('cliente_empresa');
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
        $telefone_responsavel = addslashes($_POST['telefone_responsavel'] ?? '');
        $email_responsavel = addslashes($_POST['email_responsavel'] ?? '');
        $cpf_responsavel = addslashes($_POST['cpf_responsavel'] ?? '');

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
            $email_contato, $telefone, $responsavel, $telefone_responsavel, $email_responsavel, $cpf_responsavel
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
        $telefone_responsavel = addslashes($_POST['telefone_responsavel'] ?? '');
        $email_responsavel = addslashes($_POST['email_responsavel'] ?? '');
        $cpf_responsavel = addslashes($_POST['cpf_responsavel'] ?? '');

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
            $email_contato, $telefone, $responsavel,  $telefone_responsavel, $email_responsavel, $cpf_responsavel
        );

        $this->endereco->atualizarDadosEnderecoEmpresa(
            $id_endereco, $cep, $rua, $numero, $complemento,
            $bairro, $cidade, $estado, $pais
        );

        return true;
    }


    public function alterarStatusClientes($id_empresa, $status)
    {
        $this->empresa->alterarStatusEmpresa($id_empresa, $status);
    }
}