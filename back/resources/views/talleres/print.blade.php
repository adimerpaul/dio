@php
    use Carbon\Carbon;

    /** @var \Illuminate\Support\Collection $talleres */
    /** @var string|null $start */
    /** @var string|null $end */

    function fmtDT($dt) {
        if (!$dt) return '—';
        return Carbon::parse($dt)->format('d/m/Y H:i');
    }
    function safe($v, $d='—') {
        return ($v !== null && $v !== '') ? $v : $d;
    }
    $totalEventos = $talleres->count();
    $totalAsistentes = (int) $talleres->sum('numero_asistentes');
    $porDirigido = $talleres->groupBy('dirigido_a')->map(fn($g) => [
        'eventos' => $g->count(),
        'asistentes' => (int) $g->sum('numero_asistentes'),
    ]);
@endphp
    <!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Impresión | Talleres</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root{
            --fg:#222; --muted:#666; --border:#e0e0e0; --bg:#fff; --thead:#f5f7fa; --chip-fg:#fff;
            --primary:#1976D2;
        }
        *{ box-sizing: border-box; }
        body{
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            color: var(--fg);
            margin: 0; background: var(--bg);
            font-size: 12px; line-height: 1.35;
        }
        .container{ max-width: 1000px; margin: 0 auto; padding: 24px; }
        .header{
            display: grid; grid-template-columns: 1fr auto; gap: 16px; align-items: center; margin-bottom: 12px;
        }
        .title{ font-size: 18px; font-weight: 700; margin: 0 0 4px; }
        .meta{ color: var(--muted); font-size: 12px; }
        .bar{ height: 3px; width: 100%; background: linear-gradient(90deg, var(--primary), #42a5f5); margin: 12px 0 16px; }
        .summary{
            border: 1px solid var(--border); background: #fafbfd; padding: 10px 12px; border-radius: 6px; margin-bottom: 14px;
            display: grid; grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap: 8px 16px;
        }
        .summary b{ font-weight: 700; }
        table{ width: 100%; border-collapse: collapse; }
        th, td{ border: 1px solid var(--border); padding: 6px 8px; vertical-align: top; }
        th{ background: var(--thead); text-align: left; font-weight: 700; white-space: nowrap; }
        td.nowrap, th.nowrap{ white-space: nowrap; }
        td.num, th.num{ text-align: right; }
        .muted{ color: var(--muted); }
        .chip{
            display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 11px; line-height: 1.3;
            color: var(--chip-fg);
        }
        .footnote{ margin-top: 10px; color: var(--muted); font-size: 11px; }
        .no-print{ display: inline-flex; gap: 8px; align-items: center; margin: 8px 0 12px; }
        .btn{
            border: 1px solid var(--border); background: #fff; padding: 6px 10px; border-radius: 6px; cursor: pointer;
            font-size: 12px;
        }
        .btn-primary{ border-color: var(--primary); color: #fff; background: var(--primary); }
        .totales{
            margin-top: 8px; font-size: 12px; color: var(--muted);
        }
        @media print{
            .no-print{ display: none !important; }
            .container{ padding: 0 12px; }
            @page{ margin: 14mm 12mm 16mm; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h1 class="title">Reporte de Talleres</h1>
            <div class="meta">
                Generado: {{ now()->format('d/m/Y H:i') }}
                @if($start || $end)
                    &nbsp;|&nbsp; Rango:
                    {{ $start ? Carbon::parse($start)->format('d/m/Y H:i') : '—' }}
                    &ndash;
                    {{ $end ? Carbon::parse($end)->format('d/m/Y H:i') : '—' }}
                @endif
            </div>
        </div>
        <div class="no-print">
            <button class="btn" onclick="window.close()">Cerrar</button>
            <button class="btn btn-primary" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="bar"></div>

    <div class="summary">
        <div><b>Total de eventos:</b> {{ $totalEventos }}</div>
        <div><b>Total de asistentes:</b> {{ number_format($totalAsistentes) }}</div>
        @if($porDirigido->count())
            <div style="grid-column: 1 / -1;">
                <b>Por público objetivo:</b>
                @foreach($porDirigido as $k => $v)
                    <span class="muted" style="margin-left:10px;">
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
            <th class="nowrap">Color</th>
        </tr>
        </thead>
        <tbody>
        @forelse($talleres as $t)
            @php
                $bg = $t->color ?: '#1976D2';
                // Contraste simple
                $hex = ltrim($bg, '#');
                if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
                $r = hexdec(substr($hex,0,2)); $g = hexdec(substr($hex,2,2)); $b = hexdec(substr($hex,4,2));
                $lum = (0.299*$r + 0.587*$g + 0.114*$b)/255;
                $tc = $lum > 0.5 ? '#000' : '#fff';
            @endphp
            <tr>
                <td class="nowrap">{{ fmtDT($t->fecha_inicio) }}</td>
                <td class="nowrap">{{ fmtDT($t->fecha_final) }}</td>
                <td>
                    <div><strong>{{ safe($t->colegio) }}</strong></div>
                    <div class="muted">Curso: {{ safe($t->curso) }}</div>
                    @if($t->lugar)
                        <div class="muted">Lugar: {{ $t->lugar }}</div>
                    @endif
                    @if($t->direccion_ubicacion_ue_colegio)
                        <div class="muted">Dirección: {{ $t->direccion_ubicacion_ue_colegio }}</div>
                    @endif
                </td>
                <td>
                    <div><strong>{{ safe($t->tema) }}</strong></div>
                    @if($t->descripcion)
                        <div class="muted">{{ $t->descripcion }}</div>
                    @endif
                    @if($t->encargado_taller_nombre || $t->encargado_taller_telefono)
                        <div class="muted">Encargado: {{ safe($t->encargado_taller_nombre) }}
                            @if($t->encargado_taller_telefono) ({{ $t->encargado_taller_telefono }}) @endif
                        </div>
                    @endif
                </td>
                <td class="nowrap">{{ safe($t->solicitado) }}</td>
                <td class="nowrap">{{ safe($t->dirigido_a) }}</td>
                <td class="num nowrap">{{ (int) $t->numero_asistentes }}</td>
                <td class="nowrap">
            <span class="chip" style="background: {{ $bg }}; color: {{ $tc }};">
              {{ $bg }}
            </span>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="muted" style="text-align:right;">Sin registros.</td></tr>
        @endforelse
        </tbody>
        @if($totalEventos > 0)
            <tfoot>
            <tr>
                <th colspan="6" class="num">Totales</th>
                <th class="num">{{ number_format($totalAsistentes) }}</th>
                <th></th>
            </tr>
            </tfoot>
        @endif
    </table>

    <div class="totales">
        Informe generado automáticamente. Si requiere firma o sello, imprimir y firmar al pie.
    </div>

    <div class="footnote">
        {{ config('app.name') }} – {{ url('/') }}
    </div>
</div>

<script>
    // Si quieres imprimir automáticamente al abrir:
    // window.addEventListener('load', () => window.print());
</script>
</body>
</html>
