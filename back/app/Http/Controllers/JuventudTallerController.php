<?php

namespace App\Http\Controllers;

use App\Models\JuventudTaller;
use Illuminate\Http\Request;

class JuventudTallerController extends Controller
{
    function index(){
        return JuventudTaller::orderByDesc('id')->get();
    }
    public function store(Request $request){
        $user = $request->user();
        $request->merge(['user_id' => $user->id]);
        $taller = JuventudTaller::create($request->all());
        return response()->json($taller, 201);
    }
    public function update(Request $request, JuventudTaller $taller)
    {
        $taller->update($request->all());
        return response()->json($taller, 200);
    }

    // Eliminar
    public function destroy(JuventudTaller $taller)
    {
        $taller->delete();
        return response()->json(['message' => 'Eliminado'], 200);
    }
}
