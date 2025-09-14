<template>
  <q-card flat bordered class="q-pa-md">
    <div class="row items-center q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Sesiones psicológicas</div>
        <div class="text-caption text-grey-7">Caso #{{ caseId }}</div>
      </div>
      <div class="col-auto row items-center q-gutter-sm">
        <q-btn flat color="primary" icon="refresh" :loading="loading" @click="fetchRows"/>
        <q-btn v-if="canEdit" color="green" icon="add_circle_outline" no-caps label="Nueva sesión" @click="openCreate"/>
      </div>
    </div>

    <q-separator/>

    <q-markup-table dense flat bordered class="q-mt-sm">
      <thead>
      <tr class="bg-primary text-white">
        <th style="width:70px">ID</th>
        <th style="width:140px">Acciones</th>
        <th>Fecha</th>
        <th>Título</th>
        <th>Usuario</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="it in rows.data" :key="it.id">
        <td>#{{ it.id }}</td>
        <td>
          <q-btn-dropdown dense color="primary" size="sm" label="Opciones" no-caps>
            <q-item clickable v-close-popup @click="openView(it)">
              <q-item-section avatar><q-icon name="visibility"/></q-item-section>
              <q-item-section>Ver</q-item-section>
            </q-item>
            <q-item clickable v-close-popup @click="printPdf(it)">
              <q-item-section avatar><q-icon name="picture_as_pdf"/></q-item-section>
              <q-item-section>Imprimir</q-item-section>
            </q-item>
            <template v-if="canEdit">
              <q-separator/>
              <q-item clickable v-close-popup @click="openEdit(it)">
                <q-item-section avatar><q-icon name="edit"/></q-item-section>
                <q-item-section>Editar</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="removeIt(it)">
                <q-item-section avatar><q-icon name="delete" color="negative"/></q-item-section>
                <q-item-section class="text-negative">Eliminar</q-item-section>
              </q-item>
            </template>
          </q-btn-dropdown>
        </td>
        <td>{{ it.fecha || 's/f' }}</td>
        <td>{{ it.titulo || '—' }}</td>
        <td>{{ it.user?.name || '—' }}</td>
      </tr>
      <tr v-if="!rows.data.length && !loading">
        <td colspan="5" class="text-center text-grey">Sin registros</td>
      </tr>
      </tbody>
    </q-markup-table>

    <div class="row justify-end q-mt-sm">
      <q-pagination v-model="page" :max="rows.last_page || 1" boundary-numbers direction-links @update:model-value="fetchRows"/>
    </div>

    <!-- Diálogo -->
    <q-dialog v-model="dialog" persistent>
      <q-card style="width: 95vw; max-width: 760px;">
        <q-bar class="bg-white text-dark">
          <div class="text-subtitle1">{{ mode==='view' ? 'Ver sesión' : (form.id ? 'Editar sesión' : 'Nueva sesión') }}</div>
          <q-space/><q-btn flat dense round icon="close" v-close-popup/>
        </q-bar>
        <q-separator/>

        <q-card-section class="q-gutter-md">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4"><q-input v-model="form.fecha" type="date" dense outlined label="Fecha" :readonly="mode==='view'"/></div>
            <div class="col-12 col-md-8"><q-input v-model="form.titulo" dense outlined label="Título *" :readonly="mode==='view'" :rules="[v=>!!v||'Requerido']"/></div>
          </div>
          <q-input v-model="form.descripcion" type="textarea" autogrow outlined dense label="Descripción" :readonly="mode==='view'"/>
        </q-card-section>

        <q-separator/>
        <q-card-actions align="right">
          <q-btn flat label="Cerrar" v-close-popup/>
          <q-btn color="primary" label="Guardar" v-if="mode!=='view' && canEdit" :loading="saving" @click="save"/>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-card>
</template>

<script>
export default {
  name: 'DnaPsicologia',
  props: { caseId: { type:[String,Number], required:true } },
  data: () => ({
    loading:false, saving:false,
    rows:{ data:[], last_page:1 }, page:1, perPage:10,
    dialog:false, mode:'create',
    form:{ id:null, fecha:'', titulo:'', descripcion:'' }
  }),
  computed:{
    role(){ return this.$store.user?.role || '' },
    canEdit(){ return this.role === 'Administrador' || this.role === 'Psicologo' }
  },
  created(){ this.fetchRows() },
  methods:{
    async fetchRows(){
      this.loading = true
      try{
        const { data } = await this.$axios.get(`/dnas/${this.caseId}/sesiones-psicologicas`, { params:{ page:this.page, per_page:this.perPage }})
        this.rows = data || { data:[], last_page:1 }
      }catch(e){ this.$q.notify({type:'negative', message: e?.response?.data?.message || 'Error cargando sesiones'}) }
      finally{ this.loading=false }
    },
    openCreate(){ if(!this.canEdit) return; this.mode='create'; this.form={ id:null, fecha:this.today(), titulo:'', descripcion:'' }; this.dialog=true },
    openView(it){ this.mode='view'; this.form={...it}; this.dialog=true },
    openEdit(it){ if(!this.canEdit) return; this.mode='edit'; this.form={...it}; this.dialog=true },
    today(){ const d=new Date(), z=n=>String(n).padStart(2,'0'); return `${d.getFullYear()}-${z(d.getMonth()+1)}-${z(d.getDate())}` },
    async save(){
      if(!this.form.titulo) return this.$q.notify({type:'negative',message:'El título es obligatorio'})
      this.saving = true
      try{
        if(this.form.id) await this.$axios.put(`/dnas/sesiones-psicologicas/${this.form.id}`, this.form)
        else await this.$axios.post(`/dnas/${this.caseId}/sesiones-psicologicas`, this.form)
        this.$q.notify({type:'positive', message:'Guardado'})
        this.dialog=false; this.fetchRows()
      }catch(e){ this.$q.notify({type:'negative', message: e?.response?.data?.message || 'No se pudo guardar'}) }
      finally{ this.saving=false }
    },
    removeIt(it){
      if(!this.canEdit) return
      const go = async()=> {
        try{ await this.$axios.delete(`/dnas/sesiones-psicologicas/${it.id}`); this.$q.notify({type:'positive',message:'Eliminado'}); this.fetchRows() }
        catch(e){ this.$q.notify({type:'negative',message:e?.response?.data?.message || 'No se pudo eliminar'}) }
      }
      if(this.$alert?.dialog) this.$alert.dialog('¿Eliminar la sesión?').onOk(go)
      else if(confirm('¿Eliminar la sesión?')) go()
    },
    printPdf(it){
      const base = this.$axios?.defaults?.baseURL || ''
      window.open(`${base}/dnas/sesiones-psicologicas/${it.id}/pdf`, '_blank')
    }
  }
}
</script>
