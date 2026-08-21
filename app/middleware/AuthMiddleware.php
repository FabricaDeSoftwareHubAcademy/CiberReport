<?php

namespace Middleware;

use Core\MiddlewareInterface;
use Override;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): bool
    {
        if(isset($_SESSION['usuario_id'])){
            return true;
        }

        header('Location: ' . BASE_URL . 'login');
        return false;
    }
}