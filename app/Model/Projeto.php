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

            $sql = $this->pdo->prepare("INSERT INTO projeto (empresa_id, nome, data_inicio, data_fim_prevista, data_fim_real, horas_contratadas, nivel_sigilo, escopo, contrato, restricao, status) VALUES (:empresa_id, :nome, :data_inicio, :data_fim_prevista, :data_fim_real, :horas_contratadas, :nivel_sigilo, :escopo, :contrato, :restricao, :status)");
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
            $sql->bindValue(":nivel_sigilo", $dados['nivel_sigilo']);
            $sql->bindValue(":escopo", $dados['escopo']);
            $sql->bindValue(":contrato", $dados['contrato']);
            $sql->bindValue(":restricao", $dados['restricao']);
            $sql->bindValue(":status", $dados['status']);
            $sql->execute();

            $idProjeto = (int) $this->pdo->lastInsertId();

            $this->salvarAlvos($idProjeto, $dados['alvos'] ?? []);
            $this->salvarPentests($idProjeto, $dados['pentests'] ?? []);

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

    /**
     * Grava os blocos de "Pentest" do projeto (projeto_pentest + a
     * metodologia e a equipe de cada um). Cada bloco é independente: tem
     * seu próprio líder técnico, que pode divergir entre pentests do
     * mesmo projeto.
     */
    private function salvarPentests(int $idProjeto, array $pentests): void
    {
        $sql = $this->pdo->prepare(
            "INSERT INTO projeto_pentest (projeto_id, tipo_pentest_id, horas_contratadas, abordagem, ambiente, escopo, referencia)
             VALUES (:projeto_id, :tipo_pentest_id, :horas_contratadas, :abordagem, :ambiente, :escopo, :referencia)"
        );

        foreach ($pentests as $pentest) {
            $sql->execute([
                ':projeto_id' => $idProjeto,
                ':tipo_pentest_id' => $pentest['tipo_pentest_id'],
                ':horas_contratadas' => (string) $pentest['horas_contratadas'],
                ':abordagem' => $pentest['abordagem'],
                ':ambiente' => $pentest['ambiente'],
                ':escopo' => $pentest['escopo'],
                ':referencia' => $pentest['referencia'],
            ]);

            $idPentest = (int) $this->pdo->lastInsertId();

            $this->salvarPentestFrameworks($idPentest, $pentest['frameworks_ids'] ?? []);
            $this->salvarPentestEquipe($idPentest, $pentest['lider_id'], $pentest['analistas_ids'] ?? []);
        }
    }

    private function salvarPentestFrameworks(int $idPentest, array $frameworksIds): void
    {
        if (empty($frameworksIds)) {
            return;
        }

        $sql = $this->pdo->prepare(
            "INSERT INTO projeto_pentest_framework (projeto_pentest_id, framework_id) VALUES (:id, :framework_id)"
        );

        foreach ($frameworksIds as $idFramework) {
            $sql->execute([':id' => $idPentest, ':framework_id' => $idFramework]);
        }
    }

    private function salvarPentestEquipe(int $idPentest, int $idLider, array $analistasIds): void
    {
        $sql = $this->pdo->prepare(
            "INSERT INTO projeto_pentest_usuario (projeto_pentest_id, usuario_id, papel) VALUES (:id, :usuario_id, :papel)"
        );

        $sql->execute([':id' => $idPentest, ':usuario_id' => $idLider, ':papel' => 'LIDER']);

        foreach ($analistasIds as $idAnalista) {
            if ($idAnalista === $idLider) {
                continue;
            }

            $sql->execute([':id' => $idPentest, ':usuario_id' => $idAnalista, ':papel' => 'ESPECIALISTA']);
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

    /**
     * Todos os blocos de Pentest de um projeto, já com tipo/categoria
     * ("modalidade"), frameworks ("metodologia") e equipe de cada um.
     * Usado no Editar/Visualizar e pela Andamento (agregando os N blocos).
     */
    public function buscarPentests(int $idProjeto): array
    {
        $sql = $this->pdo->prepare(
            "SELECT projeto_pentest.*, tipo_pentest.nome AS tipo_pentest_nome,
                    categoria_pentest.nome AS modalidade
             FROM projeto_pentest
             INNER JOIN tipo_pentest ON tipo_pentest.id = projeto_pentest.tipo_pentest_id
             INNER JOIN categoria_pentest ON categoria_pentest.id = tipo_pentest.categoria_id
             WHERE projeto_pentest.projeto_id = :id AND projeto_pentest.habilitado = 1
             ORDER BY projeto_pentest.id"
        );
        $sql->bindValue(":id", $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        $pentests = $sql->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pentests as &$pentest) {
            $idPentest = (int) $pentest['id'];
            $pentest['frameworks_ids'] = $this->buscarPentestFrameworksIds($idPentest);
            $equipe = $this->buscarPentestEquipe($idPentest);
            $pentest['lider_id'] = $equipe['lider_id'];
            $pentest['analistas_ids'] = $equipe['analistas_ids'];
        }

        return $pentests;
    }

    private function buscarPentestFrameworksIds(int $idPentest): array
    {
        $sql = $this->pdo->prepare("SELECT framework_id FROM projeto_pentest_framework WHERE projeto_pentest_id = :id");
        $sql->bindValue(":id", $idPentest, PDO::PARAM_INT);
        $sql->execute();
        return array_map('intval', array_column($sql->fetchAll(PDO::FETCH_ASSOC), 'framework_id'));
    }

    private function buscarPentestEquipe(int $idPentest): array
    {
        $sql = $this->pdo->prepare("SELECT usuario_id, papel FROM projeto_pentest_usuario WHERE projeto_pentest_id = :id AND habilitado = 1");
        $sql->bindValue(":id", $idPentest, PDO::PARAM_INT);
        $sql->execute();

        $liderId = null;
        $analistasIds = [];

        foreach ($sql->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            if ($linha['papel'] === 'LIDER') {
                $liderId = (int) $linha['usuario_id'];
            } else {
                $analistasIds[] = (int) $linha['usuario_id'];
            }
        }

        return ['lider_id' => $liderId, 'analistas_ids' => $analistasIds];
    }

    public function editarProjeto(array $dados)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = $this->pdo->prepare("UPDATE projeto SET empresa_id = :empresa_id, nome = :nome, data_inicio = :data_inicio, data_fim_prevista = :data_fim_prevista, data_fim_real = :data_fim_real, horas_contratadas = :horas_contratadas, nivel_sigilo = :nivel_sigilo, escopo = :escopo, contrato = :contrato, restricao = :restricao, status = :status WHERE id = :id");
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
            $sql->bindValue(":nivel_sigilo", $dados['nivel_sigilo']);
            $sql->bindValue(":escopo", $dados['escopo']);
            $sql->bindValue(":contrato", $dados['contrato']);
            $sql->bindValue(":restricao", $dados['restricao']);
            $sql->bindValue(":status", $dados['status']);
            $sql->execute();

            $idProjeto = (int) $dados['id'];

            $this->pdo->prepare("DELETE FROM projeto_alvo WHERE projeto_id = :id")->execute([':id' => $idProjeto]);
            $this->excluirPentests($idProjeto);

            $this->salvarAlvos($idProjeto, $dados['alvos'] ?? []);
            $this->salvarPentests($idProjeto, $dados['pentests'] ?? []);

            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            $this->msgErro = $e->getMessage();
            return false;
        }
    }

    /** Apaga os pentests do projeto e suas tabelas filhas (framework/equipe), nessa ordem por causa das FKs. */
    private function excluirPentests(int $idProjeto): void
    {
        $sql = $this->pdo->prepare("SELECT id FROM projeto_pentest WHERE projeto_id = :id");
        $sql->bindValue(":id", $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        $idsPentest = array_column($sql->fetchAll(PDO::FETCH_ASSOC), 'id');

        if (empty($idsPentest)) {
            return;
        }

        $marcadores = implode(',', array_fill(0, count($idsPentest), '?'));
        $this->pdo->prepare("DELETE FROM projeto_pentest_usuario WHERE projeto_pentest_id IN ($marcadores)")->execute($idsPentest);
        $this->pdo->prepare("DELETE FROM projeto_pentest_framework WHERE projeto_pentest_id IN ($marcadores)")->execute($idsPentest);
        $this->pdo->prepare("DELETE FROM projeto_pentest WHERE projeto_id = :id")->execute([':id' => $idProjeto]);
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
