<?php

namespace App\Http\Controllers;

use App\Models\Slim;
use App\Models\Psicologica;
use App\Models\InformeLegal;
use App\Models\Documento;
use App\Models\Fotografia;
use App\Models\Problematica;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SlimController extends Controller
{
    public function pendientesResumen(Request $request)
    {
        $user = $request->user();
        $q = Slim::query();

        switch ($user->role) {
            case 'Psicologo':
                $q->where('psicologica_user_id', $user->id)
                    ->whereDoesntHave('psicologicas'); // sin primera sesión
                break;

            case 'Abogado':
                $q->where('legal_user_id', $user->id)
                    ->whereDoesntHave('informesLegales'); // sin primer informe legal
                break;

            case 'Social':
                $q->where('trabajo_social_user_id', $user->id)
                    // si a futuro tienes "informes sociales" como entidad propia polimórfica, aquí el whereDoesntHave
                    ->whereDoesntHave('problematicas'); // placeholder: ajustar si usas otra entidad para Social
                break;

            default:
                return response()->json(['pendientes' => 0]);
        }

        return response()->json(['pendientes' => $q->count()]);
    }

    /**
     * GET /slims?q=&page=&per_page=
     */
    public function index(Request $request)
    {
        $q        = trim((string) $request->get('q', ''));
        $perPage  = (int) $request->get('per_page', 10);
        $perPage  = max(5, min($perPage, 100));

        $query = Slim::orderByDesc('created_at');

        if ($q !== '') {
            $like = "%{$q}%";
            $query->where(function ($s) use ($like) {
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
        if ($user->role == 'Psicologo') { $query->where('psicologica_user_id', $user->id); }
        if ($user->role == 'Social')    { $query->where('trabajo_social_user_id', $user->id); }
        if ($user->role == 'Abogado')   { $query->where('legal_user_id', $user->id); }

        $query->with(['psicologica_user:id,name','trabajo_social_user:id,name','legal_user:id,name','user:id,name']);

        $paginated = $query->paginate($perPage)->appends($request->query());
        $items = $paginated->getCollection();
        $now = Carbon::now();

        $items->transform(function ($c) use ($user, $now) {
            $rolUsuario = $user->role;
            $meAsignado =
                ($rolUsuario === 'Psicologo' && (int)$c->psicologica_user_id === (int)$user->id) ||
                ($rolUsuario === 'Social'    && (int)$c->trabajo_social_user_id === (int)$user->id) ||
                ($rolUsuario === 'Abogado'   && (int)$c->legal_user_id === (int)$user->id);

            $apertura = $c->fecha_apertura_caso
                ? Carbon::parse($c->fecha_apertura_caso)
                : ($c->created_at ? Carbon::parse($c->created_at) : $now);

            $deadline = $apertura->copy()->addDays(10);
            $diasRest = $now->diffInDays($deadline, false);
            $atrasado = $diasRest < 0;

            $hecho = false;
            $labelListo = 'Primer informe listo';

            if ($meAsignado) {
                switch ($rolUsuario) {
                    case 'Psicologo':
                        $hecho = Psicologica::whereMorphedTo('caseable', Slim::class)
                            ->where('caseable_id', $c->id)->exists();
                        $labelListo = 'Primera sesión lista';
                        break;
                    case 'Abogado':
                        $hecho = InformeLegal::whereMorphedTo('caseable', Slim::class)
                            ->where('caseable_id', $c->id)->exists();
                        $labelListo = 'Primer informe (Legal) listo';
                        break;
                    case 'Social':
                        $hecho = Problematica::whereMorphedTo('caseable', Slim::class)
                            ->where('caseable_id', $c->id)->exists();
                        $labelListo = 'Primera problemática registrada';
                        break;
                }
            }

            $c->setAttribute('mi_estado', [
                'me_asignado'    => $meAsignado,
                'rol'            => $meAsignado ? $rolUsuario : null,
                'primer_hecho'   => $hecho,
                'label_listo'    => $labelListo,
                'deadline'       => $deadline->format('Y-m-d'),
                'dias_restantes' => (int) $diasRest,
                'atrasado'       => $atrasado,
            ]);

            return $c;
        });

        return response()->json([
            'data'         => $items->values(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'per_page'     => $paginated->perPage(),
            'total'        => $paginated->total(),
            'from'         => $paginated->firstItem(),
            'to'           => $paginated->lastItem(),
        ]);
    }

    /**
     * POST /slims  (CREACIÓN)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // (mismas reglas que tenías en CasoController@store)
            'denunciante_nombres'          => ['nullable','string','max:255'],
            'denunciante_paterno'          => ['nullable','string','max:255'],
            'denunciante_materno'          => ['nullable','string','max:255'],
            'denunciante_documento'        => ['nullable','string','max:100'],
            'denunciante_nro'              => ['nullable','string','max:100'],
            'denunciante_sexo'             => ['nullable','string','max:30'],
            'denunciante_lugar_nacimiento' => ['nullable','string','max:255'],
            'denunciante_fecha_nacimiento' => ['nullable','date'],
            'denunciante_edad'             => ['nullable','integer','min:0','max:120'],
            'denunciante_telefono'         => ['nullable','string','max:120'],
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

            'familiar1_nombre_completo'    => ['nullable','string','max:255'],
            'familiar1_edad'               => ['nullable','integer','min:0','max:120'],
            'familiar1_parentesco'         => ['nullable','string','max:100'],
            'familiar1_celular'            => ['nullable','string','max:100'],

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
            'denunciado_telefono'          => ['nullable','string','max:120'],
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

            'caso_numero'                  => ['nullable','string','max:50'],
            'caso_fecha_hecho'             => ['nullable','date'],
            'caso_lugar_hecho'             => ['nullable','string','max:255'],
            'caso_zona'                    => ['nullable','string','max:255'],
            'caso_direccion'               => ['nullable','string','max:255'],
            'caso_descripcion'             => ['nullable','string','max:2000'],
            'caso_tipologia'               => ['nullable','string','max:255'],
            'caso_modalidad'               => ['nullable','string','max:255'],

            'violencia_fisica'             => ['nullable','boolean'],
            'violencia_psicologica'        => ['nullable','boolean'],
            'violencia_sexual'             => ['nullable','boolean'],
            'violencia_economica'          => ['nullable','boolean'],
        ]);

        $request['caso_numero'] = $this->numeroCaso();
        $request['fecha_apertura_caso'] = date('Y-m-d');

        $user = $request->user();
        $request['user_id'] = $user->id;
        $request['area'] = $user->area;
        $request['zona'] = $user->zona;

        $slim = Slim::create($request->all());

        return response()->json([
            'message' => 'SLIM creado con éxito',
            'slim'    => $slim,
        ], 201);
    }

    private function numeroCaso(): string
    {
        $year = date('Y');
        $count = Slim::whereYear('created_at', $year)->count() + 1;
        return str_pad($count, 3, '0', STR_PAD_LEFT) . '/' . substr($year, -2);
    }

    public function show(Slim $slim)
    {
        $slim->load(['psicologica_user:id,name,celular','trabajo_social_user:id,name,celular','legal_user:id,name,celular','user:id,name,celular']);
        return response()->json($slim);
    }

    public function update(Request $request, Slim $slim)
    {
        $data = $request->all();

        foreach (['denunciante_trabaja','violencia_fisica','violencia_psicologica','violencia_sexual','violencia_economica'] as $b) {
            if ($request->has($b)) {
                $data[$b] = filter_var($request->input($b), FILTER_VALIDATE_BOOLEAN);
            }
        }

        $slim->update($data);

        return response()->json([
            'message' => 'SLIM actualizado',
            'data'    => $slim->fresh()
        ]);
    }

    public function seguimiento(Request $request, Slim $slim)
    {
        $header = [
            'slim_id'       => $slim->id,
            'caso_numero'   => $slim->caso_numero,
            'tipologia'     => $slim->caso_tipologia,
            'modalidad'     => $slim->caso_modalidad,
            'fecha_hecho'   => $slim->caso_fecha_hecho,
            'zona'          => $slim->caso_zona ?: $slim->zona,
            'direccion'     => $slim->caso_direccion ?: $slim->denunciante_domicilio_actual,
            'denunciante'   => $slim->denunciante_nombre_completo,
            'denunciado'    => $slim->denunciado_nombre_completo,
            'registrado_por'=> optional($slim->user)->name,
            'fecha_registro'=> optional($slim->created_at)?->format('Y-m-d H:i'),
        ];

        $informes = InformeLegal::with('user')
            ->whereMorphedTo('caseable', Slim::class)
            ->where('caseable_id', $slim->id)
            ->get()->map(function ($i) {
                return [
                    'uid'   => 'INF-'.$i->id,
                    'id'    => $i->id,
                    'fecha' => $i->fecha ? $i->fecha->format('Y-m-d') : optional($i->created_at)?->format('Y-m-d'),
                    'tipo'  => 'Informe Legal',
                    'modulo'=> 'Legal',
                    'titulo'=> $i->titulo,
                    'descripcion'=> strip_tags((string) $i->contenido_html),
                    'usuario'    => optional($i->user)->name,
                    'origen'     => 'SLIM',
                    'links'      => ['pdf' => url("/api/informes/{$i->id}/pdf")],
                    'icon'       => 'description',
                ];
            });

        $sesiones = Psicologica::with('user')
            ->whereMorphedTo('caseable', Slim::class)
            ->where('caseable_id', $slim->id)
            ->get()->map(function ($s) {
                return [
                    'uid'   => 'SES-'.$s->id,
                    'id'    => $s->id,
                    'fecha' => $s->fecha ? $s->fecha->format('Y-m-d') : optional($s->created_at)?->format('Y-m-d'),
                    'tipo'  => 'Sesión',
                    'modulo'=> $s->tipo ?: 'Psicológico',
                    'titulo'=> $s->titulo,
                    'descripcion'=> strip_tags((string) $s->contenido_html),
                    'usuario'    => optional($s->user)->name,
                    'origen'     => 'SLIM',
                    'links'      => ['pdf' => url("/api/sesiones-psicologicas/{$s->id}/pdf")],
                    'icon'       => 'psychology',
                ];
            });

        $docs = Documento::with('user')
            ->whereMorphedTo('caseable', Slim::class)
            ->where('caseable_id', $slim->id)
            ->get()->map(function ($d) {
                return [
                    'uid'   => 'DOC-'.$d->id,
                    'id'    => $d->id,
                    'fecha' => optional($d->created_at)?->format('Y-m-d'),
                    'tipo'  => 'Documento',
                    'modulo'=> $d->categoria ?: 'Documentos',
                    'titulo'=> $d->titulo,
                    'descripcion'=> (string) $d->descripcion,
                    'usuario'    => optional($d->user)->name,
                    'origen'     => 'SLIM',
                    'links'      => [
                        'view'     => url("/api/documentos/{$d->id}/view"),
                        'download' => url("/api/documentos/{$d->id}/download"),
                    ],
                    'icon'       => 'attach_file',
                ];
            });

        $fotos = Fotografia::with('user')
            ->whereMorphedTo('caseable', Slim::class)
            ->where('caseable_id', $slim->id)
            ->get()->map(function ($f) {
                return [
                    'uid'   => 'FOT-'.$f->id,
                    'id'    => $f->id,
                    'fecha' => optional($f->created_at)?->format('Y-m-d'),
                    'tipo'  => 'Fotografía',
                    'modulo'=> 'Multimedia',
                    'titulo'=> $f->titulo,
                    'descripcion'=> (string) $f->descripcion,
                    'usuario'    => optional($f->user)->name,
                    'origen'     => 'SLIM',
                    'links'      => ['open' => $f->url ?: $f->thumb_url],
                    'icon'       => 'image',
                ];
            });

        $items = collect()->merge($informes)->merge($sesiones)->merge($docs)->merge($fotos)
            ->sortByDesc(fn($x) => sprintf('%s-%s', $x['fecha'] ?? '0000-00-00', $x['uid']))->values();

        return response()->json(['header' => $header, 'items' => $items]);
    }

    public function pdfHojaRuta(Request $request, Slim $slim)
    {
        $tipo = strtolower($request->query('tipo', 'denunciante'));
        if (!in_array($tipo, ['denunciante', 'denunciado'])) $tipo = 'denunciante';

        if ($tipo === 'denunciado') {
            $lat = is_numeric($slim->denunciado_latitud)  ? (float)$slim->denunciado_latitud  : null;
            $lng = is_numeric($slim->denunciado_longitud) ? (float)$slim->denunciado_longitud : null;
            $nombre    = $slim->denunciado_nombre_completo ?: trim("{$slim->denunciado_nombres} {$slim->denunciado_paterno} {$slim->denunciado_materno}");
            $telefono  = $slim->denunciado_telefono ?: ($slim->denunciado_movil ?: $slim->denunciado_fijo);
            $direccion = $slim->denunciado_domicilio_actual ?: '—';
            $zona      = $slim->caso_zona ?: $slim->zona;
            $tituloPersona = 'Denunciado';
        } else {
            $lat = is_numeric($slim->latitud)  ? (float)$slim->latitud  : null;
            $lng = is_numeric($slim->longitud) ? (float)$slim->longitud : null;
            $nombre    = $slim->denunciante_nombre_completo ?: trim("{$slim->denunciante_nombres} {$slim->denunciante_paterno} {$slim->denunciante_materno}");
            $telefono  = $slim->denunciante_telefono ?: ($slim->denunciante_movil ?: $slim->denunciante_fijo);
            $direccion = $slim->denunciante_domicilio_actual ?: ($slim->caso_direccion ?: '—');
            $zona      = $slim->caso_zona ?: $slim->zona;
            $tituloPersona = 'Denunciante';
        }

        $LAT = $lat ?? -17.966700;
        $LNG = $lng ?? -67.116700;
        $HAS = is_numeric($lat) && is_numeric($lng);

        return view('casos.pdfHojaRuta', [
            'caso'          => $slim,    // la vista puede seguir llamándose igual
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

    public function pdf(Request $request, Slim $slim)
    {
        $pdf = Pdf::loadView('casos.pdf', ['caso' => $slim])->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->getOptions()->set('defaultFont', 'DejaVu Sans');
        $download = (int) $request->query('download', 0) === 1;
        $filename = 'SLIM_'.$slim->id.'.pdf';
        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }
}
