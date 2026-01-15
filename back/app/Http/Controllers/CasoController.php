<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Documento;
use App\Models\Fotografia;
use App\Models\InformeLegal;
use App\Models\InformesSocial;
use App\Models\Psicologica;
use App\Models\Slam;
use App\Models\Slim;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Informe;
use App\Models\SesionPsicologica;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CasoController extends Controller
{
    public function historialDocumentos(Request $request)
    {
        $ci = trim((string)$request->query('ci', ''));
        $ci = preg_replace('/\s+/', '', $ci);

        if ($ci === '') {
            return response()->json([
                'ci' => $ci,
                'casos_encontrados' => 0,
                'casos' => [],
                'data' => [],
            ]);
        }

        // 1) Buscar casos relacionados a ese CI (en cualquier rol)
        $casos = Caso::query()
            ->where(function ($q) use ($ci) {
                $q->whereHas('victimas', fn($s) => $s->where('ci', $ci))
                    ->orWhereHas('menores', fn($s) => $s->where('ci', $ci))
                    ->orWhereHas('denunciantes', fn($s) => $s->where('denunciante_nro', $ci))
                    ->orWhereHas('denunciados', fn($s) => $s->where('denunciado_nro', $ci));
            })
            ->orWhere(function ($q) use ($ci) {
                $q->where('nurej', $ci)
                  ->orWhere('cud', $ci);
            })
            ->with([
                // Cargar SOLO los relacionados al CI para poder detectar el rol fácil
                'victimas' => fn($s) => $s->where('ci', $ci),
                'menores'  => fn($s) => $s->where('ci', $ci),
                'denunciantes' => fn($s) => $s->where('denunciante_nro', $ci),
                'denunciados'  => fn($s) => $s->where('denunciado_nro', $ci),

                // Archivos / módulos
                'documentos.user:id,name',
                'fotografias.user:id,name',
                'psicologicas.user:id,name',
                'informesSociales.user:id,name',
                'informesLegales.user:id,name',
            ])
            ->orderByDesc('created_at')
            ->get(['id', 'tipo', 'caso_numero', 'created_at', 'updated_at', 'fecha_apertura_caso', 'titulo']);

        // 2) Lista de casos + cómo participa
        $casosInfo = $casos->map(function ($caso) {

            $participa = [];

            // === Victima ===
            if ($caso->victimas?->isNotEmpty()) {
                foreach ($caso->victimas as $v) {
                    $participa[] = [
                        'rol' => 'Víctima',
                        'nombre' => $v->nombres_apellidos ?? trim(($v->nombres ?? '') . ' ' . ($v->paterno ?? '') . ' ' . ($v->materno ?? '')),
                        'ci' => $v->ci ?? $ci,
                    ];
                }
            }

            // === Menor ===
            if ($caso->menores?->isNotEmpty()) {
                foreach ($caso->menores as $m) {
                    $participa[] = [
                        'rol' => 'Menor',
                        'nombre' => $m->nombres_apellidos ?? trim(($m->nombres ?? '') . ' ' . ($m->paterno ?? '') . ' ' . ($m->materno ?? '')),
                        'ci' => $m->ci ?? $ci,
                    ];
                }
            }

            // === Denunciante ===
            if ($caso->denunciantes?->isNotEmpty()) {
                foreach ($caso->denunciantes as $d) {
                    $participa[] = [
                        'rol' => 'Denunciante',
                        'nombre' => trim(($d->denunciante_nombres ?? '') . ' ' . ($d->denunciante_paterno ?? '') . ' ' . ($d->denunciante_materno ?? '')),
                        'ci' => $d->denunciante_nro ?? $ci,
                    ];
                }
            }

            // === Denunciado ===
            if ($caso->denunciados?->isNotEmpty()) {
                foreach ($caso->denunciados as $d) {
                    $participa[] = [
                        'rol' => 'Denunciado',
                        'nombre' => trim(($d->denunciado_nombres ?? '') . ' ' . ($d->denunciado_paterno ?? '') . ' ' . ($d->denunciado_materno ?? '')),
                        'ci' => $d->denunciado_nro ?? $ci,
                    ];
                }
            }

            $casoLabel = trim(($caso->tipo ?: 'CASO') . ' ' . ($caso->caso_numero ?: "#{$caso->id}"));

            return [
                'id' => $caso->id,
                'caso' => $casoLabel,
                'tipo' => $caso->tipo,
                'caso_numero' => $caso->caso_numero,
                'titulo' => $caso->titulo,
                'fecha_apertura' => optional($caso->fecha_apertura_caso)->format('Y-m-d'),
                'creado' => optional($caso->created_at)->format('Y-m-d H:i'),

                // antes: participa_como => ['Víctima', 'Denunciado']
                // ahora:
                'participa' => $participa,
            ];
        })->values();

        // 3) Construir historial “items”
        $items = collect();

        foreach ($casos as $caso) {
            $casoLabel = trim(($caso->tipo ?: 'CASO') . ' ' . ($caso->caso_numero ?: "#{$caso->id}"));

            foreach (($caso->documentos ?? []) as $d) {
                $items->push([
                    'tipo' => 'Documento',
                    'nombre' => $d->titulo ?: ($d->original_name ?: 'Documento'),
                    'fecha_subida' => optional($d->created_at)->format('Y-m-d H:i'),
                    'url' => $d->url,
                    'caso_id' => $caso->id,
                    'caso' => $casoLabel,
                    'usuario' => optional($d->user)->name,
                    'extra' => ['categoria' => $d->categoria ?? null],
                ]);
            }

            foreach (($caso->fotografias ?? []) as $f) {
                $items->push([
                    'tipo' => 'Fotografía',
                    'nombre' => $f->titulo ?: ($f->original_name ?: 'Fotografía'),
                    'fecha_subida' => optional($f->created_at)->format('Y-m-d H:i'),
                    'url' => $f->url ?: $f->thumb_url,
                    'caso_id' => $caso->id,
                    'caso' => $casoLabel,
                    'usuario' => optional($f->user)->name,
                    'extra' => ['descripcion' => $f->descripcion ?? null],
                ]);
            }

            foreach (($caso->psicologicas ?? []) as $p) {
                $items->push([
                    'tipo' => 'Sesión Psicológica',
                    'nombre' => $p->titulo ?: 'Sesión',
                    'fecha_subida' => optional($p->fecha)->format('Y-m-d') ?: optional($p->created_at)->format('Y-m-d H:i'),
                    'url' => $p->archivo ?? null,
                    'caso_id' => $caso->id,
                    'caso' => $casoLabel,
                    'usuario' => optional($p->user)->name,
                    'extra' => ['pdf' => url("/api/sesiones-psicologicas/{$p->id}/pdf")],
                ]);
            }

            foreach (($caso->informesSociales ?? []) as $s) {
                $items->push([
                    'tipo' => 'Informe Social',
                    'nombre' => $s->titulo ?: 'Informe Social',
                    'fecha_subida' => optional($s->fecha)->format('Y-m-d') ?: optional($s->created_at)->format('Y-m-d H:i'),
                    'url' => $s->archivo ?? null,
                    'caso_id' => $caso->id,
                    'caso' => $casoLabel,
                    'usuario' => optional($s->user)->name,
                    'extra' => ['pdf' => url("/api/informesSocial/{$s->id}/pdf")],
                ]);
            }

            foreach (($caso->informesLegales ?? []) as $l) {
                $items->push([
                    'tipo' => 'Informe Legal',
                    'nombre' => $l->titulo ?: 'Informe Legal',
                    'fecha_subida' => optional($l->fecha)->format('Y-m-d') ?: optional($l->created_at)->format('Y-m-d H:i'),
                    'url' => $l->archivo ?? null,
                    'caso_id' => $caso->id,
                    'caso' => $casoLabel,
                    'usuario' => optional($l->user)->name,
                    'extra' => ['pdf' => url("/api/informes/{$l->id}/pdf")],
                ]);
            }

            if (!empty($caso->codigo_file)) {
                $items->push([
                    'tipo' => 'Código File',
                    'nombre' => 'Código File',
                    'fecha_subida' => optional($caso->updated_at)->format('Y-m-d H:i'),
                    'url' => $caso->codigo_file,
                    'caso_id' => $caso->id,
                    'caso' => $casoLabel,
                    'usuario' => null,
                    'extra' => null,
                ]);
            }

            if (!empty($caso->respaldo)) {
                $items->push([
                    'tipo' => 'Respaldo',
                    'nombre' => 'Respaldo',
                    'fecha_subida' => optional($caso->updated_at)->format('Y-m-d H:i'),
                    'url' => $caso->respaldo,
                    'caso_id' => $caso->id,
                    'caso' => $casoLabel,
                    'usuario' => null,
                    'extra' => null,
                ]);
            }
        }

        // 4) Ordenar por fecha (desc)
        $items = $items->sortByDesc(function ($x) {
            return $x['fecha_subida'] ?? '0000-00-00 00:00';
        })->values();

        // (Opcional) agrupado por caso
        $grouped = $items->groupBy('caso_id')->map(function ($rows) {
            return $rows->values();
        });

        return response()->json([
            'ci' => $ci,
            'casos_encontrados' => $casos->count(),
            'casos' => $casosInfo,
            'data' => $items,
            'data_grouped' => $grouped, // si no lo quieres, elimínalo
        ]);
    }



    function uploadCodigoFile(Request $request, Caso $caso){
        $request->validate([
            'file' => ['required', 'file', 'max:51200'], // 50 MB
        ]);

        $file = $request->file('file');
        $userId = $request->user()->id;
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = "caso_codigo/user_{$userId}/" . $fileName;
        Storage::disk('public')->put($filePath, file_get_contents($file));
        $url = Storage::url($filePath);
        $caso ->codigo_file = $url;
        $caso ->save();

        return response()->json(['url' => $url], 201);
    }
    function uploadRespaldo(Request $request, Caso $caso){
        $request->validate([
            'file' => ['required', 'file', 'max:51200'], // 50 MB
        ]);

        $file = $request->file('file');
        $userId = $request->user()->id;
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = "caso_respaldo/user_{$userId}/" . $fileName;
        Storage::disk('public')->put($filePath, file_get_contents($file));
        $url = Storage::url($filePath);
        $caso ->respaldo = $url;
        $caso ->save();

        return response()->json(['url' => $url], 201);
    }
    function aceptarLegal(Request $request, Caso $caso){
        $caso->fecha_aceptacion_area_legal = date('Y-m-d H:i:s');
        $caso->save();
        return response()->json($caso);
    }
    function aceptarSocial(Request $request, Caso $caso){
        $caso->fecha_aceptacion_area_social = date('Y-m-d H:i:s');
        $caso->save();
        return response()->json($caso);
    }
    function aceptarPsicologico(Request $request, Caso $caso){
        $caso->fecha_aceptacion_area_psicologica = date('Y-m-d H:i:s');
        $caso->save();
        return response()->json($caso);
    }

    function uploadFile(Request $request)
    {

        $request->validate([
            'titulo' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:51200'], // 50 MB
            'case_id' => ['required', 'integer', 'exists:casos,id'],
            'tipo' => ['required'],
        ]);

        $file = $request->file('file');
        $caseId = $request->input('case_id');
        $tipo = $request->input('tipo');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = "caso/{$caseId}/{$tipo}/" . $fileName;
        Storage::disk('public')->put($filePath, file_get_contents($file));
        $url = Storage::url($filePath);
        if ($tipo == 'psicologico') {
            $informe = Psicologica::create([
                'caseable_id' => $caseId,
                'caseable_type' => Caso::class,
                'user_id' => $request->user()->id,
                'titulo' => $request->string('titulo')->toString() ?: $file->getClientOriginalName(),
                'contenido_html' => "<p>Archivo adjunto: <a href=\"{$url}\" target=\"_blank\" rel=\"noopener\">{$file->getClientOriginalName()}</a></p>",
                'archivo' => $url,
            ]);
        } elseif ($tipo == 'social') {
            $informe = InformesSocial::create([
                'caseable_id' => $caseId,
                'fecha' => date('Y-m-d'),
                'caseable_type' => Caso::class,
                'user_id' => $request->user()->id,
                'titulo' => $request->string('titulo')->toString() ?: $file->getClientOriginalName(),
                'contenido_html' => "<p>Archivo adjunto: <a href=\"{$url}\" target=\"_blank\" rel=\"noopener\">{$file->getClientOriginalName()}</a></p>",
                'archivo' => $url,
            ]);
        } elseif ($tipo == 'legal') {
            $informe = InformeLegal::create([
                'caseable_id' => $caseId,
                'caseable_type' => Caso::class,
                'user_id' => $request->user()->id,
                'titulo' => $request->string('titulo')->toString() ?: $file->getClientOriginalName(),
                'contenido_html' => "<p>Archivo adjunto: <a href=\"{$url}\" target=\"_blank\" rel=\"noopener\">{$file->getClientOriginalName()}</a></p>",
                'archivo' => $url,
            ]);
        } else {
            return response()->json(['message' => 'Tipo no válido'], 400);
        }
        return response()->json($informe->load('user:id,name,username'), 201);
    }

    function destroy(Caso $caso)
    {
        $caso->delete();
        return response()->json(['message' => 'Caso eliminado']);
    }

    public function updateCodigos(\Illuminate\Http\Request $request, \App\Models\Caso $caso)
    {
        // Actualiza SOLO si vienen en el payload; si no vienen, no toca ese campo
        if ($request->has('nurej')) {
            $caso->nurej = $request->input('nurej');
        }
        if ($request->has('cud')) {
            $caso->cud = $request->input('cud');
        }
//        'numero_juzgado',
//        'responsable_fiscalia',
        if ($request->has('numero_juzgado')) {
            $caso->numero_juzgado = $request->input('numero_juzgado');
        }
        if ($request->has('responsable_fiscalia')) {
            $caso->responsable_fiscalia = $request->input('responsable_fiscalia');
        }
//        numero_juzgado_padre
        if ($request->has('numero_juzgado_padre')) {
            $caso->numero_juzgado_padre = $request->input('numero_juzgado_padre');
        }

        $caso->save();

        return response()->json([
            'message' => 'Códigos actualizados',
            'caso' => $caso->only(['id', 'nurej', 'cud', 'numero_juzgado', 'responsable_fiscalia'])
        ]);
    }

    public function reportesResumen(Request $request)
    {
        $start = $request->query('start'); // YYYY-MM-DD
        $end = $request->query('end');   // YYYY-MM-DD
        $tipo = $request->query('tipo');  // SLIM|DNA|SLAM|UMADIS|PROPREMIS
        $area = $request->query('area');
        $zona = $request->query('zona');

        // UNA sola expresión de fecha:
        // MySQL/MariaDB: DATE(COALESCE(fecha_apertura_caso, created_at))
        // Postgres      : COALESCE(fecha_apertura_caso::date, created_at::date)
        $dateSql = "DATE(COALESCE(fecha_apertura_caso, created_at))";

        $q = \App\Models\Caso::query();

        // Filtro por rango
        if ($start && $end) {
            $q->whereRaw("$dateSql BETWEEN ? AND ?", [$start, $end]);
        } elseif ($start) {
            $q->whereRaw("$dateSql >= ?", [$start]);
        } elseif ($end) {
            $q->whereRaw("$dateSql <= ?", [$end]);
        }

        if ($tipo) $q->where('tipo', $tipo);
        if ($area) $q->where('area', $area);
        if ($zona) $q->where('zona', $zona);

        // Filtro por rol (como ya hacías)
        if ($u = $request->user()) {
            if ($u->role === 'Psicologo') $q->where('psicologica_user_id', $u->id);
            if ($u->role === 'Social') $q->where('trabajo_social_user_id', $u->id);
            if ($u->role === 'Abogado') $q->where('legal_user_id', $u->id);
        }

        $base = (clone $q);
        $total = (clone $base)->count();

        $byTipo = (clone $base)
            ->select('tipo', DB::raw('COUNT(*) AS total'))
            ->groupBy('tipo')->orderByDesc('total')->get();

        $byTipologia = (clone $base)
            ->select('caso_tipologia', DB::raw('COUNT(*) AS total'))
            ->groupBy('caso_tipologia')->orderByDesc('total')->get();

        $byModalidad = (clone $base)
            ->select('caso_modalidad', DB::raw('COUNT(*) AS total'))
            ->groupBy('caso_modalidad')->orderByDesc('total')->get();

        $byDia = (clone $base)
            ->selectRaw("$dateSql AS dia, COUNT(*) AS total")
            ->groupBy('dia')->orderBy('dia')->get();

        $byZona = (clone $base)
            ->select('caso_zona', DB::raw('COUNT(*) AS total'))
            ->groupBy('caso_zona')->orderByDesc('total')->limit(10)->get();

        $fmt = function ($rows) {
            $labels = [];
            $series = [];
            foreach ($rows as $r) {
                if (isset($r->tipo)) {
                    $label = $r->tipo;
                } elseif (isset($r->caso_tipologia)) {
                    $label = $r->caso_tipologia;
                } elseif (isset($r->caso_modalidad)) {
                    $label = $r->caso_modalidad;
                } elseif (isset($r->caso_zona)) {
                    $label = $r->caso_zona;
                } else {
                    $label = '—';
                }

                $labels[] = $label ?: '—';
                $series[] = (int)($r->total ?? 0);
            }
            return ['labels' => $labels, 'series' => $series];
        };

        return response()->json([
            'total' => $total,
            'tipo' => $fmt($byTipo),
            'tipologia' => $fmt($byTipologia),
            'modalidad' => $fmt($byModalidad),
            'zona_top10' => $fmt($byZona),
            'diario' => $byDia, // [{dia:'YYYY-MM-DD', total: N}]
        ]);
    }


    public function lineaTiempo(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $perPage = (int)$request->get('per_page', 10);
        $perPage = max(5, min($perPage, 100));
        // SLA configurable; por defecto 10 días exactos desde la apertura/creación
        $deadlineDays = (int)$request->get('deadline_days', 10);

        $query = Caso::orderByDesc('created_at');

        if ($q !== '') {
            $query->where(function ($s) use ($q) {
                $like = "%{$q}%";
                $s->orWhere('caso_numero', 'like', $like)
                    ->orWhere('caso_tipologia', 'like', $like)
                    ->orWhere('caso_zona', 'like', $like)
                    ->orWhere('caso_direccion', 'like', $like)
                    ->orWhere('caso_descripcion', 'like', $like);
            });

            $query->orWhereHas('denunciantes', function ($s) use ($q) {
                $like = "%{$q}%";
                $s->where(DB::raw("CONCAT_WS(' ', denunciante_nombres, denunciante_paterno, denunciante_materno)"), 'like', $like)
                    ->orWhere('denunciante_nro', 'like', $like);
            });

            $query->orWhereHas('denunciados', function ($s) use ($q) {
                $like = "%{$q}%";
                $s->where(DB::raw("CONCAT_WS(' ', denunciado_nombres, denunciado_paterno, denunciado_materno)"), 'like', $like);
            });
        }

        // Filtro por rol asignado (si aplica)
        $user = $request->user();
        if ($user?->role === 'Psicologo') {
            $query->where('psicologica_user_id', $user->id);
        }
        if ($user?->role === 'Social') {
            $query->where('trabajo_social_user_id', $user->id);
        }
        if ($user?->role === 'Abogado') {
            $query->where('legal_user_id', $user->id);
        }
        if ($user?->role === 'Auxiliar' || $user?->role === 'Asistente') {
            $query->where('area', $user->area)
                    ->where('zona', $user->zona);
        }

        // Relaciones mínimas necesarias
        $query->with([
            'user:id,name',
            'denunciantes:id,denunciante_nombres,denunciante_paterno,denunciante_materno,denunciante_nro,caso_id',
            'psicologicas:id,caseable_id,caseable_type,fecha,created_at',
            'informesSociales:id,caseable_id,caseable_type,fecha,created_at',
            'informesLegales:id,caseable_id,caseable_type,fecha,created_at',
        ]);

        $paginated = $query->paginate($perPage)->appends($request->query());
        $now = Carbon::now();

        // === Helper estado por área (corrige días restantes/vencidos) ===
        $buildArea = function ($collection, Carbon $baseC, int $deadlineDays) use ($now) {

            // Fecha límite exacta: base + N días (solo fecha, sin sorpresas por horas)
            $baseDateOnly = Carbon::create($baseC->year, $baseC->month, $baseC->day, 0, 0, 0);
            $deadline = $baseDateOnly->copy()->addDays($deadlineDays);

            if (!$collection || $collection->isEmpty()) {
                $diasRest = $baseDateOnly->diffInDays($now, false); // negativo si ya pasó

                return [
                    'entregado' => false,
                    'creado_dmY' => $baseDateOnly->format('d/m/Y'),
                    'deadline_dmY' => $deadline->format('d/m/Y'),
                    'dias_restantes' => round($diasRest),
                    'vencido' => $diasRest < 0,
                ];
            }

            // Primera entrega por 'fecha' (fallback a created_at)
            $first = $collection->sortBy(function ($x) {
                return $x->fecha ?: $x->created_at;
            })->first();

            $f = $first->fecha ?: $first->created_at;
            $entrega = Carbon::parse($f);
            // Diferencia exacta en días entre base y entrega
            $diasHastaEntrega = $baseDateOnly->diffInDays(Carbon::create(
                $entrega->year, $entrega->month, $entrega->day, 0, 0, 0
            ));

            return [
                'entregado' => true,
                'entrego_dmY' => $entrega->format('d/m/Y'),
                'dias_hasta_entrega' => $diasHastaEntrega,
            ];
        };

        $rows = collect($paginated->items())->map(function (Caso $c) use ($now, $buildArea, $deadlineDays) {
            $den = optional($c->denunciantes->first());
            $denName = trim(collect([
                $den?->denunciante_nombres, $den?->denunciante_paterno, $den?->denunciante_materno
            ])->filter()->implode(' '));

            // Base = fecha_apertura_caso o created_at
            $base = $c->fecha_apertura_caso ?: $c->created_at;
            $baseC = $base ? Carbon::parse($base) : $now;

            return [
                'id' => $c->id,
                'tipo' => $c->tipo,
                'caso_numero' => $c->caso_numero,
                'caso_zona' => $c->caso_zona ?: $c->zona,
                'creado_dmY' => $baseC->format('d/m/Y'),
                'base_user' => $c->user?->name,

                'denunciante_nombre' => $denName ?: null,
                'denunciante_nro' => $den?->denunciante_nro,

                'psico' => $buildArea($c->psicologicas, $baseC, $deadlineDays),
                'social' => $buildArea($c->informesSociales, $baseC, $deadlineDays),
                'legal' => $buildArea($c->informesLegales, $baseC, $deadlineDays),
            ];
        });

        return response()->json([
            'data' => $rows,
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
        ]);
    }

    public function fotoStore(Request $request, Caso $caso)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'], // 10 MB
            'titulo' => ['required', 'string', 'max:255'], // <- AHORA requerido
            'descripcion' => ['nullable', 'string'],
        ]);

        $disk = 'public';
        $dir = "caso/{$caso->id}/fotos";

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $nameNoExt = pathinfo($originalName, PATHINFO_FILENAME);
        $extLower = strtolower($file->getClientOriginalExtension() ?: 'jpg');

        // Nombre base único
        $base = Str::slug($nameNoExt) . '-' . Str::random(6);

        $manager = new ImageManager(new Driver());

        // ==== Imagen principal (máx 2000px) ====
        $img = $manager->read($file->getPathname());
        $img->resizeDown(width: 2000, height: 2000);

        // Normalizamos extensión de salida
        $storedExt = in_array($extLower, ['jpg', 'jpeg', 'png', 'webp']) ? ($extLower === 'jpeg' ? 'jpg' : $extLower) : 'jpg';
        $storedName = "{$base}.{$storedExt}";
        $path = "{$dir}/{$storedName}";

        if ($storedExt === 'jpg') {
            Storage::disk($disk)->put($path, (string)$img->toJpeg(quality: 85));
            $mime = 'image/jpeg';
        } elseif ($storedExt === 'png') {
            Storage::disk($disk)->put($path, (string)$img->toPng());
            $mime = 'image/png';
        } else { // webp
            Storage::disk($disk)->put($path, (string)$img->toWebp(quality: 80));
            $mime = 'image/webp';
        }

        // ==== Miniatura (máx 480px) ====
        $thumb = $manager->read($file->getPathname());
        $thumb->resizeDown(width: 480, height: 480);

        $thumbName = "{$base}-thumb.{$storedExt}";
        $thumbPath = "{$dir}/{$thumbName}";

        if ($storedExt === 'jpg') {
            Storage::disk($disk)->put($thumbPath, (string)$thumb->toJpeg(quality: 80));
        } elseif ($storedExt === 'png') {
            Storage::disk($disk)->put($thumbPath, (string)$thumb->toPng());
        } else {
            Storage::disk($disk)->put($thumbPath, (string)$thumb->toWebp(quality: 75));
        }

        // URLs públicas relativas (/storage/...)
        $url = Storage::url($path);
        $thumbUrl = Storage::url($thumbPath);

        $bytes = Storage::disk($disk)->size($path);
        $width = $img->width();
        $height = $img->height();

        $foto = Fotografia::create([
            'caseable_id'   => $caso->id,
            'caseable_type' => Caso::class,
            'user_id'       => $request->user()->id,

            // usa el título que viene del front
            'titulo'        => $request->string('titulo')->toString(),
            'descripcion'   => $request->get('descripcion'),

            'original_name' => $originalName,
            'stored_name'   => $storedName,
            'extension'     => $storedExt,
            'mime'          => $mime,
            'size_bytes'    => $bytes,

            'disk'       => $disk,
            'path'       => $path,
            'url'        => $url,
            'thumb_path' => $thumbPath,
            'thumb_url'  => $thumbUrl,

            'width'  => $width,
            'height' => $height,
        ]);

        return response()->json($foto->load('user:id,name,username'), 201);
    }


    /** DELETE /api/slims/fotografias/{fotografia} */
    public function fotoDestroy(Fotografia $fotografia)
    {
        // Asegura que la foto pertenece a un SLIM (polimórfico)
        if ($fotografia->caseable_type !== Caso::class) abort(404);

        try {
            if ($fotografia->path && Storage::disk($fotografia->disk)->exists($fotografia->path)) {
                Storage::disk($fotografia->disk)->delete($fotografia->path);
            }
            if ($fotografia->thumb_path && Storage::disk($fotografia->disk)->exists($fotografia->thumb_path)) {
                Storage::disk($fotografia->disk)->delete($fotografia->thumb_path);
            }
        } catch (\Throwable $e) {
            // opcional: \Log::warning($e->getMessage());
        }

        $fotografia->delete();

        return response()->json(['message' => 'Fotografía eliminada']);
    }

    function socialPdf(Request $request, InformesSocial $informe)
    {
        // Opciones extra para mejor render
        $pdf = Pdf::loadView('informes_legales.pdf', [
            'informe' => $informe,
        ])->setPaper('A4', 'portrait');

        // Para acentos: usar DejaVu Sans
        $pdf->getDomPDF()->getOptions()->set('defaultFont', 'DejaVu Sans');

        // ?download=1 para descargar, 0 para ver en el navegador
        $download = (int)$request->query('download', 0) === 1;

        $filename = 'InformeLegal_' . $informe->id . '.pdf';
        return $download ? $pdf->download($filename) : $pdf->stream($filename);

    }

    function legalPdf(Request $request, InformeLegal $informe)
    {
        // Opciones extra para mejor render
        $pdf = Pdf::loadView('informes_legales.pdf', [
            'informe' => $informe,
        ])->setPaper('A4', 'portrait');

        // Para acentos: usar DejaVu Sans
        $pdf->getDomPDF()->getOptions()->set('defaultFont', 'DejaVu Sans');

        // ?download=1 para descargar, 0 para ver en el navegador
        $download = (int)$request->query('download', 0) === 1;

        $filename = 'InformeLegal_' . $informe->id . '.pdf';
        return $download ? $pdf->download($filename) : $pdf->stream($filename);

    }

    function psicoPdf(Request $request, Psicologica $psicologica)
    {
        // Opciones extra para mejor render
        $pdf = Pdf::loadView('psicologicas.pdf', [
            'sesion' => $psicologica,
        ])->setPaper('A4', 'portrait');

        // Para acentos: usar DejaVu Sans
        $pdf->getDomPDF()->getOptions()->set('defaultFont', 'DejaVu Sans');

        // ?download=1 para descargar, 0 para ver en el navegador
        $download = (int)$request->query('download', 0) === 1;

        $filename = 'SesionPsicologica_' . $psicologica->id . '.pdf';
        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }

    public function pendientesResumen(Request $request)
    {
        $user = $request->user();
        $q = Caso::query();

        switch ($user->role) {
            case 'Psicologo':
                $q->where('psicologica_user_id', $user->id)
                    ->whereDoesntHave('psicologicas'); // sin la primera sesión
                break;

            case 'Abogado':
                $q->where('legal_user_id', $user->id)
                    ->whereDoesntHave('informesLegales'); // sin el primer informe legal
                break;

            case 'Social':
                $q->where('trabajo_social_user_id', $user->id)
                    ->whereDoesntHave('informesSociales'); // sin el primer informe social
                break;

            default:
                return response()->json(['pendientes' => 0]);
        }

        return $q->get();
    }

    public function seguimiento(\Illuminate\Http\Request $request, \App\Models\Caso $caso)
    {
        // Cabecera compacta para el bloque superior
        $header = [
            'caso_id' => $caso->id,
            'caso_numero' => $caso->caso_numero,
            'tipologia' => $caso->caso_tipologia,
            'modalidad' => $caso->caso_modalidad,
            'fecha_hecho' => $caso->caso_fecha_hecho,
            'zona' => $caso->caso_zona ?: $caso->zona,
            'direccion' => $caso->caso_direccion ?: $caso->denunciante_domicilio_actual,
            'denunciante' => $caso->denunciante_nombre_completo,
            'denunciado' => $caso->denunciado_nombre_completo,
            'registrado_por' => optional($caso->user)->name,
            'fecha_registro' => optional($caso->created_at)?->format('Y-m-d H:i'),
        ];

        // Colecciones por tipo
        $informes = \App\Models\Informe::with('user')
            ->where('caso_id', $caso->id)
            ->get()
            ->map(function ($i) {
                return [
                    'uid' => 'INF-' . $i->id,
                    'id' => $i->id,
                    'fecha' => $i->fecha ? $i->fecha->format('Y-m-d') : optional($i->created_at)?->format('Y-m-d'),
                    'tipo' => 'Informe',
                    'modulo' => $i->area ?: 'General',
                    'titulo' => $i->titulo,
                    'descripcion' => strip_tags((string)$i->contenido_html),
                    'usuario' => optional($i->user)->name,
                    'origen' => 'SLIM',
                    // banderas para columnas estilo “visto/reserva/OJ/instrucción” (si las necesitas a futuro)
                    'visto' => false,
                    'reserva' => false,
                    'oj_enviado' => false,
                    'instruccion' => null,
                    'links' => [
                        'pdf' => url("/api/informes/{$i->id}/pdf"),
                    ],
                    'icon' => 'description',
                ];
            });

        $sesiones = \App\Models\SesionPsicologica::with('user')
            ->where('caso_id', $caso->id)
            ->get()
            ->map(function ($s) {
                return [
                    'uid' => 'SES-' . $s->id,
                    'id' => $s->id,
                    'fecha' => $s->fecha ? $s->fecha->format('Y-m-d') : optional($s->created_at)?->format('Y-m-d'),
                    'tipo' => 'Sesión',
                    'modulo' => $s->tipo ?: 'Psicológico',
                    'titulo' => $s->titulo,
                    'descripcion' => strip_tags((string)$s->contenido_html),
                    'usuario' => optional($s->user)->name,
                    'origen' => 'SLIM',
                    'visto' => false,
                    'reserva' => false,
                    'oj_enviado' => false,
                    'instruccion' => null,
                    'links' => [
                        'pdf' => url("/api/sesiones-psicologicas/{$s->id}/pdf"),
                    ],
                    'icon' => 'psychology',
                ];
            });

        $docs = \App\Models\Documento::with('user')
            ->where('caso_id', $caso->id)
            ->get()
            ->map(function ($d) {
                return [
                    'uid' => 'DOC-' . $d->id,
                    'id' => $d->id,
                    'fecha' => optional($d->created_at)?->format('Y-m-d'),
                    'tipo' => 'Documento',
                    'modulo' => $d->categoria ?: 'Documentos',
                    'titulo' => $d->titulo,
                    'descripcion' => (string)$d->descripcion,
                    'usuario' => optional($d->user)->name,
                    'origen' => 'SLIM',
                    'visto' => false,
                    'reserva' => false,
                    'oj_enviado' => false,
                    'instruccion' => null,
                    'links' => [
                        'view' => url("/api/documentos/{$d->id}/view"),
                        'download' => url("/api/documentos/{$d->id}/download"),
                    ],
                    'icon' => 'attach_file',
                ];
            });

        $fotos = \App\Models\Fotografia::with('user')
            ->where('caso_id', $caso->id)
            ->get()
            ->map(function ($f) {
                return [
                    'uid' => 'FOT-' . $f->id,
                    'id' => $f->id,
                    'fecha' => optional($f->created_at)?->format('Y-m-d'),
                    'tipo' => 'Fotografía',
                    'modulo' => 'Multimedia',
                    'titulo' => $f->titulo,
                    'descripcion' => (string)$f->descripcion,
                    'usuario' => optional($f->user)->name,
                    'origen' => 'SLIM',
                    'visto' => false,
                    'reserva' => false,
                    'oj_enviado' => false,
                    'instruccion' => null,
                    'links' => [
                        'open' => $f->url ?: $f->thumb_url,
                    ],
                    'icon' => 'image',
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
            'items' => $items,
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
            $denunciado = $caso->denunciados;
            $nombre = $denunciado->isNotEmpty() ? trim("{$denunciado->first()->denunciado_nombres} {$denunciado->first()->denunciado_paterno} {$denunciado->first()->denunciado_materno}") : '—';
            $lat = is_numeric($denunciado->first()->denunciado_latitud) ? (float)$denunciado->first()->denunciado_latitud : null;
            $lng = is_numeric($denunciado->first()->denunciado_longitud) ? (float)$denunciado->first()->denunciado_longitud : null;
            $telefono = $denunciado->first()->denunciado_telefono ?: ($denunciado->first()->denunciado_movil ?: $denunciado->first()->denunciado_fijo);
            $direccion = $denunciado->first()->denunciado_domicilio_actual ?: ($caso->caso_direccion ?: '—');
            $zona = $caso->caso_zona ?: $caso->zona;
            $tituloPersona = 'Denunciado';
        } else {
            // denunciante
            $denunciantes = $caso->denunciantes;
            $nombre = $denunciantes->isNotEmpty() ? trim("{$denunciantes->first()->denunciante_nombres} {$denunciantes->first()->denunciante_paterno} {$denunciantes->first()->denunciante_materno}") : '—';
            $lat = is_numeric($denunciantes->first()->latitud) ? (float)$denunciantes->first()->latitud : null;
            $lng = is_numeric($denunciantes->first()->longitud) ? (float)$denunciantes->first()->longitud : null;

            $telefono = $denunciantes->first()->denunciante_telefono ?: ($denunciantes->first()->denunciante_movil ?: $denunciantes->first()->denunciante_fijo);
            $direccion = $denunciantes->first()->denunciante_domicilio_actual ?: ($caso->caso_direccion ?: '—');
            $zona = $caso->caso_zona ?: $caso->zona;
            $tituloPersona = 'Denunciante';
        }

        // Fallback Oruro si no hay coords
        $LAT = $lat ?? -17.966700;
        $LNG = $lng ?? -67.116700;
        $HAS = is_numeric($lat) && is_numeric($lng);

        return view('casos.pdfHojaRuta', [
            'caso' => $caso,
            'tipo' => $tipo,
            'tituloPersona' => $tituloPersona,
            'LAT' => $LAT,
            'LNG' => $LNG,
            'HAS' => $HAS,
            'nombre' => $nombre ?: '—',
            'telefono' => $telefono ?: '—',
            'zona' => $zona ?: '—',
            'direccion' => $direccion ?: '—',
        ]);
    }

    public function pdf(Request $request, Caso $caso)
    {
        if ($caso->tipo == 'SLIM') {
            $caso = Caso::with(['denunciantes', 'denunciados', 'familiares', 'menores', 'psicologica_user:id,name', 'trabajo_social_user:id,name', 'legal_user:id,name', 'user:id,name'])->find($caso->id);
//            return response()->json($caso);
            $pdf = Pdf::loadView('casos.pdf', [
                'caso' => $caso,
            ])->setPaper('A4', 'portrait');

            // Para acentos: usar DejaVu Sans
            $pdf->getDomPDF()->getOptions()->set('defaultFont', 'DejaVu Sans');

            // ?download=1 para descargar, 0 para ver en el navegador
            $download = (int)$request->query('download', 0) === 1;

            $filename = 'Caso_' . $caso->id . '.pdf';
            return $download ? $pdf->download($filename) : $pdf->stream($filename);
        }
//        dna
        if ($caso->tipo == 'DNA') {
            $caso = Caso::with(['denunciantes', 'denunciados', 'familiares', 'menores', 'psicologica_user:id,name', 'trabajo_social_user:id,name', 'legal_user:id,name', 'user:id,name'])->find($caso->id);
            $pdf = Pdf::loadView('pdf.dna.pdf', [
                'caso' => $caso,
            ])->setPaper('A4', 'portrait');
            // Para acentos: usar DejaVu Sans
            $pdf->getDomPDF()->getOptions()->set('defaultFont', 'DejaVu Sans');
            // ?download=1 para descargar, 0 para ver en el navegador
            $download = (int)$request->query('download', 0) === 1;
            $filename = 'DNA_Caso_' . $caso->id . '.pdf';
            return $download ? $pdf->download($filename) : $pdf->stream($filename);
        }
        if ($caso->tipo == 'SLAM') {
            $caso = Caso::with(['denunciantes', 'denunciados', 'familiares', 'menores', 'psicologica_user:id,name', 'trabajo_social_user:id,name', 'legal_user:id,name', 'user:id,name'])->find($caso->id);
            $pdf = Pdf::loadView('pdf.slam.pdf', [
                'caso' => $caso,
            ])->setPaper('A4', 'portrait');
            // Para acentos: usar DejaVu Sans
            $pdf->getDomPDF()->getOptions()->set('defaultFont', 'DejaVu Sans');
            // ?download=1 para descargar, 0 para ver en el navegador
            $download = (int)$request->query('download', 0) === 1;
            $filename = 'DNA_Caso_' . $caso->id . '.pdf';
            return $download ? $pdf->download($filename) : $pdf->stream($filename);
        }
//        umadis
//        Propremis
        if ($caso->tipo == 'UMADIS') {
            $caso = Caso::with(['denunciantes', 'denunciados', 'familiares', 'menores', 'psicologica_user:id,name', 'trabajo_social_user:id,name', 'legal_user:id,name', 'user:id,name'])->find($caso->id);
            $pdf = Pdf::loadView('pdf.umadi.pdf', [
                'caso' => $caso,
            ])->setPaper('A4', 'portrait');
            // Para acentos: usar DejaVu Sans
            $pdf->getDomPDF()->getOptions()->set('defaultFont', 'DejaVu Sans');
            // ?download=1 para descargar, 0 para ver en el navegador
            $download = (int)$request->query('download', 0) === 1;
            $filename = 'UMADIS_Caso_' . $caso->id . '.pdf';
            return $download ? $pdf->download($filename) : $pdf->stream($filename);
        }

        if ($caso->tipo == 'PROPREMI') {
            $caso = Caso::with(['denunciantes', 'denunciados', 'familiares', 'menores', 'psicologica_user:id,name', 'trabajo_social_user:id,name', 'legal_user:id,name', 'user:id,name'])->find($caso->id);
            $pdf = Pdf::loadView('pdf.propremi.pdf', [
                'caso' => $caso,
            ])->setPaper('A4', 'portrait');
            // Para acentos: usar DejaVu Sans
            $pdf->getDomPDF()->getOptions()->set('defaultFont', 'DejaVu Sans');
            // ?download=1 para descargar, 0 para ver en el navegador
            $download = (int)$request->query('download', 0) === 1;
            $filename = 'PROPREMIS_Caso_' . $caso->id . '.pdf';
            return $download ? $pdf->download($filename) : $pdf->stream($filename);
        }
    }

    /**
     * GET /casos?q=texto&page=1&per_page=10
     * Devuelve paginado con filtro por texto libre.
     */
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $tipologia = trim((string)$request->get('tipologia', ''));
        $perPage = (int)$request->get('per_page', 10);
        $perPage = max(5, min($perPage, 100));
        $tipo = trim((string)$request->get('tipo', ''));

        $query = Caso::query()
            ->orderByDesc('created_at')
            ->when($tipo !== '', fn($qq) => $qq->where('tipo', $tipo));

        if ($tipologia !== '') {
            $query->where('caso_tipologia', $tipologia);
        }

        if ($q !== '') {
            $like = "%{$q}%";
//            error_log("Buscando casos con texto: {$q}");

            $query->where(function ($group) use ($like) {
                // Campos directos del caso
                $group->where(function ($s) use ($like) {
                    $s->where('caso_numero', 'like', $like)
                        ->orWhere('caso_tipologia', 'like', $like)
                        ->orWhere('caso_zona', 'like', $like)
                        ->orWhere('caso_direccion', 'like', $like)
                        ->orWhere('caso_descripcion', 'like', $like)
                        ->orWhere('zona', 'like', $like);
                })
                    // Denunciantes
                    ->orWhereHas('denunciantes', function ($s) use ($like) {
                        $s->whereRaw("CONCAT_WS(' ', denunciante_nombres, denunciante_paterno, denunciante_materno) LIKE ?", [$like])
                            ->orWhere('denunciante_nro', 'like', $like)
                            ->orWhere('denunciante_documento', 'like', $like)
                            ->orWhere('denunciante_residencia', 'like', $like);
                    })
                    // Denunciados
                    ->orWhereHas('denunciados', function ($s) use ($like) {
                        $s->whereRaw("CONCAT_WS(' ', denunciado_nombres, denunciado_paterno, denunciado_materno) LIKE ?", [$like])
                            ->orWhere('denunciado_nro', 'like', $like)
                            ->orWhere('denunciado_documento', 'like', $like)
                            ->orWhere('denunciado_residencia', 'like', $like);
                    })->orWhereHas('victimas', function ($s) use ($like) {
                        $s->where('ci', 'like', $like)
                            ->orWhere('nombres_apellidos', 'like', $like);
                    });
            });
        }

        $user = $request->user();

        // Filtros por rol (se mantienen como AND porque el bloque de búsqueda ya está agrupado)
        if ($user->role === 'Psicologo') {
            $query->where('psicologica_user_id', $user->id);
        }
        if ($user->role === 'Social') {
            $query->where('trabajo_social_user_id', $user->id);
        }
        if ($user->role === 'Abogado') {
            $query->where('legal_user_id', $user->id);
        }
        if ($user->role === 'Asistente') {
            $query->where('zona', $user->zona);
        }
        if ($user->role === 'Auxiliar') {
            $query->where('zona', $user->zona);
        }

        $query->with([
            'psicologica_user:id,name',
            'trabajo_social_user:id,name',
            'legal_user:id,name',
            'user:id,name',
            'denunciantes',
            'denunciados',
            'victimas',
            'acogimientos'
        ]);

        $paginated = $query->paginate($perPage)->appends($request->query());

        // === lo demás de tu lógica (mi_estado) se queda igual ===
        $items = $paginated->getCollection();
        $now = \Carbon\Carbon::now();

        $items->transform(function ($c) use ($user, $now) {
            $rolUsuario = $user->role;

            $meAsignado = (
                ($rolUsuario === 'Psicologo' && (int)$c->psicologica_user_id === (int)$user->id) ||
                ($rolUsuario === 'Social' && (int)$c->trabajo_social_user_id === (int)$user->id) ||
                ($rolUsuario === 'Abogado' && (int)$c->legal_user_id === (int)$user->id)
            );

            $apertura = $c->fecha_apertura_caso
                ? \Carbon\Carbon::parse($c->fecha_apertura_caso)
                : ($c->created_at ? \Carbon\Carbon::parse($c->created_at) : $now);

            $deadline = $apertura->copy()->addDays(10);
            $diasRest = $now->diffInDays($deadline, false);
            $atrasado = $diasRest < 0;

            $hecho = false;
            $labelListo = 'Primer informe listo';

            if ($meAsignado) {
                switch ($rolUsuario) {
                    case 'Psicologo':
                        $hecho = \App\Models\SesionPsicologica::where('caso_id', $c->id)->exists();
                        $labelListo = 'Primera sesión lista';
                        break;
                    case 'Abogado':
                        $hecho = \App\Models\Informe::where('caso_id', $c->id)->where('area', 'Legal')->exists();
                        $labelListo = 'Primer informe (Legal) listo';
                        break;
                    case 'Social':
                        $hecho = \App\Models\Informe::where('caso_id', $c->id)->where('area', 'Social')->exists();
                        $labelListo = 'Primer informe (Social) listo';
                        break;
                }
            }

            $c->setAttribute('mi_estado', [
                'me_asignado' => $meAsignado,
                'rol' => $meAsignado ? $rolUsuario : null,
                'primer_informe_hecho' => $hecho,
                'label_listo' => $labelListo,
                'deadline' => $deadline->format('Y-m-d'),
                'dias_restantes' => (int)$diasRest,
                'atrasado' => $atrasado,
            ]);

            return $c;
        });

        return response()->json([
            'data' => $items->values(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $request['caso_numero'] = $this->numeroCaso($request->tipo);
            $request['user_id'] = $user->id;
            $request['fecha_apertura_caso'] = date('Y-m-d H:i:s');
            $request['area'] = $user->area;
            $request['estado_caso'] = "Apertura Denuncia";
            $request['zona'] = $user->zona;
            if ($request->has('legal_user_id') && $request->legal_user_id) {
                $request['fecha_derivacion_area_legal'] = date('Y-m-d H:i:s');
            }
            if ($request->has('psicologica_user_id') && $request->psicologica_user_id) {
                $request['fecha_derivacion_area_psicologica'] = date('Y-m-d H:i:s');
            }
            if ($request->has('trabajo_social_user_id') && $request->trabajo_social_user_id) {
                $request['fecha_derivacion_area_social'] = date('Y-m-d H:i:s');
            }
            $caso = Caso::create($request->all());

//            $caso->asynccon denunciante
            if ($request->has('denunciantes')) {
                foreach ($request->denunciantes as $denunciante) {
                    $caso->denunciantes()->create($denunciante);
                }
            }
            if ($request->has('denunciados')) {
                foreach ($request->denunciados as $denunciado) {
                    $caso->denunciados()->create($denunciado);
                }
            }
            if ($request->has('familiares')) {
                foreach ($request->familiares as $familiar) {
                    $caso->familiares()->create($familiar);
                }
            }
            if ($request->has('menores')) {
                foreach ($request->menores as $menor) {
                    $caso->menores()->create($menor);
                }
            }
            if ($request->has('victimas')) {
                foreach ($request->victimas as $victima) {
                    $caso->victimas()->create($victima);
                }
            }


            DB::commit();
            return $caso;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al crear el caso', 'error' => $e->getMessage()], 500);
        }
    }

    function numeroCaso($tipo)
    {
        $year = date('Y');
        $count = Caso::where('tipo', $tipo)->whereYear('created_at', $year)->count() + 1;
        $numero = str_pad($count, 3, '0', STR_PAD_LEFT) . '/' . substr($year, -2);
        return $numero;
    }

    public function show(Caso $caso)
    {
        $caso = Caso::with(
            [
                'psicologica_user:id,name,celular',
                'trabajo_social_user:id,name,celular',
                'legal_user:id,name,celular',
                'user:id,name,celular',
                'denunciantes',
                'menores',
                'denunciados',
                'familiares',
                'psicologicas.user:id,name,role',
                'informesLegales.user:id,name,role',
                'documentos.user:id,name,role',
                'fotografias.user:id,name,role',
                'informesSociales.user:id,name,role',
                'victimas'
            ]
        )
            ->find($caso->id);
        return response()->json($caso);
    }

    public function update(Request $request, Caso $caso)
    {

//        unset($request['fecha_apertura_caso']);
//        unset($request['caso_numero']);
//        unset($request['user_id']);
//        unset($request['area']);
//        unset($request['zona']);
//        unset($request['tipo']);
        $request->request->remove('fecha_apertura_caso');
        $request->request->remove('caso_numero');
        $request->request->remove('user_id');
        $request->request->remove('area');
        $request->request->remove('zona');
        $request->request->remove('tipo');
        DB::beginTransaction();
        try {
            if ($request->has('legal_user_id') && $request->legal_user_id && !$caso->fecha_derivacion_area_legal) {
                $request['fecha_derivacion_area_legal'] = date('Y-m-d H:i:s');
            }

            if ($request->has('psicologica_user_id') && $request->psicologica_user_id && !$caso->fecha_derivacion_area_psicologica) {
                $request['fecha_derivacion_area_psicologica'] = date('Y-m-d H:i:s');
            }

            if ($request->has('trabajo_social_user_id') && $request->trabajo_social_user_id && !$caso->fecha_derivacion_area_social) {
                $request['fecha_derivacion_area_social'] = date('Y-m-d H:i:s');
            }

            $caso->update($request->all());

            if ($request->has('familiares')) {
                $caso->familiares()->delete();
                foreach ($request->familiares as $familiar) {
                    $caso->familiares()->create($familiar);
                }
            }
            if ($request->has('denunciantes')) {
                $caso->denunciantes()->delete();
                foreach ($request->denunciantes as $denunciante) {
                    $caso->denunciantes()->create($denunciante);
                }
            }
//            error_log(json_encode($caso->denunciados));
            if ($request->has('denunciados')) {
                $caso->denunciados()->delete();
                foreach ($request->denunciados as $denunciado) {
                    $caso->denunciados()->create($denunciado);
                }
            }
            if ($request->has('menores')) {
                $caso->menores()->delete();
                foreach ($request->menores as $menor) {
                    $caso->menores()->create($menor);
                }
            }
            if ($request->has('victimas')) {
                $caso->victimas()->delete();
                foreach ($request->victimas as $victima) {
                    $caso->victimas()->create($victima);
                }
            }
            DB::commit();
            return Caso::with(['denunciantes', 'denunciados', 'familiares'])->find($caso->id);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el caso', 'error' => $e->getMessage()], 500);
        }
    }

    function psicoStore(Request $request, Caso $caso)
    {
        $user = $request->user();

//        actulizar caso [sgicologica/] fecha_derivacion_psicologica
        if (!$caso->fecha_derivacion_psicologica) {
            $caso->fecha_derivacion_psicologica = date('Y-m-d');
            $caso->save();
        }

        $request['caso_id'] = $caso->id;
        $request['user_id'] = $user->id;
        $request['caseable_type'] = Caso::class;
        $request['caseable_id'] = $caso->id;
        $sesion = Psicologica::create($request->all());
        return $sesion;
    }

    function psicoUpdate(Request $request, Psicologica $psicologica)
    {
        $psicologica->update($request->all());
        return $psicologica;
    }

    function psicoDestroy(Psicologica $psicologica)
    {
        $psicologica->delete();
        return response()->json(['message' => 'Sesión psicológica eliminada']);
    }
//Route::post('/casos/{caso}/informes-legales', [\App\Http\Controllers\CasoController::class, 'legalStore']);
//Route::put ('/informes-legales/{informe}',     [\App\Http\Controllers\CasoController::class, 'legalUpdate']);
//Route::delete('/informes-legales/{informe}', [\App\Http\Controllers\CasoController::class, 'legalDestroy']);
    function legalStore(Request $request, Caso $caso)
    {
        $user = $request->user();
        //        actulizar caso [sgicologica/] fecha_derivacion_psicologica
//        if (!$caso->fecha_derivacion_legal) {
//            $caso->fecha_derivacion_area_legal = date('Y-m-d');
//            $caso->save();
//        }
        $request['caso_id'] = $caso->id;
        $request['user_id'] = $user->id;
        $request['caseable_type'] = Caso::class;
        $request['caseable_id'] = $caso->id;
        $informe = InformeLegal::create($request->all());
        return $informe;
    }

    function legalUpdate(Request $request, InformeLegal $informe)
    {
        $informe->update($request->all());
        return $informe;
    }

    function legalDestroy(InformeLegal $informe)
    {
        $informe->delete();
        return response()->json(['message' => 'Informe legal eliminado']);
    }
//Route::post('/casos/{caso}/informes-sociales', [\App\Http\Controllers\CasoController::class, 'socialStore']);
//Route::put ('/informes-sociales/{informe}',     [\App\Http\Controllers\CasoController::class, 'socialUpdate']);
//Route::delete('/informes-sociales/{informe}', [\App\Http\Controllers\CasoController::class, 'socialDestroy']);
    function socialStore(Request $request, Caso $caso)
    {
        $user = $request->user();
        //        actulizar caso [sgicologica/] fecha_derivacion_psicologica
        if (!$caso->fecha_derivacion_social) {
            $caso->fecha_informe_trabajo_social = date('Y-m-d');
            $caso->save();
        }
        $request['caso_id'] = $caso->id;
        $request['user_id'] = $user->id;
        $request['caseable_type'] = Caso::class;
        $request['caseable_id'] = $caso->id;
        $informe = InformesSocial::create($request->all());
        return $informe;
    }

    function socialUpdate(Request $request, InformesSocial $informe)
    {
        $informe->update($request->all());
        return $informe;
    }

    function socialDestroy(InformesSocial $informe)
    {
        $informe->delete();
        return response()->json(['message' => 'Informe social eliminado']);
    }

    function docStore(Request $request, Caso $caso)
    {
        $payload = [
            'caseable_id' => $caso->id,
            'caseable_type' => Caso::class,
            'user_id' => $request->user()?->id,
            'titulo' => $request['titulo'] ?? null,
            'categoria' => $request['categoria'] ?? null,
            'descripcion' => $request['descripcion'] ?? null,
        ];
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());
            $storedName = Str::uuid()->toString() . '.' . $ext;
            $path = $file->storeAs("caso/{$caso->id}/documentos", $storedName, 'public');
            $url = Storage::disk('public')->url($path);

            $payload += [
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'extension' => $ext,
                'mime' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
                'disk' => 'public',
                'path' => $path,
                'url' => $url,
            ];
        }
        $documento = Documento::create($payload);
        return response()->json(['documento' => $documento], 201);
        return $documento;
    }

    function docDownload($doc)
    {
        $doc = Documento::where('id', $doc)->first();
        if ($doc && $doc->path && Storage::disk($doc->disk)->exists($doc->path)) {
            return Storage::disk($doc->disk)->download($doc->path, $doc->original_name);
        } else {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }
    }

    function docView($doc)
    {
        $doc = Documento::where('id', $doc)->first();
        if ($doc && $doc->path && Storage::disk($doc->disk)->exists($doc->path)) {
            $fileContent = Storage::disk($doc->disk)->get($doc->path);
            $mimeType = $doc->mime ?? 'application/octet-stream';
            return response($fileContent, 200)->header('Content-Type', $mimeType);
        } else {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }
    }

    function docUpdate(Request $request, $doc)
    {
        $data = $request->only(['titulo', 'categoria', 'descripcion']);
        $doc = Documento::where('id', $doc)->first();
        if ($doc) {
            $doc->update($data);
            return response()->json(['documento' => $doc]);
        } else {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }
    }

    function docDestroy($doc)
    {
        $doc = Documento::where('id', $doc)->first();
        if ($doc) {
            // Elimina el archivo físico si existe
            if ($doc->path && Storage::disk($doc->disk)->exists($doc->path)) {
                Storage::disk($doc->disk)->delete($doc->path);
            }
            $doc->delete();
            return response()->json(['message' => 'Documento eliminado']);
        } else {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }
    }

}
