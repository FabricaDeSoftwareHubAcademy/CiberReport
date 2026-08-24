<?php

use http\Route;
use Middleware\AuthMiddleware;

// Públicas
Route::GET('/', 'AuthController@exibirLogin');
Route::GET('/login', 'AuthController@exibirLogin');
Route::POST('/login', 'AuthController@login');

Route::GET('/recuperar-senha', 'AuthController@exibirRecuperarSenha');
Route::POST('/recuperar-senha', 'AuthController@recuperarSenha');

Route::GET('/redefinir-senha', 'AuthController@exibirRedefinirSenha');
Route::POST('/redefinir-senha', 'AuthController@redefinirSenha');

// Exigem autenticação
Route::middleware([AuthMiddleware::class], function (): void {
    Route::POST('/logout', 'AuthController@logout');

    Route::GET('/gerenciar-pentest', 'TipoPentestController@index');
    Route::POST('/gerenciar-pentest', 'TipoPentestController@index');

    Route::GET('/usuario', 'GerenciamentoUsuarioController@index');
    Route::POST('/usuario', 'GerenciamentoUsuarioController@index');

    Route::GET('/checklist', 'ChecklistController@index');
    Route::POST('/checklist', 'ChecklistController@index');

    Route::GET('/vulnerabilidades', 'VulnerabilidadesController@index');
    Route::POST('/vulnerabilidades', 'VulnerabilidadesController@index');

    Route::GET('/cliente-empresa', 'CadastroEmpresaController@index');
    Route::POST('/cliente-empresa', 'CadastroEmpresaController@index');

    Route::GET('/gerenciamento-acesso', 'GerenciarAcessoController@index');
    Route::POST('/gerenciamento-acesso', 'GerenciarAcessoController@index');

    Route::GET('/gerenciamento-projeto', 'ProjetoController@index');
    Route::POST('/gerenciamento-projeto', 'ProjetoController@index');

    Route::GET('/dashboard-gestor', 'DashboardGestorController@index');
    Route::POST('/dashboard-gestor', 'DashboardGestorController@index');
    
    Route::GET('/projetos-alocados','ProjetosAlocadosController@index');
    Route::POST('/projetos-alocados','ProjetosAlocadosController@index');
});


