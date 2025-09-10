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

<div style="margin-top:48px; display:flex; justify-content:space-between; font-size:12px;">
  <div style="text-align:center; width:30%;">
    __________________________<br/>
    FIRMA
  </div>
  <div style="text-align:center; width:30%;">
    __________________________<br/>
    NOMBRE
  </div>
  <div style="text-align:center; width:30%;">
    __________________________<br/>
    HUELLA DIGITAL
  </div>
</div>
`
  }
};
