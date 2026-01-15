<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\CasoDenunciante;
use App\Models\CasoDenunciado;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardCasoController extends Controller
{
    public function index(Request $request)
    {
        // ===== RANGO DE FECHAS (por defecto últimos 6 meses) =====
        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : (clone $to)->subMonths(5)->startOfMonth(); // últimos 6 meses

        // ===== QUERY BASE DE CASOS (usando created_at) =====
        $casos = Caso::query()
            ->whereBetween('created_at', [$from, $to]);

        // -------- FILTRO SOLO POR ÁREA ----------
        if ($request->filled('area')) {
            $casos->where('area', $request->area);
        }else{
            $user = $request->user();
            if($user->role != 'Administrador' && $user->area){
                $casos->where('area', $user->area);
            }
        }

        $baseCasos = clone $casos;
        $casoIds   = (clone $baseCasos)->pluck('id');

        // ===== TOTALES =====
        $totalCasos = $casoIds->count();

        $denunciantesBase = CasoDenunciante::whereIn('caso_id', $casoIds);
        $denunciadosBase  = CasoDenunciado::whereIn('caso_id', $casoIds);

        $totalDenunciantes = (clone $denunciantesBase)->count();
        $totalDenunciados  = (clone $denunciadosBase)->count();

        // ===== CASOS POR ÁREA =====
        $porAreaRaw = (clone $casos)
            ->select(
                DB::raw('COALESCE(area, "Sin área") as label'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('area')
            ->orderByDesc('total')
            ->get();

        $porArea = [
            'labels' => $porAreaRaw->pluck('label'),
            'data'   => $porAreaRaw->pluck('total'),
        ];

        // ===== CASOS POR TIPO =====
        $porTipoRaw = (clone $casos)
            ->select(
                DB::raw('COALESCE(tipo, "Sin tipo") as label'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tipo')
            ->orderByDesc('total')
            ->get();

        $porTipo = [
            'labels' => $porTipoRaw->pluck('label'),
            'data'   => $porTipoRaw->pluck('total'),
        ];

        // ===== CASOS POR TIPOLOGÍA =====
        $porTipologiaRaw = (clone $casos)
            ->select(
                DB::raw('COALESCE(caso_tipologia, "Sin tipología") as label'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('caso_tipologia')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $porTipologia = [
            'labels' => $porTipologiaRaw->pluck('label'),
            'data'   => $porTipologiaRaw->pluck('total'),
        ];

        // ===== CASOS POR RANGO DE EDAD (denunciante) =====
        $edades = (clone $denunciantesBase)
            ->whereNotNull('denunciante_fecha_nacimiento')
            ->pluck('denunciante_fecha_nacimiento')
            ->map(fn ($f) => Carbon::parse($f)->age)
            ->filter(fn ($age) => $age >= 0);

        $buckets = [
            '0-11'  => 0,
            '12-17' => 0,
            '18-25' => 0,
            '26-59' => 0,
            '60+'   => 0,
        ];

        foreach ($edades as $age) {
            if ($age <= 11) {
                $buckets['0-11']++;
            } elseif ($age <= 17) {
                $buckets['12-17']++;
            } elseif ($age <= 25) {
                $buckets['18-25']++;
            } elseif ($age <= 59) {
                $buckets['26-59']++;
            } else {
                $buckets['60+']++;
            }
        }

        $porEdad = [
            'labels' => array_keys($buckets),
            'data'   => array_values($buckets),
        ];

        // ===== SERIE POR MES (usando created_at) =====
        $seriesRaw = (clone $baseCasos)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $seriesTiempo = [
            'labels' => $seriesRaw->pluck('mes'),
            'data'   => $seriesRaw->pluck('total'),
        ];

        // ===== OPCIONES DE FILTRO =====
        $areas = Caso::select('area')
            ->whereNotNull('area')
            ->distinct()
            ->orderBy('area')
            ->pluck('area');

        $tipos = Caso::select('tipo')
            ->whereNotNull('tipo')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        $tipologias = Caso::select('caso_tipologia')
            ->whereNotNull('caso_tipologia')
            ->distinct()
            ->orderBy('caso_tipologia')
            ->pluck('caso_tipologia');

        $fraganciaBase = (clone $casos)
            ->whereIn('tipo', ['DNA', 'SLIM']);

        $porFraganciaRaw = (clone $fraganciaBase)
            ->select(
                DB::raw('CASE
                    WHEN titulo = "Registrar Nuevo Caso Hechos de Fragancia"
                        THEN "Hechos de fragancia"
                    ELSE "Otros casos (por denuncia)"
                 END as label'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('label')
            ->orderByDesc('total')
            ->get();

        $porFragancia = [
            'labels' => $porFraganciaRaw->pluck('label'),
            'data'   => $porFraganciaRaw->pluck('total'),
        ];

        // ===== DENUNCIANTES DEL DÍA =====
        $hoy = Carbon::now();
        $cantidad_denunciantes_del_dia = CasoDenunciante::whereDate('created_at', $hoy->toDateString())
            ->count();


        return response()->json([
            'totales' => [
                'casos'        => $totalCasos,
                'denunciantes' => $totalDenunciantes,
                'denunciados'  => $totalDenunciados,
            ],
            'por_area'      => $porArea,
            'cantidad_denunciantes_del_dia'      => $cantidad_denunciantes_del_dia,
            'por_tipo'      => $porTipo,
            'por_tipologia' => $porTipologia,
            'por_edad'      => $porEdad,
            'series_tiempo' => $seriesTiempo,
            'por_fragancia' => $porFragancia,
            'filtros'       => [
                'areas'      => $areas,
                'tipos'      => $tipos,
                'tipologias' => $tipologias,
            ],
            'rango'         => [
                'from' => $from->toDateString(),
                'to'   => $to->toDateString(),
            ],
        ]);
    }

    /**
     * Detalle de casos para un segmento del gráfico (tipo o tipología).
     * GET dashboard-casos-detalle
     * params: from, to, area, group_by=tipo|tipologia, value=...
     */
    public function detalle(Request $request)
    {
        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : (clone $to)->subMonths(5)->startOfMonth();

        $groupBy = $request->input('group_by'); // 'tipo', 'tipologia' o 'fragancia'
        $value   = $request->input('value');

        if (!$groupBy || !$value) {
            return response()->json([]);
        }

        $casos = Caso::query()
            ->whereBetween('created_at', [$from, $to]);

        if ($request->filled('area')) {
            $casos->where('area', $request->area);
        }

        if ($groupBy === 'tipo') {
            $casos->where('tipo', $value);
        } elseif ($groupBy === 'tipologia') {
            $casos->where('caso_tipologia', $value);
        } elseif ($groupBy === 'fragancia') {
            // SOLO DNA y SLIM
            $casos->whereIn('tipo', ['DNA', 'SLIM']);

            if ($value === 'Hechos de fragancia') {
                $casos->where('titulo', 'Registrar Nuevo Caso Hechos de Fragancia');
            } else {
                // Otros (por denuncia)
                $casos->where(function ($q) {
                    $q->where('titulo', '!=', 'Registrar Nuevo Caso Hechos de Fragancia')
                        ->orWhereNull('titulo');
                });
            }
        }

        $rows = $casos
            ->select(
                'id',
                'area',
                'tipo',
                'caso_tipologia',
                'caso_numero',
                'caso_fecha_hecho',
                'caso_lugar_hecho',
                'principal'
            )
            ->orderByDesc('caso_fecha_hecho')
            ->limit(200)
            ->get();

        return response()->json($rows);
    }
}
