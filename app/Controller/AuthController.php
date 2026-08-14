<?php

namespace Controller;

use Core\Controller;
use Model\UsuarioModel;

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
            'erro' => null
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

        $this->redirect('gerenciar-pentest');
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
