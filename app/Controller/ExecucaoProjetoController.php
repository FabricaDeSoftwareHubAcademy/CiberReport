<?php

namespace Controller;

use Core\Controller;

class ExecucaoProjetoController extends Controller
{
    public function index(): void
    {
        $this->view('execucaoProjeto');
    }

    public function vulnerabilidade(): void
    {
        $this->view('execucaoVulnerabilidade');
    }
}
