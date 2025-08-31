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
    public function store(Request $request)
    {
        // Validación básica (ajusta a gusto)
        $data = $request->validate([
            // Denunciante
            'denunciante_nombre_completo'  => ['required','string','max:255'],
            'denunciante_nombres'          => ['nullable','string','max:255'],
            'denunciante_paterno'          => ['nullable','string','max:255'],
            'denunciante_materno'          => ['nullable','string','max:255'],
            'denunciante_documento'        => ['nullable','string','max:100'],
            'denunciante_nro'              => ['nullable','string','max:100'],
            'denunciante_sexo'             => ['nullable','string','max:30'],
            'denunciante_lugar_nacimiento' => ['nullable','string','max:255'],
            'denunciante_fecha_nacimiento' => ['nullable','date'],
            'denunciante_edad'             => ['nullable','integer','min:0','max:120'],
            'denunciante_residencia'       => ['nullable','string','max:255'],
            'denunciante_estado_civil'     => ['nullable','string','max:100'],
            'denunciante_relacion'         => ['nullable','string','max:100'],
            'denunciante_grado'            => ['nullable','string','max:100'],
            'latitud'                      => ['nullable','numeric','between:-90,90'],
            'longitud'                     => ['nullable','numeric','between:-180,180'],
            'denunciante_trabaja'          => ['nullable','boolean'],
            'denunciante_prox'             => ['nullable','string','max:255'],
            'denunciante_ocupacion'        => ['nullable','string','max:255'],
            'denunciante_ocupacion_exacto' => ['nullable','string','max:255'],
            'denunciante_idioma'           => ['nullable','string','max:100'],
            'denunciante_fijo'             => ['nullable','string','max:100'],
            'denunciante_movil'            => ['nullable','string','max:100'],
            'denunciante_domicilio_actual' => ['nullable','string','max:255'],

            // Familiares (solo primero como ejemplo)
            'familiar1_nombre_completo'    => ['nullable','string','max:255'],
            'familiar1_edad'               => ['nullable','integer','min:0','max:120'],
            'familiar1_parentesco'         => ['nullable','string','max:100'],
            'familiar1_celular'            => ['nullable','string','max:100'],

            // Denunciado
            'denunciado_nombre_completo'   => ['nullable','string','max:255'],
            'denunciado_nombres'           => ['nullable','string','max:255'],
            'denunciado_paterno'           => ['nullable','string','max:255'],
            'denunciado_materno'           => ['nullable','string','max:255'],
            'denunciado_documento'         => ['nullable','string','max:100'],
            'denunciado_nro'               => ['nullable','string','max:100'],
            'denunciado_sexo'              => ['nullable','string','max:30'],
            'denunciado_lugar_nacimiento'  => ['nullable','string','max:255'],
            'denunciado_fecha_nacimiento'  => ['nullable','date'],
            'denunciado_edad'              => ['nullable','integer','min:0','max:120'],
            'denunciado_residencia'        => ['nullable','string','max:255'],
            'denunciado_estado_civil'      => ['nullable','string','max:100'],
            'denunciado_relacion'          => ['nullable','string','max:100'],
            'denunciado_grado'             => ['nullable','string','max:100'],
            'denunciado_trabaja'           => ['nullable','boolean'],
            'denunciado_prox'              => ['nullable','string','max:255'],
            'denunciado_ocupacion'         => ['nullable','string','max:255'],
            'denunciado_ocupacion_exacto'  => ['nullable','string','max:255'],
            'denunciado_idioma'            => ['nullable','string','max:100'],
            'denunciado_fijo'              => ['nullable','string','max:100'],
            'denunciado_movil'             => ['nullable','string','max:100'],
            'denunciado_domicilio_actual'  => ['nullable','string','max:255'],
            'denunciado_latitud'           => ['nullable','numeric','between:-90,90'],
            'denunciado_longitud'          => ['nullable','numeric','between:-180,180'],

            // Caso
            'caso_numero'                  => ['nullable','string','max:50'],
            'caso_fecha_hecho'             => ['nullable','date'],
            'caso_lugar_hecho'             => ['nullable','string','max:255'],
            'caso_zona'                    => ['nullable','string','max:255'],
            'caso_direccion'               => ['nullable','string','max:255'],
            'caso_descripcion'             => ['nullable','string','max:2000'],
            'caso_tipologia'               => ['nullable','string','max:255'],
            'caso_modalidad'               => ['nullable','string','max:255'],

            // Violencias (booleanos)
            'violencia_fisica'             => ['nullable','boolean'],
            'violencia_psicologica'        => ['nullable','boolean'],
            'violencia_sexual'             => ['nullable','boolean'],
            'violencia_economica'          => ['nullable','boolean'],

            // Seguimiento
            'seguimiento_area'             => ['nullable','string','max:255'],
            'seguimiento_area_social'      => ['nullable','string','max:255'],
            'seguimiento_area_legal'       => ['nullable','string','max:255'],
        ]);

        $caso = Caso::create($data);

        return response()->json([
            'message' => 'Caso creado con éxito',
            'caso'    => $caso,
        ], 201);
    }
    public function show(Caso $caso)
    {
        // si ocultaste campos en $hidden, se respetan
        return response()->json($caso);
    }

    public function update(Request $request, Caso $caso)
    {
        // Validación ligera (ajusta a tus reglas reales)
        $data = $request->validate([
            // Denunciante
            'denunciante_nombre_completo' => ['nullable','string','max:255'],
            'denunciante_documento'       => ['nullable','string','max:255'],
            'denunciante_nro'             => ['nullable','string','max:255'],
            'denunciante_sexo'            => ['nullable','string','max:255'],
            'denunciante_residencia'      => ['nullable','string','max:255'],
            'denunciante_estado_civil'    => ['nullable','string','max:255'],
            'denunciante_idioma'          => ['nullable','string','max:255'],
            'denunciante_trabaja'         => ['nullable','boolean'],
            'denunciante_ocupacion'       => ['nullable','string','max:255'],
            'denunciante_domicilio_actual'=> ['nullable','string','max:500'],
            'latitud'                     => ['nullable','numeric'],
            'longitud'                    => ['nullable','numeric'],

            // Familiares
            'familiar1_nombre_completo'   => ['nullable','string','max:255'],
            'familiar1_edad'              => ['nullable','integer'],
            'familiar1_parentesco'        => ['nullable','string','max:255'],
            'familiar1_celular'           => ['nullable','string','max:255'],
            'familiar2_nombre_completo'   => ['nullable','string','max:255'],
            'familiar2_edad'              => ['nullable','integer'],
            'familiar2_parentesco'        => ['nullable','string','max:255'],
            'familiar2_celular'           => ['nullable','string','max:255'],
            'familiar3_nombre_completo'   => ['nullable','string','max:255'],
            'familiar3_edad'              => ['nullable','integer'],
            'familiar3_parentesco'        => ['nullable','string','max:255'],
            'familiar3_celular'           => ['nullable','string','max:255'],
            'familiar4_nombre_completo'   => ['nullable','string','max:255'],
            'familiar4_edad'              => ['nullable','integer'],
            'familiar4_parentesco'        => ['nullable','string','max:255'],
            'familiar4_celular'           => ['nullable','string','max:255'],
            'familiar5_nombre_completo'   => ['nullable','string','max:255'],
            'familiar5_edad'              => ['nullable','integer'],
            'familiar5_parentesco'        => ['nullable','string','max:255'],
            'familiar5_celular'           => ['nullable','string','max:255'],

            // Denunciado
            'denunciado_nombre_completo'  => ['nullable','string','max:255'],
            'denunciado_documento'        => ['nullable','string','max:255'],
            'denunciado_nro'              => ['nullable','string','max:255'],
            'denunciado_sexo'             => ['nullable','string','max:255'],
            'denunciado_residencia'       => ['nullable','string','max:255'],
            'denunciado_domicilio_actual' => ['nullable','string','max:500'],
            'denunciado_latitud'          => ['nullable','numeric'],
            'denunciado_longitud'         => ['nullable','numeric'],

            // Caso
            'caso_numero'                 => ['nullable','string','max:255'],
            'caso_fecha_hecho'            => ['nullable','date'],
            'caso_lugar_hecho'            => ['nullable','string','max:500'],
            'caso_tipologia'              => ['nullable','string','max:255'],
            'caso_modalidad'              => ['nullable','string','max:255'],
            'caso_descripcion'            => ['nullable','string','max:2000'],

            // Violencias
            'violencia_fisica'            => ['nullable','boolean'],
            'violencia_psicologica'       => ['nullable','boolean'],
            'violencia_sexual'            => ['nullable','boolean'],
            'violencia_economica'         => ['nullable','boolean'],

            // Seguimiento
            'seguimiento_area'            => ['nullable','string','max:255'],
            'seguimiento_area_social'     => ['nullable','string','max:255'],
            'seguimiento_area_legal'      => ['nullable','string','max:255'],
        ]);

        // Tip: si vienen "on"/"off" desde un form, castear:
        foreach (['denunciante_trabaja','violencia_fisica','violencia_psicologica','violencia_sexual','violencia_economica'] as $b) {
            if ($request->has($b)) {
                $data[$b] = filter_var($request->input($b), FILTER_VALIDATE_BOOLEAN);
            }
        }

        $caso->update($data);

        return response()->json([
            'message' => 'Caso actualizado',
            'data'    => $caso->fresh()
        ]);
    }
}
