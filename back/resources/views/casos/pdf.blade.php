@php
    function yesno($v){ return $v ? 'SI' : 'NO'; }
    function d($v, $def=''){ return $v !== null && $v !== '' ? $v : $def; }
    function fdate($v){ return $v ? \Carbon\Carbon::parse($v)->format('d/m/Y') : ''; }
@endphp
    <!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro SLIM — Caso #{{ $caso->id }}</title>
    <style>
        @page { margin: 28px 28px 40px 28px; }
        * { font-family: "DejaVu Sans", sans-serif; font-size: 9px; line-height: 1.12; }
        .title { font-weight: 700; font-size: 13px; text-align: center; margin: 0; }
        .subtitle { font-weight: 700; font-size: 10px; margin: 6px 0 3px; }
        .row { display: table; width: 100%; table-layout: fixed; }
        .col { display: table-cell; vertical-align: top; }
        .box { border: 1px solid #222; padding: 3px 4px; }
        .b { font-weight: 700; }
        .tiny { font-size: 8px; }
        .center { text-align: center; }
        .right { text-align: right; }
        table.tbl { width: 100%; border-collapse: collapse; }
        table.tbl th, table.tbl td { border: 1px solid #222; padding: 2px 4px; }
        table.tbl th { background: #eef4ff; font-weight: 700; }
        .header-line { border: 1px solid #222; padding: 4px; background: #f5f8ff; margin-bottom: 4px; }
        .lbl { color: #333; font-size: 8px; }
        .mt4 { margin-top: 4px; } .mt8 { margin-top: 6px; }
        .tight td, .tight th { padding-top: 2px; padding-bottom: 2px; }
        .no-gap { margin: 0; }
        .nowrap { white-space: nowrap; }
        .clip { word-break: break-word; hyphens: auto; }
        .box, table { page-break-inside: avoid; }
    </style>
</head>
<body>

<!-- ENCABEZADO -->
<div class="header-line">
    <div class="title">SERVICIO LEGAL INTEGRAL MUNICIPAL (SLIM)</div>
    <div class="center b" style="margin-top:2px;">REGISTRO DE DENUNCIAS</div>
</div>

<!-- 1. Datos básicos del caso -->
<div class="row mt8">
    <div class="col box" style="width: 64%;">
        <span class="lbl">1.- Caso</span><br>
        <span class="clip">N° {{ d($caso->caso_numero) }} · Fecha Hecho: {{ fdate($caso->caso_fecha_hecho) }}</span>
    </div>
    <div class="col box" style="width: 36%;">
        <div class="row">
            <div class="col" style="width: 100%;">
                <span class="lbl">Fecha de registro:</span>
                <div class="nowrap">{{ fdate($caso->fecha_apertura_caso) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- 2. DATOS DEL/LOS DENUNCIANTES -->
<div class="subtitle mt8 no-gap">2.- Datos del/los Denunciantes</div>
@foreach($caso->denunciantes as $den)
    <table class="tbl tiny tight" style="margin-bottom:4px;">
        <tr>
            <th style="width: 23%;">Nombre(s)</th>
            <th style="width: 23%;">Ap. Paterno</th>
            <th style="width: 23%;">Ap. Materno</th>
            <th>Teléfono</th>
        </tr>
        <tr>
            <td>{{ d($den->denunciante_nombres) }}</td>
            <td>{{ d($den->denunciante_paterno) }}</td>
            <td>{{ d($den->denunciante_materno) }}</td>
            <td>{{ d($den->denunciante_telefono) }}</td>
        </tr>
        <tr>
            <th>Documento</th>
            <td colspan="3">{{ d($den->denunciante_documento) }} {{ d($den->denunciante_nro) }}</td>
        </tr>
    </table>
@endforeach

<!-- 3. GRUPO FAMILIAR -->
<div class="subtitle mt8 no-gap">3.- Grupo Familiar</div>
<table class="tbl tiny tight">
    <thead>
    <tr>
        <th style="width: 6%;">NRO</th>
        <th>NOMBRES Y APELLIDOS</th>
        <th style="width: 10%;">EDAD</th>
        <th style="width: 20%;">PARENTESCO</th>
        <th style="width: 18%;">CELULAR</th>
    </tr>
    </thead>
    <tbody>
    @foreach($caso->familiares as $i => $fam)
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td>{{ d($fam->familiar_nombre_completo) }}</td>
            <td class="center">{{ d($fam->familiar_edad) }}</td>
            <td>{{ d($fam->familiar_parentesco) }}</td>
            <td>{{ d($fam->familiar_telefono) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- 4. DATOS DEL/LOS DENUNCIADOS -->
<div class="subtitle mt8 no-gap">4.- Datos del/los Denunciados</div>
@foreach($caso->denunciados as $denu)
    <table class="tbl tiny tight" style="margin-bottom:4px;">
        <tr>
            <th style="width: 25%;">Nombre(s)</th>
            <th>Teléfono</th>
        </tr>
        <tr>
            <td>{{ d($denu->denunciado_nombres) }}</td>
            <td>{{ d($denu->denunciado_telefono) }}</td>
        </tr>
        <tr>
            <th>Documento</th>
            <td>{{ d($denu->denunciado_documento) }} {{ d($denu->denunciado_nro) }}</td>
        </tr>
    </table>
@endforeach

<!-- 5. CIRCUNSTANCIA DEL HECHO -->
<div class="subtitle mt8 no-gap">5.- Breve circunstancia del hecho</div>
<div class="box" style="min-height: 60px;">
    {!! nl2br(e(d($caso->caso_descripcion))) !!}
</div>

<!-- 6. TIPOLOGÍA -->
<div class="subtitle mt8 no-gap">6.- Tipología del Hecho Principal</div>
<div class="box">{{ d($caso->caso_tipologia) }}</div>

<!-- 7/8/9/10 Tipos de violencia -->
<div class="row mt8">
    <div class="col box" style="width: 25%;">Física: {{ yesno($caso->violencia_fisica) }}</div>
    <div class="col box" style="width: 25%;">Psicológica: {{ yesno($caso->violencia_psicologica) }}</div>
    <div class="col box" style="width: 25%;">Sexual: {{ yesno($caso->violencia_sexual) }}</div>
    <div class="col box" style="width: 25%;">Económica: {{ yesno($caso->violencia_economica) }}</div>
</div>

<!-- 11. Seguimiento -->
<div class="subtitle mt8 no-gap">11.- Seguimiento del caso</div>
<table class="tbl tiny tight">
    <thead>
    <tr><th style="width: 28%;">ÁREA</th><th>RESPONSABLE</th></tr>
    </thead>
    <tbody>
    <tr><td>Psicológica</td><td>{{ optional($caso->psicologica_user)->name }}</td></tr>
    <tr><td>Área Social</td><td>{{ optional($caso->trabajo_social_user)->name }}</td></tr>
    <tr><td>Área Legal</td><td>{{ optional($caso->legal_user)->name }}</td></tr>
    </tbody>
</table>

</body>
</html>
