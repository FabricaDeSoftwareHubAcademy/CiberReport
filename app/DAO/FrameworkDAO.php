<?php

namespace DAO;

use PDO;

class FrameworkDAO extends DAO
{
    public function selectAtivos(): array
    {
        $sql = self::conexao()->prepare(
            "SELECT id, nome, descricao FROM framework WHERE habilitado = 1 ORDER BY nome"
        );
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
