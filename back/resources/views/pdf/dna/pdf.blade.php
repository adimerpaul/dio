{{-- resources/views/pdf/dna/pdf.blade.php --}}
    <!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro de Atención y/o Denuncia</title>
    <style>
        @page { margin: 16mm 14mm 18mm 14mm; }
        * { font-family: DejaVu Sans, sans-serif; color:#222; }
        body { font-size: 12px; }
        .w-100 { width:100%; }
        .mb-2 { margin-bottom:8px; }
        .mb-3 { margin-bottom:12px; }
        .mb-4 { margin-bottom:16px; }
        .mb-6 { margin-bottom:22px; }
        .text-center { text-align:center; }
        .text-right { text-align:right; }
        .upper { text-transform: uppercase; }
        .small { font-size: 11px; }
        .xs { font-size: 10px; }
        .b { font-weight:700; }
        .muted { color:#555; }
        .table { width:100%; border-collapse: collapse; }
        .table td, .table th { border:1px solid #444; padding:5px 6px; vertical-align: top; }
        .table th { background:#f6f6f6; }
        .no-border td, .no-border th { border:none; }
        .label { font-weight:700; white-space:nowrap; }
        .box { display:inline-block; width:12px; height:12px; border:1px solid #444; text-align:center; line-height:12px; font-weight:700; }
        .hr { height:1px; background:#555; }
        .section { font-weight:700; margin: 8px 0 6px; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
@php
    // Helpers seguros
    $v = fn($x) => (isset($x) && $x !== '' && $x !== null) ? $x : '—';
    $x = fn($b) => ($b===1 || $b===true || $b==='1' || $b==='SI' || $b==='Si' || $b==='si') ? 'X' : '';
    $fmtDate = function($d) {
        try { return $d ? \Illuminate\Support\Carbon::parse($d)->format('d/m/Y') : '—'; }
        catch (\Throwable $e) { return $d ?: '—'; }
    };

    // Tomamos primeros elementos cuando el diseño pide solo 1 registro
    $denunciante = ($caso->denunciantes[0] ?? null);
    $denunciado  = ($caso->denunciados[0] ?? null);

    // Domicilio/Teléfono generales (se usa del denunciante si existe)
    $domicilio = $denunciante->denunciante_domicilio_actual ?? null;
    $telefono  = $denunciante->denunciante_telefono ?? null;

    // Nombre completo helpers
    $nombreDenunciante = $denunciante
        ? trim(($denunciante->denunciante_nombres ?? '').' '.($denunciante->denunciante_paterno ?? '').' '.($denunciante->denunciante_materno ?? ''))
        : null;

    $nombreDenunciado = $denunciado
        ? trim(($denunciado->denunciado_nombres ?? '').' '.($denunciado->denunciado_paterno ?? '').' '.($denunciado->denunciado_materno ?? ''))
        : null;
@endphp

{{-- ====== CABECERA ====== --}}
<table class="table mb-3">
    <tr>
        <th style="width:36%; text-align:center;">
            <div class="b">+SID</div>
            <div class="small">Sistema de Información<br>de Defensorías</div>
        </th>
        <th style="width:44%; text-align:center;">
            <div class="b upper" style="font-size:14px;">Registro de Atención y/o Denuncia</div>
        </th>
        <th style="width:20%;">
            <div><span class="label">Fecha:</span> {{ $fmtDate($caso->fecha_apertura_caso ?? now()) }}</div>
            <div><span class="label">Código:</span> {{ $v($caso->caso_numero ?? $caso->id) }}</div>
            <div><span class="label">Nº Atención:</span> {{ $v($caso->id) }}</div>
        </th>
    </tr>
</table>

{{-- ====== 1. DATOS GENERALES ====== --}}
<div class="section upper">1.- Datos Generales</div>
<table class="table mb-3">
    <tr>
        <th style="width:14%;">Principal :</th>
        <td>{{ $v($caso->principal ?? $caso->tipo) }}</td>
    </tr>
    <tr>
        <th>Lugar del hecho :</th>
        <td>{{ $v($caso->caso_lugar_hecho) }}</td>
    </tr>
    <tr>
        <th>Fecha del hecho :</th>
        <td>{{ $fmtDate($caso->caso_fecha_hecho ?? '') }}</td>
    </tr>
</table>

{{-- ====== MENORES (mismo bloque/estilo) ====== --}}
<table class="table mb-3">
    <tr>
        <th style="width:4%;">N°</th>
        <th style="width:24%;">Nombres y Apellidos<br><span class="xs">(del menor)</span></th>
        <th style="width:7%;">Gestante</th>
        <th style="width:10%;">Edad</th>
        <th style="width:8%;">Sexo</th>
        <th style="width:8%;">C. Nac</th>
        <th style="width:8%;">Estudia</th>
        <th style="width:11%;">Último curso</th>
        <th style="width:20%;">Tipo de trabajo</th>
    </tr>
    @forelse($caso->menores as $i => $m)
        @php
            // Soporte flexible de campos posibles
            $nomMenor = $m->nombre ?? trim(($m->nombres ?? '').' '.($m->paterno ?? '').' '.($m->materno ?? ''));
            $edadA = $m->edad_anios ?? $m->edad ?? null;
            $edadM = $m->edad_meses ?? null;
            $sexo  = $m->sexo ?? null;
        @endphp
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>{{ $v($nomMenor) }}</td>
            <td class="text-center">
                <span class="box">{{ $x($m->gestante_si ?? 0) }}</span> <span class="xs">SI</span>
                <span class="box">{{ $x(isset($m->gestante_no) ? $m->gestante_no : (empty($m->gestante_si) ? 1:0)) }}</span> <span class="xs">NO</span>
            </td>
            <td>
                <div><span class="xs">AÑOS:</span> {{ $v($edadA) }}</div>
                <div><span class="xs">MESES:</span> {{ $v($edadM) }}</div>
            </td>
            <td class="text-center">
                <span class="box">{{ ($sexo==='M') ? 'X' : '' }}</span> <span class="xs">M</span>
                <span class="box">{{ ($sexo==='F') ? 'X' : '' }}</span> <span class="xs">F</span>
            </td>
            <td class="text-center">
                <span class="box">{{ $x($m->cert_nac ?? 0) }}</span> <span class="xs">SI</span>
                <span class="box">{{ $x(isset($m->cert_nac_no) ? $m->cert_nac_no : (empty($m->cert_nac) ? 1:0)) }}</span> <span class="xs">NO</span>
            </td>
            <td class="text-center">
                <span class="box">{{ $x($m->estudia ?? 0) }}</span> <span class="xs">SI</span>
                <span class="box">{{ $x(isset($m->estudia_no) ? $m->estudia_no : (empty($m->estudia) ? 1:0)) }}</span> <span class="xs">NO</span>
            </td>
            <td>{{ $v($m->ultimo_curso ?? '') }}</td>
            <td>{{ $v($m->tipo_trabajo ?? '') }}</td>
        </tr>
    @empty
        <tr><td colspan="9" class="text-center muted">— Sin menores registrados —</td></tr>
    @endforelse
</table>

<table class="table mb-4">
    <tr>
        <th style="width:12%;">Domicilio:</th>
        <td style="width:58%">{{ $v($domicilio) }}</td>
        <th style="width:12%;">Teléfono:</th>
        <td style="width:18%">{{ $v($telefono) }}</td>
    </tr>
</table>

{{-- ====== 3. DATOS DEL GRUPO FAMILIAR ====== --}}
<div class="section upper">3.- Datos del Grupo Familiar</div>
<table class="table mb-4">
    <tr>
        <th style="width:6%;">N°</th>
        <th style="width:34%;">Nombres y Apellidos</th>
        <th style="width:14%;">Parentesco</th>
        <th style="width:10%;">Edad</th>
        <th style="width:10%;">Sexo</th>
        <th style="width:14%;">G. Instrucción</th>
        <th style="width:12%;">Ocupación</th>
    </tr>
    @forelse($caso->familiares as $i => $f)
        @php
            $nombreFam = $f->familiar_nombre_completo
                ?? trim(($f->familiar_nombres ?? '').' '.($f->familiar_paterno ?? '').' '.($f->familiar_materno ?? ''));
        @endphp
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>{{ $v($nombreFam) }}</td>
            <td>{{ $v($f->familiar_parentesco ?? '') }}</td>
            <td>{{ $v($f->familiar_edad ?? '') }}</td>
            <td>{{ $v($f->familiar_sexo ?? '') }}</td>
            <td>{{ $v($f->familiar_grado ?? '') }}</td>
            <td>{{ $v($f->familiar_ocupacion ?? '') }}</td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center muted">— Sin familiares registrados —</td></tr>
    @endforelse
</table>

{{-- ====== 5. DATOS DEL DENUNCIADO (primer denunciado para mantener el formato) ====== --}}
<div class="section upper">5.- Datos del Denunciado</div>
<table class="table mb-4">
    <tr>
        <th style="width:24%;">Nombres y Apellidos</th>
        <td style="width:40%">{{ $v($nombreDenunciado) }}</td>
        <th style="width:8%;">Sexo</th>
        <td style="width:8%">{{ $v($denunciado->denunciado_sexo ?? '') }}</td>
        <th style="width:8%;">Edad</th>
        <td style="width:12%">{{ $v($denunciado->denunciado_edad ?? '') }}</td>
    </tr>
    <tr>
        <th>Parentesco o tipo de relación</th>
        <td>{{ $v($denunciado->denunciado_relacion ?? '') }}</td>
        <th>C.I.</th>
        <td colspan="3">{{ $v(($denunciado->denunciado_documento ?? '').' '.($denunciado->denunciado_nro ?? '')) }}</td>
    </tr>
    <tr>
        <th>Domicilio (zona/comunidad)</th>
        <td>{{ $v($denunciado->denunciado_domicilio_actual ?? '') }}</td>
        <th>Teléfono</th>
        <td>{{ $v($denunciado->denunciado_telefono ?? '') }}</td>
        <th>Lugar de Trabajo</th>
        <td>{{ $v($denunciado->denunciado_lugar_trabajo ?? '') }}</td>
    </tr>
    <tr>
        <th>Ocupación</th>
        <td colspan="5">{{ $v($denunciado->denunciado_ocupacion ?? '') }}</td>
    </tr>
</table>

{{-- ====== DATOS DEL DENUNCIANTE (primer denunciante para mantener el formato) ====== --}}
<div class="section upper">Datos del Denunciante</div>
<table class="table mb-4">
    <tr>
        <th style="width:24%;">Nombre y Apellido</th>
        <td style="width:40%">{{ $v($nombreDenunciante) }}</td>
        <th style="width:8%;">Sexo</th>
        <td style="width:8%">{{ $v($denunciante->denunciante_sexo ?? '') }}</td>
        <th style="width:8%;">Edad</th>
        <td style="width:12%">{{ $v($denunciante->denunciante_edad ?? '') }}</td>
    </tr>
    <tr>
        <th>C.I.</th>
        <td>{{ $v(($denunciante->denunciante_documento ?? '').' '.($denunciante->denunciante_nro ?? '')) }}</td>
        <th>Domicilio</th>
        <td colspan="3">{{ $v($denunciante->denunciante_domicilio_actual ?? '') }}</td>
    </tr>
    <tr>
        <th>Teléfono</th>
        <td>{{ $v($denunciante->denunciante_telefono ?? '') }}</td>
        <th>Lugar de Trabajo</th>
        <td>{{ $v($denunciante->denunciante_lugar_trabajo ?? '') }}</td>
        <th>Ocupación</th>
        <td>{{ $v($denunciante->denunciante_ocupacion ?? '') }}</td>
    </tr>
</table>

{{-- ====== 6. DESCRIPCIÓN DE LA DENUNCIA ====== --}}
<div class="section upper">6.- Descripción de la denuncia</div>
<table class="table mb-6">
    <tr>
        <td style="height:160px;">
            {!! nl2br(e($caso->caso_descripcion ?? '')) !!}
        </td>
    </tr>
</table>

{{-- ====== FIRMAS ====== --}}
<table class="table no-border">
    <tr>
        <td style="width:50%; padding-right:20px;">
            <div class="hr"></div>
            <div class="small">DENUNCIANTE:</div>
            <div class="small">Nombre: ..............................................................</div>
            <div class="small">Firma / huella digital</div>
            <div class="small">C.I.: ..................................................................</div>
        </td>
        <td style="width:50%; padding-left:20px;">
            <div class="hr"></div>
            <div class="small">Firma</div>
            <div class="small">Asistente Legal del D.N.A</div>
        </td>
    </tr>
</table>

<table class="table no-border" style="margin-top:26px;">
    <tr>
        <td style="width:50%; padding-right:20px;">
            <div class="small b">ABOGADO ASIGNADO AL CASO:</div>
            <div class="hr"></div>
            <div class="small">Dr. (a)</div>
        </td>
        <td style="width:50%; padding-left:20px;">
            <div class="hr"></div>
            <div class="small">Firma del Abogado</div>
        </td>
    </tr>
</table>

</body>
</html>
