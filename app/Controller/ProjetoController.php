<?php

namespace Controller;

require_once __DIR__ . "/../Model/ProjetoValidator.php";
require_once __DIR__ . "/../Model/EmpresaModel.php";

use Core\Controller;
use Empresa;
use Exception;
use Model\Andamento;
use Model\Projeto;
use Model\TipoPentest;
use Model\UsuarioModel;
use ProjetoValidator;

class ProjetoController extends Controller
{
    private $projeto;
    private $empresa;
    private $usuario;
    private $andamento;

    public function __construct()
    {
        require_once __DIR__ . '/../DAO/DAO.php';
        $conexao = \DAO\DAO::conexao();
        $this->projeto = new Projeto($conexao);
        $this->empresa = new Empresa($conexao);
        $this->usuario = new UsuarioModel($conexao);
        $this->andamento = new Andamento($conexao);
    }

    public function index()
    {
        $dadosModal = [
            'empresas' => htmlspecialchars(json_encode($this->listarEmpresasAtivas(), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'),
            'tiposPentest' => htmlspecialchars(json_encode($this->listarTiposPentestAtivos(), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'),
            'usuarios' => htmlspecialchars(json_encode($this->listarUsuariosAtivos(), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'),
            'projetos' => htmlspecialchars(json_encode($this->listarCompletos(), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'),
        ];

        $dadosAndamento = htmlspecialchars(json_encode($this->listarAndamentoCompleto(), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');

        $this->view('gerenciamento_projeto', ['dadosModal' => $dadosModal, 'dadosAndamento' => $dadosAndamento]);
    }

    public function listarAndamentoCompleto()
    {
        $dados = $this->projeto->listarDados();
        $porProjeto = [];

        foreach ($dados as $linha) {
            $idProjeto = (int) $linha['id'];
            $projeto = $this->andamento->buscarProjeto($idProjeto);

            if ($projeto === null) {
                continue;
            }

            $minutosConsumidos = $this->andamento->buscarHoras($idProjeto)['horas_consumidas_minutos'];
            $horasContratadas = (float) $projeto['horas_contratadas'];
            $minutosContratados = (int) round($horasContratadas * 60);
            $minutosRestantes = max(0, $minutosContratados - $minutosConsumidos);

            $vulnerabilidades = $this->andamento->buscarVulnerabilidades($idProjeto);
            $porSeveridade = ['CRITICA' => 0, 'ALTA' => 0, 'MEDIA' => 0, 'BAIXA' => 0, 'INFO' => 0];
            $porCategoria = [];
            foreach ($vulnerabilidades as $vuln) {
                $sev = $vuln['severidade_vulnerabilidade'];
                if (isset($porSeveridade[$sev])) {
                    $porSeveridade[$sev]++;
                }
                $cat = $vuln['categoria'] ?: 'Outros';
                $porCategoria[$cat] = ($porCategoria[$cat] ?? 0) + 1;
            }

            $checklist = $this->andamento->buscarChecklist($idProjeto);
            $checklistConcluidos = count(array_filter($checklist, fn($item) => (int) $item['concluido'] === 1));

            $porProjeto[$idProjeto] = [
                'id' => $idProjeto,
                'nome' => $projeto['nome'],
                'empresa' => $projeto['nome_fantasia'] ?: $projeto['razao_social'],
                'tipos_pentest' => $this->andamento->buscarTiposPentest($idProjeto),
                'nivel_sigilo' => $projeto['nivel_sigilo'],
                'modalidade' => $projeto['modalidade'],
                'data_inicio' => $projeto['data_inicio'],
                'data_fim_prevista' => $projeto['data_fim_prevista'],
                'horas_contratadas_minutos' => $minutosContratados,
                'horas_consumidas_minutos' => $minutosConsumidos,
                'horas_restantes_minutos' => $minutosRestantes,
                'vulnerabilidades' => $vulnerabilidades,
                'vulnerabilidades_por_severidade' => $porSeveridade,
                'vulnerabilidades_por_categoria' => $porCategoria,
                'equipe' => $this->andamento->buscarEquipe($idProjeto),
                'checklist' => $checklist,
                'checklist_concluidos' => $checklistConcluidos,
                'checklist_total' => count($checklist),
                'log' => $this->andamento->buscarLogAtividade($idProjeto),
            ];
        }

        return $porProjeto;
    }

    public function listar()
    {
        return $this->projeto->listarDados();
    }

    public function listarCompletos()
    {
        $projetos = $this->projeto->listarDados();

        foreach ($projetos as &$projeto) {
            $idProjeto = (int) $projeto['id'];
            $equipe = $this->projeto->buscarEquipe($idProjeto);

            $projeto['alvos'] = $this->projeto->buscarAlvos($idProjeto);
            $projeto['tipos_pentest_ids'] = $this->projeto->buscarTiposPentestIds($idProjeto);
            $projeto['lider_id'] = $equipe['lider_id'];
            $projeto['especialistas_ids'] = $equipe['especialistas_ids'];
        }

        return $projetos;
    }

    public function listarEmpresasAtivas()
    {
        return $this->empresa->listarEmpresasAtivasParaSelecao();
    }

    public function listarTiposPentestAtivos()
    {
        return TipoPentest::listarAtivosParaSelecao();
    }

    public function listarUsuariosAtivos()
    {
        return $this->usuario->listarAtivosParaSelecao();
    }

    public function cadastrar()
    {
        try {
            $dadosLimpos = ProjetoValidator::processarCadastro($_POST);
            
            $caminhoContrato = $this->processarUploadContrato();
            if ($caminhoContrato !== false) {
                $dadosLimpos['contrato'] = $caminhoContrato;
            }

            $idProjeto = $this->projeto->cadastrarProjeto($dadosLimpos);

            if ($idProjeto !== false) {
                $idUsuarioLogado = (int) ($_SESSION['usuario_id'] ?? $dadosLimpos['lider_tecnico_id']);
                $this->andamento->registrarLog($idProjeto, $idUsuarioLogado, 'PROJETO_CRIADO', 'Projeto criado e equipe alocada com sucesso.');

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
            } else {
                // Sem upload novo, preserva o contrato já salvo em vez de apagar a referência.
                $dadosLimpos['contrato'] = $this->projeto->buscarContratoAtual($dadosLimpos['id']) ?? '';
            }

            if (!isset($_POST['status'])) {
                // O formulário de edição não tem campo de status; sem isso, o
                // validador assume 'PLANEJADO' e sobrescreve status como
                // CONCLUIDO/EM_ANDAMENTO toda vez que o projeto é editado.
                $dadosLimpos['status'] = $this->projeto->buscarStatusAtual($dadosLimpos['id']) ?? $dadosLimpos['status'];
            }

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
