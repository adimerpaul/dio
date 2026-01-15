<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MapaController extends Controller{
    public function mapa(Request $request)
    {
        // Params ?lat=...&lng=...&pin=0|1
        $lat = $request->query('lat', 19.4326);
        $lng = $request->query('lng', -99.1332);
        $pin = $request->boolean('pin', true); // si quieres ocultar el pin: ?pin=0

        $LAT = is_numeric($lat) ? (float)$lat : 0.0;
        $LNG = is_numeric($lng) ? (float)$lng : 0.0;
        $HAS = is_numeric($lat) && is_numeric($lng);

        return view('mapa.pdf', [
            'LAT' => $LAT,
            'LNG' => $LNG,
            'HAS' => $HAS,
            'PIN' => $pin,
            'label' => $request->query('label', 'Ubicación'),
        ]);
    }
}
