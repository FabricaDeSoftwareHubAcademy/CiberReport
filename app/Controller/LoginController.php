<?php

require_once __DIR__ . '/../Model/UsuarioModel.php';

function processarLogin(PDO $conexao, string $email, string $senha): array {
    if (empty($email) || empty($senha)) {
        return ['sucesso' => false, 'erro' => 'Preencha todos os campos.'];
    }

    $usuarioModel = new UsuarioModel($conexao);
    $usuario = $usuarioModel->buscarPorEmail($email);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        return ['sucesso' => true, 'usuario' => $usuario];
    }

    return ['sucesso' => false, 'erro' => 'E-mail ou senha inválidos.'];
}