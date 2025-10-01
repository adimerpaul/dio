
export const InformeHtml = {
  informeSocial(caso) {
    // ===== Helpers =====
    const fmt = v => (v === null || v === undefined || v === '' ? '—' : String(v));
    const fmtDate = v => {
      if (!v) return '—';
      const d = new Date(v);
      if (isNaN(d.getTime())) return fmt(v);
      const y = d.getFullYear();
      const m = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${y}-${m}-${day}`;
    };

    const fullNameDenunciante = (d = {}) =>
      [d.denunciante_nombres, d.denunciante_paterno, d.denunciante_materno]
        .filter(Boolean).join(' ') || '—';

    // ===== Datos =====
    const denunciante = (caso?.denunciantes && caso.denunciantes[0]) ? caso.denunciantes[0] : {};
    const nombre = fullNameDenunciante(denunciante);
    const edad = fmt(denunciante?.denunciante_edad);
    const fnac = fmtDate(denunciante?.denunciante_fecha_nacimiento);
    const lnac = fmt(denunciante?.denunciante_lugar_nacimiento || '—');
    const grado = fmt(denunciante?.denunciante_grado);
    const eciv = fmt(denunciante?.denunciante_estado_civil);
    const ocu = fmt(denunciante?.denunciante_ocupacion);
    const contacto = fmt(denunciante?.denunciante_relacion);

    const tipologia = fmt(caso?.caso_tipologia || caso?.principal || '—');
    const numeroCaso = fmt(caso?.caso_numero);

    return `
<div style="border:1px solid #333;border-radius:6px;padding:14px;margin-bottom:12px;font-size:13px;line-height:1.4">

  <div style="text-align:center; font-weight:700; font-size:16px; margin-bottom:12px;">
    INFORME SOCIAL
  </div>

  <div style="margin-bottom:8px"><b>A :</b> __________________________</div>
  <div style="margin-bottom:8px">
    <b>DE :</b> <br/>
    TRABAJADORA SOCIAL - DIRECCION DE IGUALDAD DE OPORTUNIDADES
  </div>

  <div style="margin-top:10px;">
    <b>TIPOLOGIA:</b> ${tipologia}<br/>
    <b>CASO:</b> ${numeroCaso}
  </div>

  <div style="margin:18px 0; font-weight:700">I. REFERENCIA DEL CASO</div>
  <p>...</p>

  <div style="margin:18px 0; font-weight:700">II. DATOS PERSONALES</div>
  <div><b>NOMBRE Y APELLIDO:</b> ${nombre}</div>
  <div><b>EDAD:</b> ${edad}</div>
  <div><b>FECHA DE NACIMIENTO:</b> ${fnac}</div>
  <div><b>LUGAR DE NACIMIENTO:</b> ${lnac}</div>
  <div><b>GRADO DE INSTRUCCIÓN:</b> ${grado}</div>
  <div><b>ESTADO CIVIL:</b> ${eciv}</div>
  <div><b>OCUPACION:</b> ${ocu}</div>
  <div><b>NOMBRE DE CONTACTO:</b> ${contacto}</div>

  <div style="margin:18px 0; font-weight:700">III. GRUPO FAMILIAR NUCLEAR</div>
  <p>...</p>

  <div style="margin:18px 0; font-weight:700">IV. INTERVENCION TECNICOS Y/O INSTRUMENTOS UTILIZADOS</div>
  <p>...</p>

  <div style="margin:18px 0; font-weight:700">V. HISTORIA SOCIAL</div>
  <p>...</p>

  <div style="margin:18px 0; font-weight:700">VI. SITUACION ACTUAL</div>
  <p>...</p>

  <div style="margin:18px 0; font-weight:700">VII. DIAGNOSTICO SOCIAL</div>
  <p>...</p>

  <div style="margin:18px 0; font-weight:700">VIII. RECOMENDACIONES</div>
  <p>...</p>

  <div style="margin-top:36px;text-align:center">
    __________________________<br/>
    <br/>
    Trabajadora Social
  </div>
</div>
  `;
  },

};
