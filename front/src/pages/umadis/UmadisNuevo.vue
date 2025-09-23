<!-- src/pages/umadis/CasoNuevoUMADIS.vue -->
<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Barra superior -->
    <div class="toolbar q-pa-sm bg-white row items-center q-gutter-sm shadow-1" v-if="!editable">
      <div class="col">
        <div class="text-h6 text-weight-bold">Registro de Denuncias — UMADIS</div>
        <div class="text-caption text-grey-7">Nuevo caso · Servicio Legal Integral Municipal (UMADIS)</div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </div>

    <div v-if="editable" class="q-mb-sm text-right">
      <q-btn flat color="primary" icon="refresh" label="Recargar" @click="getCaso" class="q-mr-sm"/>
      <q-btn color="primary" icon="save" label="Actualizar" :loading="loading" @click="update"/>
    </div>

    <q-form class="q-mt-lg" @submit.prevent="save">
      <!-- 1) ENCABEZADO -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="assignment" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Encabezado</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-input v-model="f.caso_fecha_hecho" type="date" dense outlined label="Fecha de registro"/>
            </div>
            <div class="col-12 col-md-5">
              <q-input v-model="f.principal" dense outlined clearable label="Tipología del hecho principal *" :rules="[req]"/>
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="f.zona" dense outlined clearable label="Módulo / Zona"/>
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="f.caso_numero" dense outlined clearable label="N° caso (si corresponde)" readonly/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 2) DENUNCIANTE -->
      <q-card flat bordered class="section-card">
        <q-card>
          <q-card-section class="row items-center">
            <q-icon name="assignment" class="q-mr-sm"/>
            <div class="text-subtitle1 text-weight-medium">2) Datos del/la denunciante</div>

          </q-card-section>
          <q-card-section>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_nombres" dense outlined clearable label="Nombres *" :rules="[req]"/></div>
              <div class="col-6 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_paterno" dense outlined clearable label="Ap. paterno"/></div>
              <div class="col-6 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_materno" dense outlined clearable label="Ap. materno"/></div>
              <div class="col-6 col-md-3">
                <q-select v-model="f.denunciantes[0].denunciante_sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/>
              </div>

              <div class="col-6 col-md-3">
                <q-select v-model="f.denunciantes[0].denunciante_documento" :options="$documentos" emit-value map-options dense outlined label="Documento"/>
              </div>
              <div class="col-6 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_nro" dense outlined clearable label="N° documento"/></div>

              <div class="col-12 col-md-3">
                <q-input v-model="f.denunciantes[0].denunciante_fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento"
                         @update:model-value="autoEdad('denunciante')"/>
              </div>
              <div class="col-12 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/></div>

              <div class="col-6 col-md-2"><q-input v-model.number="f.denunciantes[0].denunciante_edad" type="number" dense outlined label="Edad"/></div>
              <div class="col-6 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_residencia" dense outlined clearable label="Residencia habitual"/></div>
              <div class="col-6 col-md-3">
                <q-select v-model="f.denunciantes[0].denunciante_estado_civil"
                          :options="estadosCiviles" dense outlined label="Estado civil"/>
              </div>

              <div class="col-12 col-md-3">
                <q-input v-model="f.denunciantes[0].denunciante_relacion" dense outlined clearable label="Relación con el denunciado/a"/>
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model="f.denunciantes[0].denunciante_grado" dense outlined clearable label="Grado de instrucción"/>
              </div>

              <div class="col-12 col-md-3">
                <q-toggle v-model="f.denunciantes[0].denunciante_trabaja" label="Trabaja"/>
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model="f.denunciantes[0].denunciante_ocupacion" dense outlined clearable label="Ocupación"/>
              </div>

              <div class="col-12 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_idioma" dense outlined clearable label="Idioma"/></div>
              <div class="col-12 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_telefono" dense outlined clearable label="Teléfono de referencia"/></div>

              <div class="col-12 col-md-7"><q-input v-model="f.denunciantes[0].denunciante_domicilio_actual" dense outlined clearable label="Domicilio actual"/></div>
              <div class="col-12 col-md-2">
                <q-btn outline label="Buscar en mapa" @click="$refs.denMap?.geocodeAndFly(f.denunciantes[0].denunciante_domicilio_actual)"/>
              </div>
              <div class="col-12">
                <MapPicker v-model="denunciantePos" :center="oruroCenter" :address="f.denunciantes[0].denunciante_domicilio_actual" country="bo" ref="denMap"/>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </q-card>

      <!-- 3) GRUPO FAMILIAR -->
      <q-card flat bordered class="section-card">
        <q-expansion-item icon="diversity_3" dense-toggle expand-separator
                          label="3) Grupo familiar" header-class="text-subtitle1">
          <q-card>
            <q-card-section>
              <div class="q-mb-sm">
                <q-btn dense icon="add" color="primary" label="Agregar familiar" @click="addFam"/>
              </div>

              <q-markup-table dense flat bordered>
                <thead>
                <tr class="bg-blue-1 text-blue-10">
                  <th>Nombres y apellidos</th>
                  <th style="width:90px">Edad</th>
                  <th style="width:160px">Parentesco</th>
                  <th style="width:160px">Estado civil</th>
                  <th>Ocupación</th>
                  <th style="width:160px">Celular</th>
                  <th style="width:56px"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(g,i) in f.familiares" :key="'g'+i">
                  <td class="row q-col-gutter-xs">
                    <div class="col"><q-input v-model="g.familiar_nombres" dense outlined placeholder="Nombres"/></div>
                    <div class="col-6 col-md-3"><q-input v-model="g.familiar_paterno" dense outlined placeholder="Paterno"/></div>
                    <div class="col-6 col-md-3"><q-input v-model="g.familiar_materno" dense outlined placeholder="Materno"/></div>
                  </td>
                  <td><q-input v-model.number="g.familiar_edad" type="number" dense outlined/></td>
                  <td><q-input v-model="g.familiar_parentesco" dense outlined/></td>
                  <td><q-input v-model="g.familiar_estado_civil" dense outlined/></td>
                  <td><q-input v-model="g.familiar_ocupacion" dense outlined/></td>
                  <td><q-input v-model="g.familiar_telefono" dense outlined/></td>
                  <td class="text-center">
                    <q-btn flat dense round icon="delete" color="negative" @click="f.familiares.splice(i,1)"/>
                  </td>
                </tr>
                </tbody>
              </q-markup-table>
              <div v-if="!f.familiares.length" class="text-grey text-caption">Sin integrantes registrados</div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- 4) DENUNCIADO/A -->
      <q-card flat bordered class="section-card">
        <q-expansion-item icon="person_off" dense-toggle expand-separator
                          label="4) Datos de la persona denunciada" header-class="text-subtitle1">
          <q-card>
            <q-card-section>
              <div class="row q-col-gutter-md">
                <div class="col-12 col-md-3"><q-input v-model="f.denunciados[0].denunciado_nombres" dense outlined clearable label="Nombres"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_paterno" dense outlined clearable label="Ap. paterno"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_materno" dense outlined clearable label="Ap. materno"/></div>

                <div class="col-6 col-md-3"><q-select v-model="f.denunciados[0].denunciado_sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/></div>

                <div class="col-6 col-md-3">
                  <q-select v-model="f.denunciados[0].denunciado_documento" :options="$documentos" emit-value map-options dense outlined label="Documento"/>
                </div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_nro" dense outlined clearable label="N° documento"/></div>

                <div class="col-12 col-md-3">
                  <q-input v-model="f.denunciados[0].denunciado_fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento"
                           @update:model-value="autoEdad('denunciado')"/>
                </div>
                <div class="col-12 col-md-3"><q-input v-model="f.denunciados[0].denunciado_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/></div>

                <div class="col-6 col-md-2"><q-input v-model.number="f.denunciados[0].denunciado_edad" type="number" dense outlined label="Edad"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_residencia" dense outlined clearable label="Residencia habitual"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_estado_civil" dense outlined clearable label="Estado civil"/></div>

                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_idioma" dense outlined clearable label="Idioma"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_grado" dense outlined clearable label="Grado de instrucción"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_ocupacion" dense outlined clearable label="Ocupación"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_telefono" dense outlined clearable label="Teléfono"/></div>
                <div class="col-12 col-md-6"><q-input v-model="f.denunciados[0].denunciado_domicilio_actual" dense outlined clearable label="Dirección actual"/></div>
                <div class="col-12 col-md-6"><q-input v-model="f.denunciados[0].denunciado_relacion" dense outlined clearable label="Relación con la denunciante"/></div>
              </div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- 5) HECHO -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="description" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">5) Breve circunstancia del hecho / denuncia</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <q-input
            v-model="f.caso_descripcion"
            type="textarea" autogrow outlined dense clearable
            label="Descripción"
            maxlength="5000" counter
          >
            <template v-slot:append>
              <q-icon name="mic" class="cursor-pointer"
                      :color="isListening && activeField==='caso_descripcion' ? 'red' : 'grey-7'"
                      @click="toggleRecognition('caso_descripcion')"/>
            </template>
          </q-input>
        </q-card-section>
      </q-card>

      <!-- 6) SEGUIMIENTO -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">6) Seguimiento del caso</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-select v-model="f.psicologica_user_id" dense outlined emit-value map-options clearable
                        :options="psicologos.map(u => ({ label: u.name, value: u.id }))"
                        label="Área psicológica (responsable)"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="f.trabajo_social_user_id" dense outlined emit-value map-options clearable
                        :options="sociales.map(u => ({ label: u.name, value: u.id }))"
                        label="Área social (responsable)"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="f.legal_user_id" dense outlined emit-value map-options clearable
                        :options="abogados.map(u => ({ label: u.name, value: u.id }))"
                        label="Área legal (responsable)"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Acciones -->
      <div class="text-right q-mb-lg">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm" class="q-mr-sm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save" v-if="!editable"/>
        <q-btn color="primary" icon="save" label="Actualizar" :loading="loading" @click="update" v-if="editable"/>
      </div>
    </q-form>
  </q-page>
</template>

<script>
import MapPicker from 'components/MapPicker.vue'

export default {
  name: 'CasoNuevoUMADIS',
  components: { MapPicker },
  props: {
    casoId: { type: [Number, String], default: null }, // si lo usas para editar
    editable: { type: Boolean, default: false },
  },
  data () {
    return {
      loading: false,
      recognition: null,
      activeField: null,
      isListening: false,
      oruroCenter: [-17.9667, -67.1167],

      psicologos: [], abogados: [], sociales: [],
      sexos: [
        { label: 'Femenino', value: 'Femenino' },
        { label: 'Masculino', value: 'Masculino' },
        { label: 'Otro', value: 'Otro' },
      ],
      estadosCiviles: ['Soltero/a','Casado/a','Divorciado/a','Separado/a','Viudo/a','Unión libre','Otro'],

      f: {
        // ====== ENCABEZADO / CASO ======
        tipo: 'UMADIS',
        zona: 'CENTRAL',
        principal: '',
        caso_numero: '',
        caso_fecha_hecho: '',
        caso_descripcion: '',

        // ====== NESTEDS ======
        denunciantes: [{
          denunciante_nombres: '',
          denunciante_paterno: '',
          denunciante_materno: '',
          denunciante_documento: 'Carnet de identidad',
          denunciante_nro: '',
          denunciante_sexo: '',
          denunciante_lugar_nacimiento: '',
          denunciante_fecha_nacimiento: '',
          denunciante_edad: '',
          denunciante_telefono: '',
          denunciante_residencia: '',
          denunciante_estado_civil: '',
          denunciante_relacion: '',
          denunciante_grado: '',
          denunciante_trabaja: false,
          denunciante_ocupacion: '',
          denunciante_idioma: '',
          denunciante_domicilio_actual: '',
          latitud: null,
          longitud: null,
        }],
        denunciados: [{
          denunciado_nombres: '',
          denunciado_paterno: '',
          denunciado_materno: '',
          denunciado_documento: 'Carnet de identidad',
          denunciado_nro: '',
          denunciado_sexo: '',
          denunciado_lugar_nacimiento: '',
          denunciado_fecha_nacimiento: '',
          denunciado_edad: '',
          denunciado_telefono: '',
          denunciado_residencia: '',
          denunciado_estado_civil: '',
          denunciado_relacion: '',
          denunciado_grado: '',
          denunciado_ocupacion: '',
          denunciado_idioma: '',
          denunciado_domicilio_actual: '',
          denunciado_latitud: null,
          denunciado_longitud: null,
        }],
        familiares: [],

        // ====== ASIGNACIONES ======
        psicologica_user_id: null,
        trabajo_social_user_id: null,
        legal_user_id: null,
      }
    }
  },
  computed: {
    denunciantePos: {
      get () {
        const d = this.f.denunciantes?.[0] || {}
        return { latitud: d.latitud, longitud: d.longitud }
      },
      set (v) {
        if (!this.f.denunciantes?.length) return
        this.f.denunciantes[0].latitud  = v?.latitud ?? v?.lat ?? null
        this.f.denunciantes[0].longitud = v?.longitud ?? v?.lng ?? null
      }
    }
  },
  mounted () {
    // usuarios por rol
    this.$axios.get('/usuariosRole').then(res => {
      this.psicologos = res.data.psicologos || []
      this.abogados   = res.data.abogados   || []
      this.sociales   = res.data.sociales   || []
    }).catch(() => this.$alert?.error?.('No se pudo cargar los usuarios por rol'))

    // speech to text (opcional)
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
      const SR = window.SpeechRecognition || window.webkitSpeechRecognition
      this.recognition = new SR()
      this.recognition.lang = 'es-ES'
      this.recognition.interimResults = false
      this.recognition.continuous = false
      this.recognition.onstart = () => { this.isListening = true }
      this.recognition.onend   = () => { this.isListening = false; this.activeField = null }
      this.recognition.onresult = (e) => {
        const text = e.results[0][0].transcript
        if (this.activeField === 'caso_descripcion') {
          this.f.caso_descripcion = (this.f.caso_descripcion ? this.f.caso_descripcion + ' ' : '') + text
        }
      }
    }

    // si es edición
    this.getCaso()
  },
  methods: {
    getCaso () {
      if (!this.editable || !this.casoId) return
      this.loading = true
      this.$axios.get(`/casos/${this.casoId}`)
        .then(r => {
          const data = r.data || {}
          // Asegurar arrays mínimas
          if (!data.denunciantes || !data.denunciantes.length) {
            data.denunciantes = [this.f.denunciantes[0]]
          }
          if (!data.denunciados || !data.denunciados.length) {
            data.denunciados = [this.f.denunciados[0]]
          }
          if (!data.familiares) data.familiares = []
          this.f = { ...this.f, ...data, tipo: 'UMADIS' }
        })
        .catch(() => this.$alert?.error?.('No se pudo cargar el caso'))
        .finally(() => { this.loading = false })
    },
    req (v) { return !!v || 'Requerido' },
    addFam () {
      this.f.familiares.push({
        familiar_nombres: '', familiar_paterno: '', familiar_materno: '',
        familiar_edad: null, familiar_parentesco: '', familiar_estado_civil: '',
        familiar_ocupacion: '', familiar_telefono: ''
      })
    },
    resetForm () {
      const keep = { tipo: 'UMADIS', zona: this.f.zona }
      this.f = {
        ...keep,
        principal: '', caso_numero: '', caso_fecha_hecho: '', caso_descripcion: '',
        denunciantes: [ { ...this.$options.data().f.denunciantes[0] } ],
        denunciados : [ { ...this.$options.data().f.denunciados[0] } ],
        familiares  : [],
        psicologica_user_id: null, trabajo_social_user_id: null, legal_user_id: null
      }
      this.$q.notify({ type: 'info', message: 'Formulario reiniciado' })
    },
    autoEdad (quien) {
      const key = quien === 'denunciado' ? 'denunciados' : 'denunciantes'
      const obj = this.f[key]?.[0]
      if (!obj?.[`${quien}_fecha_nacimiento`]) return
      const birth = new Date(obj[`${quien}_fecha_nacimiento`])
      const now   = new Date()
      const age = Math.floor((now - birth) / (1000 * 60 * 60 * 24 * 365.25))
      obj[`${quien}_edad`] = isFinite(age) ? age : ''
    },
    toggleRecognition (field) {
      if (!this.recognition) {
        this.$q.notify({ color: 'negative', message: 'El navegador no soporta micrófono.' })
        return
      }
      if (this.isListening && this.activeField === field) { try { this.recognition.stop() } catch(e){}; return }
      if (this.isListening) { try { this.recognition.stop() } catch(e){} }
      this.activeField = field
      try { this.recognition.start() } catch(e) {}
    },
    async update () {
      if (!this.f.principal) { this.$alert?.error?.('La tipología principal es obligatoria'); return }
      this.loading = true
      try {
        await this.$axios.put(`/casos/${this.casoId}`, this.f)
        this.$alert?.success?.('Caso UMADIS actualizado')
        this.$router.push(`/casos/${this.casoId}`)
      } catch (e) {
        this.$alert?.error?.(e?.response?.data?.message || 'No se pudo actualizar el caso')
      } finally { this.loading = false }
    },
    async save () {
      if (!this.f.principal || !this.f.denunciantes[0].denunciante_nombres) {
        this.$alert?.error?.('Tipología y nombre del denunciante son obligatorios')
        return
      }
      this.loading = true
      try {
        const res = await this.$axios.post('/casos', { ...this.f, tipo: 'UMADIS' })
        const id = res?.data?.id || res?.data?.caso?.id
        this.$alert?.success?.('Caso UMADIS creado')
        if (id) this.$router.push(`/casos/${id}`)
        else     this.$router.push('/casos?tipo=UMADIS')
      } catch (e) {
        this.$alert?.error?.(e?.response?.data?.message || 'No se pudo crear el caso UMADIS')
      } finally { this.loading = false }
    }
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
.toolbar   { position: sticky; top: 0; z-index: 5; border-radius: 12px; }
.section-card { border-radius: 14px; overflow: hidden; margin-bottom: 16px; background: #fff; }
</style>
