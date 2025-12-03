<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- TOOLBAR -->
    <q-toolbar class="bg-primary text-white q-mb-md">
      <q-btn flat round dense icon="arrow_back" @click="$router.back()" />
      <q-toolbar-title>Lugar de Acogida NNA</q-toolbar-title>
    </q-toolbar>

    <!-- CABECERA DEL CASO -->
    <q-card flat bordered class="q-mb-md bg-white">
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-3">
          <div class="text-caption text-grey-7">COD CASO</div>
          <div class="text-h6 text-primary">
            {{ caso?.caso_numero || '—' }}
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="text-caption text-grey-7">NUREJ</div>
          <div class="text-subtitle1">
            {{ caso?.nurej || '—' }}
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="text-caption text-grey-7">NNA</div>
          <div class="text-subtitle1 text-weight-bold">
            {{ nnaNombre }}
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="text-caption text-grey-7">JUZGADO</div>
          <div class="text-subtitle1">
            {{ caso?.numero_juzgado || '—' }}
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- FORMULARIO LUGAR DE ACOGIDA -->
    <q-card flat bordered class="bg-white">
      <q-card-section class="q-pb-none">
        <div class="row items-center q-col-gutter-md">
          <div class="col-12 col-md-6">
            <div class="text-caption text-grey-7 q-mb-xs">TIPO DE ACOGIDA</div>
            <q-select
              v-model="form.tipo_de_acogida"
              :options="tipoAcogidaOptions"
              outlined dense
              emit-value
              map-options
              :disable="saving"
            >
              <template #prepend>
                <q-icon name="groups" />
              </template>
            </q-select>
          </div>

          <div class="col-12 col-md-6" v-if="form.tipo_de_acogida === 'ACOGIDA INSTITUCIONAL (AI)'">
            <div class="text-caption text-grey-7 q-mb-xs">CENTRO DE ACOGIDA</div>
            <q-select
              v-model="form.centro_de_acogida"
              :options="[
                'GOTA DE LECHE',
                'ANDROILI',
                'ZELADA',
                'PENY',
                'ALDEAS SOS',
                'CRECER'
              ]"
              placeholder="Seleccione o ingrese el nombre del centro / familia"
              outlined dense
              use-input
              fill-input
              :disable="saving"
            >
              <template #prepend>
                <q-icon name="home" />
              </template>
            </q-select>
          </div>
        </div>
      </q-card-section>

      <q-separator class="q-my-md" />

      <q-card-section v-if="form.tipo_de_acogida !== 'ACOGIDA INSTITUCIONAL (AI)' && form.tipo_de_acogida !== null" class="q-pt-none">
        <div class="text-subtitle2 q-mb-sm">Datos del Cuidador(a)</div>

        <div class="row q-col-gutter-md">
          <div class="col-12">
            <q-input
              v-model="form.cuidadora_nombre_completo"
              label="Nombre completo"
              outlined dense
              :disable="saving"
            >
              <template #prepend>
                <q-icon name="person" />
              </template>
            </q-input>
          </div>

          <div class="col-12 col-md-4">
            <q-input
              v-model="form.cuidadora_celular"
              label="N° celular"
              outlined dense
              :disable="saving"
            >
              <template #prepend>
                <q-icon name="phone_iphone" />
              </template>
            </q-input>
          </div>

          <div class="col-12 col-md-4">
            <q-input
              v-model="form.cuidadora_domicilio"
              label="Domicilio"
              outlined dense
              :disable="saving"
            >
              <template #prepend>
                <q-icon name="location_city" />
              </template>
            </q-input>
          </div>

          <div class="col-12 col-md-4">
            <q-input
              v-model="form.cuidadora_ubicacion"
              label="Ubicación / Zona"
              outlined dense
              :disable="saving"
            >
              <template #prepend>
                <q-icon name="place" />
              </template>
            </q-input>
          </div>
        </div>

        <!-- lat/lng opcional (por ahora oculto si quieres; se puede dejar visible) -->
        <!--
        <div class="row q-col-gutter-md q-mt-sm">
          <div class="col-6 col-md-3">
            <q-input v-model="form.cuidadora_lat" label="Latitud" outlined dense />
          </div>
          <div class="col-6 col-md-3">
            <q-input v-model="form.cuidadora_lng" label="Longitud" outlined dense />
          </div>
        </div>
        -->
      </q-card-section>

      <q-card-section class="q-pt-none">
        <div class="row justify-end q-mt-md">
          <q-btn
            color="primary"
            unelevated
            icon="save"
            label="Guardar"
            :loading="saving"
            no-caps
            @click="onSubmit"
          />
        </div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
export default {
  name: 'LugarAcogidaNna',
  data () {
    return {
      loading: false,
      saving: false,
      caso: null,
      form: {
        tipo_de_acogida: null,
        centro_de_acogida: '',
        cuidadora_nombre_completo: '',
        cuidadora_celular: '',
        cuidadora_domicilio: '',
        cuidadora_ubicacion: '',
        cuidadora_lat: '',
        cuidadora_lng: ''
      },
      tipoAcogidaOptions: [
        'ACOGIDA INSTITUCIONAL (AI)',
        'ACOGIMIENTO CON FAMILIA AMPLIADA (AFA)',
        'ACOGIMIENTO CON FAMILIA COMUNITARIA (AFC)',
        'RESTITUCIÓN DE FAMILIA ORIGEN (RFO)',
      ]
    }
  },
  computed: {
    casoId () {
      return this.$route.params.id
    },
    nnaNombre () {
      // primera víctima del caso
      const v = this.caso?.victimas?.[0]
      return v?.nombres_apellidos || '—'
    }
  },
  mounted () {
    this.loadData()
  },
  methods: {
    async loadData () {
      this.loading = true
      try {
        // 1. Cabecera del caso
        const casoRes = await this.$axios.get(`/casos/${this.casoId}`)
        this.caso = casoRes.data

        // 2. Datos de acogimiento (si existen)
        const acRes = await this.$axios.get(`/casos/${this.casoId}/acogimiento`)
        if (acRes.data) {
          Object.assign(this.form, acRes.data)
        }
      } catch (e) {
        console.error(e)
        this.$q.notify({
          type: 'negative',
          message: e.response?.data?.message || 'Error cargando datos del caso'
        })
      } finally {
        this.loading = false
      }
    },
    async onSubmit () {
      this.saving = true
      try {
        await this.$axios.post(`/casos/${this.casoId}/acogimiento`, this.form)
        this.$q.notify({
          type: 'positive',
          message: 'Lugar de acogida guardado correctamente'
        })
      } catch (e) {
        console.error(e)
        this.$q.notify({
          type: 'negative',
          message: e.response?.data?.message || 'Error al guardar los datos'
        })
      } finally {
        this.saving = false
      }
    }
  }
}
</script>
