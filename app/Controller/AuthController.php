<?php 

namespace Controller;
use Core\Controller;


class AuthController extends Controller
{
    public function index()
    {
        $this->view('login');
    }
}