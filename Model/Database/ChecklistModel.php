<?php

class ChecklistModel
{
    private PDO $pdo;

    public string $msgErro = '';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarChecklist(): array
    {
        $consultaChecklist = $this->pdo->query(
            'SELECT
                id,
                nome,
                descricao,
                categoria,
                habilitado
            FROM checklist
            ORDER BY nome'
        );

        return $consultaChecklist->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function listarChecklistAtivos(): array
    {
        $consultaChecklist = $this->pdo->query(
            'SELECT
                id,
                nome,
                descricao,
                categoria,
                habilitado
            FROM checklist
            WHERE habilitado = 1
            ORDER BY nome'
        );

        return $consultaChecklist->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function buscarChecklist(
        int $idChecklist
    ): array|false {
        $consultaChecklist = $this->pdo->prepare(
            'SELECT
                id,
                nome,
                descricao,
                categoria,
                habilitado
            FROM checklist
            WHERE id = :id'
        );

        $consultaChecklist->execute([
            'id' => $idChecklist
        ]);

        return $consultaChecklist->fetch(
            PDO::FETCH_ASSOC
        );
    }

    public function cadastrarChecklist(
        string $nomeChecklist,
        string $descricaoChecklist,
        string $categoriaChecklist,
        array $itensIdsChecklist
    ): int {
        try {
            $this->pdo->beginTransaction();

            $consultaChecklist = $this->pdo->prepare(
                'INSERT INTO checklist (
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
                )'
            );

            $consultaChecklist->execute([
                'nome' => $nomeChecklist,
                'descricao' => $descricaoChecklist,
                'categoria' => $categoriaChecklist
            ]);

            $idChecklist = (int) $this->pdo->lastInsertId();

            $this->salvarVinculosChecklist(
                $idChecklist,
                $itensIdsChecklist
            );

            $this->pdo->commit();

            return $idChecklist;
        } catch (Throwable $erroChecklist) {
            $this->desfazerTransacaoChecklist();

            throw $erroChecklist;
        }
    }

    public function atualizarChecklist(
        int $idChecklist,
        string $nomeChecklist,
        string $descricaoChecklist,
        string $categoriaChecklist,
        array $itensIdsChecklist
    ): bool {
        if (!$this->buscarChecklist($idChecklist)) {
            return false;
        }

        try {
            $this->pdo->beginTransaction();

            $consultaChecklist = $this->pdo->prepare(
                'UPDATE checklist
                SET
                    nome = :nome,
                    descricao = :descricao,
                    categoria = :categoria
                WHERE id = :id'
            );

            $consultaChecklist->execute([
                'id' => $idChecklist,
                'nome' => $nomeChecklist,
                'descricao' => $descricaoChecklist,
                'categoria' => $categoriaChecklist
            ]);

            $this->excluirVinculosChecklist(
                $idChecklist
            );

            $this->salvarVinculosChecklist(
                $idChecklist,
                $itensIdsChecklist
            );

            $this->pdo->commit();

            return true;
        } catch (Throwable $erroChecklist) {
            $this->desfazerTransacaoChecklist();

            throw $erroChecklist;
        }
    }

    private function salvarVinculosChecklist(
        int $idChecklist,
        array $itensIdsChecklist
    ): void {
        $itensIdsChecklist =
            $this->filtrarItensExistentesChecklist(
                $itensIdsChecklist
            );

        if (empty($itensIdsChecklist)) {
            return;
        }

        $consultaVinculoChecklist = $this->pdo->prepare(
            'INSERT INTO checklist_item_vinculo (
                checklist_id,
                item_id
            )
            VALUES (
                :checklist_id,
                :item_id
            )'
        );

        foreach ($itensIdsChecklist as $idItemChecklist) {
            $consultaVinculoChecklist->execute([
                'checklist_id' => $idChecklist,
                'item_id' => $idItemChecklist
            ]);
        }
    }

    private function filtrarItensExistentesChecklist(
        array $itensIdsChecklist
    ): array {
        $itensIdsChecklist = array_map(
            'intval',
            $itensIdsChecklist
        );

        $itensIdsChecklist = array_filter(
            $itensIdsChecklist,
            static fn(int $idItemChecklist): bool =>
            $idItemChecklist > 0
        );

        $itensIdsChecklist = array_values(
            array_unique($itensIdsChecklist)
        );

        if (empty($itensIdsChecklist)) {
            return [];
        }

        $marcadoresChecklist = implode(
            ',',
            array_fill(
                0,
                count($itensIdsChecklist),
                '?'
            )
        );

        $consultaItensChecklist = $this->pdo->prepare(
            "SELECT id
            FROM checklist_item_catalogo
            WHERE id IN ({$marcadoresChecklist})"
        );

        $consultaItensChecklist->execute(
            $itensIdsChecklist
        );

        return array_map(
            'intval',
            $consultaItensChecklist->fetchAll(
                PDO::FETCH_COLUMN
            )
        );
    }

    private function excluirVinculosChecklist(
        int $idChecklist
    ): void {
        $consultaVinculosChecklist = $this->pdo->prepare(
            'DELETE FROM checklist_item_vinculo
            WHERE checklist_id = :checklist_id'
        );

        $consultaVinculosChecklist->execute([
            'checklist_id' => $idChecklist
        ]);
    }

    public function excluirChecklist(
        int $idChecklist
    ): bool {
        if (!$this->buscarChecklist($idChecklist)) {
            return false;
        }

        try {
            $this->pdo->beginTransaction();

            $this->excluirVinculosChecklist(
                $idChecklist
            );

            $consultaChecklist = $this->pdo->prepare(
                'DELETE FROM checklist
                WHERE id = :id'
            );

            $consultaChecklist->execute([
                'id' => $idChecklist
            ]);

            $this->pdo->commit();

            return true;
        } catch (Throwable $erroChecklist) {
            $this->desfazerTransacaoChecklist();

            throw $erroChecklist;
        }
    }

    public function alterarStatusChecklist(
        int $idChecklist,
        int $statusChecklist
    ): bool {
        if (!$this->buscarChecklist($idChecklist)) {
            return false;
        }

        $consultaChecklist = $this->pdo->prepare(
            'UPDATE checklist
            SET habilitado = :habilitado
            WHERE id = :id'
        );

        return $consultaChecklist->execute([
            'id' => $idChecklist,
            'habilitado' => $statusChecklist === 1 ? 1 : 0
        ]);
    }

    public function buscarComItensChecklist(
        int $idChecklist
    ): array|false {
        $checklist = $this->buscarChecklist(
            $idChecklist
        );

        if ($checklist === false) {
            return false;
        }

        $consultaItensChecklist = $this->pdo->prepare(
            'SELECT
                item.id,
                item.titulo,
                item.referencia,
                item.obrigatorio,
                item.habilitado
            FROM checklist_item_vinculo AS vinculo
            INNER JOIN checklist_item_catalogo AS item
                ON item.id = vinculo.item_id
            WHERE vinculo.checklist_id = :checklist_id
            ORDER BY item.titulo'
        );

        $consultaItensChecklist->execute([
            'checklist_id' => $idChecklist
        ]);

        $checklist['itens'] =
            $consultaItensChecklist->fetchAll(
                PDO::FETCH_ASSOC
            );

        return $checklist;
    }

    public function listarCategoriasChecklist(): array
    {
        $consultaCategoriasChecklist = $this->pdo->query(
            "SELECT DISTINCT categoria
            FROM checklist
            WHERE categoria IS NOT NULL
              AND TRIM(categoria) <> ''
            ORDER BY categoria"
        );

        return $consultaCategoriasChecklist->fetchAll(
            PDO::FETCH_COLUMN
        );
    }

    public function listarItensCatalogoChecklist(): array
    {
        $consultaItensChecklist = $this->pdo->query(
            'SELECT
                id,
                titulo,
                referencia,
                obrigatorio,
                habilitado
            FROM checklist_item_catalogo
            WHERE habilitado = 1
            ORDER BY titulo'
        );

        return $consultaItensChecklist->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function buscarItemCatalogoChecklist(
        int $idItemChecklist
    ): array|false {
        $consultaItemChecklist = $this->pdo->prepare(
            'SELECT
                id,
                titulo,
                referencia,
                obrigatorio,
                habilitado
            FROM checklist_item_catalogo
            WHERE id = :id'
        );

        $consultaItemChecklist->execute([
            'id' => $idItemChecklist
        ]);

        return $consultaItemChecklist->fetch(
            PDO::FETCH_ASSOC
        );
    }

    public function cadastrarItemCatalogoChecklist(
        string $tituloItemChecklist,
        string $referenciaItemChecklist,
        int $obrigatorioItemChecklist
    ): array|false {
        $itemExistenteChecklist =
            $this->buscarItemPorTituloChecklist(
                $tituloItemChecklist
            );

        if ($itemExistenteChecklist !== false) {
            $idItemChecklist = (int) (
                $itemExistenteChecklist['id']
            );

            if (
                (int) $itemExistenteChecklist['habilitado']
                === 0
            ) {
                $this->reativarItemCatalogoChecklist(
                    $idItemChecklist,
                    $referenciaItemChecklist,
                    $obrigatorioItemChecklist
                );

                return $this->buscarItemCatalogoChecklist(
                    $idItemChecklist
                );
            }

            return $itemExistenteChecklist;
        }

        $consultaItemChecklist = $this->pdo->prepare(
            'INSERT INTO checklist_item_catalogo (
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
            )'
        );

        $consultaItemChecklist->execute([
            'titulo' => $tituloItemChecklist,
            'referencia' =>
            $referenciaItemChecklist !== ''
                ? $referenciaItemChecklist
                : null,
            'obrigatorio' =>
            $obrigatorioItemChecklist === 1 ? 1 : 0
        ]);

        return $this->buscarItemCatalogoChecklist(
            (int) $this->pdo->lastInsertId()
        );
    }

    private function buscarItemPorTituloChecklist(
        string $tituloItemChecklist,
        ?int $idIgnoradoChecklist = null
    ): array|false {
        $sqlChecklist = '
            SELECT
                id,
                titulo,
                referencia,
                obrigatorio,
                habilitado
            FROM checklist_item_catalogo
            WHERE titulo = :titulo
        ';

        $parametrosChecklist = [
            'titulo' => $tituloItemChecklist
        ];

        if ($idIgnoradoChecklist !== null) {
            $sqlChecklist .= ' AND id <> :id';

            $parametrosChecklist['id'] =
                $idIgnoradoChecklist;
        }

        $sqlChecklist .= ' LIMIT 1';

        $consultaItemChecklist = $this->pdo->prepare(
            $sqlChecklist
        );

        $consultaItemChecklist->execute(
            $parametrosChecklist
        );

        return $consultaItemChecklist->fetch(
            PDO::FETCH_ASSOC
        );
    }

    private function reativarItemCatalogoChecklist(
        int $idItemChecklist,
        string $referenciaItemChecklist,
        int $obrigatorioItemChecklist
    ): void {
        $consultaItemChecklist = $this->pdo->prepare(
            'UPDATE checklist_item_catalogo
            SET
                referencia = :referencia,
                obrigatorio = :obrigatorio,
                habilitado = 1
            WHERE id = :id'
        );

        $consultaItemChecklist->execute([
            'id' => $idItemChecklist,
            'referencia' =>
            $referenciaItemChecklist !== ''
                ? $referenciaItemChecklist
                : null,
            'obrigatorio' =>
            $obrigatorioItemChecklist === 1 ? 1 : 0
        ]);
    }

    public function atualizarItemCatalogoChecklist(
        int $idItemChecklist,
        string $tituloItemChecklist,
        string $referenciaItemChecklist,
        int $obrigatorioItemChecklist
    ): array|false {
        if (
            !$this->buscarItemCatalogoChecklist(
                $idItemChecklist
            )
        ) {
            return false;
        }

        if (
            $this->buscarItemPorTituloChecklist(
                $tituloItemChecklist,
                $idItemChecklist
            )
        ) {
            return false;
        }

        $consultaItemChecklist = $this->pdo->prepare(
            'UPDATE checklist_item_catalogo
            SET
                titulo = :titulo,
                referencia = :referencia,
                obrigatorio = :obrigatorio
            WHERE id = :id'
        );

        $consultaItemChecklist->execute([
            'id' => $idItemChecklist,
            'titulo' => $tituloItemChecklist,
            'referencia' =>
            $referenciaItemChecklist !== ''
                ? $referenciaItemChecklist
                : null,
            'obrigatorio' =>
            $obrigatorioItemChecklist === 1 ? 1 : 0
        ]);

        return $this->buscarItemCatalogoChecklist(
            $idItemChecklist
        );
    }

    private function contarVinculosItemChecklist(
        int $idItemChecklist
    ): int {
        $consultaVinculosChecklist = $this->pdo->prepare(
            'SELECT COUNT(*)
            FROM checklist_item_vinculo
            WHERE item_id = :item_id'
        );

        $consultaVinculosChecklist->execute([
            'item_id' => $idItemChecklist
        ]);

        return (int) $consultaVinculosChecklist->fetchColumn();
    }

    public function removerItemCatalogoChecklist(
        int $idItemChecklist
    ): array|false {
        $itemChecklist =
            $this->buscarItemCatalogoChecklist(
                $idItemChecklist
            );

        if ($itemChecklist === false) {
            return false;
        }

        $totalVinculosChecklist =
            $this->contarVinculosItemChecklist(
                $idItemChecklist
            );

        if ($totalVinculosChecklist > 0) {
            $consultaItemChecklist = $this->pdo->prepare(
                'UPDATE checklist_item_catalogo
                SET habilitado = 0
                WHERE id = :id'
            );

            $consultaItemChecklist->execute([
                'id' => $idItemChecklist
            ]);

            return [
                'acao' => 'desativado',
                'vinculos' => $totalVinculosChecklist,
                'mensagem' =>
                'O item foi desativado porque está sendo utilizado em checklists.'
            ];
        }

        $consultaItemChecklist = $this->pdo->prepare(
            'DELETE FROM checklist_item_catalogo
            WHERE id = :id'
        );

        $consultaItemChecklist->execute([
            'id' => $idItemChecklist
        ]);

        return [
            'acao' => 'excluido',
            'vinculos' => 0,
            'mensagem' =>
            'Item excluído definitivamente.'
        ];
    }

    private function desfazerTransacaoChecklist(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }
}
