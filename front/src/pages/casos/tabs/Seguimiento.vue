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
<!--    <pre>{{caso}}</pre>-->

    <!-- FILTROS -->
<!--    <q-card flat bordered class="q-pa-sm q-mb-sm rounded-borders">-->
<!--      <div class="row items-center q-col-gutter-sm">-->
<!--        <div class="col-12 col-md-4">-->
<!--          <q-input v-model="filters.q" dense outlined placeholder="Buscar (título, descripción, usuario)">-->
<!--            <template #append><q-icon name="search"/></template>-->
<!--          </q-input>-->
<!--        </div>-->
<!--        <div class="col-6 col-md-2">-->
<!--          <q-select v-model="filters.tipo" :options="tipos" dense outlined label="Tipo"/>-->
<!--        </div>-->
<!--        <div class="col-6 col-md-2">-->
<!--          <q-select v-model="filters.modulo" :options="modulos" dense outlined label="Módulo"/>-->
<!--        </div>-->
<!--        <div class="col-6 col-md-2">-->
<!--          <q-input v-model="filters.desde" type="date" dense outlined label="Desde"/>-->
<!--        </div>-->
<!--        <div class="col-6 col-md-2">-->
<!--          <q-input v-model="filters.hasta" type="date" dense outlined label="Hasta"/>-->
<!--        </div>-->
<!--      </div>-->
<!--    </q-card>-->

    <!-- TABLA -->
<!--    <pre>{{rowsFiltered}}</pre>-->
<!--    <q-table-->
<!--      flat bordered-->
<!--      :rows="rowsFiltered"-->
<!--      :columns="columns"-->
<!--      row-key="uid"-->
<!--      :loading="loading"-->
<!--      hide-bottom-->
<!--      :rows-per-page-options="[0]"-->
<!--      class="rounded-borders"-->
<!--      @rowClick="clickSeguimiento"-->
<!--    >-->
<!--      <template #body-cell-actividad="props">-->
<!--        <q-td :props="props">-->
<!--          <div class="row no-wrap items-start q-gutter-sm">-->
<!--            <q-icon :name="props.row.icon || 'feed'" size="18px" class="text-primary q-mt-xs"/>-->
<!--            <div>-->
<!--              <div class="text-weight-medium">-->
<!--                {{ props.row.tipo }}-->
<!--                <span class="text-grey-7">·</span>-->
<!--                {{ props.row.titulo || '—' }}-->
<!--              </div>-->
<!--              <div class="text-caption text-grey-7 ellipsis-2-lines" style="max-width:560px">-->
<!--                {{ props.row.descripcion || '—' }}-->
<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
<!--        </q-td>-->
<!--      </template>-->

<!--      <template #body-cell-fecha="props">-->
<!--        <q-td :props="props">-->
<!--          <div class="text-weight-medium">{{ props.row.fecha || '—' }}</div>-->
<!--          <div class="text-caption text-grey-7">{{ props.row.usuario || '—' }}</div>-->
<!--        </q-td>-->
<!--      </template>-->

<!--      <template #body-cell-modulo="props">-->
<!--        <q-td :props="props">-->
<!--          <q-chip dense square>{{ props.row.modulo || '—' }}</q-chip>-->
<!--        </q-td>-->
<!--      </template>-->

<!--      <template #body-cell-acciones="props">-->
<!--        <q-td :props="props" class="q-gutter-xs">-->
<!--          <q-btn v-if="props.row.links?.pdf" dense flat icon="picture_as_pdf" @click="open(props.row.links.pdf)" title="Ver PDF"/>-->
<!--          <q-btn v-if="props.row.links?.view" dense flat icon="visibility" @click="open(props.row.links.view)" title="Ver"/>-->
<!--          <q-btn v-if="props.row.links?.download" dense flat icon="download" @click="open(props.row.links.download)" title="Descargar"/>-->
<!--          <q-btn v-if="props.row.links?.open" dense flat icon="open_in_new" @click="open(props.row.links.open)" title="Abrir"/>-->
<!--        </q-td>-->
<!--      </template>-->
<!--    </q-table>-->
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
<!--                {{ informe.archivo ? 'Archivo' : 'Informe' }}-->
                Informe
              </td>
              <td>{{ informe.titulo }}</td>
              <td>{{ informe.fecha || '—' }}</td>
              <td>{{ informe.user?.name || '—' }}</td>
              <td>
                <a @click="openSocialInforme(informe)">
                  Ver archivo
                </a>
<!--                <span v-else>—</span>-->
              </td>
            </tr>
            <ArchivoFoto role="Social" :caso="caso" />
          </tbody>
        </q-markup-table>
<!--        {-->
<!--        "id": 5,-->
<!--        "caseable_type": "App\\Models\\Caso",-->
<!--        "caseable_id": 62,-->
<!--        "user_id": 1,-->
<!--        "fecha": null,-->
<!--        "titulo": "AAA",-->
<!--        "numero": null,-->
<!--        "contenido_html": "<p>Archivo adjunto: <a href=\"/storage/caso/62/social/1761983122_MANUAL DE ACOPIO APICOLA (2).pdf\" target=\"_blank\" rel=\"noopener\">MANUAL DE ACOPIO APICOLA (2).pdf</a></p>",-->
<!--        "created_at": "2025-11-01T07:45:22.000000Z",-->
<!--        "updated_at": "2025-11-01T07:45:22.000000Z",-->
<!--        "deleted_at": null,-->
<!--        "archivo": "/storage/caso/62/social/1761983122_MANUAL DE ACOPIO APICOLA (2).pdf",-->
<!--        "user": {-->
<!--        "id": 1,-->
<!--        "name": "Ing Evelyn Ramirez Cube"-->
<!--        }-->
<!--        },-->
      </div>
      <div class="col-12 col-md-12">
        Psicológia
      </div>
      <div class="col-12 col-md-12">
        Legal
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
