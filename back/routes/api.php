<?php

use App\Http\Controllers\CasoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\FotografiaController;
use App\Http\Controllers\InformeController;
use App\Http\Controllers\KpiController;
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

    Route::get('/usuariosRole', [App\Http\Controllers\UserController::class, 'usuariosRole']);

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

    // Anidadas al caso
    Route::get ('/casos/{caso}/informes',  [InformeController::class, 'index']);
    Route::post('/casos/{caso}/informes',  [InformeController::class, 'store']);

// CRUD por id
    Route::get   ('/informes/{informe}',    [InformeController::class, 'show']);
    Route::put   ('/informes/{informe}',    [InformeController::class, 'update']);
    Route::delete('/informes/{informe}',    [InformeController::class, 'destroy']);


// Por caso
    Route::get ('/casos/{caso}/documentos', [DocumentoController::class, 'index']);
    Route::post('/casos/{caso}/documentos', [DocumentoController::class, 'store']);

// Por id
    Route::get   ('/documentos/{documento}',         [DocumentoController::class, 'show']);
    Route::put   ('/documentos/{documento}',         [DocumentoController::class, 'update']);
    Route::delete('/documentos/{documento}',         [DocumentoController::class, 'destroy']);

    Route::get ('/casos/{caso}/fotografias', [FotografiaController::class,'index']);
    Route::post('/casos/{caso}/fotografias', [FotografiaController::class,'store']);
    Route::delete('/fotografias/{fotografia}', [FotografiaController::class,'destroy']);

    Route::get('/casos-linea-tiempo', [\App\Http\Controllers\CasoTimelineController::class, 'index']);

    Route::get('/kpis', [KpiController::class, 'index']);

    Route::get('/audits', [\App\Http\Controllers\AuditController::class, 'index']);
    Route::get('/audits/{audit}', [\App\Http\Controllers\AuditController::class, 'show']);

    // Agenda (citas)
    Route::get   ('/agendas',            [\App\Http\Controllers\AgendaController::class, 'index']);
    Route::post  ('/agendas',            [\App\Http\Controllers\AgendaController::class, 'store']);
    Route::get   ('/agendas/{agenda}',   [\App\Http\Controllers\AgendaController::class, 'show']);
    Route::put   ('/agendas/{agenda}',   [\App\Http\Controllers\AgendaController::class, 'update']);
    Route::delete('/agendas/{agenda}',   [\App\Http\Controllers\AgendaController::class, 'destroy']);


    Route::get('/dashboard', [DashboardController::class, 'index']);

});
Route::get('/casos/{caso}/pdf', [CasoController::class, 'pdf']);
Route::get('/casos/{caso}/pdf/hoja-ruta', [CasoController::class, 'pdfHojaRuta']);
Route::get('problematicas/{problematica}/pdf', [ProblematicaController::class, 'pdf']);
Route::get('/sesiones-psicologicas/{sesion}/pdf', [SesionPsicologicaController::class, 'pdf']);
// PDF (puede ir fuera del middleware si lo prefieres público)
Route::get('/informes/{informe}/pdf',   [InformeController::class, 'pdf']);

Route::get   ('/documentos/{documento}/download',[DocumentoController::class, 'download']);
Route::get   ('/documentos/{documento}/view',    [DocumentoController::class, 'view']);
