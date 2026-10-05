<?php

namespace Model;

use Core\Model;

/** Representa os dados de um checklist; o acesso ao banco fica no ChecklistDAO. */
final class ChecklistModel extends Model
{
    public ?int $id = null;
    public string $nome = '';
    public string $descricao = '';
    public int $habilitado = 1;
    public array $itens_ids = [];
}
