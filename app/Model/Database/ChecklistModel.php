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
                c.id,
                c.nome,
                c.descricao,
                c.categoria,
                c.habilitado,
                COALESCE(SUM(item.tempo_estimado_minutos), 0) AS tempo_estimado_total
            FROM checklist AS c
            LEFT JOIN checklist_item_vinculo AS vinculo
                ON vinculo.checklist_id = c.id
            LEFT JOIN checklist_item_catalogo AS item
                ON item.id = vinculo.item_id
            GROUP BY c.id, c.nome, c.descricao, c.categoria, c.habilitado
            ORDER BY c.nome'
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

    public function buscarChecklistPorNomeChecklist(
        string $nomeChecklist,
        ?int $idIgnoradoChecklist = null
    ): array|false {
        $sqlChecklist = '
            SELECT id, nome, descricao, categoria, habilitado
            FROM checklist
            WHERE nome = :nome
        ';

        $parametrosChecklist = ['nome' => $nomeChecklist];

        if ($idIgnoradoChecklist !== null) {
            $sqlChecklist .= ' AND id <> :id';
            $parametrosChecklist['id'] = $idIgnoradoChecklist;
        }

        $sqlChecklist .= ' LIMIT 1';

        $consultaChecklist = $this->pdo->prepare($sqlChecklist);
        $consultaChecklist->execute($parametrosChecklist);

        return $consultaChecklist->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrarChecklist(
        string $nomeChecklist,
        string $descricaoChecklist,
        string $categoriaChecklist,
        array $itensIdsChecklist
    ): int|false {
        if ($this->buscarChecklistPorNomeChecklist($nomeChecklist) !== false) {
            return false;
        }

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

        if ($this->buscarChecklistPorNomeChecklist($nomeChecklist, $idChecklist) !== false) {
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
        $itensIdsChecklist = array_map('intval', $itensIdsChecklist);
        $itensIdsChecklist = array_values(array_unique(array_filter(
            $itensIdsChecklist,
            static fn(int $idItemChecklist): bool => $idItemChecklist > 0
        )));

        if (empty($itensIdsChecklist)) {
            return;
        }

        $itensExistentesChecklist = $this->filtrarItensExistentesChecklist(
            $itensIdsChecklist
        );

        // array_intersect preserva a ordem de $itensIdsChecklist (ordem
        // escolhida pelo usuário), descartando apenas os ids inexistentes.
        $itensValidosChecklist = array_intersect(
            $itensIdsChecklist,
            $itensExistentesChecklist
        );

        if (empty($itensValidosChecklist)) {
            return;
        }

        $consultaVinculoChecklist = $this->pdo->prepare(
            'INSERT INTO checklist_item_vinculo (
                checklist_id,
                item_id,
                ordem
            )
            VALUES (
                :checklist_id,
                :item_id,
                :ordem
            )'
        );

        $ordemChecklist = 1;

        foreach ($itensValidosChecklist as $idItemChecklist) {
            $consultaVinculoChecklist->execute([
                'checklist_id' => $idChecklist,
                'item_id' => $idItemChecklist,
                'ordem' => $ordemChecklist
            ]);

            $ordemChecklist++;
        }
    }

    private function filtrarItensExistentesChecklist(
        array $itensIdsChecklist
    ): array {
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
                item.habilitado,
                item.descricao_resumida,
                item.tempo_estimado_minutos,
                vinculo.ordem
            FROM checklist_item_vinculo AS vinculo
            INNER JOIN checklist_item_catalogo AS item
                ON item.id = vinculo.item_id
            WHERE vinculo.checklist_id = :checklist_id
            ORDER BY vinculo.ordem, item.titulo'
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
                habilitado,
                descricao_resumida,
                tempo_estimado_minutos
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
                habilitado,
                descricao_resumida,
                tempo_estimado_minutos
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
        int $obrigatorioItemChecklist,
        string $descricaoResumidaItemChecklist,
        int $tempoEstimadoItemChecklist
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
                    $obrigatorioItemChecklist,
                    $descricaoResumidaItemChecklist,
                    $tempoEstimadoItemChecklist
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
                descricao_resumida,
                tempo_estimado_minutos,
                habilitado
            )
            VALUES (
                :titulo,
                :referencia,
                :obrigatorio,
                :descricao_resumida,
                :tempo_estimado_minutos,
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
            $obrigatorioItemChecklist === 1 ? 1 : 0,
            'descricao_resumida' =>
            $descricaoResumidaItemChecklist !== ''
                ? $descricaoResumidaItemChecklist
                : null,
            'tempo_estimado_minutos' => $tempoEstimadoItemChecklist
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
                habilitado,
                descricao_resumida,
                tempo_estimado_minutos
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
        int $obrigatorioItemChecklist,
        string $descricaoResumidaItemChecklist,
        int $tempoEstimadoItemChecklist
    ): void {
        $consultaItemChecklist = $this->pdo->prepare(
            'UPDATE checklist_item_catalogo
            SET
                referencia = :referencia,
                obrigatorio = :obrigatorio,
                descricao_resumida = :descricao_resumida,
                tempo_estimado_minutos = :tempo_estimado_minutos,
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
            $obrigatorioItemChecklist === 1 ? 1 : 0,
            'descricao_resumida' =>
            $descricaoResumidaItemChecklist !== ''
                ? $descricaoResumidaItemChecklist
                : null,
            'tempo_estimado_minutos' => $tempoEstimadoItemChecklist
        ]);
    }

    public function atualizarItemCatalogoChecklist(
        int $idItemChecklist,
        string $tituloItemChecklist,
        string $referenciaItemChecklist,
        int $obrigatorioItemChecklist,
        string $descricaoResumidaItemChecklist,
        int $tempoEstimadoItemChecklist
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
                obrigatorio = :obrigatorio,
                descricao_resumida = :descricao_resumida,
                tempo_estimado_minutos = :tempo_estimado_minutos
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
            $obrigatorioItemChecklist === 1 ? 1 : 0,
            'descricao_resumida' =>
            $descricaoResumidaItemChecklist !== ''
                ? $descricaoResumidaItemChecklist
                : null,
            'tempo_estimado_minutos' => $tempoEstimadoItemChecklist
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
