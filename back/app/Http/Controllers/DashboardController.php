<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Problematica;
use App\Models\SesionPsicologica;
use App\Models\Informe;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard
     */
    public function index()
    {
        $today = Carbon::today();
        $startMonth = $today->copy()->startOfMonth();

        // Totales generales
        $totales = [
            'casos'         => Caso::count(),
            'problematicas' => Problematica::count(),
            'sesiones'      => SesionPsicologica::count(),
            'informes'      => Informe::count(),
        ];

        // Nuevos este mes
        $esteMes = [
            'casos'         => Caso::where('created_at', '>=', $startMonth)->count(),
            'problematicas' => Problematica::where('created_at', '>=', $startMonth)->count(),
            'sesiones'      => SesionPsicologica::where(DB::raw('COALESCE(fecha, created_at)'), '>=', $startMonth)->count(),
            'informes'      => Informe::where(DB::raw('COALESCE(fecha, created_at)'), '>=', $startMonth)->count(),
        ];

        // Series últimos 6 meses (para gráfica de líneas)
        $months = [];
        $cursor = $today->copy()->subMonths(5)->startOfMonth();
        while ($cursor <= $today) {
            $months[] = $cursor->format('Y-m');
            $cursor->addMonth();
        }

        $countByMonth = function($model, $col = 'created_at') use ($months) {
            return $model::select(
                DB::raw("DATE_FORMAT($col, '%Y-%m') as ym"),
                DB::raw("COUNT(*) as cnt")
            )
                ->where($col, '>=', Carbon::parse($months[0])->startOfMonth())
                ->groupBy('ym')
                ->pluck('cnt', 'ym')
                ->toArray();
        };

        $casos   = $countByMonth(Caso::class);
        $probs   = $countByMonth(Problematica::class);
        $sesions = $countByMonth(SesionPsicologica::class, 'fecha');
        $inf     = $countByMonth(Informe::class, 'fecha');

        $series = [
            'labels' => $months,
            'casos'  => array_map(fn($m) => $casos[$m] ?? 0, $months),
            'prob'   => array_map(fn($m) => $probs[$m] ?? 0, $months),
            'ses'    => array_map(fn($m) => $sesions[$m] ?? 0, $months),
            'inf'    => array_map(fn($m) => $inf[$m] ?? 0, $months),
        ];

        return response()->json([
            'totales'  => $totales,
            'esteMes'  => $esteMes,
            'series'   => $series,
        ]);
    }
}
