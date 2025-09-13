<?php

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
});

// PDFs (expuestos igual pero con Slim)
Route::get('/slims/{slim}/pdf', [SlimController::class, 'pdf']);
Route::get('/slims/{slim}/pdf/hoja-ruta', [SlimController::class, 'pdfHojaRuta']);
