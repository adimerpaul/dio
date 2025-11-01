<template>
  <div>
    <!-- CABECERA -->
    <q-card flat bordered class="q-pa-sm q-mb-md rounded-borders">
      <div class="row items-start q-col-gutter-sm">
        <div class="col-12 col-md-8">
          <div class="text-subtitle1 text-weight-bold">Seguimiento del Caso</div>
          <div class="text-caption text-grey-7">
            {{ caso?.tipo || 'SLIM' }} · Caso <b>#{{ header.caso_id }}</b> · N° <b>{{ header.caso_numero || '—' }}</b>
          </div>
        </div>
        <div class="col-12 col-md-4 flex items-center justify-end">
          <q-chip dense color="indigo-1" text-color="indigo-9" class="q-mr-xs">
            {{ header.fecha_registro || '—' }}
          </q-chip>
          <q-btn
            flat color="primary" icon="refresh"
            @click="forceRefresh" :loading="loading" title="Actualizar"
            :label="loading ? '' : 'Actualizar'" no-caps dense
          />
        </div>
      </div>

      <q-separator class="q-my-sm"/>

      <!-- GRID COMPACTA -->
      <div class="row q-col-gutter-sm">
        <div class="col-12 col-md-3">
          <div class="lbl">Tipología</div>
          <div class="val">{{ header.tipologia || '—' }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="lbl">Modalidad</div>
          <div class="val">{{ header.modalidad || '—' }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="lbl">Zona</div>
          <div class="val">{{ header.zona || '—' }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="lbl">Dirección</div>
          <div class="val ellipsis">{{ header.direccion || '—' }}</div>
        </div>

        <div class="col-12 col-md-3">
          <div class="lbl">Denunciante</div>
          <div class="val">{{ header.denunciante || '—' }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="lbl">Denunciado</div>
          <div class="val">{{ header.denunciado || '—' }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="lbl">Fecha del hecho</div>
          <div class="val">{{ header.fecha_hecho || '—' }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="lbl">Registrado por</div>
          <div class="val">{{ header.registrado_por || '—' }}</div>
        </div>
      </div>

      <!-- VIOLENCIAS + RESPONSABLES -->
      <div class="row q-col-gutter-xs q-mt-sm items-center">
        <div class="col-12 col-md-8">
          <div class="lbl q-mb-xs">Tipos de violencia</div>
          <div class="row q-gutter-xs">
            <q-chip
              v-for="v in violenciaChips"
              :key="v.label"
              dense
              :color="v.on ? v.color : 'grey-3'"
              :text-color="v.on ? 'white' : 'grey-8'"
              square
            >
              {{ v.label }}
            </q-chip>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="lbl q-mb-xs">Responsables</div>
          <div class="row q-gutter-xs justify-end">
            <q-chip v-if="caso?.psicologica_user" dense color="pink-2" text-color="pink-10" square>
              Psic.: {{ caso.psicologica_user.name }}
            </q-chip>
            <q-chip v-if="caso?.trabajo_social_user" dense color="teal-2" text-color="teal-10" square>
              TS: {{ caso.trabajo_social_user.name }}
            </q-chip>
            <q-chip v-if="caso?.legal_user" dense color="amber-2" text-color="amber-10" square>
              Legal: {{ caso.legal_user.name }}
            </q-chip>
          </div>
        </div>
      </div>
    </q-card>
    <div class="row">
      <div class="col-12 col-md-12">
        <span class="text-bold">Asistente</span>
        <q-markup-table dense bordered flat>
          <thead class="bg-primary text-white">
            <tr>
              <th>Tipo</th>
              <th>Título</th>
              <th>Fecha</th>
              <th>Usuario</th>
              <th>Archivo</th>
            </tr>
          </thead>
          <tbody>
          <ArchivoFoto role="Asistente" :caso="caso" />
          <ArchivoFoto role="Auxiliar" :caso="caso" />
          </tbody>
        </q-markup-table>
      </div>
      <div class="col-12 col-md-12">
        <span class="text-bold">Social</span>
        <q-markup-table dense bordered flat>
          <thead class="bg-primary text-white">
            <tr>
              <th>Tipo</th>
              <th>Título</th>
              <th>Fecha</th>
              <th>Usuario</th>
              <th>Archivo</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="informe in caso.informes_sociales" :key="informe.id">
              <td>
                Informe
              </td>
              <td>{{ informe.titulo }}</td>
              <td>{{ informe.fecha || '—' }}</td>
              <td>{{ informe.user?.name || '—' }}</td>
              <td>
                <a @click="openSocialInforme(informe)">
                  Ver archivo
                </a>
              </td>
            </tr>
            <ArchivoFoto role="Social" :caso="caso" />
          </tbody>
        </q-markup-table>
      </div>
      <div class="col-12 col-md-12">
        <div class="text-bold">Psicológia</div>
<!--        <pre>{{caso.psicologicas}}</pre>-->
<!--        [-->
<!--        {-->
<!--        "id": 3,-->
<!--        "caseable_type": "App\\Models\\Caso",-->
<!--        "caseable_id": 11,-->
<!--        "user_id": 28,-->
<!--        "fecha": "2025-10-02",-->
<!--        "titulo": "informe juzgado",-->
<!--        "duracion_min": null,-->
<!--        "lugar": null,-->
<!--        "tipo": "Individual",-->
<!--        "contenido_html": "<div style=\"font-size:12px; line-height:1.35\">\n  <div style=\"text-align:center; font-weight:700; text-transform:uppercase;\">\n    INFORME PSICOLÓGICO\n  </div>\n  <div style=\"text-align:center; margin-top:2px; font-size:11px; color:#333\">\n    Nro. de Caso: 005/25&nbsp;&nbsp;|&nbsp;&nbsp;Caso ID: #11\n  </div>\n\n  <div style=\"margin-top:8px; border:1px solid #333; border-radius:6px; padding:10px;\">\n    <table style=\"width:100%; border-collapse:collapse; font-size:12px\">\n      <tbody><tr>\n        <td style=\"width:10%;\"><b>A:</b></td>\n        <td> </td>\n      </tr>\n      <tr>\n        <td></td>\n        <td></td>\n      </tr>\n      <tr><td colspan=\"2\" style=\"height:8px\"></td></tr>\n      <tr>\n        <td><b>DE:</b></td>\n        <td> LIC MILENKA PINAYA FLORES </td>\n      </tr>\n      <tr>\n        <td></td>\n        <td>PSICÓLOGA / PSICÓLOGO – DIRECCIÓN DE IGUALDAD DE OPORTUNIDADES (DIO) SLIM</td>\n      </tr>\n      <tr>\n        <td><b>Fecha:</b></td>\n        <td>2025-10-02</td>\n      </tr>\n    </tbody></table>\n  </div>\n\n  <h5 style=\"margin:12px 0 4px 0;\">1. DATOS PERSONALES DE LA DENUNCIANTE</h5>\n  <table style=\"width:100%; border-collapse:collapse; font-size:12px\">\n    <tbody><tr>\n      <td style=\"width:45%\"><b>Nombres y apellidos:</b> carla myerlin blancas flores —</td>\n      <td style=\"width:20%\"><b>Edad:</b> 34</td>\n      <td style=\"width:35%\"><b>Fecha de nacimiento:</b> —</td>\n    </tr>\n    <tr>\n      <td><b>Lugar de nacimiento:</b> oruro</td>\n      <td colspan=\"2\"><b>Grado de instrucción:</b> —</td>\n    </tr>\n    <tr>\n      <td><b>Ocupación:</b> —</td>\n      <td colspan=\"2\"><b>Dirección/Domicilio:</b> juan lechin oquendo</td>\n    </tr>\n    <tr>\n      <td><b>Documento:</b> Carnet de identidad</td>\n      <td><b>Nro.:</b> —</td>\n      <td><b>Teléfono:</b> —</td>\n    </tr>\n    <tr>\n      <td colspan=\"3\"><b>Estado civil:</b> —</td>\n    </tr>\n  </tbody></table>\n\n  <h5 style=\"margin:12px 0 4px 0;\">2. DATOS FAMILIARES</h5>\n  <table style=\"width:100%; border:1px solid #333; border-collapse:collapse; font-size:12px\">\n    <thead>\n      <tr style=\"background:#eee\">\n        <th style=\"border:1px solid #333; padding:4px; text-align:left;\">Nombres y Apellidos</th>\n        <th style=\"border:1px solid #333; padding:4px; width:60px;\">Edad</th>\n        <th style=\"border:1px solid #333; padding:4px; width:110px;\">Estado civil</th>\n        <th style=\"border:1px solid #333; padding:4px; width:110px;\">Parentesco</th>\n        <th style=\"border:1px solid #333; padding:4px; width:140px;\">Ocupación</th>\n      </tr>\n    </thead>\n    <tbody>\n      \n    <tr>\n      <td>—</td>\n      <td style=\"text-align:center\">—</td>\n      <td style=\"text-align:center\">—</td>\n      <td style=\"text-align:center\">—</td>\n      <td style=\"text-align:center\">—</td>\n    </tr>\n  \n    </tbody>\n  </table>\n\n  <h5 style=\"margin:12px 0 4px 0;\">3. MOTIVO DE CONSULTA</h5><div><p class=\"MsoNormal\" style=\"margin-bottom:0cm;text-align:justify;line-height:\n115%\"><span lang=\"ES\" style=\"font-size: 12pt; line-height: 115%; font-family: &quot;Calibri Light&quot;, sans-serif;\">Se\nemite el presente informe dando cumplimiento al acta de audiencia de fecha 20\nde agosto, emitida por su autoridad, en el que dispone la notificación al equipo\ninterdisciplinario de la D.I.O. a objeto que se elabore el informe psicológico\nde los niños Luka Thiago Villca Salazar y Aitana Nataniel Villca Salazar.<o:p></o:p></span></p></div>\n\n\n  <h5 style=\"margin:12px 0 4px 0;\">4. ANTECEDENTES</h5>\n\n\n  <h5 style=\"margin:12px 0 4px 0;\">5. TÉCNICAS E INSTRUMENTOS EMPLEADOS</h5>\n\n\n  <h5 style=\"margin:12px 0 4px 0;\">6. CONCLUSIONES</h5>\n\n\n  <h5 style=\"margin:12px 0 4px 0;\">7. RECOMENDACIONES</h5>\n\n  <p style=\"margin-top:12px\">Se emite el presente informe en honor a la verdad para fines consiguientes.</p>\n\n  <div style=\"margin-top:28px; display:flex; justify-content:space-between; font-size:12px;\">\n    <div style=\"text-align:center; width:45%;\">\n      __________________________<br>\n      LIC MILENKA PINAYA FLORES\n    </div>\n    <div style=\"text-align:center; width:45%;\">\n      __________________________<br>\n      Vo.Bo.\n    </div>\n  </div>\n</div>",-->
<!--  "created_at": "2025-10-02T20:20:14.000000Z",-->
<!--  "updated_at": "2025-10-02T20:20:14.000000Z",-->
<!--  "deleted_at": null,-->
<!--  "archivo": null,-->
<!--  "user": {-->
<!--  "id": 28,-->
<!--  "name": "LIC MILENKA PINAYA FLORES",-->
<!--  "role": "Psicologo"-->
<!--  }-->
<!--  }-->
<!--  ]-->
        <q-markup-table dense bordered flat>
          <thead class="bg-primary text-white">
            <tr>
              <th>Tipo</th>
              <th>Título</th>
              <th>Fecha</th>
              <th>Usuario</th>
              <th>Archivo</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="sesion in caso.psicologicas" :key="sesion.id">
              <td>
                Informe
              </td>
              <td>{{ sesion.tipo }} — {{ sesion.titulo || 'Sesión' }}</td>
              <td>{{ sesion.fecha || '—' }}</td>
              <td>{{ sesion.user?.name || '—' }}</td>
              <td>
                <a @click="clickSeguimiento($event,{ tipo: 'Sesión', modulo: 'Psicológico', uid: 'psi-' + sesion.id })">
                  Ver archivo
                </a>
              </td>
            </tr>
            <ArchivoFoto role="Psicologo" :caso="caso" />
          </tbody>
        </q-markup-table>
      </div>
      <div class="col-12 col-md-12">
        <span class="text-bold">Legal</span>
<!--        <pre>{{caso.informes_legales}}</pre>-->
<!--        [-->
<!--        {-->
<!--        "id": 5,-->
<!--        "caseable_type": "App\\Models\\Caso",-->
<!--        "caseable_id": 59,-->
<!--        "user_id": 26,-->
<!--        "fecha": "2025-11-01",-->
<!--        "titulo": "aaaa",-->
<!--        "numero": null,-->
<!--        "contenido_html": "<div style=\"border:1px solid #333;border-radius:6px;padding:14px;margin-bottom:12px;font-size:13px;line-height:1.35\">\n\n  <div style=\"margin-bottom:10px\">\n    <div><b>A :</b> SEÑOR REPRESENTANTE DEL JUZGADO PUBLICO DE FAMILIA LA CIUDAD DE ORURO</div>\n    <div><b>DE :</b> ABG. ABG. XIMENA MAMANI VALDEZ</div>\n  </div>\n\n  <div style=\"margin:14px 0; font-weight:700\">I. FORMULACION DE DENUNCIA</div>\n  <div style=\"margin:14px 0; font-weight:700\">II. FUNDAMENTO DEL HECHO</div>\n  <div style=\"margin:14px 0; font-weight:700\">III. FUNDAMENTO DE DERECHO</div>\n  <div style=\"margin:14px 0; font-weight:700\">IV. PETITORIO</div>\n  <div style=\"margin:14px 0; font-weight:700\">V. OTROSIS</div>\n\n  <div style=\"margin:18px 0; font-weight:700\">I. FORMULA DENUNCIA POR EL MOTIVO DE :</div>\n  <div style=\"margin:6px 0 14px 0\">—</div>\n\n  <div style=\"margin:10px 0; font-weight:700\">DATOS GENERALES DEL DENUNCIANTE :</div>\n  <div><b>Nombres y Apellidos:</b> giovana ramirez</div>\n  <div><b>Fecha de Nacimiento:</b> —</div>\n  <div><b>Lugar de Nacimiento:</b> —</div>\n  <div><b>Edad:</b> —</div>\n  <div><b>C.I.:</b> —</div>\n  <div><b>Estado Civil:</b> —</div>\n  <div><b>Grado de Instrucción:</b> —</div>\n  <div><b>Dirección:</b> —</div>\n  <div><b>Ocupación:</b> —</div>\n  <div><b>Teléfono Celular:</b> —</div>\n  <div><b>Correo electrónico:</b> —</div>\n  <div><b>Ciudadanía Digital:</b> No</div>\n\n  <div style=\"margin:16px 0; font-weight:700\">DATOS DEL DEMANDADO</div>\n  <div><b>Nombres y Apellidos:</b> adimer paul</div>\n  <div><b>Fecha de Nacimiento:</b> —</div>\n  <div><b>Lugar de Nacimiento:</b> —</div>\n  <div><b>Edad:</b> —</div>\n  <div><b>C.I.:</b> —</div>\n  <div><b>Estado Civil:</b> —</div>\n  <div><b>Grado de Instrucción:</b> —</div>\n  <div><b>Dirección:</b> —</div>\n  <div><b>Ocupación:</b> —</div>\n  <div><b>Teléfono Celular:</b> —</div>\n  <div><b>Correo electrónico:</b> —</div>\n\n  <div style=\"margin:18px 0; font-weight:700\">II. FUNDAMENTO DE HECHO</div>\n  <p style=\"margin:6px 0\">...</p>\n\n  <div style=\"margin:18px 0; font-weight:700\">III. FUNDAMENTO DE DERECHO</div>\n  <p style=\"margin:6px 0\">...</p>\n\n  <div style=\"margin:18px 0; font-weight:700\">IV. PETITORIO</div>\n  <p style=\"margin:6px 0\">...</p>\n\n  <div style=\"margin:18px 0; font-weight:700\">V. OTROSIS</div>\n  <p style=\"margin:6px 0\">Por un derecho y protección a la Mujer…</p>\n\n  <div style=\"margin-top:24px;\">Oruro, 01/11/2025</div>\n\n  <div style=\"margin-top:36px;text-align:center\">\n    __________________________<br>\n    NOMBRE Y FIRMA DEL ABOGADO\n  </div>\n</div>",-->
<!--  "created_at": "2025-11-01T09:32:14.000000Z",-->
<!--  "updated_at": "2025-11-01T09:32:14.000000Z",-->
<!--  "deleted_at": null,-->
<!--  "archivo": null,-->
<!--  "user": {-->
<!--  "id": 26,-->
<!--  "name": "ABG. XIMENA MAMANI VALDEZ",-->
<!--  "role": "Abogado"-->
<!--  }-->
<!--  },-->
<!--  {-->
<!--  "id": 6,-->
<!--  "caseable_type": "App\\Models\\Caso",-->
<!--  "caseable_id": 59,-->
<!--  "user_id": 26,-->
<!--  "fecha": null,-->
<!--  "titulo": "aaaa",-->
<!--  "numero": null,-->
<!--  "contenido_html": "<p>Archivo adjunto: <a href=\"/storage/caso/59/legal/1761989540_logoColMed3.png\" target=\"_blank\" rel=\"noopener\">logoColMed3.png</a></p>",-->
<!--  "created_at": "2025-11-01T09:32:20.000000Z",-->
<!--  "updated_at": "2025-11-01T09:32:20.000000Z",-->
<!--  "deleted_at": null,-->
<!--  "archivo": "/storage/caso/59/legal/1761989540_logoColMed3.png",-->
<!--  "user": {-->
<!--  "id": 26,-->
<!--  "name": "ABG. XIMENA MAMANI VALDEZ",-->
<!--  "role": "Abogado"-->
<!--  }-->
<!--  }-->
<!--  ]-->
        <q-markup-table dense bordered flat>
          <thead class="bg-primary text-white">
            <tr>
              <th>Tipo</th>
              <th>Título</th>
              <th>Fecha</th>
              <th>Usuario</th>
              <th>Archivo</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="informe in caso.informes_legales" :key="informe.id">
              <td>
                Informe
              </td>
              <td>{{ informe.titulo }}</td>
              <td>{{ informe.fecha || '—' }}</td>
              <td>{{ informe.user?.name || '—' }}</td>
              <td>
                <a @click="clickSeguimiento($event,{ tipo: 'Informe', modulo: 'Legal', uid: 'leg-' + informe.id })">
                  Ver archivo
                </a>
              </td>
            </tr>
            <ArchivoFoto role="Abogado" :caso="caso" />
          </tbody>
        </q-markup-table>
      </div>
    </div>
  </div>
</template>

<script>
import ArchivoFoto from "components/ArchivoFoto.vue";

export default {
  name: 'SeguimientoCaso',
  components: {ArchivoFoto},
  props: {
    caso: { type: Object, required: true }
  },
  data () {
    return {
      loading: false,
      filters: {
        q: '',
        tipo: 'Todos',
        modulo: 'Todos',
        desde: '',
        hasta: ''
      },
      tipos: ['Todos', 'Informe', 'Sesión', 'Documento', 'Fotografía', 'Hito'],
      modulos: ['Todos', 'General', 'Psicológico', 'Legal', 'Social', 'Documentos', 'Multimedia'],
      columns: [
        { name: 'actividad', label: 'Actividad', field: 'actividad', align: 'left', sortable: false },
        { name: 'fecha',     label: 'Fecha / Usuario', field: 'fecha', align: 'left', sortable: true },
        { name: 'modulo',    label: 'Módulo', field: 'modulo', align: 'left', sortable: true },
        { name: 'acciones',  label: '', field: 'acciones', align: 'right', sortable: false, style: 'width: 150px' }
      ]
    }
  },
  computed: {
    header () {
      const c = this.caso || {}
      const den = Array.isArray(c.denunciantes) && c.denunciantes.length ? c.denunciantes[0] : null
      const denu = Array.isArray(c.denunciados) && c.denunciados.length ? c.denunciados[0] : null
      return {
        caso_id: c.id || '—',
        caso_numero: c.caso_numero || '—',
        tipologia: c.caso_tipologia || c.caso_tipologia === '' ? c.caso_tipologia : c.caso_tipologia || c.caso_tipologia,
        modalidad: c.caso_modalidad || c.caso_modalidad === '' ? c.caso_modalidad : c.caso_modalidad,
        zona: c.caso_zona || c.zona || '—',
        direccion: c.caso_direccion || '—',
        fecha_hecho: c.caso_fecha_hecho || '—',
        fecha_registro: c.fecha_apertura_caso || '—',
        registrado_por: c.user?.name || '—',
        denunciante: den ? this.fullName(den, 'denunciante') : '—',
        denunciado: denu ? this.fullName(denu, 'denunciado') : '—'
      }
    },
    violenciaChips () {
      const c = this.caso || {}
      const map = [
        { key: 'violencia_fisica',         label: 'Física',         color: 'red-7' },
        { key: 'violencia_psicologica',    label: 'Psicológica',    color: 'deep-purple-7' },
        { key: 'violencia_sexual',         label: 'Sexual',         color: 'purple-7' },
        { key: 'violencia_economica',      label: 'Económica',      color: 'orange-7' },
        { key: 'violencia_patrimonial',    label: 'Patrimonial',    color: 'brown-7' },
        { key: 'violencia_simbolica',      label: 'Simbólica',      color: 'indigo-7' },
        { key: 'violencia_institucional',  label: 'Institucional',  color: 'teal-7' }
      ]
      return map.map(m => ({ ...m, on: Boolean(c[m.key]) }))
    },
    // Unificamos todas las actividades en un solo arreglo
    rows () {
      const items = []
      const c = this.caso || {}

      // === Hitos generales desde la cabecera ===
      const hitos = [
        { fecha: c.fecha_apertura_caso, titulo: 'Apertura del caso' },
        { fecha: c.fecha_derivacion_psicologica, titulo: 'Derivación a Psicología' },
        { fecha: c.fecha_derivacion_area_legal,  titulo: 'Derivación a Área Legal' },
        { fecha: c.fecha_informe_area_social,    titulo: 'Informe Área Social' },
        { fecha: c.fecha_informe_area_psicologica, titulo: 'Informe Área Psicológica' },
        { fecha: c.fecha_informe_trabajo_social, titulo: 'Informe Trabajo Social' }
      ]
      hitos.forEach((h, idx) => {
        if (h.fecha) {
          // items.push({
          //   uid: `hito-${idx}-${h.fecha}`,
          //   tipo: 'Hito',
          //   modulo: 'General',
          //   titulo: h.titulo,
          //   descripcion: `Fecha: ${h.fecha}`,
          //   usuario: c.user?.name || '',
          //   fecha: h.fecha,
          //   icon: 'flag',
          //   links: {}
          // })
        }
      })

      // === Informes sociales ===
      if (Array.isArray(c.informes_sociales)) {
        c.informes_sociales.forEach((it) => {
          items.push({
            uid: `soc-${it.id}`,
            tipo: 'Informe',
            modulo: 'Social',
            titulo: it.titulo || 'Informe Social',
            descripcion: this.stripHtml(it.contenido_html || ''),
            usuario: it.user?.name || '',
            fecha: it.fecha || '',
            icon: 'description',
            links: {} // podrías mapear a PDF si tienes endpoint
          })
        })
      }

      // === Sesiones psicológicas ===
      if (Array.isArray(c.psicologicas)) {
        c.psicologicas.forEach((it) => {
          items.push({
            uid: `psi-${it.id}`,
            tipo: 'Sesión',
            modulo: 'Psicológico',
            titulo: `${it.tipo || 'Sesión'}${it.titulo ? ` — ${it.titulo}` : ''}`,
            descripcion: this.stripHtml(it.contenido_html || ''),
            usuario: it.user?.name || '',
            fecha: it.fecha || '',
            icon: 'psychology',
            links: {}
          })
        })
      }

      // === Informes legales ===
      if (Array.isArray(c.informes_legales)) {
        c.informes_legales.forEach((it) => {
          items.push({
            uid: `leg-${it.id}`,
            tipo: 'Informe',
            modulo: 'Legal',
            titulo: it.titulo || 'Informe Legal',
            descripcion: this.stripHtml(it.contenido_html || ''),
            usuario: it.user?.name || '',
            fecha: it.fecha || '',
            icon: 'gavel',
            links: {}
          })
        })
      }

      // === Documentos ===
      if (Array.isArray(c.documentos)) {
        c.documentos.forEach((d) => {
          items.push({
            uid: `doc-${d.id}`,
            tipo: 'Documento',
            modulo: 'Documentos',
            titulo: d.titulo || d.original_name || 'Documento',
            descripcion: [d.categoria, d.descripcion, d.size_human].filter(Boolean).join(' · '),
            usuario: d.user?.name || '',
            fecha: (d.created_at || '').slice(0, 10),
            icon: d.extension === 'pdf' ? 'picture_as_pdf' : 'attach_file',
            links: {
              view: d.url,
              download: d.url,
              open: d.url
            }
          })
        })
      }

      // === Fotografías (si las tuvieras en el array) ===
      if (Array.isArray(c.fotografias)) {
        c.fotografias.forEach((f) => {
          items.push({
            uid: `foto-${f.id}`,
            tipo: 'Fotografía',
            modulo: 'Multimedia',
            titulo: f.titulo || 'Fotografía',
            descripcion: f.descripcion || '',
            usuario: f.user?.name || '',
            fecha: (f.created_at || '').slice(0, 10),
            icon: 'image',
            links: { view: f.url, open: f.url, download: f.url }
          })
        })
      }

      // Ordenar por fecha descendente (cuando existe)
      items.sort((a, b) => (b.fecha || '').localeCompare(a.fecha || ''))
      return items
    },
    rowsFiltered () {
      let r = this.rows.slice()

      if (this.filters.q) {
        const q = this.filters.q.toLowerCase()
        r = r.filter(x =>
          (x.titulo || '').toLowerCase().includes(q) ||
          (x.descripcion || '').toLowerCase().includes(q) ||
          (x.usuario || '').toLowerCase().includes(q)
        )
      }
      if (this.filters.tipo && this.filters.tipo !== 'Todos') {
        r = r.filter(x => x.tipo === this.filters.tipo)
      }
      if (this.filters.modulo && this.filters.modulo !== 'Todos') {
        r = r.filter(x => (x.modulo || '').toLowerCase() === this.filters.modulo.toLowerCase())
      }
      if (this.filters.desde) {
        r = r.filter(x => (x.fecha || '0000-00-00') >= this.filters.desde)
      }
      if (this.filters.hasta) {
        r = r.filter(x => (x.fecha || '9999-12-31') <= this.filters.hasta)
      }
      return r
    }
  },
  methods: {
    openSocialInforme(informe) {
      if (informe.archivo) {
        const base = this.$axios?.defaults?.baseURL || ''
        const url = `${base}/../storage/${informe.archivo.replace('/storage/','')}`
        this.open(url)
        return
      }else{
        const id = informe.id
        const url = this.$axios.defaults.baseURL + `/informesSocial/${id}/pdf`
        console.log('url', url)
        this.open(url)
      }
    },
    clickSeguimiento(event,row) {
      // console.log('Fila clicada:', row)
      if (row.tipo === 'Informe' && row.modulo === 'Social' && row.uid.startsWith('soc-')) {
        const id = row.uid.split('-')[1]
        const url = this.$axios.defaults.baseURL + `/informesSocial/${id}/pdf`
        this.open(url)
      }

      if (row.tipo === 'Documento' && row.uid.startsWith('doc-')) {
        const id = row.uid.split('-')[1]
        const url = this.$axios.defaults.baseURL + `/documentos/${id}/view`
        this.open(url)
      }

      if (row.tipo === 'Sesión' && row.modulo === 'Psicológico' && row.uid.startsWith('psi-')) {
        const id = row.uid.split('-')[1]
        const url = this.$axios.defaults.baseURL + `/sesiones-psicologicas/${id}/pdf`
        this.open(url)
      }
      // si es legal
      // http://localhost:8000/api/informes/2/pdf
      if (row.tipo === 'Informe' && row.modulo === 'Legal' && row.uid.startsWith('leg-')) {
        const id = row.uid.split('-')[1]
        const url = this.$axios.defaults.baseURL + `/informes/${id}/pdf`
        this.open(url)
      }
    },
    stripHtml (html) {
      const div = document.createElement('div')
      div.innerHTML = html || ''
      // DomPDF suele meter entidades; aseguramos texto plano corto:
      const text = (div.textContent || div.innerText || '').trim().replace(/\s+/g, ' ')
      return text.length > 280 ? text.slice(0, 277) + '…' : text
    },
    fullName (obj, prefix) {
      const n = [
        obj[`${prefix}_nombres`],
        obj[`${prefix}_paterno`],
        obj[`${prefix}_materno`]
      ].filter(Boolean).join(' ').trim()
      return n || obj[`${prefix}_nombre_completo`] || obj[`${prefix}_nombres`] || '—'
    },
    open (url) {
      if (url) window.open(url, '_blank')
    },
    // placeholder si quieres recargar desde servidor en el futuro
    async forceRefresh () {
      this.loading = true
      try {
        // aquí podrías emitir un evento al padre para que re-obtenga el caso
        // this.$emit('refresh') // opcional
        // De momento, como todo viene por props, solo simulamos:
        await new Promise(r => setTimeout(r, 500))
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.lbl { font-size: 11px; color: #6b7280; }
.val { font-size: 13px; font-weight: 600; color: #111; }
.rounded-borders { border-radius: 12px; }
.ellipsis-2-lines {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
