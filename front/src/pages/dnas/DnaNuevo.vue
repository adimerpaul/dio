<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Barra superior -->
    <div class="toolbar q-pa-sm bg-white row items-center q-gutter-sm shadow-1">
      <div class="col">
        <div class="text-h6 text-weight-bold">Nuevo caso DNA</div>
        <div class="text-caption text-grey-7">
          Registro de atención/denuncia ·
          <q-chip dense color="blue-1" text-color="blue-9">{{ tipoProcesoLabel }}</q-chip>
        </div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </div>

    <q-form class="q-mt-lg" @submit.prevent="save">
      <!-- Encabezado -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="assignment" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Datos generales</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-input v-model="f.fecha_atencion" type="date" dense outlined label="Fecha de atención"/>
            </div>
            <div class="col-12 col-md-3">
              <q-input v-model="f.principal" dense outlined clearable label="Principal (ej. 'Asistencia Familiar')"/>
            </div>
            <div class="col-12 col-md-3">
              <q-input
                v-model="f.codigo"
                dense outlined clearable
                :label="isApoyo ? 'Nro. Apoyo Integral' : 'Código interno'"
                :hint="isApoyo ? 'Obligatorio si tu flujo lo requiere' : 'Opcional'"
              />
            </div>
            <div class="col-12 col-md-3">
              <q-input v-model="f.zona" dense outlined clearable label="Zona"/>
            </div>

            <div class="col-12 col-md-8">
              <q-input v-model="f.domicilio" dense outlined clearable label="Domicilio"/>
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model="f.telefono" dense outlined clearable label="Teléfono"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Menores -->
      <q-card flat bordered class="section-card">
        <q-expansion-item icon="child_care" dense-toggle expand-separator
                          label="2) Datos del/los menor(es)" header-class="text-subtitle1">
          <q-card>
            <q-card-section>
              <div class="q-mb-sm">
                <q-btn dense icon="add" color="primary" label="Agregar menor" @click="addMenor"/>
              </div>

              <div v-for="(m, i) in f.menores" :key="i" class="row q-col-gutter-sm items-end q-mb-sm">
                <div class="col-12 col-md-4">
                  <q-input v-model="m.nombre" dense outlined label="Nombres y apellidos"/>
                </div>
                <div class="col-6 col-md-2">
                  <q-toggle v-model="m.gestante" label="Gestante"/>
                </div>
                <div class="col-6 col-md-2">
                  <q-input v-model.number="m.edad_anios" dense outlined type="number" label="Edad (años)"/>
                </div>
                <div class="col-6 col-md-2">
                  <q-input v-model.number="m.edad_meses" dense outlined type="number" label="Edad (meses)"/>
                </div>
                <div class="col-6 col-md-2">
                  <q-select v-model="m.sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/>
                </div>

                <div class="col-6 col-md-2">
                  <q-toggle v-model="m.cert_nac" label="C. Nac"/>
                </div>
                <div class="col-6 col-md-2">
                  <q-toggle v-model="m.estudia" label="Estudia"/>
                </div>
                <div class="col-6 col-md-3">
                  <q-input v-model="m.ultimo_curso" dense outlined label="Último curso"/>
                </div>
                <div class="col-6 col-md-3">
                  <q-input v-model="m.tipo_trabajo" dense outlined label="Tipo de trabajo"/>
                </div>
                <div class="col-12 col-md-2">
                  <q-btn icon="delete" color="negative" flat dense @click="f.menores.splice(i,1)"/>
                </div>
                <q-separator class="q-my-sm" v-if="i < f.menores.length-1"/>
              </div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- Grupo familiar -->
      <q-card flat bordered class="section-card">
        <q-expansion-item icon="diversity_3" dense-toggle expand-separator
                          label="3) Grupo familiar" header-class="text-subtitle1">
          <q-card>
            <q-card-section>
              <div class="q-mb-sm">
                <q-btn dense icon="add" color="primary" label="Agregar integrante" @click="addFam"/>
              </div>

              <div v-for="(g, i) in f.grupo_familiar" :key="'g'+i" class="row q-col-gutter-sm items-end q-mb-sm">
                <div class="col-12 col-md-4"><q-input v-model="g.nombre" dense outlined label="Nombres y apellidos"/></div>
                <div class="col-6 col-md-2"><q-input v-model="g.parentesco" dense outlined label="Parentesco"/></div>
                <div class="col-6 col-md-2"><q-input v-model.number="g.edad" type="number" dense outlined label="Edad"/></div>
                <div class="col-6 col-md-2"><q-select v-model="g.sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/></div>
                <div class="col-6 col-md-2"><q-input v-model="g.instruccion" dense outlined label="G. Instrucción"/></div>
                <div class="col-6 col-md-2"><q-input v-model="g.ocupacion" dense outlined label="Ocupación"/></div>
                <div class="col-12 col-md-2">
                  <q-btn icon="delete" color="negative" flat dense @click="f.grupo_familiar.splice(i,1)"/>
                </div>
                <q-separator class="q-my-sm" v-if="i < f.grupo_familiar.length-1"/>
              </div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- Denunciado -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="person_off" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">4) Datos del denunciado</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6"><q-input v-model="f.denunciado_nombre" dense outlined clearable label="Nombre completo"/></div>
            <div class="col-6 col-md-2"><q-select v-model="f.denunciado_sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/></div>
            <div class="col-6 col-md-2"><q-input v-model.number="f.denunciado_edad" type="number" dense outlined label="Edad"/></div>
            <div class="col-6 col-md-2"><q-input v-model="f.denunciado_relacion" dense outlined label="Relación / tipo"/></div>
            <div class="col-6 col-md-2"><q-input v-model="f.denunciado_ci" dense outlined clearable label="C.I."/></div>
            <div class="col-12 col-md-6"><q-input v-model="f.denunciado_domicilio" dense outlined clearable label="Domicilio"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciado_telefono" dense outlined clearable label="Teléfono"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciado_lugar_trabajo" dense outlined clearable label="Lugar de trabajo"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciado_ocupacion" dense outlined clearable label="Ocupación"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Denunciante -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="person" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">5) Datos del denunciante</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6"><q-input v-model="f.denunciante_nombre" dense outlined clearable label="Nombre completo *" :rules="[req]"/></div>
            <div class="col-6 col-md-2"><q-select v-model="f.denunciante_sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/></div>
            <div class="col-6 col-md-2"><q-input v-model.number="f.denunciante_edad" type="number" dense outlined label="Edad"/></div>
            <div class="col-6 col-md-2"><q-input v-model="f.denunciante_ci" dense outlined clearable label="C.I."/></div>
            <div class="col-12 col-md-6"><q-input v-model="f.denunciante_domicilio" dense outlined clearable label="Domicilio"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciante_telefono" dense outlined clearable label="Teléfono"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciante_lugar_trabajo" dense outlined clearable label="Lugar de trabajo"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciante_ocupacion" dense outlined clearable label="Ocupación"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Descripción -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="description" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">6) Descripción de la denuncia</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <q-input
            v-model="f.descripcion"
            type="textarea" autogrow outlined dense clearable
            label="Descripción"
            maxlength="5000" counter
          />
        </q-card-section>
      </q-card>

      <!-- Asignación -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="group" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">7) Asignación</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-select v-model="f.abogado_user_id" dense outlined emit-value map-options clearable
                        :options="abogados.map(u => ({ label: u.name, value: u.id }))"
                        label="Abogado/a asignado"/>
            </div>
            <div class="col-12 col-md-3">
              <q-chip outline color="grey-6" text-color="grey-10">Área: DNA</q-chip>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Acciones bottom -->
      <div class="text-right q-mb-lg">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm" class="q-mr-sm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </q-form>
  </q-page>
</template>

<script>
export default {
  name: 'DnaNuevo',
  props: {
    // 'PROCESO_PENAL' | 'PROCESO_FAMILIAR' | 'PROCESO_NNA' | 'APOYO_INTEGRAL'
    tipoProceso: { type: String, required: true }
  },
  data () {
    return {
      loading: false,
      abogados: [],
      sexos: [
        { label: 'Masculino', value: 'Masculino' },
        { label: 'Femenino',  value: 'Femenino'  },
        { label: 'Otro',      value: 'Otro'      },
      ],
      f: {
        // metadatos
        tipo_proceso: this.tipoProceso,
        fecha_atencion: '',
        principal: '',
        codigo: '',
        zona: this.$store?.user?.zona || '',
        area: 'DNA',

        // generales
        domicilio: '',
        telefono: '',

        // tablas
        menores: [ { nombre:'', gestante:false, edad_anios:null, edad_meses:null, sexo:'', cert_nac:false, estudia:false, ultimo_curso:'', tipo_trabajo:'' } ],
        grupo_familiar: [],

        // denunciado
        denunciado_nombre: '', denunciado_sexo: '', denunciado_edad: null,
        denunciado_relacion: '', denunciado_ci: '', denunciado_domicilio: '',
        denunciado_telefono: '', denunciado_lugar_trabajo: '', denunciado_ocupacion: '',

        // denunciante
        denunciante_nombre: '', denunciante_sexo: '', denunciante_edad: null,
        denunciante_ci: '', denunciante_domicilio: '', denunciante_telefono: '',
        denunciante_lugar_trabajo: '', denunciante_ocupacion: '',

        // desc
        descripcion: '',

        // asignación
        abogado_user_id: null,
      }
    }
  },
  computed: {
    isApoyo () { return (this.tipoProceso || '').toUpperCase() === 'APOYO_INTEGRAL' },
    tipoProcesoLabel () {
      const map = {
        PROCESO_PENAL: 'Procesos Penales',
        PROCESO_FAMILIAR: 'Procesos Familiares',
        PROCESO_NNA: 'Procesos Niñez y Adolescencia',
        APOYO_INTEGRAL: 'Apoyos Integrales'
      }
      return map[this.tipoProceso] || this.tipoProceso
    }
  },
  mounted () {
    // Si ya tienes este endpoint que devuelve listas por rol:
    this.$axios.get('/usuariosRole')
      .then(r => { this.abogados = r.data.abogados || [] })
      .catch(() => { this.$alert?.error?.('No se pudo cargar abogados') })
  },
  methods: {
    req (v) { return !!v || 'Requerido' },
    addMenor () {
      this.f.menores.push({ nombre:'', gestante:false, edad_anios:null, edad_meses:null, sexo:'', cert_nac:false, estudia:false, ultimo_curso:'', tipo_trabajo:'' })
    },
    addFam () {
      this.f.grupo_familiar.push({ nombre:'', parentesco:'', edad:null, sexo:'', instruccion:'', ocupacion:'' })
    },
    resetForm () {
      const keep = { tipo_proceso: this.tipoProceso, area: 'DNA', zona: this.$store?.user?.zona || '' }
      this.f = {
        ...keep,
        fecha_atencion: '',
        principal: '',
        codigo: '',
        domicilio: '',
        telefono: '',
        menores: [ { nombre:'', gestante:false, edad_anios:null, edad_meses:null, sexo:'', cert_nac:false, estudia:false, ultimo_curso:'', tipo_trabajo:'' } ],
        grupo_familiar: [],
        denunciado_nombre:'', denunciado_sexo:'', denunciado_edad:null, denunciado_relacion:'', denunciado_ci:'', denunciado_domicilio:'', denunciado_telefono:'', denunciado_lugar_trabajo:'', denunciado_ocupacion:'',
        denunciante_nombre:'', denunciante_sexo:'', denunciante_edad:null, denunciante_ci:'', denunciante_domicilio:'', denunciante_telefono:'', denunciante_lugar_trabajo:'', denunciante_ocupacion:'',
        descripcion:'',
        abogado_user_id:null
      }
      this.$q.notify({ type: 'info', message: 'Formulario reiniciado' })
    },
    async save () {
      if (!this.f.denunciante_nombre) {
        this.$alert?.error?.('El nombre del denunciante es obligatorio'); return
      }
      this.loading = true
      try {
        const { data } = await this.$axios.post('/dnas', this.f)
        this.$alert?.success?.('Caso DNA creado')
        this.$router.push('/dnas') // o `/dnas/${data.dna.id}` si luego haces el show
      } catch (e) {
        this.$alert?.error?.(e?.response?.data?.message || 'No se pudo crear el caso DNA')
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
