<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Sesión psicológica #{{ $sesion->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .h1 { font-size: 18px; font-weight: bold; margin-bottom: 6px; }
        .muted { color:#666; }
        .box { border:1px solid #ddd; padding:12px; border-radius:8px; }
    </style>
</head>
<body>
<div class="h1">SLIM · Sesión psicológica #{{ $sesion->id }}</div>
<div class="muted">
    SLIM: {{ $sesion->caseable?->id }} · Usuario: {{ $sesion->user?->name ?? $sesion->user?->username }}
</div>
<hr>

<p><b>Título:</b> {{ $sesion->titulo }}</p>
<p><b>Fecha:</b> {{ optional($sesion->fecha)->format('Y-m-d') ?? '—' }}</p>
<p><b>Duración:</b> {{ $sesion->duracion_min ? $sesion->duracion_min.' min' : '—' }}</p>
<p><b>Tipo:</b> {{ $sesion->tipo ?: '—' }}</p>
<p><b>Lugar:</b> {{ $sesion->lugar ?: '—' }}</p>

<div class="box">{!! $sesion->contenido_html !!}</div>
</body>
</html>
