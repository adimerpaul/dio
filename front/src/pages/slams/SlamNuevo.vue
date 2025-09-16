<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Toolbar -->
    <div class="toolbar q-pa-sm bg-white row items-center q-gutter-sm shadow-1">
      <div class="col">
        <div class="text-h6 text-weight-bold">Nuevo S.L.A.M.</div>
        <div class="text-caption text-grey-7">Registro con múltiples Adultos y Familiares</div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </div>

    <q-form class="q-mt-lg" @submit.prevent="save">
      <!-- 1) Datos del caso (Slam) -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="description" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Datos del caso (S.L.A.M.)</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3" v-if="showNumeroApoyoIntegral">
              <q-input v-model="slam.numero_apoyo_integral" dense outlined clearable
                       label="Nº Apoyo Integral" hint="Requerido si aplica"/>
            </div>
            <div class="col-12 col-md-3">
              <q-input v-model="slam.numero_caso" dense outlined clearable label="Nº de caso"/>
            </div>

            <div class="col-12 text-subtitle2 q-mt-md">Idiomas (AM - marcadores generales)</div>
            <div class="col-6 col-md-2"><q-toggle v-model="slam.am_idioma_castellano" label="Castellano"/></div>
            <div class="col-6 col-md-2"><q-toggle v-model="slam.am_idioma_quechua" label="Quechua"/></div>
            <div class="col-6 col-md-2"><q-toggle v-model="slam.am_idioma_aymara" label="Aymara"/></div>
            <div class="col-12 col-md-6">
              <q-input v-model="slam.am_idioma_otros" dense outlined clearable label="Otros idiomas"/>
            </div>

            <div class="col-12 text-subtitle2 q-mt-sm">Teléfonos de referencia</div>
            <div class="col-12 col-md-4"><q-input v-model="slam.ref_tel_fijo" dense outlined label="Tel. fijo"/></div>
            <div class="col-12 col-md-4"><q-input v-model="slam.ref_tel_movil" dense outlined label="Tel. móvil"/></div>
            <div class="col-12 col-md-4"><q-input v-model="slam.ref_tel_movil_alt" dense outlined label="Tel. móvil (alt)"/></div>

            <div class="col-12 text-subtitle2 q-mt-sm">Ubicación (lat/lng)</div>
            <div class="col-12">
              <!-- Tu componente de mapa: pega la ruta correcta del archivo que me mandaste -->
              <MapPicker v-model="mapModel" :center="defaultCenter" :zoom-init="13"/>
            </div>

            <div class="col-6 col-md-3">
              <q-input v-model.number="slam.am_latitud" dense outlined label="Latitud" />
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model.number="slam.am_longitud" dense outlined label="Longitud" />
            </div>

            <div class="col-12 text-subtitle2 q-mt-sm">Hecho y Tipología</div>
            <div class="col-12">
              <q-input v-model="slam.hecho_descripcion" type="textarea" autogrow outlined dense clearable
                       label="Breve circunstancia del hecho / denuncia" maxlength="3000"/>
            </div>
            <div class="col-6 col-md-3"><q-toggle v-model="slam.tip_violencia_fisica" label="Violencia física"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="slam.tip_violencia_psicologica" label="Violencia psicológica"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="slam.tip_abandono" label="Abandono"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="slam.tip_apoyo_integral" label="Apoyo integral"/></div>

            <div class="col-6 col-md-3"><q-toggle v-model="slam.seg_trabajo_legal" label="Trabajo legal"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="slam.seg_trabajo_social" label="Trabajo social"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="slam.seg_psicologico" label="Psicológico"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 2) Adultos (N) -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="elderly" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">2) Adultos (N)</div>
          <q-space/>
          <q-btn dense color="primary" flat icon="add" label="Agregar adulto" @click="addAdulto"/>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div v-if="adultos.length === 0" class="text-grey-7 q-mb-md">
            No hay adultos agregados. Usa “Agregar adulto”.
          </div>
          <div v-for="(a, idx) in adultos" :key="idx" class="q-mb-md">
            <div class="row q-col-gutter-md items-center">
              <div class="col-12 col-md-4">
                <q-input v-model="a.nombre" dense outlined clearable :rules="[req]"
                         label="Nombres *"/>
              </div>
              <div class="col-6 col-md-2"><q-input v-model="a.paterno" dense outlined clearable label="Paterno"/></div>
              <div class="col-6 col-md-2"><q-input v-model="a.materno" dense outlined clearable label="Materno"/></div>

              <div class="col-6 col-md-2"><q-input v-model="a.documento_tipo" dense outlined clearable label="Doc. tipo"/></div>
              <div class="col-6 col-md-2"><q-input v-model="a.documento_num" dense outlined clearable label="Doc. Nº"/></div>

              <div class="col-6 col-md-3"><q-input v-model="a.fecha_nacimiento" type="date" dense outlined label="Fecha nac."/></div>
              <div class="col-6 col-md-3"><q-input v-model="a.lugar_nacimiento" dense outlined clearable label="Lugar nac."/></div>
              <div class="col-6 col-md-2"><q-input v-model="a.edad" dense outlined clearable label="Edad"/></div>
              <div class="col-12 col-md-4"><q-input v-model="a.domicilio" dense outlined clearable label="Domicilio"/></div>
              <div class="col-6 col-md-3"><q-input v-model="a.estado_civil" dense outlined clearable label="Estado civil"/></div>

              <div class="col-6 col-md-3"><q-input v-model="a.ocupacion_1" dense outlined clearable label="Ocupación 1"/></div>
              <div class="col-6 col-md-3"><q-input v-model="a.ocupacion_2" dense outlined clearable label="Ocupación 2"/></div>
              <div class="col-12 col-md-3"><q-input v-model="a.ingresos" dense outlined clearable label="Ingresos"/></div>

              <div class="col-auto q-mt-sm">
                <q-btn dense round flat color="negative" icon="delete" @click="removeAdulto(idx)"/>
              </div>
            </div>
            <q-separator spaced/>
          </div>
        </q-card-section>
      </q-card>

      <!-- 3) Familiares (N) -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="diversity_3" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">3) Familiares (N)</div>
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
              <div class="col-12 col-md-4"><q-input v-model="f.nombre" dense outlined clearable label="Nombres *"/></div>
              <div class="col-6 col-md-2"><q-input v-model="f.paterno" dense outlined clearable label="Paterno"/></div>
              <div class="col-6 col-md-2"><q-input v-model="f.materno" dense outlined clearable label="Materno"/></div>
              <div class="col-6 col-md-2"><q-input v-model="f.parentesco" dense outlined clearable label="Parentesco"/></div>
              <div class="col-3 col-md-1"><q-input v-model.number="f.edad" type="number" dense outlined label="Edad"/></div>
              <div class="col-3 col-md-1"><q-input v-model="f.sexo" dense outlined clearable label="Sexo"/></div>
              <div class="col-12 col-md-2"><q-input v-model="f.telefono" dense outlined clearable label="Teléfono"/></div>

              <div class="col-auto q-mt-sm">
                <q-btn dense round flat color="negative" icon="delete" @click="removeFamiliar(idx)"/>
              </div>
            </div>
            <q-separator spaced/>
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
import MapPicker from 'components/MapPicker.vue' // <-- usa el nombre/ruta de TU componente de mapa

export default {
  name: 'SlamNuevoDynamic',
  components: { MapPicker },
  props: { showNumeroApoyoIntegral: { type: Boolean, default: false } },
  data () {
    return {
      loading: false,
      defaultCenter: [-17.9667, -67.1167], // Oruro (ajústalo)
      // ----- SLAM (cabecera) -----
      slam: {
        numero_apoyo_integral: '',
        numero_caso: '',
        am_latitud: null, am_longitud: null,
        am_idioma_castellano: false, am_idioma_quechua: false, am_idioma_aymara: false, am_idioma_otros: '',
        ref_tel_fijo: '', ref_tel_movil: '', ref_tel_movil_alt: '',
        hecho_descripcion: '',
        tip_violencia_fisica: false, tip_violencia_psicologica: false, tip_abandono: false, tip_apoyo_integral: false,
        seg_trabajo_legal: false, seg_trabajo_social: false, seg_psicologico: false,
        psicologica_user_id: null, trabajo_social_user_id: null, legal_user_id: null,
      },
      // ----- adultos y familiares dinámicos -----
      adultos: [],
      familiares: [],
    }
  },
  computed: {
    // Bridge para el v-model del mapa (tu componente emite/recibe {latitud, longitud})
    mapModel: {
      get () { return { latitud: this.slam.am_latitud, longitud: this.slam.am_longitud } },
      set (v) { this.slam.am_latitud = v?.latitud ?? v?.lat ?? null; this.slam.am_longitud = v?.longitud ?? v?.lng ?? null }
    }
  },
  methods: {
    req (v) { return !!v || 'Requerido' },
    addAdulto () {
      this.adultos.push({
        nombre: '', paterno: '', materno: '',
        documento_tipo: '', documento_num: '',
        fecha_nacimiento: '', lugar_nacimiento: '',
        edad: '', domicilio: '', estado_civil: '',
        ocupacion_1: '', ocupacion_2: '', ingresos: '',
      })
    },
    removeAdulto (i) { this.adultos.splice(i, 1) },
    addFamiliar () {
      this.familiares.push({ nombre:'', paterno:'', materno:'', parentesco:'', edad:null, sexo:'', telefono:'' })
    },
    removeFamiliar (i) { this.familiares.splice(i, 1) },
    resetForm () {
      const bools = ['am_idioma_castellano','am_idioma_quechua','am_idioma_aymara','tip_violencia_fisica','tip_violencia_psicologica','tip_abandono','tip_apoyo_integral','seg_trabajo_legal','seg_trabajo_social','seg_psicologico']
      Object.keys(this.slam).forEach(k => {
        if (bools.includes(k)) this.slam[k] = false
        else if (k.includes('lat') || k.includes('long')) this.slam[k] = null
        else this.slam[k] = ''
      })
      this.slam.psicologica_user_id = this.slam.trabajo_social_user_id = this.slam.legal_user_id = null
      this.adultos = []
      this.familiares = []
    },
    async save () {
      // Validación mínima: al menos un adulto con nombre
      const firstAdult = this.adultos[0]
      if (!firstAdult || !firstAdult.nombre) {
        this.$q.notify({ type: 'negative', message: 'Debe agregar al menos un Adulto con nombres.' })
        return
      }
      // Limpiar arrays: quitar filas vacías
      const adultos = this.adultos.filter(a => (a && (a.nombre?.trim()?.length)))
      const familiares = this.familiares.filter(f => (f && (f.nombre?.trim()?.length)))

      this.loading = true
      try {
        const payload = { slam: this.slam, adultos, familiares }
        const { data } = await this.$axios.post('/slams', payload)
        this.$q.notify({ type: 'positive', message: 'SLAM creado' })
        this.$router.push(`/slams/${data.slam.id}`)
        this.resetForm()
      } catch (e) {
        this.$q.notify({ type: 'negative', message: e?.response?.data?.message || 'No se pudo crear el SLAM' })
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
.toolbar { position: sticky; top: 0; z-index: 5; border-radius: 12px; }
.section-card { border-radius: 14px; overflow: hidden; margin-bottom: 16px; background: #fff; }
</style>
