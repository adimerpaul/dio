<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Casos</title>
    <style>
        @page { margin: 16mm 14mm 16mm 14mm; }
        * { font-family: DejaVu Sans, sans-serif; color:#222; }
        body { font-size: 12px; }
        h1,h2 { margin:0; padding:0; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 14px; }
        .small { font-size: 11px; color:#555; }

        table { width:100%; border-collapse: collapse; }
        th, td { border: 1px solid #222; padding: 6px 5px; vertical-align: top; }
        th { font-size: 11px; text-transform: uppercase; background:#f2f2f2; }
        td { font-size: 11px; }

        .center { text-align:center; }
        .w-obs { width: 140px; } /* Observaciones un poco ancha */
    </style>
</head>
<body>
<h1 class="mb-2">{{ $titulo }}</h1>
<div class="small mb-4">{{ $subtitulo }}</div>

<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>N° Caso</th>
        <th>Denunciante</th>
        <th>Denunciado</th>
        <th>Tipología</th>
        <th>Resp. Legal</th>
        <th>Resp. Psicológico</th>
        <th>Resp. Social</th>
        <th>CUD</th>
        <th>NUREJ</th>
        <th>Juzgado</th>
        <th>Fiscalía (Resp.)</th>
        <th>Estado actual del proceso</th>
        @if($apoyoIntegralExtraCol)
            <th>Fec. Entrega al Juzgado</th>
        @endif
        <th class="w-obs">Obs.</th>
    </tr>
    </thead>
    <tbody>
    @php
        $fmt = function ($d) {
          try { return $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : '—'; }
          catch(\Exception $e){ return '—'; }
        };
        $nombreCompleto = function ($p, $n, $paterno, $materno) {
          $nom = trim(($p[$n] ?? '').' '.($p[$paterno] ?? '').' '.($p[$materno] ?? ''));
          return $nom !== '' ? $nom : '—';
        };
    @endphp

    @forelse($casos as $c)
        @php
            // FECHA = fecha_apertura_caso
            $fecha = $fmt($c->fecha_apertura_caso);

            // N° CASO
            $numCaso = $c->caso_numero ?: '—';

            // DENUNCIANTE(S) y DENUNCIADO(S) (toma el primero; si quieres todos, únelos con " / ")
            $denunciante = '—';
            if ($c->denunciantes && $c->denunciantes->count() > 0) {
              $p = $c->denunciantes->first()->toArray();
              $denunciante = $nombreCompleto($p, 'denunciante_nombres', 'denunciante_paterno', 'denunciante_materno');
            }

            $denunciado = '—';
            if ($c->denunciados && $c->denunciados->count() > 0) {
              $p = $c->denunciados->first()->toArray();
              $denunciado = $nombreCompleto($p, 'denunciado_nombres', 'denunciado_paterno', 'denunciado_materno');
            }

            // Responsables
            $rLegal = optional($c->legal_user)->name ?: '—';
            $rPsico = optional($c->psicologica_user)->name ?: '—';
            $rSocial= optional($c->trabajo_social_user)->name ?: '—';

            // Campos varios
            $tipologia = $c->caso_tipologia ?: '—';
            $cud = $c->cud ?: '—';
            $nurej = $c->nurej ?: '—';

            // No tienes campos Juzgado / Fiscalía / Estado en el modelo que pasaste;
            // quedan como "—" para que coincidan con el formato del Word.
            $juzgado = '—';
            $fiscalia = '—';
            $estadoProc = '—';

            // Si pasaste la bandera apoyo_integral=1, agrega columna extra (vacía por ahora)
            $fecEntregaJuzgado = '—';
        @endphp

        <tr>
            <td class="center">{{ $fecha }}</td>
            <td class="center">{{ $numCaso }}</td>
            <td>{{ $denunciante }}</td>
            <td>{{ $denunciado }}</td>
            <td>{{ $tipologia }}</td>
            <td>{{ $rLegal }}</td>
            <td>{{ $rPsico }}</td>
            <td>{{ $rSocial }}</td>
            <td class="center">{{ $cud }}</td>
            <td class="center">{{ $nurej }}</td>
            <td>{{ $juzgado }}</td>
            <td>{{ $fiscalia }}</td>
            <td>{{ $estadoProc }}</td>
            @if($apoyoIntegralExtraCol)
                <td class="center">{{ $fecEntregaJuzgado }}</td>
            @endif
            <td></td>
        </tr>
    @empty
        <tr>
            <td colspan="{{ 14 + ($apoyoIntegralExtraCol ? 1 : 0) }}" class="center">Sin resultados</td>
        </tr>
    @endforelse
    </tbody>
</table>

@if($apoyoIntegralExtraCol)
    <div class="small" style="margin-top:10px;">
        * Apoyo Integral: se incluye columna adicional «Fecha de entrega al Juzgado».
    </div>
@endif
</body>
</html>
