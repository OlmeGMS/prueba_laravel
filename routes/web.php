<?php

// Cargar clases
use \App\Http\Middleware\ApiAuthMiddleware;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/prueba/{nombre?}', function ($nombre = null) {
    $texto = '<h1>Texto desde una ruta</h1>';
    $name = $nombre;
    return view('prueba', array(
        'texto'  => $texto,
        'name' => $name
    ));
});

Route::get('/pruebas/animales', 'PruebaController@index');

Route::get('/test-orm', 'PruebaController@testOrm');

//Rutas del api

// Rutas de pruebas 
Route::get('/usuario/prueba', 'UserController@pruebas');
Route::get('/categoria/prueba', 'CategoryController@pruebas');
Route::get('/post/prueba', 'PostController@pruebas');


//Rutas de Usuario
Route::post('/api/register', 'UserController@registro');
Route::post('/api/login', 'UserController@login');
Route::put('/api/user_update', 'UserController@update');
Route::post('api/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
Route::get('/api/user/detail/{id}', 'UserController@detail');

//Rutas de categorias
Route::resource('/api/category', 'CategoryController');