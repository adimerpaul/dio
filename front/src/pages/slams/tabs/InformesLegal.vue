<template>
  <q-card flat bordered class="q-pa-md">
    <!-- Header -->
    <div class="row items-center q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Informes legales</div>
        <div class="text-caption text-grey-7">Vinculados al caso #{{ caseId }}</div>
      </div>
      <div class="col-auto row items-center q-gutter-sm">
        <q-btn flat color="primary" icon="refresh" :loading="loading" @click="$emit('refresh')"/>
        <q-btn
          v-if="canEdit"
          color="green"
          icon="add_circle_outline"
          no-caps
          label="Crear informe"
          @click="openCreate"
        />
      </div>
    </div>

    <q-separator/>

    <!-- Tabla -->
    <q-markup-table dense flat bordered wrap-cells class="q-mt-sm">
      <thead>
      <tr class="bg-primary text-white">
        <th style="width:70px">ID</th>
        <th style="width:140px">Acciones</th>
        <th>Título</th>
        <th style="width:120px">Fecha</th>
        <th style="width:120px">Nro</th>
        <th style="width:180px">Usuario</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="it in caso.informes_legales" :key="it.id">
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
        <td>{{ it.titulo }}</td>
        <td>{{ it.fecha || 's/f' }}</td>
        <td>{{ it.numero || '—' }}</td>
        <td>{{ it.user?.name || it.user?.username || '—' }}</td>
      </tr>
      <tr v-if="!caso.informes_legales.length && !loading">
        <td colspan="6" class="text-center text-grey">Sin registros</td>
      </tr>
      </tbody>
    </q-markup-table>

    <!-- Diálogo Crear / Ver / Editar -->
    <q-dialog v-model="dialog" persistent maximized>
      <q-card>
        <q-bar class="bg-white text-dark">
          <div class="text-subtitle1">
            {{ mode==='view' ? 'Ver informe' : (form.id ? 'Editar informe' : 'Nuevo informe') }}
          </div>
          <q-space/>
          <q-btn flat dense round icon="close" v-close-popup/>
        </q-bar>

        <q-separator/>

        <q-card-section class="">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-input v-model="form.fecha" type="date" dense outlined label="Fecha" :readonly="mode==='view'"/>
            </div>
            <div class="col-12 col-md-7">
              <q-input v-model="form.titulo" dense outlined label="Título *" :readonly="mode==='view'" :rules="[v=>!!v||'Requerido']"/>
            </div>
            <div class="col-12 col-md-2">
              <q-input v-model="form.numero" dense outlined label="Nro" :readonly="mode==='view'"/>
            </div>
          </div>

          <!-- Plantillas (legales) -->
          <div v-if="mode!=='view'">
            <q-select
              v-model="plantilla"
              :options="plantillasOptions"
              label="Plantilla"
              outlined dense emit-value map-options
              @update:model-value="applyTemplate"
            />
          </div>

          <div>
            <div class="text-caption text-grey-7 q-mb-xs">Contenido (WYSIWYG / guarda HTML)</div>
            <q-editor
              v-model="form.contenido_html"
              :readonly="mode==='view'"
              min-height="320px"
              placeholder="Escriba el informe legal..."
              :toolbar="[
                ['left','center','right','justify'],
                ['bold','italic','strike','underline'],
                ['quote','unordered','ordered','outdent','indent'],
                ['hr','link'],
                ['undo','redo'],
                ['removeFormat']
              ]"
            />
          </div>
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
  name: 'InformesLegal',
  props: {
    caseId: { type:[String,Number], required:true },
    caso: { type:Object, default:null }
  },
  data(){
    return {
      loading:false, saving:false,
      search:'', page:1, perPage:10,
      rows:{ data:[], total:0, last_page:1 },

      dialog:false, mode:'create',
      form:{ id:null, fecha:'', titulo:'', numero:'', contenido_html:'' },

      plantilla: 'informe_legal',
      plantillasOptions: [
        { label:'Informe Legal (SLIM)', value:'informe_legal' },
        { label:'Memorial (simple)',   value:'memorial' },
        { label:'Oficio / Nota',       value:'oficio' },
        { label:'Acta de Compromiso',  value:'acta' },
        { label:'Denuncia penal — Ministerio Público', value:'denuncia_mp' },
      ],
    }
  },
  computed:{
    role(){ return this.$store.user?.role || '' },
    canEdit(){ return this.role === 'Administrador' || this.role === 'Abogado' }
  },
  // watch:{
  //   caseId(){ this.page=1; this.fetchRows() },
  //   search(){ this.page=1; this.fetchRows() },
  // },
  // created(){ this.fetchRows() },
  methods:{
    today(){ const d=new Date(), z=n=>String(n).padStart(2,'0'); return `${d.getFullYear()}-${z(d.getMonth()+1)}-${z(d.getDate())}` },
    // async fetchRows(){
    //   if(!this.caseId) return
    //   this.loading = true
    //   try{
    //     const res = await this.$axios.get(`/slams/${this.caseId}/informes-legales`, {
    //       params:{ q:this.search, page:this.page, per_page:this.perPage }
    //     })
    //     this.rows = res.data || { data:[], last_page:1 }
    //   }catch(e){
    //     this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'Error cargando informes' })
    //   }finally{ this.loading=false }
    // },
    openCreate(){
      if(!this.canEdit) return
      this.mode='create'
      this.form = { id:null, fecha:this.today(), titulo:'', numero:'', contenido_html:'' }
      this.dialog = true
      this.$nextTick(()=> this.applyTemplate(this.plantilla))
    },
    openView(it){ this.mode='view'; this.form={...it}; this.dialog=true },
    openEdit(it){ if(!this.canEdit) return; this.mode='edit'; this.form={...it}; this.dialog=true },

    applyTemplate(val){
      if(this.mode==='view') return
      const fecha = this.form.fecha || this.today()
      const year  = fecha.slice(0,4)
      const nro   = this.form.numero ? `N° ${this.form.numero}` : ''
      const header = `
        <div style="text-align:center; margin-bottom:8px;">
          <div style="font-size:16px; font-weight:bold;">DIRECCIÓN DE IGUALDAD DE OPORTUNIDADES</div>
          <div style="font-size:12px;">Gobierno Autónomo Municipal de Oruro</div>
          <div style="font-size:12px; font-weight:bold;">${nro}</div>
          <hr/>
        </div>
      `

      if (val === 'memorial') {
        this.form.contenido_html = header + `
          <p><b>Señor(a) Juez/Fiscal</b></p>
          <p>De mi consideración:</p>
          <p style="text-align:justify">Por medio del presente, ...</p>
          <p>Oruro, ${fecha}</p>
        `
        return
      }
      if (val === 'oficio') {
        this.form.contenido_html = header + `
          <p><b>Ref.:</b> Solicitud de información</p>
          <p>De nuestra consideración:</p>
          <p style="text-align:justify">Solicitamos remitir la información ...</p>
          <p>Oruro, ${fecha}</p>
        `
        return
      }
      if (val === 'acta') {
        this.form.contenido_html = header + `
          <p style="text-align:center"><b>ACTA DE COMPROMISO</b></p>
          <p style="text-align:justify">En la ciudad de Oruro, a ${fecha}, las partes acuerdan ...</p>
        `
        return
      }
      if (val === 'denuncia_mp') {
        const c = this.caso || {}
        const den = c.denunciante_nombre_completo || ''
        this.form.contenido_html = header + `
          <p style="text-align:center"><b>SEÑOR(A) REPRESENTANTE DEL MINISTERIO PÚBLICO — ORURO</b></p>
          <p><b>DENUNCIANTE:</b> ${den || '—'}</p>
          <p style="text-align:justify">Que, en mérito a los antecedentes ...</p>
          <p>Oruro, ${fecha}</p>
        `
        return
      }

      // default: informe_legal
      this.form.contenido_html = header + `
        <p><b>Informe Legal ${year}</b></p>
        <p style="text-align:justify">En atención a los antecedentes, se emite el presente informe con el siguiente análisis jurídico:</p>
        <p><b>Fundamentos de derecho:</b></p>
        <ul>
          <li>Ley N° 348 y reglamentación.</li>
          <li>Constitución Política del Estado.</li>
          <li>Normativa municipal vigente.</li>
        </ul>
        <p><b>Recomendaciones:</b> …</p>
        <p>Oruro, ${fecha}</p>
      `
    },

    async save(){
      if(!this.canEdit) return
      if(!this.form.titulo) return this.$q.notify({type:'negative', message:'El título es obligatorio'})
      if(!this.form.contenido_html) return this.$q.notify({type:'negative', message:'El contenido está vacío'})

      this.saving = true
      try{
        if(this.form.id)
          await this.$axios.put(`/slams/informes-legales/${this.form.id}`, this.form)
        else
          await this.$axios.post(`/slams/${this.caseId}/informes-legales`, this.form)

        this.$q.notify({ type:'positive', message:'Guardado' })
        this.dialog=false;
        this.$emit('refresh')
      }catch(e){
        this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'No se pudo guardar' })
      }finally{ this.saving=false }
    },

    removeIt(it){
      if(!this.canEdit) return
      const go = async ()=> {
        try{
          await this.$axios.delete(`/slams/informes-legales/${it.id}`)
          this.$q.notify({type:'positive', message:'Eliminado'})
          this.$emit('refresh')
        }catch(e){
          this.$q.notify({type:'negative', message:e?.response?.data?.message || 'No se pudo eliminar'})
        }
      }
      if(this.$alert?.dialog) this.$alert.dialog('¿Eliminar el informe?').onOk(go)
      else if(confirm('¿Eliminar el informe?')) go()
    },

    printPdf(it){
      const base = this.$axios?.defaults?.baseURL || ''
      window.open(`${base}/slams/informes-legales/${it.id}/pdf`, '_blank')
    }
  }
}
</script>
