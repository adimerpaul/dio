<!-- src/components/Informes.vue -->
<template>
  <q-card flat bordered class="q-pa-md">
    <div class="row items-center q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Informes</div>
        <div class="text-caption text-grey-7">Vinculados al caso #{{ caseId }}</div>
      </div>
      <div class="col-auto row items-center q-gutter-sm">
        <q-input dense outlined v-model="search" placeholder="Buscar..." style="width:260px">
          <template #append><q-icon name="search"/></template>
        </q-input>
        <q-btn flat color="primary" icon="refresh" :loading="loading" @click="$emit('refresh')"/>
        <q-btn color="green" icon="add_circle_outline" no-caps label="Crear informe" @click="openCreate"/>
        <q-btn color="primary" icon="upload" no-caps label="Subir Informe" @click="openSubirInforme"/>
<!--        <q-btn color="secondary" icon="folder_open" no-caps label="Carpeta digital" @click="openCarpetaDigital"/>-->
      </div>
    </div>

    <q-separator/>
<!--    <pre>{{caso}}</pre>-->

    <q-markup-table dense flat bordered wrap-cells class="q-mt-sm">
      <thead>
      <tr class="bg-primary text-white">
        <th style="width:70px">ID</th>
        <th style="width:140px">Acciones</th>
        <th>Título</th>
        <th style="width:120px">Fecha</th>
        <th style="width:130px">Área</th>
        <th style="width:120px">Nro</th>
        <th style="width:180px">Usuario</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="it in caso.informes_sociales" :key="it.id">
        <td>#{{ it.id }}</td>
        <td>
          <q-btn-dropdown dense color="primary" size="sm" label="Opciones" no-caps>
            <template v-if="!it.archivo">
              <q-item clickable v-close-popup @click="openView(it)">
                <q-item-section avatar><q-icon name="visibility"/></q-item-section>
                <q-item-section>Ver</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="printPdf(it)">
                <q-item-section avatar><q-icon name="print"/></q-item-section>
                <q-item-section>Imprimir</q-item-section>
              </q-item>
              <q-separator/>
              <q-item clickable v-close-popup @click="openEdit(it)">
                <q-item-section avatar><q-icon name="edit"/></q-item-section>
                <q-item-section>Editar</q-item-section>
              </q-item>
            </template>
            <template v-else>
              <q-item clickable v-close-popup @click="openFile(it)" >
                <q-item-section avatar><q-icon name="download"/></q-item-section>
                <q-item-section>
                  Descargar
                </q-item-section>
              </q-item>
            </template>
            <q-item clickable v-close-popup @click="removeIt(it)">
              <q-item-section avatar><q-icon name="delete" color="negative"/></q-item-section>
              <q-item-section class="text-negative">Eliminar</q-item-section>
            </q-item>
          </q-btn-dropdown>
        </td>
        <td>{{ it.titulo }}</td>
        <td>{{ it.fecha || 's/f' }}</td>
        <td>{{ it.area || '—' }}</td>
        <td>{{ it.numero || '—' }}</td>
        <td>{{ it.user?.name || it.user?.username || '—' }}</td>
      </tr>
      <tr v-if="!caso.informes_sociales.length">
        <td colspan="7" class="text-center text-grey">Sin registros</td>
      </tr>
      </tbody>
    </q-markup-table>

<!--    <div class="row justify-end q-mt-sm">-->
<!--      <q-pagination v-model="page" :max="rows.last_page || 1" boundary-numbers direction-links @input="fetchRows"/>-->
<!--    </div>-->

    <!-- Diálogo -->
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

        <q-card-section class="q-gutter-md">
          <div class="row ">
            <div class="col-12 col-md-3">
              <q-input v-model="form.fecha" type="date" dense outlined label="Fecha" :readonly="mode==='view'"/>
            </div>
            <div class="col-12 col-md-5">
              <q-input v-model="form.titulo" dense outlined label="Título *" :readonly="mode==='view'" :rules="[v=>!!v||'Requerido']"/>
            </div>
            <div class="col-6 col-md-2">
              <q-select v-model="form.area" :options="areas" dense outlined label="Área" :readonly="mode==='view'" emit-value map-options/>
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="form.numero" dense outlined label="Nro" :readonly="mode==='view'"/>
            </div>
          </div>

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
              placeholder="Escriba el informe..."
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
          <q-btn color="primary" label="Guardar" v-if="mode!=='view'" :loading="saving" @click="save"/>
        </q-card-actions>
      </q-card>
    </q-dialog>
    <!--    <q-dialog openSubirArchivo-->
    <q-dialog v-model="openSubirArchivo" persistent>
      <q-card style="max-width: 500px; width: 90vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1">Subir informe</div>
          <q-space/>
          <q-btn flat round dense icon="close" v-close-popup/>
        </q-card-section>

        <q-card-section class="q-gutter-md">
          <div class="text-body2 q-mb-sm">
            Aquí puede subir un informe psicológico ya elaborado (PDF, Word, etc.) y vincularlo al caso.
          </div>
          <q-input v-model="form.titulo" dense outlined label="Título informe *" :rules="[v=>!!v||'Requerido']"/>
          <q-file v-model="file" label="Seleccionar archivo" outlined dense @update:model-value="onFileChange"
                  accept=".pdf,.doc,.docx,.odt,.png,.jpg,.jpeg,.xlsx,.xls"
          />
          <q-btn color="primary" label="Guardar" :disabled="!file || !form.titulo" @click="guardarInforme"
                 :loading="loading"
          />
        </q-card-section>

        <q-separator/>
        <q-card-actions align="right">
          <q-btn flat label="Cerrar" v-close-popup/>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-card>
</template>

<script>
import { InformeHtml } from 'src/addons/SocialPlantillas.js'

export default {
  name: 'SocialInformes',
  props: {
    caseId: { type:[String,Number], required:true },
    caso: { type:Object, default:null }  // opcional, para usar en plantillas
  },
  data(){
    return {
      openSubirArchivo: false,
      file: null,
      loading:false, saving:false,
      search:'', page:1, perPage:10,
      rows:{ data:[], total:0, last_page:1 },

      dialog:false, mode:'create',
      form:{ id:null, fecha:'', titulo:'', area:'psicologico', numero:'', contenido_html:'' },

      plantilla: 'informe_social',
      plantillasOptions: [
        // { label:'Informe Psicológico (formato SLIM)', value:'informe_psico' },
        // { label:'Informe Legal (fundamentos)',       value:'informe_legal' },
        // { label:'Informe Social (visita / entorno)', value:'informe_social' },
        // { label:'Denuncia penal — Ministerio Público (Afbuso sexual)', value:'denuncia_mp' }
        { label:'Informe Social', value:'informe_social' },
        { label:'Acto Social', value:'acto_social' },
      ],
      areas: [
        { label:'Psicológico', value:'psicologico' },
        { label:'Legal',       value:'legal' },
        { label:'Social',      value:'social' },
      ],
    }
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
    //     const res = await this.$axios.get(`/casos/${this.caseId}/informes`, {
    //       params:{ q:this.search, page:this.page, per_page:this.perPage }
    //     })
    //     this.rows = res.data || { data:[], last_page:1 }
    //   }catch(e){
    //     this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'Error cargando informes' })
    //   }finally{ this.loading=false }
    // },
    guardarInforme(){
      if(!this.file){
        this.$q.notify({ type:'negative', message:'Seleccione un archivo' })
        return
      }
      if(!this.form.titulo){
        this.$q.notify({ type:'negative', message:'El título es obligatorio' })
        return
      }
      const formData = new FormData()
      formData.append('titulo', this.form.titulo)
      formData.append('file', this.file)
      formData.append('case_id', this.caseId)
      formData.append('tipo', 'social')
      this.saving = true
      this.loading = true
      this.$axios.post(`/casos/${this.caseId}/uploadFile`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      }).then(res=>{
        this.$q.notify({ type:'positive', message:'Informe subido' })
        this.openSubirArchivo = false
        this.file = null
        this.form.titulo = ''
        this.$emit('refresh')
      }).catch(e=>{
        this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'No se pudo subir el informe' })
      }).finally(()=>{
        this.saving = false
        this.loading = false
      })
    },
    onFileChange(file){
      this.file = file
      if(!file) return
    },
    openCarpetaDigital(){
      console.log('Abrir carpeta digital del caso', this.caseId)
    },
    openSubirInforme(){
      this.openSubirArchivo = true
    },
    openCreate(){
      this.mode='create'
      this.form = { id:null, fecha:this.today(), titulo:'', area:'psicologico', numero:'', contenido_html:'' }
      this.dialog = true
      this.$nextTick(()=> this.applyTemplate(this.plantilla))
    },
    openView(it){ this.mode='view'; this.form={...it}; this.dialog=true },
    openEdit(it){ this.mode='edit'; this.form={...it}; this.dialog=true },
    openFile(it){
      window.open(this.$url+ '/..'+ it.archivo, '_blank')
    },

    applyTemplate(val){
      if(this.mode==='view') return
      // vaciar
      this.form.contenido_html = ''

      if(val==='informe_social') {this.form.contenido_html = InformeHtml.informeSocial(this.caso)}

      // const baseMin = {
      //   casoId: this.caseId,
      //   fecha: this.form.fecha || this.today(),
      //   titulo: this.form.titulo || 'Informe',
      //   area: this.form.area || 'psicologico',
      //   numero: this.form.numero || ''
      // }
      //
      // if (val === 'informe_psico')  this.form.contenido_html = InformeHtml.psicologico(baseMin)
      // if (val === 'informe_legal')  this.form.contenido_html = InformeHtml.legal(baseMin)
      // if (val === 'informe_social') this.form.contenido_html = InformeHtml.social(baseMin)
      //
      // if (val === 'denuncia_mp') {
      //   // Si tienes el caso cargado en esta vista, úsalo aquí.
      //   const c = this.caso || {}  // <-- pásalo como prop si puedes, similar a SesionesPsicologico
      //   const denunciante = {
      //     nombre: c.denunciante_nombre_completo,
      //     ci: c.denunciante_nro,
      //     fecha_nac: c.denunciante_fecha_nacimiento,
      //     lugar_nac: c.denunciante_lugar_nacimiento,
      //     edad: c.denunciante_edad,
      //     ocupacion: c.denunciante_ocupacion_exacto || c.denunciante_ocupacion,
      //     estado_civil: c.denunciante_estado_civil,
      //     domicilio: c.denunciante_domicilio_actual || c.denunciante_residencia,
      //     celular: c.denunciante_telefono || c.denunciante_movil,
      //     correo: c.denunciante_correo // si lo tienes
      //   }
      //   const denunciado = {
      //     nombre: c.denunciado_nombre_completo,
      //     ci: c.denunciado_nro,
      //     fecha_nac: c.denunciado_fecha_nacimiento,
      //     nacionalidad: 'Boliviana',
      //     ocupacion: c.denunciado_ocupacion_exacto || c.denunciado_ocupacion,
      //     estado_civil: c.denunciado_estado_civil,
      //     domicilio: c.denunciado_domicilio_actual || c.denunciado_residencia,
      //     celular: c.denunciado_telefono || c.denunciado_movil,
      //     correo: c.denunciado_correo // si lo tienes
      //   }
      //
      //   const payload = {
      //     ...baseMin,
      //     ciudad: 'ORURO',
      //     fiscaliaTitulo: 'SEÑOR REPRESENTANTE DEL MINISTERIO PÚBLICO DE LA CIUDAD DE ORURO',
      //     titulo: 'Denuncia penal por Abuso Sexual',
      //     denunciante,
      //     denunciado,
      //     ciudadania_digital_denunciante: c.documento_ciudadania_digital ? c.denunciante_nro : '',
      //     relato: c.caso_descripcion || '',
      //     abogado: {
      //       nombre: c.legal_user?.name || '',
      //       correo: c.legal_user?.correo || '',
      //       whatsapp: c.legal_user?.celular || ''
      //     }
      //   }
      //
      //   this.form.contenido_html = InformeHtml.denuncia_mp(payload)
      // }
    },

    async save(){
      if(!this.form.titulo) return this.$q.notify({type:'negative', message:'El título es obligatorio'})
      if(!this.form.contenido_html) return this.$q.notify({type:'negative', message:'El contenido está vacío'})
      this.saving = true
      try{
        // Route::post('/casos/{caso}/informes-sociales', [\App\Http\Controllers\CasoController::class, 'socialStore']);
        // Route::put ('/informes-sociales/{informe}',     [\App\Http\Controllers\CasoController::class, 'socialUpdate']);
        // Route::delete('/informes-sociales/{informe}', [\App\Http\Controllers\CasoController::class, 'socialDestroy']);
        if(this.form.id) await this.$axios.put(`/informes-sociales/${this.form.id}`, this.form)
        else await this.$axios.post(`/casos/${this.caseId}/informes-sociales`, this.form)
        this.$q.notify({ type:'positive', message:'Guardado' })
        this.$emit('refresh')
        this.dialog = false
      }catch(e){
        this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'No se pudo guardar' })
      }finally{ this.saving=false }
    },

    removeIt(it){
      const go = async ()=> {
        try{ await this.$axios.delete(`/informes-sociales/${it.id}`); this.$q.notify({type:'positive', message:'Eliminado'}); this.$emit('refresh') }
        catch(e){ this.$q.notify({type:'negative', message:e?.response?.data?.message || 'No se pudo eliminar'}) }
      }
      if(this.$alert?.dialog) this.$alert.dialog('¿Eliminar el informe?').onOk(go)
      else if(confirm('¿Eliminar el informe?')) go()
    },

    printPdf(it){
      const base = this.$url || ''
      window.open(`${base}/informesSocial/${it.id}/pdf`, '_blank')
    }
  }
}
</script>
