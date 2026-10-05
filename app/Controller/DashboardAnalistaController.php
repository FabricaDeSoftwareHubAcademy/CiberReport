<?php

namespace Controller;

use Core\Controller;

class DashboardAnalistaController extends Controller
{
    public function index()
    {
        $this->view('dashboard_analista');
    }
}
