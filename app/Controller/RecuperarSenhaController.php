<?php

namespace Controller;

use Model\UsuarioModel;
use PDO;
use function Config\enviarEmailRecuperacao;

class RecuperarSenhaController
{
    public function processar(PDO $conexao, string $email): string
    {
        $usuarioModel = new UsuarioModel($conexao);
        $usuario = $usuarioModel->buscarPorEmail($email);

        $mensagemPadrao = "Se o e-mail existir em nossa base, um link de recuperação foi enviado.";

        if (!$usuario) {
            return $mensagemPadrao;
        }

        $token  = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $usuarioModel->salvarTokenRecuperacao($email, $token, $expira);

        $link = BASE_URL . "?token=$token";
        enviarEmailRecuperacao($email, $usuario['nome'], $link);

        return $mensagemPadrao;
    }
}
