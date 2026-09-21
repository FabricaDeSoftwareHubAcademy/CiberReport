<?php

namespace Core;

/**
 * Classe-pai de todas as entidades do projeto (igual ao Infotech).
 * Guarda a última coleção de resultados lida por getAllRows().
 */
abstract class Model
{
    public array $rows = [];
}
