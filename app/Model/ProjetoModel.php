<?php
namespace Model;

use PDO;
class ProjetoModel
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

            $this->salvarLider($idProjeto, (int) $dados['lider_id']);
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
            $this->salvarPentestAnalistas($idPentest, $pentest['analistas_ids'] ?? []);
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

    private function salvarPentestAnalistas(int $idPentest, array $analistasIds): void
    {
        $sql = $this->pdo->prepare(
            "INSERT INTO projeto_pentest_usuario (projeto_pentest_id, usuario_id, papel) VALUES (:id, :usuario_id, 'ESPECIALISTA')"
        );

        foreach ($analistasIds as $idAnalista) {
            $sql->execute([':id' => $idPentest, ':usuario_id' => $idAnalista]);
        }
    }

    /**
     * O líder técnico é um só por projeto e fica em projeto_usuario. Troca
     * apenas a linha de LIDER: outros papéis do projeto (ex.: GESTOR) não
     * são deste formulário e precisam sobreviver à edição.
     */
    private function salvarLider(int $idProjeto, int $idLider): void
    {
        $this->pdo->prepare("DELETE FROM projeto_usuario WHERE projeto_id = :id AND papel = 'LIDER'")
            ->execute([':id' => $idProjeto]);

        $this->pdo->prepare("INSERT INTO projeto_usuario (projeto_id, usuario_id, papel) VALUES (:id, :usuario_id, 'LIDER')")
            ->execute([':id' => $idProjeto, ':usuario_id' => $idLider]);
    }

    public function buscarLiderId(int $idProjeto): ?int
    {
        $sql = $this->pdo->prepare(
            "SELECT usuario_id FROM projeto_usuario WHERE projeto_id = :id AND papel = 'LIDER' AND habilitado = 1 LIMIT 1"
        );
        $sql->bindValue(":id", $idProjeto, PDO::PARAM_INT);
        $sql->execute();
        $idLider = $sql->fetchColumn();

        return $idLider !== false ? (int) $idLider : null;
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
            $pentest['analistas_ids'] = $this->buscarPentestAnalistasIds($idPentest);
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

    /**
     * Qualquer papel conta como analista do pentest: projetos gravados
     * quando o líder era por pentest ainda têm linhas com papel LIDER aqui.
     */
    private function buscarPentestAnalistasIds(int $idPentest): array
    {
        $sql = $this->pdo->prepare("SELECT DISTINCT usuario_id FROM projeto_pentest_usuario WHERE projeto_pentest_id = :id AND habilitado = 1");
        $sql->bindValue(":id", $idPentest, PDO::PARAM_INT);
        $sql->execute();

        return array_map('intval', array_column($sql->fetchAll(PDO::FETCH_ASSOC), 'usuario_id'));
    }

    /**
     * Tipos de pentest ativos com a categoria, que o modal mostra como
     * "Modalidade". Consulta própria do módulo de projetos: só lê as tabelas
     * do módulo de Pentest.
     */
    public function buscarTiposPentestAtivos(): array
    {
        $sql = $this->pdo->prepare(
            "SELECT tipo_pentest.id, tipo_pentest.nome, tipo_pentest.categoria_id, categoria_pentest.nome AS categoria_nome
             FROM tipo_pentest
             INNER JOIN categoria_pentest ON categoria_pentest.id = tipo_pentest.categoria_id
             WHERE tipo_pentest.habilitado = 1
             ORDER BY tipo_pentest.nome"
        );
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Frameworks vinculados a cada tipo de pentest ([tipo_id => [framework_id, ...]]), para o modal sugerir a metodologia. */
    public function buscarFrameworksPorTipoPentest(): array
    {
        $sql = $this->pdo->prepare("SELECT tipo_pentest_id, framework_id FROM tipo_pentest_framework");
        $sql->execute();

        $porTipo = [];
        foreach ($sql->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            $porTipo[(int) $linha['tipo_pentest_id']][] = (int) $linha['framework_id'];
        }

        return $porTipo;
    }

    /**
     * Checklists vinculados a cada tipo de pentest, com os títulos dos itens,
     * para o modal mostrar a prévia: [tipo_id => [['nome' => ..., 'itens' => [...]], ...]].
     */
    public function buscarChecklistsPorTipoPentest(): array
    {
        $sql = $this->pdo->prepare(
            "SELECT tipo_pentest_checklist.tipo_pentest_id, checklist.id AS checklist_id, checklist.nome,
                    checklist_item_catalogo.titulo
             FROM tipo_pentest_checklist
             INNER JOIN checklist ON checklist.id = tipo_pentest_checklist.checklist_id AND checklist.habilitado = 1
             LEFT JOIN checklist_item_vinculo ON checklist_item_vinculo.checklist_id = checklist.id
                 AND checklist_item_vinculo.habilitado = 1
             LEFT JOIN checklist_item_catalogo ON checklist_item_catalogo.id = checklist_item_vinculo.item_id
                 AND checklist_item_catalogo.habilitado = 1
             ORDER BY tipo_pentest_checklist.tipo_pentest_id, checklist.nome, checklist_item_vinculo.ordem"
        );
        $sql->execute();

        $porTipo = [];
        foreach ($sql->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            $idTipo = (int) $linha['tipo_pentest_id'];
            $idChecklist = (int) $linha['checklist_id'];

            $porTipo[$idTipo][$idChecklist] ??= ['nome' => $linha['nome'], 'itens' => []];

            if ($linha['titulo'] !== null) {
                $porTipo[$idTipo][$idChecklist]['itens'][] = $linha['titulo'];
            }
        }

        return array_map('array_values', $porTipo);
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

            $this->salvarLider($idProjeto, (int) $dados['lider_id']);
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
