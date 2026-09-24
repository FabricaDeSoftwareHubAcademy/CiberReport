<?php

namespace Model;

use Core\Model;
use DAO\FrameworkDAO;

final class FrameworkModel extends Model
{
    public function getAllRows(): array
    {
        $this->rows = (new FrameworkDAO())->selectAtivos();

        return $this->rows;
    }
}
