<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Header -->
    <div class="row items-center q-mb-md">
      <div class="col">
        <div class="text-h6 text-weight-bold">DNA #{{ caseId }}</div>
        <div class="text-caption text-grey-7">Detalle y gestión</div>
      </div>
      <div class="col-auto">
        <q-btn flat color="primary" icon="refresh" @click="load" :loading="loading"/>
      </div>
    </div>

    <!-- Tabs -->
    <q-card flat bordered class="q-mb-md">
      <q-tabs v-model="tab" class="text-primary" dense align="left"
              active-color="primary" indicator-color="primary" outside-arrows mobile-arrows>
        <q-tab name="info"        label="1 Información General" icon="dashboard"      no-caps/>
        <q-tab name="seguimiento" label="2 Seguimiento"         icon="track_changes"  no-caps/>
        <q-tab name="psico"       label="3 Área Psicología"     icon="psychology"     no-caps/>
        <q-tab name="legal"       label="4 Área Legal"          icon="gavel"          no-caps/>
        <q-tab name="docs"        label="5 Documentos"          icon="folder"         no-caps/>
        <q-tab name="fotos"       label="6 Fotografías"         icon="photo_library"  no-caps/>
      </q-tabs>
    </q-card>

    <q-tab-panels v-model="tab" animated keep-alive>
      <q-tab-panel name="info">
        <DnaInfoGeneral :case-id="caseId"/>
      </q-tab-panel>

      <q-tab-panel name="seguimiento">
        <DnaSeguimiento :case-id="caseId"/>
      </q-tab-panel>

      <q-tab-panel name="psico">
        <DnaPsicologia :case-id="caseId"/>
      </q-tab-panel>

      <q-tab-panel name="legal">
        <DnaInformesLegal :case-id="caseId" :caso="dna"/>
      </q-tab-panel>

      <q-tab-panel name="docs">
        <DnaDocumentos :case-id="caseId"/>
      </q-tab-panel>

      <q-tab-panel name="fotos">
        <DnaFotografias :case-id="caseId"/>
      </q-tab-panel>
    </q-tab-panels>
  </q-page>
</template>

<script>
import DnaInfoGeneral   from './tabs/InfoGeneral.vue'
import DnaSeguimiento   from './tabs/Seguimiento.vue'
import DnaPsicologia    from './tabs/Psicologia.vue'
import DnaInformesLegal from './tabs/InformesLegal.vue'
import DnaDocumentos    from './tabs/Documentos.vue'
import DnaFotografias   from './tabs/Fotografias.vue'

export default {
  name: 'DnaShow',
  components: { DnaInfoGeneral, DnaSeguimiento, DnaPsicologia, DnaInformesLegal, DnaDocumentos, DnaFotografias },
  data: () => ({ tab: 'info', dna: null, loading: false }),
  computed: {
    caseId () { return this.$route.params.id }
  },
  mounted () { this.load() },
  methods: {
    async load () {
      this.loading = true
      try {
        const { data } = await this.$axios.get(`/dnas/${this.caseId}`)
        this.dna = data || {}
      } catch (e) {
        this.$alert?.error?.(e?.response?.data?.message || 'No se pudo cargar el caso')
      } finally { this.loading = false }
    }
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
</style>
