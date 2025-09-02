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
        /* ====== DENSO A UNA HOJA ====== */
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
        .mt4 { margin-top: 4px; } .mt8 { margin-top: 6px; } /* menor que antes */
        /* Ajustes finos para caber mejor */
        .tight td, .tight th { padding-top: 2px; padding-bottom: 2px; }
        .no-gap { margin: 0; }
        .nowrap { white-space: nowrap; }
        .clip { word-break: break-word; hyphens: auto; }
        /* Evitar cortes feos dentro de cajas/tablas */
        .box, table { page-break-inside: avoid; }
    </style>
</head>
<body>

<!-- ENCABEZADO -->
<div class="header-line">
    <div class="title">SERVICIO LEGAL INTEGRAL MUNICIPAL (SLIM)</div>
    <div class="center b" style="margin-top:2px;">REGISTRO DE DENUNCIAS</div>
</div>

<!-- 1. Encabezado de caso -->
<div class="row mt8">
    <div class="col box" style="width: 64%;">
        <span class="lbl">1.- Nombre del Denunciante</span><br>
        <span class="clip">{{ d($caso->denunciante_nombre_completo) }}</span>
    </div>
    <div class="col box" style="width: 36%;">
        <div class="row">
            <div class="col" style="width: 50%;">
                <span class="lbl">CASO:</span>
                <div class="b nowrap">{{ d($caso->caso_numero) }}</div>
            </div>
            <div class="col">
                <span class="lbl">Fecha de registro:</span>
                <div class="nowrap">{{ fdate($caso->created_at) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- 2. DATOS DEL DENUNCIANTE -->
<div class="subtitle mt8 no-gap">2.- Datos del Denunciante</div>

<table class="tbl tiny tight">
    <tr>
        <th style="width: 23%;">2.1 Nombre(s)</th>
        <th style="width: 23%;">Ap. Paterno</th>
        <th style="width: 23%;">Ap. Materno</th>
        <th>Obs.</th>
    </tr>
    <tr>
        <td class="clip">{{ d($caso->denunciante_nombres) }}</td>
        <td class="clip">{{ d($caso->denunciante_paterno) }}</td>
        <td class="clip">{{ d($caso->denunciante_materno) }}</td>
        <td></td>
    </tr>
</table>

<table class="tbl tiny tight" style="margin-top:-1px;">
    <tr>
        <th style="width: 32%;">2.2 Documento de Identidad</th>
        <th style="width: 18%;">Nro.</th>
        <th style="width: 16%;">2.3 Sexo</th>
        <th>—</th>
    </tr>
    <tr>
        <td class="clip">{{ d($caso->denunciante_documento) }}</td>
        <td class="clip">{{ d($caso->denunciante_nro) }}</td>
        <td class="clip">{{ d($caso->denunciante_sexo) }}</td>
        <td></td>
    </tr>
</table>

<table class="tbl tiny tight" style="margin-top:-1px;">
    <tr>
        <th style="width: 60%;">2.4 Lugar de nacimiento</th>
        <th>2.5 Fecha de nacimiento</th>
    </tr>
    <tr>
        <td class="clip">{{ d($caso->denunciante_lugar_nacimiento) }}</td>
        <td class="nowrap">{{ fdate($caso->denunciante_fecha_nacimiento) }}</td>
    </tr>
</table>

<table class="tbl tiny tight" style="margin-top:-1px;">
    <tr>
        <th style="width: 18%;">2.6 Edad</th>
        <th>2.7 Residencia Habitual</th>
    </tr>
    <tr>
        <td class="center">{{ d($caso->denunciante_edad) }}</td>
        <td class="clip">{{ d($caso->denunciante_residencia) }}</td>
    </tr>
</table>

<table class="tbl tiny tight" style="margin-top:-1px;">
    <tr>
        <th style="width: 33%;">2.8 Estado Civil</th>
        <th style="width: 33%;">2.9 Relación con el Denunciado(s)</th>
        <th>2.10 Grado de instrucción</th>
    </tr>
    <tr>
        <td class="clip">{{ d($caso->denunciante_estado_civil) }}</td>
        <td class="clip">{{ d($caso->denunciante_relacion) }}</td>
        <td class="clip">{{ d($caso->denunciante_grado) }}</td>
    </tr>
</table>

<table class="tbl tiny tight" style="margin-top:-1px;">
    <tr>
        <th style="width: 12%;">2.11 Trabaja</th>
        <th style="width: 16%;">Aprox.</th>
        <th style="width: 16%;">Exacto</th>
        <th>Ocupación / Detalle</th>
    </tr>
    <tr>
        <td class="center">{{ yesno($caso->denunciante_trabaja) }}</td>
        <td class="clip">{{ d($caso->denunciante_prox) }}</td>
        <td class="clip">{{ d($caso->denunciante_ocupacion_exacto) }}</td>
        <td class="clip">{{ d($caso->denunciante_ocupacion) }}</td>
    </tr>
</table>

<table class="tbl tiny tight" style="margin-top:-1px;">
    <tr><th>2.13 Idioma</th></tr>
    <tr><td class="clip">{{ d($caso->denunciante_idioma) }}</td></tr>
</table>

<table class="tbl tiny tight" style="margin-top:-1px;">
    <tr>
        <th style="width: 22%;">2.14 L. Fija 1</th>
        <th style="width: 22%;">Fija 2</th>
        <th style="width: 22%;">Móvil 1</th>
        <th style="width: 22%;">Móvil 2</th>
        <th>Obs.</th>
    </tr>
    <tr>
        <td class="clip">{{ d($caso->denunciante_fijo) }}</td>
        <td></td>
        <td class="clip">{{ d($caso->denunciante_movil) }}</td>
        <td></td>
        <td></td>
    </tr>
</table>

<table class="tbl tiny tight" style="margin-top:-1px;">
    <tr><th>2.15 Domicilio Actual</th></tr>
    <tr><td class="clip">{{ d($caso->denunciante_domicilio_actual) }}</td></tr>
</table>

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
    @for($i=1;$i<=5;$i++)
        @php
            $n = "familiar{$i}_nombre_completo";
            $e = "familiar{$i}_edad";
            $p = "familiar{$i}_parentesco";
            $c = "familiar{$i}_celular";
        @endphp
        <tr>
            <td class="center">{{ $i }}</td>
            <td class="clip">{{ d($caso->$n) }}</td>
            <td class="center">{{ d($caso->$e) }}</td>
            <td class="clip">{{ d($caso->$p) }}</td>
            <td class="clip">{{ d($caso->$c) }}</td>
        </tr>
    @endfor
    </tbody>
</table>

<!-- 4. DATOS DEL DENUNCIADO -->
<div class="subtitle mt8 no-gap">4.- Datos de la Persona Denunciado</div>

<table class="tbl tiny tight">
    <tr>
        <th style="width: 23%;">4.1 Nombre(s)</th>
        <th style="width: 23%;">Ap. Paterno</th>
        <th style="width: 23%;">Ap. Materno</th>
        <th>Obs.</th>
    </tr>
    <tr>
        <td class="clip">{{ d($caso->denunciado_nombres) }}</td>
        <td class="clip">{{ d($caso->denunciado_paterno) }}</td>
        <td class="clip">{{ d($caso->denunciado_materno) }}</td>
        <td></td>
    </tr>
</table>

<table class="tbl tiny tight" style="margin-top:-1px;">
    <tr>
        <th style="width: 32%;">4.2 Documento de Identidad</th>
        <th style="width: 18%;">Nro.</th>
        <th style="width: 16%;">4.3 Sexo</th>
        <th>—</th>
    </tr>
    <tr>
        <td class="clip">{{ d($caso->denunciado_documento) }}</td>
        <td class="clip">{{ d($caso->denunciado_nro) }}</td>
        <td class="clip">{{ d($caso->denunciado_sexo) }}</td>
        <td></td>
    </tr>
</table>

<table class="tbl tiny tight" style="margin-top:-1px;">
    <tr>
        <th style="width: 60%;">4.4 Lugar de nacimiento</th>
        <th>4.5 Fecha de nacimiento</th>
    </tr>
    <tr>
        <td class="clip">{{ d($caso->denunciado_lugar_nacimiento) }}</td>
        <td class="nowrap">{{ fdate($caso->denunciado_fecha_nacimiento) }}</td>
    </tr>
</table>

<table class="tbl tiny tight" style="margin-top:-1px;">
    <tr>
        <th style="width: 12%;">Edad</th>
        <th style="width: 33%;">Residencia Habitual</th>
        <th style="width: 22%;">4.6 Estado Civil</th>
        <th>4.7 Idioma</th>
    </tr>
    <tr>
        <td class="center">{{ d($caso->denunciado_edad) }}</td>
        <td class="clip">{{ d($caso->denunciado_residencia) }}</td>
        <td class="clip">{{ d($caso->denunciado_estado_civil) }}</td>
        <td class="clip">{{ d($caso->denunciado_idioma) }}</td>
    </tr>
</table>

<table class="tbl tiny tight" style="margin-top:-1px;">
    <tr>
        <th style="width: 24%;">4.8 Grado de instrucción</th>
        <th>4.9 Ocupación</th>
        <th style="width: 16%;">4.10 Aprox.</th>
        <th style="width: 16%;">Exacto</th>
    </tr>
    <tr>
        <td class="clip">{{ d($caso->denunciado_grado) }}</td>
        <td class="clip">{{ d($caso->denunciado_ocupacion) }}</td>
        <td class="clip">{{ d($caso->denunciado_prox) }}</td>
        <td class="clip">{{ d($caso->denunciado_ocupacion_exacto) }}</td>
    </tr>
</table>

<table class="tbl tiny tight" style="margin-top:-1px;">
    <tr>
        <th style="width: 40%;">4.11 Relación con la Denunciante</th>
        <th>Dirección actual</th>
    </tr>
    <tr>
        <td class="clip">{{ d($caso->denunciante_relacion) }}</td>
        <td class="clip">{{ d($caso->denunciado_domicilio_actual) }}</td>
    </tr>
</table>

<!-- 5. BREVE CIRCUNSTANCIA DEL HECHO -->
<div class="subtitle mt8 no-gap">5.- Breve circunstancia del hecho o Denuncia</div>
<div class="box" style="min-height: 60px;">
    {!! nl2br(e(d($caso->caso_descripcion))) !!}
</div>

<!-- 6. TIPOLOGÍA DEL HECHO PRINCIPAL -->
<div class="subtitle mt8 no-gap">6.- Tipología del Hecho Principal</div>
<div class="box">{{ d($caso->caso_tipologia) }}</div>

<!-- 7/8/9/10 Tipos de violencia -->
<div class="row mt8">
    <div class="col box" style="width: 25%;"><span class="b">7.- Violencia Física:</span> {{ yesno($caso->violencia_fisica) }}</div>
    <div class="col box" style="width: 25%;"><span class="b">8.- Violencia Psicológica:</span> {{ yesno($caso->violencia_psicologica) }}</div>
    <div class="col box" style="width: 25%;"><span class="b">9.- Violencia sexual:</span> {{ yesno($caso->violencia_sexual) }}</div>
    <div class="col box" style="width: 25%;"><span class="b">10.- Violencia económica patrimonial:</span> {{ yesno($caso->violencia_economica) }}</div>
</div>

<!-- 11. Seguimiento -->
<div class="subtitle mt8 no-gap">Seguimiento del caso</div>
<table class="tbl tiny tight">
    <thead>
    <tr>
        <th style="width: 28%;">ÁREA</th>
        <th>RESPONSABLE</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>PSICOLÓGICA</td>
        <td class="clip">{{ d($caso->seguimiento_area) }}</td>
    </tr>
    <tr>
        <td>ÁREA SOCIAL</td>
        <td class="clip">{{ d($caso->seguimiento_area_social) }}</td>
    </tr>
    <tr>
        <td>ÁREA LEGAL</td>
        <td class="clip">{{ d($caso->seguimiento_area_legal) }}</td>
    </tr>
    </tbody>
</table>

</body>
</html>
