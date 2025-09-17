@php
    function d($v, $def=''){ return $v !== null && $v !== '' ? $v : $def; }
    function fdate($v){ return $v ? \Carbon\Carbon::parse($v)->format('d/m/Y') : ''; }
@endphp
    <!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>DNA · Sesión Psicológica #{{ $sesion->id }}</title>
    <style>
        @page { margin: 28px 28px 40px 28px; }
        * { font-family: "DejaVu Sans", sans-serif; font-size: 10px; line-height: 1.15; }
        .title { font-weight: 700; font-size: 14px; text-align: center; margin: 0; }
        .subtitle { font-weight: 700; font-size: 11px; margin: 8px 0 4px; }
        .row { display: table; width: 100%; table-layout: fixed; }
        .col { display: table-cell; vertical-align: top; }
        .box { border: 0px solid #222; padding: 5px 6px; }
        .b { font-weight: 700; }
        .center { text-align: center; }
        .lbl { color: #333; font-size: 9px; }
        table.tbl { width: 100%; border-collapse: collapse; }
        table.tbl th, table.tbl td { border: 1px solid #222; padding: 3px 5px; vertical-align: top; }
        table.tbl th { background: #eef4ff; font-weight: 700; }
        .mt4 { margin-top: 6px; }
        .clip { word-break: break-word; hyphens: auto; }
    </style>
</head>
<body>

<!-- Encabezado -->
{{--<div class="box" style="background:#f5f8ff; margin-bottom:6px;">--}}
{{--    <div class="title">DIRECCIÓN DE NIÑEZ Y ADOLESCENCIA (DNA)</div>--}}
{{--    <div class="center b" style="margin-top:2px;">SESIÓN PSICOLÓGICA</div>--}}
{{--</div>--}}

<!-- Cabecera del caso -->
{{--<div class="row">--}}
{{--    <div class="col box" style="width: 60%;">--}}
{{--        <div class="lbl">Caso / Código</div>--}}
{{--        <div class="b">{{ d($caso->codigo, 'S/C') }}</div>--}}
{{--        <div class="lbl" style="margin-top:6px;">Denunciante</div>--}}
{{--        <div>{{ d($caso->denunciante_nombre) }}</div>--}}
{{--        <div class="lbl" style="margin-top:6px;">Zona / Domicilio</div>--}}
{{--        <div class="clip">{{ d($caso->zona) }} — {{ d($caso->domicilio) }}</div>--}}
{{--    </div>--}}
{{--    <div class="col box" style="width: 40%;">--}}
{{--        <div class="row">--}}
{{--            <div class="col" style="width: 50%;">--}}
{{--                <div class="lbl">Fecha atención</div>--}}
{{--                <div>{{ fdate($caso->fecha_atencion) }}</div>--}}
{{--            </div>--}}
{{--            <div class="col">--}}
{{--                <div class="lbl">Registrado por</div>--}}
{{--                <div>{{ optional($caso->user)->name }}</div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="lbl" style="margin-top:6px;">Tipo proceso / Principal</div>--}}
{{--        <div class="clip">{{ d($caso->tipo_proceso) }} {{ $caso->principal ? ' — '.d($caso->principal) : '' }}</div>--}}
{{--    </div>--}}
{{--</div>--}}

<!-- Datos de la sesión -->
{{--<div class="subtitle mt4">Datos de la Sesión</div>--}}
{{--<table class="tbl">--}}
{{--    <tr>--}}
{{--        <th style="width: 18%;">Fecha</th>--}}
{{--        <th style="width: 34%;">Título</th>--}}
{{--        <th style="width: 18%;">Tipo</th>--}}
{{--        <th style="width: 12%;">Minutos</th>--}}
{{--        <th>Profesional</th>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <td>{{ fdate($sesion->fecha) }}</td>--}}
{{--        <td class="clip">{{ d($sesion->titulo) }}</td>--}}
{{--        <td>{{ d($sesion->tipo, '—') }}</td>--}}
{{--        <td class="center">{{ $sesion->duracion_min !== null ? $sesion->duracion_min : '' }}</td>--}}
{{--        <td class="clip">{{ optional($sesion->user)->name }}</td>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <th colspan="1">Lugar</th>--}}
{{--        <td colspan="4" class="clip">{{ d($sesion->lugar) }}</td>--}}
{{--    </tr>--}}
{{--</table>--}}

{{--<!-- Contenido -->--}}
{{--<div class="subtitle mt4">Contenido</div>--}}
<div class="box">
    {!! $sesion->contenido_html !!}
</div>

</body>
</html>
