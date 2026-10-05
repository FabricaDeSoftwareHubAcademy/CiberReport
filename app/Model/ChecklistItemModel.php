<?php

namespace Model;

use Core\Model;

/** Representa um item do catálogo de checklist. */
final class ChecklistItemModel extends Model
{
    public ?int $id = null;
    public string $titulo = '';
    public string $referencia = '';
    public int $obrigatorio = 1;
    public string $descricao_resumida = '';
    public int $habilitado = 1;
}
