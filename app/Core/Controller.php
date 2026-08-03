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
}