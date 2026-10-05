<?php

namespace DAO;

use PDO;

class ChecklistItemDAO extends DAO
{
    public function listarItensCatalogo(): array
    {
        $consulta = self::conexao()->query(
            'SELECT id, titulo, referencia, obrigatorio, habilitado,
                    descricao_resumida
             FROM checklist_item_catalogo
             WHERE habilitado = 1
             ORDER BY titulo'
        );

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarItem(int $id): array|false
    {
        $consulta = self::conexao()->prepare(
            'SELECT id, titulo, referencia, obrigatorio, habilitado,
                    descricao_resumida
             FROM checklist_item_catalogo
             WHERE id = :id'
        );
        $consulta->execute(['id' => $id]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrar(
        string $titulo,
        string $referencia,
        int $obrigatorio,
        string $descricaoResumida
    ): array|false {
        $existente = $this->buscarPorTitulo($titulo);

        if ($existente !== false) {
            $id = (int) $existente['id'];

            if ((int) $existente['habilitado'] === 0) {
                $this->reativar($id, $referencia, $obrigatorio, $descricaoResumida);
                return $this->buscarItem($id);
            }

            return $existente;
        }

        $consulta = self::conexao()->prepare(
            'INSERT INTO checklist_item_catalogo
                (titulo, referencia, obrigatorio, descricao_resumida, habilitado)
             VALUES (:titulo, :referencia, :obrigatorio, :descricao_resumida, 1)'
        );
        $consulta->execute([
            'titulo' => $titulo,
            'referencia' => $referencia !== '' ? $referencia : null,
            'obrigatorio' => $obrigatorio === 1 ? 1 : 0,
            'descricao_resumida' => $descricaoResumida !== '' ? $descricaoResumida : null
        ]);

        return $this->buscarItem((int) self::conexao()->lastInsertId());
    }

    public function atualizar(
        int $id,
        string $titulo,
        string $referencia,
        int $obrigatorio,
        string $descricaoResumida
    ): array|false {
        if ($this->buscarItem($id) === false || $this->buscarPorTitulo($titulo, $id) !== false) {
            return false;
        }

        $consulta = self::conexao()->prepare(
            'UPDATE checklist_item_catalogo
             SET titulo = :titulo, referencia = :referencia,
                 obrigatorio = :obrigatorio, descricao_resumida = :descricao_resumida
             WHERE id = :id'
        );
        $consulta->execute([
            'id' => $id,
            'titulo' => $titulo,
            'referencia' => $referencia !== '' ? $referencia : null,
            'obrigatorio' => $obrigatorio === 1 ? 1 : 0,
            'descricao_resumida' => $descricaoResumida !== '' ? $descricaoResumida : null
        ]);

        return $this->buscarItem($id);
    }

    public function remover(int $id): array|false
    {
        $item = $this->buscarItem($id);
        if ($item === false) {
            return false;
        }

        $consulta = self::conexao()->prepare(
            'SELECT COUNT(*) FROM checklist_item_vinculo
             WHERE item_id = :item_id AND habilitado = 1'
        );
        $consulta->execute(['item_id' => $id]);
        $vinculos = (int) $consulta->fetchColumn();

        if ($vinculos > 0) {
            $consulta = self::conexao()->prepare(
                'UPDATE checklist_item_catalogo SET habilitado = 0 WHERE id = :id'
            );
            $consulta->execute(['id' => $id]);

            return [
                'acao' => 'desativado',
                'vinculos' => $vinculos,
                'mensagem' => 'O item foi desativado porque está sendo utilizado em checklists.'
            ];
        }

        $consulta = self::conexao()->prepare(
            'DELETE FROM checklist_item_catalogo WHERE id = :id'
        );
        $consulta->execute(['id' => $id]);

        return [
            'acao' => 'excluido',
            'vinculos' => 0,
            'mensagem' => 'Item excluído definitivamente.'
        ];
    }

    public function alterarStatus(int $id, int $status): bool
    {
        if ($this->buscarItem($id) === false) {
            return false;
        }

        $consulta = self::conexao()->prepare(
            'UPDATE checklist_item_catalogo
             SET habilitado = :habilitado
             WHERE id = :id'
        );

        return $consulta->execute([
            'id' => $id,
            'habilitado' => $status === 1 ? 1 : 0
        ]);
    }

    private function buscarPorTitulo(string $titulo, ?int $idIgnorado = null): array|false
    {
        $sql = 'SELECT id, titulo, referencia, obrigatorio, habilitado,
                       descricao_resumida
                FROM checklist_item_catalogo
                WHERE titulo = :titulo';
        $parametros = ['titulo' => $titulo];

        if ($idIgnorado !== null) {
            $sql .= ' AND id <> :id';
            $parametros['id'] = $idIgnorado;
        }

        $consulta = self::conexao()->prepare($sql . ' LIMIT 1');
        $consulta->execute($parametros);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    private function reativar(
        int $id,
        string $referencia,
        int $obrigatorio,
        string $descricaoResumida
    ): void {
        $consulta = self::conexao()->prepare(
            'UPDATE checklist_item_catalogo
             SET referencia = :referencia, obrigatorio = :obrigatorio,
                 descricao_resumida = :descricao_resumida, habilitado = 1
             WHERE id = :id'
        );
        $consulta->execute([
            'id' => $id,
            'referencia' => $referencia !== '' ? $referencia : null,
            'obrigatorio' => $obrigatorio === 1 ? 1 : 0,
            'descricao_resumida' => $descricaoResumida !== '' ? $descricaoResumida : null
        ]);
    }
}
