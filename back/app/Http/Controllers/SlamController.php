<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Fotografia;
use App\Models\InformeLegal;
use App\Models\Problematica;
use App\Models\Psicologica;
use App\Models\Slam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SlamController extends Controller
{
    function psicoPdf($psico)
    {
        $psico = Psicologica::where('id', $psico)->with(['user:id,name'])->first();
        if ($psico) {
            $html = view('pdf.slam.psicologica', ['sesion' => $psico])->render();
            $pdf = app('dompdf.wrapper');
            $pdf->loadHTML($html);
            return $pdf->stream("Psicológica_{$psico->id}.pdf");
        } else {
            return response()->json(['message' => 'Psicológica no encontrada'], 404);
        }
    }
    function psicoDestroy($psico)
    {
        $psico = Psicologica::where('id', $psico)->first();
        if ($psico) {
            $psico->delete();
            return response()->json(['message' => 'Psicológica eliminada']);
        } else {
            return response()->json(['message' => 'Psicológica no encontrada'], 404);
        }
    }
    function psicoUpdate(Request $request, $psico)
    {
        $data = $request->all();
        $psico = Psicologica::where('id', $psico)->first();
        $psico->update($data);
        return response()->json(['psicologica' => $psico]);
    }
    function psicoStore(Request $request, Slam $slam)
    {
        $data = $request->all();
        $user = $request->user();
        $data['user_id'] = $user->id;
        $data['caseable_type'] = Slam::class;
        $data['caseable_id']   = $slam->id;
        $psico = Psicologica::create($data);
        return response()->json(['psicologica' => $psico], 201);
    }
    function show(Slam $slam)
    {
        $slam->load(['adultos', 'familiares',
            'psicologica_user:id,name',
            'trabajo_social_user:id,name',
            'legal_user:id,name',
            'user:id,name',
            'psicologicas.user:id,name',
            'informesLegales.user:id,name',
            'documentos.user:id,name',
            'fotografias.user:id,name',
        ]);
//        public function psicologicas()   { return $this->morphMany(Psicologica::class,  'caseable'); }
//        public function informesLegales(){ return $this->morphMany(InformeLegal::class,'caseable'); }
//        public function documentos()     { return $this->morphMany(Documento::class,    'caseable'); }
//        public function fotografias()    { return $this->morphMany(Fotografia::class,   'caseable'); }
        return response()->json(['slam' => $slam]);
    }
    public function index(Request $request)
    {
        $q        = trim((string) $request->get('q', ''));
        $perPage  = (int) $request->get('per_page', 10);
        $perPage  = max(5, min($perPage, 100));

        $query = Slam::orderByDesc('created_at')->with(['adultos', 'familiares']);

        if ($q !== '') {
            $like = "%{$q}%";
            $query->where(function ($s) use ($like) {
                $s->orWhere('numero_caso', 'like', $like)
                    ->orWhere('den_nombres', 'like', $like)
                    ->orWhere('den_paterno', 'like', $like)
                    ->orWhere('den_materno', 'like', $like);
            });
            $query->orWhereHas('adultos', function ($a) use ($like) {
//                $a->where('nombres', 'like', $like)
//                  ->orWhere('paterno', 'like', $like)
//                  ->orWhere('materno', 'like', $like);
                $a->where(DB::raw("CONCAT_WS(' ', nombre, paterno, materno)"), 'like', $like);
            });
            $query->orWhereHas('familiares', function ($f) use ($like) {
//                $f->where('nombres', 'like', $like)
//                  ->orWhere('paterno', 'like', $like)
//                  ->orWhere('materno', 'like', $like);
                $f->where(DB::raw("CONCAT_WS(' ', nombre, paterno, materno)"), 'like', $like);
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
                        $hecho = Psicologica::whereMorphedTo('caseable', Slam::class)
                            ->where('caseable_id', $c->id)->exists();
                        $labelListo = 'Primera sesión lista';
                        break;
                    case 'Abogado':
                        $hecho = InformeLegal::whereMorphedTo('caseable', Slam::class)
                            ->where('caseable_id', $c->id)->exists();
                        $labelListo = 'Primer informe (Legal) listo';
                        break;
                    case 'Social':
                        $hecho = Problematica::whereMorphedTo('caseable', Slam::class)
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
    public function update(Request $request, Slam $slam)
    {
        $payload     = $request->all();
        $slamData    = $payload['slam']       ?? [];
        $adultos     = $payload['adultos']    ?? [];
        $familiares  = $payload['familiares'] ?? [];

        $slam = DB::transaction(function () use ($slam, $slamData, $adultos, $familiares) {

            // Actualiza el SLAM
            $slam->update($slamData);

            // Actualiza N adultos (si llegan)
            if (!empty($adultos)) {
                foreach ($adultos as $a) {
                    if (isset($a['id']) && $a['id'] > 0) {
                        // Actualiza existente
                        $adulto = $slam->adultos()->where('id', $a['id'])->first();
                        if ($adulto) {
                            if (isset($a['_delete']) && $a['_delete']) {
                                // Elimina
                                $adulto->delete();
                            } else {
                                // Actualiza
                                $adulto->update($a);
                            }
                        }
                    } else {
                        // Nuevo adulto
                        if (empty($a['_delete']) || !$a['_delete']) {
                            $slam->adultos()->create($a);
                        }
                    }
                }
            }

            // Actualiza N familiares (si llegan)
            if (!empty($familiares)) {
                foreach ($familiares as $f) {
                    if (isset($f['id']) && $f['id'] > 0) {
                        // Actualiza existente
                        $familiar = $slam->familiares()->where('id', $f['id'])->first();
                        if ($familiar) {
                            if (isset($f['_delete']) && $f['_delete']) {
                                // Elimina
                                $familiar->delete();
                            } else {
                                // Actualiza
                                $familiar->update($f);
                            }
                        }
                    } else {
                        // Nuevo familiar
                        if (empty($f['_delete']) || !$f['_delete']) {
                            $slam->familiares()->create($f);
                        }
                    }
                }
            }

            // Devuelve con relaciones
            return $slam->load(['adultos', 'familiares']);
        });

        return response()->json(['slam' => $slam]);
    }
    public function store(Request $request)
    {
        $payload     = $request->all();
        $slamData    = $payload['slam']       ?? [];
        $adultos     = $payload['adultos']    ?? [];
        $familiares  = $payload['familiares'] ?? [];
        $user       = $request->user();

        $slam = DB::transaction(function () use ($slamData, $adultos, $familiares, $request, $user) {

            // Crea el SLAM
            $slamData['fecha_registro'] = now();
            $slamData['zona'] = $user->zona ?? 'Sin zona';
            $slamData['area'] = $user->area ?? 'Sin área';
            $slamData['user_id'] = $user->id;

            $slam = Slam::create($slamData);

            // Crea N adultos (si llegan)
            if (!empty($adultos)) {
                $slam->adultos()->createMany($adultos);
            }

            // Crea N familiares (si llegan)
            if (!empty($familiares)) {
                $slam->familiares()->createMany($familiares);
            }

            // Devuelve con relaciones
            return $slam->load(['adultos', 'familiares']);
        });

        return response()->json(['slam' => $slam], 201);
    }
}
