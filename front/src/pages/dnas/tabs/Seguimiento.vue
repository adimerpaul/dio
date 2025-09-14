<template>
  <div>
    <q-card flat bordered class="q-pa-sm q-mb-sm">
      <div class="row items-start q-col-gutter-sm">
        <div class="col">
          <div class="text-subtitle1 text-weight-bold">Seguimiento del Caso</div>
          <div class="text-caption text-grey-7">DNA #{{ caseId }}</div>
        </div>
        <div class="col-auto">
          <q-btn flat color="primary" icon="refresh" :loading="loading" @click="fetch"/>
        </div>
      </div>
    </q-card>

    <q-card flat bordered class="q-pa-sm q-mb-sm">
      <div class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-4">
          <q-input v-model="filters.q" dense outlined placeholder="Buscar (título, descripción, usuario)">
            <template #append><q-icon name="search"/></template>
          </q-input>
        </div>
        <div class="col-6 col-md-2"><q-select v-model="filters.tipo" :options="tipos" dense outlined label="Tipo"/></div>
        <div class="col-6 col-md-2"><q-select v-model="filters.modulo" :options="modulos" dense outlined label="Módulo"/></div>
        <div class="col-6 col-md-2"><q-input v-model="filters.desde" type="date" dense outlined label="Desde"/></div>
        <div class="col-6 col-md-2"><q-input v-model="filters.hasta" type="date" dense outlined label="Hasta"/></div>
      </div>
    </q-card>

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
      <template #body-cell-actividad="p">
        <q-td :props="p">
          <div class="row no-wrap items-center q-gutter-xs">
            <q-icon :name="p.row.icon || 'feed'" size="18px" class="text-primary"/>
            <div>
              <div class="text-weight-medium">{{ p.row.tipo }} <span class="text-grey-7">·</span> {{ p.row.titulo || '—' }}</div>
              <div class="text-caption text-grey-7 ellipsis-2-lines" style="max-width:520px">{{ p.row.descripcion || '—' }}</div>
            </div>
          </div>
        </q-td>
      </template>
      <template #body-cell-fecha="p">
        <q-td :props="p">
          <div class="text-weight-medium">{{ p.row.fecha || '—' }}</div>
          <div class="text-caption text-grey-7">{{ p.row.usuario || '—' }}</div>
        </q-td>
      </template>
      <template #body-cell-modulo="p">
        <q-td :props="p"><q-chip dense square>{{ p.row.modulo || '—' }}</q-chip></q-td>
      </template>
      <template #body-cell-acciones="p">
        <q-td :props="p" class="q-gutter-xs">
          <q-btn v-if="p.row.links?.pdf" dense flat icon="picture_as_pdf" @click="open(p.row.links.pdf)" title="Ver PDF"/>
          <q-btn v-if="p.row.links?.view" dense flat icon="visibility" @click="open(p.row.links.view)" title="Ver"/>
          <q-btn v-if="p.row.links?.download" dense flat icon="download" @click="open(p.row.links.download)" title="Descargar"/>
          <q-btn v-if="p.row.links?.open" dense flat icon="open_in_new" @click="open(p.row.links.open)" title="Abrir"/>
        </q-td>
      </template>
    </q-table>
  </div>
</template>

<script>
export default {
  name: 'DnaSeguimiento',
  props: { caseId: { type: [String, Number], required: true } },
  data: () => ({
    loading:false,
    header:{}, rows:[],
    filters:{ q:'', tipo:'Todos', modulo:'Todos', desde:'', hasta:'' },
    tipos:['Todos','Informe','Sesión','Documento','Fotografía'],
    modulos:['Todos','General','Psicológico','Legal','Documentos','Multimedia'],
    columns:[
      { name:'actividad', label:'Actividad', field:'actividad', align:'left' },
      { name:'fecha',     label:'Fecha / Usuario', field:'fecha', align:'left', sortable:true },
      { name:'modulo',    label:'Módulo', field:'modulo', align:'left', sortable:true },
      { name:'acciones',  label:'', align:'right', style:'width:140px' }
    ]
  }),
  computed:{
    rowsFiltered(){
      let r = this.rows.slice()
      const f = this.filters
      if (f.q) {
        const q = f.q.toLowerCase()
        r = r.filter(x =>
          (x.titulo||'').toLowerCase().includes(q) ||
          (x.descripcion||'').toLowerCase().includes(q) ||
          (x.usuario||'').toLowerCase().includes(q)
        )
      }
      if (f.tipo !== 'Todos')   r = r.filter(x => x.tipo === f.tipo)
      if (f.modulo !== 'Todos') r = r.filter(x => (x.modulo || '').toLowerCase() === f.modulo.toLowerCase())
      if (f.desde) r = r.filter(x => (x.fecha || '0000-00-00') >= f.desde)
      if (f.hasta) r = r.filter(x => (x.fecha || '9999-12-31') <= f.hasta)
      return r
    }
  },
  mounted(){ this.fetch() },
  methods:{
    async fetch(){
      this.loading=true
      try{
        const { data } = await this.$axios.get(`/dnas/${this.caseId}/seguimiento`)
        this.header = data.header || {}
        this.rows   = data.items  || []
      }catch(e){ this.$alert?.error?.(e?.response?.data?.message || 'No se pudo cargar el seguimiento') }
      finally{ this.loading=false }
    },
    toPublicUrl (url) {
      if (!url) return ''
      if (/^https?:\/\//i.test(url)) return url
      const baseApi = this.$axios?.defaults?.baseURL || ''
      const basePublic = baseApi.replace(/\/api\/?$/, '')
      return `${basePublic}${url}`
    },
    open(u){ const url=this.toPublicUrl(u); if(url) window.open(url,'_blank') }
  }
}
</script>

<style scoped>
.rounded-borders{ border-radius:12px }
.ellipsis-2-lines{ display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden }
</style>
