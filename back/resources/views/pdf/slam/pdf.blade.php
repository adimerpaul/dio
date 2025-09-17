<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>SLAM – Registro de Denuncias</title>
    <style>
        @page { margin: 18mm 16mm 18mm 16mm; }
        * { font-family: DejaVu Sans, sans-serif; color:#222; }
        body { font-size: 12px; }
        h1,h2,h3 { margin:0; padding:0; }
        .w-100 { width:100%; }
        .mb-4 { margin-bottom: 12px; }
        .mb-6 { margin-bottom: 18px; }
        .text-center { text-align:center; }
        .text-right { text-align:right; }
        .upper { text-transform: uppercase; }
        .small { font-size: 11px; }
        .xs    { font-size: 10px; }
        .b { font-weight: 700; }
        .table { width:100%; border-collapse: collapse; }
        .table td, .table th { border:1px solid #444; padding:5px 6px; vertical-align: top; }
        .table th { background:#f6f6f6; }
        .grid td { padding:6px 6px; }
        .label { font-weight:700; white-space:nowrap; }
        .box { display:inline-block; width:12px; height:12px; border:1px solid #444; text-align:center; line-height:12px; font-weight:700; }
        .section-title { font-weight:700; margin: 8px 0 6px; }
        .page-break { page-break-before: always; }
        .logos td{ border:none; }
        .muted { color:#555; }
        .hr { height:1px; background:#666; margin:6px 0 0 0; }
    </style>
</head>
<body>
@php
    // helpers rápidos
    $v = fn($x) => (isset($x) && $x !== '') ? $x : '—';
    $x = fn($b) => ($b===1 || $b===true || $b==='1') ? 'X' : '';
    // imagenes locales (puedes cambiar nombres/rutas)
    $logoLeft  = public_path('img/escudo_gob.png');
    $logoRight = public_path('img/logo_muni.png');
@endphp

    <!-- ====== ENCABEZADO ====== -->
<table class="w-100 logos" style="margin-bottom:6px;">
    <tr>
        <td style="width:18%; text-align:left;">
            @if(is_file($logoLeft))
                <img src="{{ $logoLeft }}" style="height:48px;">
            @endif
        </td>
        <td style="width:64%; text-align:center;">
            <div class="b upper" style="font-size:14px; letter-spacing:.3px;">SERVICIO LEGAL INTEGRAL DE APOYO</div>
            <div class="b upper" style="font-size:14px;">ADULTO MAYOR (S.L.A.M.)</div>
            <div class="upper small muted">Registro de denuncias</div>
        </td>
        <td style="width:18%; text-align:right;">
            @if(is_file($logoRight))
                <img src="{{ $logoRight }}" style="height:48px;">
            @endif
        </td>
    </tr>
</table>

<table class="table mb-6">
    <tr>
        <td style="width:35%"><span class="label">Fecha de registro:</span> {{ $v($slam->fecha_registro) }}</td>
        <td style="width:25%"><span class="label">Área:</span> {{ $v($slam->area) }}</td>
        <td style="width:20%"><span class="label">Zona:</span> {{ $v($slam->zona) }}</td>
        <td style="width:20%"><span class="label">N° de caso:</span> <span class="b">{{ $v($slam->numero_caso) }}</span></td>
    </tr>
</table>

<!-- ====== 1. DATOS DEL ADULTO MAYOR ====== -->
<div class="section-title upper">1. Datos del adulto mayor</div>

<table class="table grid mb-4">
    <tr>
        <th style="width:32%">Nombre(s)</th>
        <th style="width:18%">Ap. Paterno</th>
        <th style="width:18%">Ap. Materno</th>
        <th style="width:16%">Fecha nac.</th>
        <th style="width:16%">Edad</th>
    </tr>
    @forelse($slam->adultos as $a)
        <tr>
            <td>{{ $v($a->nombre) }}</td>
            <td>{{ $v($a->paterno) }}</td>
            <td>{{ $v($a->materno) }}</td>
            <td>{{ $a->fecha_nacimiento ? \Illuminate\Support\Carbon::parse($a->fecha_nacimiento)->format('d/m/Y') : '—' }}</td>
            <td>{{ $v($a->edad) }}</td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center muted">— Sin adultos registrados —</td></tr>
    @endforelse
</table>

<table class="table grid mb-4">
    <tr>
        <th style="width:25%">Documento (CI u otro)</th>
        <th style="width:30%">Lugar de nacimiento</th>
        <th style="width:45%">Domicilio</th>
    </tr>
    @forelse($slam->adultos as $a)
        <tr>
            <td>{{ trim(($a->documento_tipo ? $a->documento_tipo.': ' : '').$v($a->documento_num)) }}</td>
            <td>{{ $v($a->lugar_nacimiento) }}</td>
            <td>{{ $v($a->domicilio) }}</td>
        </tr>
    @empty
        <tr><td colspan="3" class="text-center muted">—</td></tr>
    @endforelse
</table>

<table class="table grid mb-4">
    <tr>
        <th style="width:25%">Estado civil</th>
        <th style="width:25%">Ocupación 1</th>
        <th style="width:25%">Ocupación 2</th>
        <th style="width:25%">Ingresos</th>
    </tr>
    @forelse($slam->adultos as $a)
        <tr>
            <td>{{ $v($a->estado_civil) }}</td>
            <td>{{ $v($a->ocupacion_1) }}</td>
            <td>{{ $v($a->ocupacion_2) }}</td>
            <td>{{ $v($a->ingresos) }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-center muted">—</td></tr>
    @endforelse
</table>

<table class="table grid mb-4">
    <tr>
        <th style="width:50%">2.8 Idioma</th>
        <th style="width:50%">2.9 Teléfonos de referencia</th>
    </tr>
    <tr>
        <td>
            <div>C<span class="xs">ASTELLANO</span>: <span class="box">{{ $x($slam->am_idioma_castellano) }}</span></div>
            <div>Q<span class="xs">UECHUA</span>: <span class="box">{{ $x($slam->am_idioma_quechua) }}</span></div>
            <div>A<span class="xs">YMARA</span>: <span class="box">{{ $x($slam->am_idioma_aymara) }}</span></div>
            <div>O<span class="xs">TROS</span>: {{ $v($slam->am_idioma_otros) }}</div>
        </td>
        <td>
            <div><span class="label">N° fijo:</span> {{ $v($slam->ref_tel_fijo) }}</div>
            <div><span class="label">N° móvil:</span> {{ $v($slam->ref_tel_movil) }}</div>
            <div><span class="label">N° móvil (alt):</span> {{ $v($slam->ref_tel_movil_alt) }}</div>
        </td>
    </tr>
</table>

<!-- ====== 3. GRUPO FAMILIAR ====== -->
<div class="section-title upper">3. Grupo familiar</div>
<table class="table grid mb-6">
    <tr>
        <th style="width:38%">Nombres y apellidos</th>
        <th style="width:10%">Edad</th>
        <th style="width:18%">Parentesco</th>
        <th style="width:12%">Sexo</th>
        <th style="width:22%">Teléfono</th>
    </tr>
    @forelse($slam->familiares as $f)
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

<div class="page-break"></div>

<!-- ====== 4. DATOS DEL DENUNCIADO/A ====== -->
<div class="section-title upper">4. Datos del denunciado/a</div>
<table class="table grid mb-4">
    <tr>
        <th style="width:32%">Nombre(s)</th>
        <th style="width:18%">Ap. Paterno</th>
        <th style="width:18%">Ap. Materno</th>
        <th style="width:12%">Edad</th>
        <th style="width:20%">Estado civil</th>
    </tr>
    <tr>
        <td>{{ $v($slam->den_nombres) }}</td>
        <td>{{ $v($slam->den_paterno) }}</td>
        <td>{{ $v($slam->den_materno) }}</td>
        <td>{{ $v($slam->den_edad) }}</td>
        <td>{{ $v($slam->den_estado_civil) }}</td>
    </tr>
</table>

<table class="table grid mb-4">
    <tr>
        <th style="width:45%">Domicilio</th>
        <th style="width:15%">Idioma</th>
        <th style="width:20%">Grado de instrucción</th>
        <th style="width:20%">Ocupación</th>
    </tr>
    <tr>
        <td>{{ $v($slam->den_domicilio) }}</td>
        <td>{{ $v($slam->den_idioma) }}</td>
        <td>{{ $v($slam->den_grado_instruccion) }}</td>
        <td>{{ $v($slam->den_ocupacion) }}</td>
    </tr>
</table>

<!-- ====== 5. BREVE CIRCUNSTANCIA DEL HECHO ====== -->
<div class="section-title upper">5. Breve circunstancia del hecho o denuncia</div>
<table class="table grid mb-4">
    <tr>
        <td style="height:140px;">
            {!! nl2br(e($slam->hecho_descripcion ?? '')) !!}
        </td>
    </tr>
</table>

<!-- ====== 6. TIPOLOGÍA ====== -->
<div class="section-title upper">6. Tipología</div>
<table class="table grid mb-4">
    <tr>
        <th style="width:40%">Violencia física</th>
        <th style="width:40%">Violencia psicológica</th>
        <th style="width:20%">Abandono</th>
    </tr>
    <tr>
        <td><span class="box">{{ $x($slam->tip_violencia_fisica) }}</span></td>
        <td><span class="box">{{ $x($slam->tip_violencia_psicologica) }}</span></td>
        <td><span class="box">{{ $x($slam->tip_abandono) }}</span></td>
    </tr>
    <tr>
        <th colspan="3">Apoyo integral</th>
    </tr>
    <tr>
        <td colspan="3"><span class="box">{{ $x($slam->tip_apoyo_integral) }}</span></td>
    </tr>
</table>

<!-- ====== 7. SEGUIMIENTO DEL CASO ====== -->
<div class="section-title upper">7. Seguimiento del caso</div>
<table class="table grid">
    <tr>
        <th style="width:33%">Trabajo legal</th>
        <th style="width:33%">Trabajo social</th>
        <th style="width:34%">Psicológico</th>
    </tr>
    <tr>
        <td>
            <div><span class="box">{{ $slam->legal_user_id ? 'X' : '' }}</span>
                <span class="small muted"> Responsable:</span> {{ $slam->legal_user->name ?? '—' }}</div>
        </td>
        <td>
            <div><span class="box">{{ $slam->trabajo_social_user_id ? 'X' : '' }}</span>
                <span class="small muted"> Responsable:</span> {{ $slam->trabajo_social_user->name ?? '—' }}</div>
        </td>
        <td>
            <div><span class="box">{{ $slam->psicologica_user_id ? 'X' : '' }}</span>
                <span class="small muted"> Responsable:</span> {{ $slam->psicologica_user->name ?? '—' }}</div>
        </td>
    </tr>
</table>

</body>
</html>
