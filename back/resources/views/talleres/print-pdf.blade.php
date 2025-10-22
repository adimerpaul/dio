@php
    use Carbon\Carbon;

    /** @var \Illuminate\Support\Collection $talleres */
    /** @var string $start */
    /** @var string $end */

    function fmtDT($dt){ return $dt ? Carbon::parse($dt)->format('d/m/Y H:i') : '—'; }
    function safe($v,$d='—'){ return ($v!==null && $v!=='') ? $v : $d; }

    $totalEventos    = $talleres->count();
    $totalAsistentes = (int) $talleres->sum('numero_asistentes');
    $porDirigido     = $talleres->groupBy('dirigido_a')->map(fn($g)=>[
      'eventos'=>$g->count(),
      'asistentes'=>(int)$g->sum('numero_asistentes'),
    ]);
@endphp
    <!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Talleres</title>
    <style>
        /* Márgenes moderados para DomPDF */
        @page { margin: 16mm 12mm 16mm 12mm; }

        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 11.5px; color:#222; }

        .title { font-size: 16px; font-weight: 700; margin: 0 0 4px; }
        .muted { color:#666; }
        .bar   { height: 3px; background:#1976D2; margin: 6px 0 10px; }

        .summary { border:1px solid #e6e6e6; background:#fafbfd; padding:8px 10px; margin: 0 0 10px; }
        .summary b { font-weight:700; }

        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid #ddd; padding:6px 5px; vertical-align:top; }
        th { background:#f4f6f8; text-align:left; white-space:nowrap; font-weight:700; }
        td.nowrap, th.nowrap { white-space:nowrap; }
        td.num, th.num { text-align:right; }

        .chip { display:inline-block; padding:2px 6px; border-radius:3px; font-size:10.5px; line-height:1.2; }

        /* Repetir encabezado de tabla en cada página (sin header fijo) */
        thead { display: table-header-group; }
        tfoot { display: table-row-group; }
        tr    { page-break-inside: auto; } /* permitir cortar si hace falta */
    </style>
</head>
<body>

{{-- Encabezado "normal" (no fijo) --}}
<div class="title">Reporte de Talleres</div>
<div class="muted" style="font-size:11px; margin-bottom:2px;">
    Generado: {{ now()->format('d/m/Y H:i') }}
    &nbsp;|&nbsp; Rango: {{ Carbon::parse($start)->format('d/m/Y H:i') }} – {{ Carbon::parse($end)->format('d/m/Y H:i') }}
</div>
<div class="bar"></div>

<div class="summary">
    <b>Total de eventos:</b> {{ $totalEventos }}
    &nbsp;&nbsp;|&nbsp;&nbsp;
    <b>Total de asistentes:</b> {{ number_format($totalAsistentes) }}
    @if($porDirigido->count())
        <div class="muted" style="margin-top:4px;">
            <b>Por público objetivo:</b>
            @foreach($porDirigido as $k=>$v)
                <span style="margin-left:10px;">
            {{ $k ?? '—' }}: {{ $v['eventos'] }} eventos / {{ $v['asistentes'] }} asistentes
          </span>
            @endforeach
        </div>
    @endif
</div>

<table>
    <thead>
    <tr>
        <th class="nowrap">Inicio</th>
        <th class="nowrap">Fin</th>
        <th>Colegio / Curso</th>
        <th>Tema &amp; Detalle</th>
        <th class="nowrap">Solicitado</th>
        <th class="nowrap">Dirigido a</th>
        <th class="num nowrap">N° Asist.</th>
{{--        <th class="nowrap">Color</th>--}}
    </tr>
    </thead>
    <tbody>
    @forelse($talleres as $t)
        @php
            $bg  = $t->color ?: '#1976D2';
            $hex = ltrim($bg, '#');
            if(strlen($hex)===3){ $hex=$hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2]; }
            $r=hexdec(substr($hex,0,2)); $g=hexdec(substr($hex,2,2)); $b=hexdec(substr($hex,4,2));
            $lum=(0.299*$r+0.587*$g+0.114*$b)/255; $tc=$lum>0.5 ? '#000':'#fff';
        @endphp
        <tr>
            <td class="nowrap">{{ fmtDT($t->fecha_inicio) }}</td>
            <td class="nowrap">{{ fmtDT($t->fecha_final) }}</td>
            <td>
                <div><strong>{{ safe($t->colegio) }}</strong></div>
                <div class="muted">Curso: {{ safe($t->curso) }}</div>
                @if($t->lugar)<div class="muted">Lugar: {{ $t->lugar }}</div>@endif
                @if($t->direccion_ubicacion_ue_colegio)<div class="muted">Dirección: {{ $t->direccion_ubicacion_ue_colegio }}</div>@endif
            </td>
            <td>
                <div><strong>{{ safe($t->tema) }}</strong></div>
                @if($t->descripcion)<div class="muted">{{ $t->descripcion }}</div>@endif
                @if($t->encargado_taller_nombre || $t->encargado_taller_telefono)
                    <div class="muted">
                        Encargado: {{ safe($t->encargado_taller_nombre) }}@if($t->encargado_taller_telefono) ({{ $t->encargado_taller_telefono }})@endif
                    </div>
                @endif
            </td>
            <td class="nowrap">{{ safe($t->solicitado) }}</td>
            <td class="nowrap">{{ safe($t->dirigido_a) }}</td>
            <td class="num nowrap">{{ (int) $t->numero_asistentes }}</td>
{{--            <td class="nowrap"><span class="chip" style="background:{{ $bg }}; color:{{ $tc }};">{{ $bg }}</span></td>--}}
        </tr>
    @empty
        <tr><td colspan="8" class="muted" style="text-align:right;">Sin registros.</td></tr>
    @endforelse
    </tbody>
    @if($totalEventos>0)
        <tfoot>
        <tr>
            <th colspan="6" class="num">Totales</th>
            <th class="num">{{ number_format($totalAsistentes) }}</th>
            <th></th>
        </tr>
        </tfoot>
    @endif
</table>

{{-- Numeración de páginas (solo texto, sin header/footer fijos) --}}
<script type="text/php">
    if (isset($pdf)) {
        $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
        $font = $fontMetrics->get_font("DejaVu Sans","normal");
        $pdf->page_text(500, 815, $text, $font, 9, [0.4,0.4,0.4]); // ajusta Y si usas A4
    }
</script>
</body>
</html>
