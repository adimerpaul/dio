<?php

namespace App\Http\Controllers;

use App\Models\Taller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TallerController extends Controller
{
    public function print(Request $request)
    {
        $request->validate([
            'start' => ['required','date'],
            'end'   => ['required','date','after_or_equal:start'],
        ]);

        $start = $request->query('start');
        $end   = $request->query('end');

        // Trae por rango completo de fecha/hora, no solo whereDate:
        $talleres = Taller::where('fecha_inicio', '>=', $start)
            ->where('fecha_inicio', '<=', $end)
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        $pdf = \PDF::loadView('talleres.print-pdf', [
            'talleres' => $talleres,
            'start'    => $start,
            'end'      => $end,
        ])->setPaper('letter', 'portrait'); // o 'a4'

        $filename = 'reporte_talleres_'.now()->format('Ymd_His').'.pdf';
        return $pdf->stream($filename); // o ->download($filename)
    }
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
