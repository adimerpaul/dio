<?php

use App\Http\Controllers\DnaController;
use App\Http\Controllers\SlimDocumentoController;
use App\Http\Controllers\SlimFotografiaController;
use App\Http\Controllers\SlimInformeLegalController;
use App\Http\Controllers\SlimPsicologicaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlimController;
// ... (tus otros use ya existentes)

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

    // ======== SLIMS (antes CASOS) ========
    Route::get ('/slims/pendientes-resumen', [SlimController::class, 'pendientesResumen']);
    Route::get ('/slims',        [SlimController::class, 'index']);
    Route::post('/slims',        [SlimController::class, 'store']);   // CREACIÓN
    Route::get ('/slims/{slim}', [SlimController::class, 'show']);
    Route::put ('/slims/{slim}', [SlimController::class, 'update']);

    // Anidadas a SLIM (lista/crear)
    Route::get ('/slims/{slim}/sesiones-psicologicas', [SlimPsicologicaController::class, 'index']);
    Route::post('/slims/{slim}/sesiones-psicologicas', [SlimPsicologicaController::class, 'store']);

// Planas (ver/editar/eliminar/pdf)
    Route::get   ('/slims/sesiones-psicologicas/{psicologica}',      [SlimPsicologicaController::class, 'show']);
    Route::put   ('/slims/sesiones-psicologicas/{psicologica}',      [SlimPsicologicaController::class, 'update']);
    Route::delete('/slims/sesiones-psicologicas/{psicologica}',      [SlimPsicologicaController::class, 'destroy']);


    // Seguimiento y dashboard siguen igual pero ya apuntando a Slim
    Route::get('/slims/{slim}/seguimiento', [SlimController::class, 'seguimiento']);

    // Tu dashboard, kpis, auditorías, agenda, etc. se mantienen
    Route::get('/kpis', [App\Http\Controllers\KpiController::class, 'index']);
    Route::get('/audits', [\App\Http\Controllers\AuditController::class, 'index']);
    Route::get('/audits/{audit}', [\App\Http\Controllers\AuditController::class, 'show']);
    Route::get('/agendas', [\App\Http\Controllers\AgendaController::class, 'index']);
    Route::post('/agendas', [\App\Http\Controllers\AgendaController::class, 'store']);
    Route::get('/agendas/{agenda}', [\App\Http\Controllers\AgendaController::class, 'show']);
    Route::put('/agendas/{agenda}', [\App\Http\Controllers\AgendaController::class, 'update']);
    Route::delete('/agendas/{agenda}', [\App\Http\Controllers\AgendaController::class, 'destroy']);
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);


// Anidadas a SLIM (listar / crear)
    Route::get ('/slims/{slim}/informes-legales', [SlimInformeLegalController::class, 'index']);
    Route::post('/slims/{slim}/informes-legales', [SlimInformeLegalController::class, 'store']);

// Planas (ver / editar / eliminar / pdf)
    Route::get   ('/slims/informes-legales/{informe}',     [SlimInformeLegalController::class, 'show']);
    Route::put   ('/slims/informes-legales/{informe}',     [SlimInformeLegalController::class, 'update']);
    Route::delete('/slims/informes-legales/{informe}',     [SlimInformeLegalController::class, 'destroy']);


    // Listar y subir (anidadas al SLIM)
    Route::get ('/slims/{slim}/documentos', [SlimDocumentoController::class, 'index']);
    Route::post('/slims/{slim}/documentos', [SlimDocumentoController::class, 'store']);

// Operaciones por documento (planas)
    Route::get   ('/slims/documentos/{documento}',        [SlimDocumentoController::class, 'show']);
    Route::put   ('/slims/documentos/{documento}',        [SlimDocumentoController::class, 'update']);
    Route::delete('/slims/documentos/{documento}',        [SlimDocumentoController::class, 'destroy']);

    Route::get   ('/slims/{slim}/fotografias',     [SlimFotografiaController::class, 'index']);
    Route::post  ('/slims/{slim}/fotografias',     [SlimFotografiaController::class, 'store']);
    Route::delete('/slims/fotografias/{fotografia}', [SlimFotografiaController::class, 'destroy']);

    Route::get   ('/dnas',        [DnaController::class, 'index']);
    Route::post  ('/dnas',        [DnaController::class, 'store']);
    Route::get   ('/dnas/{dna}',  [DnaController::class, 'show']);
    Route::put   ('/dnas/{dna}',  [DnaController::class, 'update']);
    Route::delete('/dnas/{dna}',  [DnaController::class, 'destroy']);
});

Route::get   ('/slims/documentos/{documento}/view',   [SlimDocumentoController::class, 'view']);

// PDFs (expuestos igual pero con Slim)
Route::get('/slims/{slim}/pdf', [SlimController::class, 'pdf']);
Route::get('/slims/{slim}/pdf/hoja-ruta', [SlimController::class, 'pdfHojaRuta']);

Route::get   ('/slims/sesiones-psicologicas/{psicologica}/pdf',  [SlimPsicologicaController::class, 'pdf']);
Route::get   ('/slims/informes-legales/{informe}/pdf', [SlimInformeLegalController::class, 'pdf']);
Route::get   ('/slims/documentos/{documento}/download',[SlimDocumentoController::class, 'download']);
