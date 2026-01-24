{{-- resources/views/pdf/dna/pdf.blade.php --}}
@php
    function d($v, $def=''){ return ($v !== null && $v !== '') ? $v : $def; }
    function fdate($v){ return $v ? \Carbon\Carbon::parse($v)->format('d/m/Y') : ''; }
    function chk($cond){ return $cond ? 'X' : ''; }

    // 1 registro "principal" para denunciado/denunciante (formulario DNA es 1)
    $den = $caso->denunciantes[0] ?? null;
    $denu = $caso->denunciados[0] ?? null;

    $nombreDen = $den ? trim(($den->denunciante_nombres ?? '').' '.($den->denunciante_paterno ?? '').' '.($den->denunciante_materno ?? '')) : '';
    $nombreDenu = $denu ? trim(($denu->denunciado_nombres ?? '').' '.($denu->denunciado_paterno ?? '').' '.($denu->denunciado_materno ?? '')) : '';

    // "Principal" del formato (en la imagen es texto grande a mano: ASISTENCIA FAMILIAR)
    $principal = d($caso->principal, d($caso->caso_tipologia, d($caso->titulo, d($caso->tipo))));
@endphp

    <!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>DNA — Registro de Atención y/o Denuncia</title>
    <style>
        @page { margin: 18px 18px 22px 18px; }
        * { font-family: "DejaVu Sans", sans-serif; font-size: 10px; line-height: 1.15; color:#000; }
        .b { font-weight: 700; }
        .upper { text-transform: uppercase; }
        .center { text-align:center; }
        .right { text-align:right; }
        .xs { font-size: 9px; }
        .xxs { font-size: 8px; }
        .muted { color:#111; }
        .box { border:1px solid #000; }
        table { width:100%; border-collapse: collapse; }
        td, th { border:1px solid #000; padding:4px 5px; vertical-align: top; }
        .no-b td, .no-b th { border:none; padding:0; }
        .tight td, .tight th { padding:3px 4px; }
        .h40 { height:40px; }
        .h60 { height:60px; }
        .h90 { height:90px; }
        .h140 { height:140px; }
        .chkbox { display:inline-block; width:12px; height:12px; border:1px solid #000; line-height:12px; text-align:center; font-size:10px; }
        .section { background:#f2f2f2; font-weight:700; }
        .avoid { page-break-inside: avoid; }
        table.tbl th, table.tbl td { border:1px solid #000; padding:2px 3px; }
        table.tbl th { background:#fff; }

    </style>
</head>
<body>

{{-- ===== HEADER +SID ===== --}}
<table class="tight">
    <tr>
        <td class="center" style="width:22%;">
            <div class="b">+SID</div>
            <div class="xxs">Sistema</div>
            <div class="xxs">de Información</div>
            <div class="xxs">de Defensorías</div>
        </td>
        <td class="center b" style="width:56%; font-size:12px;">
            Registro de Atención y/o Denuncia
        </td>
        <td style="width:22%;">
            <div><span class="b">Fecha:</span> {{ fdate($caso->fecha_apertura_caso ?? now()) }}</div>
            <div><span class="b">Código:</span> {{ d($caso->caso_numero, $caso->id) }}</div>
            <div><span class="b">No Atención:</span> {{ d($caso->id) }}</div>
        </td>
    </tr>
</table>

{{-- ===== 1. DATOS GENERALES ===== --}}
<table class="tight" style="margin-top:6px;">
    <tr>
        <td class="section" style="width:100%;">1.- DATOS GENERALES</td>
    </tr>
</table>

<table class="tight avoid">
    <tr>
        <td style="width:18%;" class="b">PRINCIPAL :</td>
        <td style="width:82%;">{{ $principal }}</td>
    </tr>
</table>

{{-- ===== 2. MENORES ===== --}}
<table class="tight" style="margin-top:6px;">
    <tr>
        <td class="section">2.- (Menores) Nombres y Apellidos (del menor)</td>
    </tr>
</table>

<table class="tbl tiny tight avoid">
    {{-- Encabezado 2 niveles (como formulario) --}}
    <tr class="center b">
        <th rowspan="2" style="width:4%;">N°</th>
        <th rowspan="2" style="width:28%;">Nombres y<br>Apellidos</th>

        <th colspan="2" style="width:10%;">GESTANTE</th>
        <th colspan="2" style="width:10%;">EDAD</th>
        <th colspan="2" style="width:8%;">SEXO</th>
        <th colspan="2" style="width:8%;">C. Nac</th>
        <th colspan="2" style="width:10%;">ESTUDIA</th>

        <th rowspan="2" style="width:12%;">ÚLTIMO<br>CURSO</th>
        <th rowspan="2" style="width:10%;">TIPO DE<br>TRABAJO</th>
    </tr>
    <tr class="center b">
        <th style="width:5%;">SI</th>
        <th style="width:5%;">NO</th>

        <th style="width:5%;">AÑOS</th>
        <th style="width:5%;">MESES</th>

        <th style="width:4%;">F</th>
        <th style="width:4%;">M</th>

        <th style="width:4%;">SI</th>
        <th style="width:4%;">NO</th>

        <th style="width:5%;">SI</th>
        <th style="width:5%;">NO</th>
    </tr>

    @forelse($caso->victimas as $i => $v)
        @php
            $nom = d($v->nombres_apellidos);
            $sexo = strtoupper(d($v->sexo));
            $edadA = d($v->edad);
            $edadM = d($v->edad_meses); // si no existe quedará vacío

            $gestante = (int) d($v->gestante, 0) === 1;
            $estudia  = (int) d($v->estudia, 0) === 1;

            // En tu modelo Victima NO existe cert_nac: queda vacío
            $cert = (int) d($v->cert_nac, null); // null -> no marca nada
        @endphp

        <tr class="center">
            <td>{{ $i+1 }}</td>
            <td class="clip" style="text-align:left;">{{ $nom }}</td>

            {{-- GESTANTE --}}
            <td>{{ chk($gestante) }}</td>
            <td>{{ chk(!$gestante) }}</td>

            {{-- EDAD --}}
            <td>{{ $edadA }}</td>
            <td>{{ $edadM }}</td>

            {{-- SEXO --}}
            <td>{{ chk($sexo === 'F') }}</td>
            <td>{{ chk($sexo === 'M') }}</td>

            {{-- C. NAC --}}
            <td>{{ chk($cert === 1) }}</td>
            <td>{{ chk($cert === 0) }}</td>

            {{-- ESTUDIA --}}
            <td>{{ chk($estudia) }}</td>
            <td>{{ chk(!$estudia) }}</td>

            {{-- ÚLTIMO CURSO --}}
            <td style="text-align:left;">{{ d($v->grado_curso) }}</td>

            {{-- TIPO DE TRABAJO --}}
            <td style="text-align:left;">{{ d($v->lugar_trabajo) }}</td>
        </tr>
    @empty
        <tr>
            <td class="center">1</td>
            <td colspan="13" class="center muted">— Sin víctimas registradas —</td>
        </tr>
    @endforelse
</table>

<table class="tight avoid">
    <tr>
        <td style="width:15%;" class="b">Domicilio:</td>
        <td style="width:55%;">{{ d($den?->denunciante_domicilio_actual) }}</td>
        <td style="width:15%;" class="b">Teléfono:</td>
        <td style="width:15%;">{{ d($den?->denunciante_telefono) }}</td>
    </tr>
</table>

{{-- ===== 3. DATOS DEL GRUPO FAMILIAR ===== --}}
<table class="tight" style="margin-top:6px;">
    <tr><td class="section">3.- DATOS DEL GRUPO FAMILIAR</td></tr>
</table>

<table class="tight avoid">
    <tr class="center b">
        <td style="width:5%;">N°</td>
        <td style="width:35%;">Nombres y Apellidos</td>
        <td style="width:18%;">Parentesco</td>
        <td style="width:10%;">Edad</td>
        <td style="width:10%;">Sexo</td>
        <td style="width:12%;">G. Instrucción</td>
        <td style="width:10%;">Ocupación</td>
    </tr>

    @forelse($caso->familiares as $i => $f)
        @php
            $nombreFam = d($f->familiar_nombre_completo,
                trim(d($f->familiar_nombres).' '.d($f->familiar_paterno).' '.d($f->familiar_materno))
            );
        @endphp
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td>{{ $nombreFam }}</td>
            <td>{{ d($f->familiar_parentesco) }}</td>
            <td class="center">{{ d($f->familiar_edad) }}</td>
            <td class="center">{{ d($f->familiar_sexo) }}</td>
            <td>{{ d($f->familiar_grado) }}</td>
            <td>{{ d($f->familiar_ocupacion) }}</td>
        </tr>
    @empty
        <tr><td class="center">1</td><td colspan="6" class="center muted">— Sin grupo familiar registrado —</td></tr>
    @endforelse
</table>

{{-- ===== 5. DATOS DEL DENUNCIADO ===== --}}
<table class="tight" style="margin-top:6px;">
    <tr><td class="section">5.- DATOS DEL DENUNCIADO <span class="xxs">(Especificar la institución si corresponde)</span></td></tr>
</table>

<table class="tight avoid">
    <tr>
        <td style="width:18%;" class="b">Nombres y Apellidos</td>
        <td style="width:52%;">{{ d($nombreDenu) }}</td>
        <td style="width:10%;" class="b">Sexo:</td>
        <td style="width:20%;">{{ d($denu?->denunciado_sexo) }}</td>
    </tr>
    <tr>
        <td class="b">Parentesco o tipo de relación </td>
        <td>{{ d($denu?->denunciado_relacion_victima) }}</td>
        <td class="b">C.I.</td>
        <td>{{ d($denu?->denunciado_nro) }}</td>
    </tr>
    <tr>
        <td class="b">Domicilio (zona/comunidad)</td>
        <td>{{ d($denu?->denunciado_domicilio_actual) }}</td>
        <td class="b">Teléfono</td>
        <td>{{ d($denu?->denunciado_telefono) }}</td>
    </tr>
    <tr>
        <td class="b">Lugar de Trabajo</td>
        <td>{{ d($denu?->denunciado_prox) }}</td>
        <td class="b">Ocupación</td>
        <td>
            {{ d($denu?->denunciado_ocupacion) }}
            @if(d($denu?->denunciado_ocupacion_exacto))
                <span class="xxs">— {{ d($denu?->denunciado_ocupacion_exacto) }}</span>
            @endif
        </td>
    </tr>
</table>

{{-- ===== DATOS DEL DENUNCIANTE ===== --}}
<table class="tight" style="margin-top:6px;">
    <tr><td class="section">DATOS DEL DENUNCIANTE <span class="xxs">(Especificar la institución si corresponde)</span></td></tr>
</table>

<table class="tight avoid">
    <tr>
        <td style="width:18%;" class="b">Nombre y Apellido</td>
        <td style="width:52%;">{{ d($nombreDen) }}</td>
        <td style="width:10%;" class="b">Sexo:</td>
        <td style="width:20%;">{{ d($den?->denunciante_sexo) }}</td>
    </tr>
    <tr>
        <td class="b">Parentesco o tipo de relación</td>
        <td>{{ d($den?->denunciante_relacion_victima) }}</td>
        <td class="b">C.I.</td>
        <td>{{ d($den?->denunciante_nro) }}</td>
    </tr>
    <tr>
        <td class="b">Domicilio</td>
        <td>{{ d($den?->denunciante_domicilio_actual) }}</td>
        <td class="b">Teléfono</td>
        <td>{{ d($den?->denunciante_telefono) }}</td>
    </tr>
    <tr>
        <td class="b">Lugar de Trabajo</td>
        <td>{{ d($den?->denunciante_cargo) }}</td>
        <td class="b">Ocupación</td>
        <td>{{ d($den?->denunciante_ocupacion) }}</td>
    </tr>
</table>

{{-- ===== 6. DESCRIPCIÓN DE LA DENUNCIA ===== --}}
<table class="tight" style="margin-top:6px;">
    <tr><td class="section">6.- DESCRIPCIÓN DE LA DENUNCIA</td></tr>
</table>

<table class="tight avoid">
    <tr>
        <td class="h140">
            {!! nl2br(e(d($caso->caso_descripcion))) !!}
        </td>
    </tr>
</table>

{{-- ===== FIRMAS (como tu imagen) ===== --}}
<table class="tight avoid" style="margin-top:8px;">
    <tr>
        <td style="width:50%; height:80px;">
            <div class="b">DENUNCIANTE:</div>
            <div class="xxs" style="margin-top:10px;">NOMBRE: ............................................................</div>
            <div class="xxs" style="margin-top:10px;" class="center">Firma / huella digital</div>
            <div class="xxs" style="margin-top:10px;">C.I.: ..................................................................</div>
        </td>
        <td style="width:50%; height:80px;" class="center">
            <div style="margin-top:45px;" class="xxs">Firma</div>
            <div class="xxs">Asistente Legal del D.N.A</div>
        </td>
    </tr>
    <tr>
        <td style="width:50%; height:75px;">
            <div class="b">ABOGADO ASIGNADO AL CASO :</div>
            <div class="xxs" style="margin-top:25px;">
{{--                Dr. (a)--}}
                {{$caso->legal_user? $caso->legal_user->name : '............................................................'}}
            </div>
        </td>
        <td style="width:50%; height:75px;" class="center">
            <div style="margin-top:40px;" class="xxs">Firma del Abogado</div>
        </td>
    </tr>
</table>

</body>
</html>
