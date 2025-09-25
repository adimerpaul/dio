<?php

namespace App\Http\Controllers;

use App\Models\Acogimiento;
use App\Models\Caso;
use Illuminate\Http\Request;

class AcogimientoController extends Controller
{
    function searchNurej(Request $request) {
        $nurej = $request->input('nurej');

        if (empty($nurej)) {
            return response()->json(['message' => 'El parámetro NUREJ es obligatorio.'], 400);
        }
        $caso = \App\Models\Caso::where('nurej', $nurej)->get();

        if ($caso) {
            return  $caso;
        } else {
            return response()->json(['message' => 'No se encontró ningún caso con ese NUREJ.'], 404);
        }
    }
    function show($Casoid) {
        $caso = \App\Models\Caso::where('id', $Casoid)->with(['acogimientos'])->first();
        if ($caso) {
            return $caso;
        } else {
            return response()->json(['message' => 'No se encontró ningún caso con ese ID.'], 404);
        }
    }
    public function showByCaso(Caso $caso)
    {
        $acog = Acogimiento::where('caso_id', $caso->id)->first();

        // Trae info mínima del caso para cabecera (NUREJ, 1er menor)
        $caso = Caso::select('id','nurej','cud','caso_numero')
            ->findOrFail($caso->id);

        return response()->json([
            'caso'        => $caso,
            'acogimiento' => $acog, // puede ser null si no existe
        ]);
    }

    // PUT /api/casos/{caso}/acogimiento  (crea o actualiza por caso)
    public function upsert(Request $request, Caso $caso)
    {
        // Sin validación (como pediste)
//        $data = $request->only([
//            'adopcion', 'hogar',
//            'nino1','nino2','nino3','nino4','nino5','nino6','nino7','nino8','nino9','nino10',
//            'juzgado',
//            'cuidado_nombre','cuidado_paterno','cuidado_materno',
//            'area_legal','tipologia',
//            'audiencia_evaluacion','fecha','proximas_audiencia',
//        ]);

        error_log($caso);
        $request['caso_id'] = $caso->id;

//        $acog = Acogimiento::updateOrCreate(
//            ['caso_id' => $caso->id],
//            $request
//        );
        $findAcogimiento = Acogimiento::where('caso_id', $caso->id)->first();
        if ($findAcogimiento) {
            $findAcogimiento->update($request->all());
            $acog = $findAcogimiento;
        } else {
            $acog = Acogimiento::create($request->all());
        }

        return response()->json([
            'message'     => 'Acogimiento guardado',
            'acogimiento' => $acog,
        ]);
    }
}
