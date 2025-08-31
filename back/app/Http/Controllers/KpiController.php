<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Informe;
use App\Models\Problematica;
use App\Models\SesionPsicologica;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KpiController extends Controller
{
    /**
     * GET /api/kpis?start=YYYY-MM-DD&end=YYYY-MM-DD
     * Devuelve:
     *  - Totales (casos, problemáticas, sesiones, informes)
     *  - Promedios de tiempo (min/h desde creación del caso)
     *  - Conteos por tipología y por zona (top N)
     *  - Series mensuales (casos, problemáticas, sesiones, informes)
     */
    public function index(Request $request)
    {
        // Rango (default: últimos 90 días)
        $end   = $request->get('end')   ? Carbon::parse($request->get('end'))->endOfDay()   : now();
        $start = $request->get('start') ? Carbon::parse($request->get('start'))->startOfDay() : $end->copy()->subDays(89)->startOfDay();

        // ==== Totales ====
        $totCasos = Caso::whereBetween('created_at', [$start, $end])->count();

        $totProb = Problematica::whereBetween('created_at', [$start, $end])->count();

        $totSes = SesionPsicologica::whereBetween(DB::raw('COALESCE(fecha, created_at)'), [$start, $end])->count();

        $totInf = Informe::whereBetween(DB::raw('COALESCE(fecha, created_at)'), [$start, $end])->count();

        // ==== Promedios de tiempo desde creación de caso ====
        // Para cada caso del rango, hallamos primer hito y luego promediamos diferencia
        $casesInRange = Caso::whereBetween('created_at', [$start, $end])->select('id', 'created_at')->get();

        $diffs = [
            'problematica' => [],
            'sesion'       => [],
            'informe'      => [],
        ];

        foreach ($casesInRange as $c) {
            $base = Carbon::parse($c->created_at);

            $pAt = Problematica::where('caso_id', $c->id)->min('created_at');
            if ($pAt) $diffs['problematica'][] = $base->diffInMinutes(Carbon::parse($pAt));

            $sAt = SesionPsicologica::where('caso_id', $c->id)
                ->select(DB::raw('MIN(COALESCE(fecha, created_at)) as first_at'))
                ->value('first_at');
            if ($sAt) $diffs['sesion'][] = $base->diffInMinutes(Carbon::parse($sAt));

            $iAt = Informe::where('caso_id', $c->id)
                ->select(DB::raw('MIN(COALESCE(fecha, created_at)) as first_at'))
                ->value('first_at');
            if ($iAt) $diffs['informe'][] = $base->diffInMinutes(Carbon::parse($iAt));
        }

        $avg = function(array $arr) {
            if (!count($arr)) return null;
            return array_sum($arr) / count($arr);
        };

        $avgProbMin = $avg($diffs['problematica']);
        $avgSesMin  = $avg($diffs['sesion']);
        $avgInfMin  = $avg($diffs['informe']);

        // ==== Por tipología / zona (Top 10) sobre casos del rango ====
        $porTipologia = Caso::whereBetween('created_at', [$start, $end])
            ->select('caso_tipologia as name', DB::raw('COUNT(*) as value'))
            ->groupBy('caso_tipologia')
            ->orderByDesc('value')->limit(10)->get();

        $porZona = Caso::whereBetween('created_at', [$start, $end])
            ->select('caso_zona as name', DB::raw('COUNT(*) as value'))
            ->groupBy('caso_zona')
            ->orderByDesc('value')->limit(10)->get();

        // ==== Series mensuales ====
        // Generamos lista de meses del rango
        $cursor = $start->copy()->startOfMonth();
        $months = [];
        while ($cursor <= $end) {
            $months[] = $cursor->format('Y-m');
            $cursor->addMonth();
        }

        // helpers para contar por mes
        $countByMonth = function($builder, $dateColumn) use ($start, $end) {
            return $builder
                ->whereBetween($dateColumn, [$start, $end])
                ->select(DB::raw("DATE_FORMAT($dateColumn, '%Y-%m') as ym"), DB::raw('COUNT(*) as cnt'))
                ->groupBy('ym')
                ->pluck('cnt', 'ym')
                ->toArray();
        };

        $casosByMonth = $countByMonth(Caso::query(), 'created_at');

        $probByMonth  = $countByMonth(Problematica::query(), 'created_at');

        $sesByMonth   = $countByMonth(SesionPsicologica::query(), DB::raw('COALESCE(fecha, created_at)'));

        $infByMonth   = $countByMonth(Informe::query(), DB::raw('COALESCE(fecha, created_at)'));

        $seriesMensual = [
            'labels' => $months,
            'casos'  => array_map(fn($m) => $casosByMonth[$m] ?? 0, $months),
            'prob'   => array_map(fn($m) => $probByMonth[$m]  ?? 0, $months),
            'ses'    => array_map(fn($m) => $sesByMonth[$m]   ?? 0, $months),
            'inf'    => array_map(fn($m) => $infByMonth[$m]   ?? 0, $months),
        ];

        return response()->json([
            'period' => [
                'start' => $start->toDateString(),
                'end'   => $end->toDateString(),
            ],
            'totals' => [
                'casos'         => $totCasos,
                'problematicas' => $totProb,
                'sesiones'      => $totSes,
                'informes'      => $totInf,
            ],
            'avg_minutes' => [
                'to_first_problematica' => $avgProbMin,
                'to_first_sesion'       => $avgSesMin,
                'to_first_informe'      => $avgInfMin,
            ],
            'avg_hours' => [
                'to_first_problematica' => is_null($avgProbMin) ? null : round($avgProbMin/60, 2),
                'to_first_sesion'       => is_null($avgSesMin)  ? null : round($avgSesMin/60, 2),
                'to_first_informe'      => is_null($avgInfMin)  ? null : round($avgInfMin/60, 2),
            ],
            'por_tipologia' => $porTipologia,
            'por_zona'      => $porZona,
            'mensual'       => $seriesMensual,
        ]);
    }
}
