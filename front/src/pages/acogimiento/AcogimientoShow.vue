<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-pa-md">
      <div class="row items-center q-gutter-sm q-mb-sm">
        <q-avatar icon="home" color="primary" text-color="white" />
        <div class="text-h6 text-weight-bold">Acogimiento</div>
        <q-space />
        <q-btn color="primary" icon="save" label="Guardar" :loading="saving" @click="save" />
      </div>

      <q-separator spaced />

      <!-- Cabecera -->
      <div class="row q-col-gutter-md q-mb-md">
        <div class="col-12 col-md-4">
          <q-input v-model="header.nurej" label="NUREJ" readonly filled>
            <template #prepend><q-icon name="tag" /></template>
          </q-input>
        </div>
        <div class="col-12 col-md-4">
          <q-input v-model="header.caso_numero" label="Nº Caso" readonly filled>
            <template #prepend><q-icon name="numbers" /></template>
          </q-input>
        </div>
        <div class="col-12 col-md-4">
<!--          <q-input v-model="header.nna" label="Nombre NNA" readonly filled>-->
<!--            <template #prepend><q-icon name="child_care" /></template>-->
<!--          </q-input>-->
        </div>
      </div>

      <!-- Tipo de acogimiento -->
      <q-card flat bordered class="q-pa-md q-mb-md">
        <div class="text-subtitle1 q-mb-sm">Tipo de acogimiento</div>
        <q-option-group
          v-model="form.adopcion"
          type="radio"
          :options="adopcionOpts"
          inline dense
        />
      </q-card>

      <!-- Hogar + datos -->
      <q-card flat bordered class="q-pa-md q-mb-md">
        <div class="row q-col-gutter-md">
          <div class="col-12 col-md-4">
            <q-select v-model="form.hogar" :options="hogarOpts" label="Hogar" filled emit-value map-options />
          </div>
          <div class="col-12 col-md-4">
            <q-input v-model="form.juzgado" label="Juzgado" filled />
          </div>
          <div class="col-12 col-md-4">
            <q-input v-model="form.audiencia_evaluacion" label="Audiencia de Evaluación" filled type="date" />
          </div>
          <div class="col-12 col-md-4">
            <q-input v-model="form.fecha" label="Fecha (YYYY-MM-DD)" filled />
          </div>
          <div class="col-12 col-md-4">
            <q-input v-model="form.proximas_audiencia" label="Próxima Audiencia (YYYY-MM-DD)" filled />
          </div>
        </div>
      </q-card>

      <!-- Área legal + tipología -->
      <q-card flat bordered class="q-pa-md q-mb-md">
        <div class="row q-col-gutter-md">
          <div class="col-12 col-md-4">
            <q-select
              v-model="form.area_legal"
              :options="areaLegalOpts"
              label="Área Legal" filled emit-value map-options
              @update:model-value="onAreaLegalChange"
            />
          </div>
          <div class="col-12 col-md-8">
            <q-select
              v-model="form.tipologia"
              :options="tipologiaComputed"
              label="Tipología" filled emit-value map-options
              use-input fill-input input-debounce="0"
            />
          </div>
        </div>
      </q-card>

      <!-- NNA asociados -->
      <q-card flat bordered class="q-pa-md">
        <div class="text-subtitle1 q-mb-sm">Niños/Niñas asociados</div>
        <div class="row q-col-gutter-md">
          <div v-for="i in 10" :key="i" class="col-12 col-md-6">
            <q-input v-model="form[`nino${i}`]" :label="`Niño ${i}`" filled />
          </div>
        </div>
      </q-card>
    </q-card>
  </q-page>
</template>

<script>
export default {
  name: 'AcogimientoShow',
  data () {
    return {
      casoId: null,
      saving: false,
      header: { nurej: '', caso_numero: '', nna: '' },
      form: {
        adopcion: null,
        hogar: null,
        nino1: null, nino2: null, nino3: null, nino4: null, nino5: null,
        nino6: null, nino7: null, nino8: null, nino9: null, nino10: null,
        juzgado: null,
        cuidado_nombre: null, cuidado_paterno: null, cuidado_materno: null,
        area_legal: null, tipologia: null,
        audiencia_evaluacion: null, fecha: null, proximas_audiencia: null,
      },
      adopcionOpts: [
        { label: '(AI) ACOGIDA INSTITUCIONAL', value: 'AI' },
        { label: '(AFA) ACOGIMIENTO CON FAMILIA AMPLIADA', value: 'AFA' },
        { label: '(AFC) ACOGIMIENTO CON FAMILIA COMUNITARIA', value: 'AFC' },
        { label: '(RFO) RESTITUCIÓN DE FAMILIA AL ORIGEN', value: 'RFO' },
      ],
      hogarOpts: [
        { label: 'GOTA DE LECHE', value: 'GOTA DE LECHE' },
        { label: 'ANDRIOLI', value: 'ANDRIOLI' },
        { label: 'ZELADA', value: 'ZELADA' },
        { label: 'PENI', value: 'PENI' },
        { label: 'ALDEAS SOS', value: 'ALDEAS SOS' },
        { label: 'CRECER', value: 'CRECER' },
      ],
      areaLegalOpts: [
        { label: 'DENUNCIA', value: 'DENUNCIA' },
        { label: 'DEMANDA', value: 'DEMANDA' },
      ],
      tipologiasPorArea: {
        DEMANDA: [
          { label: 'ASISTENCIAS FAMILIARES (AF)', value: 'ASISTENCIAS FAMILIARES (AF)' },
          { label: 'EXTENSIONES GUARDAS', value: 'EXTENSIONES GUARDAS' },
          { label: 'INFRACCIÓN A LA LEY', value: 'INFRACCIÓN A LA LEY' },
        ],
        DENUNCIA: [
          { label: 'Sustracción de un Menor o Incapaz', value: 'Sustracción de un Menor o Incapaz' },
          { label: 'Infanticidio', value: 'Infanticidio' },
          { label: 'Lesiones Graves y Leves', value: 'Lesiones Graves y Leves' },
          { label: 'Violencia Familiar o Doméstica', value: 'Violencia Familiar o Doméstica' },
          { label: 'Violación', value: 'Violación' },
          { label: 'Violación NNA', value: 'Violación NNA' },
          { label: 'Estupro', value: 'Estupro' },
          { label: 'Abuso sexual', value: 'Abuso sexual' },
          { label: 'Acoso sexual', value: 'Acoso sexual' },
          { label: 'Corrupción de NNA', value: 'Corrupción de NNA' },
          { label: 'Pornografía', value: 'Pornografía' },
          { label: 'Otros', value: 'Otros' },
        ],
      },
    }
  },
  computed: {
    tipologiaComputed () {
      const k = this.form.area_legal || 'DENUNCIA'
      return this.tipologiasPorArea[k] || []
    },
  },
  mounted () {
    this.casoId = this.$route.params.id
    this.load()
  },
  methods: {
    async load () {
      try {
        const { data } = await this.$axios.get(`/casos/${this.casoId}/acogimiento`)
        const caso = data?.caso || {}
        const acog = data?.acogimiento || null

        // Header
        this.header.nurej = caso.nurej || '—'
        this.header.caso_numero = caso.caso_numero || '—'
        const menor = Array.isArray(caso.menores) && caso.menores.length ? caso.menores[0] : null
        this.header.nna = menor
          ? [menor.menor_nombres, menor.menor_paterno, menor.menor_materno].filter(Boolean).join(' ')
          : '—'

        // Form (hidratar si existe)
        if (acog) Object.assign(this.form, acog)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar el acogimiento')
      }
    },
    onAreaLegalChange () {
      const vals = this.tipologiaComputed.map(o => o.value)
      if (!vals.includes(this.form.tipologia)) this.form.tipologia = null
    },
    async save () {
      this.saving = true
      try {
        const payload = { ...this.form } // sin validación
        await this.$axios.put(`/casos/${this.casoId}/acogimiento`, payload)
        this.$alert.success('Guardado correctamente')
        await this.load()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar')
      } finally {
        this.saving = false
      }
    },
  },
}
</script>

<style scoped>
.q-card { border-radius: 16px; }
</style>
