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
legal(caso) {
  // ===== Helpers =====
  const fmt = (v) => (v === null || v === undefined || v === '') ? '—' : String(v);
  const yesNo = (v) => (v === true || v === 1 || v === '1') ? 'Sí' : (v === false || v === 0 || v === '0') ? 'No' : '—';
  const fmtDate = (v) => {
    if (!v) return '—';
    const d = new Date(v);
    if (isNaN(d.getTime())) return fmt(v);
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,'0');
    const day = String(d.getDate()).padStart(2,'0');
    return `${y}-${m}-${day}`;
  };
  const todayBo = () => {
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,'0');
    const day = String(d.getDate()).padStart(2,'0');
    return `${day}/${m}/${y}`;
  };
  const fullNameDenunciante = (d = {}) =>
    [d.denunciante_nombres, d.denunciante_paterno, d.denunciante_materno].filter(Boolean).join(' ') || '—';
  const fullNameDenunciado = (d = {}) =>
    [d.denunciado_nombres, d.denunciado_paterno, d.denunciado_materno].filter(Boolean).join(' ') || '—';

  // ===== Datos base =====
  const denunciante = (caso?.denunciantes && caso.denunciantes[0]) ? caso.denunciantes[0] : {};
  const denunciado  = (caso?.denunciados   && caso.denunciados[0])   ? caso.denunciados[0]   : {};

  const motivo = fmt(caso?.principal || caso?.caso_tipologia);
  const ciudadaniaDigital = yesNo(caso?.documento_ciudadania_digital);

  // Campos denunciante
  const denNomAp = fullNameDenunciante(denunciante);
  const denFNac  = fmtDate(denunciante?.denunciante_fecha_nacimiento);
  const denLNac  = fmt(denunciante?.denunciante_lugar_nacimiento);
  const denEdad  = fmt(denunciante?.denunciante_edad);
  const denCI    = fmt(denunciante?.denunciante_nro);
  const denECiv  = fmt(denunciante?.denunciante_estado_civil);
  const denGrado = fmt(denunciante?.denunciante_grado);
  const denDir   = fmt(denunciante?.denunciante_residencia || denunciante?.denunciante_domicilio_actual || caso?.caso_direccion);
  const denOcu   = fmt(denunciante?.denunciante_ocupacion);
  const denTel   = fmt(denunciante?.denunciante_telefono);
  const denMail  = '—'; // no viene en tu JSON

  // Campos denunciado
  const demNomAp = fullNameDenunciado(denunciado);
  const demFNac  = fmtDate(denunciado?.denunciado_fecha_nacimiento);
  const demLNac  = fmt(denunciado?.denunciado_lugar_nacimiento);
  const demEdad  = fmt(denunciado?.denunciado_edad);
  const demCI    = fmt(denunciado?.denunciado_nro);
  const demECiv  = fmt(denunciado?.denunciado_estado_civil);
  const demGrado = fmt(denunciado?.denunciado_grado);
  const demDir   = fmt(denunciado?.denunciado_residencia || denunciado?.denunciado_domicilio_actual);
  const demOcu   = fmt(denunciado?.denunciado_ocupacion);
  const demTel   = fmt(denunciado?.denunciado_telefono);
  const demMail  = '—'; // no viene en tu JSON

  // Encabezado (abogada/o responsable si existe)
  const abogado = fmt(caso?.legal_user?.name ? `ABG. ${caso.legal_user.name}` : 'ABG.');

  // ===== HTML =====
  return `
<div style="border:1px solid #333;border-radius:6px;padding:14px;margin-bottom:12px;font-size:13px; line-height:1.35">
  <div style="text-align:center; font-weight:700; font-size:16px; margin-bottom:10px">INFORME LEGAL</div>

  <div style="margin-bottom:10px">
    <div><b>A :</b> SEÑOR REPRESENTANTE DEL MINISTERIO PUBLICO</div>
    <div><b>DE :</b> ${abogado}</div>
  </div>

  <div style="margin:14px 0; font-weight:700">I. FORMULACION DE DENUNCIA</div>
  <div style="margin:14px 0; font-weight:700">II. FUNDAMENTO DEL HECHO</div>
  <div style="margin:14px 0; font-weight:700">III. FUNDAMENTO DE DERECHO</div>
  <div style="margin:14px 0; font-weight:700">IV. PETITORIO</div>
  <div style="margin:14px 0; font-weight:700">V. OTROSIS</div>

  <div style="margin:18px 0; font-weight:700">I. FORMULA DENUNCIA POR EL MOTIVO DE :</div>
  <div style="margin:6px 0 14px 0">${motivo}</div>

  <div style="margin:10px 0; font-weight:700">DATOS GENERALES DEL DENUNCIANTE :</div>
  <div><b>Nombres y Apellidos:</b> ${denNomAp}</div>
  <div><b>Fecha de Nacimiento:</b> ${denFNac}</div>
  <div><b>Lugar de Nacimiento:</b> ${denLNac}</div>
  <div><b>Edad:</b> ${denEdad}</div>
  <div><b>C.I.:</b> ${denCI}</div>
  <div><b>Estado Civil:</b> ${denECiv}</div>
  <div><b>Grado de Instrucción:</b> ${denGrado}</div>
  <div><b>Dirección:</b> ${denDir}</div>
  <div><b>Ocupación:</b> ${denOcu}</div>
  <div><b>Teléfono Celular:</b> ${denTel}</div>
  <div><b>Correo electrónico:</b> ${denMail}</div>
  <div><b>Ciudadanía Digital:</b> ${ciudadaniaDigital}</div>

  <div style="margin:16px 0; font-weight:700">DATOS DEL DEMANDADO</div>
  <div><b>Nombres y Apellidos:</b> ${demNomAp}</div>
  <div><b>Fecha de Nacimiento:</b> ${demFNac}</div>
  <div><b>Lugar de Nacimiento:</b> ${demLNac}</div>
  <div><b>Edad:</b> ${demEdad}</div>
  <div><b>C.I.:</b> ${demCI}</div>
  <div><b>Estado Civil:</b> ${demECiv}</div>
  <div><b>Grado de Instrucción:</b> ${demGrado}</div>
  <div><b>Dirección:</b> ${demDir}</div>
  <div><b>Ocupación:</b> ${demOcu}</div>
  <div><b>Teléfono Celular:</b> ${demTel}</div>
  <div><b>Correo electrónico:</b> ${demMail}</div>

  <div style="margin:18px 0; font-weight:700">II. FUNDAMENTO DEL HECHO</div>
  <p style="margin:6px 0">...</p>

  <div style="margin:18px 0; font-weight:700">III. FUNDAMENTO DE DERECHO</div>
  <p style="margin:6px 0">...</p>

  <div style="margin:18px 0; font-weight:700">IV. PETITORIO</div>
  <p style="margin:6px 0">...</p>

  <div style="margin:18px 0; font-weight:700">V. OTROSIS</div>
  <p style="margin:6px 0">Por un derecho y protección a la Mujer…</p>

  <div style="margin-top:24px;">Oruro, ${todayBo()}</div>

  <div style="margin-top:36px;text-align:center">
    __________________________<br>
    ${abogado}
  </div>
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
  dirigido_otros(caso) {
    // ===== Helpers =====
    const fmt = v => (v === null || v === undefined || v === '' ? '—' : String(v));
    const yesNo = v =>
      (v === true || v === 1 || v === '1') ? 'Sí' :
        (v === false || v === 0 || v === '0') ? 'No' : '—';

    const fmtDate = v => {
      if (!v) return '—';
      const d = new Date(v);
      if (isNaN(d.getTime())) return fmt(v);
      const y = d.getFullYear();
      const m = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${y}-${m}-${day}`;
    };

    const todayBo = () => {
      const d = new Date();
      const y = d.getFullYear();
      const m = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${day}/${m}/${y}`;
    };

    const fullNameDenunciante = (d = {}) =>
      [d.denunciante_nombres, d.denunciante_paterno, d.denunciante_materno]
        .filter(Boolean).join(' ') || '—';

    const fullNameDenunciado = (d = {}) =>
      [d.denunciado_nombres, d.denunciado_paterno, d.denunciado_materno]
        .filter(Boolean).join(' ') || '—';

    // ===== Datos =====
    const denunciante = (caso?.denunciantes && caso.denunciantes[0]) ? caso.denunciantes[0] : {};
    const denunciado  = (caso?.denunciados   && caso.denunciados[0])   ? caso.denunciados[0]   : {};

    const motivo = fmt(caso?.principal || caso?.caso_tipologia);
    const ciudadaniaDigital = yesNo(caso?.documento_ciudadania_digital);

    // Denunciante
    const denNomAp = fullNameDenunciante(denunciante);
    const denFNac  = fmtDate(denunciante?.denunciante_fecha_nacimiento);
    const denLNac  = fmt(denunciante?.denunciante_lugar_nacimiento || 'ORURO');
    const denEdad  = fmt(denunciante?.denunciante_edad);
    const denCI    = fmt(denunciante?.denunciante_nro);
    const denECiv  = fmt(denunciante?.denunciante_estado_civil);
    const denGrado = fmt(denunciante?.denunciante_grado);
    const denDir   = fmt(denunciante?.denunciante_residencia || denunciante?.denunciante_domicilio_actual || caso?.caso_direccion || 'ORURO');
    const denOcu   = fmt(denunciante?.denunciante_ocupacion);
    const denTel   = fmt(denunciante?.denunciante_telefono);
    const denMail  = '—';

    // Denunciado
    const demNomAp = fullNameDenunciado(denunciado);
    const demFNac  = fmtDate(denunciado?.denunciado_fecha_nacimiento);
    const demLNac  = fmt(denunciado?.denunciado_lugar_nacimiento || 'ORURO');
    const demEdad  = fmt(denunciado?.denunciado_edad);
    const demCI    = fmt(denunciado?.denunciado_nro);
    const demECiv  = fmt(denunciado?.denunciado_estado_civil);
    const demGrado = fmt(denunciado?.denunciado_grado || '—');
    const demDir   = fmt(denunciado?.denunciado_residencia || denunciado?.denunciado_domicilio_actual || 'ORURO');
    const demOcu   = fmt(denunciado?.denunciado_ocupacion || denunciado?.denunciado_ocupacion_exacto || '—');
    const demTel   = fmt(denunciado?.denunciado_telefono || denunciado?.denunciado_movil || denunciado?.denunciado_fijo);
    const demMail  = '—';

    // Abogado responsable
    const abogadoNombre = (caso?.legal_user?.name || caso?.user?.name || 'Responsable').toString();

    // ===== HTML =====
    return `
<div style="border:1px solid #333;border-radius:6px;padding:14px;margin-bottom:12px;font-size:13px;line-height:1.35">

  <div style="margin-bottom:10px">
    <div><b>A :</b> __________________________</div>
    <div><b>DE :</b> __________________________</div>
  </div>

  <div style="margin:14px 0; font-weight:700">I. FORMULACION DE DENUNCIA</div>
  <div style="margin:14px 0; font-weight:700">II. FUNDAMENTO DEL HECHO</div>
  <div style="margin:14px 0; font-weight:700">III. FUNDAMENTO DE DERECHO</div>
  <div style="margin:14px 0; font-weight:700">IV. PETITORIO</div>
  <div style="margin:14px 0; font-weight:700">V. OTROSIS</div>

  <div style="margin:18px 0; font-weight:700">I. FORMULA DENUNCIA POR EL MOTIVO DE :</div>
  <div style="margin:6px 0 14px 0">${motivo}</div>

  <div style="margin:10px 0; font-weight:700">DATOS GENERALES DEL DENUNCIANTE :</div>
  <div><b>Nombres y Apellidos:</b> ${denNomAp}</div>
  <div><b>Fecha de Nacimiento:</b> ${denFNac}</div>
  <div><b>Lugar de Nacimiento:</b> ${denLNac}</div>
  <div><b>Edad:</b> ${denEdad}</div>
  <div><b>C.I.:</b> ${denCI}</div>
  <div><b>Estado Civil:</b> ${denECiv}</div>
  <div><b>Grado de Instrucción:</b> ${denGrado}</div>
  <div><b>Dirección:</b> ${denDir}</div>
  <div><b>Ocupación:</b> ${denOcu}</div>
  <div><b>Teléfono Celular:</b> ${denTel}</div>
  <div><b>Correo electrónico:</b> ${denMail}</div>
  <div><b>Ciudadanía Digital:</b> ${ciudadaniaDigital}</div>

  <div style="margin:16px 0; font-weight:700">DATOS DEL DEMANDADO</div>
  <div><b>Nombres y Apellidos:</b> ${demNomAp}</div>
  <div><b>Fecha de Nacimiento:</b> ${demFNac}</div>
  <div><b>Lugar de Nacimiento:</b> ${demLNac}</div>
  <div><b>Edad:</b> ${demEdad}</div>
  <div><b>C.I.:</b> ${demCI}</div>
  <div><b>Estado Civil:</b> ${demECiv}</div>
  <div><b>Grado de Instrucción:</b> ${demGrado}</div>
  <div><b>Dirección:</b> ${demDir}</div>
  <div><b>Ocupación:</b> ${demOcu}</div>
  <div><b>Teléfono Celular:</b> ${demTel}</div>
  <div><b>Correo electrónico:</b> ${demMail}</div>

  <div style="margin:18px 0; font-weight:700">II. FUNDAMENTO DEL HECHO</div>
  <p style="margin:6px 0">...</p>

  <div style="margin:18px 0; font-weight:700">III. FUNDAMENTO DE DERECHO</div>
  <p style="margin:6px 0">...</p>

  <div style="margin:18px 0; font-weight:700">IV. PETITORIO</div>
  <p style="margin:6px 0">...</p>

  <div style="margin:18px 0; font-weight:700">V. OTROSIS</div>
  <p style="margin:6px 0">...</p>

  <div style="margin-top:24px;">Oruro, ${todayBo()}</div>

  <div style="margin-top:36px;display:flex;justify-content:space-between;">
    <div style="text-align:center;width:45%;">
      __________________________<br>
      NOMBRE Y FIRMA DEL DENUNCIANTE
    </div>
    <div style="text-align:center;width:45%;">
      __________________________<br>
      NOMBRE Y FIRMA DEL ABOGADO
    </div>
  </div>
</div>
  `;
  },
  // DENUNCIA PENAL ANTE MINISTERIO PÚBLICO (formato DIO / Oruro)
// Campos esperados (todos opcionales; la plantilla se autopuebla con "—" o textos base):
// {
//   casoId, fecha, numero, ciudad, fiscaliaTitulo, titulo,
//   denunciante: { nombre, ci, fecha_nac, lugar_nac, edad, ocupacion, estado_civil, domicilio, celular, correo },
//   denunciado:  { nombre, ci, fecha_nac, nacionalidad, ocupacion, estado_civil, domicilio, celular, correo },
//   relato, fundamentos_derecho_extra, petitorio_extra,
//   anexos: ['...', '...'], // si se omite, se muestran varios por defecto
//   abogado: { nombre, correo, whatsapp },
//   ciudadania_digital_denunciante // ej. número
// }
  dirigido_juzgado(caso) {
    // ===== Helpers =====
    const fmt = v => (v === null || v === undefined || v === '' ? '—' : String(v));
    const yesNo = v =>
      (v === true || v === 1 || v === '1') ? 'Sí' :
        (v === false || v === 0 || v === '0') ? 'No' : '—';

    const fmtDate = v => {
      if (!v) return '—';
      const d = new Date(v);
      if (isNaN(d.getTime())) return fmt(v);
      const y = d.getFullYear();
      const m = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${y}-${m}-${day}`;
    };

    const todayBo = () => {
      const d = new Date();
      const y = d.getFullYear();
      const m = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${day}/${m}/${y}`;
    };

    const fullNameDenunciante = (d = {}) =>
      [d.denunciante_nombres, d.denunciante_paterno, d.denunciante_materno]
        .filter(Boolean).join(' ') || '—';

    const fullNameDenunciado = (d = {}) =>
      [d.denunciado_nombres, d.denunciado_paterno, d.denunciado_materno]
        .filter(Boolean).join(' ') || '—';

    // ===== Datos desde el caso =====
    const denunciante = (caso?.denunciantes && caso.denunciantes[0]) ? caso.denunciantes[0] : {};
    const denunciado  = (caso?.denunciados   && caso.denunciados[0])   ? caso.denunciados[0]   : {};

    const motivo = fmt(caso?.principal || caso?.caso_tipologia);
    const ciudadaniaDigital = yesNo(caso?.documento_ciudadania_digital);

    // Denunciante
    const denNomAp = fullNameDenunciante(denunciante);
    const denFNac  = fmtDate(denunciante?.denunciante_fecha_nacimiento);
    const denLNac  = fmt(denunciante?.denunciante_lugar_nacimiento);
    const denEdad  = fmt(denunciante?.denunciante_edad);
    const denCI    = fmt(denunciante?.denunciante_nro);
    const denECiv  = fmt(denunciante?.denunciante_estado_civil);
    const denGrado = fmt(denunciante?.denunciante_grado);
    const denDir   = fmt(denunciante?.denunciante_residencia || denunciante?.denunciante_domicilio_actual || caso?.caso_direccion || caso?.caso_zona || 'ORURO');
    const denOcu   = fmt(denunciante?.denunciante_ocupacion);
    const denTel   = fmt(denunciante?.denunciante_telefono);
    const denMail  = '—';

    // Denunciado
    const demNomAp = fullNameDenunciado(denunciado);
    const demFNac  = fmtDate(denunciado?.denunciado_fecha_nacimiento);
    const demLNac  = fmt(denunciado?.denunciado_lugar_nacimiento || 'ORURO');
    const demEdad  = fmt(denunciado?.denunciado_edad);
    const demCI    = fmt(denunciado?.denunciado_nro);
    const demECiv  = fmt(denunciado?.denunciado_estado_civil);
    const demGrado = fmt(denunciado?.denunciado_grado || 'BACHILLER');
    const demDir   = fmt(denunciado?.denunciado_residencia || denunciado?.denunciado_domicilio_actual || 'ORURO');
    const demOcu   = fmt(denunciado?.denunciado_ocupacion || denunciado?.denunciado_ocupacion_exacto || '—');
    const demTel   = fmt(denunciado?.denunciado_telefono || denunciado?.denunciado_movil || denunciado?.denunciado_fijo);
    const demMail  = '—';

    // Abogado responsable
    const abogadoNombre = (caso?.legal_user?.name || caso?.user?.name || 'Responsable').toString();

    // ===== HTML =====
    return `
<div style="border:1px solid #333;border-radius:6px;padding:14px;margin-bottom:12px;font-size:13px;line-height:1.35">
  <div style="text-align:center; font-weight:700; font-size:15px; margin-bottom:8px;">
    DIRECCION DE IGUALDAD DE OPORTUNIDADES
  </div>

  <div style="margin-bottom:10px">
    <div><b>A :</b> SEÑOR REPRESENTANTE DEL JUZGADO PUBLICO DE FAMILIA LA CIUDAD DE ORURO</div>
    <div><b>DE :</b> ABG. ${abogadoNombre}</div>
  </div>

  <div style="margin:14px 0; font-weight:700">I. FORMULACION DE DENUNCIA</div>
  <div style="margin:14px 0; font-weight:700">II. FUNDAMENTO DEL HECHO</div>
  <div style="margin:14px 0; font-weight:700">III. FUNDAMENTO DE DERECHO</div>
  <div style="margin:14px 0; font-weight:700">IV. PETITORIO</div>
  <div style="margin:14px 0; font-weight:700">V. OTROSIS</div>

  <div style="margin:18px 0; font-weight:700">I. FORMULA DENUNCIA POR EL MOTIVO DE :</div>
  <div style="margin:6px 0 14px 0">${motivo}</div>

  <div style="margin:10px 0; font-weight:700">DATOS GENERALES DEL DENUNCIANTE :</div>
  <div><b>Nombres y Apellidos:</b> ${denNomAp}</div>
  <div><b>Fecha de Nacimiento:</b> ${denFNac}</div>
  <div><b>Lugar de Nacimiento:</b> ${denLNac}</div>
  <div><b>Edad:</b> ${denEdad}</div>
  <div><b>C.I.:</b> ${denCI}</div>
  <div><b>Estado Civil:</b> ${denECiv}</div>
  <div><b>Grado de Instrucción:</b> ${denGrado}</div>
  <div><b>Dirección:</b> ${denDir}</div>
  <div><b>Ocupación:</b> ${denOcu}</div>
  <div><b>Teléfono Celular:</b> ${denTel}</div>
  <div><b>Correo electrónico:</b> ${denMail}</div>
  <div><b>Ciudadanía Digital:</b> ${ciudadaniaDigital}</div>

  <div style="margin:16px 0; font-weight:700">DATOS DEL DEMANDADO</div>
  <div><b>Nombres y Apellidos:</b> ${demNomAp}</div>
  <div><b>Fecha de Nacimiento:</b> ${demFNac}</div>
  <div><b>Lugar de Nacimiento:</b> ${demLNac}</div>
  <div><b>Edad:</b> ${demEdad}</div>
  <div><b>C.I.:</b> ${demCI}</div>
  <div><b>Estado Civil:</b> ${demECiv}</div>
  <div><b>Grado de Instrucción:</b> ${demGrado}</div>
  <div><b>Dirección:</b> ${demDir}</div>
  <div><b>Ocupación:</b> ${demOcu}</div>
  <div><b>Teléfono Celular:</b> ${demTel}</div>
  <div><b>Correo electrónico:</b> ${demMail}</div>

  <div style="margin:18px 0; font-weight:700">II. FUNDAMENTO DE HECHO</div>
  <p style="margin:6px 0">...</p>

  <div style="margin:18px 0; font-weight:700">III. FUNDAMENTO DE DERECHO</div>
  <p style="margin:6px 0">...</p>

  <div style="margin:18px 0; font-weight:700">IV. PETITORIO</div>
  <p style="margin:6px 0">...</p>

  <div style="margin:18px 0; font-weight:700">V. OTROSIS</div>
  <p style="margin:6px 0">Por un derecho y protección a la Mujer…</p>

  <div style="margin-top:24px;">Oruro, ${todayBo()}</div>

  <div style="margin-top:36px;text-align:center">
    __________________________<br>
    NOMBRE Y FIRMA DEL ABOGADO
  </div>
</div>
  `;
  },
  dirigido_mp(caso) {
    // ===== Helpers =====
    const fmt = v => (v === null || v === undefined || v === '' ? '—' : String(v));
    const yesNo = v =>
      (v === true || v === 1 || v === '1') ? 'Sí' :
        (v === false || v === 0 || v === '0') ? 'No' : '—';

    const fmtDate = v => {
      if (!v) return '—';
      const d = new Date(v);
      if (isNaN(d.getTime())) return fmt(v);
      const y = d.getFullYear();
      const m = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${y}-${m}-${day}`;
    };

    const todayBo = () => {
      const d = new Date();
      const y = d.getFullYear();
      const m = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${day}/${m}/${y}`;
    };

    const fullNameDenunciante = (d = {}) =>
      [d.denunciante_nombres, d.denunciante_paterno, d.denunciante_materno]
        .filter(Boolean).join(' ') || '—';

    const fullNameDenunciado = (d = {}) =>
      [d.denunciado_nombres, d.denunciado_paterno, d.denunciado_materno]
        .filter(Boolean).join(' ') || '—';

    // ===== Extracción de datos del caso =====
    const denunciante = (caso?.denunciantes && caso.denunciantes[0]) ? caso.denunciantes[0] : {};
    const denunciado  = (caso?.denunciados   && caso.denunciados[0])   ? caso.denunciados[0]   : {};

    const motivo = fmt(caso?.principal || caso?.caso_tipologia);
    const ciudadaniaDigital = yesNo(caso?.documento_ciudadania_digital);

    // Denunciante
    const denNomAp = fullNameDenunciante(denunciante);
    const denFNac  = fmtDate(denunciante?.denunciante_fecha_nacimiento);
    const denLNac  = fmt(denunciante?.denunciante_lugar_nacimiento);
    const denEdad  = fmt(denunciante?.denunciante_edad);
    const denCI    = fmt(denunciante?.denunciante_nro);
    const denECiv  = fmt(denunciante?.denunciante_estado_civil);
    const denGrado = fmt(denunciante?.denunciante_grado);
    const denDir   = fmt(denunciante?.denunciante_residencia || caso?.caso_direccion);
    const denOcu   = fmt(denunciante?.denunciante_ocupacion);
    const denTel   = fmt(denunciante?.denunciante_telefono);
    const denMail  = '—';

    // Denunciado
    const demNomAp = fullNameDenunciado(denunciado);
    const demFNac  = fmtDate(denunciado?.denunciado_fecha_nacimiento);
    const demLNac  = fmt(denunciado?.denunciado_lugar_nacimiento);
    const demEdad  = fmt(denunciado?.denunciado_edad);
    const demCI    = fmt(denunciado?.denunciado_nro);
    const demECiv  = fmt(denunciado?.denunciado_estado_civil);
    const demGrado = fmt(denunciado?.denunciado_grado);
    const demDir   = fmt(denunciado?.denunciado_residencia || denunciado?.denunciado_domicilio_actual);
    const demOcu   = fmt(denunciado?.denunciado_ocupacion || denunciado?.denunciado_ocupacion_exacto);
    const demTel   = fmt(denunciado?.denunciado_telefono || denunciado?.denunciado_movil || denunciado?.denunciado_fijo);
    const demMail  = '—';

    // Abogado responsable
    const abogadoNombre = (caso?.legal_user?.name || caso?.user?.name || 'Responsable').toString();
    const abogado = `ABG. ${abogadoNombre}`;

    // ===== HTML =====
    return `
<div style="border:1px solid #333;border-radius:6px;padding:14px;margin-bottom:12px;font-size:13px;line-height:1.35">

  <div style="margin-bottom:10px">
    <div><b>A :</b> SEÑOR REPRESENTANTE DEL JUZGADO PUBLICO DE FAMILIA LA CIUDAD DE ORURO</div>
    <div><b>DE :</b> ${abogado}</div>
  </div>

  <div style="margin:14px 0; font-weight:700">I. FORMULACION DE DENUNCIA</div>
  <div style="margin:14px 0; font-weight:700">II. FUNDAMENTO DEL HECHO</div>
  <div style="margin:14px 0; font-weight:700">III. FUNDAMENTO DE DERECHO</div>
  <div style="margin:14px 0; font-weight:700">IV. PETITORIO</div>
  <div style="margin:14px 0; font-weight:700">V. OTROSIS</div>

  <div style="margin:18px 0; font-weight:700">I. FORMULA DENUNCIA POR EL MOTIVO DE :</div>
  <div style="margin:6px 0 14px 0">${motivo}</div>

  <div style="margin:10px 0; font-weight:700">DATOS GENERALES DEL DENUNCIANTE :</div>
  <div><b>Nombres y Apellidos:</b> ${denNomAp}</div>
  <div><b>Fecha de Nacimiento:</b> ${denFNac}</div>
  <div><b>Lugar de Nacimiento:</b> ${denLNac}</div>
  <div><b>Edad:</b> ${denEdad}</div>
  <div><b>C.I.:</b> ${denCI}</div>
  <div><b>Estado Civil:</b> ${denECiv}</div>
  <div><b>Grado de Instrucción:</b> ${denGrado}</div>
  <div><b>Dirección:</b> ${denDir}</div>
  <div><b>Ocupación:</b> ${denOcu}</div>
  <div><b>Teléfono Celular:</b> ${denTel}</div>
  <div><b>Correo electrónico:</b> ${denMail}</div>
  <div><b>Ciudadanía Digital:</b> ${ciudadaniaDigital}</div>

  <div style="margin:16px 0; font-weight:700">DATOS DEL DEMANDADO</div>
  <div><b>Nombres y Apellidos:</b> ${demNomAp}</div>
  <div><b>Fecha de Nacimiento:</b> ${demFNac}</div>
  <div><b>Lugar de Nacimiento:</b> ${demLNac}</div>
  <div><b>Edad:</b> ${demEdad}</div>
  <div><b>C.I.:</b> ${demCI}</div>
  <div><b>Estado Civil:</b> ${demECiv}</div>
  <div><b>Grado de Instrucción:</b> ${demGrado}</div>
  <div><b>Dirección:</b> ${demDir}</div>
  <div><b>Ocupación:</b> ${demOcu}</div>
  <div><b>Teléfono Celular:</b> ${demTel}</div>
  <div><b>Correo electrónico:</b> ${demMail}</div>

  <div style="margin:18px 0; font-weight:700">II. FUNDAMENTO DE HECHO</div>
  <p style="margin:6px 0">...</p>

  <div style="margin:18px 0; font-weight:700">III. FUNDAMENTO DE DERECHO</div>
  <p style="margin:6px 0">...</p>

  <div style="margin:18px 0; font-weight:700">IV. PETITORIO</div>
  <p style="margin:6px 0">...</p>

  <div style="margin:18px 0; font-weight:700">V. OTROSIS</div>
  <p style="margin:6px 0">Por un derecho y protección a la Mujer…</p>

  <div style="margin-top:24px;">Oruro, ${todayBo()}</div>

  <div style="margin-top:36px;text-align:center">
    __________________________<br>
    NOMBRE Y FIRMA DEL ABOGADO
  </div>
</div>
  `;
  },
  denuncia_mp (p = {}) {
    const d = (v, fb = '—') => (v == null || v === '' ? fb : v)
    const denunciante = p.denunciante || {}
    const denunciado  = p.denunciado  || {}
    const anexos = (p.anexos && p.anexos.length ? p.anexos : [
      'Formulario de Registro domiciliario de la denunciante.',
      'Formulario de Registro domiciliario del denunciado.',
      'Croquis denunciante.',
      'Croquis del denunciado.',
      'Fotocopia simple de la Cédula de Identidad de la denunciante.',
      'Fotocopia simple de la Cédula de Identidad del denunciado.',
      'Informe Psicológico emitido por profesional de la D.I.O.'
    ])

    const ciudad = d(p.ciudad, 'ORURO')
    const fecha  = d(p.fecha)
    const numero = d(p.numero)
    const fiscaliaTitulo = d(p.fiscaliaTitulo, 'SEÑOR REPRESENTANTE DEL MINISTERIO PÚBLICO DE LA CIUDAD DE ORURO')

    return `
<div style="font-size:12px; line-height:1.35">
  <!-- Encabezado simple DomPDF-friendly -->
  <div style="border:1px solid #333;border-radius:6px;padding:10px;margin-bottom:10px;">
    <div style="font-weight:700">DIRECCIÓN DE IGUALDAD DE OPORTUNIDADES — DEFENSORÍA DE LA NIÑEZ Y ADOLESCENCIA</div>
    <div style="font-size:11px;color:#555">SERVICIO LEGAL INTEGRAL MUNICIPAL</div>
  </div>

  <div style="text-align:center; font-weight:700; text-transform:uppercase; margin-top:4px;">
    ${fiscaliaTitulo}
  </div>

  <div style="margin:10px 0 6px 0;">
    <b>I.</b>&nbsp; <b>FORMULA DENUNCIA POR EL DELITO DE ABUSO SEXUAL</b>, SOLICITANDO EL LEVANTAMIENTO DE DILIGENCIAS PRELIMINARES Y SE PROCEDA CONFORME A LEY.
  </div>

  <div style="margin:6px 0;">
    <b>II.</b>&nbsp; <b>FUNDAMENTO DE HECHO</b>
  </div>

  <div style="margin:6px 0;">
    <b>III.</b>&nbsp; <b>FUNDAMENTO DE DERECHO</b>
  </div>

  <div style="margin:6px 0;">
    <b>IV.</b>&nbsp; <b>PETITORIO</b>
  </div>

  <div style="margin:6px 0;">
    <b>V.</b>&nbsp; <b>OTROSÍES</b>
  </div>

  <hr style="margin:10px 0;">

  <!-- Datos Generales de Denunciante -->
  <h5 style="margin:6px 0 4px 0;">DATOS GENERALES DE DENUNCIANTE</h5>
  <table style="width:100%; border-collapse:collapse; font-size:12px">
    <tr><td style="width:35%"><b>Nombres y apellidos:</b></td><td>${d(denunciante.nombre)}</td></tr>
    <tr><td><b>Fecha de nacimiento:</b></td><td>${d(denunciante.fecha_nac)}</td></tr>
    <tr><td><b>Lugar de nacimiento:</b></td><td>${d(denunciante.lugar_nac)}</td></tr>
    <tr><td><b>Cédula de identidad:</b></td><td>${d(denunciante.ci)}</td></tr>
    <tr><td><b>Edad:</b></td><td>${d(denunciante.edad)}</td></tr>
    <tr><td><b>Ocupación:</b></td><td>${d(denunciante.ocupacion)}</td></tr>
    <tr><td><b>Estado Civil:</b></td><td>${d(denunciante.estado_civil)}</td></tr>
    <tr><td><b>Domicilio:</b></td><td>${d(denunciante.domicilio)}</td></tr>
    <tr><td><b>Teléfono Celular:</b></td><td>${d(denunciante.celular)}</td></tr>
    <tr><td><b>Correo electrónico:</b></td><td>${d(denunciante.correo)}</td></tr>
    <tr><td><b>Ciudadanía Digital:</b></td><td>${d(p.ciudadania_digital_denunciante)}</td></tr>
  </table>

  <h5 style="margin:12px 0 4px 0;">DATOS GENERALES DEL DENUNCIADO</h5>
  <table style="width:100%; border-collapse:collapse; font-size:12px">
    <tr><td style="width:35%"><b>Nombres y apellidos:</b></td><td>${d(denunciado.nombre)}</td></tr>
    <tr><td><b>Fecha de nacimiento:</b></td><td>${d(denunciado.fecha_nac)}</td></tr>
    <tr><td><b>Nacionalidad:</b></td><td>${d(denunciado.nacionalidad, 'Boliviana')}</td></tr>
    <tr><td><b>Cédula de identidad:</b></td><td>${d(denunciado.ci)}</td></tr>
    <tr><td><b>Ocupación:</b></td><td>${d(denunciado.ocupacion)}</td></tr>
    <tr><td><b>Estado Civil:</b></td><td>${d(denunciado.estado_civil)}</td></tr>
    <tr><td><b>Domicilio:</b></td><td>${d(denunciado.domicilio)}</td></tr>
    <tr><td><b>Teléfono Celular:</b></td><td>${d(denunciado.celular)}</td></tr>
    <tr><td><b>Correo electrónico:</b></td><td>${d(denunciado.correo, 'Se desconoce')}</td></tr>
  </table>

  <h5 style="margin:12px 0 4px 0;">II. FUNDAMENTO DE HECHO — RELACIÓN CIRCUNSTANCIADA</h5>
  <p style="text-align:justify;">
    ${d(p.relato, 'La denunciante refiere los hechos ocurridos, consignando fechas, lugares, personas involucradas y las acciones desplegadas.')}
  </p>

  <h5 style="margin:12px 0 4px 0;">III. FUNDAMENTO DE DERECHO</h5>
  <div style="margin-left:6px">
    <div style="margin:4px 0;"><b>a) DE LA CONSTITUCIÓN POLÍTICA DEL ESTADO — Artículo 15</b></div>
    <ul style="margin:0 0 0 16px;">
      <li>Toda persona tiene derecho a la vida y a la integridad física, psicológica y sexual. Nadie será torturado ni sufrirá tratos crueles, inhumanos, degradantes u humillantes.</li>
      <li>Las personas, en particular las mujeres, tienen derecho a no sufrir violencia física, sexual o psicológica.</li>
      <li>El Estado adoptará medidas para prevenir, eliminar y sancionar la violencia de género.</li>
    </ul>

    <div style="margin:8px 0 4px 0;"><b>b) DE LA LEY 348 — Artículo 83 (modifica el Código Penal)</b></div>
    <div style="margin:0 0 6px 0;">Se adecúan tipos penales y se agravan penas cuando la víctima es niña, niño o adolescente.</div>

    <div style="margin:4px 0;"><b>c) CÓDIGO PENAL — Artículo 312 (Abuso Sexual)</b></div>
    <div>Cuando, en las circunstancias previstas por ley, se realicen actos sexuales no constitutivos de penetración o acceso carnal, la pena será de seis (6) a diez (10) años de privación de libertad.</div>

    ${p.fundamentos_derecho_extra ? `<div style="margin-top:8px;">${p.fundamentos_derecho_extra}</div>` : ''}
  </div>

  <h5 style="margin:12px 0 4px 0;">IV. PETITORIO</h5>
  <p style="text-align:justify;">
    Por lo expuesto y al amparo de las normas citadas, solicito se admita la presente DENUNCIA por el delito de ABUSO SEXUAL, se disponga el levantamiento de diligencias preliminares y se remitan las actuaciones que correspondan, a efectos de que el hecho no quede impune, determinando responsabilidades conforme a derecho.
    ${p.petitorio_extra ? ` ${p.petitorio_extra}` : ''}
  </p>

  <h5 style="margin:12px 0 4px 0;">V. OTROSÍ 1º — Prueba literal</h5>
  <ul style="margin:0 0 0 16px;">
    ${anexos.map(x => `<li>${x}</li>`).join('')}
  </ul>

  <h5 style="margin:10px 0 4px 0;">OTROSÍ 2º — Medidas de protección</h5>
  <p>Al amparo de la Ley 348, solicito se evalúen medidas de protección a favor de la víctima y su entorno familiar para precautelar el normal desenvolvimiento de sus actividades.</p>

  <h5 style="margin:10px 0 4px 0;">OTROSÍ 3º — Domicilio procesal y contactos</h5>
  <table style="width:100%; border-collapse:collapse; font-size:12px">
    <tr><td style="width:30%"><b>Ciudadanía digital (víctima):</b></td><td>${d(p.ciudadania_digital_denunciante)}</td></tr>
    <tr><td><b>WhatsApp (víctima):</b></td><td>${d(denunciante.celular)}</td></tr>
    <tr><td><b>Correo electrónico (víctima):</b></td><td>${d(denunciante.correo)}</td></tr>
    <tr><td><b>Ciudadanía digital (abogado/a):</b></td><td>${d(p.abogado?.ciudadania)}</td></tr>
    <tr><td><b>Correo electrónico (abogado/a):</b></td><td>${d(p.abogado?.correo)}</td></tr>
    <tr><td><b>Celular / WhatsApp (abogado/a):</b></td><td>${d(p.abogado?.whatsapp)}</td></tr>
  </table>

  <div style="margin-top:18px;">
    ${d(ciudad)}, ${fecha ? fecha : '___ de __________ de _____'}
  </div>

  <div style="margin-top:26px; display:flex; justify-content:space-between;">
    <div style="text-align:center; width:45%;">
      __________________________<br/>
      Firma Denunciante
    </div>
    <div style="text-align:center; width:45%;">
      __________________________<br/>
      Abg. ${d(p.abogado?.nombre, 'Responsable')}
    </div>
  </div>
</div>
`
  },
};
