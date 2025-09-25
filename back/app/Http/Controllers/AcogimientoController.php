<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcogimientoController extends Controller
{
    function searchNurej(Request $request) {
        $nurej = $request->input('nurej');

        if (empty($nurej)) {
            return response()->json(['error' => 'El campo nurej es obligatorio.'], 400);
        }
        $caso = \App\Models\Caso::where('nurej', $nurej)->first();

        if ($caso) {
            return  $caso;
        } else {
            return response()->json(['message' => 'No se encontró ningún caso con ese NUREJ.'], 404);
        }
    }
    function show($id) {
        $acogimiento = \App\Models\Acogimiento::where('id', $id)->with('caso')->first();
        if ($acogimiento) {
            return $acogimiento;
        } else {
            return response()->json(['message' => 'No se encontró ningún acogimiento con ese ID.'], 404);
        }
    }
}
