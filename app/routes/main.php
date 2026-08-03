<?php

use http\Route;

// Autenticação
Route::GET('/', 'AuthController@index');
Route::POST('/', 'AuthController@index');
Route::GET('/login', 'AuthController@index');
Route::POST('/login', 'AuthController@index');

// Tipos de pentest
Route::GET('/gerenciar-pentest', 'TipoPentestController@index');
Route::POST('/gerenciar-pentest', 'TipoPentestController@index');

// Compatibilidade temporária com a URL antiga
Route::GET('/GerenciarPentest', 'TipoPentestController@index');
Route::POST('/GerenciarPentest', 'TipoPentestController@index');