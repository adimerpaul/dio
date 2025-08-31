<?php

use App\Http\Controllers\CasoController;
use App\Http\Controllers\ProblematicaController;
use App\Http\Controllers\SesionPsicologicaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
Route::post('/login', [App\Http\Controllers\UserController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\UserController::class, 'logout']);
    Route::get('/me', [App\Http\Controllers\UserController::class, 'me']);


    Route::get('/users', [App\Http\Controllers\UserController::class, 'index']);
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store']);
    Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update']);
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy']);
    Route::put('/updatePassword/{user}', [App\Http\Controllers\UserController::class, 'updatePassword']);
    Route::post('/{user}/avatar', [App\Http\Controllers\UserController::class, 'updateAvatar']);

    Route::get('/permissions', [App\Http\Controllers\PermissionController::class, 'index']);
    Route::get('/users/{user}/permissions', [App\Http\Controllers\UserController::class, 'getPermissions']);
    Route::put('/users/{user}/permissions', [App\Http\Controllers\UserController::class, 'syncPermissions']);

    Route::get('/casos', [CasoController::class, 'index']);
    Route::post('/casos', [CasoController::class, 'store']);
    Route::get('/casos/{caso}', [CasoController::class, 'show']);
    Route::put('/casos/{caso}', [CasoController::class, 'update']);

    // por caso
    Route::get('casos/{caso}/problematicas', [ProblematicaController::class, 'index']);
    Route::post('casos/{caso}/problematicas', [ProblematicaController::class, 'store']);

    // individuales
    Route::get('problematicas/{problematica}', [ProblematicaController::class, 'show']);
    Route::put('problematicas/{problematica}', [ProblematicaController::class, 'update']);
    Route::delete('problematicas/{problematica}', [ProblematicaController::class, 'destroy']);

    // listado + creación anidados al caso
    Route::get ('/casos/{caso}/sesiones-psicologicas', [SesionPsicologicaController::class, 'index']);
    Route::post('/casos/{caso}/sesiones-psicologicas', [SesionPsicologicaController::class, 'store']);

// crud por id
    Route::get   ('/sesiones-psicologicas/{sesion}', [SesionPsicologicaController::class, 'show']);
    Route::put   ('/sesiones-psicologicas/{sesion}', [SesionPsicologicaController::class, 'update']);
    Route::delete('/sesiones-psicologicas/{sesion}', [SesionPsicologicaController::class, 'destroy']);

// pdf

});
Route::get('/casos/{caso}/pdf', [CasoController::class, 'pdf']);
Route::get('problematicas/{problematica}/pdf', [ProblematicaController::class, 'pdf']);
Route::get('/sesiones-psicologicas/{sesion}/pdf', [SesionPsicologicaController::class, 'pdf']);

