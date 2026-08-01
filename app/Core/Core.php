<?php 

namespace Core;

use http\Request;
use http\Response;

class Core
{
    public static function dispatch(array $routes)
    {
        $url = '/';

        isset($_GET['url']) && $url .= $_GET['url'];

        $url !== '/' && $url = rtrim($url, '/');
        
        $prefixController = 'Controller\\';
        $routeFound = false;

        foreach($routes as $route) {
            $pattern = '#^'. preg_replace('/{id}/', '([\w-]+)',$route['path']) . '$#';

            if(preg_match($pattern,$url,$matches)){
              array_shift($matches);

              $routeFound = true;

              if($route['method'] !== Request::method()){
                Response::json([
                    'error' => true,
                    'sucess' => false,
                    'message' => 'Desculpa, metodo não encontrado'
                ],405);
                return;
              }
              
              [$controller, $action] = explode('@', $route['action']);

              $controller = $prefixController . $controller;
              $extendController = new $controller();
              $extendController->$action(new Request, new Response, $matches);
            }
        }

         if(!$routeFound){
            $controller = $prefixController."NotFoundController";
            $extendController = new $controller;
            $extendController->index(new Request, new Response);       
        }
    }
}