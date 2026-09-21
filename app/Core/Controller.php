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

    /** true quando a requisição atual é um POST. */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /** Leitura segura de um campo de $_POST, com valor padrão. */
    protected function post(string $chave, $padrao = null)
    {
        return $_POST[$chave] ?? $padrao;
    }

    /** Leitura segura de um campo de $_GET, com valor padrão. */
    protected function query(string $chave, $padrao = null)
    {
        return $_GET[$chave] ?? $padrao;
    }

    /**
     * Resposta padrão da API — igual ao jsonResponse() do Infotech: define
     * o Content-Type e encerra a requisição. O HTTP continua 200; quem
     * decide sucesso/erro é o campo 'status' dentro do corpo, nunca o
     * status HTTP real. Contrato do corpo: { status, data?, msg? }.
     */
    protected function json(array $dados): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
