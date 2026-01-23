<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Formulario Registro Domiciliario — Caso #{{ $caso->id }}</title>

    <style>
        @page { size:A4 portrait; margin: 10mm 10mm 12mm 10mm; }
        * { box-sizing: border-box; font-family: DejaVu Sans, Arial, sans-serif; color:#111; }
        body { margin:30px; font-size:11px; }

        .top {
            display:grid;
            grid-template-columns: 110px 1fr 110px;
            gap: 8px;
            align-items:center;
            margin-bottom: 6px;
        }
        .logoBox {
            height: 70px;
            border: 1px solid #222;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size: 9px;
            text-align:center;
            padding: 4px;
        }
        .titleBox { text-align:center; }
        .titleBox h1 { margin:0; font-size:16px; letter-spacing:.2px; }
        .titleBox .sub { margin-top: 3px; font-size: 10px; }

        .caseBox {
            display:flex; gap:8px; justify-content:flex-end; align-items:center;
            margin-top: 2px;
        }
        .caseBox .caseLabel { font-weight:700; }
        .inputLine {
            border:1px solid #222; height:22px; padding:3px 6px;
            display:flex; align-items:center; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;
        }
        .caseBox .caseInput { width: 110px; }

        .rowLine { display:flex; gap:8px; align-items:center; margin: 6px 0; }
        .grow { flex:1; }
        .label { font-weight:700; }
        .dashedLine {
            border: 1px dashed #111;
            /*height: 28px;*/
            padding: 4px 6px;
            display:flex;
            align-items:center;
            overflow:hidden;
        }

        .sectionTitle { font-weight:700; margin: 6px 0 4px; font-size:12px; }

        .checks { display:flex; flex-wrap:wrap; gap:14px; }
        .chk { display:flex; align-items:center; gap:6px; min-width:120px; }
        .box {
            width: 12px; height: 12px; border: 1px solid #111;
            display:inline-flex; align-items:center; justify-content:center;
            font-size: 11px; line-height: 1;
        }

        .grid2 { display:grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .grid3 { display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; }
        .grid4 { display:grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 8px; }

        .field .cap { font-size: 10px; font-weight: 700; margin-bottom: 2px; }
        .field .val {
            border: 1px solid #111; height: 22px; padding: 3px 6px;
            display:flex; align-items:center; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;
        }

        .croquisTitle { margin-top: 8px; font-weight:700; font-size: 12px; }
        .croquisWrap {
            border: 1px dashed #111;
            padding: 6px;
            margin-top: 4px;
        }
        .croquisGrid { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .croquisGrid td { border: 1px dashed #444; height: 14px; }

        .toolbar { margin-top: 10px; display:flex; gap: 8px; justify-content:center; }
        .btn {
            padding: 6px 12px; border: 1px solid #111; background: #f3f4f6;
            border-radius: 6px; cursor: pointer; font-size: 12px;
        }

        @media print {
            .no-print { display:none !important; }
        }
    </style>
</head>

<body>
@php
    // Usar directamente el JSON padre ($caso) -> denunciados[0]
    $denunciado = $caso->denunciados->first();
//            'denunciado_nombres',
//        'denunciado_paterno',
//        'denunciado_materno',
//        'denunciado_documento',
//        'denunciado_documento_otro',
//        'denunciado_nro',
//        'denunciado_sexo',
//        'denunciado_lugar_nacimiento',
//        'denunciado_fecha_nacimiento',
//        'denunciado_edad',
//        'denunciado_telefono',
//        'denunciado_residencia',
//        'denunciado_estado_civil',
//        'denunciado_relacion',
//        'denunciado_grado',
//        'denunciado_trabaja',
//        'denunciado_prox',
//        'denunciado_ocupacion',
//        'denunciado_ocupacion_exacto',
//        'denunciado_idioma',
//        'denunciado_fijo',
//        'denunciado_movil',
//        'denunciado_domicilio_actual',
//        'denunciado_latitud',
//        'denunciado_longitud',
//        'caso_id',
//        'denunciado_ingresos',
//        'denunciado_relacion_victima',
//        'denunciado_relacion_denunciado',
    $de = $caso->denunciantes->first();
//            'denunciante_nombres',
//        'denunciante_paterno',
//        'denunciante_materno',
//        'denunciante_documento',
//        'denunciante_nro',
//        'denunciante_sexo',
//        'denunciante_lugar_nacimiento',
//        'denunciante_fecha_nacimiento',
//        'denunciante_edad',
//        'denunciante_telefono',
//        'denunciante_residencia',
//        'denunciante_estado_civil',
//        'denunciante_relacion',
//        'denunciante_grado',
//        'denunciante_domicilio_actual',
//        'latitud',
//        'longitud',
//        'caso_id',
//        'denunciante_idioma',
//        'denunciante_trabaja',
//        'denunciante_ocupacion',
//        'denunciante_ingresos',
//        'denunciante_relacion_victima',
//        'denunciante_relacion_denunciado',
//        'denunciante_cargo'
@endphp

<div class="sheet">

    <div class="top">
        <div style="text-align: center;">
            <div>
                <img src="{{ asset('images/logo1.jpeg') }}" style="max-height:62px;max-width:100%;" />
            </div>
            <div style="text-align: center">
                TRIBUNAL<br>DEPARTAMENTAL<br>DE ORURO
            </div>
        </div>

        <div class="titleBox">
            <h1>Formulario de Registro Domiciliario</h1>
            <div class="sub">
                Área: <b>{{ $caso->area ?: '—' }}</b> &nbsp;·&nbsp;
                Tipo: <b>{{ $caso->tipo ?: '—' }}</b> &nbsp;·&nbsp;
                Registrado: <b>{{ optional($caso->created_at)->format('d/m/Y H:i') }}</b>
            </div>

            <div class="caseBox">
                <span class="caseLabel">CASO N°</span>
                <div class="inputLine caseInput">{{ $caso->caso_numero ?: ('#'.$caso->id) }}</div>
            </div>
        </div>

        <div style="text-align: center;">
{{--            ESTADO<br>PLURINACIONAL<br>DE BOLIVIA--}}
{{--            --}}{{-- <img src="{{ asset('img/slim/escudo.png') }}" style="max-height:62px;max-width:100%;" /> --}}
            <div>
                <img src="{{ asset('images/logo2.jpeg') }}" style="max-height:62px;max-width:100%;" />
            </div>
            <span style="font-size:10px; text-align:center;">
                ESTADO<br>PLURINACIONAL<br>DE BOLIVIA
            </span>
        </div>
    </div>

    <div class="rowLine">
        <span class="label">Hecho denunciado</span>
        <div class="dashedLine grow">
            {{ $caso->caso_descripcion ?: '' }}
        </div>
    </div>

    <div class="sectionTitle">Datos de Identificación (Denunciate)</div>

    <div class="grid3">
        <div class="field">
            <div class="cap">Nombres</div>
            <div class="val">
                {{$de?->denunciante_nombres}}
            </div>
        </div>
    </div>

    <div class="sectionTitle">Documento de Identidad</div>

    <div class="grid2">
        <div class="field">
            <div class="cap">Tipo</div>
            <div class="val">{{ $de->denunciante_documento ?: '—' }}</div>
        </div>
        <div class="field">
            <div class="cap">Número</div>
            <div class="val">{{ $de?->denunciante_nro ?: '—' }}</div>
        </div>
    </div>

    <div class="sectionTitle">Dirección (Descripción completa de la dirección)</div>

    <div class="dashedLine">
        {{ $de?->denunciante_domicilio_actual ?: ($caso->caso_direccion ?: '—') }}
    </div>

    <div class="checks" style="margin-top:6px;">
        <div class="chk"><span class="box"></span> Domicilio</div>
        <div class="chk"><span class="box"></span> Laboral</div>
        <div class="chk"><span class="box"></span> Comercial</div>
        <div class="chk"><span class="box"></span> Procesal</div>
    </div>

    <div class="grid4" style="margin-top: 6px;">
        <div class="field">
            <div class="cap">Distrito</div>
            <div class="val"></div>
        </div>
        <div class="field">
            <div class="cap">Urbanización</div>
            <div class="val"></div>
        </div>
        <div class="field">
            <div class="cap">Barrio</div>
            <div class="val"></div>
        </div>
        <div class="field">
            <div class="cap">Ciudad</div>
            <div class="val">Oruro</div>
        </div>
    </div>

    <div class="grid3" style="margin-top: 6px;">
        <div class="field">
            <div class="cap">Departamento</div>
            <div class="val">Oruro</div>
        </div>
        <div class="field">
            <div class="cap">Teléfono de Contacto</div>
            <div class="val">
                {{ $de?->denunciante_telefono}}
            </div>
        </div>
        <div class="field">
            <div class="cap">Nombre laboral / Comercial / Abogado</div>
            <div class="val">
                {{ $caso->legal_user? $caso->legal_user->name : '—' }}
            </div>
        </div>
    </div>

    <div class="croquisTitle">Croquis de ubicación domiciliaria</div>
    <div class="croquisWrap">
        <table class="croquisGrid" aria-label="Croquis">
            @for($r=0; $r<22; $r++)
                <tr>
                    @for($c=0; $c<18; $c++)
                        <td></td>
                    @endfor
                </tr>
            @endfor
        </table>
    </div>
{{--    salto de linea--}}
    <div style="page-break-after: always;"></div>
    <br><br>
    <div class="sectionTitle">Datos de Identificación (Denunciado)</div>

    <div class="grid3">
        <div class="field">
            <div class="cap">Nombres</div>
            <div class="val">
                {{$denunciado?->denunciado_nombres}}
            </div>
        </div>
    </div>

    <div class="sectionTitle">Documento de Identidad</div>

    <div class="grid2">
        <div class="field">
            <div class="cap">Tipo</div>
            <div class="val">{{ $denunciado->denunciado_documento ?: '—' }}</div>
        </div>
        <div class="field">
            <div class="cap">Número</div>
            <div class="val">{{ $denunciado->denunciado_nro ?: '—' }}</div>
        </div>
    </div>

    <div class="sectionTitle">Dirección (Descripción completa de la dirección)</div>

    <div class="dashedLine">
{{--        {{ $d?->denunciado_domicilio_actual ?: ($caso->caso_direccion ?: '—') }}--}}
    </div>

    <div class="checks" style="margin-top:6px;">
        <div class="chk"><span class="box"></span> Domicilio</div>
        <div class="chk"><span class="box"></span> Laboral</div>
        <div class="chk"><span class="box"></span> Comercial</div>
        <div class="chk"><span class="box"></span> Procesal</div>
    </div>

    <div class="grid4" style="margin-top: 6px;">
        <div class="field">
            <div class="cap">Distrito</div>
            <div class="val"></div>
        </div>
        <div class="field">
            <div class="cap">Urbanización</div>
            <div class="val"></div>
        </div>
        <div class="field">
            <div class="cap">Barrio</div>
            <div class="val"></div>
        </div>
        <div class="field">
            <div class="cap">Ciudad</div>
            <div class="val">Oruro</div>
        </div>
    </div>

    <div class="grid3" style="margin-top: 6px;">
        <div class="field">
            <div class="cap">Departamento</div>
            <div class="val">Oruro</div>
        </div>
        <div class="field">
            <div class="cap">Teléfono de Contacto</div>
            <div class="val">
                {{ $denunciado?->denunciado_telefono}}
            </div>
        </div>
        <div class="field">
            <div class="cap">Nombre laboral / Comercial / Abogado</div>
            <div class="val">
{{--                {{ $caso->legal_user? $caso->legal_user->name : '—' }}--}}
            </div>
        </div>
    </div>

    <div class="croquisTitle">Croquis de ubicación domiciliaria</div>
    <div class="croquisWrap">
        <table class="croquisGrid" aria-label="Croquis">
            @for($r=0; $r<22; $r++)
                <tr>
                    @for($c=0; $c<18; $c++)
                        <td></td>
                    @endfor
                </tr>
            @endfor
        </table>
    </div>

    <div class="toolbar no-print">
        <button class="btn" onclick="window.print()">Imprimir</button>
        <button class="btn" onclick="window.close()">Cerrar</button>
    </div>

</div>

<script>
    (function(){
        // auto-imprimir como tu hoja ruta
        let printed = false;
        function goPrint(){
            if(printed) return;
            printed = true;
            setTimeout(()=>window.print(), 200);
        }
        window.addEventListener('load', goPrint);
        window.addEventListener('afterprint', ()=> setTimeout(()=>{ try{ window.close(); }catch(e){} }, 250));
    })();
</script>

</body>
</html>
