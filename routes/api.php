<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// RUTAS DE USUARIO
Route::post('/register',[UserController::class, 'register']);
Route::post('/login',[UserController::class, 'login']);
Route::put('/user/update',[UserController::class, 'update'])->middleware('api.auth');
Route::post('/user/upload',[UserController::class, 'upload'])->middleware('api.auth');
Route::get('/user/avatar/{filename}',[UserController::class, 'getImage']);
Route::get('/user/details/{id}',[UserController::class, 'details']);


// RUTAS DE CATEGORIAS
Route::resource('/category', CategoryController::class);











