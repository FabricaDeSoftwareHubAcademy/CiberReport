<?php

require_once __DIR__ . '/../Model/UsuarioModel.php';
require_once __DIR__ . '/../../config/mailer.php';

function processarRecuperarSenha(PDO $conexao, string $email): string {
    $usuarioModel = new UsuarioModel($conexao);
    $usuario = $usuarioModel->buscarPorEmail($email);

    $mensagemPadrao = "Se o e-mail existir em nossa base, um link de recuperação foi enviado.";

    if (!$usuario) {
        return $mensagemPadrao;
    }

    $token  = bin2hex(random_bytes(32));
    $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $usuarioModel->salvarTokenRecuperacao($email, $token, $expira);

    $link = BASE_URL . "login?token=$token";
    enviarEmailRecuperacao($email, $usuario['nome'], $link);

    return $mensagemPadrao;
}