<template>
  <q-card flat bordered class="q-pa-md">
    <!-- Header -->
    <div class="row items-center q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Sesiones (Psicológico · DNA)</div>
        <div class="text-caption text-grey-7">Caso #{{ caseId }}</div>
      </div>
      <div class="col-auto row items-center q-gutter-sm">
        <q-input dense outlined v-model="search" placeholder="Buscar..." style="width:260px">
          <template #append><q-icon name="search"/></template>
        </q-input>
        <q-btn flat color="primary" icon="refresh" :loading="loading" @click="fetchRows"/>
        <q-btn
          v-if="canEdit"
          color="green"
          icon="add_circle_outline"
          no-caps
          label="Crear sesión"
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
        <th style="width:120px">Tipo</th>
        <th style="width:180px">Usuario</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="it in rows.data" :key="it.id">
        <td class="text-grey-8">#{{ it.id }}</td>
        <td>
          <q-btn-dropdown dense color="primary" size="sm" label="Opciones" no-caps>
            <q-item clickable v-close-popup @click="openView(it)">
              <q-item-section avatar><q-icon name="visibility"/></q-item-section>
              <q-item-section>Ver</q-item-section>
            </q-item>
            <q-item clickable v-close-popup @click="printPdf(it)">
              <q-item-section avatar><q-icon name="print"/></q-item-section>
              <q-item-section>Imprimir</q-item-section>
            </q-item>

            <template v-if="canEdit">
              <q-separator/>
              <q-item clickable v-close-popup @click="openEdit(it)">
                <q-item-section avatar><q-icon name="edit"/></q-item-section>
                <q-item-section>Editar</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="remove(it)">
                <q-item-section avatar><q-icon name="delete" color="negative"/></q-item-section>
                <q-item-section class="text-negative">Eliminar</q-item-section>
              </q-item>
            </template>
          </q-btn-dropdown>
        </td>
        <td>{{ it.titulo || '—' }}</td>
        <td>{{ it.fecha || 's/f' }}</td>
        <td>{{ it.tipo || '—' }}</td>
        <td>{{ it.user?.name || it.user?.username || '—' }}</td>
      </tr>

      <tr v-if="(!rows || !rows.data || rows.data.length === 0) && !loading">
        <td colspan="6" class="text-center text-grey">Sin registros</td>
      </tr>
      </tbody>
    </q-markup-table>

    <!-- Paginación -->
    <div class="row justify-end q-mt-sm">
      <q-pagination
        v-model="page"
        :max="rows.last_page || 1"
        boundary-numbers
        direction-links
        @update:model-value="fetchRows"
      />
    </div>

    <!-- Diálogo Crear / Ver / Editar -->
    <q-dialog v-model="dialog" persistent>
      <q-card style="max-width: 900px; width: 95vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1">
            {{ mode==='view' ? 'Ver sesión' : (form.id ? 'Editar sesión' : 'Nueva sesión') }}
          </div>
          <q-space/>
          <q-btn flat round dense icon="close" v-close-popup/>
        </q-card-section>

        <q-card-section class="q-gutter-md">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-input v-model="form.fecha" type="date" dense outlined label="Fecha" :readonly="mode==='view'"/>
            </div>
            <div class="col-12 col-md-5">
              <q-input v-model="form.titulo" dense outlined label="Título *" :readonly="mode==='view'" :rules="[v=>!!v||'Requerido']"/>
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model.number="form.duracion_min" type="number" dense outlined label="Minutos" :readonly="mode==='view'"/>
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="form.tipo" dense outlined label="Tipo" :readonly="mode==='view'"/>
            </div>
            <div class="col-12">
              <q-input v-model="form.lugar" dense outlined label="Lugar" :readonly="mode==='view'"/>
            </div>
          </div>

          <!-- Plantillas (solo creación/edición) -->
          <div v-if="mode!=='view'">
            <q-select
              v-model="plantilla"
              :options="plantillasOptions"
              label="Plantilla"
              outlined dense
              emit-value map-options
              @update:model-value="applyTemplate"
            />
          </div>

          <div>
            <div class="text-caption text-grey-7 q-mb-xs">Contenido (WYSIWYG / se guarda HTML)</div>
            <q-editor
              v-model="form.contenido_html"
              :readonly="mode==='view'"
              min-height="320px"
              placeholder="Escriba aquí el contenido de la sesión..."
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
import { SesionHtml } from 'src/addons/SesionPlantillas.js'

export default {
  name: 'DnaPsicologiaSesiones',
  props: {
    caseId: { type:[String,Number], required:true },
    // Pásame el caso DNA (ideal con relaciones cargadas) para poder autocompletar plantillas
    caso: { type:Object, default:null }
  },
  data(){
    return {
      loading:false, saving:false,
      search:'', page:1, perPage:10,
      rows:{ data:[], total:0, last_page:1 },

      dialog:false,
      mode:'create', // view | edit | create
      form:{
        id:null, fecha:'', titulo:'',
        duracion_min:null, lugar:'', tipo:'Individual',
        contenido_html:''
      },

      plantilla: null,
      plantillasOptions: [
        { label:'Consentimiento informado', value:'consentimiento'},
        { label:'Informe Psicológico (formato DIO)', value:'informe_dio' },
        { label:'Acta de sesión (DIO)', value:'acta' },
        { label:'Informe breve (psicológico)', value:'informe' },
        { label:'Constancia de asistencia', value:'constancia' },
      ],
    }
  },
  computed:{
    role(){ return this.$store.user?.role || '' },
    roleNorm(){
      const s = (this.role || '').normalize('NFD').replace(/\p{Diacritic}/gu,'').toUpperCase()
      return s
    },
    canEdit(){
      // Admin/Administrador y Psicólogo/variantes
      return ['ADMIN','ADMINISTRADOR','PSICOLOGO','PSICOLOGA','PSICOLOGIA','PSICOLOGÍA'].includes(this.roleNorm)
    }
  },
  watch:{
    caseId(){ this.page=1; this.fetchRows() },
    search(){ this.page=1; this.fetchRows() }
  },
  created(){ this.fetchRows() },
  methods:{
    today(){
      const d=new Date(), z=n=>String(n).padStart(2,'0')
      return `${d.getFullYear()}-${z(d.getMonth()+1)}-${z(d.getDate())}`
    },

    async fetchRows(){
      if(!this.caseId) return
      this.loading = true
      try{
        const res = await this.$axios.get(`/dnas/${this.caseId}/sesiones-psicologicas`, {
          params:{ q:this.search, page:this.page, per_page:this.perPage }
        })
        // Normaliza: soporta respuesta paginada o array plano
        this.rows = Array.isArray(res.data)
          ? { data: res.data, last_page: 1, total: res.data.length }
          : (res.data || { data:[], last_page:1, total:0 })
      }catch(e){
        this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'Error cargando sesiones' })
        this.rows = { data:[], last_page:1, total:0 }
      }finally{ this.loading=false }
    },

    openCreate(){
      if(!this.canEdit) return
      this.mode='create'
      this.plantilla = 'consentimiento'
      this.form = {
        id:null, fecha:this.today(), titulo:'',
        duracion_min:null, lugar:'', tipo:'Individual', contenido_html:''
      }
      this.dialog = true
      this.$nextTick(()=> this.applyTemplate('consentimiento'))
    },
    openView(it){
      this.mode='view'
      this.form = {
        id: it.id || null,
        fecha: it.fecha || '',
        titulo: it.titulo || '',
        duracion_min: it.duracion_min ?? null,
        lugar: it.lugar || '',
        tipo: it.tipo || 'Individual',
        contenido_html: it.contenido_html || it.descripcion || ''
      }
      this.dialog = true
    },
    openEdit(it){
      if(!this.canEdit) return
      this.mode='edit'
      this.form = {
        id: it.id || null,
        fecha: it.fecha || '',
        titulo: it.titulo || '',
        duracion_min: it.duracion_min ?? null,
        lugar: it.lugar || '',
        tipo: it.tipo || 'Individual',
        contenido_html: it.contenido_html || it.descripcion || ''
      }
      this.dialog = true
    },

    applyTemplate(val) {
      if (this.mode === 'view') return

      // === DNA mapeo ===
      const c = this.caso || {}

      // Denunciante (DNA usa campos simples)
      const denunciante = {
        nombres: c.denunciante_nombre || '',
        apellidos: '',
        edad: c.denunciante_edad || '',
        fecha_nacimiento: '', // DNA no lo tiene por defecto
        lugar_nacimiento: '',
        grado: '', // no está en DNA base
        ocupacion: c.denunciante_ocupacion || '',
        domicilio: c.denunciante_domicilio || '',
        documento: c.denunciante_ci ? 'C.I.' : 'Documento',
        nro: c.denunciante_ci || '',
        telefono: c.denunciante_telefono || '',
        estado_civil: '',
      }

      // Familiares (si cargaste relación -> array)
      const familiares = Array.isArray(c.familiares)
        ? c.familiares.map(f => ({
          nombre: f.nombre || '',
          edad: f.edad || '',
          estado_civil: '',
          parentesco: f.parentesco || '',
          ocupacion: f.ocupacion || '',
        }))
        : []

      // Número de caso
      const yyyy = (this.form.fecha || this.today()).slice(0, 4)
      const numeroCaso = c.codigo && String(c.codigo).trim() !== ''
        ? String(c.codigo)
        : `${String(this.caseId).padStart(2, '0')}/${yyyy}`

      const abogadoNombre = c.abogado?.name || c.abogado?.username || ''
      const psicologoNombre = (this.$store?.state?.user?.name) || (this.$store?.user?.name) || ''

      const motivo = c.tipo_proceso || c.principal
        ? `Evaluación psicológica en el marco de ${[c.tipo_proceso, c.principal].filter(Boolean).join(' / ')}.`
        : 'Evaluación psicológica para establecer indicadores clínicos relevantes.'

      const antecedentes = (() => {
        const f = c.fecha_atencion ? `en fecha ${c.fecha_atencion}` : 'en fecha no precisada'
        const lugar = [c.zona, c.domicilio].filter(Boolean).join(', ')
        const lugarTxt = lugar ? `, en ${lugar}` : ''
        const relato = c.descripcion ? ` ${c.descripcion}` : ''
        return `Según los antecedentes del caso, la atención se realizó ${f}${lugarTxt}.${relato}`
      })()

      const tecnicas = [
        'Entrevista clínica y observación conductual',
        'Aplicación de instrumentos psicométricos según pertinencia (Goldberg, IES-R, Rosenberg)',
      ].join('; ') + '.'

      const conclusiones =
        'Se observan indicadores emocionales compatibles con el evento referido. No se evidencian signos de psicosis. Se recomienda seguimiento clínico.'

      const recomendaciones = [
        'Terapia focalizada en manejo de ansiedad/estrés y fortalecimiento de recursos personales.',
        'Orientación legal para evitar revictimización y garantizar medidas de protección, si corresponde.',
      ].join(' ')

      const base = {
        casoId: this.caseId,
        fecha: this.form.fecha || this.today(),
        titulo: this.form.titulo || 'Sesión psicológica',
        lugar: this.form.lugar || '',
        tipo: this.form.tipo || 'Individual',

        // Identificación
        nombre: c.denunciante_nombre || '',
        documento: c.denunciante_ci || '',

        // Cabecera
        numeroCaso,
        abogadoNombre,
        psicologoNombre,

        // Bloques
        denunciante,
        familiares,
        motivo,
        antecedentes,
        tecnicas,
        conclusiones,
        recomendaciones,
      }

      if (val === 'acta')               this.form.contenido_html = SesionHtml.acta(base)
      else if (val === 'informe')       this.form.contenido_html = SesionHtml.informe(base)
      else if (val === 'constancia')    this.form.contenido_html = SesionHtml.constancia(base)
      else if (val === 'consentimiento')this.form.contenido_html = SesionHtml.consentimiento(base)
      else if (val === 'informe_dio')   this.form.contenido_html = SesionHtml.informe_dio(base)
    },

    async save(){
      if(!this.canEdit) return
      if(!this.form.titulo){
        this.$q.notify({ type:'negative', message:'El título es obligatorio' })
        return
      }
      if(!this.form.contenido_html){
        this.$q.notify({ type:'negative', message:'El contenido está vacío' })
        return
      }
      this.saving = true
      try{
        const payload = {
          fecha: this.form.fecha || null,
          titulo: this.form.titulo,
          duracion_min: this.form.duracion_min ?? null,
          lugar: this.form.lugar || '',
          tipo: this.form.tipo || 'Individual',
          contenido_html: this.form.contenido_html || ''
        }
        if(this.form.id){
          await this.$axios.put(`/dnas/sesiones-psicologicas/${this.form.id}`, payload)
        }else{
          await this.$axios.post(`/dnas/${this.caseId}/sesiones-psicologicas`, payload)
        }
        this.$q.notify({ type:'positive', message:'Guardado' })
        this.dialog=false
        this.fetchRows()
      }catch(e){
        this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'No se pudo guardar' })
      }finally{ this.saving=false }
    },

    remove(it){
      if(!this.canEdit) return
      const go = async ()=>{
        try{
          await this.$axios.delete(`/dnas/sesiones-psicologicas/${it.id}`)
          this.$q.notify({ type:'positive', message:'Eliminado' })
          this.fetchRows()
        }catch(e){
          this.$q.notify({ type:'negative', message:e?.response?.data?.message || 'No se pudo eliminar' })
        }
      }
      if(this.$alert?.dialog) this.$alert.dialog('¿Eliminar la sesión?').onOk(go)
      else if(confirm('¿Eliminar la sesión?')) go()
    },

    printPdf(it){
      const base = this.$axios?.defaults?.baseURL || ''
      // Asegúrate de tener la ruta en backend: GET /dnas/sesiones-psicologicas/{id}/pdf
      window.open(`${base}/dnas/sesiones-psicologicas/${it.id}/pdf`, '_blank')
    }
  }
}
</script>
