// resources/views/pdf/informe.blade.php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Informe</title>
    <style>
        @page { margin: 28mm 18mm 22mm 18mm; }
        body    { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #111; }
        header  { position: fixed; top: -22mm; left: 0; right: 0; height: 22mm; }
        footer  { position: fixed; bottom: -16mm; left: 0; right: 0; height: 16mm; font-size: 10px; color: #555; }
        .brand  { font-size: 13px; font-weight: 700; letter-spacing: .4px; }
        .muted  { color: #666; }
        .box    { border: 1px solid #333; border-radius: 6px; padding: 12px; }
        .h      { font-weight: 700; }
        .row    { display: flex; gap: 12px; }
        .col    { flex: 1; }
    </style>
</head>
<body>
<header>
    <table width="100%">
        <tr>
            <td class="brand">SERVICIO LEGAL INTEGRAL MUNICIPAL — D.I.O.</td>
            <td align="right" class="muted">Informe {{ $informe->area ? '— '.strtoupper($informe->area) : '' }}</td>
        </tr>
    </table>
    <hr>
</header>

<footer>
    <hr>
    <table width="100%">
        <tr>
            <td class="muted">Emitido por el sistema — {{ now()->format('d/m/Y H:i') }}</td>
            <td align="right" class="muted">Página {PAGE_NUM} de {PAGE_COUNT}</td>
        </tr>
    </table>
</footer>

<main>
    <div class="box" style="margin-bottom: 12px;">
        <div class="row">
            <div class="col"><span class="h">Caso:</span> #{{ $informe->caso_id }}</div>
            <div class="col"><span class="h">Informe:</span> #{{ $informe->id }}</div>
            <div class="col"><span class="h">Fecha:</span> {{ optional($informe->fecha)->format('d/m/Y') ?? '—' }}</div>
        </div>
        <div class="row" style="margin-top: 6px;">
            <div class="col"><span class="h">Título:</span> {{ $informe->titulo }}</div>
            <div class="col"><span class="h">Área:</span> {{ $informe->area ?? '—' }}</div>
            <div class="col"><span class="h">Nro:</span> {{ $informe->numero ?? '—' }}</div>
            <div class="col"><span class="h">Responsable:</span> {{ $informe->user->name ?? $informe->user->username ?? '—' }}</div>
        </div>
    </div>

    {{-- HTML guardado desde el editor --}}
    {!! $informe->contenido_html !!}
</main>
</body>
</html>
