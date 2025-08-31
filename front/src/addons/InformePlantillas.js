// src/addons/InformePlantillas.js
export const InformeHtml = {
  psicologico({ casoId, fecha, titulo, numero }) {
    return `
<div style="border:1px solid #333;border-radius:6px;padding:12px;margin-bottom:12px;font-size:12px">
  <div style="display:flex;justify-content:space-between">
    <div>
      <div style="font-weight:700">DIRECCIÓN DE IGUALDAD DE OPORTUNIDADES (D.I.O.)</div>
      <div style="font-size:11px;color:#555">SERVICIO LEGAL INTEGRAL MUNICIPAL</div>
    </div>
    <div style="text-align:right;font-size:11px;color:#555">
      <div><b>Caso:</b> ${casoId}</div>
      <div><b>Fecha:</b> ${fecha}</div>
      <div><b>Nro. Informe:</b> ${numero || '—'}</div>
    </div>
  </div>
</div>

<h4 style="margin:8px 0 4px 0;">${titulo || 'INFORME PSICOLÓGICO'}</h4>

<h5 style="margin:10px 0 4px 0;">1. Datos personales</h5>
<p>...</p>

<h5 style="margin:10px 0 4px 0;">2. Motivo de consulta</h5>
<p>...</p>

<h5 style="margin:10px 0 4px 0;">3. Antecedentes</h5>
<p>...</p>

<h5 style="margin:10px 0 4px 0;">4. Técnicas e instrumentos empleados</h5>
<ul>
  <li>...</li>
</ul>

<h5 style="margin:10px 0 4px 0;">5. Resultados</h5>
<p>...</p>

<h5 style="margin:10px 0 4px 0;">6. Conclusiones</h5>
<p>...</p>

<h5 style="margin:10px 0 4px 0;">7. Recomendaciones</h5>
<ol>
  <li>...</li>
</ol>

<div style="margin-top:40px;display:flex;justify-content:space-between;font-size:12px;">
  <div style="text-align:center;width:45%;">
    <div>__________________________</div>
    <div>Profesional responsable</div>
  </div>
  <div style="text-align:center;width:45%;">
    <div>__________________________</div>
    <div>Vo.Bo.</div>
  </div>
</div>
    `;
  },

  legal({ casoId, fecha, titulo, numero }) {
    return `
<div style="border:1px solid #333;border-radius:6px;padding:12px;margin-bottom:12px;font-size:12px">
  <div style="display:flex;justify-content:space-between">
    <div><div style="font-weight:700">D.I.O. — ÁREA LEGAL</div></div>
    <div style="text-align:right;font-size:11px;color:#555">
      <div><b>Caso:</b> ${casoId}</div>
      <div><b>Fecha:</b> ${fecha}</div>
      <div><b>Nro. Informe:</b> ${numero || '—'}</div>
    </div>
  </div>
</div>

<h4>${titulo || 'INFORME LEGAL'}</h4>

<h5>I. Objeto</h5>
<p>...</p>

<h5>II. Fundamentación de hecho</h5>
<p>Relación circunstanciada de los hechos...</p>

<h5>III. Fundamentación de derecho</h5>
<ul>
  <li>Constitución Política del Estado — Art. 15 ...</li>
  <li>Ley 348 — Art. 83 ...</li>
  <li>Código Penal — Art. 312 (Abuso Sexual) ...</li>
</ul>

<h5>IV. Petitorio</h5>
<p>...</p>

<div style="margin-top:36px;text-align:center">
  __________________________<br>
  Abg. Responsable
</div>
    `;
  },

  social({ casoId, fecha, titulo, numero }) {
    return `
<div style="border:1px solid #333;border-radius:6px;padding:12px;margin-bottom:12px;font-size:12px">
  <div style="display:flex;justify-content:space-between">
    <div><div style="font-weight:700">D.I.O. — ÁREA SOCIAL</div></div>
    <div style="text-align:right;font-size:11px;color:#555">
      <div><b>Caso:</b> ${casoId}</div>
      <div><b>Fecha:</b> ${fecha}</div>
      <div><b>Nro. Informe:</b> ${numero || '—'}</div>
    </div>
  </div>
</div>

<h4>${titulo || 'INFORME SOCIAL'}</h4>

<h5>1. Datos de la familia</h5>
<p>...</p>

<h5>2. Visita domiciliaria / Observaciones del entorno</h5>
<p>...</p>

<h5>3. Situación socioeconómica</h5>
<p>...</p>

<h5>4. Conclusiones y recomendaciones</h5>
<p>...</p>

<div style="margin-top:36px;text-align:center">
  __________________________<br>
  Trabajadora Social
</div>
    `;
  },
};
