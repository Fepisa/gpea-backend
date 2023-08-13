<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\LaudoController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\PublicLaudoController;

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

Route::controller(LoginController::class)->group(function(){
    Route::post('login', 'login');
});

Route::resource('laudos-mobile', PublicLaudoController::class);
Route::get('/laudos/codigo/{codigo}', 'App\Http\Controllers\API\PublicLaudoController@getByCodigo');

Route::group(['middleware' => ['auth:sanctum', 'can:view-laudos']], function() {
    Route::get('/users/current', 'App\Http\Controllers\API\UserController@currentUser');
    Route::patch('/users/current', 'App\Http\Controllers\API\UserController@updateCurrentUser');
    Route::get('/laudos/exportar', 'App\Http\Controllers\API\LaudoController@exportCsv');
    Route::get('/laudos/local/{local_geral}/contratante/{contratante}', 'App\Http\Controllers\API\LaudoController@getByLocalAndContratante');
    Route::get('/laudos/{id}', 'App\Http\Controllers\API\LaudoController@show');
    Route::get('/laudos/cod/{codigo}', 'App\Http\Controllers\API\LaudoController@getByCodigo');
    Route::patch('/laudos/observacao', 'App\Http\Controllers\API\LaudoController@updateObservacao');
    Route::post('/laudos/importar', 'App\Http\Controllers\API\LaudoController@importCsv');
    Route::get('/image/{codigo}', 'App\Http\Controllers\API\ImageController@show');
});

Route::group(['middleware' => ['auth:sanctum', 'can:manage-users']], function () {
    Route::patch('/users/roles', 'App\Http\Controllers\API\UserController@updateRole');
    Route::resource('users', UserController::class);
});

Route::group(['middleware' => ['auth:sanctum', 'can:manage-laudos']], function () {
    Route::resource('laudos', LaudoController::class);
    Route::resource('image', ImageController::class);
});
