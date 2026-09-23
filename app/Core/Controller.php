<?php

namespace Core;

class Controller
{
    protected function view($name, $data = [])
    {
        extract($data);
         $viewFile = __DIR__ . "/../Views/" . $name . ".php";
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("View $name not found.");
        }
    }

    protected function redirect($url)
    {
        $redirectUrl = (strpos($url, 'http') === 0) ? $url : BASE_URL . ltrim($url, '/');
        header("Location: ". $redirectUrl);
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function post(string $chave, $padrao = null)
    {
        return $_POST[$chave] ?? $padrao;
    }

    protected function query(string $chave, $padrao = null)
    {
        return $_GET[$chave] ?? $padrao;
    }

    protected function json(array $dados): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
