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
            "SELECT DISTINCT tipo_pentest.nome
             FROM projeto_pentest
             INNER JOIN tipo_pentest ON tipo_pentest.id = projeto_pentest.tipo_pentest_id
             WHERE projeto_pentest.projeto_id = :id AND projeto_pentest.habilitado = 1"
        );
        $sql->bindValue(':id', $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        return array_column($sql->fetchAll(PDO::FETCH_ASSOC), 'nome');
    }

    /** Modalidades (categorias) distintas entre todos os pentests do projeto — um projeto pode ter mais de uma. */
    public function buscarModalidades(int $idProjeto): array
    {
        $sql = $this->pdo->prepare(
            "SELECT DISTINCT categoria_pentest.nome
             FROM projeto_pentest
             INNER JOIN tipo_pentest ON tipo_pentest.id = projeto_pentest.tipo_pentest_id
             INNER JOIN categoria_pentest ON categoria_pentest.id = tipo_pentest.categoria_id
             WHERE projeto_pentest.projeto_id = :id AND projeto_pentest.habilitado = 1"
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

    /**
     * Equipe do projeto: o líder técnico (um por projeto, em projeto_usuario)
     * mais os analistas de todos os pentests. Quem é líder e também analista
     * de algum pentest aparece uma única vez, como LIDER.
     */
    public function buscarEquipe(int $idProjeto): array
    {
        $sql = $this->pdo->prepare(
            "SELECT equipe.id, equipe.nome, equipe.papel
             FROM (
                 SELECT membros.id, membros.nome,
                        CASE WHEN MIN(membros.ordem) = 0 THEN 'LIDER' ELSE 'ESPECIALISTA' END AS papel
                 FROM (
                     SELECT usuario.id, usuario.nome, 0 AS ordem
                     FROM projeto_usuario
                     INNER JOIN usuario ON usuario.id = projeto_usuario.usuario_id
                     WHERE projeto_usuario.projeto_id = :id_lider AND projeto_usuario.papel = 'LIDER'
                         AND projeto_usuario.habilitado = 1
                     UNION ALL
                     SELECT usuario.id, usuario.nome, 1 AS ordem
                     FROM projeto_pentest
                     INNER JOIN projeto_pentest_usuario ON projeto_pentest_usuario.projeto_pentest_id = projeto_pentest.id
                         AND projeto_pentest_usuario.habilitado = 1
                     INNER JOIN usuario ON usuario.id = projeto_pentest_usuario.usuario_id
                     WHERE projeto_pentest.projeto_id = :id_pentest AND projeto_pentest.habilitado = 1
                 ) AS membros
                 GROUP BY membros.id, membros.nome
             ) AS equipe
             ORDER BY FIELD(equipe.papel, 'LIDER', 'ESPECIALISTA'), equipe.nome"
        );
        $sql->bindValue(':id_lider', $idProjeto, PDO::PARAM_INT);
        $sql->bindValue(':id_pentest', $idProjeto, PDO::PARAM_INT);
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
