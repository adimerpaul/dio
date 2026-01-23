<?php

namespace App\Http\Controllers;

use App\Models\RemisionCaso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RemisionCasoController extends Controller
{
    function marcarLeido(Request $request, $remisionCasoId)
    {
        $remisionCaso = RemisionCaso::findOrFail($remisionCasoId);
//        error_log('Marcando como leído remisionCaso ID: ' . $remisionCaso->id);
        $remisionCaso->fechaleido = now();
        $remisionCaso->save();
//        error_log('RemisionCaso marcado como leído en: ' . $remisionCaso->fechaleido);
        return response()->json(['message' => 'Marcado como leído']);
    }
    function misRemisionCasos(Request $request)
    {
        $userId = $request->user()->id;
        return RemisionCaso::with('user:id,name,role')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();
    }
    // LISTA
    public function index(Request $request)
    {
        // Para poder usar row.user en el front
        $interoExterno = $request->query('interoExterno');
//        error_log('Intero Externo: ' . $interoExterno);
        if ($interoExterno == 'EXTERNO') {
            return RemisionCaso::with('user:id,name,role')
                ->orderBy('id', 'desc')
                ->where('interno_externo', 'EXTERNO')
                ->get();
        }
        $user = $request->user();
        $zona = $user->zona;
        $area = $user->area;
        return RemisionCaso::with('user:id,name,role')
            ->orderBy('id', 'desc')
            ->where('interno_externo', 'INTERNO')
            ->where('zona', $zona)
            ->where('area', $area)
            ->get();
    }

    // CREAR
    public function store(Request $request)
    {
        $codigo = (RemisionCaso::max('codigo_ingreso') ?? 0) + 1;

        $data = $request->all();
        $user = $request->user();
        $data['codigo_ingreso'] = $codigo;
        $data['fecha_hora'] = now();

        $data['area'] = $user->area ?? 'Sin área';
        $data['zona'] = $user->zona ?? 'Sin zona';

        // Si viene archivo, lo guardamos
        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('remisiones', 'public');
            $data['archivo'] = $path;
        }

        $remision = RemisionCaso::create($data);
        $remision->load('user:id,name,role');

        return response()->json($remision, 201);
    }

    // VER UNO
    public function show(RemisionCaso $remisionCaso)
    {
        $remisionCaso->load('user:id,name,role');
        return $remisionCaso;
    }

    // ACTUALIZAR
    public function update(Request $request, RemisionCaso $remisionCaso)
    {
        $data = $request->all();

        // Si envían un archivo nuevo, reemplazamos el anterior
        if ($request->hasFile('archivo')) {
            if ($remisionCaso->archivo) {
                Storage::disk('public')->delete($remisionCaso->archivo);
            }

            $path = $request->file('archivo')->store('remisiones', 'public');
            $data['archivo'] = $path;
        } else {
            // Si no viene archivo en el request, no queremos pisar el valor existente
            unset($data['archivo']);
        }

        $remisionCaso->fill($data);
        $remisionCaso->save();
        $remisionCaso->load('user:id,name,role');

        return response()->json($remisionCaso);
    }

    // ELIMINAR (soft delete + borrar archivo físico)
    public function destroy(RemisionCaso $remisionCaso)
    {
        if ($remisionCaso->archivo) {
            Storage::disk('public')->delete($remisionCaso->archivo);
        }

        $remisionCaso->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
