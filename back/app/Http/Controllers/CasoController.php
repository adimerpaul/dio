<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
class CasoController extends Controller
{
    public function seguimiento(\Illuminate\Http\Request $request, \App\Models\Caso $caso)
    {
        // Cabecera compacta para el bloque superior
        $header = [
            'caso_id'        => $caso->id,
            'caso_numero'    => $caso->caso_numero,
            'tipologia'      => $caso->caso_tipologia,
            'modalidad'      => $caso->caso_modalidad,
            'fecha_hecho'    => $caso->caso_fecha_hecho,
            'zona'           => $caso->caso_zona ?: $caso->zona,
            'direccion'      => $caso->caso_direccion ?: $caso->denunciante_domicilio_actual,
            'denunciante'    => $caso->denunciante_nombre_completo,
            'denunciado'     => $caso->denunciado_nombre_completo,
            'registrado_por' => optional($caso->user)->name,
            'fecha_registro' => optional($caso->created_at)?->format('Y-m-d H:i'),
        ];

        // Colecciones por tipo
        $informes = \App\Models\Informe::with('user')
            ->where('caso_id', $caso->id)
            ->get()
            ->map(function ($i) {
                return [
                    'uid'        => 'INF-'.$i->id,
                    'id'         => $i->id,
                    'fecha'      => $i->fecha ? $i->fecha->format('Y-m-d') : optional($i->created_at)?->format('Y-m-d'),
                    'tipo'       => 'Informe',
                    'modulo'     => $i->area ?: 'General',
                    'titulo'     => $i->titulo,
                    'descripcion'=> strip_tags((string) $i->contenido_html),
                    'usuario'    => optional($i->user)->name,
                    'origen'     => 'SLIM',
                    // banderas para columnas estilo “visto/reserva/OJ/instrucción” (si las necesitas a futuro)
                    'visto'      => false,
                    'reserva'    => false,
                    'oj_enviado' => false,
                    'instruccion'=> null,
                    'links'      => [
                        'pdf' => url("/api/informes/{$i->id}/pdf"),
                    ],
                    'icon'       => 'description',
                ];
            });

        $sesiones = \App\Models\SesionPsicologica::with('user')
            ->where('caso_id', $caso->id)
            ->get()
            ->map(function ($s) {
                return [
                    'uid'        => 'SES-'.$s->id,
                    'id'         => $s->id,
                    'fecha'      => $s->fecha ? $s->fecha->format('Y-m-d') : optional($s->created_at)?->format('Y-m-d'),
                    'tipo'       => 'Sesión',
                    'modulo'     => $s->tipo ?: 'Psicológico',
                    'titulo'     => $s->titulo,
                    'descripcion'=> strip_tags((string) $s->contenido_html),
                    'usuario'    => optional($s->user)->name,
                    'origen'     => 'SLIM',
                    'visto'      => false,
                    'reserva'    => false,
                    'oj_enviado' => false,
                    'instruccion'=> null,
                    'links'      => [
                        'pdf' => url("/api/sesiones-psicologicas/{$s->id}/pdf"),
                    ],
                    'icon'       => 'psychology',
                ];
            });

        $docs = \App\Models\Documento::with('user')
            ->where('caso_id', $caso->id)
            ->get()
            ->map(function ($d) {
                return [
                    'uid'        => 'DOC-'.$d->id,
                    'id'         => $d->id,
                    'fecha'      => optional($d->created_at)?->format('Y-m-d'),
                    'tipo'       => 'Documento',
                    'modulo'     => $d->categoria ?: 'Documentos',
                    'titulo'     => $d->titulo,
                    'descripcion'=> (string) $d->descripcion,
                    'usuario'    => optional($d->user)->name,
                    'origen'     => 'SLIM',
                    'visto'      => false,
                    'reserva'    => false,
                    'oj_enviado' => false,
                    'instruccion'=> null,
                    'links'      => [
                        'view'     => url("/api/documentos/{$d->id}/view"),
                        'download' => url("/api/documentos/{$d->id}/download"),
                    ],
                    'icon'       => 'attach_file',
                ];
            });

        $fotos = \App\Models\Fotografia::with('user')
            ->where('caso_id', $caso->id)
            ->get()
            ->map(function ($f) {
                return [
                    'uid'        => 'FOT-'.$f->id,
                    'id'         => $f->id,
                    'fecha'      => optional($f->created_at)?->format('Y-m-d'),
                    'tipo'       => 'Fotografía',
                    'modulo'     => 'Multimedia',
                    'titulo'     => $f->titulo,
                    'descripcion'=> (string) $f->descripcion,
                    'usuario'    => optional($f->user)->name,
                    'origen'     => 'SLIM',
                    'visto'      => false,
                    'reserva'    => false,
                    'oj_enviado' => false,
                    'instruccion'=> null,
                    'links'      => [
                        'open' => $f->url ?: $f->thumb_url,
                    ],
                    'icon'       => 'image',
                ];
            });

        // Merge + ordenar por fecha (desc) y por created_at como desempate
        $items = collect()
            ->merge($informes)
            ->merge($sesiones)
            ->merge($docs)
            ->merge($fotos)
            ->sortByDesc(function ($x) {
                return sprintf('%s-%s', $x['fecha'] ?? '0000-00-00', $x['uid']);
            })
            ->values();

        return response()->json([
            'header' => $header,
            'items'  => $items,
        ]);
    }


    public function pdfHojaRuta(Request $request, Caso $caso)
    {
        // 'denunciante' por defecto; acepta 'denunciado'
        $tipo = strtolower($request->query('tipo', 'denunciante'));
        if (!in_array($tipo, ['denunciante', 'denunciado'])) {
            $tipo = 'denunciante';
        }

        // Coordenadas por tipo
        if ($tipo === 'denunciado') {
            $lat = is_numeric($caso->denunciado_latitud)  ? (float)$caso->denunciado_latitud  : null;
            $lng = is_numeric($caso->denunciado_longitud) ? (float)$caso->denunciado_longitud : null;
            $nombre    = $caso->denunciado_nombre_completo ?: trim("{$caso->denunciado_nombres} {$caso->denunciado_paterno} {$caso->denunciado_materno}");
            $telefono  = $caso->denunciado_telefono ?: ($caso->denunciado_movil ?: $caso->denunciado_fijo);
            $direccion = $caso->denunciado_domicilio_actual ?: '—';
            $zona      = $caso->caso_zona ?: $caso->zona; // si tienes zona específica del denunciado, cámbialo aquí
            $tituloPersona = 'Denunciado';
        } else {
            // denunciante
            $lat = is_numeric($caso->latitud)  ? (float)$caso->latitud  : null;
            $lng = is_numeric($caso->longitud) ? (float)$caso->longitud : null;
            $nombre    = $caso->denunciante_nombre_completo ?: trim("{$caso->denunciante_nombres} {$caso->denunciante_paterno} {$caso->denunciante_materno}");
            $telefono  = $caso->denunciante_telefono ?: ($caso->denunciante_movil ?: $caso->denunciante_fijo);
            $direccion = $caso->denunciante_domicilio_actual ?: ($caso->caso_direccion ?: '—');
            $zona      = $caso->caso_zona ?: $caso->zona;
            $tituloPersona = 'Denunciante';
        }

        // Fallback Oruro si no hay coords
        $LAT = $lat ?? -17.966700;
        $LNG = $lng ?? -67.116700;
        $HAS = is_numeric($lat) && is_numeric($lng);

        // Pasamos todo a la vista
        return view('casos.pdfHojaRuta', [
            'caso'          => $caso,
            'tipo'          => $tipo,
            'tituloPersona' => $tituloPersona,
            'LAT'           => $LAT,
            'LNG'           => $LNG,
            'HAS'           => $HAS,
            'nombre'        => $nombre ?: '—',
            'telefono'      => $telefono ?: '—',
            'zona'          => $zona ?: '—',
            'direccion'     => $direccion ?: '—',
        ]);
    }
    public function pdf(Request $request, Caso $caso)
    {
        // Opciones extra para mejor render
        $pdf = Pdf::loadView('casos.pdf', [
            'caso' => $caso,
        ])->setPaper('A4', 'portrait');

        // Para acentos: usar DejaVu Sans
        $pdf->getDomPDF()->getOptions()->set('defaultFont', 'DejaVu Sans');

        // ?download=1 para descargar, 0 para ver en el navegador
        $download = (int) $request->query('download', 0) === 1;

        $filename = 'SLIM_Caso_'.$caso->id.'.pdf';
        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }
    /**
     * GET /casos?q=texto&page=1&per_page=10
     * Devuelve paginado con filtro por texto libre.
     */
    public function index(Request $request)
    {
        $q        = trim((string) $request->get('q', ''));
        $perPage  = (int) $request->get('per_page', 10);
        $perPage  = max(5, min($perPage, 100)); // entre 5 y 100

//        $columns = [
//            'id',
//            'caso_numero',
//            'caso_fecha_hecho',
//            'caso_tipologia',
//            'caso_zona',
//            'caso_direccion',
//            'caso_descripcion',
//            'denunciante_nombre_completo',
//            'denunciante_nro',
//            'denunciado_nombre_completo',
//            'denunciado_nro',
//            'created_at'
//        ];

        $query = Caso::orderByDesc('created_at');

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
        $user = $request->user();
        if($user->role == 'Psicologo'){
            $query->where('psicologica_user_id', $user->id);
        }
        if($user->role == 'Social'){
            $query->where('trabajo_social_user_id', $user->id);
        }
        if($user->role == 'Abogado'){
            $query->where('legal_user_id', $user->id);
        }
        $query->with(['psicologica_user:id,name','trabajo_social_user:id,name','legal_user:id,name','user:id,name']);

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
//            'denunciante_nombre_completo'  => ['required','string','max:255'],
//            'area'          => ['nullable','string','max:255'],
//            'zona'          => ['nullable','string','max:255'],
            'denunciante_nombres'          => ['nullable','string','max:255'],
            'denunciante_paterno'          => ['nullable','string','max:255'],
            'denunciante_materno'          => ['nullable','string','max:255'],
            'denunciante_documento'        => ['nullable','string','max:100'],
            'denunciante_nro'              => ['nullable','string','max:100'],
            'denunciante_sexo'             => ['nullable','string','max:30'],
            'denunciante_lugar_nacimiento' => ['nullable','string','max:255'],
            'denunciante_fecha_nacimiento' => ['nullable','date'],
            'denunciante_edad'             => ['nullable','integer','min:0','max:120'],
            'denunciante_telefono'             => ['nullable','string','min:0','max:120'],
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
            'denunciado_telefono'              => ['nullable','string','min:0','max:120'],
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
//            'seguimiento_area'             => ['nullable','string','max:255'],
//            'seguimiento_area_social'      => ['nullable','string','max:255'],
//            'seguimiento_area_legal'       => ['nullable','string','max:255'],
        ]);

        $numero_casos = $this->numeroCaso();
        $request['caso_numero'] = $numero_casos;
        $request['fecha_apertura_caso'] = date('Y-m-d');
        $user = $request->user();
        $request['user_id'] = $user->id;
        $request['area'] = $user->area;
        $request['zona'] = $user->zona;


        $caso = Caso::create($request->all());

        return response()->json([
            'message' => 'Caso creado con éxito',
            'caso'    => $caso,
        ], 201);
    }
    function numeroCaso(){
        $year = date('Y');
        $count = Caso::whereYear('created_at', $year)->count() + 1;
        $numero = str_pad($count, 3, '0', STR_PAD_LEFT) . '/' . substr($year, -2);
        return $numero;
    }
    public function show(Caso $caso)
    {
        $caso = Caso::with(['psicologica_user:id,name,celular','trabajo_social_user:id,name,celular','legal_user:id,name,celular','user:id,name,celular'])->find($caso->id);
        return response()->json($caso);
    }

    public function update(Request $request, Caso $caso)
    {
        // Validación ligera (ajusta a tus reglas reales)
        $data = $request->validate([
            // Denunciante
//            'area'          => ['nullable','string','max:255'],
//            'zona'          => ['nullable','string','max:255'],
            'denunciante_nombres'          => ['string','max:255'],
            'denunciante_paterno'          => ['nullable','string','max:255'],
            'denunciante_materno'          => ['nullable','string','max:255'],
            'denunciante_documento'        => ['nullable','string','max:100'],
            'denunciante_nro'              => ['nullable','string','max:100'],
            'denunciante_sexo'             => ['nullable','string','max:30'],
            'denunciante_lugar_nacimiento' => ['nullable','string','max:255'],
            'denunciante_fecha_nacimiento' => ['nullable','date'],
            'denunciante_edad'             => ['nullable','integer','min:0','max:120'],
            'denunciante_telefono'             => ['nullable','string','min:0','max:120'],
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
            'denunciado_telefono'              => ['nullable','string','min:0','max:120'],
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
//            'seguimiento_area'            => ['nullable','string','max:255'],
//            'seguimiento_area_social'     => ['nullable','string','max:255'],
//            'seguimiento_area_legal'      => ['nullable','string','max:255'],
        ]);

        // Tip: si vienen "on"/"off" desde un form, castear:
        foreach (['denunciante_trabaja','violencia_fisica','violencia_psicologica','violencia_sexual','violencia_economica'] as $b) {
            if ($request->has($b)) {
                $data[$b] = filter_var($request->input($b), FILTER_VALIDATE_BOOLEAN);
            }
        }

        $caso->update($request->all());

        return response()->json([
            'message' => 'Caso actualizado',
            'data'    => $caso->fresh()
        ]);
    }
}
