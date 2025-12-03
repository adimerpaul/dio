<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\CasoAcogimiento;
use Illuminate\Http\Request;

class CasoAcogimientoController extends Controller
{
    /**
     * GET /casos/{caso}/acogimiento
     * Devuelve el acogimiento (si existe) de ese caso.
     */
    public function showByCaso(Caso $caso)
    {
        $acogimiento = $caso->acogimientos()->latest('id')->first();

        return response()->json($acogimiento);
    }

    /**
     * POST /casos/{caso}/acogimiento
     * Crea o actualiza el registro de acogida del caso.
     */
    public function storeOrUpdate(Request $request, Caso $caso)
    {
        $data = $request->validate([
            'tipo_de_acogida'           => ['nullable', 'string', 'max:255'],
            'centro_de_acogida'         => ['nullable', 'string', 'max:255'],
            'cuidadora_nombre_completo' => ['nullable', 'string', 'max:255'],
            'cuidadora_celular'         => ['nullable', 'string', 'max:50'],
            'cuidadora_domicilio'       => ['nullable', 'string', 'max:255'],
            'cuidadora_ubicacion'       => ['nullable', 'string', 'max:255'],
            'cuidadora_lat'             => ['nullable', 'string', 'max:50'],
            'cuidadora_lng'             => ['nullable', 'string', 'max:50'],
        ]);

        $acogimiento = CasoAcogimiento::updateOrCreate(
            ['caso_id' => $caso->id],
            $data + ['caso_id' => $caso->id]
        );

        return response()->json($acogimiento, 201);
    }
}
