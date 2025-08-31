<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Problematica;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProblematicaController extends Controller
{
    public function pdf(\App\Models\Problematica $problematica)
    {
        $problematica->load(['caso', 'user']);

        $pdf = Pdf::loadView('pdf.problematica', [
            'p' => $problematica,
            // si tienes logo en public/images/logo_slim.png, pásalo:
            'logo' => public_path('images/logo_slim.png'),
            'entidad' => 'SERVICIO LEGAL INTEGRAL MUNICIPAL (S.L.I.M.)',
            'municipio' => 'G.A.M. ORURO', // cámbialo si quieres
        ]);

        $filename = 'Problemática-Caso-'.$problematica->caso_id.'-#'.$problematica->id.'.pdf';
        return $pdf->stream($filename);
    }
    // Listado paginado por Caso, con search (?q=texto&per_page=10)
    public function index(Request $request, Caso $caso)
    {
        $q  = $request->query('q');
        $pp = (int) $request->query('per_page', 10);

        $rows = Problematica::with('user:id,name,username')
            ->where('caso_id', $caso->id)
            ->when($q, function ($w) use ($q) {
                $w->where(function ($s) use ($q) {
                    $s->where('titulo','like',"%$q%")
                        ->orWhere('detalle','like',"%$q%")
                        ->orWhere('acciones_inmediatas','like',"%$q%");
                });
            })
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate($pp);

        return response()->json($rows);
    }

    // Crear
    public function store(Request $request, Caso $caso)
    {
        $data = $request->validate([
            'fecha' => ['nullable','date'],
            'titulo' => ['required','string','max:255'],
            'detalle' => ['nullable','string'],
            'acciones_inmediatas' => ['nullable','string'],
            'observaciones' => ['nullable','string'],
        ]);

        $data['caso_id'] = $caso->id;
        $data['user_id'] = $request->user()->id; // requiere auth

        $row = Problematica::create($data)->load('user:id,name,username');
        return response()->json(['message'=>'Creado','data'=>$row], 201);
    }

    // Ver
    public function show(Problematica $problematica)
    {
        $problematica->load('user:id,name,username','caso:id,caso_numero');
        return response()->json($problematica);
    }

    // Actualizar
    public function update(Request $request, Problematica $problematica)
    {
        $data = $request->validate([
            'fecha' => ['nullable','date'],
            'titulo' => ['required','string','max:255'],
            'detalle' => ['nullable','string'],
            'acciones_inmediatas' => ['nullable','string'],
            'observaciones' => ['nullable','string'],
        ]);
        $problematica->update($data);
        return response()->json(['message'=>'Actualizado','data'=>$problematica->fresh('user:id,name,username')]);
    }

    // Borrar
    public function destroy(Problematica $problematica)
    {
        $problematica->delete();
        return response()->json(['message'=>'Eliminado']);
    }
}
