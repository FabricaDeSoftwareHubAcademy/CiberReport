<?php

namespace Controller;

require_once __DIR__ . "/../Model/ProjetoValidator.php";

use Core\Controller;
use Exception;
use Model\Projeto;
use ProjetoValidator;

class ProjetoController extends Controller
{
    private $projeto;

    public function __construct()
    {
        $conexao = require __DIR__ . '/../Model/conexao.php';
        $this->projeto = new Projeto($conexao);
    }

    public function index()
    {
        $this->view('gerenciamento_projeto');
    }

    public function listar()
    {
        return $this->projeto->listarDados();
    }

    public function cadastrar()
    {
        try {
            $dadosLimpos = ProjetoValidator::processarCadastro($_POST);
            
            $caminhoContrato = $this->processarUploadContrato();
            if ($caminhoContrato !== false) {
                $dadosLimpos['contrato'] = $caminhoContrato;
            }

            $dadosLimpos['habilitado'] = 1;

            if ($this->projeto->cadastrarProjeto($dadosLimpos)) {
                return "Projeto cadastrado com sucesso!";
            } else {
                return "Erro ao cadastrar projeto: " . $this->projeto->msgErro;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function editar()
    {
        try {
            $dadosLimpos = ProjetoValidator::processarEdicao($_POST);
            
            $caminhoContrato = $this->processarUploadContrato();
            if ($caminhoContrato !== false) {
                $dadosLimpos['contrato'] = $caminhoContrato;
            }

            $dadosLimpos['habilitado'] = 1;

            if ($this->projeto->editarProjeto($dadosLimpos)) {
                return "Projeto atualizado com sucesso!";
            } else {
                return "Erro ao atualizar projeto: " . $this->projeto->msgErro;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function excluir($id)
    {
        if ($this->projeto->excluirProjeto((int)$id)) {
            return "Projeto inativado com sucesso!";
        } else {
            return "Erro ao inativar projeto: " . $this->projeto->msgErro;
        }
    }

    private function processarUploadContrato()
    {
        if (!isset($_FILES['contrato']) || $_FILES['contrato']['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($_FILES['contrato']['size'] > 5 * 1024 * 1024) {
            throw new Exception("O arquivo do contrato excede o limite de 5MB.");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['contrato']['tmp_name']);
        finfo_close($finfo);

        if ($mimeType !== 'application/pdf') {
            throw new Exception("Formato inválido. Apenas arquivos PDF são permitidos para o contrato.");
        }

        $extensao = 'pdf';
        $novoNome = hash('sha256', uniqid(rand(), true)) . '.' . $extensao;

        $diretorioDestino = __DIR__ . '/../uploads/contratos/';
        
        if (!is_dir($diretorioDestino)) {
            mkdir($diretorioDestino, 0755, true);
        }

        $caminhoFinal = $diretorioDestino . $novoNome;

        if (move_uploaded_file($_FILES['contrato']['tmp_name'], $caminhoFinal)) {
            return 'uploads/contratos/' . $novoNome;
        }

        throw new Exception("Falha ao salvar o arquivo do contrato.");
    }
}
