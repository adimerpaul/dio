<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Informe Legal #{{ $informe->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .h1 { font-size: 18px; font-weight: bold; margin-bottom: 6px; }
        .muted { color:#666; }
        .box { border:1px solid #ddd; padding:12px; border-radius:8px; }
    </style>
</head>
<body>
{{--<div class="h1">SLIM · Informe #{{ $informe->id }}</div>--}}
{{--<div class="muted">--}}
{{--    SLIM: {{ $informe->caseable?->id }} · Usuario: {{ $informe->user?->name ?? $informe->user?->username }}--}}
{{--</div>--}}
{{--<hr>--}}

{{--<p><b>Título:</b> {{ $informe->titulo }}</p>--}}
{{--<p><b>Fecha:</b> {{ optional($informe->fecha)->format('Y-m-d') ?? '—' }}</p>--}}
{{--<p><b>Número:</b> {{ $informe->numero ?: '—' }}</p>--}}

<div class="box">{!! $informe->contenido_html !!}</div>
</body>
</html>
