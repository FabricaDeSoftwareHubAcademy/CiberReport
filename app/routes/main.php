<?php

use http\Route;

Route::GET('/', 'AuthController@index');
Route::POST('/', 'AuthController@index');

Route::GET('/gerenciar-pentest','TipoPentestController@index');
Route::POST('/gerenciar-pentest','TipoPentestController@index');

Route::GET('/usuario','GerenciamentoUsuarioController@index');
Route::POST('/usuario','GerenciamentoUsuarioController@index');

Route::GET('/checklist','ChecklistController@index');
Route::POST('/checklist','ChecklistController@index');

Route::GET('/vulnerabilidades','VulnerabilidadesController@index');
Route::POST('/vulnerabilidades','VulnerabilidadesController@index');

Route::GET('/cliente-empresa','CadastroEmpresaController@index');
Route::POST('/cliente-empresa','CadastroEmpresaController@index');

Route::GET('/gerenciamento-acesso','GerenciarAcessoController@index');
Route::POST('/gerenciamento-acesso','GerenciarAcessoController@index');

Route::GET('/gerenciamento-projeto','ProjetoController@index');
Route::POST('/gerenciamento-projeto','ProjetoController@index');

Route::GET('/dashboard-gestor','DashboardGestorController@index');
Route::POST('/dashboard-gestor','DashboardGestorController@index');