<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Toolbar -->
    <div class="toolbar q-pa-sm bg-white row items-center q-gutter-sm shadow-1">
      <div class="col">
        <div class="text-h6 text-weight-bold">Nuevo U.M.A.D.I.</div>
        <div class="text-caption text-grey-7">Registro del adulto mayor + denunciado + familiares</div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </div>

    <q-form class="q-mt-lg" @submit.prevent="save">
      <!-- 1) Datos del adulto mayor / denunciante -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="badge" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Datos del adulto mayor / denunciante</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
<!--            <div class="col-12 col-md-2"><q-input v-model="umadi.area" dense outlined label="Área"/></div>-->
<!--            <div class="col-12 col-md-2"><q-input v-model="umadi.zona" dense outlined label="Zona"/></div>-->
            <div class="col-12 col-md-3" v-if="showNumeroApoyoIntegral">
              <q-input v-model="umadi.numero_apoyo_integral" dense outlined label="Nº Apoyo Integral"/>
            </div>
            <div class="col-12 col-md-3"><q-input v-model="umadi.numero_caso" dense outlined label="Nº de caso"/></div>
            <div class="col-12 col-md-2"><q-input v-model="umadi.fecha_registro" type="date" dense outlined label="Fecha registro"/></div>

            <div class="col-12 text-subtitle2 q-mt-sm">Identificación</div>
            <div class="col-12 col-md-3"><q-input v-model="umadi.nombres" dense outlined label="Nombres"/></div>
            <div class="col-6 col-md-2"><q-input v-model="umadi.paterno" dense outlined label="Paterno"/></div>
            <div class="col-6 col-md-2"><q-input v-model="umadi.materno" dense outlined label="Materno"/></div>
            <div class="col-6 col-md-2"><q-select v-model="umadi.tipo_documento" :options="documentos" emit-value map-options dense outlined label="Documento"/></div>
            <div class="col-6 col-md-3"><q-input v-model="umadi.numero_documento" dense outlined label="Nº documento"/></div>

            <div class="col-6 col-md-2"><q-select v-model="umadi.sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/></div>
            <div class="col-6 col-md-3"><q-input v-model="umadi.lugar_nacimiento" dense outlined label="Lugar de nacimiento"/></div>
            <div class="col-6 col-md-3"><q-input v-model="umadi.fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento" @update:model-value="calcEdad"/></div>
            <div class="col-6 col-md-2"><q-input v-model="umadi.edad" dense outlined label="Edad"/></div>

            <div class="col-12 col-md-6"><q-input v-model="umadi.direccion" dense outlined label="Dirección"/></div>
            <div class="col-6 col-md-3"><q-input v-model="umadi.estado_civil" dense outlined label="Estado civil"/></div>
            <div class="col-6 col-md-3"><q-input v-model="umadi.relacion_denunciado" dense outlined label="Relación con denunciado"/></div>

            <div class="col-6 col-md-3"><q-input v-model="umadi.grado_instruccion" dense outlined label="Grado de instrucción"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="umadi.trabaja" label="Trabaja"/></div>
            <div class="col-6 col-md-3"><q-input v-model="umadi.ocupacion" dense outlined label="Ocupación"/></div>

            <div class="col-12 text-subtitle2 q-mt-sm">Otros datos</div>
            <div class="col-6 col-md-2"><q-input v-model="umadi.edad_aprox" dense outlined label="Edad aprox."/></div>
            <div class="col-6 col-md-2"><q-input v-model="umadi.edada_exacto" dense outlined label="Edad exacta"/></div>
            <div class="col-6 col-md-3"><q-input v-model="umadi.idioma" dense outlined label="Idioma"/></div>

            <div class="col-12 text-subtitle2 q-mt-sm">Teléfonos</div>
            <div class="col-6 col-md-3"><q-input v-model="umadi.celular1" dense outlined label="Celular 1"/></div>
            <div class="col-6 col-md-3"><q-input v-model="umadi.celular2" dense outlined label="Celular 2"/></div>
            <div class="col-6 col-md-3"><q-input v-model="umadi.telefono_fijo1" dense outlined label="Teléfono fijo 1"/></div>
            <div class="col-6 col-md-3"><q-input v-model="umadi.telefono_fijo2" dense outlined label="Teléfono fijo 2"/></div>

            <div class="col-12 col-md-9"><q-input v-model="umadi.direccion_actual" dense outlined label="Dirección actual"/></div>
            <div class="col-12 text-subtitle2 q-mt-sm">Ubicación (lat/lng)</div>
            <div class="col-12">
              <MapPicker v-model="mapModel" :center="defaultCenter" :zoom-init="13"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 2) Familiares (N) -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="diversity_3" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">2) Familiares (N)</div>
          <q-space/>
          <q-btn dense color="primary" flat icon="add" label="Agregar familiar" @click="addFamiliar"/>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div v-if="familiares.length === 0" class="text-grey-7 q-mb-md">
            No hay familiares agregados. Usa “Agregar familiar”.
          </div>

          <div v-for="(f, idx) in familiares" :key="idx" class="q-mb-md">
            <div class="row q-col-gutter-md items-center">
              <div class="col-12 col-md-4"><q-input v-model="f.nombre" dense outlined label="Nombres *"/></div>
              <div class="col-6  col-md-2"><q-input v-model="f.paterno" dense outlined label="Paterno"/></div>
              <div class="col-6  col-md-2"><q-input v-model="f.materno" dense outlined label="Materno"/></div>
              <div class="col-6  col-md-2"><q-input v-model="f.parentesco" dense outlined label="Parentesco"/></div>
              <div class="col-3  col-md-1"><q-input v-model.number="f.edad" type="number" dense outlined label="Edad"/></div>
              <div class="col-3  col-md-1"><q-input v-model="f.sexo" dense outlined label="Sexo"/></div>
              <div class="col-12 col-md-3"><q-input v-model="f.telefono" dense outlined label="Teléfono"/></div>

              <div class="col-auto q-mt-sm">
                <q-btn dense round flat color="negative" icon="delete" @click="removeFamiliar(idx)"/>
              </div>
            </div>
            <q-separator spaced/>
          </div>
        </q-card-section>
      </q-card>

      <!-- 3) Datos del denunciado -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="person_off" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">3) Datos del denunciado</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4"><q-input v-model="umadi.denunciado_nombres" dense outlined label="Nombres"/></div>
            <div class="col-6  col-md-2"><q-input v-model="umadi.denunciado_paterno" dense outlined label="Paterno"/></div>
            <div class="col-6  col-md-2"><q-input v-model="umadi.denunciado_materno" dense outlined label="Materno"/></div>
            <div class="col-6  col-md-2"><q-input v-model="umadi.denunciado_edad" dense outlined label="Edad"/></div>
            <div class="col-6  col-md-2"><q-input v-model="umadi.denunciado_ci" dense outlined label="CI"/></div>

            <div class="col-6  col-md-2"><q-input v-model="umadi.denunciado_ciudad_nacimiento" dense outlined label="Ciudad nac."/></div>
            <div class="col-6  col-md-2"><q-select v-model="umadi.denunciado_sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/></div>
            <div class="col-6  col-md-3"><q-input v-model="umadi.denunciado_lugar_nacimiento" dense outlined label="Lugar nac."/></div>
            <div class="col-6  col-md-3"><q-input v-model="umadi.denunciado_fecha_nacimiento" type="date" dense outlined label="Fecha nac." @update:model-value="calcEdadDenunciado"/></div>

            <div class="col-12 col-md-6"><q-input v-model="umadi.denunciado_direccion" dense outlined label="Dirección"/></div>
            <div class="col-6  col-md-3"><q-input v-model="umadi.denunciado_estado_civil" dense outlined label="Estado civil"/></div>
            <div class="col-6  col-md-3"><q-input v-model="umadi.denunciado_idioma" dense outlined label="Idioma"/></div>
            <div class="col-6  col-md-3"><q-input v-model="umadi.denunciado_grado_instruccion" dense outlined label="Grado de instrucción"/></div>
            <div class="col-6  col-md-3"><q-input v-model="umadi.denunciado_ocupacion" dense outlined label="Ocupación"/></div>

            <div class="col-6  col-md-3"><q-input v-model="umadi.denunciado_celular1" dense outlined label="Celular 1"/></div>
            <div class="col-6  col-md-3"><q-input v-model="umadi.denunciado_celular2" dense outlined label="Celular 2"/></div>
            <div class="col-6  col-md-3"><q-input v-model="umadi.denunciado_telefono_fijo1" dense outlined label="Teléfono fijo 1"/></div>
            <div class="col-6  col-md-3"><q-input v-model="umadi.denunciado_telefono_fijo2" dense outlined label="Teléfono fijo 2"/></div>
            <div class="col-12 col-md-6"><q-input v-model="umadi.denunciado_direccion_actual" dense outlined label="Dirección actual"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 4) Datos del hecho / denuncia -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="description" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">4) Datos del hecho / denuncia</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-6 col-md-3"><q-input v-model="umadi.fecha_hecho" type="date" dense outlined label="Fecha del hecho"/></div>
            <div class="col-6 col-md-4"><q-input v-model="umadi.relacion_denunciante" dense outlined label="Relación con denunciante"/></div>
            <div class="col-12 col-md-5"><q-input v-model="umadi.direccion_hecho" dense outlined label="Dirección del hecho"/></div>

            <div class="col-12">
              <q-input v-model="umadi.descripcion_hecho" type="textarea" autogrow outlined dense
                       label="Breve circunstancia del hecho / denuncia" maxlength="4000"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 5) Seguimiento / responsables -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">5) Seguimiento / responsables</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-select v-model="umadi.psicologica_user_id" dense outlined emit-value map-options clearable
                        :options="psicologos.map(u => ({ label: u.name, value: u.id }))"
                        label="Psicológica (responsable)"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="umadi.trabajo_social_user_id" dense outlined emit-value map-options clearable
                        :options="sociales.map(u => ({ label: u.name, value: u.id }))"
                        label="Trabajo social (responsable)"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="umadi.legal_user_id" dense outlined emit-value map-options clearable
                        :options="abogados.map(u => ({ label: u.name, value: u.id }))"
                        label="Legal (responsable)"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 6) Check documentos -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="inventory_2" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">6) Check documentos</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-2"><q-checkbox v-model="umadi.doc_ci" label="Fotocopia CI denunciante"/></div>
            <div class="col-12 col-md-2"><q-checkbox v-model="umadi.doc_frontal_denunciado" label="Foto frontal denunciado"/></div>
            <div class="col-12 col-md-2"><q-checkbox v-model="umadi.doc_frontal_denunciante" label="Foto frontal denunciante"/></div>
            <div class="col-12 col-md-2"><q-checkbox v-model="umadi.doc_croquis" label="Croquis del hecho"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Acciones bottom -->
      <div class="text-right q-mb-xl">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm" class="q-mr-sm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </q-form>
  </q-page>
</template>

<script>
import MapPicker from 'components/MapPicker.vue'

export default {
  name: 'NuevoUmadi',
  props: { showNumeroApoyoIntegral: { type: Boolean, default: true } },
  components: { MapPicker },
  data () {
    return {
      loading: false,
      defaultCenter: [-17.9667, -67.1167], // Oruro aprox
      // catálogo simple
      documentos: [
        { label: 'Carnet de identidad', value: 'CI' },
        { label: 'Pasaporte', value: 'Pasaporte' },
        { label: 'Libreta militar', value: 'Libreta militar' },
        { label: 'Licencia de conducir', value: 'Licencia' }
      ],
      sexos: [
        { label: 'Masculino', value: 'Masculino' },
        { label: 'Femenino',  value: 'Femenino'  },
        { label: 'Otro',      value: 'Otro'      }
      ],

      // responsables
      psicologos: [],
      abogados: [],
      sociales: [],

      // ----- UMADI (cabecera) -----
      umadi: {
        area: '', zona: '', fecha_registro: '', numero_apoyo_integral: '', numero_caso: '',
        latitud: null, longitud: null,
        nombres: '', paterno: '', materno: '', tipo_documento: '', numero_documento: '',
        sexo: '', lugar_nacimiento: '', fecha_nacimiento: '', edad: '',
        direccion: '', estado_civil: '', relacion_denunciado: '', grado_instruccion: '',
        trabaja: false, ocupacion: '', edad_aprox: '', edada_exacto: '', idioma: '',
        celular1: '', celular2: '', telefono_fijo1: '', telefono_fijo2: '', direccion_actual: '',

        denunciado_nombres: '', denunciado_paterno: '', denunciado_materno: '',
        denunciado_ci: '', denunciado_ciudad_nacimiento: '', denunciado_sexo: '',
        denunciado_lugar_nacimiento: '', denunciado_fecha_nacimiento: '', denunciado_edad: '',
        denunciado_direccion: '', denunciado_estado_civil: '', denunciado_idioma: '',
        denunciado_grado_instruccion: '', denunciado_ocupacion: '',
        denunciado_celular1: '', denunciado_celular2: '', denunciado_telefono_fijo1: '', denunciado_telefono_fijo2: '',
        denunciado_direccion_actual: '',

        fecha_hecho: '', relacion_denunciante: '', direccion_hecho: '', descripcion_hecho: '',

        doc_ci: false, doc_frontal_denunciado: false, doc_frontal_denunciante: false, doc_croquis: false,

        psicologica_user_id: null, trabajo_social_user_id: null, legal_user_id: null
      },

      // ----- familiares dinámicos -----
      familiares: []
    }
  },
  computed: {
    mapModel: {
      get () { return { latitud: this.umadi.latitud, longitud: this.umadi.longitud } },
      set (v) { this.umadi.latitud = v?.latitud ?? v?.lat ?? null; this.umadi.longitud = v?.longitud ?? v?.lng ?? null }
    }
  },
  mounted() {
    // cargar responsables
    this.$axios.get('/usuariosRole')
      .then(res => {
        this.psicologos = res.data.psicologos || []
        this.abogados  = res.data.abogados  || []
        this.sociales  = res.data.sociales  || []
      })
      .catch(() => this.$q.notify({ type:'negative', message:'No se pudo cargar los usuarios por rol' }))
  },
  methods: {
    calcEdad () {
      const v = this.umadi.fecha_nacimiento
      if (!v) { this.umadi.edad = ''; return }
      const birth = new Date(v)
      const diff  = Date.now() - birth.getTime()
      const age   = Math.abs(new Date(diff).getUTCFullYear() - 1970)
      this.umadi.edad = String(age)
    },
    calcEdadDenunciado () {
      const v = this.umadi.denunciado_fecha_nacimiento
      if (!v) { this.umadi.denunciado_edad = ''; return }
      const birth = new Date(v)
      const diff  = Date.now() - birth.getTime()
      const age   = Math.abs(new Date(diff).getUTCFullYear() - 1970)
      this.umadi.denunciado_edad = String(age)
    },
    addFamiliar () {
      this.familiares.push({ nombre:'', paterno:'', materno:'', parentesco:'', edad:null, sexo:'', telefono:'' })
    },
    removeFamiliar (i) { this.familiares.splice(i, 1) },
    resetForm () {
      const bools = ['trabaja', 'doc_ci','doc_frontal_denunciado','doc_frontal_denunciante','doc_croquis']
      Object.keys(this.umadi).forEach(k => {
        if (bools.includes(k)) this.umadi[k] = false
        else if (['latitud','longitud'].includes(k)) this.umadi[k] = null
        else this.umadi[k] = ''
      })
      this.umadi.psicologica_user_id = this.umadi.trabajo_social_user_id = this.umadi.legal_user_id = null
      this.familiares = []
    },
    async save () {
      // simple: permitimos guardar incluso sin familiares
      this.loading = true
      try {
        const payload = { umadi: this.umadi, familiares: this.familiares.filter(f => f?.nombre?.trim()) }
        const { data } = await this.$axios.post('/umadis', payload)
        this.$q.notify({ type: 'positive', message: 'UMADI creado' })
        this.$router.push(`/umadis/${data.umadi.id}`)
        this.resetForm()
      } catch (e) {
        this.$q.notify({ type: 'negative', message: e?.response?.data?.message || 'No se pudo crear el UMADI' })
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
.toolbar { position: sticky; top: 50px; z-index: 500; border-radius: 12px; }
.section-card { border-radius: 14px; overflow: hidden; margin-bottom: 16px; background: #fff; }
</style>
