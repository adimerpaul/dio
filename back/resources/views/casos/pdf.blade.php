@php
    // Helpers simples
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
        * { font-family: "DejaVu Sans", sans-serif; font-size: 11px; }
        .title { font-weight: 700; font-size: 16px; text-align: center; }
        .subtitle { font-weight: 700; font-size: 12px; margin: 6px 0 2px; }
        .row { display: table; width: 100%; table-layout: fixed; }
        .col { display: table-cell; vertical-align: top; }
        .box { border: 1px solid #222; padding: 4px 6px; }
        .b { font-weight: 700; }
        .tiny { font-size: 10px; }
        .mt4 { margin-top: 4px; }
        .mt8 { margin-top: 8px; }
        .mb4 { margin-bottom: 4px; }
        .mb8 { margin-bottom: 8px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .grid2 .col { width: 50%; }
        .grid3 .col { width: 33.333%; }
        .grid4 .col { width: 25%; }
        table.tbl { width: 100%; border-collapse: collapse; }
        table.tbl th, table.tbl td { border: 1px solid #222; padding: 4px 6px; }
        table.tbl th { background: #e9f2ff; font-weight: 700; }
        .header-line { border: 1px solid #222; padding: 6px; background: #f5f8ff; }
        .lbl { color: #333; font-size: 10px; }
    </style>
</head>
<body>

<!-- ENCABEZADO -->
<div class="header-line">
    <div class="title">SERVICIO LEGAL INTEGRAL MUNICIPAL (SLIM)</div>
    <div class="center b">REGISTRO DE DENUNCIAS</div>
</div>

<div class="row mt8">
    <div class="col box" style="width: 65%;">
        <span class="lbl">1.- Nombre del Denunciante</span><br>
        {{ d($caso->denunciante_nombre_completo) }}
    </div>
    <div class="col box" style="width: 35%;">
        <div class="row">
            <div class="col" style="width: 55%;">
                <span class="lbl">CASO:</span>
                <div class="b">{{ d($caso->caso_numero) }}</div>
            </div>
            <div class="col">
                <span class="lbl">Fecha de registro:</span>
                <div>{{ fdate($caso->created_at) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- 2. DATOS DEL DENUNCIANTE -->
<div class="subtitle mt8">2.- Datos del Denunciante</div>

<div class="row">
    <div class="col box" style="width: 45%;">
        <span class="lbl">2.1 Identificación</span><br>
        <span class="lbl">Documento:</span> {{ d($caso->denunciante_documento) }} &nbsp;&nbsp;
        <span class="lbl">Nro:</span> {{ d($caso->denunciante_nro) }}<br>
        <span class="lbl">Sexo:</span> {{ d($caso->denunciante_sexo) }}
    </div>
    <div class="col box" style="width: 55%;">
        <span class="lbl">2.2 Nacimiento / Residencia</span><br>
        <span class="lbl">Lugar nac.:</span> {{ d($caso->denunciante_lugar_nacimiento) }} &nbsp;&nbsp;
        <span class="lbl">Fecha nac.:</span> {{ fdate($caso->denunciante_fecha_nacimiento) }}<br>
        <span class="lbl">Edad:</span> {{ d($caso->denunciante_edad) }} &nbsp;&nbsp;
        <span class="lbl">Residencia:</span> {{ d($caso->denunciante_residencia) }}
    </div>
</div>

<div class="row">
    <div class="col box" style="width: 33%;">
        <span class="lbl">2.3 Estado Civil</span><br>
        {{ d($caso->denunciante_estado_civil) }}
    </div>
    <div class="col box" style="width: 33%;">
        <span class="lbl">2.4 Relación con el denunciado</span><br>
        {{ d($caso->denunciante_relacion) }}
    </div>
    <div class="col box" style="width: 34%;">
        <span class="lbl">2.5 Grado de instrucción</span><br>
        {{ d($caso->denunciante_grado) }}
    </div>
</div>

<div class="row">
    <div class="col box" style="width: 25%;">
        <span class="lbl">Trabaja</span><br>{{ yesno($caso->denunciante_trabaja) }}
    </div>
    <div class="col box" style="width: 45%;">
        <span class="lbl">Ocupación</span><br>{{ d($caso->denunciante_ocupacion) }} {{ d($caso->denunciante_ocupacion_exacto) }}
    </div>
    <div class="col box" style="width: 30%;">
        <span class="lbl">Idioma</span><br>{{ d($caso->denunciante_idioma) }}
    </div>
</div>

<div class="row">
    <div class="col box" style="width: 55%;">
        <span class="lbl">2.6 Teléfonos de referencia</span><br>
        Fijo: {{ d($caso->denunciante_fijo) }} &nbsp;&nbsp; Móvil: {{ d($caso->denunciante_movil) }}
    </div>
    <div class="col box" style="width: 45%;">
        <span class="lbl">2.7 Domicilio actual</span><br>{{ d($caso->denunciante_domicilio_actual) }}
    </div>
</div>

<!-- 3. GRUPO FAMILIAR -->
<div class="subtitle mt8">3.- Grupo Familiar</div>
<table class="tbl tiny">
    <thead>
    <tr>
        <th style="width: 5%;">NR</th>
        <th>NOMBRES Y APELLIDOS</th>
        <th style="width: 12%;">EDAD</th>
        <th style="width: 22%;">PARENTESCO</th>
        <th style="width: 20%;">CELULAR</th>
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
            <td>{{ d($caso->$n) }}</td>
            <td class="center">{{ d($caso->$e) }}</td>
            <td>{{ d($caso->$p) }}</td>
            <td>{{ d($caso->$c) }}</td>
        </tr>
    @endfor
    </tbody>
</table>

<!-- 4. DATOS DEL DENUNCIADO -->
<div class="subtitle mt8">4.- Datos de la Persona Denunciada</div>
<div class="row">
    <div class="col box" style="width: 50%;">
        <span class="lbl">4.1 Identificación</span><br>
        {{ d($caso->denunciado_nombre_completo) }}<br>
        <span class="lbl">Documento:</span> {{ d($caso->denunciado_documento) }} &nbsp;
        <span class="lbl">Nro:</span> {{ d($caso->denunciado_nro) }} &nbsp;
        <span class="lbl">Sexo:</span> {{ d($caso->denunciado_sexo) }}
    </div>
    <div class="col box" style="width: 50%;">
        <span class="lbl">4.2 Nacimiento / Residencia</span><br>
        <span class="lbl">Lugar nac.:</span> {{ d($caso->denunciado_lugar_nacimiento) }} &nbsp;
        <span class="lbl">Fecha nac.:</span> {{ fdate($caso->denunciado_fecha_nacimiento) }}<br>
        <span class="lbl">Edad:</span> {{ d($caso->denunciado_edad) }} &nbsp;
        <span class="lbl">Residencia:</span> {{ d($caso->denunciado_residencia) }}
    </div>
</div>

<div class="row">
    <div class="col box" style="width: 50%;">
        <span class="lbl">4.3 Instrucción / Ocupación</span><br>
        <span class="lbl">Grado:</span> {{ d($caso->denunciado_grado) }} &nbsp;
        <span class="lbl">Ocupación:</span> {{ d($caso->denunciado_ocupacion) }} {{ d($caso->denunciado_ocupacion_exacto) }}
    </div>
    <div class="col box" style="width: 50%;">
        <span class="lbl">4.4 Contacto / Domicilio</span><br>
        Fijo: {{ d($caso->denunciado_fijo) }} &nbsp; Móvil: {{ d($caso->denunciado_movil) }}<br>
        <span class="lbl">Dirección:</span> {{ d($caso->denunciado_domicilio_actual) }}
    </div>
</div>

<!-- 5. BREVE CIRCUNSTANCIA DEL HECHO -->
<div class="subtitle mt8">5.- Breve circunstancia del hecho o Denuncia</div>
<div class="box" style="min-height: 90px;">
    {!! nl2br(e(d($caso->caso_descripcion))) !!}
</div>

<!-- 6. TIPOLOGÍA DEL HECHO PRINCIPAL -->
<div class="subtitle mt8">6.- Tipología del Hecho Principal</div>
<div class="box">{{ d($caso->caso_tipologia) }}</div>

<!-- 7/8/9/10 Tipos de violencia -->
<div class="row mt8">
    <div class="col box" style="width: 25%;"><span class="b">7.- Violencia Física:</span> {{ yesno($caso->violencia_fisica) }}</div>
    <div class="col box" style="width: 25%;"><span class="b">8.- Violencia Psicológica:</span> {{ yesno($caso->violencia_psicologica) }}</div>
    <div class="col box" style="width: 25%;"><span class="b">9.- Violencia sexual:</span> {{ yesno($caso->violencia_sexual) }}</div>
    <div class="col box" style="width: 25%;"><span class="b">10.- Violencia económica patrimonial:</span> {{ yesno($caso->violencia_economica) }}</div>
</div>

<!-- 11. Seguimiento -->
<div class="subtitle mt8">Seguimiento del caso</div>
<table class="tbl tiny">
    <thead>
    <tr>
        <th>ÁREA</th>
        <th>RESPONSABLE</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>PSICOLÓGICA</td>
        <td>{{ d($caso->seguimiento_area) }}</td>
    </tr>
    <tr>
        <td>ÁREA SOCIAL</td>
        <td>{{ d($caso->seguimiento_area_social) }}</td>
    </tr>
    <tr>
        <td>ÁREA LEGAL</td>
        <td>{{ d($caso->seguimiento_area_legal) }}</td>
    </tr>
    </tbody>
</table>

</body>
</html>
