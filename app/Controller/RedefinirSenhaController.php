<?php
require_once __DIR__ . '/../Model/UsuarioModel.php';

function processarRedefinirSenha(PDO $conexao, string $token, string $novaSenha): bool {
    $usuarioModel = new UsuarioModel($conexao);
    $usuario = $usuarioModel->buscarPorTokenValido($token);

    if (!$usuario) {
        return false;
    }

    $hash = password_hash($novaSenha, PASSWORD_BCRYPT);
    $usuarioModel->redefinirSenha($usuario['id'], $hash);
    return true;
}