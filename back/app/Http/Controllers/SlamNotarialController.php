<?php

namespace App\Http\Controllers;

use App\Models\SlamNotarial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlamNotarialController extends Controller
{
    public function index(Request $req)
    {
        $q = SlamNotarial::query()
            ->where('user_id', $req->user()->id);

        if ($req->filled('start') && $req->filled('end')) {
            $q->whereBetween('fecha', [$req->start, $req->end]);
        }

        return $q->orderBy('fecha','desc')->get();
    }
    public function store(Request $req)
    {
        $path = null;
        if ($req->hasFile('url')) {
            $path = $req->file('url')->store('slam-notariales', 'public');
        }

        $item = SlamNotarial::create([
            'user_id'            => $req->user()->id,
            'fecha'              => date('Y-m-d H:i:s'),
            'nombre_completo'    => $req->input('nombre_completo'),
            'edad'               => $req->input('edad'),
            'fecha_nacimiento'   => $req->input('fecha_nacimiento'),
            'estado_civil'       => $req->input('estado_civil'),
            'ci'                 => $req->input('ci'),
            'grado_instruccion'  => $req->input('grado_instruccion'),
            'ocupacion'          => $req->input('ocupacion'),
            'direccion_domicilio'=> $req->input('direccion_domicilio'),
            'url'                => $path,
        ]);

        return response()->json($item, 201);
    }
//    put
    function  update(Request $req, $id)
    {
        $item = SlamNotarial::where('user_id', $req->user()->id)->findOrFail($id);

        if ($req->hasFile('url')) {
            $path = $req->file('url')->store('slam-notariales', 'public');
            $item->url = $path;
        }

//        $item->fecha               = $req->input('fecha', $item->fecha);
        $item->nombre_completo     = $req->input('nombre_completo', $item->nombre_completo);
        $item->edad                = $req->input('edad', $item->edad);
        $item->fecha_nacimiento    = $req->input('fecha_nacimiento', $item->fecha_nacimiento);
        $item->estado_civil        = $req->input('estado_civil', $item->estado_civil);
        $item->ci                  = $req->input('ci', $item->ci);
        $item->grado_instruccion   = $req->input('grado_instruccion', $item->grado_instruccion);
        $item->ocupacion           = $req->input('ocupacion', $item->ocupacion);
        $item->direccion_domicilio = $req->input('direccion_domicilio', $item->direccion_domicilio);

        $item->save();

        return response()->json($item);
    }
    public function destroy(Request $req, $id)
    {
        $item = SlamNotarial::where('user_id', $req->user()->id)->findOrFail($id);
        if ($item->url) Storage::disk('public')->delete($item->url);
        $item->delete();
        return response()->json(['ok' => true]);
    }
}
