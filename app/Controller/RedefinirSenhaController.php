<?php

namespace Controller;

use Model\UsuarioModel;
use PDO;

class RedefinirSenhaController
{
    public function processar(PDO $conexao, string $token, string $novaSenha): bool
    {
        $usuarioModel = new UsuarioModel($conexao);
        $usuario = $usuarioModel->buscarPorTokenValido($token);

        if (!$usuario) {
            return false;
        }

        $hash = password_hash($novaSenha, PASSWORD_BCRYPT);
        $usuarioModel->redefinirSenha($usuario['id'], $hash);
        return true;
    }
}
