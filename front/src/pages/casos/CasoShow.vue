<template>
  <q-page class="q-pa-md bg-grey-2">

    <!-- Header -->
    <div class="row items-center q-mb-md">
      <div class="col">
        <div class="text-h6 text-weight-bold">Caso #{{ caso?.id || '...' }}</div>
        <div class="text-caption text-grey-7">Detalle y gestión integral</div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn flat color="secondary" icon="print" label="Imprimir PDF" @click="printPdf" />
        <q-btn flat color="primary" icon="refresh" @click="fetchCaso" :loading="loading"/>
      </div>
    </div>

    <!-- Tabs -->
    <q-card flat bordered class="q-mb-md">
      <q-tabs v-model="tab" class="text-primary" dense align="left"
              active-color="primary" indicator-color="primary" outside-arrows mobile-arrows>
        <q-tab name="info"         label="1 INFORMACIÓN GENERAL"   icon="dashboard"/>
        <q-tab name="problematica" label="2 PROBLEMÁTICA"          icon="report_problem"/>
        <q-tab name="psico"        label="3 SESIONES PSICOLÓGICO"  icon="psychology"/>
        <q-tab name="legal"        label="4 INFORMES LEGAL"        icon="gavel"/>
        <q-tab name="apoyo"        label="5 APOYO INTEGRAL"        icon="diversity_1"/>
        <q-tab name="docs"         label="6 DOCUMENTOS GENERAL"    icon="folder"/>
        <q-tab name="fotos"        label="7 FOTOGRAFÍAS"           icon="photo_library"/>
      </q-tabs>
    </q-card>

    <q-tab-panels v-model="tab" animated keep-alive>
      <!-- 1) Información General -->
      <q-tab-panel name="info">
        <InfoGeneral :case-id="caseId"/>
      </q-tab-panel>

      <!-- 2) Problemática -->
      <q-tab-panel name="problematica">
        <Problematica :case-id="caseId"/>
      </q-tab-panel>

      <!-- 3) Sesiones Psicológico -->
      <q-tab-panel name="psico">
        <SesionesPsicologico :case-id="caseId"/>
      </q-tab-panel>

      <!-- 4) Informes Legal -->
      <q-tab-panel name="legal">
        <InformesLegal :case-id="caseId"/>
      </q-tab-panel>

      <!-- 5) Apoyo Integral -->
      <q-tab-panel name="apoyo">
        <ApoyoIntegral :case-id="caseId"/>
      </q-tab-panel>

      <!-- 6) Documentos General -->
      <q-tab-panel name="docs">
        <DocumentosGeneral :case-id="caseId"/>
      </q-tab-panel>

      <!-- 7) Fotografías -->
      <q-tab-panel name="fotos">
        <Fotografias :case-id="caseId"/>
      </q-tab-panel>
    </q-tab-panels>
  </q-page>
</template>

<script>
// Componentes de tabs
import InfoGeneral       from './tabs/InfoGeneral.vue'
import Problematica      from './tabs/Problematica.vue'
import SesionesPsicologico from './tabs/SesionesPsicologico.vue'
import InformesLegal     from './tabs/InformesLegal.vue'
import ApoyoIntegral     from './tabs/ApoyoIntegral.vue'
import DocumentosGeneral from './tabs/DocumentosGeneral.vue'
import Fotografias       from './tabs/Fotografias.vue'

export default {
  name: 'CasoDetalle',
  components: {
    InfoGeneral, Problematica, SesionesPsicologico, InformesLegal, ApoyoIntegral, DocumentosGeneral, Fotografias
  },
  data () {
    return {
      loading: false,
      tab: 'info',
      caso: null
    }
  },
  computed: {
    caseId () { return this.$route.params.id }
  },
  created () {
    this.fetchCaso()
  },
  methods: {
    async fetchCaso () {
      this.loading = true
      try {
        const res = await this.$axios.get(`/casos/${this.caseId}`)
        this.caso = res.data
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo cargar el caso')
      } finally {
        this.loading = false
      }
    },
    printPdf () {
      // placeholder — luego conectas tu endpoint/cliente PDF
      this.$alert.info('Generando PDF… (próximamente)')
    }
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
</style>
