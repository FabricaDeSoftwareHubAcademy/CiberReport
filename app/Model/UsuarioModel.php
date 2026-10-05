<?php

namespace Model;
use PDO;

class UsuarioModel {
    private PDO $conexao;

    public function __construct(PDO $conexao) {
        $this->conexao = $conexao;
    }

    public function buscarPorEmail(string $email): array|false {
        $stmt = $this->conexao->prepare("SELECT id, nome, senha, perfil_id FROM usuario WHERE email = ? AND habilitado = 1 LIMIT 1");
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

    /** Usuários ativos com perfil Pentester: os únicos que podem compor a equipe de um projeto. */
    public function listarPentestersAtivosParaSelecao(): array {
        $stmt = $this->conexao->prepare(
            "SELECT usuario.id, usuario.nome
             FROM usuario
             INNER JOIN perfil_acesso ON perfil_acesso.id = usuario.perfil_id
             WHERE usuario.habilitado = 1 AND perfil_acesso.habilitado = 1 AND perfil_acesso.nome = 'Pentester'
             ORDER BY usuario.nome"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}