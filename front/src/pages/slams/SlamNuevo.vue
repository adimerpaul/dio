<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Toolbar -->
    <div class="toolbar q-pa-sm bg-white row items-center q-gutter-sm shadow-1">
      <div class="col">
        <div class="text-h6 text-weight-bold">Nuevo S.L.A.M.</div>
        <div class="text-caption text-grey-7">Registro de denuncia (Adulto Mayor)</div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </div>

    <q-form class="q-mt-lg" @submit.prevent="save">
      <!-- 1) Datos del Adulto Mayor -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="elderly" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Datos del Adulto Mayor</div>
          <q-space/>
          <q-badge color="blue-2" text-color="blue-10" outline>Obligatorio *</q-badge>
        </q-card-section>
        <q-separator/>

        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-input v-model="f.fecha_registro" type="date" dense outlined label="Fecha de registro"/>
            </div>
            <div class="col-12 col-md-3">
              <q-input v-model="f.numero_caso" dense outlined clearable label="Nº de caso"/>
            </div>

            <!-- Nombre del AM (usamos fila 1) -->
            <div class="col-12 col-md-3">
              <q-input v-model="f.am_nombres1" dense outlined clearable label="Nombres *" :rules="[req]"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.am_paterno1" dense outlined clearable label="Ap. paterno"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.am_materno1" dense outlined clearable label="Ap. materno"/>
            </div>

            <!-- Documentos A / B -->
            <div class="col-12 text-subtitle2 q-mt-md">Documentos de identidad</div>

            <div class="col-6 col-md-3">
              <q-input v-model="f.am_doc_tipo_a" dense outlined clearable label="Tipo (A)"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.am_doc_num_a" dense outlined clearable label="Nº (A)"/>
            </div>
            <div class="col-4 col-md-2"><q-input v-model.number="f.am_doc_dia_a" type="number" dense outlined label="Día (A)"/></div>
            <div class="col-4 col-md-2"><q-input v-model.number="f.am_doc_mes_a" type="number" dense outlined label="Mes (A)"/></div>
            <div class="col-4 col-md-2"><q-input v-model.number="f.am_doc_anio_a" type="number" dense outlined label="Año (A)"/></div>
            <div class="col-12 col-md-4"><q-input v-model="f.am_doc_lugar_a" dense outlined clearable label="Lugar (A)"/></div>

            <div class="col-6 col-md-3">
              <q-input v-model="f.am_doc_tipo_b" dense outlined clearable label="Tipo (B)"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.am_doc_num_b" dense outlined clearable label="Nº (B)"/>
            </div>
            <div class="col-4 col-md-2"><q-input v-model.number="f.am_doc_dia_b" type="number" dense outlined label="Día (B)"/></div>
            <div class="col-4 col-md-2"><q-input v-model.number="f.am_doc_mes_b" type="number" dense outlined label="Mes (B)"/></div>
            <div class="col-4 col-md-2"><q-input v-model.number="f.am_doc_anio_b" type="number" dense outlined label="Año (B)"/></div>
            <div class="col-12 col-md-4"><q-input v-model="f.am_doc_lugar_b" dense outlined clearable label="Lugar (B)"/></div>

            <div class="col-12 col-md-4"><q-input v-model="f.am_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/></div>
            <div class="col-6 col-md-2"><q-input v-model="f.am_edad" dense outlined clearable label="Edad"/></div>
            <div class="col-12 col-md-6"><q-input v-model="f.am_domicilio" dense outlined clearable label="Domicilio"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.am_estado_civil" dense outlined clearable label="Estado civil"/></div>

            <div class="col-6 col-md-3"><q-input v-model="f.am_ocupacion_1" dense outlined clearable label="Ocupación 1"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.am_ocupacion_2" dense outlined clearable label="Ocupación 2"/></div>
            <div class="col-12 col-md-3"><q-input v-model="f.am_ingresos" dense outlined clearable label="Ingresos (ej. Renta Dignidad)"/></div>

            <div class="col-12 text-subtitle2 q-mt-sm">Idiomas</div>
            <div class="col-6 col-md-2"><q-toggle v-model="f.am_idioma_castellano" label="Castellano"/></div>
            <div class="col-6 col-md-2"><q-toggle v-model="f.am_idioma_quechua" label="Quechua"/></div>
            <div class="col-6 col-md-2"><q-toggle v-model="f.am_idioma_aymara" label="Aymara"/></div>
            <div class="col-12 col-md-6"><q-input v-model="f.am_idioma_otros" dense outlined clearable label="Otros idiomas"/></div>

            <div class="col-12 text-subtitle2 q-mt-sm">Teléfonos de referencia</div>
            <div class="col-12 col-md-3"><q-input v-model="f.ref_tel_fijo" dense outlined clearable label="Nº fijo"/></div>
            <div class="col-12 col-md-3"><q-input v-model="f.ref_tel_movil" dense outlined clearable label="Nº móvil"/></div>
            <div class="col-12 col-md-3"><q-input v-model="f.ref_tel_movil_alt" dense outlined clearable label="Nº móvil (alt)"/></div>

            <div class="col-12 text-subtitle2 q-mt-sm">Ubicación (AM)</div>
            <div class="col-6 col-md-3"><q-input v-model.number="f.am_latitud" type="number" dense outlined label="Latitud"/></div>
            <div class="col-6 col-md-3"><q-input v-model.number="f.am_longitud" type="number" dense outlined label="Longitud"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 2) Grupo familiar rápido -->
      <q-card flat bordered class="section-card">
        <q-expansion-item icon="diversity_3" dense-toggle expand-separator
                          label="2) Grupo Familiar" header-class="text-subtitle1">
          <q-card>
            <q-card-section>
              <div v-for="i in 5" :key="i" class="q-mb-md">
                <div class="text-caption q-mb-xs">Familiar {{ i }}</div>
                <div class="row q-col-gutter-md">
                  <div class="col-12 col-md-5"><q-input v-model="f[`fam${i}_nombres`]" dense outlined clearable label="Nombres y apellidos"/></div>
                  <div class="col-6 col-md-2"><q-input v-model="f[`fam${i}_edad`]" dense outlined clearable label="Edad"/></div>
                  <div class="col-6 col-md-2"><q-input v-model="f[`fam${i}_parentesco`]" dense outlined clearable label="Parentesco"/></div>
                  <div class="col-6 col-md-2"><q-input v-model="f[`fam${i}_estado_civil`]" dense outlined clearable label="Estado civil"/></div>
                  <div class="col-6 col-md-3"><q-input v-model="f[`fam${i}_ocupacion`]" dense outlined clearable label="Ocupación"/></div>
                </div>
                <q-separator spaced/>
              </div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- 3) Denunciado -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="person_pin_circle" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">3) Datos del Denunciado</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4"><q-input v-model="f.den_nombres" dense outlined clearable label="Nombres y apellidos"/></div>
            <div class="col-6 col-md-2"><q-input v-model="f.den_paterno" dense outlined clearable label="Ap. paterno"/></div>
            <div class="col-6 col-md-2"><q-input v-model="f.den_materno" dense outlined clearable label="Ap. materno"/></div>
            <div class="col-6 col-md-2"><q-input v-model="f.den_edad" dense outlined clearable label="Edad"/></div>
            <div class="col-12 col-md-6"><q-input v-model="f.den_domicilio" dense outlined clearable label="Domicilio"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.den_estado_civil" dense outlined clearable label="Estado civil"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.den_idioma" dense outlined clearable label="Idioma"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.den_grado_instruccion" dense outlined clearable label="Grado de instrucción"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.den_ocupacion" dense outlined clearable label="Ocupación"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 4) Hechos y Tipología -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="gavel" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">4) Hechos y Tipología</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12">
              <q-input v-model="f.hecho_descripcion" type="textarea" autogrow outlined dense clearable
                       label="Breve circunstancia del hecho / denuncia" maxlength="3000"/>
            </div>
            <div class="col-6 col-md-3"><q-toggle v-model="f.tip_violencia_fisica" label="Violencia física"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="f.tip_violencia_psicologica" label="Violencia psicológica"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="f.tip_abandono" label="Abandono"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="f.tip_apoyo_integral" label="Apoyo integral"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 5) Seguimiento y áreas -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">5) Seguimiento</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-6 col-md-3"><q-toggle v-model="f.seg_trabajo_legal" label="Trabajo legal"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="f.seg_trabajo_social" label="Trabajo social"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="f.seg_psicologico" label="Psicológico"/></div>

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
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </q-form>
  </q-page>
</template>

<script>
export default {
  name: 'SlamNuevo',
  data () {
    return {
      loading: false,
      psicologos: [],
      sociales: [],
      abogados: [],
      f: {
        // encabezado
        fecha_registro: '',
        numero_caso: '',
        // AM
        am_latitud: null, am_longitud: null,
        am_nombres1: '', am_paterno1: '', am_materno1: '',
        am_doc_tipo_a: '', am_doc_num_a: '', am_doc_dia_a: null, am_doc_mes_a: null, am_doc_anio_a: null, am_doc_lugar_a: '',
        am_doc_tipo_b: '', am_doc_num_b: '', am_doc_dia_b: null, am_doc_mes_b: null, am_doc_anio_b: null, am_doc_lugar_b: '',
        am_lugar_nacimiento: '', am_edad: '', am_domicilio: '', am_estado_civil: '',
        am_ocupacion_1: '', am_ocupacion_2: '', am_ingresos: '',
        am_idioma_castellano: false, am_idioma_quechua: false, am_idioma_aymara: false, am_idioma_otros: '',
        ref_tel_fijo: '', ref_tel_movil: '', ref_tel_movil_alt: '',

        // familiares rápidos
        fam1_nombres: '', fam1_edad: '', fam1_parentesco: '', fam1_estado_civil: '', fam1_ocupacion: '',
        fam2_nombres: '', fam2_edad: '', fam2_parentesco: '', fam2_estado_civil: '', fam2_ocupacion: '',
        fam3_nombres: '', fam3_edad: '', fam3_parentesco: '', fam3_estado_civil: '', fam3_ocupacion: '',
        fam4_nombres: '', fam4_edad: '', fam4_parentesco: '', fam4_estado_civil: '', fam4_ocupacion: '',
        fam5_nombres: '', fam5_edad: '', fam5_parentesco: '', fam5_estado_civil: '', fam5_ocupacion: '',

        // denunciado
        den_nombres: '', den_paterno: '', den_materno: '', den_edad: '', den_domicilio: '', den_estado_civil: '',
        den_idioma: '', den_grado_instruccion: '', den_ocupacion: '',

        // hechos
        hecho_descripcion: '',

        // tipología
        tip_violencia_fisica: false,
        tip_violencia_psicologica: false,
        tip_abandono: false,
        tip_apoyo_integral: false,

        // seguimiento
        seg_trabajo_legal: false,
        seg_trabajo_social: false,
        seg_psicologico: false,
        psicologica_user_id: null,
        trabajo_social_user_id: null,
        legal_user_id: null,
      }
    }
  },
  mounted () {
    this.$axios.get('/usuariosRole').then(res => {
      this.psicologos = res.data.psicologos || []
      this.abogados  = res.data.abogados || []
      this.sociales  = res.data.sociales || []
    }).catch(() => this.$alert?.error('No se pudo cargar los usuarios por rol'))
  },
  methods: {
    req (v) { return !!v || 'Requerido' },
    resetForm () {
      const b = [
        'am_idioma_castellano','am_idioma_quechua','am_idioma_aymara',
        'tip_violencia_fisica','tip_violencia_psicologica','tip_abandono','tip_apoyo_integral',
        'seg_trabajo_legal','seg_trabajo_social','seg_psicologico'
      ]
      Object.keys(this.f).forEach(k => {
        if (b.includes(k)) this.f[k] = false
        else if (k.includes('lat') || k.includes('long')) this.f[k] = null
        else this.f[k] = ''
      })
    },
    async save () {
      if (!this.f.am_nombres1) {
        this.$alert?.error('El nombre del Adulto Mayor es obligatorio')
        return
      }
      this.loading = true
      try {
        const { data } = await this.$axios.post('/slams', this.f)
        this.$alert?.success('SLAM creado')
        this.$router.push(`/slams/${data.slam.id}`)
        this.resetForm()
      } catch (e) {
        this.$alert?.error(e?.response?.data?.message || 'No se pudo crear el SLAM')
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
