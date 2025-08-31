<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\SesionPsicologica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SesionPsicologicaController extends Controller
{
    // GET /api/casos/{caso}/sesiones-psicologicas
    public function index(Request $request, Caso $caso)
    {
        $q    = trim($request->get('q', ''));
        $page = (int) $request->get('page', 1);
        $per  = (int) $request->get('per_page', 10);

        $query = SesionPsicologica::with('user')
            ->where('caso_id', $caso->id);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('titulo', 'like', "%{$q}%")
                    ->orWhere('contenido_html', 'like', "%{$q}%")
                    ->orWhere('lugar', 'like', "%{$q}%")
                    ->orWhere('tipo', 'like', "%{$q}%");
            });
        }

        $rows = $query->orderByDesc('fecha')->orderByDesc('id')->paginate($per, ['*'], 'page', $page);

        return response()->json($rows);
    }

    // POST /api/casos/{caso}/sesiones-psicologicas
    public function store(Request $request, Caso $caso)
    {
        $data = $request->validate([
            'fecha'          => ['nullable','date'],
            'titulo'         => ['required','string','max:255'],
            'duracion_min'   => ['nullable','integer','min:0'],
            'lugar'          => ['nullable','string','max:255'],
            'tipo'           => ['nullable','string','max:30'],
            'contenido_html' => ['required','string'],
        ]);

        $data['caso_id'] = $caso->id;
        $data['user_id'] = Auth::id();

        $row = SesionPsicologica::create($data);

        return response()->json(['message' => 'Creado', 'data' => $row], 201);
    }

    // GET /api/sesiones-psicologicas/{sesion}
    public function show(SesionPsicologica $sesion)
    {
        $sesion->load('user', 'caso');
        return response()->json($sesion);
    }

    // PUT /api/sesiones-psicologicas/{sesion}
    public function update(Request $request, SesionPsicologica $sesion)
    {
        $data = $request->validate([
            'fecha'          => ['nullable','date'],
            'titulo'         => ['required','string','max:255'],
            'duracion_min'   => ['nullable','integer','min:0'],
            'lugar'          => ['nullable','string','max:255'],
            'tipo'           => ['nullable','string','max:30'],
            'contenido_html' => ['required','string'],
        ]);

        $sesion->update($data);

        return response()->json(['message' => 'Actualizado', 'data' => $sesion]);
    }

    // DELETE /api/sesiones-psicologicas/{sesion}
    public function destroy(SesionPsicologica $sesion)
    {
        $sesion->delete();
        return response()->json(['message' => 'Eliminado']);
    }

    // GET /api/sesiones-psicologicas/{sesion}/pdf
    public function pdf(SesionPsicologica $sesion)
    {
        $sesion->load('user', 'caso');

        $pdf = Pdf::loadView('pdf.sesion_psicologica', [
            'sesion' => $sesion,
        ])->setPaper('a4', 'portrait');

        $filename = 'SesionPsicologica-Caso_'.$sesion->caso_id.'_#'.$sesion->id.'.pdf';
        return $pdf->stream($filename);
    }
}
