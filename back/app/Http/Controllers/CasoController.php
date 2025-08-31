<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use Illuminate\Http\Request;

class CasoController extends Controller
{
    /**
     * GET /casos?q=texto&page=1&per_page=10
     * Devuelve paginado con filtro por texto libre.
     */
    public function index(Request $request)
    {
        $q        = trim((string) $request->get('q', ''));
        $perPage  = (int) $request->get('per_page', 10);
        $perPage  = max(5, min($perPage, 100)); // entre 5 y 100

        $columns = [
            'id',
            'caso_numero',
            'caso_fecha_hecho',
            'caso_tipologia',
            'caso_zona',
            'caso_direccion',
            'caso_descripcion',
            'denunciante_nombre_completo',
            'denunciante_nro',
            'denunciado_nombre_completo',
            'denunciado_nro',
            'created_at'
        ];

        $query = Caso::select($columns)->orderByDesc('created_at');

        if ($q !== '') {
            $query->where(function ($s) use ($q) {
                $like = "%{$q}%";
                $s->orWhere('caso_numero', 'like', $like)
                    ->orWhere('caso_tipologia', 'like', $like)
                    ->orWhere('caso_zona', 'like', $like)
                    ->orWhere('caso_direccion', 'like', $like)
                    ->orWhere('caso_descripcion', 'like', $like)
                    ->orWhere('denunciante_nombre_completo', 'like', $like)
                    ->orWhere('denunciante_nro', 'like', $like)
                    ->orWhere('denunciado_nombre_completo', 'like', $like)
                    ->orWhere('denunciado_nro', 'like', $like);
            });
        }

        $paginated = $query->paginate($perPage)->appends($request->query());

        // respuesta "plana" fácil para el front
        return response()->json([
            'data'         => $paginated->items(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'per_page'     => $paginated->perPage(),
            'total'        => $paginated->total(),
            'from'         => $paginated->firstItem(),
            'to'           => $paginated->lastItem(),
        ]);
    }
}
