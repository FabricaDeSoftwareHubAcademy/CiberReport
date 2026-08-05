<?php

use http\Route;

Route::GET('/', 'AuthController@index');
Route::POST('/', 'AuthController@index');

Route::GET('/gerenciar-pentest','TipoPentestController@index');
Route::POST('/gerenciar-pentest','TipoPentestController@index');