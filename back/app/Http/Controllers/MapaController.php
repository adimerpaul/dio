<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MapaController extends Controller{
    function mapa(Request $request){
        $lat = $request->query('lat', 19.4326);
        $lng = $request->query('lng', -99.1332);
        $LAT = floatval($lat);
        $LNG = floatval($lng);
        $HAS = is_numeric($lat) && is_numeric($lng);

        return view('mapa.pdf', [
            'LAT' => $LAT,
            'LNG' => $LNG,
            'HAS' => $HAS,
        ]);
    }
}
