<?php

namespace App\Http\Controllers;

use App\Models\Slim;            // Asegúrate de tener este modelo
use App\Models\InformeLegal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SlimInformeLegalController extends Controller
{
    /** LISTA (por SLIM) */
    public function index(Request $request, Slim $slim)
    {
        $q       = trim((string) $request->query('q', ''));
        $perPage = max(1, min((int) $request->query('per_page', 10), 100));

        $query = InformeLegal::query()
            ->with(['user:id,name,username'])
            ->where('caseable_type', Slim::class)
            ->where('caseable_id', $slim->id);

        if ($q !== '') {
            $like = "%{$q}%";
            $query->where(function ($s) use ($like) {
                $s->orWhere('titulo', 'like', $like)
                    ->orWhere('numero', 'like', $like)
                    ->orWhere('contenido_html', 'like', $like)
                    ->orWhereHas('user', function ($u) use ($like) {
                        $u->where('name', 'like', $like)
                            ->orWhere('username', 'like', $like);
                    });
            });
        }

        $rows = $query->orderByDesc('fecha')->orderByDesc('id')
            ->paginate($perPage)->appends($request->query());

        return response()->json($rows);
    }

    /** CREAR (en SLIM) */
    public function store(Request $request, Slim $slim)
    {
        $data = $request->validate([
            'fecha'          => ['nullable','date'],
            'titulo'         => ['required','string','max:255'],
            'numero'         => ['nullable','string','max:100'],
            'contenido_html' => ['required','string'],
        ]);

        $data['caseable_id']   = $slim->id;
        $data['caseable_type'] = Slim::class;
        $data['user_id']       = $request->user()->id;

        $item = InformeLegal::create($data);

        return response()->json($item->load('user:id,name,username'), 201);
    }

    /** VER (plano) */
    public function show(InformeLegal $informe)
    {
        return response()->json(
            $informe->load(['user:id,name,username','caseable:id'])
        );
    }

    /** ACTUALIZAR (plano) */
    public function update(Request $request, InformeLegal $informe)
    {
        $data = $request->validate([
            'fecha'          => ['nullable','date'],
            'titulo'         => ['sometimes','required','string','max:255'],
            'numero'         => ['nullable','string','max:100'],
            'contenido_html' => ['sometimes','required','string'],
        ]);

        $informe->update($data);

        return response()->json([
            'message' => 'Informe actualizado',
            'data'    => $informe->fresh()->load('user:id,name,username')
        ]);
    }

    /** ELIMINAR (plano) */
    public function destroy(InformeLegal $informe)
    {
        $informe->delete();
        return response()->json(['message' => 'Informe eliminado'], 200);
    }

    /** PDF (plano) */
    public function pdf(InformeLegal $informe, Request $request)
    {
        $inf = $informe->load(['user:id,name,username','caseable']);
        $pdf = Pdf::loadView('informes_legales.pdf', ['informe' => $inf])->setPaper('A4','portrait');
        $pdf->getDomPDF()->getOptions()->set('defaultFont', 'DejaVu Sans');

        $download = (int) $request->query('download', 0) === 1;
        $filename = 'SLIM_InformeLegal_'.$informe->id.'.pdf';

        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }
}
