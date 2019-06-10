<?php

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

use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/prueba', 'UserController@pruebas');


// Rutas del usuario

Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');
Route::put('/update', 'UserController@update');
Route::post('/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('/detail/{id}', 'UserController@detail');

//Ruta de las categorias

Route::resource('/category', 'CategoryController');

//Ruta de los post

Route::resource('/post', 'PostController');
Route::post('/post/upload', 'PostController@upload');
Route::get('/post/image/{filename}', 'PostController@getImage');
Route::get('/post/category/{id}', 'PostController@getPostsByCategory');
Route::get('/post/user/{id}', 'PostController@getPostsByUser');

//Ruta de mails

Route::post('mail/send', 'MailController@send');

