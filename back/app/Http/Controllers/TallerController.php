<?php

namespace App\Http\Controllers;

use App\Models\Taller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TallerController extends Controller
{
    // Listar (simple)
    public function index(Request $request)
    {
        return Taller::orderByDesc('id')->get();
    }

    // Crear
    public function store(Request $request){
        $user = $request->user();
        $request->merge(['user_id' => $user->id]);
        $taller = Taller::create($request->all());
        return response()->json($taller, 201);
    }
    public function update(Request $request, Taller $taller)
    {
        $taller->update($request->all());
        return response()->json($taller, 200);
    }

    // Eliminar
    public function destroy(Taller $taller)
    {
        $taller->delete();
        return response()->json(['message' => 'Eliminado'], 200);
    }
}
