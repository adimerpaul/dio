<template>
  <div>
    <!-- CABECERA -->
    <q-card flat bordered class="q-pa-sm q-mb-md">
      <div class="row items-start q-col-gutter-sm">
        <div class="col-12 col-md-8">
          <div class="text-subtitle1 text-weight-bold">Seguimiento del Caso — UMADI</div>
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
        <div class="col-12 col-md-3">
          <div class="lbl">Denunciante</div>
          <div class="val">{{ header.denunciante || '—' }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="lbl">Denunciado</div>
          <div class="val">{{ header.denunciado || '—' }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="lbl">Dirección (actual)</div>
          <div class="val ellipsis">{{ header.direccion_actual || '—' }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="lbl">Fecha del hecho</div>
          <div class="val">{{ header.fecha_hecho || '—' }}</div>
        </div>
      </div>
    </q-card>

    <!-- LÍNEA DE TIEMPO -->
    <q-card flat bordered class="q-pa-md">
      <div class="text-subtitle1 text-weight-medium q-mb-md">Historial</div>

      <q-timeline color="primary" layout="comfortable">
        <q-timeline-entry
          v-for="item in history"
          :key="item.uid"
          :title="item.title"
          :subtitle="item.subtitle"
          :body="item.brief"
          :icon="item.icon"
          :side="item.side"
        >
          <div class="q-mt-sm">
            <div class="row q-gutter-xs">
              <q-btn
                v-if="item.kind==='doc' && item.url"
                dense flat icon="download" label="Descargar / Abrir"
                @click="open(item.url)"
              />
              <q-btn
                v-if="item.kind==='photo' && item.url"
                dense flat icon="image" label="Ver imagen"
                @click="open(item.url)"
              />
              <q-btn
                v-if="(item.kind==='psy' || item.kind==='legal') && item.html"
                dense flat icon="visibility" label="Ver contenido"
                @click="openHtml(item.title, item.html)"
              />
            </div>

            <q-img
              v-if="item.kind==='photo' && item.thumbUrl"
              :src="toPublicUrl(item.thumbUrl)"
              :ratio="16/9"
              class="q-mt-sm rounded-borders"
              style="max-width:380px"
              :alt="item.title"
              spinner-color="primary"
            />
          </div>
        </q-timeline-entry>
      </q-timeline>

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
</template>

<script>
export default {
  name: 'SeguimientoUmadi',
  props: {
    // Puede venir plano { ... } o anidado { umadi: {...} }
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
  watch: {
    caso: { handler () { this.hydrate() }, deep: true }
  },
  methods: {
    hydrate () {
      // 1) Normaliza forma del objeto
      const u = this.caso?.umadi ? this.caso.umadi : (this.caso || {})

      // 2) Header
      const denunciante = [u.nombres, u.paterno, u.materno].filter(Boolean).join(' ')
      const denunciado  = [u.denunciado_nombres, u.denunciado_paterno, u.denunciado_materno].filter(Boolean).join(' ')

      this.header = {
        area: u.area, zona: u.zona,
        fecha_registro: u.fecha_registro,
        numero_caso: u.numero_caso || u.numero_apoyo_integral,
        direccion_actual: u.direccion_actual || u.direccion,
        fecha_hecho: u.fecha_hecho,
        registrado_por: u.user?.name || '—',
        denunciante: denunciante || '—',
        denunciado: denunciado || '—'
      }

      // 3) Línea de tiempo desde:
      //    psicologicas, informes_legales, documentos, fotografias
      const items = []

        // Psicología
      ;(u.psicologicas || []).forEach(p => {
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

      // Legal
      ;(u.informes_legales || []).forEach(l => {
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
      ;(u.documentos || []).forEach(d => {
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
      ;(u.fotografias || []).forEach(f => {
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
