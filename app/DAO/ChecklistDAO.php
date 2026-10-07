<?php

namespace DAO;

use Core\DAO;
use PDO;
use Throwable;

class ChecklistDAO extends DAO
{
    public function listarChecklist(): array
    {
        $consultaChecklist = self::conexao()->query(
            'SELECT
                id,
                nome,
                descricao,
                habilitado
            FROM checklist
            ORDER BY nome'
        );

        return $consultaChecklist->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarChecklistAtivos(): array
    {
        $consultaChecklist = self::conexao()->query(
            'SELECT
                id,
                nome,
                descricao,
                habilitado
            FROM checklist
            WHERE habilitado = 1
            ORDER BY nome'
        );

        return $consultaChecklist->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarChecklist(int $idChecklist): array|false
    {
        $consultaChecklist = self::conexao()->prepare(
            'SELECT
                id,
                nome,
                descricao,
                habilitado
            FROM checklist
            WHERE id = :id'
        );

        $consultaChecklist->execute(['id' => $idChecklist]);

        return $consultaChecklist->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarChecklistPorNomeChecklist(
        string $nomeChecklist,
        ?int $idIgnoradoChecklist = null
    ): array|false {
        $sqlChecklist = '
            SELECT id, nome, descricao, habilitado
            FROM checklist
            WHERE nome = :nome
        ';

        $parametrosChecklist = ['nome' => $nomeChecklist];

        if ($idIgnoradoChecklist !== null) {
            $sqlChecklist .= ' AND id <> :id';
            $parametrosChecklist['id'] = $idIgnoradoChecklist;
        }

        $sqlChecklist .= ' LIMIT 1';

        $consultaChecklist = self::conexao()->prepare($sqlChecklist);
        $consultaChecklist->execute($parametrosChecklist);

        return $consultaChecklist->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrarChecklist(
        string $nomeChecklist,
        string $descricaoChecklist,
        array $itensIdsChecklist
    ): int|false {
        if ($this->buscarChecklistPorNomeChecklist($nomeChecklist) !== false) {
            return false;
        }

        try {
            self::conexao()->beginTransaction();

            $consultaChecklist = self::conexao()->prepare(
                'INSERT INTO checklist (
                    nome,
                    descricao,
                    habilitado
                )
                VALUES (
                    :nome,
                    :descricao,
                    1
                )'
            );

            $consultaChecklist->execute([
                'nome' => $nomeChecklist,
                'descricao' => $descricaoChecklist
            ]);

            $idChecklist = (int) self::conexao()->lastInsertId();

            $this->salvarVinculosChecklist($idChecklist, $itensIdsChecklist);

            self::conexao()->commit();

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
        array $itensIdsChecklist
    ): bool {
        if (!$this->buscarChecklist($idChecklist)) {
            return false;
        }

        if ($this->buscarChecklistPorNomeChecklist($nomeChecklist, $idChecklist) !== false) {
            return false;
        }

        try {
            self::conexao()->beginTransaction();

            $consultaChecklist = self::conexao()->prepare(
                'UPDATE checklist
                SET
                    nome = :nome,
                    descricao = :descricao
                WHERE id = :id'
            );

            $consultaChecklist->execute([
                'id' => $idChecklist,
                'nome' => $nomeChecklist,
                'descricao' => $descricaoChecklist
            ]);

            $this->reconciliarVinculosChecklist($idChecklist, $itensIdsChecklist);

            self::conexao()->commit();

            return true;
        } catch (Throwable $erroChecklist) {
            $this->desfazerTransacaoChecklist();

            throw $erroChecklist;
        }
    }

    public function excluirChecklist(int $idChecklist): bool
    {
        return $this->alterarStatusChecklist($idChecklist, 0);
    }

    public function alterarStatusChecklist(int $idChecklist, int $statusChecklist): bool
    {
        if (!$this->buscarChecklist($idChecklist)) {
            return false;
        }

        $consultaChecklist = self::conexao()->prepare(
            'UPDATE checklist
            SET habilitado = :habilitado
            WHERE id = :id'
        );

        return $consultaChecklist->execute([
            'id' => $idChecklist,
            'habilitado' => $statusChecklist === 1 ? 1 : 0
        ]);
    }

    public function buscarComItensChecklist(int $idChecklist): array|false
    {
        $checklist = $this->buscarChecklist($idChecklist);

        if ($checklist === false) {
            return false;
        }

        $consultaItensChecklist = self::conexao()->prepare(
            'SELECT
                item.id,
                item.titulo,
                item.referencia,
                item.obrigatorio,
                item.habilitado,
                item.descricao_resumida,
                vinculo.ordem
            FROM checklist_item_vinculo AS vinculo
            INNER JOIN checklist_item_catalogo AS item
                ON item.id = vinculo.item_id
            WHERE vinculo.checklist_id = :checklist_id
              AND vinculo.habilitado = 1
            ORDER BY vinculo.ordem, item.titulo'
        );

        $consultaItensChecklist->execute(['checklist_id' => $idChecklist]);

        $checklist['itens'] = $consultaItensChecklist->fetchAll(PDO::FETCH_ASSOC);

        return $checklist;
    }

    /* Catalog item operations live in ChecklistItemDAO. */

    public function buscarItemCatalogoChecklist(int $idItemChecklist): array|false
    {
        $consultaItemChecklist = self::conexao()->prepare(
            'SELECT
                id,
                titulo,
                referencia,
                obrigatorio,
                habilitado,
                descricao_resumida
            FROM checklist_item_catalogo
            WHERE id = :id'
        );

        $consultaItemChecklist->execute(['id' => $idItemChecklist]);

        return $consultaItemChecklist->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrarItemCatalogoChecklist(
        string $tituloItemChecklist,
        string $referenciaItemChecklist,
        int $obrigatorioItemChecklist,
        string $descricaoResumidaItemChecklist
    ): array|false {
        $itemExistenteChecklist = $this->buscarItemPorTituloChecklist($tituloItemChecklist);

        if ($itemExistenteChecklist !== false) {
            $idItemChecklist = (int) $itemExistenteChecklist['id'];

            if ((int) $itemExistenteChecklist['habilitado'] === 0) {
                $this->reativarItemCatalogoChecklist(
                    $idItemChecklist,
                    $referenciaItemChecklist,
                    $obrigatorioItemChecklist,
                    $descricaoResumidaItemChecklist
                );

                return $this->buscarItemCatalogoChecklist($idItemChecklist);
            }

            return $itemExistenteChecklist;
        }

        $consultaItemChecklist = self::conexao()->prepare(
            'INSERT INTO checklist_item_catalogo (
                titulo,
                referencia,
                obrigatorio,
                descricao_resumida,
                habilitado
            )
            VALUES (
                :titulo,
                :referencia,
                :obrigatorio,
                :descricao_resumida,
                1
            )'
        );

        $consultaItemChecklist->execute([
            'titulo' => $tituloItemChecklist,
            'referencia' => $referenciaItemChecklist !== '' ? $referenciaItemChecklist : null,
            'obrigatorio' => $obrigatorioItemChecklist === 1 ? 1 : 0,
            'descricao_resumida' => $descricaoResumidaItemChecklist !== ''
                ? $descricaoResumidaItemChecklist
                : null
        ]);

        return $this->buscarItemCatalogoChecklist((int) self::conexao()->lastInsertId());
    }

    public function atualizarItemCatalogoChecklist(
        int $idItemChecklist,
        string $tituloItemChecklist,
        string $referenciaItemChecklist,
        int $obrigatorioItemChecklist,
        string $descricaoResumidaItemChecklist
    ): array|false {
        if (!$this->buscarItemCatalogoChecklist($idItemChecklist)) {
            return false;
        }

        if ($this->buscarItemPorTituloChecklist($tituloItemChecklist, $idItemChecklist)) {
            return false;
        }

        $consultaItemChecklist = self::conexao()->prepare(
            'UPDATE checklist_item_catalogo
            SET
                titulo = :titulo,
                referencia = :referencia,
                obrigatorio = :obrigatorio,
                descricao_resumida = :descricao_resumida
            WHERE id = :id'
        );

        $consultaItemChecklist->execute([
            'id' => $idItemChecklist,
            'titulo' => $tituloItemChecklist,
            'referencia' => $referenciaItemChecklist !== '' ? $referenciaItemChecklist : null,
            'obrigatorio' => $obrigatorioItemChecklist === 1 ? 1 : 0,
            'descricao_resumida' => $descricaoResumidaItemChecklist !== ''
                ? $descricaoResumidaItemChecklist
                : null
        ]);

        return $this->buscarItemCatalogoChecklist($idItemChecklist);
    }

    public function removerItemCatalogoChecklist(int $idItemChecklist): array|false
    {
        $itemChecklist = $this->buscarItemCatalogoChecklist($idItemChecklist);

        if ($itemChecklist === false) {
            return false;
        }

        $totalVinculosChecklist = $this->contarVinculosItemChecklist($idItemChecklist);

        if ($totalVinculosChecklist > 0) {
            $consultaItemChecklist = self::conexao()->prepare(
                'UPDATE checklist_item_catalogo
                SET habilitado = 0
                WHERE id = :id'
            );

            $consultaItemChecklist->execute(['id' => $idItemChecklist]);

            return [
                'acao' => 'desativado',
                'vinculos' => $totalVinculosChecklist,
                'mensagem' => 'O item foi desativado porque está sendo utilizado em checklists.'
            ];
        }

        $consultaItemChecklist = self::conexao()->prepare(
            'DELETE FROM checklist_item_catalogo
            WHERE id = :id'
        );

        $consultaItemChecklist->execute(['id' => $idItemChecklist]);

        return [
            'acao' => 'excluido',
            'vinculos' => 0,
            'mensagem' => 'Item excluído definitivamente.'
        ];
    }

    public function alterarStatusItemCatalogoChecklist(
        int $idItemChecklist,
        int $statusItemChecklist
    ): bool {
        if (!$this->buscarItemCatalogoChecklist($idItemChecklist)) {
            return false;
        }

        $consultaItemChecklist = self::conexao()->prepare(
            'UPDATE checklist_item_catalogo
            SET habilitado = :habilitado
            WHERE id = :id'
        );

        return $consultaItemChecklist->execute([
            'id' => $idItemChecklist,
            'habilitado' => $statusItemChecklist === 1 ? 1 : 0
        ]);
    }

    private function prepararItensValidosChecklist(array $itensIdsChecklist): array
    {
        $itensIdsChecklist = array_map('intval', $itensIdsChecklist);
        $itensIdsChecklist = array_values(array_unique(array_filter(
            $itensIdsChecklist,
            static fn(int $idItemChecklist): bool => $idItemChecklist > 0
        )));

        if (empty($itensIdsChecklist)) {
            return [];
        }

        $itensExistentesChecklist = $this->filtrarItensExistentesChecklist($itensIdsChecklist);

        return array_values(array_intersect($itensIdsChecklist, $itensExistentesChecklist));
    }

    private function salvarVinculosChecklist(int $idChecklist, array $itensIdsChecklist): void
    {
        $itensValidosChecklist = $this->prepararItensValidosChecklist($itensIdsChecklist);

        if (empty($itensValidosChecklist)) {
            return;
        }

        $consultaVinculoChecklist = self::conexao()->prepare(
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

    private function reconciliarVinculosChecklist(int $idChecklist, array $itensIdsChecklist): void
    {
        $itensValidosChecklist = $this->prepararItensValidosChecklist($itensIdsChecklist);
        $vinculosAtuaisChecklist = $this->listarVinculosChecklist($idChecklist);

        $mapaVinculosChecklist = [];
        $itensAtivosAtualmenteChecklist = [];

        foreach ($vinculosAtuaisChecklist as $vinculoChecklist) {
            $idItemVinculoChecklist = (int) $vinculoChecklist['item_id'];
            $mapaVinculosChecklist[$idItemVinculoChecklist] = (int) $vinculoChecklist['id'];

            if ((int) $vinculoChecklist['habilitado'] === 1) {
                $itensAtivosAtualmenteChecklist[] = $idItemVinculoChecklist;
            }
        }

        if (empty($itensValidosChecklist)) {
            $this->inativarVinculosChecklist($idChecklist);
            return;
        }

        $consultaInserirChecklist = self::conexao()->prepare(
            'INSERT INTO checklist_item_vinculo (
                checklist_id,
                item_id,
                ordem,
                habilitado
            )
            VALUES (
                :checklist_id,
                :item_id,
                :ordem,
                1
            )'
        );

        $consultaAtualizarChecklist = self::conexao()->prepare(
            'UPDATE checklist_item_vinculo
            SET ordem = :ordem, habilitado = 1
            WHERE id = :id'
        );

        $ordemChecklist = 1;
        $itensMantidosChecklist = [];

        foreach ($itensValidosChecklist as $idItemChecklist) {
            if (isset($mapaVinculosChecklist[$idItemChecklist])) {
                $consultaAtualizarChecklist->execute([
                    'id' => $mapaVinculosChecklist[$idItemChecklist],
                    'ordem' => $ordemChecklist
                ]);

                $itensMantidosChecklist[] = $idItemChecklist;
            } else {
                $consultaInserirChecklist->execute([
                    'checklist_id' => $idChecklist,
                    'item_id' => $idItemChecklist,
                    'ordem' => $ordemChecklist
                ]);
            }

            $ordemChecklist++;
        }

        $itensParaInativarChecklist = array_diff(
            $itensAtivosAtualmenteChecklist,
            $itensMantidosChecklist
        );

        foreach ($itensParaInativarChecklist as $idItemInativarChecklist) {
            $this->inativarVinculoPorIdChecklist(
                $mapaVinculosChecklist[$idItemInativarChecklist]
            );
        }
    }

    private function listarVinculosChecklist(int $idChecklist): array
    {
        $consultaChecklist = self::conexao()->prepare(
            'SELECT id, item_id, habilitado
            FROM checklist_item_vinculo
            WHERE checklist_id = :checklist_id'
        );

        $consultaChecklist->execute(['checklist_id' => $idChecklist]);

        return $consultaChecklist->fetchAll(PDO::FETCH_ASSOC);
    }

    private function inativarVinculosChecklist(int $idChecklist): void
    {
        $consultaChecklist = self::conexao()->prepare(
            'UPDATE checklist_item_vinculo
            SET habilitado = 0
            WHERE checklist_id = :checklist_id
              AND habilitado = 1'
        );

        $consultaChecklist->execute(['checklist_id' => $idChecklist]);
    }

    private function inativarVinculoPorIdChecklist(int $idVinculoChecklist): void
    {
        $consultaChecklist = self::conexao()->prepare(
            'UPDATE checklist_item_vinculo
            SET habilitado = 0
            WHERE id = :id'
        );

        $consultaChecklist->execute(['id' => $idVinculoChecklist]);
    }

    private function filtrarItensExistentesChecklist(array $itensIdsChecklist): array
    {
        if (empty($itensIdsChecklist)) {
            return [];
        }

        $marcadoresChecklist = implode(
            ',',
            array_fill(0, count($itensIdsChecklist), '?')
        );

        $consultaItensChecklist = self::conexao()->prepare(
            "SELECT id
            FROM checklist_item_catalogo
            WHERE id IN ({$marcadoresChecklist})"
        );

        $consultaItensChecklist->execute($itensIdsChecklist);

        return array_map(
            'intval',
            $consultaItensChecklist->fetchAll(PDO::FETCH_COLUMN)
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
                descricao_resumida
            FROM checklist_item_catalogo
            WHERE titulo = :titulo
        ';

        $parametrosChecklist = ['titulo' => $tituloItemChecklist];

        if ($idIgnoradoChecklist !== null) {
            $sqlChecklist .= ' AND id <> :id';
            $parametrosChecklist['id'] = $idIgnoradoChecklist;
        }

        $sqlChecklist .= ' LIMIT 1';

        $consultaItemChecklist = self::conexao()->prepare($sqlChecklist);
        $consultaItemChecklist->execute($parametrosChecklist);

        return $consultaItemChecklist->fetch(PDO::FETCH_ASSOC);
    }

    private function reativarItemCatalogoChecklist(
        int $idItemChecklist,
        string $referenciaItemChecklist,
        int $obrigatorioItemChecklist,
        string $descricaoResumidaItemChecklist
    ): void {
        $consultaItemChecklist = self::conexao()->prepare(
            'UPDATE checklist_item_catalogo
            SET
                referencia = :referencia,
                obrigatorio = :obrigatorio,
                descricao_resumida = :descricao_resumida,
                habilitado = 1
            WHERE id = :id'
        );

        $consultaItemChecklist->execute([
            'id' => $idItemChecklist,
            'referencia' => $referenciaItemChecklist !== '' ? $referenciaItemChecklist : null,
            'obrigatorio' => $obrigatorioItemChecklist === 1 ? 1 : 0,
            'descricao_resumida' => $descricaoResumidaItemChecklist !== ''
                ? $descricaoResumidaItemChecklist
                : null
        ]);
    }

    private function contarVinculosItemChecklist(int $idItemChecklist): int
    {
        $consultaVinculosChecklist = self::conexao()->prepare(
            'SELECT COUNT(*)
            FROM checklist_item_vinculo
            WHERE item_id = :item_id
              AND habilitado = 1'
        );

        $consultaVinculosChecklist->execute(['item_id' => $idItemChecklist]);

        return (int) $consultaVinculosChecklist->fetchColumn();
    }

    private function desfazerTransacaoChecklist(): void
    {
        if (self::conexao()->inTransaction()) {
            self::conexao()->rollBack();
        }
    }
}
