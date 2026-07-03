<?php
class ChecklistModel
{
    private PDO $pdo;
    public $msgErro = "";

public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function listarChecklist(): array
    {
        $sql = $this->pdo->query("
            SELECT id, nome, descricao, categoria, habilitado
            FROM checklist
            ORDER BY nome
        ");

        return $sql->fetchAll();
    }

    public function buscar(int $id): array|false
    {
        $sql = $this->pdo->prepare("
            SELECT id, nome, descricao, categoria, habilitado
            FROM checklist
            WHERE id = :id
        ");

        $sql->execute(['id' => $id]);

        return $sql->fetch();
    }

    public function cadastrarChecklist(
        string $nome,
        string $descricao,
        string $categoria,
        array $itensIds
    ): int {
        try {
            $this->pdo->beginTransaction();

            $sql = $this->pdo->prepare("
                INSERT INTO checklist (
                    nome,
                    descricao,
                    categoria,
                    habilitado
                )
                VALUES (
                    :nome,
                    :descricao,
                    :categoria,
                    1
                )
            ");

            $sql->execute([
                'nome' => $nome,
                'descricao' => $descricao,
                'categoria' => $categoria
            ]);

            $checklistId = (int) $this->pdo->lastInsertId();

            $this->salvarVinculos($checklistId, $itensIds);

            $this->pdo->commit();

            return $checklistId;
        } catch (Throwable $erro) {
            $this->desfazerTransacao();
            throw $erro;
        }
    }

    public function atualizarChecklist(
        int $id,
        string $nome,
        string $descricao,
        string $categoria,
        array $itensIds
    ): bool {
        try {
            $this->pdo->beginTransaction();

            $sql = $this->pdo->prepare("
                UPDATE checklist
                SET nome = :nome,
                    descricao = :descricao,
                    categoria = :categoria
                WHERE id = :id
            ");

            $sql->execute([
                'id' => $id,
                'nome' => $nome,
                'descricao' => $descricao,
                'categoria' => $categoria
            ]);

            $this->excluirVinculosChecklist($id);
            $this->salvarVinculos($id, $itensIds);

            $this->pdo->commit();

            return true;
        } catch (Throwable $erro) {
            $this->desfazerTransacao();
            throw $erro;
        }
    }

    private function salvarVinculos(int $checklistId, array $itensIds): void
    {
        $itensIds = $this->filtrarItensExistentes($itensIds);

        if (empty($itensIds)) {
            return;
        }

        $sql = $this->pdo->prepare("
            INSERT INTO checklist_item_vinculo (
                checklist_id,
                item_id
            )
            VALUES (
                :checklist_id,
                :item_id
            )
        ");

        foreach ($itensIds as $itemId) {
            $sql->execute([
                'checklist_id' => $checklistId,
                'item_id' => $itemId
            ]);
        }
    }

    private function filtrarItensExistentes(array $itensIds): array
    {
        $itensIds = array_map('intval', $itensIds);
        $itensIds = array_filter($itensIds, fn(int $id) => $id > 0);
        $itensIds = array_values(array_unique($itensIds));

        if (empty($itensIds)) {
            return [];
        }

        $marcadores = implode(',', array_fill(0, count($itensIds), '?'));

        $sql = $this->pdo->prepare("
            SELECT id
            FROM checklist_item_catalogo
            WHERE id IN ({$marcadores})
        ");

        $sql->execute($itensIds);

        return array_map('intval', $sql->fetchAll(PDO::FETCH_COLUMN));
    }

    private function excluirVinculosChecklist(int $checklistId): void
    {
        $sql = $this->pdo->prepare("
            DELETE FROM checklist_item_vinculo
            WHERE checklist_id = :checklist_id
        ");

        $sql->execute(['checklist_id' => $checklistId]);
    }

    public function excluirChecklist(int $id): bool
    {
        try {
            $this->pdo->beginTransaction();

            $this->excluirVinculosChecklist($id);

            $sql = $this->pdo->prepare("
                DELETE FROM checklist
                WHERE id = :id
            ");

            $sql->execute(['id' => $id]);

            $this->pdo->commit();

            return $sql->rowCount() > 0;
        } catch (Throwable $erro) {
            $this->desfazerTransacao();
            throw $erro;
        }
    }

    public function alterarStatusChecklist(int $id, int $status): bool
    {
        $sql = $this->pdo->prepare("
            UPDATE checklist
            SET habilitado = :habilitado
            WHERE id = :id
        ");

        $sql->execute([
            'id' => $id,
            'habilitado' => $status
        ]);

        return $sql->rowCount() > 0;
    }

    public function buscarComItensChecklist(int $id): array|false
    {
        $checklist = $this->buscar($id);

        if (!$checklist) {
            return false;
        }

        $sql = $this->pdo->prepare("
            SELECT
                item.id,
                item.titulo,
                item.referencia,
                item.obrigatorio,
                item.habilitado
            FROM checklist_item_vinculo AS vinculo
            INNER JOIN checklist_item_catalogo AS item
                ON item.id = vinculo.item_id
            WHERE vinculo.checklist_id = :checklist_id
            ORDER BY item.titulo
        ");

        $sql->execute(['checklist_id' => $id]);

        $checklist['itens'] = $sql->fetchAll();

        return $checklist;
    }

    public function listarCategoriasChecklist(): array
    {
        $sql = $this->pdo->query("
            SELECT DISTINCT categoria
            FROM checklist
            WHERE categoria IS NOT NULL
              AND TRIM(categoria) <> ''
            ORDER BY categoria
        ");

        return $sql->fetchAll(PDO::FETCH_COLUMN);
    }

    public function listarItensCatalogoChecklist(): array
    {
        $sql = $this->pdo->query("
            SELECT
                id,
                titulo,
                referencia,
                obrigatorio,
                habilitado
            FROM checklist_item_catalogo
            WHERE habilitado = 1
            ORDER BY titulo
        ");

        return $sql->fetchAll();
    }

    public function buscarItemCatalogo(int $id): array|false
    {
        $sql = $this->pdo->prepare("
            SELECT
                id,
                titulo,
                referencia,
                obrigatorio,
                habilitado
            FROM checklist_item_catalogo
            WHERE id = :id
        ");

        $sql->execute(['id' => $id]);

        return $sql->fetch();
    }

    public function cadastrarItemCatalogoChecklist(
        string $titulo,
        string $referencia,
        int $obrigatorio
    ): array {
        $itemExistente = $this->buscarItemPorTitulo($titulo);

        if ($itemExistente) {
            if ((int) $itemExistente['habilitado'] === 0) {
                $this->reativarItemCatalogo((int) $itemExistente['id']);

                $itemExistente['habilitado'] = 1;
            }

            return $itemExistente;
        }

        $sql = $this->pdo->prepare("
            INSERT INTO checklist_item_catalogo (
                titulo,
                referencia,
                obrigatorio,
                habilitado
            )
            VALUES (
                :titulo,
                :referencia,
                :obrigatorio,
                1
            )
        ");

        $sql->execute([
            'titulo' => $titulo,
            'referencia' => $referencia !== '' ? $referencia : null,
            'obrigatorio' => $obrigatorio
        ]);

        return $this->buscarItemCatalogo(
            (int) $this->pdo->lastInsertId()
        );
    }

    private function buscarItemPorTitulo(
        string $titulo,
        ?int $ignorarId = null
    ): array|false {
        $sqlTexto = "
            SELECT
                id,
                titulo,
                referencia,
                obrigatorio,
                habilitado
            FROM checklist_item_catalogo
            WHERE titulo = :titulo
        ";

        $parametros = ['titulo' => $titulo];

        if ($ignorarId !== null) {
            $sqlTexto .= ' AND id <> :id';
            $parametros['id'] = $ignorarId;
        }

        $sqlTexto .= ' LIMIT 1';

        $sql = $this->pdo->prepare($sqlTexto);
        $sql->execute($parametros);

        return $sql->fetch();
    }

    private function reativarItemCatalogo(int $id): void
    {
        $sql = $this->pdo->prepare("
            UPDATE checklist_item_catalogo
            SET habilitado = 1
            WHERE id = :id
        ");

        $sql->execute(['id' => $id]);
    }

    public function atualizarItemCatalogoCheckList(
        int $id,
        string $titulo,
        string $referencia,
        int $obrigatorio
    ): array|false {
        if (!$this->buscarItemCatalogo($id)) {
            return false;
        }

        if ($this->buscarItemPorTitulo($titulo, $id)) {
            return false;
        }

        $sql = $this->pdo->prepare("
            UPDATE checklist_item_catalogo
            SET titulo = :titulo,
                referencia = :referencia,
                obrigatorio = :obrigatorio
            WHERE id = :id
        ");

        $sql->execute([
            'id' => $id,
            'titulo' => $titulo,
            'referencia' => $referencia !== '' ? $referencia : null,
            'obrigatorio' => $obrigatorio
        ]);

        return $this->buscarItemCatalogo($id);
    }

    public function contarVinculosItem(int $id): int
    {
        $sql = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM checklist_item_vinculo
            WHERE item_id = :item_id
        ");

        $sql->execute(['item_id' => $id]);

        return (int) $sql->fetchColumn();
    }

    public function removerItemCatalogoChecklist(int $id): array|false
    {
        $item = $this->buscarItemCatalogo($id);

        if (!$item) {
            return false;
        }

        $totalVinculos = $this->contarVinculosItem($id);

        if ($totalVinculos > 0) {
            $sql = $this->pdo->prepare("
                UPDATE checklist_item_catalogo
                SET habilitado = 0
                WHERE id = :id
            ");

            $sql->execute(['id' => $id]);

            return [
                'acao' => 'desativado',
                'vinculos' => $totalVinculos,
                'mensagem' => 'O item foi desativado porque está sendo utilizado em checklists.'
            ];
        }

        $sql = $this->pdo->prepare("
            DELETE FROM checklist_item_catalogo
            WHERE id = :id
        ");

        $sql->execute(['id' => $id]);

        return [
            'acao' => 'excluido',
            'vinculos' => 0,
            'mensagem' => 'Item excluído definitivamente.'
        ];
    }

    private function desfazerTransacao(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }
}
