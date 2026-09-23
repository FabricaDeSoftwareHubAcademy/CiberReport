<?php
namespace Model;

use PDO;

class Andamento
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscarProjeto(int $idProjeto): ?array
    {
        $sql = $this->pdo->prepare(
            "SELECT projeto.*, empresa.nome_fantasia, empresa.razao_social
             FROM projeto
             INNER JOIN empresa ON empresa.id = projeto.empresa_id
             WHERE projeto.id = :id"
        );
        $sql->bindValue(':id', $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        $projeto = $sql->fetch(PDO::FETCH_ASSOC);
        return $projeto !== false ? $projeto : null;
    }

    public function buscarTiposPentest(int $idProjeto): array
    {
        $sql = $this->pdo->prepare(
            "SELECT tipo_pentest.nome
             FROM projeto_tipo_pentest
             INNER JOIN tipo_pentest ON tipo_pentest.id = projeto_tipo_pentest.tipo_pentest_id
             WHERE projeto_tipo_pentest.projeto_id = :id AND projeto_tipo_pentest.habilitado = 1"
        );
        $sql->bindValue(':id', $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        return array_column($sql->fetchAll(PDO::FETCH_ASSOC), 'nome');
    }

    public function buscarHoras(int $idProjeto): array
    {
        $sql = $this->pdo->prepare(
            "SELECT COALESCE(SUM(duracao_minutos), 0) AS minutos_consumidos
             FROM cronometro_registro
             WHERE projeto_id = :id AND habilitado = 1"
        );
        $sql->bindValue(':id', $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        $minutosConsumidos = (int) $sql->fetchColumn();

        return [
            'horas_consumidas_minutos' => $minutosConsumidos,
        ];
    }

    public function buscarVulnerabilidades(int $idProjeto): array
    {
        $sql = $this->pdo->prepare(
            "SELECT id, nome, cvss, cve, descricao, categoria, severidade_vulnerabilidade
             FROM vulnerabilidade
             WHERE projeto_id = :id AND habilitado = 1
             ORDER BY id DESC"
        );
        $sql->bindValue(':id', $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarEquipe(int $idProjeto): array
    {
        $sql = $this->pdo->prepare(
            "SELECT usuario.id, usuario.nome, projeto_usuario.papel
             FROM projeto_usuario
             INNER JOIN usuario ON usuario.id = projeto_usuario.usuario_id
             WHERE projeto_usuario.projeto_id = :id AND projeto_usuario.habilitado = 1
             ORDER BY FIELD(projeto_usuario.papel, 'LIDER', 'GESTOR', 'ESPECIALISTA'), usuario.nome"
        );
        $sql->bindValue(':id', $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarChecklist(int $idProjeto): array
    {
        $sql = $this->pdo->prepare(
            "SELECT checklist_item_catalogo.titulo, projeto_checklist_item.concluido
             FROM projeto_checklist_item
             INNER JOIN checklist_item_catalogo ON checklist_item_catalogo.id = projeto_checklist_item.item_id
             WHERE projeto_checklist_item.projeto_id = :id AND projeto_checklist_item.habilitado = 1
             ORDER BY projeto_checklist_item.concluido DESC, checklist_item_catalogo.titulo"
        );
        $sql->bindValue(':id', $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarLogAtividade(int $idProjeto): array
    {
        $sql = $this->pdo->prepare(
            "SELECT log_atividade.tipo_evento, log_atividade.descricao, log_atividade.criado_em, usuario.nome AS usuario_nome
             FROM log_atividade
             INNER JOIN usuario ON usuario.id = log_atividade.usuario_id
             WHERE log_atividade.projeto_id = :id
             ORDER BY log_atividade.criado_em DESC"
        );
        $sql->bindValue(':id', $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarLog(int $idProjeto, int $idUsuario, string $tipoEvento, string $descricao): void
    {
        $sql = $this->pdo->prepare(
            "INSERT INTO log_atividade (projeto_id, usuario_id, tipo_evento, descricao) VALUES (:projeto_id, :usuario_id, :tipo_evento, :descricao)"
        );
        $sql->execute([
            ':projeto_id' => $idProjeto,
            ':usuario_id' => $idUsuario,
            ':tipo_evento' => $tipoEvento,
            ':descricao' => $descricao,
        ]);
    }
}
