<?php
namespace Model;

use PDO;
class Projeto
{
    private $pdo;

    public $msgErro = "";

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function buscarProjeto($id)
    {
        $sql = $this->pdo->prepare("SELECT id FROM projeto WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    public function cadastrarProjeto(array $dados)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = $this->pdo->prepare("INSERT INTO projeto (empresa_id, nome, data_inicio, data_fim_prevista, data_fim_real, horas_contratadas, modalidade, nivel_sigilo, escopo, contrato, restricao, status) VALUES (:empresa_id, :nome, :data_inicio, :data_fim_prevista, :data_fim_real, :horas_contratadas, :modalidade, :nivel_sigilo, :escopo, :contrato, :restricao, :status)");
            $sql->bindValue(":empresa_id", $dados['empresa_id'], PDO::PARAM_INT);
            $sql->bindValue(":nome", $dados['nome']);
            $sql->bindValue(
                ":data_inicio",
                $dados['data_inicio'],
                $dados['data_inicio'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
            );
            $sql->bindValue(
                ":data_fim_prevista",
                $dados['data_fim_prevista'],
                $dados['data_fim_prevista'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
            );
            $sql->bindValue(
                ":data_fim_real",
                $dados['data_fim_real'],
                $dados['data_fim_real'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
            );
            $sql->bindValue(
                ":horas_contratadas",
                (string) $dados['horas_contratadas'],
                PDO::PARAM_STR
            );
            $sql->bindValue(":modalidade", $dados['modalidade']);
            $sql->bindValue(":nivel_sigilo", $dados['nivel_sigilo']);
            $sql->bindValue(":escopo", $dados['escopo']);
            $sql->bindValue(":contrato", $dados['contrato']);
            $sql->bindValue(":restricao", $dados['restricao']);
            $sql->bindValue(":status", $dados['status']);
            $sql->execute();

            $idProjeto = (int) $this->pdo->lastInsertId();

            $this->salvarAlvos($idProjeto, $dados['alvos'] ?? []);
            $this->salvarTiposPentest($idProjeto, $dados['tipos_pentest_ids'] ?? []);
            $this->salvarEquipe($idProjeto, $dados['lider_tecnico_id'], $dados['analistas_ids'] ?? []);

            $this->pdo->commit();
            return $idProjeto;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            $this->msgErro = $e->getMessage();
            return false;
        }
    }

    private function salvarAlvos(int $idProjeto, array $alvos): void
    {
        if (empty($alvos)) {
            return;
        }

        $sql = $this->pdo->prepare(
            "INSERT INTO projeto_alvo (projeto_id, tipo, valor) VALUES (:projeto_id, :tipo, :valor)"
        );

        foreach ($alvos as $valor) {
            $sql->execute([
                ':projeto_id' => $idProjeto,
                ':tipo' => $this->classificarAlvo($valor),
                ':valor' => $valor,
            ]);
        }
    }

    private function classificarAlvo(string $valor): string
    {
        if (preg_match('/^\d{1,3}(\.\d{1,3}){3}$/', $valor)) {
            return 'IP';
        }

        if (preg_match('/^https?:\/\//i', $valor)) {
            return 'URL';
        }

        if (preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/i', $valor)) {
            return 'DOMINIO';
        }

        return 'OUTRO';
    }

    private function salvarTiposPentest(int $idProjeto, array $tiposPentestIds): void
    {
        if (empty($tiposPentestIds)) {
            return;
        }

        $sql = $this->pdo->prepare(
            "INSERT INTO projeto_tipo_pentest (projeto_id, tipo_pentest_id) VALUES (:projeto_id, :tipo_pentest_id)"
        );

        foreach ($tiposPentestIds as $idTipoPentest) {
            $sql->execute([
                ':projeto_id' => $idProjeto,
                ':tipo_pentest_id' => $idTipoPentest,
            ]);
        }
    }

    private function salvarEquipe(int $idProjeto, int $idLiderTecnico, array $analistasIds): void
    {
        $sql = $this->pdo->prepare(
            "INSERT INTO projeto_usuario (projeto_id, usuario_id, papel) VALUES (:projeto_id, :usuario_id, :papel)"
        );

        $sql->execute([
            ':projeto_id' => $idProjeto,
            ':usuario_id' => $idLiderTecnico,
            ':papel' => 'LIDER',
        ]);

        foreach ($analistasIds as $idAnalista) {
            if ($idAnalista === $idLiderTecnico) {
                continue;
            }

            $sql->execute([
                ':projeto_id' => $idProjeto,
                ':usuario_id' => $idAnalista,
                ':papel' => 'ESPECIALISTA',
            ]);
        }
    }
    public function listarDados()
    {
        $sql = $this->pdo->prepare("SELECT projeto.*, empresa.nome_fantasia FROM projeto INNER JOIN empresa ON projeto.empresa_id = empresa.id WHERE projeto.habilitado = 1 ORDER BY projeto.created_at DESC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarStatusAtual(int $idProjeto): ?string
    {
        $sql = $this->pdo->prepare("SELECT status FROM projeto WHERE id = :id");
        $sql->bindValue(":id", $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        $status = $sql->fetchColumn();
        return $status !== false ? $status : null;
    }

    public function buscarContratoAtual(int $idProjeto): ?string
    {
        $sql = $this->pdo->prepare("SELECT contrato FROM projeto WHERE id = :id");
        $sql->bindValue(":id", $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        $contrato = $sql->fetchColumn();
        return $contrato !== false ? $contrato : null;
    }

    public function buscarAlvos(int $idProjeto): array
    {
        $sql = $this->pdo->prepare("SELECT valor FROM projeto_alvo WHERE projeto_id = :id AND habilitado = 1 ORDER BY id");
        $sql->bindValue(":id", $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        return array_column($sql->fetchAll(PDO::FETCH_ASSOC), 'valor');
    }

    public function buscarTiposPentestIds(int $idProjeto): array
    {
        $sql = $this->pdo->prepare("SELECT tipo_pentest_id FROM projeto_tipo_pentest WHERE projeto_id = :id AND habilitado = 1");
        $sql->bindValue(":id", $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        return array_map('intval', array_column($sql->fetchAll(PDO::FETCH_ASSOC), 'tipo_pentest_id'));
    }

    public function buscarEquipe(int $idProjeto): array
    {
        $sql = $this->pdo->prepare("SELECT usuario_id, papel FROM projeto_usuario WHERE projeto_id = :id AND habilitado = 1");
        $sql->bindValue(":id", $idProjeto, PDO::PARAM_INT);
        $sql->execute();

        $liderId = null;
        $especialistasIds = [];

        foreach ($sql->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            if ($linha['papel'] === 'LIDER') {
                $liderId = (int) $linha['usuario_id'];
            } elseif ($linha['papel'] === 'ESPECIALISTA') {
                $especialistasIds[] = (int) $linha['usuario_id'];
            }
        }

        return ['lider_id' => $liderId, 'especialistas_ids' => $especialistasIds];
    }

    public function editarProjeto(array $dados)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = $this->pdo->prepare("UPDATE projeto SET empresa_id = :empresa_id, nome = :nome, data_inicio = :data_inicio, data_fim_prevista = :data_fim_prevista, data_fim_real = :data_fim_real, horas_contratadas = :horas_contratadas, modalidade = :modalidade, nivel_sigilo = :nivel_sigilo, escopo = :escopo, contrato = :contrato, restricao = :restricao, status = :status WHERE id = :id");
            $sql->bindValue(":id", $dados['id'], PDO::PARAM_INT);
            $sql->bindValue(":empresa_id", $dados['empresa_id'], PDO::PARAM_INT);
            $sql->bindValue(":nome", $dados['nome']);
            $sql->bindValue(
                ":data_inicio",
                $dados['data_inicio'],
                $dados['data_inicio'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
            );
            $sql->bindValue(
                ":data_fim_prevista",
                $dados['data_fim_prevista'],
                $dados['data_fim_prevista'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
            );
            $sql->bindValue(
                ":data_fim_real",
                $dados['data_fim_real'],
                $dados['data_fim_real'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
            );
            $sql->bindValue(
                ":horas_contratadas",
                (string) $dados['horas_contratadas'],
                PDO::PARAM_STR
            );
            $sql->bindValue(":modalidade", $dados['modalidade']);
            $sql->bindValue(":nivel_sigilo", $dados['nivel_sigilo']);
            $sql->bindValue(":escopo", $dados['escopo']);
            $sql->bindValue(":contrato", $dados['contrato']);
            $sql->bindValue(":restricao", $dados['restricao']);
            $sql->bindValue(":status", $dados['status']);
            $sql->execute();

            $idProjeto = (int) $dados['id'];

            $this->pdo->prepare("DELETE FROM projeto_alvo WHERE projeto_id = :id")->execute([':id' => $idProjeto]);
            $this->pdo->prepare("DELETE FROM projeto_tipo_pentest WHERE projeto_id = :id")->execute([':id' => $idProjeto]);
            $this->pdo->prepare("DELETE FROM projeto_usuario WHERE projeto_id = :id")->execute([':id' => $idProjeto]);

            $this->salvarAlvos($idProjeto, $dados['alvos'] ?? []);
            $this->salvarTiposPentest($idProjeto, $dados['tipos_pentest_ids'] ?? []);
            $this->salvarEquipe($idProjeto, $dados['lider_tecnico_id'], $dados['analistas_ids'] ?? []);

            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            $this->msgErro = $e->getMessage();
            return false;
        }
    }
    public function excluirProjeto($id)
    {
        try {
            $sql = $this->pdo->prepare("UPDATE projeto SET habilitado = 0 WHERE id = :id");
            $sql->bindValue(":id", $id);
            $sql->execute();
            return true;
        } catch (\PDOException $e) {
            $this->msgErro = $e->getMessage();
            return false;
        }
    }
    
}
