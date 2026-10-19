<?php

namespace Controller;

require_once __DIR__ . "/../Model/ProjetoValidator.php";
require_once __DIR__ . "/../Model/EmpresaModel.php";

use Core\Controller;
use Core\DAO;
use DAO\ChecklistDAO;
use Empresa;
use Exception;
use Model\Andamento;
use Model\CategoriaPentestModel;
use Model\FrameworkModel;
use Model\ProjetoModel;
use Model\TipoPentestModel;
use Model\UsuarioModel;
use ProjetoValidator;
use ValidacaoProjetoException;
use Service\TipoPentestService;

class ProjetoController extends Controller
{
    private $projeto;
    private $empresa;
    private $usuario;
    private $andamento;

    public function __construct()
    {
        $conexao = DAO::conexao();
        $this->projeto = new ProjetoModel($conexao);
        $this->empresa = new Empresa($conexao);
        $this->usuario = new UsuarioModel($conexao);
        $this->andamento = new Andamento($conexao);
    }

    public function index()
    {
        $dadosModal = [
            'empresas' => htmlspecialchars(json_encode($this->listarEmpresasAtivas(), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'),
            'tiposPentest' => htmlspecialchars(json_encode($this->listarTiposPentestAtivos(), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'),
            'frameworks' => htmlspecialchars(json_encode((new FrameworkModel())->getAllRows(), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'),
            'usuarios' => htmlspecialchars(json_encode($this->listarUsuariosAtivos(), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'),
            'projetos' => htmlspecialchars(json_encode($this->listarCompletos(), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'),
        ];

        $dadosAndamento = htmlspecialchars(json_encode($this->listarAndamentoCompleto(), JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');

        // Os atalhos "+" do modal de projeto abrem os modais de cadastro de
        // tipo de pentest e de empresa; o primeiro precisa destas variáveis.
        $this->view('gerenciamento_projeto', $this->dadosModalTipoPentest() + ['dadosModal' => $dadosModal, 'dadosAndamento' => $dadosAndamento]);
    }

    /** Variáveis que Components/modais/tipo_pentest.php espera encontrar no escopo de quem o inclui. */
    private function dadosModalTipoPentest(): array
    {
        return [
            'categorias' => (new CategoriaPentestModel())->getAllRows(),
            'frameworks' => (new FrameworkModel())->getAllRows(),
            'checklists' => (new ChecklistDAO())->listarChecklistAtivos(),
            'rotulosTecnica' => TipoPentestModel::ROTULOS_TECNICA,
            'rotulosRisco' => TipoPentestModel::ROTULOS_RISCO,
            'classesRisco' => TipoPentestModel::CLASSES_RISCO,
            'rotulosProfundidade' => TipoPentestModel::ROTULOS_PROFUNDIDADE,
        ];
    }

    /**
     * POST /gerenciamento-projeto/cadastrar-tipo-pentest — cadastra o tipo de
     * pentest pelo atalho do bloco de pentest, sem recarregar a página. A
     * validação e a gravação são as do módulo de Pentest (TipoPentestService e
     * TipoPentestModel); aqui só muda a forma da resposta, que lá é redirect.
     */
    public function cadastrarTipoPentestJson()
    {
        try {
            $dados = TipoPentestService::processarCadastro($_POST);

            $model = new TipoPentestModel();
            foreach ($dados as $campo => $valor) {
                $model->$campo = $valor;
            }
            $model->id = null; // o atalho só cria; edição continua na tela de Pentest

            $model->save(); // o DAO preenche $model->id ao inserir
            $id = $model->id;
        } catch (Exception $e) {
            $this->json(['status' => 400, 'msg' => $e->getMessage()]);
        } catch (\Throwable $e) {
            $this->json(['status' => 400, 'msg' => 'Não foi possível salvar o tipo de pentest.']);
        }

        if (!$id) {
            $this->json(['status' => 400, 'msg' => 'Não foi possível salvar o tipo de pentest.']);
        }

        $this->json([
            'status' => 200,
            'msg' => 'Tipo de pentest cadastrado com sucesso.',
            'id' => (int) $id,
            'data' => $this->listarTiposPentestAtivos(),
        ]);
    }

    /** GET /gerenciamento-projeto/tipos-pentest — lista atualizada depois de um cadastro pelo atalho. */
    public function tiposPentestJson()
    {
        $this->json(['status' => 200, 'data' => $this->listarTiposPentestAtivos()]);
    }

    /**
     * POST /gerenciamento-projeto/cadastrar-empresa — cadastra a empresa pelo
     * atalho do campo Cliente, sem recarregar a página. A regra de cadastro é
     * a do próprio módulo de Clientes; aqui só muda a forma da resposta.
     */
    public function cadastrarEmpresaJson()
    {
        try {
            $resultado = (new EmpresaController())->cadastrarEmpresa();
        } catch (\Throwable $e) {
            // O cadastro de empresa não trata erro de banco; sem isto a resposta sairia como página de erro, não JSON.
            $this->json(['status' => 400, 'msg' => 'Não foi possível cadastrar a empresa. Confira os dados e tente novamente.']);
        }

        if ($resultado !== true) {
            $this->json(['status' => 400, 'msg' => is_string($resultado) ? $resultado : 'Não foi possível cadastrar a empresa.']);
        }

        $nova = $this->empresa->buscarPorCnpj(addslashes($_POST['cnpj'] ?? ''));

        $this->json([
            'status' => 200,
            'msg' => 'Empresa cadastrada com sucesso.',
            'id' => $nova ? (int) $nova['id'] : null,
            'data' => $this->listarEmpresasAtivas(),
        ]);
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
            // Pode ficar negativo de propósito: o projeto é contratado por horas,
            // e passar do contratado deve ser informado (não proibido nem escondido).
            $minutosRestantes = $minutosContratados - $minutosConsumidos;

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
                'modalidade' => implode(', ', $this->andamento->buscarModalidades($idProjeto)),
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
        $projetos = $this->projeto->listarDados();

        foreach ($projetos as &$projeto) {
            $projeto['modalidade'] = implode(', ', $this->andamento->buscarModalidades((int) $projeto['id']));
        }

        return $projetos;
    }

    public function listarCompletos()
    {
        $projetos = $this->projeto->listarDados();

        foreach ($projetos as &$projeto) {
            $idProjeto = (int) $projeto['id'];

            $projeto['lider_id'] = $this->projeto->buscarLiderId($idProjeto);
            $projeto['alvos'] = $this->projeto->buscarAlvos($idProjeto);
            $projeto['pentests'] = $this->projeto->buscarPentests($idProjeto);
        }

        return $projetos;
    }

    public function listarEmpresasAtivas()
    {
        return $this->empresa->listarEmpresasAtivasParaSelecao();
    }

    public function listarTiposPentestAtivos()
    {
        $tipos = $this->projeto->buscarTiposPentestAtivos();
        $frameworksPorTipo = $this->projeto->buscarFrameworksPorTipoPentest();
        $checklistsPorTipo = $this->projeto->buscarChecklistsPorTipoPentest();

        foreach ($tipos as &$tipo) {
            $tipo['frameworks_ids'] = $frameworksPorTipo[(int) $tipo['id']] ?? [];
            $tipo['checklists'] = $checklistsPorTipo[(int) $tipo['id']] ?? [];
        }

        return $tipos;
    }

    public function listarUsuariosAtivos()
    {
        return $this->usuario->listarPentestersAtivosParaSelecao();
    }

    /** O filtro da tela não basta (dá pra forjar o POST): o líder e os analistas de cada pentest também são checados no servidor. */
    private function validarEquipeElegivel(array $dadosLimpos): void
    {
        $idsPermitidos = array_map('intval', array_column($this->listarUsuariosAtivos(), 'id'));
        $erros = [];

        if (!in_array((int) $dadosLimpos['lider_id'], $idsPermitidos, true)) {
            $erros['lider_id'] = 'O líder técnico precisa ser um usuário ativo com perfil Pentester. Escolha outro na lista.';
        }

        foreach ($dadosLimpos['pentests'] as $indice => $pentest) {
            foreach ($pentest['analistas_ids'] as $id) {
                if (!in_array((int) $id, $idsPermitidos, true)) {
                    $erros["pentests.{$indice}.analistas_ids"] = 'Há analista que não é um pentester ativo. Remova-o e escolha outro na lista.';
                }
            }
        }

        if ($erros) {
            throw new ValidacaoProjetoException($erros);
        }
    }

    /**
     * Resposta do salvar quando o modal envia por fetch (Accept: application/json):
     * em vez de recarregar a página, devolve os erros por campo para o modal
     * mostrar cada um no lugar certo, sem perder o que foi preenchido.
     */
    public function salvarJson(string $acao): void
    {
        $resultado = $acao === 'editar' ? $this->executarEdicao() : $this->executarCadastro();

        http_response_code($resultado['ok'] ? 200 : 422);
        $this->json($resultado);
    }

    /** Envio sem JavaScript (formulário comum): devolve só a mensagem para o aviso da página. */
    public function cadastrar()
    {
        return $this->executarCadastro()['msg'];
    }

    public function editar()
    {
        return $this->executarEdicao()['msg'];
    }

    /** @return array{ok: bool, msg: string, erros: array<string, string>} */
    private function executarCadastro(): array
    {
        try {
            $dadosLimpos = ProjetoValidator::processarCadastro($_POST);
            $this->validarEquipeElegivel($dadosLimpos);
            
            $caminhoContrato = $this->processarUploadContrato();
            if ($caminhoContrato !== false) {
                $dadosLimpos['contrato'] = $caminhoContrato;
            }

            $idProjeto = $this->projeto->cadastrarProjeto($dadosLimpos);

            if ($idProjeto !== false) {
                $idUsuarioLogado = (int) ($_SESSION['usuario_id'] ?? $dadosLimpos['lider_id']);
                $this->andamento->registrarLog($idProjeto, $idUsuarioLogado, 'PROJETO_CRIADO', 'Projeto criado e equipe alocada com sucesso.');

                return ['ok' => true, 'msg' => 'Projeto cadastrado com sucesso!', 'erros' => []];
            }

            return $this->falhaDeGravacao('Erro ao cadastrar projeto.');
        } catch (ValidacaoProjetoException $e) {
            return $this->falhaDeValidacao($e);
        } catch (Exception $e) {
            return ['ok' => false, 'msg' => 'Erro: ' . $e->getMessage(), 'erros' => ['geral' => $e->getMessage()]];
        }
    }

    /** @return array{ok: bool, msg: string, erros: array<string, string>} */
    private function executarEdicao(): array
    {
        try {
            $dadosLimpos = ProjetoValidator::processarEdicao($_POST);
            $this->validarEquipeElegivel($dadosLimpos);

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
                return ['ok' => true, 'msg' => 'Projeto atualizado com sucesso!', 'erros' => []];
            }

            return $this->falhaDeGravacao('Erro ao atualizar projeto.');
        } catch (ValidacaoProjetoException $e) {
            return $this->falhaDeValidacao($e);
        } catch (Exception $e) {
            return ['ok' => false, 'msg' => 'Erro: ' . $e->getMessage(), 'erros' => ['geral' => $e->getMessage()]];
        }
    }

    private function falhaDeValidacao(ValidacaoProjetoException $e): array
    {
        $total = count($e->erros);

        return [
            'ok' => false,
            'msg' => $total === 1
                ? 'Erro: corrija o campo destacado e tente salvar de novo.'
                : "Erro: corrija os {$total} campos destacados e tente salvar de novo.",
            'erros' => $e->erros,
        ];
    }

    /** Falha do banco: o detalhe técnico vai para o log do servidor, não para a tela. */
    private function falhaDeGravacao(string $mensagem): array
    {
        error_log("[ProjetoController] {$mensagem} " . $this->projeto->msgErro);

        return [
            'ok' => false,
            'msg' => "{$mensagem} Tente novamente; se continuar, avise o administrador.",
            'erros' => ['geral' => "{$mensagem} Tente novamente; se continuar, avise o administrador."],
        ];
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
        $erroUpload = $_FILES['contrato']['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($erroUpload === UPLOAD_ERR_NO_FILE) {
            return false;
        }

        if (in_array($erroUpload, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) || $_FILES['contrato']['size'] > 5 * 1024 * 1024) {
            throw new ValidacaoProjetoException(['contrato' => 'O contrato passou de 5MB. Envie um PDF menor (por exemplo, comprimido).']);
        }

        if ($erroUpload !== UPLOAD_ERR_OK) {
            throw new ValidacaoProjetoException(['contrato' => 'O envio do contrato falhou. Selecione o arquivo de novo.']);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['contrato']['tmp_name']);
        finfo_close($finfo);

        if ($mimeType !== 'application/pdf') {
            throw new ValidacaoProjetoException(['contrato' => 'O contrato precisa ser um arquivo PDF. Selecione um arquivo .pdf.']);
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
