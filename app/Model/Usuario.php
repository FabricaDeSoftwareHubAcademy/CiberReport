<?php

class Usuario
{
    private PDO $conexao;

    public function __construct(PDO $conexao)
    {
        $this->conexao = $conexao;
    }

    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->conexao->prepare(
            "SELECT id, nome, senha FROM usuario WHERE email = ?"
        );
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    public function gerarTokenRecuperacao(string $email): ?string
    {
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->conexao->prepare(
            "UPDATE usuario
             SET reset_token = ?, reset_token_expira = ?
             WHERE email = ?"
        );
        $stmt->execute([$token, $expira, $email]);

        return $stmt->rowCount() > 0 ? $token : null;
    }

    public function redefinirSenha(string $token, string $novaSenha): bool
    {
        $stmt = $this->conexao->prepare(
            "SELECT id
             FROM usuario
             WHERE reset_token = ? AND reset_token_expira > NOW()"
        );
        $stmt->execute([$token]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return false;
        }

        $stmt = $this->conexao->prepare(
            "UPDATE usuario
             SET senha = ?, reset_token = NULL, reset_token_expira = NULL
             WHERE id = ?"
        );

        return $stmt->execute([
            password_hash($novaSenha, PASSWORD_BCRYPT),
            $usuario['id'],
        ]);
    }
}
