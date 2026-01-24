<?php

namespace App\Http\Controllers;

use App\Models\AutorizacionesTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AutorizacionesTrabajoController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start');
        $end   = $request->input('end');

        return AutorizacionesTrabajo::query()
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('fecha', [$start . ' 00:00:00', $end . ' 23:59:59']);
            })
            ->orderByDesc('id')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->except(['url']);

        // user_id si estás con sanctum
        if ($request->user()) {
            $data['user_id'] = $request->user()->id;
        }

        if (!isset($data['fecha']) || !$data['fecha']) {
            $data['fecha'] = now();
        }

        // booleans (por si llega "true"/"false" string)
        foreach (['pago_diario','pago_semanal','pago_quincenal','pago_anual'] as $k) {
            $data[$k] = filter_var($request->input($k), FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->hasFile('url')) {
            $file = $request->file('url');
            $ext = $file->getClientOriginalExtension() ?: 'bin';
            $name = Str::random(40) . '.' . $ext;
            $path = $file->storeAs('autorizaciones-trabajo', $name, 'public');
            $data['url'] = $path;
        }

        return AutorizacionesTrabajo::create($data);
    }

    public function update(Request $request, $id)
    {
        $row = AutorizacionesTrabajo::findOrFail($id);

        $data = $request->except(['url']);

        foreach (['pago_diario','pago_semanal','pago_quincenal','pago_anual'] as $k) {
            if ($request->has($k)) {
                $data[$k] = filter_var($request->input($k), FILTER_VALIDATE_BOOLEAN);
            }
        }

        if ($request->hasFile('url')) {
            // borrar anterior
            if ($row->url) {
                Storage::disk('public')->delete($row->url);
            }

            $file = $request->file('url');
            $ext = $file->getClientOriginalExtension() ?: 'bin';
            $name = Str::random(40) . '.' . $ext;
            $path = $file->storeAs('autorizaciones-trabajo', $name, 'public');
            $data['url'] = $path;
        }

        $row->update($data);
        return $row;
    }

    public function destroy($id)
    {
        $row = AutorizacionesTrabajo::findOrFail($id);

        if ($row->url) {
            Storage::disk('public')->delete($row->url);
        }

        $row->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
