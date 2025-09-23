{{-- resources/views/pdf/umadis/pdf.blade.php --}}
    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>UMADIS · Registro de Denuncias</title>
    <style>
        /* Dompdf usa el font que le pases desde el controller (DejaVu Sans recomendado) */
        *{ box-sizing: border-box; }
        body{ font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
        h1,h2,h3{ margin:0 0 6px 0; }
        .mb-2{ margin-bottom:8px; } .mb-3{ margin-bottom:12px; } .mb-4{ margin-bottom:16px; }
        .fw-700{ font-weight:700; } .ta-c{ text-align:center; } .ta-r{ text-align:right; }
        .small{ font-size: 11px; } .xs{ font-size:10px; }
        table{ border-collapse: collapse; width:100%; }
        th,td{ padding:6px 8px; vertical-align: top; }
        .b{ border:1px solid #000; } .bt{ border-top:1px solid #000; } .bb{ border-bottom:1px solid #000; }
        .bl{ border-left:1px solid #000; } .br{ border-right:1px solid #000; }
        .section-title{ background:#eee; border:1px solid #000; padding:6px 8px; font-weight:700; }
        .row{ display:flex; gap:8px; } .col{ flex:1; }
        .box{ border:1px solid #000; padding:6px 8px; }
        .line{ height:24px; border:1px solid #000; }
        .label{ color:#444; font-weight:700; }
        .hr{ height:1px; background:#000; margin:8px 0; }
        .muted{ color:#555; }
    </style>
</head>
<body>
@php
    $den = optional($caso->denunciantes->first());
    $denuNombre = trim(($den->denunciante_nombres ?? '').' '.($den->denunciante_paterno ?? '').' '.($den->denunciante_materno ?? ''));
    $denun = optional($caso->denunciados->first());
    $denunNombre = trim(($denun->denunciado_nombres ?? '').' '.($denun->denunciado_paterno ?? '').' '.($denun->denunciado_materno ?? ''));
    $familiares = $caso->familiares ?? collect();
    function v($x){ return ($x!==null && $x!=='') ? $x : '—'; }
@endphp

{{-- ENCABEZADO --}}
<table class="mb-4">
    <tr>
        <td class="b" style="width:70%;padding:10px">
            <div class="fw-700" style="font-size:16px">SERVICIO LEGAL INTEGRAL MUNICIPAL (UMADIS)</div>
            <div class="fw-700" style="font-size:15px">REGISTRO DE DENUNCIAS</div>
            <div class="small muted">Zona / Módulo: <b>{{ v($caso->zona ?: $caso->area) }}</b></div>
        </td>
        <td class="b" style="width:30%;padding:10px">
            <div><span class="label">Caso N°:</span> <b>{{ v($caso->caso_numero) }}</b></div>
            <div><span class="label">Fecha de registro:</span> {{ v(optional($caso->caso_fecha_hecho)->format('Y-m-d') ?? $caso->caso_fecha_hecho) }}</div>
            <div><span class="label">Registrado por:</span> {{ optional($caso->user)->name }}</div>
        </td>
    </tr>
</table>

{{-- 1) NOMBRE DEL DENUNCIANTE --}}
<div class="section-title">1.- Nombre del/la Denunciante</div>
<div class="box mb-3">{{ v($denuNombre) }}</div>

{{-- 2) DATOS DEL/LA DENUNCIANTE --}}
<div class="section-title">2.- Datos del/la Denunciante</div>
<table class="b mb-3">
    <tr>
        <td class="b" style="width:34%">
            <div class="label">2.1 Identificación</div>
            <div>Nombre(s): {{ v($den->denunciante_nombres) }}</div>
            <div>Ap. Paterno: {{ v($den->denunciante_paterno) }}</div>
            <div>Ap. Materno: {{ v($den->denunciante_materno) }}</div>
        </td>
        <td class="b" style="width:33%">
            <div class="label">2.2 Documento de identidad</div>
            <div>{{ v($den->denunciante_documento) }} &nbsp; N° {{ v($den->denunciante_nro) }}</div>
            <div class="label">2.3 Sexo</div>
            <div>{{ v($den->denunciante_sexo) }}</div>
            <div class="label">2.4 Lugar de nacimiento</div>
            <div>{{ v($den->denunciante_lugar_nacimiento) }}</div>
        </td>
        <td class="b" style="width:33%">
            <div class="label">2.5 Fecha de nacimiento</div>
            <div>{{ v($den->denunciante_fecha_nacimiento) }}</div>
            <div class="label">2.6 Edad</div>
            <div>{{ v($den->denunciante_edad) }}</div>
            <div class="label">2.7 Residencia habitual</div>
            <div>{{ v($den->denunciante_residencia) }}</div>
        </td>
    </tr>
    <tr>
        <td class="b">
            <div class="label">2.8 Estado civil</div>
            <div>{{ v($den->denunciante_estado_civil) }}</div>
            <div class="label">2.9 Relación con el denunciado</div>
            <div>{{ v($den->denunciante_relacion) }}</div>
        </td>
        <td class="b">
            <div class="label">2.10 Grado de instrucción</div>
            <div>{{ v($den->denunciante_grado) }}</div>
            <div class="label">2.11 Ocupación</div>
            <div>{{ v($den->denunciante_ocupacion) }}</div>
        </td>
        <td class="b">
            <div class="label">2.12 Trabaja</div>
            <div>{{ ($den->denunciante_trabaja ?? false) ? 'SI' : 'NO' }}</div>
            <div class="label">2.13 Idioma</div>
            <div>{{ v($den->denunciante_idioma) }}</div>
        </td>
    </tr>
    <tr>
        <td class="b">
            <div class="label">2.14 Teléfonos de referencia</div>
            <div>{{ v($den->denunciante_telefono) }}</div>
        </td>
        <td class="b" colspan="2">
            <div class="label">2.15 Domicilio actual</div>
            <div>{{ v($den->denunciante_domicilio_actual) }}</div>
        </td>
    </tr>
</table>

{{-- 3) GRUPO FAMILIAR --}}
<div class="section-title">3.- Grupo familiar</div>
<table class="b mb-3">
    <thead>
    <tr>
        <th class="b">N°</th>
        <th class="b">Nombres y Apellidos</th>
        <th class="b">Edad</th>
        <th class="b">Parentesco</th>
        <th class="b">Estado civil</th>
        <th class="b">Ocupación</th>
        <th class="b">Celular</th>
    </tr>
    </thead>
    <tbody>
    @forelse($familiares as $i=>$f)
        <tr>
            <td class="b" style="text-align:center">{{ $i+1 }}</td>
            <td class="b">
                {{ trim(($f->familiar_nombres ?? '').' '.($f->familiar_paterno ?? '').' '.($f->familiar_materno ?? '')) ?: '—' }}
            </td>
            <td class="b" style="text-align:center">{{ v($f->familiar_edad) }}</td>
            <td class="b">{{ v($f->familiar_parentesco) }}</td>
            <td class="b">{{ v($f->familiar_estado_civil) }}</td>
            <td class="b">{{ v($f->familiar_ocupacion) }}</td>
            <td class="b">{{ v($f->familiar_telefono) }}</td>
        </tr>
    @empty
        <tr><td class="b" colspan="7" style="text-align:center">Sin registros</td></tr>
    @endforelse
    </tbody>
</table>

{{-- 4) DATOS DE LA PERSONA DENUNCIADA --}}
<div class="section-title">4.- Datos de la persona denunciada</div>
<table class="b mb-3">
    <tr>
        <td class="b" style="width:34%">
            <div class="label">4.1 Identificación</div>
            <div>Nombre(s): {{ v($denun->denunciado_nombres) }}</div>
            <div>Ap. Paterno: {{ v($denun->denunciado_paterno) }}</div>
            <div>Ap. Materno: {{ v($denun->denunciado_materno) }}</div>
        </td>
        <td class="b" style="width:33%">
            <div class="label">4.2 Documento de identidad</div>
            <div>{{ v($denun->denunciado_documento) }} &nbsp; N° {{ v($denun->denunciado_nro) }}</div>
            <div class="label">4.3 Sexo</div>
            <div>{{ v($denun->denunciado_sexo) }}</div>
            <div class="label">4.4 Lugar de nacimiento</div>
            <div>{{ v($denun->denunciado_lugar_nacimiento) }}</div>
        </td>
        <td class="b" style="width:33%">
            <div class="label">4.5 Fecha de nacimiento</div>
            <div>{{ v($denun->denunciado_fecha_nacimiento) }}</div>
            <div class="label">4.6 Edad</div>
            <div>{{ v($denun->denunciado_edad) }}</div>
            <div class="label">4.7 Idioma</div>
            <div>{{ v($denun->denunciado_idioma) }}</div>
        </td>
    </tr>
    <tr>
        <td class="b">
            <div class="label">4.8 Grado de instrucción</div>
            <div>{{ v($denun->denunciado_grado) }}</div>
        </td>
        <td class="b">
            <div class="label">4.9 Ocupación</div>
            <div>{{ v($denun->denunciado_ocupacion) }}</div>
        </td>
        <td class="b">
            <div class="label">4.10 Ingreso económico</div>
            <div>{{ v($denun->denunciado_prox) }}</div>
        </td>
    </tr>
    <tr>
        <td class="b">
            <div class="label">4.11 Relación con la denunciante</div>
            <div>{{ v($denun->denunciado_relacion) }}</div>
        </td>
        <td class="b" colspan="2">
            <div class="label">Dirección actual</div>
            <div>{{ v($denun->denunciado_domicilio_actual) }}</div>
        </td>
    </tr>
</table>

{{-- 5) BREVE CIRCUNSTANCIA DEL HECHO --}}
<div class="section-title">5.- Breve circunstancia del hecho o denuncia</div>
<div class="box mb-3" style="min-height:90px">{{ v($caso->caso_descripcion) }}</div>

{{-- 6) TIPOLOGÍA DEL HECHO PRINCIPAL --}}
<div class="section-title">6.- Tipología del hecho principal</div>
<div class="box mb-4" style="min-height:36px">{{ v($caso->principal ?: $caso->caso_tipologia) }}</div>

{{-- 7) SEGUIMIENTO DEL CASO --}}
<div class="section-title">Seguimiento del caso</div>
<table class="b">
    <tr>
        <td class="b" style="width:33%"><b>Área psicológica:</b> {{ optional($caso->psicologica_user)->name ?: '—' }}</td>
        <td class="b" style="width:33%"><b>Área social:</b> {{ optional($caso->trabajo_social_user)->name ?: '—' }}</td>
        <td class="b" style="width:34%"><b>Área legal:</b> {{ optional($caso->legal_user)->name ?: '—' }}</td>
    </tr>
</table>

<div class="mb-4"></div>

{{-- Firmas --}}
<table>
    <tr>
        <td class="ta-c">
            <div class="hr"></div>
            <div class="small">Firma / Huella del denunciante</div>
        </td>
        <td class="ta-c">
            <div class="hr"></div>
            <div class="small">Firma del Abogado (UMADIS)</div>
        </td>
    </tr>
</table>
</body>
</html>
