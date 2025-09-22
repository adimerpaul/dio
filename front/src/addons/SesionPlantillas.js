// Plantillas simples en HTML para el WYSIWYG (DomPDF friendly)

export const SesionHtml = {
  acta({casoId, fecha, titulo, lugar, tipo}) {
    return `
<div style="border:1px solid #333;border-radius:6px;padding:12px;margin-bottom:12px;font-size:12px">
  <div style="display:flex;justify-content:space-between">
    <div>
      <div style="font-weight:700">DIRECCIÓN DE IGUALDAD DE OPORTUNIDADES (D.I.O.)</div>
      <div style="font-size:11px;color:#555">Servicio Legal Integral Municipal</div>
    </div>
    <div style="text-align:right;font-size:11px;color:#555">
      <div><b>Caso:</b> ${casoId}</div>
      <div><b>Fecha sesión:</b> ${fecha}</div>
    </div>
  </div>
</div>

<h5 style="margin:8px 0 4px 0;">ACTA DE SESIÓN</h5>
<table style="width:100%; border-collapse:collapse; font-size:12px">
  <tr>
    <td style="width:60%"><b>Título:</b> ${titulo}</td>
    <td style="width:20%"><b>Tipo:</b> ${tipo}</td>
    <td style="width:20%"><b>Lugar:</b> ${lugar || '—'}</td>
  </tr>
</table>

<h5 style="margin:12px 0 4px 0;">1. Motivo de consulta</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">2. Desarrollo de la sesión</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">3. Observaciones</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">4. Recomendaciones / acuerdos</h5>
<ul><li>...</li></ul>

<div style="margin-top:38px;display:flex;justify-content:space-between;font-size:12px;">
  <div style="text-align:center;width:45%;">
    <div>__________________________</div>
    <div>Firma profesional</div>
  </div>
  <div style="text-align:center;width:45%;">
    <div>__________________________</div>
    <div>Vo.Bo.</div>
  </div>
</div>`;
  },

  informe({casoId, fecha, titulo}) {
    return `
<h4 style="margin:0 0 8px 0;">Informe psicológico</h4>
<div style="font-size:12px;color:#555;margin-bottom:8px">
  Caso ${casoId} — Fecha ${fecha}
</div>

<h5 style="margin:8px 0 4px 0;">Resumen</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">Antecedentes</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">Resultados / análisis</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">Conclusiones</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">Recomendaciones</h5>
<ul><li>...</li></ul>`;
  },

  constancia({casoId, fecha, titulo}) {
    return `
<div style="text-align:center; font-weight:700; text-decoration:underline; margin-bottom:12px;">CONSTANCIA DE ASISTENCIA</div>
<p>Se deja constancia que la persona atendió a la sesión psicológica indicada:</p>
<table style="width:100%; border-collapse:collapse; font-size:12px">
  <tr><td style="width:30%"><b>Cas o:</b></td><td>#${casoId}</td></tr>
  <tr><td><b>Título sesión:</b></td><td>${titulo}</td></tr>
  <tr><td><b>Fecha:</b></td><td>${fecha}</td></tr>
</table>

<p style="margin-top:28px">Se expide para fines que estime conveniente.</p>

<div style="margin-top:38px;text-align:center">
  __________________________<br>
  Firma profesional
</div>`;
  },
  consentimiento({fecha, nombre, documento}) {
    return `
<div style="text-align:center; font-weight:700; margin-bottom:12px;">
  CONSENTIMIENTO INFORMADO PSICOLOGÍA
</div>

<p style="font-size:12px; margin-bottom:8px;">
  <b>Fecha:</b> ${fecha || '______/______/______'}
</p>

<p style="font-size:12px; text-align:justify;">
  Sr.(a) Usuario, por favor lea atentamente el siguiente documento que tiene como objetivo explicarle el uso y la confidencialidad de sus datos, así como sus derechos, respecto al proceso de atención psicológica.
  Si tiene alguna duda y/o consulta lo puede realizar con el/la Psicólogo/a.
</p>

<p style="font-size:12px; margin-top:12px;">
  <b>Yo:</b> ${nombre || '_____________________________________________'}
</p>

<p style="font-size:12px; margin-top:8px;">
  <b>Con Documento Nro.:</b> ${documento || '_____________________________'}
</p>

<p style="font-size:12px; text-align:justify; margin-top:12px;">
  Confirmo que he sido informado con claridad y veracidad, respecto al proceso de evaluaciones, sesiones, tiempos, las pruebas psicológicas que se me van a realizar, y también en cuanto a los resultados.
  De esta evaluación acepto, según los resultados de las pruebas psicológicas que se me realice, libre y voluntariamente, doy mi consentimiento para realizar las evaluaciones psicológicas.
</p>

<!--<div style="margin-top:48px; display:flex; justify-content:space-between; font-size:12px;">-->
<!--  <div style="text-align:center; width:30%;">-->
<!--    __________________________<br/>-->
<!--    FIRMA-->
<!--  </div>-->
<!--  <div style="text-align:center; width:30%;">-->
<!--    __________________________<br/>-->
<!--    NOMBRE-->
<!--  </div>-->
<!--  <div style="text-align:center; width:30%;">-->
<!--    __________________________<br/>-->
<!--    HUELLA DIGITAL-->
<!--  </div>-->
<!--</div>-->
<table style="width:100%; border-collapse:collapse; font-size:12px; margin-top:48px;">
  <tr>
    <td style="width:33%; text-align:center;">
      __________________________<br/>
      FIRMA
    </td>
    <td style="width:33%; text-align:center;">
      __________________________<br/>
      NOMBRE
    </td>
    <td style="width:33%; text-align:center;">
    __________________________<br/>
      HUELLA DIGITAL
    </td>
  </tr>
</table>
`
  },
  // Plantilla formal tipo DIO — Informe Psicológico
// Espera:
// {
//   numeroCaso, casoId, fecha, abogadoNombre, psicologoNombre,
//   denunciante: { nombres, apellidos, edad, fecha_nacimiento, lugar_nacimiento, grado, ocupacion, domicilio, documento, nro, telefono, estado_civil },
//   familiares: [{ nombre, edad, estado_civil, parentesco, ocupacion }, ...],
//   motivo, antecedentes, tecnicas, conclusiones, recomendaciones
// }
  informe_dio({
                numeroCaso,
                casoId,
                fecha,
                abogadoNombre,
                psicologoNombre,
                denunciante = {},
                familiares = [],
                motivo = '',
                antecedentes = '',
                tecnicas = '',
                conclusiones = '',
                recomendaciones = ''
              }) {
    const d = (v, fallback = '—') => (v == null || v === '' ? fallback : v)

    const familiaresRows = (familiares && familiares.length
        ? familiares
        : [{ nombre:'', edad:'', estado_civil:'', parentesco:'', ocupacion:'' }]
    ).map(f => `
    <tr>
      <td>${d(f.nombre)}</td>
      <td style="text-align:center">${d(f.edad)}</td>
      <td style="text-align:center">${d(f.estado_civil)}</td>
      <td style="text-align:center">${d(f.parentesco)}</td>
      <td style="text-align:center">${d(f.ocupacion)}</td>
    </tr>
  `).join('')

    return `
<div style="font-size:12px; line-height:1.35">
  <div style="text-align:center; font-weight:700; text-transform:uppercase;">
    INFORME PSICOLÓGICO
  </div>
  <div style="text-align:center; margin-top:2px; font-size:11px; color:#333">
    Nro. de Caso: ${d(numeroCaso)}&nbsp;&nbsp;|&nbsp;&nbsp;Caso ID: #${d(casoId)}
  </div>

  <div style="margin-top:8px; border:1px solid #333; border-radius:6px; padding:10px;">
    <table style="width:100%; border-collapse:collapse; font-size:12px">
      <tr>
        <td style="width:10%;"><b>A:</b></td>
        <td> ${d(abogadoNombre, '________________________________________')} </td>
      </tr>
      <tr>
        <td></td>
        <td>ABOGADO/A DE LA DIRECCIÓN DE IGUALDAD DE OPORTUNIDADES</td>
      </tr>
      <tr><td colspan="2" style="height:8px"></td></tr>
      <tr>
        <td><b>DE:</b></td>
        <td> ${d(psicologoNombre, '________________________________________')} </td>
      </tr>
      <tr>
        <td></td>
        <td>PSICÓLOGA / PSICÓLOGO – DIRECCIÓN DE IGUALDAD DE OPORTUNIDADES (DIO) SLIM</td>
      </tr>
      <tr>
        <td><b>Fecha:</b></td>
        <td>${d(fecha)}</td>
      </tr>
    </table>
  </div>

  <h5 style="margin:12px 0 4px 0;">1. DATOS PERSONALES DE LA DENUNCIANTE</h5>
  <table style="width:100%; border-collapse:collapse; font-size:12px">
    <tr>
      <td style="width:45%"><b>Nombres y apellidos:</b> ${d((denunciante.nombres ? (denunciante.nombres + ' ') : '') + (denunciante.apellidos || ''))}</td>
      <td style="width:20%"><b>Edad:</b> ${d(denunciante.edad)}</td>
      <td style="width:35%"><b>Fecha de nacimiento:</b> ${d(denunciante.fecha_nacimiento)}</td>
    </tr>
    <tr>
      <td><b>Lugar de nacimiento:</b> ${d(denunciante.lugar_nacimiento)}</td>
      <td colspan="2"><b>Grado de instrucción:</b> ${d(denunciante.grado)}</td>
    </tr>
    <tr>
      <td><b>Ocupación:</b> ${d(denunciante.ocupacion)}</td>
      <td colspan="2"><b>Dirección/Domicilio:</b> ${d(denunciante.domicilio)}</td>
    </tr>
    <tr>
      <td><b>Documento:</b> ${d(denunciante.documento)}</td>
      <td><b>Nro.:</b> ${d(denunciante.nro)}</td>
      <td><b>Teléfono:</b> ${d(denunciante.telefono)}</td>
    </tr>
    <tr>
      <td colspan="3"><b>Estado civil:</b> ${d(denunciante.estado_civil)}</td>
    </tr>
  </table>

  <h5 style="margin:12px 0 4px 0;">2. DATOS FAMILIARES</h5>
  <table style="width:100%; border:1px solid #333; border-collapse:collapse; font-size:12px">
    <thead>
      <tr style="background:#eee">
        <th style="border:1px solid #333; padding:4px; text-align:left;">Nombres y Apellidos</th>
        <th style="border:1px solid #333; padding:4px; width:60px;">Edad</th>
        <th style="border:1px solid #333; padding:4px; width:110px;">Estado civil</th>
        <th style="border:1px solid #333; padding:4px; width:110px;">Parentesco</th>
        <th style="border:1px solid #333; padding:4px; width:140px;">Ocupación</th>
      </tr>
    </thead>
    <tbody>
      ${familiaresRows}
    </tbody>
  </table>

  <h5 style="margin:12px 0 4px 0;">3. MOTIVO DE CONSULTA</h5>


  <h5 style="margin:12px 0 4px 0;">4. ANTECEDENTES</h5>


  <h5 style="margin:12px 0 4px 0;">5. TÉCNICAS E INSTRUMENTOS EMPLEADOS</h5>


  <h5 style="margin:12px 0 4px 0;">6. CONCLUSIONES</h5>


  <h5 style="margin:12px 0 4px 0;">7. RECOMENDACIONES</h5>

  <p style="margin-top:12px">Se emite el presente informe en honor a la verdad para fines consiguientes.</p>

  <div style="margin-top:28px; display:flex; justify-content:space-between; font-size:12px;">
    <div style="text-align:center; width:45%;">
      __________________________<br/>
      ${d(psicologoNombre, 'Psicóloga/o Interviniente')}
    </div>
    <div style="text-align:center; width:45%;">
      __________________________<br/>
      Vo.Bo.
    </div>
  </div>
</div>
`
  },
};
