<template>
  <div>
    <!-- Encabezado acciones -->
    <div class="row items-center q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Información General</div>
        <div class="text-caption text-grey-7">Tipo: <q-badge color="blue-2" text-color="blue-10">{{ tipoLabel(form.tipo_proceso) }}</q-badge></div>
      </div>
      <div class="col-auto row q-gutter-sm" v-if="canEdit">
        <q-btn v-if="!editing" color="primary" icon="edit" label="Editar" @click="startEdit" :loading="loading"/>
        <template v-else>
          <q-btn flat color="negative" icon="close" label="Cancelar" @click="cancelEdit" :loading="loading"/>
          <q-btn color="primary" icon="save" label="Guardar" @click="save" :loading="loading"/>
        </template>
      </div>
    </div>

    <!-- Lectura -->
    <template v-if="!editing">
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row q-col-gutter-md">
          <div class="col-6 col-md-3"><b>Código:</b> {{ orDash(form.codigo) }}</div>
          <div class="col-6 col-md-3"><b>Fecha:</b> {{ orDash(form.fecha_atencion) }}</div>
          <div class="col-12 col-md-6"><b>Principal:</b> {{ orDash(form.principal) }}</div>
          <div class="col-12 col-md-6"><b>Domicilio:</b> {{ orDash(form.domicilio) }}</div>
          <div class="col-6 col-md-3"><b>Teléfono:</b> {{ orDash(form.telefono) }}</div>
          <div class="col-6 col-md-3"><b>Zona:</b> {{ orDash(form.zona) }}</div>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="text-subtitle1">Denunciante</q-card-section>
        <q-separator/>
        <q-card-section class="row q-col-gutter-md">
          <div class="col-12 col-md-6"><b>Nombre:</b> {{ orDash(form.denunciante_nombre) }}</div>
          <div class="col-6 col-md-3"><b>Sexo:</b> {{ orDash(form.denunciante_sexo) }}</div>
          <div class="col-6 col-md-3"><b>Edad:</b> {{ orDash(form.denunciante_edad) }}</div>
          <div class="col-6 col-md-3"><b>CI:</b> {{ orDash(form.denunciante_ci) }}</div>
          <div class="col-12 col-md-6"><b>Domicilio:</b> {{ orDash(form.denunciante_domicilio) }}</div>
          <div class="col-6 col-md-3"><b>Teléfono:</b> {{ orDash(form.denunciante_telefono) }}</div>
          <div class="col-6 col-md-3"><b>Ocupación:</b> {{ orDash(form.denunciante_ocupacion) }}</div>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="text-subtitle1">Denunciado</q-card-section>
        <q-separator/>
        <q-card-section class="row q-col-gutter-md">
          <div class="col-12 col-md-6"><b>Nombre:</b> {{ orDash(form.denunciado_nombre) }}</div>
          <div class="col-6 col-md-3"><b>Sexo:</b> {{ orDash(form.denunciado_sexo) }}</div>
          <div class="col-6 col-md-3"><b>Edad:</b> {{ orDash(form.denunciado_edad) }}</div>
          <div class="col-6 col-md-3"><b>CI:</b> {{ orDash(form.denunciado_ci) }}</div>
          <div class="col-12 col-md-6"><b>Domicilio:</b> {{ orDash(form.denunciado_domicilio) }}</div>
          <div class="col-6 col-md-3"><b>Teléfono:</b> {{ orDash(form.denunciado_telefono) }}</div>
          <div class="col-6 col-md-3"><b>Ocupación:</b> {{ orDash(form.denunciado_ocupacion) }}</div>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="text-subtitle1">Asignación</q-card-section>
        <q-separator/>
        <q-card-section>
          <div><b>Abogado(a):</b> {{ abogadoNombre }}</div>
        </q-card-section>
      </q-card>
    </template>

    <!-- Edición -->
    <template v-else>
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3"><q-input v-model="form.codigo" dense outlined label="Código"/></div>
            <div class="col-12 col-md-3"><q-input v-model="form.fecha_atencion" type="date" dense outlined label="Fecha"/></div>
            <div class="col-12 col-md-6"><q-input v-model="form.principal" dense outlined clearable label="Principal"/></div>
            <div class="col-12 col-md-6"><q-input v-model="form.domicilio" dense outlined clearable label="Domicilio"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.telefono" dense outlined clearable label="Teléfono"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.zona" dense outlined clearable label="Zona"/></div>
          </div>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="text-subtitle1">Denunciante</q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6"><q-input v-model="form.denunciante_nombre" dense outlined label="Nombre *" :rules="[req]"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciante_sexo" dense outlined label="Sexo"/></div>
            <div class="col-6 col-md-3"><q-input v-model.number="form.denunciante_edad" type="number" dense outlined label="Edad"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciante_ci" dense outlined label="CI"/></div>
            <div class="col-12 col-md-6"><q-input v-model="form.denunciante_domicilio" dense outlined label="Domicilio"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciante_telefono" dense outlined label="Teléfono"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciante_ocupacion" dense outlined label="Ocupación"/></div>
          </div>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="text-subtitle1">Denunciado</q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6"><q-input v-model="form.denunciado_nombre" dense outlined label="Nombre"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_sexo" dense outlined label="Sexo"/></div>
            <div class="col-6 col-md-3"><q-input v-model.number="form.denunciado_edad" type="number" dense outlined label="Edad"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_ci" dense outlined label="CI"/></div>
            <div class="col-12 col-md-6"><q-input v-model="form.denunciado_domicilio" dense outlined label="Domicilio"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_telefono" dense outlined label="Teléfono"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_ocupacion" dense outlined label="Ocupación"/></div>
          </div>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="text-subtitle1">Asignación</q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-select v-model="form.abogado_user_id" dense outlined emit-value map-options clearable
                        :options="abogados.map(u => ({ label: u.name, value: u.id }))"
                        label="Abogado/a asignado"/>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </template>
  </div>
</template>

<script>
export default {
  name: 'DnaInfoGeneral',
  props: { caseId: { type: [String, Number], required: true } },
  data: () => ({
    loading:false, editing:false, form:{}, backup:null, abogados:[]
  }),
  computed:{
    role(){ return this.$store.user?.role || '' },
    canEdit(){ return this.role==='Administrador' || this.role==='Asistente' },
    abogadoNombre(){
      if (this.form.abogado?.name) return this.form.abogado.name
      const f = this.abogados.find(a => Number(a.id) === Number(this.form.abogado_user_id))
      return f ? f.name : 'Sin asignar'
    }
  },
  created(){ this.fetch(); this.fetchAbogados() },
  methods:{
    tipoLabel(v){
      const m = { PROCESO_PENAL:'Procesos Penales', PROCESO_FAMILIAR:'Procesos Familiares', PROCESO_NNA:'Procesos Niñez y Adolescencia', APOYO_INTEGRAL:'Apoyos Integrales' }
      return m[(v||'').toUpperCase()] || v || '—'
    },
    req(v){ return !!v || 'Requerido' },
    orDash(v){ return v ? v : '—' },
    async fetch(){
      this.loading = true
      try{
        const { data } = await this.$axios.get(`/dnas/${this.caseId}`)
        this.form = { ...data }
        if (!this.form.abogado_user_id && this.form.abogado?.id) this.form.abogado_user_id = this.form.abogado.id
        this.backup = { ...this.form }
      }catch(e){ this.$alert?.error?.(e?.response?.data?.message || 'No se pudo cargar') }
      finally{ this.loading = false }
    },
    async fetchAbogados(){
      try{
        const { data } = await this.$axios.get('/usuariosRole')
        this.abogados = data.abogados || []
      }catch(e){ /* noop */ }
    },
    startEdit(){ if(!this.canEdit) return; this.editing=true; this.backup={...this.form} },
    cancelEdit(){ this.form={...this.backup}; this.editing=false; this.$q.notify({type:'info',message:'Cambios descartados'}) },
    async save(){
      this.loading = true
      try{
        const res = await this.$axios.put(`/dnas/${this.caseId}`, this.form)
        this.form = res.data?.dna || res.data || this.form
        this.backup = { ...this.form }
        this.editing = false
        this.$q.notify({ type:'positive', message:'Información actualizada' })
      }catch(e){ this.$alert?.error?.(e?.response?.data?.message || 'No se pudo actualizar') }
      finally{ this.loading=false }
    }
  }
}
</script>

<style scoped>
.section-card { border-radius: 14px; overflow: hidden; margin-bottom: 16px; background: #fff; }
</style>
