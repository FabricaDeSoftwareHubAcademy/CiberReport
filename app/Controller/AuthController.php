<?php

namespace Controller;

use Core\Controller;
use Model\UsuarioModel;
use function config\enviarEmailRecuperacao;

class AuthController extends Controller
{
    private UsuarioModel $auth;

    public function __construct()
    {
        $conexao = require __DIR__ . '/../Model/conexao.php';
        $this->auth = new UsuarioModel($conexao);
    }

    public function exibirLogin(): void
    {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('gerenciar-pentest');
        }

        $this->view('login', [
            'modo' => 'login',
            'erro' => null,
            'mensagem' => null,
            'token' => null,
            'sucessoRedefinicao' => isset($_GET['senha_redefinida'])
        ]);
    }

    public function exibirRedefinirSenha(): void
    {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('gerenciar-pentest');
        }

        $token = trim($_GET['token'] ?? '');

        if (!$this->tokenTemFormatoValido($token)) {
            $this->view('login', [
                'modo' => 'login',
                'erro' => 'Link inválido ou expirado.',
                'mensagem' => null,
                'token' => null,
                'sucessoRedefinicao' => false
            ]);
            return;
        }

        $usuario = $this->auth->buscarPorTokenValido($token);

        if (!$usuario) {
            $this->view('login', [
                'modo' => 'login',
                'erro' => 'Link inválido ou expirado.',
                'mensagem' => null,
                'token' => null,
                'sucessoRedefinicao' => false
            ]);

            return;
        }

        $this->view('login', [
            'modo' => 'redefinir',
            'erro' => null,
            'mensagem' => null,
            'token' => $token,
            'sucessoRedefinicao' => false
        ]);
    }

    private function tokenTemFormatoValido(string $token): bool
    {
        return preg_match('/^[a-f0-9]{64}$/', $token) === 1;
    }

    public function exibirRecuperarSenha(): void
    {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('gerenciar-pentest');
        }

        $this->view('login', [
            'modo' => 'recuperar',
            'erro' => null,
            'mensagem' => null,
            'token' => null,
            'sucessoRedefinicao' => false
        ]);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $resultado = $this->autenticar($email, $senha);

        if (!$resultado['sucesso']) {
            $this->view('login', [
                'erro' => $resultado['erro']
            ]);

            return;
        }

        session_regenerate_id(true);

        $_SESSION['usuario_id'] = (int) $resultado['usuario']['id'];
        $_SESSION['usuario_nome'] = $resultado['usuario']['nome'];
        $_SESSION['perfil_id'] = (int) $resultado['usuario']['perfil_id'];

        $this->redirect('dashboard-gestor');
    }

    public function recuperarSenha(): void
    {
        $email = trim($_POST['email'] ?? '');

        if ($email == '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('login', [
                'modo' => 'recuperar',
                'erro' => 'Informe um e-mail válido.',
                'mensagem' => null,
                'token' => null,
                'sucessoRedefinicao' => false
            ]);

            return;
        }

        $mensagemPadrao = "Se o e-mail existir em nossa base, um link de recuperação foi enviado.";

        $usuario = $this->auth->buscarPorEmail($email);

        if (!$usuario) {
            $this->view('login', [
                'modo' => 'recuperar',
                'erro' => null,
                'mensagem' => $mensagemPadrao,
                'token' => null,
                'sucessoRedefinicao' => false
            ]);
            return;
        }

        $token = bin2hex(random_bytes(32));

        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $tokenSalvo = $this->auth->salvarTokenRecuperacao(
            $email,
            $token,
            $expira
        );

        if (!$tokenSalvo) {
            $this->view('login', [
                'modo' => 'recuperar',
                'erro' => 'Não foi possível processar a solicitação.',
                'mensagem' => null,
                'token' => null,
                'sucessoRedefinicao' => false
            ]);

            return;
        }

        $link = BASE_URL . 'redefinir-senha?token=' . rawurlencode($token);

        enviarEmailRecuperacao(
            $email,
            $usuario['nome'],
            $link
        );

        $this->view('login', [
            'modo' => 'recuperar',
            'erro' => null,
            'mensagem' => $mensagemPadrao,
            'token' => null,
            'sucessoRedefinicao' => false
        ]);
    }

    public function redefinirSenha(): void
    {
        $token = trim($_POST['token'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        if (!$this->tokenTemFormatoValido($token)) {
            $this->view('login', [
                'modo' => 'login',
                'erro' => 'Link inválido ou expirado.',
                'mensagem' => null,
                'token' => null,
                'sucessoRedefinicao' => false
            ]);

            return;
        }

        if (strlen($senha) < 8) {
            $this->view('login', [
                'modo' => 'redefinir',
                'erro' => 'A senha deve ter pelo menos 8 caracteres.',
                'mensagem' => null,
                'token' => $token,
                'sucessoRedefinicao' => false
            ]);
            
            return;
        }

         if ($senha !== $confirmarSenha) {
            $this->view('login', [
                'modo' => 'redefinir',
                'erro' => 'As senhas não coincidem.',
                'mensagem' => null,
                'token' => $token,
                'sucessoRedefinicao' => false
            ]);

            return;
        }

        $usuario = $this->auth->buscarPorTokenValido($token);

        if(!$usuario){
            $this->view('login',[
                'modo' => 'login',
                'erro' => 'Link inválido ou expirado.',
                'mensagem' => null,
                'token' => null,
                'sucessoRedefinicao' => false
            ]);

            return;
        }

        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $this->auth->redefinirSenha((int) $usuario['id'], $hash);
        $this->redirect('login?senha_redefinida=1');
    }

    private function autenticar(string $email, string $senha): array
    {
        if ($email === '' || $senha === '') {
            return [
                'sucesso' => false,
                'erro' => 'Preencha todos os campos.'
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'sucesso' => false,
                'erro' => 'E-mail ou senha inválidos.'
            ];
        }

        $usuario = $this->auth->buscarPorEmail($email);

        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            return [
                'sucesso' => false,
                'erro' => 'E-mail ou senha inválidos.'
            ];
        }

        return [
            'sucesso' => true,
            'usuario' => $usuario
        ];
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();

        $this->redirect('login');
    }
}
