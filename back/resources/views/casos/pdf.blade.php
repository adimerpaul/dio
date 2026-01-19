@php
    function yesno($v){ return $v ? 'SI' : 'NO'; }
    function d($v, $def=''){ return $v !== null && $v !== '' ? $v : $def; }
    function fdate($v){ return $v ? \Carbon\Carbon::parse($v)->format('d/m/Y') : ''; }

    // marca X si coincide (simple)
    function chk($cond){ return $cond ? 'X' : ''; }

    // normaliza para comparar textos
    function norm($v){
        $v = mb_strtolower(trim((string)$v));
        $v = str_replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'], $v);
        return $v;
    }

    // detecta tipo de documento desde el texto
    function docType($doc){
        $t = norm($doc);
        if (str_contains($t, 'carnet') || $t === 'ci' || str_contains($t, 'c.i')) return 'CI';
        if (str_contains($t, 'rum')) return 'RUM';
        if (str_contains($t, 'nacimiento') || str_contains($t, 'cert')) return 'NAC';
        if ($t === '' || str_contains($t, 'ninguno')) return 'NING';
        return 'OTRO';
    }
@endphp

    <!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>REGISTRO SLIM — Caso #{{ $caso->id }}</title>
    <style>
        @page { margin: 18px 18px 30px 18px; }
        * { font-family: "DejaVu Sans", sans-serif; font-size: 9px; line-height: 1.15; color: #000; }

        .h1 { text-align:center; font-weight:700; font-size:12px; letter-spacing:.2px; }
        .h2 { text-align:center; font-weight:700; font-size:11px; margin-top:2px; }

        .toprow { width:100%; border-collapse:collapse; margin-top:6px; }
        .toprow td { vertical-align:top; }

        .lbl { font-size:8px; font-weight:700; }
        .muted { font-size:8px; color:#222; font-weight:700; }
        .casebox { font-weight:700; font-size:11px; text-align:right; }

        .form { width:100%; border-collapse:collapse; margin-top:6px; }
        .form th, .form td { border: 1px solid #000; padding: 3px 4px; vertical-align:top; }
        .form th { font-weight:700; background:#fff; }

        .cell-label { font-size:8px; font-weight:700; }
        .cell-value { margin-top:1px; font-weight:400; }
        .line { display:block; border-bottom:1px solid #000; height:12px; }

        .section { margin-top:8px; font-weight:700; }
        .tight td { padding-top:2px; padding-bottom:2px; }

        .checks { width:100%; border-collapse:collapse; }
        .checks td { border:none; padding:0 10px 0 0; font-size:8px; }
        .boxchk {
            display:inline-block;
            width:10px; height:10px;
            border:1px solid #000;
            text-align:center;
            line-height:10px;
            font-size:9px;
            margin-right:4px;
        }

        .mt4{ margin-top:4px; }
        .mt6{ margin-top:6px; }
        .avoid { page-break-inside: avoid; }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="h1">SERVICIO LEGAL INTEGRAL MUNICIPAL ({{ d($caso->area) }})</div>
<div class="h2">REGISTRO DE DENUNCIAS</div>

<!-- CABECERA: Nombre del denunciante + Caso -->
@php
    $den0 = $caso->denunciantes && count($caso->denunciantes) ? $caso->denunciantes[0] : null;
@endphp

<table class="toprow">
    <tr>
        <td style="width:60%;">
            <div class="muted">1.- Nombre del Denunciante:</div>
            <div style="margin-top:2px;">
                <span class="line">{{ $den0 ? d($den0->denunciante_nombres) : '' }}</span>
            </div>

            <table style="width:100%; border-collapse:collapse; margin-top:6px;">
                <tr>
                    <td style="width:33%; padding-right:6px;">
                        <div class="muted">Fecha de registro:</div>
                        <span class="line">{{ fdate($caso->fecha_apertura_caso) }}</span>
                    </td>
                    <td style="width:33%; padding-right:6px;">
                        <div class="muted">Departamento:</div>
                        <span class="line">{{ d($caso->departamento, 'ORURO') }}</span>
                    </td>
                    <td style="width:34%;">
                        <div class="muted">Módulo/Zona:</div>
                        <span class="line">{{ d($caso->zona) }}</span>
                    </td>
                </tr>
            </table>
        </td>

        <td style="width:40%;">
            <div class="casebox">CASO: {{ d($caso->caso_numero) }}</div>
            <div class="mt6">
                <div class="muted">Provincia:</div>
                <span class="line">{{ d($caso->provincia) }}</span>
            </div>
            <div class="mt6">
                <div class="muted">Municipio:</div>
                <span class="line">{{ d($caso->municipio) }}</span>
            </div>
        </td>
    </tr>
</table>

<!-- 2. DATOS DEL DENUNCIANTE (formato como imagen) -->
<div class="section">2.- Datos del Denunciante</div>

@foreach($caso->denunciantes as $den)
    @php $denDoc = docType($den->denunciante_documento); @endphp

    <table class="form tight avoid">
        <tr>
            <td style="width:34%;">
                <div class="cell-label">2.1 Identificación — Nombre(s)</div>
                <span class="line">{{ d($den->denunciante_nombres) }}</span>
            </td>
            <td style="width:33%;">
                <div class="cell-label">Ap. Paterno</div>
                <span class="line">{{ d($den->denunciante_paterno) }}</span>
            </td>
            <td style="width:33%;">
                <div class="cell-label">Ap. Materno</div>
                <span class="line">{{ d($den->denunciante_materno) }}</span>
            </td>
        </tr>

        <tr>
            <td style="width:60%;" colspan="2">
                <div class="cell-label">2.2 Documento de Identidad</div>
                <table class="checks">
                    <tr>
                        <td><span class="boxchk">{{ chk($denDoc==='CI') }}</span> C.I.</td>
                        <td><span class="boxchk">{{ chk($denDoc==='RUM') }}</span> R.U.M.</td>
                        <td><span class="boxchk">{{ chk($denDoc==='NAC') }}</span> C. Nacimiento</td>
                        <td><span class="boxchk">{{ chk($denDoc==='NING') }}</span> Ninguno</td>
                    </tr>
                </table>
                <div class="mt4">
                    <span class="line">{{ d($den->denunciante_documento) }} {{ d($den->denunciante_nro) }}</span>
                </div>
            </td>
            <td style="width:40%;">
                <div class="cell-label">2.3 Sexo</div>
                <span class="line">{{ d($den->denunciante_sexo) }}</span>
            </td>
        </tr>

        <tr>
            <td style="width:50%;" colspan="2">
                <div class="cell-label">2.4 Lugar de nacimiento</div>
                <span class="line">{{ d($den->denunciante_lugar_nacimiento) }}</span>
            </td>
            <td style="width:50%;">
                <div class="cell-label">2.5 Fecha de Nacimiento</div>
                <span class="line">{{ fdate($den->denunciante_fecha_nacimiento) }}</span>
            </td>
        </tr>

        <tr>
            <td style="width:15%;">
                <div class="cell-label">2.6 Edad</div>
                <span class="line">{{ d($den->denunciante_edad) }}</span>
            </td>
            <td style="width:55%;" colspan="2">
                <div class="cell-label">2.7 Residencia Habitual</div>
                <span class="line">{{ d($den->denunciante_residencia) }}</span>
            </td>
        </tr>

        <tr>
            <td style="width:30%;">
                <div class="cell-label">2.8 Estado Civil</div>
                <span class="line">{{ d($den->denunciante_estado_civil) }}</span>
            </td>
            <td style="width:40%;">
                <div class="cell-label">2.9 Relación con el Denunciado(s)</div>
                <span class="line">{{ d($den->denunciante_relacion_denunciado) }}</span>
            </td>
            <td style="width:30%;">
                <div class="cell-label">2.10 Grado de instrucción</div>
                <span class="line">{{ d($den->denunciante_grado) }}</span>
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <div class="cell-label">2.11 Ocupación</div>
                <table style="width:100%; border-collapse:collapse; margin-top:2px;">
                    <tr>
                        <td style="width:18%; padding-right:6px;">
                            <div class="lbl">Trabaja:</div>
                            <span class="line">{{ yesno($den->denunciante_trabaja) }}</span>
                        </td>
                        <td style="width:52%; padding-right:6px;">
                            <div class="lbl">Ocupación:</div>
                            <span class="line">{{ d($den->denunciante_ocupacion) }}</span>
                        </td>
                        <td style="width:30%;">
                            <div class="lbl">Ingreso Económico:</div>
                            <span class="line">{{ d($den->denunciante_ingresos) }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="width:35%;">
                <div class="cell-label">2.13 Idioma</div>
                <span class="line">{{ d($den->denunciante_idioma) }}</span>
            </td>
            <td style="width:65%;" colspan="2">
                <div class="cell-label">2.14 Teléfonos de referencia</div>
                <table style="width:100%; border-collapse:collapse; margin-top:2px;">
                    <tr>
                        <td style="width:33%; padding-right:6px;">
                            <div class="lbl">Celular</div>
                            <span class="line">{{ d($den->denunciante_telefono) }}</span>
                        </td>
                        <td style="width:33%; padding-right:6px;">
                            <div class="lbl">Fija 1</div>
                            <span class="line">{{ d($den->denunciante_fijo1 ?? null) }}</span>
                        </td>
                        <td style="width:34%;">
                            <div class="lbl">Fija 2</div>
                            <span class="line">{{ d($den->denunciante_fijo2 ?? null) }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <div class="cell-label">2.15 Domicilio Actual</div>
                <span class="line">{{ d($den->denunciante_domicilio_actual) }}</span>
            </td>
        </tr>
    </table>
@endforeach

<!-- 3. GRUPO FAMILIAR -->
<div class="section">3.- Grupo Familiar</div>
<table class="form tight avoid">
    <tr>
        <th style="width:6%;">N.</th>
        <th>NOMBRES Y APELLIDOS</th>
        <th style="width:10%;">EDAD</th>
        <th style="width:20%;">PARENTESCO</th>
        <th style="width:18%;">CELULAR</th>
    </tr>
    @foreach($caso->familiares as $i => $fam)
        <tr>
            <td style="text-align:center;">{{ $i+1 }}</td>
            <td>{{ d($fam->familiar_nombre_completo) }}</td>
            <td style="text-align:center;">{{ d($fam->familiar_edad) }}</td>
            <td>{{ d($fam->familiar_parentesco) }}</td>
            <td>{{ d($fam->familiar_telefono) }}</td>
        </tr>
    @endforeach
</table>

<!-- 4. DATOS DEL DENUNCIADO (similar al formato de imagen 2) -->
<div class="section">4.- Datos del Denunciado</div>

@foreach($caso->denunciados as $denu)
    @php $denuDoc = docType($denu->denunciado_documento); @endphp

    <table class="form tight avoid">
        <tr>
            <td style="width:34%;">
                <div class="cell-label">4.1 Identificación — Nombre(s)</div>
                <span class="line">{{ d($denu->denunciado_nombres) }}</span>
            </td>
            <td style="width:33%;">
                <div class="cell-label">Ap. Paterno</div>
                <span class="line">{{ d($denu->denunciado_paterno) }}</span>
            </td>
            <td style="width:33%;">
                <div class="cell-label">Ap. Materno</div>
                <span class="line">{{ d($denu->denunciado_materno) }}</span>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <div class="cell-label">4.2 Documento de Identidad</div>
                <table class="checks">
                    <tr>
                        <td><span class="boxchk">{{ chk($denuDoc==='CI') }}</span> C.I.</td>
                        <td><span class="boxchk">{{ chk($denuDoc==='RUM') }}</span> R.U.M.</td>
                        <td><span class="boxchk">{{ chk($denuDoc==='NAC') }}</span> C. Nacimiento</td>
                        <td><span class="boxchk">{{ chk($denuDoc==='NING') }}</span> Ninguno</td>
                    </tr>
                </table>
                <div class="mt4">
                        <span class="line">
                            {{ d($denu->denunciado_documento) }} {{ d($denu->denunciado_nro) }}
                            @if(d($denu->denunciado_documento_otro))
                                ({{ d($denu->denunciado_documento_otro) }})
                            @endif
                        </span>
                </div>
            </td>
            <td>
                <div class="cell-label">4.3 Sexo</div>
                <span class="line">{{ d($denu->denunciado_sexo) }}</span>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <div class="cell-label">4.4 Lugar de Nacimiento</div>
                <span class="line">{{ d($denu->denunciado_lugar_nacimiento) }}</span>
            </td>
            <td>
                <div class="cell-label">4.5 Fecha de Nacimiento</div>
                <span class="line">{{ fdate($denu->denunciado_fecha_nacimiento) }}</span>
            </td>
        </tr>

        <tr>
            <td style="width:15%;">
                <div class="cell-label">4.6 Edad</div>
                <span class="line">{{ d($denu->denunciado_edad) }}</span>
            </td>
            <td style="width:55%;" colspan="2">
                <div class="cell-label">4.7 Residencia Habitual</div>
                <span class="line">{{ d($denu->denunciado_residencia) }}</span>
            </td>
        </tr>

        <tr>
            <td style="width:30%;">
                <div class="cell-label">4.8 Estado Civil</div>
                <span class="line">{{ d($denu->denunciado_estado_civil) }}</span>
            </td>
            <td style="width:40%;">
                <div class="cell-label">4.9 Ocupación</div>
                <span class="line">
                        {{ d($denu->denunciado_ocupacion) }}
                    @if(d($denu->denunciado_ocupacion_exacto))
                        — {{ d($denu->denunciado_ocupacion_exacto) }}
                    @endif
                    </span>
            </td>
            <td style="width:30%;">
                <div class="cell-label">4.10 Ingreso Económico</div>
                <span class="line">{{ d($denu->denunciado_ingresos) }}</span>
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <div class="cell-label">4.11 Relación con la Denunciante</div>
                <span class="line">{{ d($denu->denunciado_relacion) }}</span>
            </td>
        </tr>

        <tr>
            <td colspan="3">
                <div class="cell-label">DIRECCIÓN ACTUAL</div>
                <span class="line">{{ d($denu->denunciado_domicilio_actual) }}</span>
            </td>
        </tr>

        <tr>
            <td style="width:35%;">
                <div class="cell-label">Teléfono / Celular</div>
                <span class="line">
                        {{ d($denu->denunciado_telefono) }}
                    @if(d($denu->denunciado_movil))
                        — {{ d($denu->denunciado_movil) }}
                    @endif
                    @if(d($denu->denunciado_fijo))
                        — Fijo: {{ d($denu->denunciado_fijo) }}
                    @endif
                    </span>
            </td>
            <td style="width:35%;">
                <div class="cell-label">Idioma</div>
                <span class="line">{{ d($denu->denunciado_idioma) }}</span>
            </td>
            <td style="width:30%;">
                <div class="cell-label">Cargo</div>
                <span class="line">{{ d($denu->denunciado_cargo) }}</span>
            </td>
        </tr>
    </table>
@endforeach

<!-- 5. Breve circunstancia -->
<div class="section">5.- Breve circunstancia del hecho o Denuncia</div>
<table class="form tight avoid">
    <tr>
        <td style="height:70px;">
            {!! nl2br(e(d($caso->caso_descripcion))) !!}
        </td>
    </tr>
</table>

<!-- 6. Tipología -->
<div class="section">6.- Tipología del Hecho Principal</div>
<table class="form tight avoid">
    <tr>
        <td>
            <span class="line">{{ d($caso->caso_tipologia) }}</span>
        </td>
    </tr>
</table>

<!-- Violencias (si quieres mantenerlas) -->
<table class="form tight avoid" style="margin-top:6px;">
    <tr>
        <td style="width:25%;">Física: <b>{{ yesno($caso->violencia_fisica) }}</b></td>
        <td style="width:25%;">Psicológica: <b>{{ yesno($caso->violencia_psicologica) }}</b></td>
        <td style="width:25%;">Sexual: <b>{{ yesno($caso->violencia_sexual) }}</b></td>
        <td style="width:25%;">Económica: <b>{{ yesno($caso->violencia_economica) }}</b></td>
    </tr>
</table>

<!-- Seguimiento del caso -->
<div class="section">Seguimiento del caso</div>
<table class="form tight avoid">
    <tr>
        <th style="width:35%;">ÁREA</th>
        <th>RESPONSABLE</th>
    </tr>
    <tr>
        <td>ÁREA SOCIAL</td>
        <td>{{ optional($caso->trabajo_social_user)->name }}</td>
    </tr>
    <tr>
        <td>ÁREA LEGAL</td>
        <td>{{ optional($caso->legal_user)->name }}</td>
    </tr>
    <tr>
        <td>ÁREA PSICOLÓGICA</td>
        <td>{{ optional($caso->psicologica_user)->name }}</td>
    </tr>
</table>

</body>
</html>
