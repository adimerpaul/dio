<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Informe;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InformeController extends Controller
{
    // GET /api/casos/{caso}/informes
    public function index(Request $request, Caso $caso)
    {
        $q    = trim($request->get('q', ''));
        $per  = (int) $request->get('per_page', 10);

        $query = Informe::with('user')->where('caso_id', $caso->id);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('titulo', 'like', "%{$q}%")
                    ->orWhere('area', 'like', "%{$q}%")
                    ->orWhere('numero', 'like', "%{$q}%")
                    ->orWhere('contenido_html', 'like', "%{$q}%");
            });
        }

        $rows = $query->orderByDesc('fecha')->orderByDesc('id')->paginate($per);
        return response()->json($rows);
    }

    // POST /api/casos/{caso}/informes
    public function store(Request $request, Caso $caso)
    {
        $data = $request->validate([
            'fecha'          => ['nullable','date'],
            'titulo'         => ['required','string','max:255'],
            'area'           => ['nullable','string','max:30'],
            'numero'         => ['nullable','string','max:50'],
            'contenido_html' => ['required','string'],
        ]);

        $data['caso_id'] = $caso->id;
        $data['user_id'] = Auth::id();

        $row = Informe::create($data);
        return response()->json(['message'=>'Creado','data'=>$row], 201);
    }

    // GET /api/informes/{informe}
    public function show(Informe $informe)
    {
        $informe->load('user','caso');
        return response()->json($informe);
    }

    // PUT /api/informes/{informe}
    public function update(Request $request, Informe $informe)
    {
        $data = $request->validate([
            'fecha'          => ['nullable','date'],
            'titulo'         => ['required','string','max:255'],
            'area'           => ['nullable','string','max:30'],
            'numero'         => ['nullable','string','max:50'],
            'contenido_html' => ['required','string'],
        ]);

        $informe->update($data);
        return response()->json(['message'=>'Actualizado','data'=>$informe]);
    }

    // DELETE /api/informes/{informe}
    public function destroy(Informe $informe)
    {
        $informe->delete();
        return response()->json(['message'=>'Eliminado']);
    }

    // GET /api/informes/{informe}/pdf
    public function pdf(Informe $informe)
    {
        $informe->load('user','caso');

        $pdf = Pdf::loadView('pdf.informe', [
            'informe' => $informe,
        ])->setPaper('a4', 'portrait');

        $filename = 'Informe-Caso_'.$informe->caso_id.'_#'.$informe->id.'.pdf';
        return $pdf->stream($filename);
    }
}
