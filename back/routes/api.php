<?php

use App\Http\Controllers\CasoController;
use App\Http\Controllers\DnaController;
use App\Http\Controllers\SlamController;
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

    Route::post('/casos',        [\App\Http\Controllers\CasoController::class, 'store']);
    Route::get ('/casos',        [\App\Http\Controllers\CasoController::class, 'index']);
    Route::get ('/casos/{caso}', [\App\Http\Controllers\CasoController::class, 'show']);
    Route::put ('/casos/{caso}', [\App\Http\Controllers\CasoController::class, 'update']);

    Route::get ('/casos-linea-tiempo', [\App\Http\Controllers\CasoController::class, 'lineaTiempo']);


    Route::post('/casos/{caso}/sesiones-psicologicas',[\App\Http\Controllers\CasoController::class, 'psicoStore']);
    Route::put ('/sesiones-psicologicas/{psicologica}', [\App\Http\Controllers\CasoController::class, 'psicoUpdate']);
    Route::delete('/sesiones-psicologicas/{psicologica}', [\App\Http\Controllers\CasoController::class, 'psicoDestroy']);

    Route::post('/casos/{caso}/informes-legales', [\App\Http\Controllers\CasoController::class, 'legalStore']);
    Route::put ('/informes-legales/{informe}',     [\App\Http\Controllers\CasoController::class, 'legalUpdate']);
    Route::delete('/informes-legales/{informe}', [\App\Http\Controllers\CasoController::class, 'legalDestroy']);

    Route::post('/casos/{caso}/informes-sociales', [\App\Http\Controllers\CasoController::class, 'socialStore']);
    Route::put ('/informes-sociales/{informe}',     [\App\Http\Controllers\CasoController::class, 'socialUpdate']);
    Route::delete('/informes-sociales/{informe}', [\App\Http\Controllers\CasoController::class, 'socialDestroy']);

    Route::post('/casos/{caso}/documentos', [\App\Http\Controllers\CasoController::class, 'docStore']);
    Route::put ('/documentos/{documento}', [\App\Http\Controllers\CasoController::class, 'docUpdate']);
    Route::delete('/documentos/{documento}', [\App\Http\Controllers\CasoController::class, 'docDestroy']);

    Route::post('/casos/{caso}/fotografias', [\App\Http\Controllers\CasoController::class, 'fotoStore']);
    Route::delete('/fotografias/{fotografia}', [\App\Http\Controllers\CasoController::class, 'fotoDestroy']);





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

// ====== DNA: Seguimiento ======
    Route::get('/dnas/{dna}/seguimiento', [DnaController::class, 'seguimiento']);

// ====== DNA: Sesiones Psicológicas ======
    Route::get   ('/dnas/{dna}/sesiones-psicologicas',          [DnaController::class, 'psicoIndex']);
    Route::post  ('/dnas/{dna}/sesiones-psicologicas',          [DnaController::class, 'psicoStore']);
    Route::get   ('/dnas/sesiones-psicologicas/{psicologica}',  [DnaController::class, 'psicoShow']);
    Route::put   ('/dnas/sesiones-psicologicas/{psicologica}',  [DnaController::class, 'psicoUpdate']);
    Route::delete('/dnas/sesiones-psicologicas/{psicologica}',  [DnaController::class, 'psicoDestroy']);

// ====== DNA: Informes Legales ======
    Route::get   ('/dnas/{dna}/informes-legales',       [DnaController::class, 'legalIndex']);
    Route::post  ('/dnas/{dna}/informes-legales',       [DnaController::class, 'legalStore']);
    Route::get   ('/dnas/informes-legales/{informe}',   [DnaController::class, 'legalShow']);
    Route::put   ('/dnas/informes-legales/{informe}',   [DnaController::class, 'legalUpdate']);
    Route::delete('/dnas/informes-legales/{informe}',   [DnaController::class, 'legalDestroy']);

// ====== DNA: Documentos ======
    Route::get   ('/dnas/{dna}/documentos',           [DnaController::class, 'docIndex']);
    Route::post  ('/dnas/{dna}/documentos',           [DnaController::class, 'docStore']);
    Route::get   ('/dnas/documentos/{documento}',     [DnaController::class, 'docShow']);
    Route::put   ('/dnas/documentos/{documento}',     [DnaController::class, 'docUpdate']);
    Route::delete('/dnas/documentos/{documento}',     [DnaController::class, 'docDestroy']);

// ====== DNA: Fotografías ======
    Route::get   ('/dnas/{dna}/fotografias',          [DnaController::class, 'fotoIndex']);
    Route::post  ('/dnas/{dna}/fotografias',          [DnaController::class, 'fotoStore']);
    Route::delete('/dnas/fotografias/{fotografia}',   [DnaController::class, 'fotoDestroy']);

    Route::get   ('/dnas/{dna}/fotografias',  [DnaController::class, 'fotoIndex']);
    Route::post  ('/dnas/{dna}/fotografias',  [DnaController::class, 'fotoStore']);
    Route::delete('/dnas/fotografias/{fotografia}', [DnaController::class, 'fotoDestroy']);

    Route::post('/slams', [SlamController::class, 'store']);
    Route::get('/slams', [SlamController::class, 'index']);
    Route::get('/slams/{slam}', [SlamController::class, 'show']);
    Route::put('/slams/{slam}', [SlamController::class, 'update']);

    Route::post('/slams/{slam}/sesiones-psicologicas', [SlamController::class, 'psicoStore']);
    Route::put('/slams/sesiones-psicologicas/{psicologica}', [SlamController::class, 'psicoUpdate']);
    Route::delete('/slams/sesiones-psicologicas/{psicologica}', [SlamController::class, 'psicoDestroy']);

    Route::post('/slams/{slam}/informes-legales', [SlamController::class, 'legalStore']);
    Route::put('/slams/informes-legales/{legal}', [SlamController::class, 'legalUpdate']);
    Route::delete('/slams/informes-legales/{legal}', [SlamController::class, 'legalDestroy']);

    Route::post('/slams/{slam}/documentos', [SlamController::class, 'docStore']);
    Route::put('/slams/documentos/{documento}', [SlamController::class, 'docUpdate']);
    Route::delete('/slams/documentos/{documento}', [SlamController::class, 'docDestroy']);

    Route::post('/slams/{slam}/fotografias', [SlamController::class, 'fotoStore']);
    Route::delete('/slams/fotografias/{fotografia}', [SlamController::class, 'fotoDestroy']);

    Route::post('/umadis', [\App\Http\Controllers\UmadiController::class, 'store']);
    Route::get('/umadis', [\App\Http\Controllers\UmadiController::class, 'index']);
    Route::get('/umadis/{umadi}', [\App\Http\Controllers\UmadiController::class, 'show']);
    Route::put('/umadis/{umadi}', [\App\Http\Controllers\UmadiController::class, 'update']);

    Route::post('/umadis/{umadi}/sesiones-psicologicas', [\App\Http\Controllers\UmadiController::class, 'psicoStore']);
    Route::put('/umadis/sesiones-psicologicas/{psicologica}', [\App\Http\Controllers\UmadiController::class, 'psicoUpdate']);
    Route::delete('/umadis/sesiones-psicologicas/{psicologica}', [\App\Http\Controllers\UmadiController::class, 'psicoDestroy']);

    Route::post('/umadis/{umadi}/informes-legales', [\App\Http\Controllers\UmadiController::class, 'legalStore']);
    Route::put('/umadis/informes-legales/{legal}', [\App\Http\Controllers\UmadiController::class, 'legalUpdate']);
    Route::delete('/umadis/informes-legales/{legal}', [\App\Http\Controllers\UmadiController::class, 'legalDestroy']);

    Route::post('/umadis/{umadi}/documentos', [\App\Http\Controllers\UmadiController::class, 'docStore']);
    Route::put('/umadis/documentos/{documento}', [\App\Http\Controllers\UmadiController::class, 'docUpdate']);
    Route::delete('/umadis/documentos/{documento}', [\App\Http\Controllers\UmadiController::class, 'docDestroy']);

    Route::post('/umadis/{umadi}/fotografias', [\App\Http\Controllers\UmadiController::class, 'fotoStore']);
    Route::delete('/umadis/fotografias/{fotografia}', [\App\Http\Controllers\UmadiController::class, 'fotoDestroy']);

});

Route::get('/casos/{caso}/pdf/hoja-ruta', [CasoController::class, 'pdfHojaRuta']);

Route::get   ('/casos/{caso}/pdf',  [CasoController::class, 'pdf']);
Route::get   ('/documentos/{documento}/view',     [\App\Http\Controllers\CasoController::class, 'docView']);
Route::get   ('/documentos/{documento}/download', [\App\Http\Controllers\CasoController::class, 'docDownload']);

//http://localhost:8000/api/sesiones-psicologicas/1/pdf
Route::get   ('/sesiones-psicologicas/{psicologica}/pdf',  [CasoController::class, 'psicoPdf']);
Route::get   ('/informes/{informe}/pdf', [CasoController::class, 'legalPdf']);

Route::get   ('/slims/documentos/{documento}/view',   [SlimDocumentoController::class, 'view']);

Route::get('/slims/{slim}/pdf', [SlimController::class, 'pdf']);
Route::get('/slims/{slim}/pdf/hoja-ruta', [SlimController::class, 'pdfHojaRuta']);

Route::get   ('/slims/sesiones-psicologicas/{psicologica}/pdf',  [SlimPsicologicaController::class, 'pdf']);
Route::get   ('/slims/informes-legales/{informe}/pdf', [SlimInformeLegalController::class, 'pdf']);
Route::get   ('/slims/documentos/{documento}/download',[SlimDocumentoController::class, 'download']);

Route::get('/dna/{dna}/pdf', [DnaController::class, 'pdf']);
Route::get('/dna/{dna}/pdf/hoja-ruta', [DnaController::class, 'pdfHojaRuta']);
Route::get('/dnas/sesiones-psicologicas/{psicologica}/pdf', [DnaController::class, 'psicoPdf']);
Route::get('/dnas/informes-legales/{informe}/pdf', [\App\Http\Controllers\DnaController::class, 'legalPdf']);
Route::get('/dnas/documentos/{documento}/view',     [\App\Http\Controllers\DnaController::class, 'docView']);
Route::get('/dnas/documentos/{documento}/download', [\App\Http\Controllers\DnaController::class, 'docDownload']);

Route::get('/slams/{slam}/pdf', [SlamController::class, 'pdf']);
Route::get('/slams/sesiones-psicologicas/{psicologica}/pdf', [SlamController::class, 'psicoPdf']);
Route::get('/slams/informes-legales/{informe}/pdf', [SlamController::class, 'legalPdf']);
Route::get('/slams/documentos/{documento}/view', [SlamController::class, 'docView']);
Route::get('/slams/documentos/{documento}/download', [SlamController::class, 'docDownload']);
Route::get('/slams/{slam}/pdf/hoja-ruta', [SlamController::class, 'pdfHojaRuta']);

Route::get('/umadis/{slam}/pdf', [\App\Http\Controllers\UmadiController::class, 'pdf']);
Route::get('/umadis/sesiones-psicologicas/{psicologica}/pdf', [\App\Http\Controllers\UmadiController::class, 'psicoPdf']);
Route::get('/umadis/informes-legales/{informe}/pdf', [\App\Http\Controllers\UmadiController::class, 'legalPdf']);
Route::get('/umadis/documentos/{documento}/view', [\App\Http\Controllers\UmadiController::class, 'docView']);
Route::get('/umadis/documentos/{documento}/download', [\App\Http\Controllers\UmadiController::class, 'docDownload']);
Route::get('/umadis/{slam}/pdf/hoja-ruta', [\App\Http\Controllers\UmadiController::class, 'pdfHojaRuta']);
