<?php

namespace Core;

use RuntimeException;

final class View
{
    private const VIEWS_PATH = __DIR__ . '/../Views/';

    public static function render(string $view, array $data = []): void
    {
        $view = trim($view, '/\\');

        if (
            $view === ''
            || str_contains($view, '..')
            || !preg_match('#^[a-zA-Z0-9/_-]+$#', $view)
        ) {
            throw new RuntimeException('Nome de View inválido.');
        }

        $viewFile = self::VIEWS_PATH . $view . '.php';

        if (!is_file($viewFile)) {
            throw new RuntimeException("View não encontrada: {$view}");
        }

        extract($data, EXTR_SKIP);

        require $viewFile;
    }
}
