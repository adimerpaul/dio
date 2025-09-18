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
    // helpers
    $v = fn($x) => (isset($x) && $x !== '') ? $x : '—';
    $x = fn($b) => ($b===1 || $b===true || $b==='1' || $b==='SI' || $b==='Si' || $b==='si') ? 'X' : '';
    $fmtDate = function($d) {
        try { return $d ? \Illuminate\Support\Carbon::parse($d)->format('d/m/Y') : '—'; }
        catch (\Throwable $e) { return $d ?: '—'; }
    };
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
            <div><span class="label">Fecha:</span> {{ $fmtDate($dna->fecha_atencion ?? now()) }}</div>
            <div><span class="label">Código:</span> {{ $v($dna->codigo ?? $dna->id) }}</div>
            <div><span class="label">Nº Atención:</span> {{ $v($dna->id) }}</div>
        </th>
    </tr>
</table>

{{-- ====== 1. DATOS GENERALES ====== --}}
<div class="section upper">1.- Datos Generales</div>
<table class="table mb-3">
    <tr>
        <th style="width:14%;">Principal :</th>
        <td>{{ $v($dna->principal ?? $dna->tipo_proceso) }}</td>
    </tr>
</table>

{{-- ====== MENORES (como en el bloque central de la planilla) ====== --}}
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
    @forelse($dna->menores as $i => $m)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>{{ $v($m->nombre ?? '') }}</td>
            <td class="text-center">
                <span class="box">{{ $x($m->gestante_si ?? 0) }}</span> <span class="xs">SI</span>
                <span class="box">{{ $x(isset($m->gestante_no) ? $m->gestante_no : (empty($m->gestante_si) ? 1:0)) }}</span> <span class="xs">NO</span>
            </td>
            <td>
                <div><span class="xs">AÑOS:</span> {{ $v($m->edad_anios ?? $m->edad ?? '') }}</div>
                <div><span class="xs">MESES:</span> {{ $v($m->edad_meses ?? '') }}</div>
            </td>
            <td class="text-center">
                <span class="box">{{ ($m->sexo ?? '')==='M' ? 'X' : '' }}</span> <span class="xs">M</span>
                <span class="box">{{ ($m->sexo ?? '')==='F' ? 'X' : '' }}</span> <span class="xs">F</span>
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
        <td style="width:58%">{{ $v($dna->domicilio) }}</td>
        <th style="width:12%;">Teléfono:</th>
        <td style="width:18%">{{ $v($dna->telefono) }}</td>
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
    @forelse($dna->familiares as $i => $f)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>{{ trim(($f->nombre ?? '').' '.($f->paterno ?? '').' '.($f->materno ?? '')) ?: '—' }}</td>
            <td>{{ $v($f->parentesco ?? '') }}</td>
            <td>{{ $v($f->edad ?? '') }}</td>
            <td>{{ $v($f->sexo ?? '') }}</td>
            <td>{{ $v($f->grado_instruccion ?? '') }}</td>
            <td>{{ $v($f->ocupacion ?? '') }}</td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center muted">— Sin familiares registrados —</td></tr>
    @endforelse
</table>

{{-- ====== 5. DATOS DEL DENUNCIADO ====== --}}
<div class="section upper">5.- Datos del Denunciado</div>
<table class="table mb-4">
    <tr>
        <th style="width:24%;">Nombres y Apellidos</th>
        <td style="width:40%">{{ $v($dna->denunciado_nombre) }}</td>
        <th style="width:8%;">Sexo</th>
        <td style="width:8%">{{ $v($dna->denunciado_sexo) }}</td>
        <th style="width:8%;">Edad</th>
        <td style="width:12%">{{ $v($dna->denunciado_edad) }}</td>
    </tr>
    <tr>
        <th>Parentesco o tipo de relación</th>
        <td>{{ $v($dna->denunciado_relacion) }}</td>
        <th>C.I.</th>
        <td colspan="3">{{ $v($dna->denunciado_ci) }}</td>
    </tr>
    <tr>
        <th>Domicilio (zona/comunidad)</th>
        <td>{{ $v($dna->denunciado_domicilio) }}</td>
        <th>Teléfono</th>
        <td>{{ $v($dna->denunciado_telefono) }}</td>
        <th>Lugar de Trabajo</th>
        <td>{{ $v($dna->denunciado_lugar_trabajo) }}</td>
    </tr>
    <tr>
        <th>Ocupación</th>
        <td colspan="5">{{ $v($dna->denunciado_ocupacion) }}</td>
    </tr>
</table>

{{-- ====== DATOS DEL DENUNCIANTE ====== --}}
<div class="section upper">Datos del Denunciante</div>
<table class="table mb-4">
    <tr>
        <th style="width:24%;">Nombre y Apellido</th>
        <td style="width:40%">{{ $v($dna->denunciante_nombre) }}</td>
        <th style="width:8%;">Sexo</th>
        <td style="width:8%">{{ $v($dna->denunciante_sexo) }}</td>
        <th style="width:8%;">Edad</th>
        <td style="width:12%">{{ $v($dna->denunciante_edad) }}</td>
    </tr>
    <tr>
        <th>C.I.</th>
        <td>{{ $v($dna->denunciante_ci) }}</td>
        <th>Domicilio</th>
        <td colspan="3">{{ $v($dna->denunciante_domicilio) }}</td>
    </tr>
    <tr>
        <th>Teléfono</th>
        <td>{{ $v($dna->denunciante_telefono) }}</td>
        <th>Lugar de Trabajo</th>
        <td>{{ $v($dna->denunciante_lugar_trabajo) }}</td>
        <th>Ocupación</th>
        <td>{{ $v($dna->denunciante_ocupacion) }}</td>
    </tr>
</table>

{{-- ====== 6. DESCRIPCIÓN DE LA DENUNCIA ====== --}}
<div class="section upper">6.- Descripción de la denuncia</div>
<table class="table mb-6">
    <tr>
        <td style="height:160px;">
            {!! nl2br(e($dna->descripcion ?? '')) !!}
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
