<?php 

namespace Controller;

use Core\Controller;
use Model\GerenUsuario;

class GerenciamentoUsuarioController extends Controller
{
    private  $usuario;

    public function __construct()
    {
        global $conexao;
        
        $this->usuario = new GerenUsuario($conexao);
    }

     public function index()
    {
        $this->view('usuario.php');
    }

    public function listar()
    {
        return $this->usuario->listarUsuario();
    }

    public function cadastrar()
    {
        $nome           = trim($_POST['nome'] ?? '');
        $telefone       = trim($_POST['telefone'] ?? '');
        $cpf            = trim($_POST['cpf'] ?? '');
        $cargo          = trim($_POST['cargo'] ?? '');
        $especialidade  = trim($_POST['especialidade'] ?? '');
        $email          = trim($_POST['email'] ?? '');
        $senha          = $_POST['senha'] ?? '';
        $perfil_id      = $_POST['perfil_id'] ?? '';

        // Campos obrigatórios (batendo com o NOT NULL do banco)
        if (empty($nome) || empty($cpf) || empty($email) || empty($senha) || empty($perfil_id)) {
            return false;
        }

        // Validação simples de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return $this->usuario->cadastrarUsuario(
            $nome,
            $telefone,
            $cpf,
            $cargo,
            $especialidade,
            $email,
            $senha,
            $perfil_id
        );
    }

    public function uploadFoto($id, $arquivo)
    {
        if (!isset($arquivo['tmp_name']) || $arquivo['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $diretorio = __DIR__ . '/../assets/uploads/usuarios/';
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        $extensao       = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
        $nomeArquivo    = 'usuario_' . $id . '_' . time() . '.' . $extensao;
        $caminhoDestino = $diretorio . $nomeArquivo;

        if (move_uploaded_file($arquivo['tmp_name'], $caminhoDestino)) {
            $caminhoRelativo = 'assets/uploads/usuarios/' . $nomeArquivo;
            $this->usuario->atualizarFotoUsuario($id, $caminhoRelativo);
            return $caminhoRelativo;
        }

        return false;
    }
    
    public function excluir($id)
    {
        $this->usuario->excluirUsuario((int) $id);
    }

    public function alterarStatus($id, $status)
    {
        $this->usuario->alterarStatusUsuario((int) $id, (int) $status);
    }
}


?>