<template>
  <div>
    <!-- BLOQUE SUPERIOR: cabecera estilo ficha -->
    <pre>{{caso}}</pre>
    <q-card flat bordered class="q-pa-sm q-mb-md">
      <div class="row items-start q-col-gutter-sm">
        <div class="col-12 col-md-8">
          <div class="text-subtitle1 text-weight-bold">Seguimiento del Caso</div>
          <div class="text-caption text-grey-7">
            Caso <b>#{{ header.caso_id }}</b> · N° <b>{{ header.caso_numero || '—' }}</b>
          </div>
        </div>
        <div class="col-12 col-md-4 flex items-center justify-end">
          <q-chip dense color="indigo-1" text-color="indigo-9" class="q-mr-xs">
            {{ header.fecha_registro || '—' }}
          </q-chip>
          <!--            btn actulizar-->
          <q-btn flat color="primary" icon="refresh" @click="fetch" :loading="loading" title="Actualizar" :label="loading ? '' : 'Actualizar'" no-caps dense/>
        </div>
      </div>

      <q-separator class="q-my-sm"/>

      <!-- Grid de datos compactos -->
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
    </q-card>

    <!-- FILTROS -->
    <q-card flat bordered class="q-pa-sm q-mb-sm">
      <div class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-4">
          <q-input v-model="filters.q" dense outlined placeholder="Buscar (título, descripción, usuario)">
            <template #append><q-icon name="search"/></template>
          </q-input>
        </div>
        <div class="col-6 col-md-2">
          <q-select v-model="filters.tipo" :options="tipos" dense outlined label="Tipo"/>
        </div>
        <div class="col-6 col-md-2">
          <q-select v-model="filters.modulo" :options="modulos" dense outlined label="Módulo"/>
        </div>
        <div class="col-6 col-md-2">
          <q-input v-model="filters.desde" type="date" dense outlined label="Desde"/>
        </div>
        <div class="col-6 col-md-2">
          <q-input v-model="filters.hasta" type="date" dense outlined label="Hasta"/>
        </div>
      </div>
    </q-card>

    <!-- TABLA -->
    <q-table
      flat bordered
      :rows="rowsFiltered"
      :columns="columns"
      row-key="uid"
      :loading="loading"
      hide-bottom
      :rows-per-page-options="[0]"
      class="rounded-borders"
    >
      <template #body-cell-actividad="props">
        <q-td :props="props">
          <div class="row no-wrap items-center q-gutter-xs">
            <q-icon :name="props.row.icon || 'feed'" size="18px" class="text-primary"/>
            <div>
              <div class="text-weight-medium">{{ props.row.tipo }} <span class="text-grey-7">·</span> {{ props.row.titulo || '—' }}</div>
              <div class="text-caption text-grey-7 ellipsis-2-lines" style="max-width:520px">
                {{ props.row.descripcion || '—' }}
              </div>
            </div>
          </div>
        </q-td>
      </template>

      <template #body-cell-fecha="props">
        <q-td :props="props">
          <div class="text-weight-medium">{{ props.row.fecha || '—' }}</div>
          <div class="text-caption text-grey-7">{{ props.row.usuario || '—' }}</div>
        </q-td>
      </template>

      <template #body-cell-modulo="props">
        <q-td :props="props">
          <q-chip dense square>{{ props.row.modulo || '—' }}</q-chip>
        </q-td>
      </template>

      <template #body-cell-acciones="props">
        <q-td :props="props" class="q-gutter-xs">
          <q-btn v-if="props.row.links?.pdf" dense flat icon="picture_as_pdf" @click="open(props.row.links.pdf)" title="Ver PDF"/>
          <q-btn v-if="props.row.links?.view" dense flat icon="visibility" @click="open(props.row.links.view)" title="Ver"/>
          <q-btn v-if="props.row.links?.download" dense flat icon="download" @click="open(props.row.links.download)" title="Descargar"/>
          <q-btn v-if="props.row.links?.open" dense flat icon="open_in_new" @click="open(props.row.links.open)" title="Abrir"/>
        </q-td>
      </template>
    </q-table>
  </div>
</template>

<script>
export default {
  name: 'Seguimiento',
  props: {
    // caseId: { type: [String, Number], required: true }
    caso: { type: Object, required: true }
  },
  data () {
    return {
      loading: false,
      header: {},
      rows: [],
      filters: {
        q: '',
        tipo: 'Todos',
        modulo: 'Todos',
        desde: '',
        hasta: ''
      },
      tipos: ['Todos', 'Informe', 'Sesión', 'Documento', 'Fotografía'],
      modulos: ['Todos', 'General', 'Psicológico', 'Legal', 'Social', 'Documentos', 'Multimedia'],
      columns: [
        { name: 'actividad', label: 'Actividad', field: 'actividad', align: 'left', sortable: false },
        { name: 'fecha',     label: 'Fecha / Usuario', field: 'fecha', align: 'left', sortable: true },
        { name: 'modulo',    label: 'Módulo', field: 'modulo', align: 'left', sortable: true },
        { name: 'acciones',  label: '', field: 'acciones', align: 'right', sortable: false, style: 'width: 140px' }
      ]
    }
  },
  computed: {
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
    },
    apiBase () {
      return this.$axios?.defaults?.baseURL || ''
    }
  },
  mounted () {
    // this.fetch()
  },
  methods: {
    async fetch () {
      this.loading = true
      try {
        const { data } = await this.$axios.get(`/casos/${this.caseId}/seguimiento`)
        this.header = data.header || {}
        this.rows   = data.items  || []
      } catch (e) {
        this.$alert?.error?.(e?.response?.data?.message || 'No se pudo cargar el seguimiento')
      } finally {
        this.loading = false
      }
    },
    open (url) {
      if (url) window.open(url, '_blank')
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
