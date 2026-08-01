<?php

namespace Controller;

use Core\View;

class HomeController
{
    public function index(): void
    {
        View::render('login', [
            'title' => 'Login',
        ]);
    }
}
