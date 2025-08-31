<template>
  <q-card flat bordered class="q-pa-md">
    <div class="row items-center q-gutter-sm q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Problemática</div>
        <div class="text-caption text-grey-7">Registros vinculados al caso #{{ caseId }}</div>
      </div>
      <div class="col-auto row items-center q-gutter-sm">
        <q-input dense outlined v-model="search" placeholder="Buscar..." style="width:260px">
          <template v-slot:append><q-icon name="search"/></template>
        </q-input>
        <q-btn color="primary" icon="add_circle" label="Nueva" @click="openCreate" v-if="canEdit"/>
        <q-btn flat color="primary" icon="refresh" @click="fetchRows" :loading="loading"/>
      </div>
    </div>

    <q-separator/>

    <!-- Lista -->
    <q-list bordered separator class="q-mt-sm">
      <q-item v-for="it in rows.data" :key="it.id">
        <q-item-section>
          <q-item-label class="text-weight-medium">{{ it.titulo }}</q-item-label>
          <q-item-label caption class="ellipsis-2-lines">
            {{ (it.detalle || '').slice(0,220) }}
          </q-item-label>
          <q-item-label caption>
            <q-badge outline color="blue-7" class="q-mr-xs">{{ it.fecha || 's/f' }}</q-badge>
            <q-badge outline color="grey-7">Por: {{ it.user?.name || it.user?.username || '—' }}</q-badge>
          </q-item-label>
        </q-item-section>
        <q-item-section side top>
          <q-btn dense flat icon="visibility" @click="openView(it)"/>
          <q-btn dense flat icon="edit" color="primary" @click="openEdit(it)" v-if="canEdit"/>
          <q-btn dense flat icon="delete" color="negative" @click="remove(it)" v-if="canEdit"/>
          <q-btn color="red" outline label="Imprimir" @click="print(it)"/>
        </q-item-section>
      </q-item>

      <q-item v-if="!rows.data.length && !loading">
        <q-item-section>Sin resultados</q-item-section>
      </q-item>
    </q-list>

    <!-- Paginación -->
    <div class="row justify-end q-mt-sm">
      <q-pagination
        v-model="page"
        :max="rows.last_page || 1"
        boundary-numbers
        direction-links
        @input="fetchRows"
      />
    </div>

    <!-- Diálogo Crear/Editar/Ver -->
    <q-dialog v-model="dialog" persistent>
      <q-card style="min-width: 720px; max-width: 95vw;">
        <q-card-section class="row items-center">
          <div class="text-subtitle1">
            {{ mode==='view' ? 'Detalle de Problemática' : (form.id ? 'Editar Problemática' : 'Nueva Problemática') }}
          </div>
          <q-space/>
          <q-btn flat round dense icon="close" v-close-popup/>
        </q-card-section>

        <q-separator/>

        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-input v-model="form.fecha" type="date" dense outlined label="Fecha" :readonly="mode==='view'"/>
            </div>
            <div class="col-12 col-md-8">
              <q-input v-model="form.titulo" dense outlined label="Problemática principal *"
                       :readonly="mode==='view'" :rules="[v=>!!v||'Requerido']"/>
            </div>

            <div class="col-12">
              <q-input v-model="form.detalle" type="textarea" autogrow outlined dense
                       label="Detalle / antecedentes" :readonly="mode==='view'"/>
            </div>

            <div class="col-12">
              <q-input v-model="form.acciones_inmediatas" type="textarea" autogrow outlined dense
                       label="Acciones inmediatas y realizadas" :readonly="mode==='view'"/>
            </div>

            <div class="col-12">
              <q-input v-model="form.observaciones" type="textarea" autogrow outlined dense
                       label="Observaciones" :readonly="mode==='view'"/>
            </div>
          </div>
        </q-card-section>

        <q-separator/>

        <q-card-actions align="right">
          <q-btn flat label="Cerrar" v-close-popup/>
          <q-btn color="primary" label="Guardar" v-if="mode!=='view'" :loading="saving" @click="save"/>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-card>
</template>

<script>
export default {
  name: 'Problematica',
  props: {
    caseId: { type: [String, Number], required: true },
    canEdit: { type: Boolean, default: true } // por si quieres bloquear edición por permisos
  },
  data () {
    return {
      loading: false,
      saving: false,
      search: '',
      page: 1,
      perPage: 10,
      rows: { data: [], total: 0, last_page: 1 },
      dialog: false,
      mode: 'create', // 'create' | 'edit' | 'view'
      form: { id:null, fecha:'', titulo:'', detalle:'', acciones_inmediatas:'', observaciones:'' }
    }
  },
  watch: {
    caseId () { this.resetAndFetch() },
    search () { this.page = 1; this.fetchRows() }
  },
  created () { this.fetchRows() },
  methods: {
    resetAndFetch () { this.page = 1; this.fetchRows() },
    today () {
      const d = new Date(), z = n => String(n).padStart(2,'0')
      return `${d.getFullYear()}-${z(d.getMonth()+1)}-${z(d.getDate())}`
    },
    async fetchRows () {
      if (!this.caseId) return
      this.loading = true
      try {
        const res = await this.$axios.get(`/casos/${this.caseId}/problematicas`, {
          params: { q: this.search, page: this.page, per_page: this.perPage }
        })
        this.rows = res.data
        if (!this.rows.data) this.rows.data = []
      } catch (e) {
        this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'Error cargando problemática' })
      } finally {
        this.loading = false
      }
    },
    openCreate () {
      this.mode = 'create'
      this.form = { id:null, fecha:this.today(), titulo:'', detalle:'', acciones_inmediatas:'', observaciones:'' }
      this.dialog = true
    },
    openView (it)  { this.mode = 'view';  this.form = { ...it }; this.dialog = true },
    openEdit (it)  { this.mode = 'edit';  this.form = { ...it }; this.dialog = true },
    async save () {
      if (!this.form.titulo) {
        this.$q.notify({ type:'negative', message:'El título es obligatorio' })
        return
      }
      this.saving = true
      try {
        if (this.form.id) {
          await this.$axios.put(`/problematicas/${this.form.id}`, this.form)
        } else {
          await this.$axios.post(`/casos/${this.caseId}/problematicas`, this.form)
        }
        this.dialog = false
        this.$q.notify({ type:'positive', message:'Guardado' })
        this.fetchRows()
        this.$emit('changed') // notifica al padre si necesita refrescar algo
      } catch (e) {
        this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'No se pudo guardar' })
      } finally {
        this.saving = false
      }
    },
    remove (it) {
      const go = async () => {
        try {
          await this.$axios.delete(`/problematicas/${it.id}`)
          this.$q.notify({ type:'positive', message:'Eliminado' })
          this.fetchRows()
          this.$emit('changed')
        } catch (e) {
          this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'No se pudo eliminar' })
        }
      }
      // usa tu helper si lo tienes
      if (this.$alert?.dialog) {
        this.$alert.dialog('¿Eliminar registro?').onOk(go)
      } else {
        if (confirm('¿Eliminar registro?')) go()
      }
    },
    print (it) {
      const url = this.$axios.defaults.baseURL + `/problematicas/${it.id}/pdf`
      window.open(url, '_blank')
    }
  }
}
</script>

<style scoped>
.ellipsis-2-lines{
  display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
}
</style>
