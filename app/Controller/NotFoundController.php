<?php

namespace Controller;

use http\Request;
use http\Response;

class NotFoundController
{
    public function index(Request $request, Response $response)
    {
        $response::json([
            'error' => true,
            'sucess' => false,
            'message' => 'Descupa, rota não encontrada'
        ], 404);
        return;
    }
}