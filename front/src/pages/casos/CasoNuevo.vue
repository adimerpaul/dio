<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-mb-md">
      <div class="col">
        <div class="text-h6 text-weight-bold">Nuevo Caso</div>
        <div class="text-caption text-grey-7">Registro de denuncia (SLIM)</div>
      </div>
      <div class="col-auto">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm" class="q-mr-sm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </div>

    <q-form @submit.prevent="save">

      <!-- Denunciante -->
      <q-card flat bordered class="q-mb-md">
        <q-card-section class="text-subtitle1 text-weight-medium">1) Datos del Denunciante</q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input v-model="f.denunciante_nombre_completo" dense outlined label="Nombre completo *" :rules="[req]"/>
            </div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciante_documento" dense outlined label="Documento"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciante_nro" dense outlined label="Nro documento"/></div>

            <div class="col-6 col-md-3"><q-input v-model="f.denunciante_sexo" dense outlined label="Sexo"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciante_estado_civil" dense outlined label="Estado civil"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciante_residencia" dense outlined label="Residencia"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciante_idioma" dense outlined label="Idioma"/></div>

            <div class="col-6 col-md-3"><q-toggle v-model="f.denunciante_trabaja" label="Trabaja"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciante_ocupacion" dense outlined label="Ocupación"/></div>
            <div class="col-12"><q-input v-model="f.denunciante_domicilio_actual" dense outlined label="Domicilio actual"/></div>

            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (denunciante)</div>
              <MapPicker v-model="denunciantePos" :center="oruroCenter"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Familiar simple -->
      <q-card flat bordered class="q-mb-md">
        <q-card-section class="text-subtitle1 text-weight-medium">2) Grupo Familiar</q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6"><q-input v-model="f.familiar1_nombre_completo" dense outlined label="Familiar 1: Nombres y apellidos"/></div>
            <div class="col-6 col-md-2"><q-input v-model.number="f.familiar1_edad" dense outlined type="number" label="Edad"/></div>
            <div class="col-6 col-md-2"><q-input v-model="f.familiar1_parentesco" dense outlined label="Parentesco"/></div>
            <div class="col-12 col-md-2"><q-input v-model="f.familiar1_celular" dense outlined label="Celular"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Denunciado -->
      <q-card flat bordered class="q-mb-md">
        <q-card-section class="text-subtitle1 text-weight-medium">3) Datos del Denunciado</q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6"><q-input v-model="f.denunciado_nombre_completo" dense outlined label="Nombre completo"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciado_documento" dense outlined label="Documento"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciado_nro" dense outlined label="Nro documento"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciado_sexo" dense outlined label="Sexo"/></div>
            <div class="col-6 col-md-3"><q-input v-model="f.denunciado_residencia" dense outlined label="Residencia"/></div>
            <div class="col-12"><q-input v-model="f.denunciado_domicilio_actual" dense outlined label="Domicilio actual"/></div>

            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (denunciado)</div>
              <MapPicker v-model="denunciadoPos" :center="oruroCenter"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Hechos y Tipología -->
      <q-card flat bordered class="q-mb-md">
        <q-card-section class="text-subtitle1 text-weight-medium">4) Hechos y Tipología</q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3"><q-input v-model="f.caso_numero" dense outlined label="Nro de caso"/></div>
            <div class="col-12 col-md-3"><q-input v-model="f.caso_fecha_hecho" dense outlined label="Fecha del hecho" type="date"/></div>
            <div class="col-12 col-md-6"><q-input v-model="f.caso_lugar_hecho" dense outlined label="Lugar del hecho"/></div>
            <div class="col-12 col-md-4"><q-input v-model="f.caso_tipologia" dense outlined label="Tipología"/></div>
            <div class="col-12 col-md-4"><q-input v-model="f.caso_modalidad" dense outlined label="Modalidad"/></div>
            <div class="col-12"><q-input v-model="f.caso_descripcion" type="textarea" autogrow outlined dense label="Descripción del hecho"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Violencias -->
      <q-card flat bordered class="q-mb-md">
        <q-card-section class="text-subtitle1 text-weight-medium">5) Tipos de violencia</q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-6 col-md-3"><q-toggle v-model="f.violencia_fisica" label="Física"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="f.violencia_psicologica" label="Psicológica"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="f.violencia_sexual" label="Sexual"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="f.violencia_economica" label="Económica/Patrimonial"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Seguimiento -->
      <q-card flat bordered class="q-mb-lg">
        <q-card-section class="text-subtitle1 text-weight-medium">6) Seguimiento</q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4"><q-input v-model="f.seguimiento_area" dense outlined label="Área"/></div>
            <div class="col-12 col-md-4"><q-input v-model="f.seguimiento_area_social" dense outlined label="Área social (responsable)"/></div>
            <div class="col-12 col-md-4"><q-input v-model="f.seguimiento_area_legal" dense outlined label="Área legal (responsable)"/></div>
          </div>
        </q-card-section>
      </q-card>

      <div class="q-mt-md text-right">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm" class="q-mr-sm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </q-form>
  </q-page>
</template>

<script>
import MapPicker from 'components/MapPicker.vue'

export default {
  name: 'CasoNuevo',
  components: { MapPicker },
  data () {
    return {
      loading: false,
      oruroCenter: [-17.9667, -67.1167],
      f: {
        // Denunciante
        denunciante_nombre_completo: '',
        denunciante_documento: '',
        denunciante_nro: '',
        denunciante_sexo: '',
        denunciante_residencia: '',
        denunciante_estado_civil: '',
        denunciante_idioma: '',
        denunciante_trabaja: false,
        denunciante_ocupacion: '',
        denunciante_domicilio_actual: '',
        latitud: null,
        longitud: null,
        // Familiar 1
        familiar1_nombre_completo: '',
        familiar1_edad: null,
        familiar1_parentesco: '',
        familiar1_celular: '',
        // Denunciado
        denunciado_nombre_completo: '',
        denunciado_documento: '',
        denunciado_nro: '',
        denunciado_sexo: '',
        denunciado_residencia: '',
        denunciado_domicilio_actual: '',
        denunciado_latitud: null,
        denunciado_longitud: null,
        // Caso
        caso_numero: '',
        caso_fecha_hecho: '',
        caso_lugar_hecho: '',
        caso_tipologia: '',
        caso_modalidad: '',
        caso_descripcion: '',
        // Violencias
        violencia_fisica: false,
        violencia_psicologica: false,
        violencia_sexual: false,
        violencia_economica: false,
        // Seguimiento
        seguimiento_area: '',
        seguimiento_area_social: '',
        seguimiento_area_legal: ''
      }
    }
  },
  computed: {
    // v-model para los mapas (Vue2 usa value/input, aquí lo simulamos con objetos)
    denunciantePos: {
      get () { return { latitud: this.f.latitud, longitud: this.f.longitud } },
      set (v) { this.f.latitud = v.latitud; this.f.longitud = v.longitud }
    },
    denunciadoPos: {
      get () { return { latitud: this.f.denunciado_latitud, longitud: this.f.denunciado_longitud } },
      set (v) { this.f.denunciado_latitud = v.latitud; this.f.denunciado_longitud = v.longitud }
    }
  },
  methods: {
    req (v) { return !!v || 'Requerido' },
    resetForm () {
      const bools = ['denunciante_trabaja','violencia_fisica','violencia_psicologica','violencia_sexual','violencia_economica']
      Object.keys(this.f).forEach(k => {
        if (bools.includes(k)) this.f[k] = false
        else if (k.includes('lat') || k.includes('long')) this.f[k] = null
        else this.f[k] = ''
      })
    },
    async save () {
      if (!this.f.denunciante_nombre_completo) {
        this.$alert.error('El nombre del denunciante es obligatorio')
        return
      }
      this.loading = true
      try {
        await this.$axios.post('/casos', this.f)
        this.$alert.success('Caso creado')
        this.resetForm()
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo crear el caso')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
