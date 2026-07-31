<?php

require_once __DIR__ . '/../Model/conexao.php';
require_once __DIR__ . '/../Model/Usuario.php';

class LoginController
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        global $conexao;
        $this->usuarioModel = new Usuario($conexao);
    }

    public function index(): void
    {
        $erro = null;
        $mensagem = null;
        $modoRecuperar = isset($_GET['recuperar']);
        $tokenUrl = $_GET['token'] ?? null;
        $viewCarregadaPeloController = true;

        require __DIR__ . '/../Views/login.php';
    }

    public function autenticar(): void
    {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $erro = null;
        $mensagem = null;
        $modoRecuperar = false;
        $tokenUrl = null;
        $viewCarregadaPeloController = true;

        if ($email === '' || $senha === '') {
            $erro = 'Preencha todos os campos.';
        } else {
            $usuario = $this->usuarioModel->buscarPorEmail($email);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];

                header('Location: gerenciar-tipo-pentest.php');
                exit;
            }

            $erro = 'E-mail ou senha inválidos.';
        }

        require __DIR__ . '/../Views/login.php';
    }

    public function solicitarRecuperacao(): void
    {
        $email = trim($_POST['email'] ?? '');
        $token = $email !== ''
            ? $this->usuarioModel->gerarTokenRecuperacao($email)
            : null;

        $erro = null;
        $mensagem = $token
            ? "Link gerado: login.php?token={$token}"
            : 'E-mail não encontrado.';
        $modoRecuperar = true;
        $tokenUrl = null;
        $viewCarregadaPeloController = true;

        require __DIR__ . '/../Views/login.php';
    }

    public function redefinirSenha(): void
    {
        $token = trim($_POST['token'] ?? '');
        $novaSenha = $_POST['senha'] ?? '';
        $erro = null;
        $modoRecuperar = false;
        $tokenUrl = null;
        $viewCarregadaPeloController = true;

        if (
            $token !== ''
            && $novaSenha !== ''
            && $this->usuarioModel->redefinirSenha($token, $novaSenha)
        ) {
            $mensagem = 'Senha redefinida! Faça login com a nova senha.';
        } else {
            $mensagem = null;
            $erro = 'Link inválido ou expirado.';
        }

        require __DIR__ . '/../Views/login.php';
    }
}
