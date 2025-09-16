<?php

namespace App\Http\Controllers;

use App\Models\Slam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SlamController extends Controller
{
    public function store(Request $request)
    {
        $payload     = $request->all();
        $slamData    = $payload['slam']       ?? [];
        $adultos     = $payload['adultos']    ?? [];
        $familiares  = $payload['familiares'] ?? [];

        $slam = DB::transaction(function () use ($slamData, $adultos, $familiares, $request) {

            // Si usas auth y no llegó user_id, lo completamos (opcional)
            if (!array_key_exists('user_id', $slamData)) {
                $slamData['user_id'] = optional($request->user())->id;
            }

            // Crea el SLAM
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
