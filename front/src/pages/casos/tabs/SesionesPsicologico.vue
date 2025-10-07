<template>
  <q-card flat bordered class="q-pa-md">

    <!-- Header -->
    <div class="row items-center q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Sesiones (Psicológico)</div>
        <div class="text-caption text-grey-7">Vinculadas al caso #{{ caseId }}</div>
      </div>
      <div class="col-auto row items-center q-gutter-sm">
        <q-btn flat color="primary" icon="refresh" :loading="loading" @click="$emit('refresh')"/>
        <q-btn color="green" icon="add_circle_outline" no-caps label="Crear Informe" @click="openCreate"/>
        <q-btn color="primary" icon="upload" no-caps label="Subir informe" @click="openSubirInforme"/>
      </div>
    </div>

    <q-separator/>

    <!-- Tabla -->
<!--    <pre>{{ caso }}</pre>-->
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
      <tr v-for="it in caso.psicologicas" :key="it.id">
        <td class="text-grey-8">#{{ it.id }}</td>
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
                  Descargar informe
                </q-item-section>
              </q-item>
            </template>
            <q-item clickable v-close-popup @click="remove(it)">
              <q-item-section avatar><q-icon name="delete" color="negative"/></q-item-section>
              <q-item-section class="text-negative">Eliminar</q-item-section>
            </q-item>
          </q-btn-dropdown>
        </td>
        <td>{{ it.titulo }}</td>
        <td>{{ it.fecha || 's/f' }}</td>
        <td>{{ it.tipo || '—' }}</td>
        <td>{{ it.user?.name || it.user?.username || '—' }}</td>
      </tr>

      <tr v-if="!caso.psicologicas || caso.psicologicas.length===0">
        <td colspan="6" class="text-center text-grey">Sin registros</td>
      </tr>
      </tbody>
    </q-markup-table>

    <!-- Paginación -->
<!--    <div class="row justify-end q-mt-sm">-->
<!--      <q-pagination-->
<!--        v-model="page"-->
<!--        :max="rows.last_page || 1"-->
<!--        boundary-numbers-->
<!--        direction-links-->
<!--        @input="fetchRows"-->
<!--      />-->
<!--    </div>-->

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
          <div class="row">
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

          <!-- Selector de plantilla cuando es creación -->
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
          <q-input v-model="form.titulo" dense outlined label="Título del informe *" :rules="[v=>!!v||'Requerido']"/>
          <q-file v-model="file" label="Seleccionar archivo" outlined dense @update:model-value="onFileChange"
                  accept=".pdf,.doc,.docx,.odt,.png,.jpg,.jpeg"
          />
<!--          bton guardar-->
          <q-btn color="primary" label="Guardar" :disabled="!file || !form.titulo" @click="guardarInforme"/>
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
import { SesionHtml } from 'src/addons/SesionPlantillas.js'

export default {
  name: 'SesionesPsicologico',
  emits: ['refresh'],
  props: {
    caseId: { type:[String,Number], required:true },
    caso: { type:Object, default:null }
  },
  data(){
    return {
      openSubirArchivo: false,
      file: null,
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

      // plantillas
      plantilla: null,
      plantillasOptions: [
        { label:'Consentimiento informado', value:'consentimiento'},
        { label:'Informe Psicológico (formato DIO)', value:'informe_dio' },
        { label:'Acta de sesión (DIO)', value:'acta' },
        // { label:'Informe breve (psicológico)', value:'informe' },
        // { label:'Constancia de asistencia', value:'constancia' },
      ],
    }
  },
  watch:{
    caseId(){ this.page=1; this.fetchRows() },
    search(){ this.page=1; this.fetchRows() }
  },
  created(){
    // this.fetchRows()
  },
  methods:{
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
      formData.append('tipo', 'psicologico')
      this.saving = true
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
      })
    },
    onFileChange(file){
      this.file = file
      if(!file) return
    },
    today(){
      const d=new Date(), z=n=>String(n).padStart(2,'0')
      return `${d.getFullYear()}-${z(d.getMonth()+1)}-${z(d.getDate())}`
    },
    async fetchRows(){
      if(!this.caseId) return
      this.loading = true
      try{
        const res = await this.$axios.get(`/casos/${this.caseId}/sesiones-psicologicas`, {
          params:{ q:this.search, page:this.page, per_page:this.perPage }
        })
        this.rows = res.data || { data:[], last_page:1 }
      }catch(e){
        this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'Error cargando sesiones' })
      }finally{ this.loading=false }
    },
    openSubirInforme(){
      this.form.titulo = ''
      this.openSubirArchivo = true
    },
    openCreate(){
      this.mode='create'
      this.plantilla = 'consentimiento'
      this.form = {
        id:null, fecha:this.today(), titulo:'',
        duracion_min:null, lugar:'', tipo:'Individual', contenido_html:''
      }
      this.dialog = true
      this.$nextTick(()=> this.applyTemplate('consentimiento'))
    },
    openView(it){ this.mode='view'; this.form={...it}; this.dialog=true },
    openEdit(it){ this.mode='edit'; this.form={...it}; this.dialog=true },

    applyTemplate(val) {
      if (this.mode === 'view') return

      const c = this.caso || {}

      // ===== Denunciante =====
      const denunciante = {
        nombres: c.denunciante_nombres || c.denunciante_nombre_completo || '',
        apellidos: [c.denunciante_paterno, c.denunciante_materno].filter(Boolean).join(' '),
        edad: c.denunciante_edad || '',
        fecha_nacimiento: c.denunciante_fecha_nacimiento || '',
        lugar_nacimiento: c.denunciante_lugar_nacimiento || '',
        grado: c.denunciante_grado || '',
        ocupacion: c.denunciante_ocupacion_exacto || c.denunciante_ocupacion || '',
        domicilio: c.denunciante_domicilio_actual || c.denunciante_residencia || '',
        documento: c.denunciante_documento || 'Carnet de identidad',
        nro: c.denunciante_nro || '',
        telefono: c.denunciante_telefono || c.denunciante_movil || c.denunciante_fijo || '',
        estado_civil: c.denunciante_estado_civil || '',
      }

      // ===== Familiares (1..5) =====
      const familiares = []
      for (let i = 1; i <= 5; i++) {
        const nombre = c[`familiar${i}_nombre_completo`]
        const edad   = c[`familiar${i}_edad`]
        const parent = c[`familiar${i}_parentesco`]
        const cel    = c[`familiar${i}_celular`]
        if (nombre || edad || parent || cel) {
          familiares.push({
            nombre: nombre || '',
            edad: edad || '',
            estado_civil: '',        // No está en el modelo; lo dejamos vacío
            parentesco: parent || '',
            ocupacion: '',           // Tampoco está; puedes agregarlo si luego lo guardas
          })
        }
      }

      // ===== Número de caso (preferir campo del modelo) =====
      const yyyy = (this.form.fecha || this.today()).slice(0, 4)
      const numeroCaso = c.caso_numero && String(c.caso_numero).trim() !== ''
        ? String(c.caso_numero)
        : `${String(this.caseId).padStart(2, '0')}/${yyyy}`

      // ===== Nombres de responsables (si viene con relaciones) =====
      const abogadoNombre =
        (c.legal_user?.name || c.legal_user?.username) || ''   // requiere ->with('legal_user')
      const psicologoNombre =
        (c.psicologica_user?.name || c.psicologica_user?.username) || (this.$store?.state?.user?.name) || ''

      // ===== Semillas de contenido =====
      // Motivo: breve, editable
      const motivo = c.caso_tipologia || c.caso_modalidad
        ? `Evaluación psicológica en el marco de presunta(s) ${[
          c.caso_tipologia, c.caso_modalidad,
        ].filter(Boolean).join(' / ')}.`
        : 'Evaluación psicológica para determinar los efectos emocionales y psicológicos en la evaluada.'

      // Antecedentes: usa fecha, lugar y descripción del caso
      const antecedentes = (() => {
        const f = c.caso_fecha_hecho ? `el ${c.caso_fecha_hecho}` : 'en fecha no precisada'
        const lugar = [c.caso_lugar_hecho, c.caso_zona, c.caso_direccion].filter(Boolean).join(', ')
        const lugarTxt = lugar ? `, en ${lugar}` : ''
        const relato = c.caso_descripcion ? ` ${c.caso_descripcion}` : ''
        return `Según la apertura del caso, el hecho habría ocurrido ${f}${lugarTxt}.${relato}`
      })()

      // Técnicas: base + si hay violencias marcadas
      const tecnicas = [
        'Entrevista clínica y observación conductual',
        'Aplicación de instrumentos psicométricos según pertinencia (Goldberg, IES-R, Rosenberg)',
      ].join('; ') + '.'

      // Conclusiones: base + lectura simple según flags de violencia
      const flags = [
        c.violencia_fisica ? 'violencia física' : null,
        c.violencia_psicologica ? 'violencia psicológica' : null,
        c.violencia_sexual ? 'violencia sexual' : null,
        c.violencia_economica ? 'violencia económica/patrimonial' : null,
      ].filter(Boolean)
      const conclViol = flags.length ? ` Se consideran indicadores asociados a ${flags.join(', ')}.` : ''
      const conclusiones =
        'Se evidencian reacciones emocionales congruentes con el evento referido, con impacto en el equilibrio afectivo y la autoestima.' +
        conclViol +
        ' No se evidencian signos de psicosis. Se sugiere seguimiento clínico y contención.'

      // Recomendaciones: base editable
      const recomendaciones = [
        'Intervención psicológica: terapia focalizada en manejo de ansiedad/estrés y fortalecimiento de recursos personales.',
        'Orientación/asesoramiento legal para evitar revictimización y garantizar medidas de protección, si corresponde.',
      ].join(' ')

      // ===== Base común para todas las plantillas =====
      const base = {
        casoId: this.caseId,
        fecha: this.form.fecha || this.today(),
        titulo: this.form.titulo || 'Sesión psicológica',
        lugar: this.form.lugar || '',
        tipo: this.form.tipo || 'Individual',
        nombre: c.denunciantes[0]?.denunciante_nombres || denunciante.denunciante_paterno + ' ' + denunciante.denunciante_materno || '—',
        documento: c.denunciantes[0]?.denunciante_documento || denunciante.documento || '-',

        // extras (informe DIO)
        numeroCaso,
        abogadoNombre,
        psicologoNombre,
        denunciante: {
          nombres : c.denunciantes[0].denunciante_nombres || denunciante.nombres || '—',
          apellidos: c.denunciantes[0].denunciante_apellidos || denunciante.apellidos || '—',
          edad: c.denunciantes[0].denunciante_edad || denunciante.edad || '—',
          fecha_nacimiento: c.denunciantes[0].denunciante_fecha_nacimiento || denunciante.fecha_nacimiento || '—',
          lugar_nacimiento: c.denunciantes[0].denunciante_lugar_nacimiento || denunciante.lugar_nacimiento || '—',
          grado: c.denunciantes[0].denunciante_grado || denunciante.grado || '—',
          ocupacion: c.denunciantes[0].denunciante_ocupacion_exacto || c.denunciantes[0].denunciante_ocupacion || denunciante.ocupacion || '—',
          domicilio: c.denunciantes[0].denunciante_domicilio_actual || c.denunciantes[0].denunciante_residencia || denunciante.domicilio || '—',
          documento: c.denunciantes[0].denunciante_documento || denunciante.documento || '—',
        },
        familiares: c.familiares,

        // contenido inicial
        motivo,
        antecedentes,
        tecnicas,
        conclusiones,
        recomendaciones,
      }
      // { label:'Consentimiento informado', value:'consentimiento'},
      // { label:'Informe Psicológico (formato DIO)', value:'informe_dio' },
      // { label:'Acta de sesión (DIO)', value:'acta' },

      if (val === 'acta')               this.form.contenido_html = SesionHtml.acta(base)
      else if (val === 'informe')       this.form.contenido_html = SesionHtml.informe(base)
      else if (val === 'constancia')    this.form.contenido_html = SesionHtml.constancia(base)
      else if (val === 'consentimiento')this.form.contenido_html = SesionHtml.consentimiento(base)
      else if (val === 'informe_dio')   this.form.contenido_html = SesionHtml.informe_dio(base) // NUEVO
    },

    async save(){
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
        if(this.form.id){
          await this.$axios.put(`/sesiones-psicologicas/${this.form.id}`, this.form)
        }else{
          await this.$axios.post(`/casos/${this.caseId}/sesiones-psicologicas`, this.form)
        }
        this.$q.notify({ type:'positive', message:'Guardado' })
        this.dialog=false
        this.$emit('refresh')
      }catch(e){
        this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'No se pudo guardar' })
      }finally{ this.saving=false }
    },
    openFile(it){
      window.open(this.$url+ '/..'+ it.archivo, '_blank')
    },
    remove(it){
      const go = async ()=>{
        try{
          await this.$axios.delete(`/sesiones-psicologicas/${it.id}`)
          this.$q.notify({ type:'positive', message:'Eliminado' })
          // this.fetchRows()
          this.$emit('refresh')
        }catch(e){
          this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'No se pudo eliminar' })
        }
      }
      if(this.$alert?.dialog) this.$alert.dialog('¿Eliminar la sesión?').onOk(go)
      else if(confirm('¿Eliminar la sesión?')) go()
    },

    printPdf(it){
      // Usa tu base URL si la tienes como $url
      const base = this.$url || ''
      window.open(`${base}/sesiones-psicologicas/${it.id}/pdf`, '_blank')
    }
  }
}
</script>
