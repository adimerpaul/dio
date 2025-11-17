<?php

namespace App\Http\Controllers;

use App\Models\RemisionCaso;
use Illuminate\Http\Request;

class RemisionCasoController extends Controller
{
    // LISTA
    public function index()
    {
        return RemisionCaso::orderBy('id', 'desc')->get();
    }

    // CREAR
    public function store(Request $request)
    {
        $codigo = RemisionCaso::max('codigo_ingreso') + 1;
        $request->merge(['codigo_ingreso' => $codigo]);
//        fecha_hora
        $request->merge(['fecha_hora' => date('Y-m-d H:i:s')]);
        $remision = RemisionCaso::create($request->all());
        return response()->json($remision, 201);
    }

    // VER UNO
    public function show(RemisionCaso $remisionCaso)
    {
        return $remisionCaso;
    }

    // ACTUALIZAR
    public function update(Request $request, RemisionCaso $remisionCaso)
    {
        $remisionCaso->fill($request->all());
        $remisionCaso->save();

        return response()->json($remisionCaso);
    }

    // ELIMINAR (soft delete)
    public function destroy(RemisionCaso $remisionCaso)
    {
        $remisionCaso->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
