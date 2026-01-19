{{-- resources/views/pdf/slam/pdf.blade.php --}}
@php
    function d($v, $def=''){ return ($v !== null && $v !== '') ? $v : $def; }
    function fdate($v){
        try { return $v ? \Carbon\Carbon::parse($v)->format('d/m/Y') : ''; }
        catch (\Throwable $e) { return $v ?: ''; }
    }
    function dda($v){
        try { return $v ? \Carbon\Carbon::parse($v)->format('d') : ''; }
        catch (\Throwable $e) { return ''; }
    }
    function mm($v){
        try { return $v ? \Carbon\Carbon::parse($v)->format('m') : ''; }
        catch (\Throwable $e) { return ''; }
    }
    function yy($v){
        try { return $v ? \Carbon\Carbon::parse($v)->format('Y') : ''; }
        catch (\Throwable $e) { return ''; }
    }
    function x($b){ return ($b===1 || $b===true || $b==='1' || $b==='SI' || $b==='Si' || $b==='si') ? 'X' : ''; }
@endphp

    <!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>SLAM — Registro de Denuncias</title>
    <style>
        @page { margin: 16mm 14mm 16mm 14mm; }
        * { font-family: "DejaVu Sans", sans-serif; font-size: 10px; color:#111; }
        .center{ text-align:center; }
        .right{ text-align:right; }
        .b{ font-weight:700; }
        .upper{ text-transform: uppercase; }
        .tiny{ font-size: 9px; }
        .xxs{ font-size: 8px; }
        table.tbl{ width:100%; border-collapse: collapse; }
        table.tbl th, table.tbl td{ border:1px solid #000; padding:2px 3px; vertical-align: top; }
        table.tbl th{ font-weight:700; }
        .no-border td{ border:none !important; }
        .box{ display:inline-block; width:12px; height:12px; border:1px solid #000; text-align:center; line-height:12px; font-weight:700; }
        .mb4{ margin-bottom:4px; }
        .mb8{ margin-bottom:8px; }
        .avoid{ page-break-inside: avoid; }
        .clip{ word-break: break-word; }
    </style>
</head>
<body>

{{-- ===== ENCABEZADO (como formulario) ===== --}}
<table class="tbl mb4">
    <tr>
        <td style="width:15%;" class="center">
            {{-- Si tienes logos, los pones aquí; si no, queda vacío como tu caso --}}
            <div class="b"> </div>
        </td>
        <td style="width:70%;" class="center">
            <div class="b upper">SERVICIO LEGAL INTEGRAL DE APOYO</div>
            <div class="b upper">ADULTO MAYOR (S.L.A.M.)</div>
            <div class="b upper">REGISTRO DE DENUNCIAS</div>
        </td>
        <td style="width:15%;" class="center">
            <div class="b"> </div>
        </td>
    </tr>
</table>

<table class="tbl mb8">
    <tr>
        <td style="width:35%;">
            <span class="b upper">FECHA DE REGISTRO:</span> {{ fdate($caso->fecha_registro ?? $caso->fecha_apertura_caso) }}
        </td>
        <td style="width:35%;">
            <span class="b upper">N° DE CASO:</span> {{ d($caso->numero_caso, d($caso->caso_numero, $caso->id)) }}
        </td>
        <td style="width:30%;" class="right">
            <span class="b upper">ZONA:</span> {{ d($caso->zona) }}
        </td>
    </tr>
</table>

{{-- ===== 1. DATOS DEL ADULTO MAYOR ===== --}}
<div class="b upper mb4">1. DATOS DEL ADULTO MAYOR</div>

<table class="tbl mb4 avoid">
    <tr class="center b">
        <td style="width:26%;">Nombre(s)</td>
        <td style="width:22%;">Ap. Paterno</td>
        <td style="width:22%;">Ap. Materno</td>
        <td style="width:30%;"> </td>
    </tr>
    <tr>
        <td>{{ d(optional($caso->denunciantes[0] ?? null)->denunciante_nombres) }}</td>
        <td>{{ d(optional($caso->denunciantes[0] ?? null)->denunciante_paterno) }}</td>
        <td>{{ d(optional($caso->denunciantes[0] ?? null)->denunciante_materno) }}</td>
        <td></td>
    </tr>
</table>

<div class="b upper mb4">2. DOCUMENTOS DE IDENTIDAD</div>
<table class="tbl mb4 avoid">
    <tr class="center b">
        <td style="width:8%;">Día</td>
        <td style="width:8%;">Mes</td>
        <td style="width:12%;">Año</td>
        <td style="width:18%;">N° de C.I.</td>
        <td style="width:18%;"> </td>
        <td style="width:36%;">Lugar</td>
    </tr>
    <tr class="center">
        <td>{{ dda(optional($caso->denunciantes[0] ?? null)->denunciante_fecha_nacimiento) }}</td>
        <td>{{ mm(optional($caso->denunciantes[0] ?? null)->denunciante_fecha_nacimiento) }}</td>
        <td>{{ yy(optional($caso->denunciantes[0] ?? null)->denunciante_fecha_nacimiento) }}</td>
        <td colspan="2">{{ d(optional($caso->denunciantes[0] ?? null)->denunciante_nro) }}</td>
        <td style="text-align:left;">{{ d(optional($caso->denunciantes[0] ?? null)->denunciante_lugar_nacimiento) }}</td>
    </tr>
</table>

<table class="tbl mb4 avoid">
    <tr class="center b">
        <td style="width:12%;">2.1 Edad</td>
        <td style="width:58%;">2.4 Domicilio</td>
        <td style="width:30%;">2.5 Estado civil</td>
    </tr>
    <tr>
        <td class="center">{{ d(optional($caso->denunciantes[0] ?? null)->denunciante_edad) }}</td>
        <td>{{ d(optional($caso->denunciantes[0] ?? null)->denunciante_domicilio_actual) }}</td>
        <td class="center">{{ d(optional($caso->denunciantes[0] ?? null)->denunciante_estado_civil) }}</td>
    </tr>
</table>

<table class="tbl mb4 avoid">
    <tr class="center b">
        <td style="width:50%;">2.6 Ocupación</td>
        <td style="width:50%;">2.7 Ingresos económicos</td>
    </tr>
    <tr>
        <td>
            {{ d(optional($caso->denunciantes[0] ?? null)->denunciante_ocupacion) }}
        </td>
        <td>
            {{ d(optional($caso)->ingreso_economico) }}
        </td>
    </tr>
</table>

<table class="tbl mb8 avoid">
    <tr class="center b">
        <td style="width:45%;">2.8 Idioma</td>
        <td style="width:55%;">2.9 Teléfonos de referencia</td>
    </tr>
    <tr>
        <td>
            @php $idi = strtolower(d(optional($caso->denunciantes[0] ?? null)->denunciante_idioma)); @endphp
            <div>Castellano <span class="box">{{ x(str_contains($idi,'cast')) }}</span></div>
            <div>Quechua <span class="box">{{ x(str_contains($idi,'que')) }}</span></div>
            <div>Aymara <span class="box">{{ x(str_contains($idi,'aym')) }}</span></div>
            <div>Otros: {{ (!str_contains($idi,'cast') && !str_contains($idi,'que') && !str_contains($idi,'aym')) ? d(optional($caso->denunciantes[0] ?? null)->denunciante_idioma) : '' }}</div>
        </td>
        <td>
            <table class="tbl">
                <tr class="center b">
                    <td style="width:50%;">N° fijo</td>
                    <td style="width:50%;">N° móvil</td>
                </tr>
                <tr>
                    <td class="center">{{ d($caso->ref_tel_fijo) }}</td>
                    <td class="center">{{ d($caso->ref_tel_movil, d(optional($caso->denunciantes[0] ?? null)->denunciante_telefono)) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ===== 3. GRUPO FAMILIAR ===== --}}
<div class="b upper mb4">3. GRUPO FAMILIAR</div>
<table class="tbl mb8 avoid">
    <tr class="center b">
        <td style="width:40%;">NOMBRES Y APELLIDOS</td>
        <td style="width:10%;">EDAD</td>
        <td style="width:15%;">PARENTESCO</td>
        <td style="width:15%;">ESTADO CIVIL</td>
        <td style="width:20%;">OCUPACIÓN</td>
    </tr>
    @forelse($caso->familiares as $f)
        <tr>
            <td class="clip">{{ d($f->familiar_nombre_completo, trim(d($f->familiar_nombres).' '.d($f->familiar_paterno).' '.d($f->familiar_materno))) }}</td>
            <td class="center">{{ d($f->familiar_edad) }}</td>
            <td class="center">{{ d($f->familiar_parentesco) }}</td>
            <td class="center">{{ d($f->familiar_estado_civil) }}</td>
            <td class="center">{{ d($f->familiar_ocupacion) }}</td>
        </tr>
    @empty
        <tr><td colspan="5" class="center">—</td></tr>
    @endforelse
</table>

{{--<div style="page-break-before:always;"></div>--}}

{{-- ===== 4. DATOS DEL DENUNCIADO/A ===== --}}
<div class="b upper mb4">4. DATOS DEL DENUNCIADO/A</div>
<table class="tbl mb4 avoid">
    <tr class="center b">
        <td style="width:40%;">Nombre(s)</td>
        <td style="width:20%;">Ap. Paterno</td>
        <td style="width:20%;">Ap. Materno</td>
        <td style="width:20%;"> </td>
    </tr>
    <tr>
        <td>{{ d(optional($caso->denunciados[0] ?? null)->denunciado_nombres) }}</td>
        <td>{{ d(optional($caso->denunciados[0] ?? null)->denunciado_paterno) }}</td>
        <td>{{ d(optional($caso->denunciados[0] ?? null)->denunciado_materno) }}</td>
        <td></td>
    </tr>
</table>

<table class="tbl mb4 avoid">
    <tr class="center b">
        <td style="width:10%;">4.2 Edad</td>
        <td style="width:55%;">4.3 Domicilio</td>
        <td style="width:15%;">4.4 Estado civil</td>
        <td style="width:20%;">4.9 Ocupación</td>
    </tr>
    <tr>
        <td class="center">{{ d(optional($caso->denunciados[0] ?? null)->denunciado_edad) }}</td>
        <td>{{ d(optional($caso->denunciados[0] ?? null)->denunciado_domicilio_actual) }}</td>
        <td class="center">{{ d(optional($caso->denunciados[0] ?? null)->denunciado_estado_civil) }}</td>
        <td class="center">{{ d(optional($caso->denunciados[0] ?? null)->denunciado_ocupacion) }}</td>
    </tr>
</table>

<table class="tbl mb4 avoid">
    <tr class="center b">
        <td style="width:20%;">4.3 Idioma</td>
        <td style="width:40%;">4.6 Grado de instrucción</td>
        <td style="width:40%;"> </td>
    </tr>
    <tr>
        <td class="center">{{ d(optional($caso->denunciados[0] ?? null)->denunciado_idioma) }}</td>
        <td class="center">{{ d(optional($caso->denunciados[0] ?? null)->denunciado_grado) }}</td>
        <td></td>
    </tr>
</table>

{{-- ===== 5. BREVE CIRCUNSTANCIA ===== --}}
<div class="b upper mb4">5.- BREVE CIRCUNSTANCIA DEL HECHO O DENUNCIA</div>
<div class="tbl" style="border:1px solid #000; padding:4px; min-height:110px;" class="avoid">
    {!! nl2br(e(d($caso->caso_descripcion))) !!}
</div>

{{-- ===== 6. TIPOLOGÍA ===== --}}
<div class="b upper mb4" style="margin-top:8px;">6. TIPOLOGÍA</div>
<table class="tbl mb4 avoid">
    <tr class="center b">
        <td>VIOLENCIA FÍSICA</td>
        <td>VIOLENCIA PSICOLÓGICA</td>
        <td>ABANDONO</td>
        <td>APOYO INTEGRAL</td>
    </tr>
    <tr class="center">
        <td><span class="box">{{ x($caso->violencia_fisica) }}</span></td>
        <td><span class="box">{{ x($caso->violencia_psicologica) }}</span></td>
        <td><span class="box">{{ x($caso->violencia_abandono) }}</span></td>
        <td><span class="box">{{ x($caso->tip_apoyo_integral ?? false) }}</span></td>
    </tr>
</table>

{{-- ===== 7. SEGUIMIENTO ===== --}}
<div class="b upper mb4">7. SEGUIMIENTO DEL CASO:</div>
<table class="tbl avoid">
    <tr class="center">
        <td style="width:50%;">TRABAJO LEGAL</td>
        <td><span class="box">{{ $caso->legal_user_id ? 'X' : '' }}</span></td>
    </tr>
    <tr class="center">
        <td>TRABAJO SOCIAL</td>
        <td><span class="box">{{ $caso->trabajo_social_user_id ? 'X' : '' }}</span></td>
    </tr>
    <tr class="center">
        <td>PSICOLÓGICO</td>
        <td><span class="box">{{ $caso->psicologica_user_id ? 'X' : '' }}</span></td>
    </tr>
</table>

</body>
</html>
