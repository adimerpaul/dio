@php
    function d($v, $def=''){ return $v !== null && $v !== '' ? $v : $def; }
    function fdate($v){ return $v ? \Carbon\Carbon::parse($v)->format('d/m/Y') : ''; }
@endphp
    <!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>DNA · Informe Legal #{{ $informe->id }}</title>
    <style>
        @page { margin: 28px 28px 40px 28px; }
        * { font-family: "DejaVu Sans", sans-serif; font-size: 10px; line-height: 1.15; }
        .box { padding: 4px 2px; }
        .header { text-align:center; margin-bottom:8px; }
        .header .t1 { font-size: 14px; font-weight: bold; }
        .header .t2 { font-size: 12px; }
        hr { border: 0; border-top: 1px solid #444; margin: 6px 0; }
    </style>
</head>
<body>

<div class="header">
    <div class="t1">DIRECCIÓN DE IGUALDAD DE OPORTUNIDADES — DNA</div>
    <div class="t2">Gobierno Autónomo Municipal de Oruro</div>
    @if(d($informe->numero))
        <div class="t2" style="font-weight:bold;">N° {{ $informe->numero }}</div>
    @endif
    <hr/>
</div>

<div class="box" style="margin-bottom:6px;">
    <div><b>Título:</b> {{ d($informe->titulo) }}</div>
    <div><b>Fecha:</b> {{ fdate($informe->fecha) }}</div>
    <div><b>Profesional:</b> {{ optional($informe->user)->name }}</div>
</div>

<div class="box">
    {!! $informe->contenido_html !!}
</div>

</body>
</html>
