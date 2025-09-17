<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>UMADIS – Registro de Denuncias</title>
    <style>
        @page { margin: 16mm 14mm 18mm 14mm; }
        * { font-family: DejaVu Sans, sans-serif; color:#222; }
        body { font-size: 12px; }
        .w-100{width:100%} .b{font-weight:700} .upper{text-transform:uppercase}
        .muted{color:#555} .small{font-size:11px} .xs{font-size:10px}
        .text-center{text-align:center} .text-right{text-align:right}
        .mb-4{margin-bottom:12px} .mb-6{margin-bottom:18px}
        .table{width:100%; border-collapse:collapse}
        .table th,.table td{border:1px solid #444; padding:5px 6px; vertical-align:top}
        .table th{background:#f6f6f6}
        .grid td{padding:6px 6px}
        .section{font-weight:700; margin:10px 0 6px}
        .logos td{border:none}
        .box{display:inline-block; width:12px; height:12px; border:1px solid #444; line-height:12px; text-align:center; font-weight:700}
        .line{height:22px; border:1px solid #444}
        .sign{height:70px}
    </style>
</head>
<body>
@php
    $v = fn($x) => (isset($x) && $x !== '') ? $x : '—';
    $d = fn($x) => ($x ? \Illuminate\Support\Carbon::parse($x)->format('d/m/Y') : '—');
    $siNo = fn($b) => ($b===1 || $b===true || $b==='1') ? 'SI' : 'NO';
    $logoLeft  = public_path('img/escudo_gob.png');   // opcional
    $logoRight = public_path('img/logo_muni.png');    // opcional
@endphp

    <!-- ====== ENCABEZADO ====== -->
<table class="w-100 logos" style="margin-bottom:6px;">
    <tr>
        <td style="width:18%; text-align:left;">
            @if(is_file($logoLeft)) <img src="{{ $logoLeft }}" style="height:48px;"> @endif
        </td>
        <td style="width:64%; text-align:center;">
            <div class="b upper" style="font-size:14px;">SERVICIO LEGAL INTEGRAL MUNICIPAL (UMADIS)</div>
            <div class="b upper" style="font-size:14px;">REGISTRO DE DENUNCIAS</div>
        </td>
        <td style="width:18%; text-align:right;">
            @if(is_file($logoRight)) <img src="{{ $logoRight }}" style="height:48px;"> @endif
        </td>
    </tr>
</table>

<table class="table mb-4">
    <tr>
        <td style="width:30%"><span class="b">Fecha de registro:</span> {{ $d($umadi->fecha_registro) }}</td>
        <td style="width:20%"><span class="b">Área:</span> {{ $v($umadi->area) }}</td>
        <td style="width:20%"><span class="b">Zona/Módulo:</span> {{ $v($umadi->zona) }}</td>
        <td style="width:30%" class="text-right"><span class="b">CASO:</span> <span class="b">{{ $v($umadi->numero_caso) }}</span></td>
    </tr>
</table>

<!-- ====== 1. NOMBRE DEL DENUNCIANTE ====== -->
<div class="section upper">1.- Nombre del Denunciante</div>
<table class="table grid mb-4">
    <tr>
        <th style="width:34%">Nombre(s)</th>
        <th style="width:33%">Ap. Paterno</th>
        <th style="width:33%">Ap. Materno</th>
    </tr>
    <tr>
        <td>{{ $v($umadi->nombres) }}</td>
        <td>{{ $v($umadi->paterno) }}</td>
        <td>{{ $v($umadi->materno) }}</td>
    </tr>
</table>

<!-- ====== 2. DATOS DEL DENUNCIANTE ====== -->
<div class="section upper">2.- Datos del Denunciante</div>

<table class="table grid mb-4">
    <tr>
        <th style="width:26%">2.1 Documento</th>
        <th style="width:14%">Nro.</th>
        <th style="width:14%">2.3 Sexo</th>
        <th style="width:22%">2.4 Lugar de nacimiento</th>
        <th style="width:24%">2.5 Fecha de nacimiento</th>
    </tr>
    <tr>
        <td>{{ $v($umadi->tipo_documento) }}</td>
        <td>{{ $v($umadi->numero_documento) }}</td>
        <td>{{ $v($umadi->sexo) }}</td>
        <td>{{ $v($umadi->lugar_nacimiento) }}</td>
        <td>{{ $d($umadi->fecha_nacimiento) }}</td>
    </tr>
</table>

<table class="table grid mb-4">
    <tr>
        <th style="width:10%">2.6 Edad</th>
        <th style="width:32%">2.7 Residencia habitual</th>
        <th style="width:16%">2.8 Estado civil</th>
        <th style="width:22%">2.9 Relación con el denunciado</th>
        <th style="width:20%">2.10 Grado de instrucción</th>
    </tr>
    <tr>
        <td>{{ $v($umadi->edad) }}</td>
        <td>{{ $v($umadi->direccion) }}</td>
        <td>{{ $v($umadi->estado_civil) }}</td>
        <td>{{ $v($umadi->relacion_denunciado) }}</td>
        <td>{{ $v($umadi->grado_instruccion) }}</td>
    </tr>
</table>

<table class="table grid mb-4">
    <tr>
        <th style="width:12%">2.11 Trabaja</th>
        <th style="width:28%">Ocupación</th>
        <th style="width:15%">Aprox.</th>
        <th style="width:15%">Exacto</th>
        <th style="width:30%">2.13 Idioma</th>
    </tr>
    <tr>
        <td>{{ $siNo($umadi->trabaja) }}</td>
        <td>{{ $v($umadi->ocupacion) }}</td>
        <td>{{ $v($umadi->edad_aprox) }}</td>
        <td>{{ $v($umadi->edada_exacto) }}</td>
        <td>{{ $v($umadi->idioma) }}</td>
    </tr>
</table>

<table class="table grid mb-4">
    <tr>
        <th colspan="2" style="width:50%">2.14 Teléfonos de referencia</th>
        <th style="width:50%">2.15 Domicilio actual</th>
    </tr>
    <tr>
        <td style="width:25%">L. fija 1: {{ $v($umadi->telefono_fijo1) }}</td>
        <td style="width:25%">L. fija 2 / Esposo(a): {{ $v($umadi->telefono_fijo2) }}</td>
        <td rowspan="2">{{ $v($umadi->direccion_actual) }}</td>
    </tr>
    <tr>
        <td>Móvil 1: {{ $v($umadi->celular1) }}</td>
        <td>Móvil 2: {{ $v($umadi->celular2) }}</td>
    </tr>
</table>

<!-- ====== 3. GRUPO FAMILIAR ====== -->
<div class="section upper">3.- Grupo Familiar</div>
<table class="table grid mb-6">
    <tr>
        <th style="width:44%">Nombres y apellidos</th>
        <th style="width:10%">Edad</th>
        <th style="width:18%">Parentesco</th>
        <th style="width:10%">Sexo</th>
        <th style="width:18%">Celular / Teléfono</th>
    </tr>
    @forelse($umadi->familiares as $f)
        <tr>
            <td>{{ trim($v($f->nombre).' '.$v($f->paterno).' '.$v($f->materno)) }}</td>
            <td>{{ $v($f->edad) }}</td>
            <td>{{ $v($f->parentesco) }}</td>
            <td>{{ $v($f->sexo) }}</td>
            <td>{{ $v($f->telefono) }}</td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center muted">— Sin familiares —</td></tr>
    @endforelse
</table>

<!-- ====== 4. DATOS DE LA PERSONA DENUNCIADA ====== -->
<div class="section upper">4.- Datos de la Persona Denunciada</div>

<table class="table grid mb-4">
    <tr>
        <th style="width:34%">4.1 Nombre(s)</th>
        <th style="width:33%">Ap. Paterno</th>
        <th style="width:33%">Ap. Materno</th>
    </tr>
    <tr>
        <td>{{ $v($umadi->denunciado_nombres) }}</td>
        <td>{{ $v($umadi->denunciado_paterno) }}</td>
        <td>{{ $v($umadi->denunciado_materno) }}</td>
    </tr>
</table>

<table class="table grid mb-4">
    <tr>
        <th style="width:18%">4.2 C.I.</th>
        <th style="width:16%">4.3 Sexo</th>
        <th style="width:22%">4.4 Lugar de nacimiento</th>
        <th style="width:22%">4.5 Fecha de nacimiento</th>
        <th style="width:10%">4.6 Edad</th>
        <th style="width:12%">4.7 Idioma</th>
    </tr>
    <tr>
        <td>{{ $v($umadi->denunciado_ci) }}</td>
        <td>{{ $v($umadi->denunciado_sexo) }}</td>
        <td>{{ $v($umadi->denunciado_lugar_nacimiento ?: $umadi->denunciado_ciudad_nacimiento) }}</td>
        <td>{{ $d($umadi->denunciado_fecha_nacimiento) }}</td>
        <td>{{ $v($umadi->denunciado_edad) }}</td>
        <td>{{ $v($umadi->denunciado_idioma) }}</td>
    </tr>
</table>

<table class="table grid mb-4">
    <tr>
        <th style="width:26%">4.8 Grado de instrucción</th>
        <th style="width:26%">4.9 Ocupación</th>
        <th style="width:48%">4.10 Ingreso económico</th>
    </tr>
    <tr>
        <td>{{ $v($umadi->denunciado_grado_instruccion) }}</td>
        <td>{{ $v($umadi->denunciado_ocupacion) }}</td>
        <td></td>
    </tr>
</table>

<table class="table grid mb-4">
    <tr>
        <th style="width:32%">4.11 Relación con la denunciante</th>
        <th style="width:68%">Dirección actual</th>
    </tr>
    <tr>
        <td>{{ $v($umadi->relacion_denunciante) }}</td>
        <td>{{ $v($umadi->denunciado_direccion_actual ?: $umadi->denunciado_direccion) }}</td>
    </tr>
</table>

<!-- ====== 5. BREVE CIRCUNSTANCIA ====== -->
<div class="section upper">5.- Breve circunstancia del hecho o denuncia</div>
<table class="table grid mb-4">
    <tr>
        <td style="height:140px;">
            {!! nl2br(e($umadi->descripcion_hecho ?? '')) !!}
        </td>
    </tr>
</table>

<!-- ====== 6. TIPOLOGÍA DEL HECHO PRINCIPAL ====== -->
<div class="section upper">6.- Tipología del Hecho Principal</div>
<div class="line mb-6"></div>

<!-- ====== SEGUIMIENTO DEL CASO ====== -->
<div class="section upper">Seguimiento del caso</div>
<table class="table grid mb-6">
    <tr>
        <th style="width:33%">Área Psicológica</th>
        <th style="width:33%">Área Social</th>
        <th style="width:34%">Área Legal</th>
    </tr>
    <tr>
        <td><span class="box">{{ $umadi->psicologica_user_id ? 'X' : '' }}</span>
            <span class="small muted"> Responsable:</span> {{ $umadi->psicologica_user->name ?? '—' }}</td>
        <td><span class="box">{{ $umadi->trabajo_social_user_id ? 'X' : '' }}</span>
            <span class="small muted"> Responsable:</span> {{ $umadi->trabajo_social_user->name ?? '—' }}</td>
        <td><span class="box">{{ $umadi->legal_user_id ? 'X' : '' }}</span>
            <span class="small muted"> Responsable:</span> {{ $umadi->legal_user->name ?? '—' }}</td>
    </tr>
</table>

<!-- ====== FIRMAS ====== -->
<table class="w-100">
    <tr>
        <td style="width:60%"></td>
        <td class="text-center">
            <div class="sign"></div>
            <div class="xs">Firma del denunciante</div>
            <div class="xs">{{ trim(($umadi->nombres ?? '').' '.($umadi->paterno ?? '').' '.($umadi->materno ?? '')) }}</div>
            <div class="xs">Doc.: {{ $v($umadi->tipo_documento) }} {{ $v($umadi->numero_documento) }}</div>
        </td>
    </tr>
</table>

</body>
</html>
