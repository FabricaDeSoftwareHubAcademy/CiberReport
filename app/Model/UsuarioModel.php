<?php

class UsuarioModel {
    private PDO $conexao;

    public function __construct(PDO $conexao) {
        $this->conexao = $conexao;
    }

    public function buscarPorEmail(string $email): array|false {
        $stmt = $this->conexao->prepare("SELECT id, nome, senha FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvarTokenRecuperacao(string $email, string $token, string $expira): bool {
        $stmt = $this->conexao->prepare(
            "UPDATE usuario SET reset_token = ?, reset_token_expira = ? WHERE email = ?"
        );
        $stmt->execute([$token, $expira, $email]);
        return $stmt->rowCount() > 0;
    }

    public function buscarPorTokenValido(string $token): array|false {
        $stmt = $this->conexao->prepare(
            "SELECT id FROM usuario WHERE reset_token = ? AND reset_token_expira > NOW()"
        );
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function redefinirSenha(int $id, string $hash): void {
        $this->conexao->prepare(
            "UPDATE usuario SET senha = ?, reset_token = NULL, reset_token_expira = NULL WHERE id = ?"
        )->execute([$hash, $id]);
    }
}