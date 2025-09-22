<template>
  <div>
    <!-- CABECERA -->
    <q-card flat bordered class="q-pa-sm q-mb-md">
      <div class="row items-start q-col-gutter-sm">
        <div class="col-12 col-md-8">
          <div class="text-subtitle1 text-weight-bold">Seguimiento del Caso — SLAM</div>
          <div class="text-caption text-grey-7">
            Área <b>{{ header.area || '—' }}</b> · Zona <b>{{ header.zona || '—' }}</b>
          </div>
          <div class="text-caption">
            N° Caso: <b>{{ header.numero_caso || '—' }}</b>
            <span class="text-grey-7"> · Registrado:</span> <b>{{ header.fecha_registro || '—' }}</b>
          </div>
        </div>
        <div class="col-12 col-md-4 flex items-center justify-end">
          <q-chip dense color="indigo-1" text-color="indigo-9" class="q-mr-xs">
            {{ header.registrado_por || '—' }}
          </q-chip>
        </div>
      </div>

      <q-separator class="q-my-sm"/>

      <div class="row q-col-gutter-sm">
        <div class="col-12 col-md-4">
          <div class="lbl">Adulto(s) mayor(es)</div>
          <div class="val">{{ header.adultos || '—' }}</div>
        </div>
        <div class="col-12 col-md-4">
          <div class="lbl">Denunciado</div>
          <div class="val">{{ header.denunciado || '—' }}</div>
        </div>
        <div class="col-12 col-md-4">
          <div class="lbl">Contacto / Ubicación</div>
          <div class="val ellipsis">
            {{ header.contacto || '—' }}
            <span v-if="header.latlng" class="text-grey-7"> · {{ header.latlng }}</span>
          </div>
        </div>
      </div>
    </q-card>

    <!-- LÍNEA DE TIEMPO -->
    <q-card flat bordered class="q-pa-md">
      <div class="text-subtitle1 text-weight-medium q-mb-md">Historial</div>

<!--      <q-timeline color="primary" layout="comfortable">-->
<!--        <q-timeline-entry-->
<!--          v-for="item in history"-->
<!--          :key="item.uid"-->
<!--          :title="item.title"-->
<!--          :subtitle="item.subtitle"-->
<!--          :body="item.brief"-->
<!--          :icon="item.icon"-->
<!--          :side="item.side"-->
<!--        >-->
<!--          <div class="q-mt-sm">-->
<!--            <div class="row q-gutter-xs">-->
<!--              <q-btn-->
<!--                v-if="item.kind==='doc' && item.url"-->
<!--                dense flat icon="download" label="Descargar / Abrir"-->
<!--                @click="open(item.url)"-->
<!--              />-->
<!--              <q-btn-->
<!--                v-if="item.kind==='photo' && item.url"-->
<!--                dense flat icon="image" label="Ver imagen"-->
<!--                @click="open(item.url)"-->
<!--              />-->
<!--              <q-btn-->
<!--                v-if="(item.kind==='psy' || item.kind==='legal') && item.html"-->
<!--                dense flat icon="visibility" label="Ver contenido"-->
<!--                @click="openHtml(item.title, item.html)"-->
<!--              />-->
<!--            </div>-->

<!--            <q-img-->
<!--              v-if="item.kind==='photo' && item.thumbUrl"-->
<!--              :src="toPublicUrl(item.thumbUrl)"-->
<!--              :ratio="16/9"-->
<!--              class="q-mt-sm rounded-borders"-->
<!--              style="max-width:380px"-->
<!--              :alt="item.title"-->
<!--              spinner-color="primary"-->
<!--            />-->
<!--          </div>-->
<!--        </q-timeline-entry>-->
<!--      </q-timeline>-->
      <q-markup-table>
        <thead>
          <tr>
            <th style="width:60%;">Actividad</th>
            <th style="width:25%;">Fecha</th>
            <th style="width:15%;">Módulo</th>
            <th style="width:10%;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in history" :key="item.uid">
            <td>
              <div class="row no-wrap items-center q-gutter-xs">
                <q-icon :name="item.icon || 'feed'" size="18px" class="text-primary"/>
                <div>
                  <div class="text-weight-medium">
                    {{ item.tipo }} <span class="text-grey-7">·</span> {{ item.title || '—' }}
                  </div>
                  <div class="text-caption text-grey-7 ellipsis-2-lines" style="max-width:520px">
                    {{ item.brief || '—' }}
                  </div>
                </div>
              </div>
            </td>
            <td>
              <div class="text-weight-medium">{{ item.date || '—' }}</div>
              <div class="text-caption text-grey-7">{{ item.subtitle || '—' }}</div>
            </td>
            <td>
              <q-chip dense square>{{ item.kind === 'psy' ? 'Psicología' : (item.kind === 'legal' ? 'Legal' : (item.kind === 'doc' ? 'Documento' : (item.kind === 'photo' ? 'Fotografía' : '—'))) }}</q-chip>
            </td>
            <td class="q-gutter-xs">
              <q-btn v-if="item.kind==='doc' && item.url"      dense flat icon="download"  @click="open(item.url)"      title="Descargar / Abrir"/>
              <q-btn v-if="item.kind==='photo' && item.url"    dense flat icon="image"     @click="open(item.url)"      title="Ver imagen"/>
              <q-btn v-if="(item.kind==='psy' || item.kind==='legal') && item.html" dense flat icon="visibility" @click="openHtml(item.title, item.html)" title="Ver contenido"/>
            </td>
          </tr>
        </tbody>
      </q-markup-table>

      <div v-if="!history.length" class="text-grey-7 text-center q-mt-lg">
        Sin actividades registradas.
      </div>
    </q-card>

    <!-- DIALOGO PARA HTML -->
    <q-dialog v-model="dialog.show" maximized>
      <q-card style="max-width:1000px; width:100%;">
        <q-card-section class="row items-center">
          <div class="text-subtitle1 text-weight-medium">{{ dialog.title }}</div>
          <q-space/>
          <q-btn dense flat icon="close" v-close-popup/>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="html-content" v-html="dialog.html"></div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
<!--  <pre>{{caso}}</pre>-->
<!--  {-->
<!--  "id": 1,-->
<!--  "area": "DNA",-->
<!--  "zona": "CENTRAL",-->
<!--  "fecha_registro": "2025-09-22",-->
<!--  "numero_apoyo_integral": null,-->
<!--  "numero_caso": null,-->
<!--  "am_latitud": "-17.9631568",-->
<!--  "am_longitud": "-67.1007209",-->
<!--  "am_extravio": null,-->
<!--  "am_medicina": null,-->
<!--  "am_fisioterapia": null,-->
<!--  "am_idioma_castellano": 1,-->
<!--  "am_idioma_quechua": 1,-->
<!--  "am_idioma_aymara": 1,-->
<!--  "am_idioma_otros": "asda",-->
<!--  "ref_tel_fijo": "23432",-->
<!--  "ref_tel_movil": "23423",-->
<!--  "ref_tel_movil_alt": "23432",-->
<!--  "den_nombres": "asda",-->
<!--  "den_paterno": "asda",-->
<!--  "den_materno": "asda",-->
<!--  "den_edad": "232",-->
<!--  "den_domicilio": "asda",-->
<!--  "den_estado_civil": "Caso",-->
<!--  "den_idioma": "232",-->
<!--  "den_grado_instruccion": "asda",-->
<!--  "den_ocupacion": "asda",-->
<!--  "hecho_descripcion": "choi topología",-->
<!--  "tip_violencia_fisica": 0,-->
<!--  "tip_violencia_psicologica": 0,-->
<!--  "tip_abandono": 0,-->
<!--  "tip_apoyo_integral": 0,-->
<!--  "doc_ci": 0,-->
<!--  "doc_frontal_denunciado": 1,-->
<!--  "doc_frontal_denunciante": 1,-->
<!--  "doc_croquis": 0,-->
<!--  "user_id": 3,-->
<!--  "psicologica_user_id": 3,-->
<!--  "trabajo_social_user_id": 5,-->
<!--  "legal_user_id": 4,-->
<!--  "am_domicilio": "tacna, oruro",-->
<!--  "adultos": [-->
<!--  {-->
<!--  "id": 1,-->
<!--  "slam_id": 1,-->
<!--  "nombre": "asfsadsa",-->
<!--  "paterno": "232",-->
<!--  "materno": "asda",-->
<!--  "documento_tipo": null,-->
<!--  "documento_num": "2412",-->
<!--  "fecha_nacimiento": "2000-09-11T04:00:00.000000Z",-->
<!--  "lugar_nacimiento": "oruro",-->
<!--  "edad": "25",-->
<!--  "domicilio": "calle bolivat",-->
<!--  "estado_civil": "asda",-->
<!--  "ocupacion_1": "asda",-->
<!--  "ocupacion_2": null,-->
<!--  "ingresos": "asdsa"-->
<!--  }-->
<!--  ],-->
<!--  "familiares": [-->
<!--  {-->
<!--  "id": 1,-->
<!--  "slam_id": 1,-->
<!--  "nombre": "asdsa",-->
<!--  "paterno": "asda",-->
<!--  "materno": "asda",-->
<!--  "parentesco": "asd",-->
<!--  "edad": 232,-->
<!--  "sexo": null,-->
<!--  "telefono": "12421",-->
<!--  "estado_civil": "Viudo/a",-->
<!--  "ocupacion": "Estudiante"-->
<!--  }-->
<!--  ],-->
<!--  "psicologica_user": {-->
<!--  "id": 3,-->
<!--  "name": "Lic. Ana  Calle",-->
<!--  "celular": "72461667"-->
<!--  },-->
<!--  "trabajo_social_user": {-->
<!--  "id": 5,-->
<!--  "name": "Lic. Tania Calizaya",-->
<!--  "celular": "72461667"-->
<!--  },-->
<!--  "legal_user": {-->
<!--  "id": 4,-->
<!--  "name": "Abo. Anita Calle",-->
<!--  "celular": "72461667"-->
<!--  },-->
<!--  "user": {-->
<!--  "id": 3,-->
<!--  "name": "Lic. Ana  Calle",-->
<!--  "celular": "72461667"-->
<!--  },-->
<!--  "psicologicas": [-->
<!--  {-->
<!--  "id": 1,-->
<!--  "caseable_type": "App\\Models\\Slam",-->
<!--  "caseable_id": 1,-->
<!--  "user_id": 3,-->
<!--  "fecha": "2025-09-22",-->
<!--  "titulo": "asda",-->
<!--  "duracion_min": null,-->
<!--  "lugar": null,-->
<!--  "tipo": "Individual",-->
<!--  "contenido_html": "<div style=\"text-align:center; font-weight:700; margin-bottom:12px;\">\n  CONSENTIMIENTO INFORMADO PSICOLOGÍA\n</div>\n\n<p style=\"font-size:12px; margin-bottom:8px;\">\n  <b>Fecha:</b> 2025-09-22\n</p>\n\n<p style=\"font-size:12px; text-align:justify;\">\n  Sr.(a) Usuario, por favor lea atentamente el siguiente documento que tiene como objetivo explicarle el uso y la confidencialidad de sus datos, así como sus derechos, respecto al proceso de atención psicológica.\n  Si tiene alguna duda y/o consulta lo puede realizar con el/la Psicólogo/a.\n</p>\n\n<p style=\"font-size:12px; margin-top:12px;\">\n  <b>Yo:</b> _____________________________________________\n</p>\n\n<p style=\"font-size:12px; margin-top:8px;\">\n  <b>Con Documento Nro.:</b> _____________________________\n</p>\n\n<p style=\"font-size:12px; text-align:justify; margin-top:12px;\">\n  Confirmo que he sido informado con claridad y veracidad, respecto al proceso de evaluaciones, sesiones, tiempos, las pruebas psicológicas que se me van a realizar, y también en cuanto a los resultados.\n  De esta evaluación acepto, según los resultados de las pruebas psicológicas que se me realice, libre y voluntariamente, doy mi consentimiento para realizar las evaluaciones psicológicas.\n</p>\n\n&lt;!&ndash;<div style=\"margin-top:48px; display:flex; justify-content:space-between; font-size:12px;\">&ndash;&gt;\n&lt;!&ndash;  <div style=\"text-align:center; width:30%;\">&ndash;&gt;\n&lt;!&ndash;    __________________________<br/>&ndash;&gt;\n&lt;!&ndash;    FIRMA&ndash;&gt;\n&lt;!&ndash;  </div>&ndash;&gt;\n&lt;!&ndash;  <div style=\"text-align:center; width:30%;\">&ndash;&gt;\n&lt;!&ndash;    __________________________<br/>&ndash;&gt;\n&lt;!&ndash;    NOMBRE&ndash;&gt;\n&lt;!&ndash;  </div>&ndash;&gt;\n&lt;!&ndash;  <div style=\"text-align:center; width:30%;\">&ndash;&gt;\n&lt;!&ndash;    __________________________<br/>&ndash;&gt;\n&lt;!&ndash;    HUELLA DIGITAL&ndash;&gt;\n&lt;!&ndash;  </div>&ndash;&gt;\n&lt;!&ndash;</div>&ndash;&gt;\n<table style=\"width:100%; border-collapse:collapse; font-size:12px; margin-top:48px;\">\n  <tr>\n    <td style=\"width:33%; text-align:center;\">\n      __________________________<br/>\n      FIRMA\n    </td>\n    <td style=\"width:33%; text-align:center;\">\n      __________________________<br/>\n      NOMBRE\n    </td>\n    <td style=\"width:33%; text-align:center;\">\n    __________________________<br/>\n      HUELLA DIGITAL\n    </td>\n  </tr>\n</table>",-->
<!--  "created_at": "2025-09-22T08:27:59.000000Z",-->
<!--  "updated_at": "2025-09-22T08:27:59.000000Z",-->
<!--  "deleted_at": null,-->
<!--  "user": {-->
<!--  "id": 3,-->
<!--  "name": "Lic. Ana  Calle",-->
<!--  "celular": "72461667"-->
<!--  }-->
<!--  }-->
<!--  ],-->
<!--  "informes_legales": [-->
<!--  {-->
<!--  "id": 1,-->
<!--  "caseable_type": "App\\Models\\Slam",-->
<!--  "caseable_id": 1,-->
<!--  "user_id": 1,-->
<!--  "fecha": "2025-09-22",-->
<!--  "titulo": "area legal",-->
<!--  "numero": null,-->
<!--  "contenido_html": "<div style=\"text-align:center; margin-bottom:8px;\">\n          <div style=\"font-size:16px; font-weight:bold;\">DIRECCIÓN DE IGUALDAD DE OPORTUNIDADES</div>\n          <div style=\"font-size:12px;\">Gobierno Autónomo Municipal de Oruro</div>\n          <div style=\"font-size:12px; font-weight:bold;\"></div>\n          <hr/>\n        </div>\n      \n        <p><b>Informe Legal 2025</b></p>\n        <p style=\"text-align:justify\">En atención a los antecedentes, se emite el presente informe con el siguiente análisis jurídico:</p>\n        <p><b>Fundamentos de derecho:</b></p>\n        <ul>\n          <li>Ley N° 348 y reglamentación.</li>\n          <li>Constitución Política del Estado.</li>\n          <li>Normativa municipal vigente.</li>\n        </ul>\n        <p><b>Recomendaciones:</b> …</p>\n        <p>Oruro, 2025-09-22</p>",-->
<!--  "created_at": "2025-09-22T09:20:48.000000Z",-->
<!--  "updated_at": "2025-09-22T09:20:48.000000Z",-->
<!--  "deleted_at": null,-->
<!--  "user": {-->
<!--  "id": 1,-->
<!--  "name": "Ing Evelin Ramirez Cube",-->
<!--  "celular": "72461667"-->
<!--  }-->
<!--  }-->
<!--  ],-->
<!--  "documentos": [-->
<!--  {-->
<!--  "id": 1,-->
<!--  "caseable_type": "App\\Models\\Slam",-->
<!--  "caseable_id": 1,-->
<!--  "user_id": 3,-->
<!--  "titulo": "Documento general",-->
<!--  "categoria": null,-->
<!--  "descripcion": null,-->
<!--  "original_name": "work_order (80).pdf",-->
<!--  "stored_name": "c4edf6ff-857f-45d2-93ab-489f3a419b76.pdf",-->
<!--  "extension": "pdf",-->
<!--  "mime": "application/pdf",-->
<!--  "size_bytes": 564137,-->
<!--  "disk": "public",-->
<!--  "path": "slam/1/documentos/c4edf6ff-857f-45d2-93ab-489f3a419b76.pdf",-->
<!--  "url": "http://localhost/storage/slam/1/documentos/c4edf6ff-857f-45d2-93ab-489f3a419b76.pdf",-->
<!--  "created_at": "2025-09-22T08:28:18.000000Z",-->
<!--  "updated_at": "2025-09-22T08:28:18.000000Z",-->
<!--  "deleted_at": null,-->
<!--  "size_human": "550.92 KB",-->
<!--  "user": {-->
<!--  "id": 3,-->
<!--  "name": "Lic. Ana  Calle",-->
<!--  "celular": "72461667"-->
<!--  }-->
<!--  }-->
<!--  ],-->
<!--  "fotografias": [-->
<!--  {-->
<!--  "id": 1,-->
<!--  "caseable_type": "App\\Models\\Slam",-->
<!--  "caseable_id": 1,-->
<!--  "user_id": 3,-->
<!--  "titulo": "rings",-->
<!--  "descripcion": null,-->
<!--  "original_name": "rings.png",-->
<!--  "stored_name": "rings-WS0jWT.png",-->
<!--  "extension": "png",-->
<!--  "mime": "image/png",-->
<!--  "size_bytes": 45171,-->
<!--  "disk": "public",-->
<!--  "path": "slam/1/fotografias/rings-WS0jWT.png",-->
<!--  "url": "/storage/slam/1/fotografias/rings-WS0jWT.png",-->
<!--  "thumb_path": "slam/1/fotografias/rings-WS0jWT-thumb.png",-->
<!--  "thumb_url": "/storage/slam/1/fotografias/rings-WS0jWT-thumb.png",-->
<!--  "width": 242,-->
<!--  "height": 209,-->
<!--  "created_at": "2025-09-22T09:20:22.000000Z",-->
<!--  "updated_at": "2025-09-22T09:20:22.000000Z",-->
<!--  "deleted_at": null,-->
<!--  "user": {-->
<!--  "id": 3,-->
<!--  "name": "Lic. Ana  Calle",-->
<!--  "celular": "72461667"-->
<!--  }-->
<!--  }-->
<!--  ]-->
<!--  }-->
</template>

<script>
export default {
  name: 'SeguimientoSlam',
  props: {
    // Puede venir plano { ... } o anidado { slam: {...} }
    caso: { type: Object, required: true }
  },
  data () {
    return {
      header: {},
      history: [],
      dialog: { show: false, title: '', html: '' }
    }
  },
  mounted () { this.hydrate() },
  watch: { caso: { handler () { this.hydrate() }, deep: true } },
  methods: {
    hydrate () {
      // 1) Normaliza forma del objeto
      const s = this.caso?.slam ? this.caso.slam : (this.caso || {})

      // 2) Header
      const adultos = (s.adultos || [])
        .map(a => [a.nombre, a.paterno, a.materno].filter(Boolean).join(' '))
        .filter(Boolean)
        .join(' · ')
      const denunciado = [s.den_nombres, s.den_paterno, s.den_materno].filter(Boolean).join(' ')
      const contacto = [s.ref_tel_movil, s.ref_tel_fijo, s.ref_tel_movil_alt].filter(Boolean).join(' / ')
      const latlng = (s.am_latitud && s.am_longitud) ? `${s.am_latitud}, ${s.am_longitud}` : ''

      this.header = {
        area: s.area, zona: s.zona,
        fecha_registro: s.fecha_registro,
        numero_caso: s.numero_caso || s.numero_apoyo_integral,
        registrado_por: s.user?.name || '—',
        adultos: adultos || '—',
        denunciado: denunciado || '—',
        contacto: contacto || '',
        latlng
      }

      // 3) Línea de tiempo (psicologicas, informes_legales, documentos, fotografias)
      const items = []

        // Psicología
      ;(s.psicologicas || []).forEach(p => {
        items.push({
          uid: `psy-${p.id}`,
          kind: 'psy',
          icon: 'psychology',
          side: 'left',
          date: (p.fecha || p.created_at || '').slice(0,10) || '0000-00-00',
          title: `Psicológico · ${p.titulo || 'Sin título'}`,
          subtitle: `${(p.fecha || '').slice(0,10)} · ${p.tipo || '—'} · ${p.user?.name || '—'}`,
          brief: p.lugar ? `Lugar: ${p.lugar}` : '',
          html: p.contenido_html || p.contenido || null
        })
      })

      // Legal (acepta informes_legales o informesLegales)
      const legales = s.informes_legales || s.informesLegales || []
      legales.forEach(l => {
        items.push({
          uid: `legal-${l.id}`,
          kind: 'legal',
          icon: 'gavel',
          side: 'right',
          date: (l.fecha || l.created_at || '').slice(0,10) || '0000-00-00',
          title: `Legal · ${l.titulo || 'Informe'}`,
          subtitle: `${(l.fecha || '').slice(0,10)} · ${l.user?.name || '—'}`,
          brief: l.numero ? `Informe N° ${l.numero}` : (l.descripcion || ''),
          html: l.contenido_html || l.contenido || null
        })
      })

      // Documentos
      ;(s.documentos || []).forEach(d => {
        items.push({
          uid: `doc-${d.id}`,
          kind: 'doc',
          icon: 'description',
          side: 'left',
          date: (d.created_at || d.fecha || '').slice(0,10) || '0000-00-00',
          title: `Documento · ${d.titulo || d.original_name || d.filename || 'Archivo'}`,
          subtitle: `${d.size_human || ''} ${d.extension ? '· '+String(d.extension).toUpperCase() : ''} · ${d.user?.name || '—'}`,
          brief: d.descripcion || '',
          url: this.firstUrl(d)
        })
      })

      // Fotografías
      const fotos = s.fotografias || s.fotos || []
      fotos.forEach(f => {
        items.push({
          uid: `photo-${f.id}`,
          kind: 'photo',
          icon: 'photo',
          side: 'right',
          date: (f.created_at || f.fecha || '').slice(0,10) || '0000-00-00',
          title: `Fotografía · ${f.titulo || f.original_name || f.filename || 'Imagen'}`,
          subtitle: `${(f.width && f.height) ? `${f.width}×${f.height}` : ''} ${f.user?.name ? '· '+f.user.name : ''}`,
          brief: f.descripcion || '',
          url: this.firstUrl(f),
          thumbUrl: f.thumb_url || f.thumbPath || f.thumb || null
        })
      })

      // Orden: fecha DESC (y uid de tiebreaker)
      items.sort((a, b) => {
        const fa = a.date || '0000-00-00'
        const fb = b.date || '0000-00-00'
        if (fa === fb) return a.uid > b.uid ? 1 : -1
        return fb.localeCompare(fa)
      })

      this.history = items
    },

    // Intenta descubrir el mejor campo de URL
    firstUrl (o = {}) {
      const url = o.url || o.public_url || o.path || o.file_url || ''
      return this.toPublicUrl(url)
    },

    toPublicUrl (url) {
      if (!url) return ''
      if (/^https?:\/\//i.test(url)) return url
      const baseApi = this.$axios?.defaults?.baseURL || ''           // ej: http://host/api
      const basePublic = baseApi.replace(/\/api\/?$/, '')            // -> http://host
      return `${basePublic}${url.startsWith('/') ? url : `/${url}`}` // concat seguro
    },

    open (url) {
      const u = this.toPublicUrl(url)
      if (u) window.open(u, '_blank')
    },

    openHtml (title, html) {
      this.dialog.title = title || 'Contenido'
      this.dialog.html = html || '<em>Sin contenido</em>'
      this.dialog.show = true
    }
  }
}
</script>

<style scoped>
.lbl { font-size: 11px; color: #6b7280; }
.val { font-size: 13px; font-weight: 600; color: #111; }
.rounded-borders { border-radius: 12px; }
.html-content :deep(*) { color: #111; }
</style>
