<?php

namespace App\Http\Controllers;

use App\Models\Dna;
use App\Models\DnaMenor;
use App\Models\DnaFamiliar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DnaController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string) $request->get('q', ''));
        $perPage = max(5, min((int) $request->get('per_page', 10), 100));

        $query = Dna::query()
            ->with(['user:id,name', 'abogado:id,name'])
            ->withCount(['menores', 'familiares'])
            ->orderByDesc('created_at');

        if ($q !== '') {
            $like = "%{$q}%";
            $query->where(function ($s) use ($like) {
                $s->orWhere('codigo', 'like', $like)
                    ->orWhere('tipo_proceso', 'like', $like)
                    ->orWhere('denunciante_nombre', 'like', $like)
                    ->orWhere('denunciado_nombre', 'like', $like)
                    ->orWhere('zona', 'like', $like)
                    ->orWhere('descripcion', 'like', $like);
            });
        }

        return $query->paginate($perPage)->appends($request->query());
    }

    public function store(Request $request)
    {
        $rules = [
            'codigo'         => ['nullable','string','max:50'],
            'fecha_atencion' => ['nullable','date'],
            'tipo_proceso'   => ['required', Rule::in(['PROCESO_PENAL','PROCESO_FAMILIAR','PROCESO_NNA','APOYO_INTEGRAL'])],
            'principal'      => ['nullable','string','max:255'],
            'domicilio'      => ['nullable','string','max:255'],
            'telefono'       => ['nullable','string','max:100'],
            'zona'           => ['nullable','string','max:120'],

            // Denunciado
            'denunciado_nombre'        => ['nullable','string','max:255'],
            'denunciado_sexo'          => ['nullable','string','max:30'],
            'denunciado_edad'          => ['nullable','integer','min:0','max:120'],
            'denunciado_relacion'      => ['nullable','string','max:255'],
            'denunciado_ci'            => ['nullable','string','max:100'],
            'denunciado_domicilio'     => ['nullable','string','max:255'],
            'denunciado_telefono'      => ['nullable','string','max:100'],
            'denunciado_lugar_trabajo' => ['nullable','string','max:255'],
            'denunciado_ocupacion'     => ['nullable','string','max:255'],

            // Denunciante
            'denunciante_nombre'        => ['nullable','string','max:255'],
            'denunciante_sexo'          => ['nullable','string','max:30'],
            'denunciante_edad'          => ['nullable','integer','min:0','max:120'],
            'denunciante_ci'            => ['nullable','string','max:100'],
            'denunciante_domicilio'     => ['nullable','string','max:255'],
            'denunciante_telefono'      => ['nullable','string','max:100'],
            'denunciante_lugar_trabajo' => ['nullable','string','max:255'],
            'denunciante_ocupacion'     => ['nullable','string','max:255'],

            'descripcion'     => ['nullable','string','max:5000'],
            'abogado_user_id' => ['nullable','exists:users,id'],

            // Arrays relacionados
            'menores'                     => ['nullable','array'],
            'menores.*.nombre'            => ['nullable','string','max:255'],
            'menores.*.gestante'          => ['nullable','boolean'],
            'menores.*.edad_anios'        => ['nullable','integer','min:0','max:20'],
            'menores.*.edad_meses'        => ['nullable','integer','min:0','max:11'],
            'menores.*.sexo'              => ['nullable','string','max:30'],
            'menores.*.cert_nac'          => ['nullable','boolean'],
            'menores.*.estudia'           => ['nullable','boolean'],
            'menores.*.ultimo_curso'      => ['nullable','string','max:120'],
            'menores.*.tipo_trabajo'      => ['nullable','string','max:120'],

            'grupo_familiar'              => ['nullable','array'],
            'grupo_familiar.*.nombre'     => ['nullable','string','max:255'],
            'grupo_familiar.*.parentesco' => ['nullable','string','max:120'],
            'grupo_familiar.*.edad'       => ['nullable','integer','min:0','max:120'],
            'grupo_familiar.*.sexo'       => ['nullable','string','max:30'],
            'grupo_familiar.*.instruccion'=> ['nullable','string','max:120'],
            'grupo_familiar.*.ocupacion'  => ['nullable','string','max:120'],
        ];

        $data = $request->validate($rules);
        $data['area']    = 'DNA';
        $data['user_id'] = $request->user()?->id;

        $menores = $data['menores'] ?? [];
        $familia = $data['grupo_familiar'] ?? [];
        unset($data['menores'], $data['grupo_familiar']);

        $dna = Dna::create($data);

        // Hijos
        foreach ($menores as $m) {
            $dna->menores()->create([
                'nombre'       => $m['nombre']        ?? null,
                'gestante'     => (bool)($m['gestante'] ?? false),
                'edad_anios'   => $m['edad_anios']     ?? null,
                'edad_meses'   => $m['edad_meses']     ?? null,
                'sexo'         => $m['sexo']           ?? null,
                'cert_nac'     => (bool)($m['cert_nac'] ?? false),
                'estudia'      => (bool)($m['estudia']  ?? false),
                'ultimo_curso' => $m['ultimo_curso']    ?? null,
                'tipo_trabajo' => $m['tipo_trabajo']    ?? null,
            ]);
        }

        foreach ($familia as $g) {
            $dna->familiares()->create([
                'nombre'      => $g['nombre']      ?? null,
                'parentesco'  => $g['parentesco']  ?? null,
                'edad'        => $g['edad']        ?? null,
                'sexo'        => $g['sexo']        ?? null,
                'instruccion' => $g['instruccion'] ?? null,
                'ocupacion'   => $g['ocupacion']   ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Caso Dna creado con éxito',
            'dna'     => $dna->load(['user:id,name','abogado:id,name','menores','familiares']),
        ], 201);
    }

    public function show(Dna $dna)
    {
        return $dna->load(['user:id,name','abogado:id,name','menores','familiares']);
    }

    public function update(Request $request, Dna $dna)
    {
        // Actualiza el padre
        $dna->update($request->except(['menores', 'grupo_familiar']));

        // Sincronización simple: borrar y recrear
        if ($request->has('menores')) {
            $dna->menores()->delete();
            foreach ((array)$request->input('menores', []) as $m) {
                $dna->menores()->create([
                    'nombre'       => $m['nombre']        ?? null,
                    'gestante'     => (bool)($m['gestante'] ?? false),
                    'edad_anios'   => $m['edad_anios']     ?? null,
                    'edad_meses'   => $m['edad_meses']     ?? null,
                    'sexo'         => $m['sexo']           ?? null,
                    'cert_nac'     => (bool)($m['cert_nac'] ?? false),
                    'estudia'      => (bool)($m['estudia']  ?? false),
                    'ultimo_curso' => $m['ultimo_curso']    ?? null,
                    'tipo_trabajo' => $m['tipo_trabajo']    ?? null,
                ]);
            }
        }

        if ($request->has('grupo_familiar')) {
            $dna->familiares()->delete();
            foreach ((array)$request->input('grupo_familiar', []) as $g) {
                $dna->familiares()->create([
                    'nombre'      => $g['nombre']      ?? null,
                    'parentesco'  => $g['parentesco']  ?? null,
                    'edad'        => $g['edad']        ?? null,
                    'sexo'        => $g['sexo']        ?? null,
                    'instruccion' => $g['instruccion'] ?? null,
                    'ocupacion'   => $g['ocupacion']   ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Caso Dna actualizado',
            'dna'     => $dna->load(['user:id,name','abogado:id,name','menores','familiares']),
        ]);
    }

    public function destroy(Dna $dna)
    {
        $dna->delete();
        return response()->json(['message' => 'Caso Dna eliminado']);
    }
}
