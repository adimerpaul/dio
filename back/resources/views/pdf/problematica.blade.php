<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Problemática — Caso {{ $p->caso_id }} #{{ $p->id }}</title>
    <style>
        /* ====== MODO COMPACTO PARA 1 HOJA ====== */
        @page { margin: 8mm 8mm 10mm 8mm; } /* margen menor */
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color:#101010; }

        .w-100{width:100%} .center{text-align:center} .right{text-align:right}
        .row{display:table;width:100%;table-layout:fixed}
        .col{display:table-cell;vertical-align:top}
        .pr-6{padding-right:6px} .pl-6{padding-left:6px}
        .mb-6{margin-bottom:6px} .mb-8{margin-bottom:8px} .mb-10{margin-bottom:10px}
        .tiny{font-size:9px} .muted{color:#666} .uppercase{text-transform:uppercase}
        .nowrap{white-space:nowrap}

        /* cabecera apretada */
        .header{border:1px solid #1a237e;padding:6px 8px}
        .brand{font-weight:700;font-size:13px;color:#1a237e}
        .sub{font-weight:700;font-size:12px;color:#1a237e;margin-top:2px}

        /* cajas */
        .box{
            border:1px solid #222;border-radius:2px;padding:6px;margin-bottom:8px;
            page-break-inside: avoid;
        }
        .label{font-weight:600;font-size:10px;margin-bottom:3px}
        .line{border-bottom:1px solid #777;height:16px}

        /* hoja rayada compacta */
        .paper{
            border:1px solid #222;padding:8px 10px;line-height:18px;
            background:repeating-linear-gradient(to bottom,#fff 0px,#fff 17px,#cfd8dc 18px);
            white-space:pre-wrap;word-wrap:break-word;
        }
        .paper.xs{min-height:80px}   /* título */
        .paper.sm{min-height:130px}  /* observaciones */
        .paper.md{min-height:180px}  /* detalle y acciones */

        /* firmas */
        .sign{margin-top:18px}
        .sign-line{border-bottom:1px solid #000;margin:26px 28px 4px 28px}
        .footer{
            position:fixed;bottom:-6mm;left:8mm;right:8mm;text-align:center;
            font-size:9px;color:#555;
        }
    </style>
</head>
<body>

<!-- CABECERA -->
<table class="w-100 header" cellspacing="0" cellpadding="0">
    <tr>
        <td width="18%" class="center">
            @if(!empty($logo) && file_exists($logo))
                <img src="{{ $logo }}" style="width:60px;height:auto">
            @endif
        </td>
        <td width="64%" class="center">
            <div class="brand uppercase">{{ $entidad ?? 'S.L.I.M.' }}</div>
            <div class="sub uppercase">PROBLEMÁTICA</div>
            <div class="tiny">{{ $municipio ?? '' }}</div>
        </td>
        <td width="18%" class="tiny">
            <div class="nowrap"><span class="muted">Caso:</span> <b>{{ $p->caso_id }}</b></div>
            <div class="nowrap"><span class="muted">Ficha:</span> <b>{{ $p->id }}</b></div>
            <div class="nowrap"><span class="muted">Fecha:</span>
                {{ $p->fecha ? \Illuminate\Support\Carbon::parse($p->fecha)->format('d/m/Y') : '—' }}
            </div>
        </td>
    </tr>
</table>

<!-- CONTEXTO (una fila, dos columnas) -->
<div class="box">
    <div class="row">
        <div class="col pr-6">
            <div class="label">Funcionario SLIM</div>
            <div class="line">&nbsp;{{ $p->user->name ?? $p->user->username ?? '—' }}</div>
        </div>
        <div class="col pl-6">
            <div class="label">Dependencia / Área</div>
            <div class="line">&nbsp;{{ $p->dependencia ?? 'SLIM' }}</div>
        </div>
    </div>
    @if($p->caso)
        <div class="row" style="margin-top:6px">
            <div class="col pr-6">
                <div class="label">Denunciante</div>
                <div class="line">&nbsp;{{ $p->caso->denunciante_nombre_completo ?? '—' }}</div>
            </div>
            <div class="col pl-6">
                <div class="label">Denunciado</div>
                <div class="line">&nbsp;{{ $p->caso->denunciado_nombre_completo ?? '—' }}</div>
            </div>
        </div>
    @endif
</div>

<!-- PROBLEMÁTICA -->
<div class="box">
    <div class="label uppercase">Problemática principal</div>
    <div class="paper xs">{{ trim($p->titulo ?? '') }}</div>
</div>

<!-- DETALLE -->
<div class="box">
    <div class="label uppercase">Detalle / antecedentes</div>
    <div class="paper md">{{ trim($p->detalle ?? '') }}</div>
</div>

<!-- ACCIONES -->
<div class="box">
    <div class="label uppercase">Acciones inmediatas y realizadas</div>
    <div class="paper md">{{ trim($p->acciones_inmediatas ?? '') }}</div>
</div>

<!-- OBSERVACIONES -->
<div class="box">
    <div class="label uppercase">Observaciones</div>
    <div class="paper sm">{{ trim($p->observaciones ?? '') }}</div>
</div>

<!-- FIRMAS -->
<table class="w-100 sign" cellspacing="0" cellpadding="0">
    <tr>
        <td width="50%" class="center">
            <div class="sign-line"></div>
            <div class="tiny">Funcionario SLIM</div>
        </td>
        <td width="50%" class="center">
            <div class="sign-line"></div>
            <div class="tiny">Visto Bueno</div>
        </td>
    </tr>
</table>

<div class="footer">
    Documento generado por el sistema — {{ now()->format('d/m/Y H:i') }}
</div>

<script type="text/php">
    if (isset($pdf)) {
      $pdf->page_text(520, 780, "Página {PAGE_NUM} de {PAGE_COUNT}",
        $fontMetrics->get_font("DejaVu Sans","normal"), 8, [0,0,0]);
    }
</script>
</body>
</html>
