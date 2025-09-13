<?php

namespace App\Http\Controllers;

use App\Models\Slim;              // ⬅️ tu modelo principal (el “caso” ahora es Slim)
use App\Models\Psicologica;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SlimPsicologicaController extends Controller
{
    /** LISTADO (anidado a SLIM) */
    public function index(Request $request, Slim $slim)
    {
        $q       = trim((string) $request->query('q', ''));
        $perPage = max(1, min((int) $request->query('per_page', 10), 100));

        $query = Psicologica::query()
            ->with(['user:id,name,username'])
            ->where('caseable_type', Slim::class)
            ->where('caseable_id', $slim->id);

        if ($q !== '') {
            $like = "%{$q}%";
            $query->where(function ($s) use ($like) {
                $s->orWhere('titulo', 'like', $like)
                    ->orWhere('lugar', 'like', $like)
                    ->orWhere('tipo', 'like', $like)
                    ->orWhere('contenido_html', 'like', $like)
                    ->orWhereHas('user', function ($u) use ($like) {
                        $u->where('name', 'like', $like)
                            ->orWhere('username', 'like', $like);
                    });
            });
        }

        $rows = $query
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json($rows);
    }

    /** CREAR (anidado a SLIM) */
    public function store(Request $request, Slim $slim)
    {
        // (opcional) autorizar: solo Admin/Psicólogo
        // $request->user()->authorizeRoles(['Administrador', 'Psicologo']);

        $data = $request->validate([
            'fecha'          => ['nullable','date'],
            'titulo'         => ['required','string','max:255'],
            'duracion_min'   => ['nullable','integer','min:0'],
            'lugar'          => ['nullable','string','max:255'],
            'tipo'           => ['nullable','string','max:100'],
            'contenido_html' => ['required','string'],
        ]);

        $data['caseable_id']   = $slim->id;
        $data['caseable_type'] = Slim::class;
        $data['user_id']       = $request->user()->id;

        $ps = Psicologica::create($data);

        return response()->json($ps->load('user:id,name,username'), 201);
    }

    /** VER (plano) */
    public function show(Psicologica $psicologica)
    {
        return response()->json(
            $psicologica->load(['user:id,name,username','caseable:id'])
        );
    }

    /** ACTUALIZAR (plano) */
    public function update(Request $request, Psicologica $psicologica)
    {
        // (opcional) autorizar
        // $request->user()->authorizeRoles(['Administrador', 'Psicologo']);

        $data = $request->validate([
            'fecha'          => ['nullable','date'],
            'titulo'         => ['sometimes','required','string','max:255'],
            'duracion_min'   => ['nullable','integer','min:0'],
            'lugar'          => ['nullable','string','max:255'],
            'tipo'           => ['nullable','string','max:100'],
            'contenido_html' => ['sometimes','required','string'],
        ]);

        $psicologica->update($data);

        return response()->json([
            'message' => 'Sesión actualizada',
            'data'    => $psicologica->fresh()->load('user:id,name,username')
        ]);
    }

    /** ELIMINAR (plano, SoftDeletes en el modelo) */
    public function destroy(Psicologica $psicologica)
    {
        // (opcional) autorizar
        // request()->user()->authorizeRoles(['Administrador', 'Psicologo']);

        $psicologica->delete();
        return response()->json(['message' => 'Sesión eliminada'], 200);
    }

    /** PDF (plano) */
    public function pdf(Psicologica $psicologica, Request $request)
    {
        $ps = $psicologica->load(['user:id,name,username', 'caseable']);
        $pdf = Pdf::loadView('psicologicas.pdf', ['sesion' => $ps])
            ->setPaper('A4', 'portrait');

        // Fuente unicode segura
        $pdf->getDomPDF()->getOptions()->set('defaultFont', 'DejaVu Sans');

        $download = (int) $request->query('download', 0) === 1;
        $filename = 'SLIM_Sesion_'.$psicologica->id.'.pdf';

        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }
}
