<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Problematica;
use App\Models\SesionPsicologica;
use App\Models\Informe;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CasoTimelineController extends Controller
{
    /**
     * GET /api/casos-linea-tiempo
     * Params:
     *  - q        : string (búsqueda)
     *  - page     : int
     *  - per_page : int
     *
     * Devuelve una lista paginada de casos con:
     *  - created_at (base)
     *  - first_problematica_at = MIN(problematicas.created_at)
     *  - first_sesion_at       = MIN(COALESCE(sesiones_psicologicas.fecha, sesiones_psicologicas.created_at))
     *  - first_informe_at      = MIN(COALESCE(informes.fecha, informes.created_at))
     * + deltas (en horas/días) y versiones “human”.
     */
    public function index(Request $request)
    {
        $q    = trim($request->get('q', ''));
        $page = (int) $request->get('page', 1);
        $per  = (int) $request->get('per_page', 10);
        $per  = $per > 0 ? $per : 10;

        // subqueries
        $subFirstProb = Problematica::selectRaw('MIN(problematicas.created_at)')
            ->whereColumn('problematicas.caso_id', 'casos.id');

        $subFirstSesion = SesionPsicologica::selectRaw('MIN(COALESCE(sesiones_psicologicas.fecha, sesiones_psicologicas.created_at))')
            ->whereColumn('sesiones_psicologicas.caso_id', 'casos.id');

        $subFirstInforme = Informe::selectRaw('MIN(COALESCE(informes.fecha, informes.created_at))')
            ->whereColumn('informes.caso_id', 'casos.id');

        $query = Caso::query()
            ->select([
                'casos.*',
            ])
            ->selectSub($subFirstProb, 'first_problematica_at')
            ->selectSub($subFirstSesion, 'first_sesion_at')
            ->selectSub($subFirstInforme, 'first_informe_at');

        // Búsqueda básica (ajusta campos a tus columnas reales)
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('caso_numero', 'like', "%{$q}%")
                    ->orWhere('caso_tipologia', 'like', "%{$q}%")
                    ->orWhere('caso_zona', 'like', "%{$q}%")
                    ->orWhere('caso_descripcion', 'like', "%{$q}%")
                    ->orWhere('denunciante_nombre_completo', 'like', "%{$q}%")
                    ->orWhere('denunciado_nombre_completo', 'like', "%{$q}%");
            });
        }

        $rows = $query->orderByDesc('casos.id')->paginate($per, ['*'], 'page', $page);

        // mapear para agregar diffs
        $rows->getCollection()->transform(function ($c) {
            $base = $c->created_at ? Carbon::parse($c->created_at) : null;

            $fmt = function ($dateStr) use ($base) {
                if (!$dateStr || !$base) {
                    return [
                        'exists'       => false,
                        'date'         => null,
                        'date_human'   => null,
                        'diff_human'   => null,
                        'diff_minutes' => null,
                        'diff_hours'   => null,
                        'diff_days'    => null,
                    ];
                }
                $d    = Carbon::parse($dateStr);
                $mins = $base->diffInMinutes($d);
                return [
                    'exists'       => true,
                    'date'         => $d->toDateTimeString(),
                    'date_human'   => $d->format('d/m/Y H:i'),
                    'diff_human'   => $base->diffForHumans($d, [
                        'parts'  => 3,
                        'short'  => true,
                        'syntax' => Carbon::DIFF_ABSOLUTE
                    ]),
                    'diff_minutes' => $mins,
                    'diff_hours'   => round($mins / 60, 2),
                    'diff_days'    => round($mins / 60 / 24, 2),
                ];
            };

            return [
                'id'                     => $c->id,
                'caso_numero'            => $c->caso_numero,
                'caso_tipologia'         => $c->caso_tipologia,
                'caso_zona'              => $c->caso_zona,
                'denunciante_nombre'     => $c->denunciante_nombre_completo,
                'denunciado_nombre'      => $c->denunciado_nombre_completo,
                'base'                   => $c->created_at ? Carbon::parse($c->created_at)->format('d/m/Y H:i') : null,
                'first_problematica'     => $fmt($c->first_problematica_at),
                'first_sesion'           => $fmt($c->first_sesion_at),
                'first_informe'          => $fmt($c->first_informe_at),
            ];
        });

        return response()->json($rows);
    }
}
