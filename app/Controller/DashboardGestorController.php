<?php

namespace Controller;

use Core\Controller;

class DashboardGestorController extends Controller
{
    public function index()
    {
        $this->view('dashboard_gestor');
    }
}
