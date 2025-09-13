<template>
  <q-page class="q-pa-md bg-grey-2">

    <!-- Header -->
    <div class="row items-center q-mb-md">
      <div class="col">
        <div class="text-h6 text-weight-bold">SLIM #{{ slim?.id || '...' }}</div>
        <div class="text-caption text-grey-7">Detalle y gestión integral</div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn-dropdown
          flat
          color="positive"
          icon="chat"
          label="WhatsApp"
          v-if="hasAnyWa"
        >
          <q-list>
            <q-item v-if="slim?.psicologica_user?.celular" clickable @click="sendWhatsApp('psico')">
              <q-item-section avatar><q-icon name="psychology"/></q-item-section>
              <q-item-section>Psicología ({{ slim.psicologica_user.name }})</q-item-section>
              <q-item-section side class="text-caption text-grey">{{ slim.psicologica_user.celular }}</q-item-section>
            </q-item>

            <q-item v-if="slim?.legal_user?.celular" clickable @click="sendWhatsApp('legal')">
              <q-item-section avatar><q-icon name="gavel"/></q-item-section>
              <q-item-section>Legal ({{ slim.legal_user.name }})</q-item-section>
              <q-item-section side class="text-caption text-grey">{{ slim.legal_user.celular }}</q-item-section>
            </q-item>

            <q-item v-if="slim?.trabajo_social_user?.celular" clickable @click="sendWhatsApp('social')">
              <q-item-section avatar><q-icon name="people"/></q-item-section>
              <q-item-section>Trabajo social ({{ slim.trabajo_social_user.name }})</q-item-section>
              <q-item-section side class="text-caption text-grey">{{ slim.trabajo_social_user.celular }}</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>

        <q-btn-dropdown flat color="secondary" icon="print" label="Imprimir PDF">
          <q-list>
            <q-item clickable @click="printPdf" v-close-popup >
              <q-item-section>Ficha del SLIM</q-item-section>
            </q-item>

            <q-separator/>

            <!-- hoja de ruta con dos variantes -->
            <q-item clickable @click="printPdfHojaRuta('denunciante')" v-close-popup >
              <q-item-section>Ubicación (Denunciante)</q-item-section>
            </q-item>
            <q-item clickable @click="printPdfHojaRuta('denunciado')" v-close-popup >
              <q-item-section>Ubicación (Denunciado)</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>

        <q-btn flat color="primary" icon="refresh" @click="fetchSlim" :loading="loading"/>
      </div>
    </div>

    <!-- Tabs -->
    <q-card flat bordered class="q-mb-md">
      <q-tabs v-model="tab" class="text-primary" dense align="left"
              active-color="primary" indicator-color="primary" outside-arrows mobile-arrows>
        <q-tab name="info"         label="1 Información General"   icon="dashboard"       no-caps/>
        <q-tab name="seguimiento"  label="2 Seguimiento"           icon="track_changes"   no-caps/>
        <q-tab name="hoja"         label="3 Hoja de Ruta"          icon="report_problem"  no-caps v-if="role === 'Administrador' || role === 'Asistente'"/>
        <q-tab name="psico"        label="4 Área Psicológico"      icon="psychology"      no-caps v-if="role === 'Administrador' || role === 'Psicologo'"/>
        <q-tab name="legal"        label="5 Área Legal"            icon="gavel"           no-caps v-if="role === 'Administrador' || role === 'Abogado'"/>
        <q-tab name="social"       label="6 Área Social"           icon="people"          no-caps v-if="role === 'Administrador' || role === 'Social'"/>
        <q-tab name="apoyo"        label="7 Apoyo Integral"        icon="diversity_1"     no-caps v-if="role === 'Administrador' || role === 'Social'"/>
        <q-tab name="docs"         label="8 Documentos General"    icon="folder"          no-caps/>
        <q-tab name="fotos"        label="9 Fotografías"           icon="photo_library"   no-caps/>
      </q-tabs>
    </q-card>

    <q-tab-panels v-model="tab" animated keep-alive>
      <!-- 1) Información General -->
      <q-tab-panel name="info">
        <InfoGeneral :case-id="caseId"/>
      </q-tab-panel>

      <!-- 2) Seguimiento -->
      <q-tab-panel name="seguimiento">
        <Seguimiento :case-id="caseId"/>
      </q-tab-panel>

      <!-- 3) Hoja de Ruta -->
      <q-tab-panel name="hoja">
        <HojaRuta :case-id="caseId"/>
      </q-tab-panel>

      <!-- 4) Sesiones Psicológico -->
      <q-tab-panel name="psico">
        <SesionesPsicologico :case-id="caseId" :caso="slim"/>
      </q-tab-panel>

      <!-- 5) Informes Legal -->
      <q-tab-panel name="legal">
        <InformesLegal :case-id="caseId"/>
      </q-tab-panel>

      <!-- 6) Apoyo Integral -->
      <q-tab-panel name="apoyo">
        <ApoyoIntegral :case-id="caseId"/>
      </q-tab-panel>

      <!-- 7) Documentos General -->
      <q-tab-panel name="docs">
        <DocumentosGeneral :case-id="caseId"/>
      </q-tab-panel>

      <!-- 8) Fotografías -->
      <q-tab-panel name="fotos">
        <Fotografias :case-id="caseId"/>
      </q-tab-panel>
    </q-tab-panels>
  </q-page>
</template>

<script>
// Componentes de tabs (ubicados ahora en pages/slims/tabs/)
import InfoGeneral          from './tabs/InfoGeneral.vue'
import SesionesPsicologico  from './tabs/SesionesPsicologico.vue'
import InformesLegal        from './tabs/InformesLegal.vue'
import ApoyoIntegral        from './tabs/ApoyoIntegral.vue'
import DocumentosGeneral    from './tabs/DocumentosGeneral.vue'
import Fotografias          from './tabs/Fotografias.vue'
import HojaRuta             from './tabs/HojaRuta.vue'
import Seguimiento          from './tabs/Seguimiento.vue'

export default {
  name: 'SlimDetalle',
  components: {
    InfoGeneral, SesionesPsicologico, InformesLegal, ApoyoIntegral, DocumentosGeneral, Fotografias, HojaRuta, Seguimiento
  },
  data () {
    return {
      loading: false,
      tab: 'info',
      slim: null,
      defaultCountryCallingCode: '591',
    }
  },
  computed: {
    caseId () { return this.$route.params.id },
    role () { return this.$store.user?.role || '' },
    hasAnyWa () {
      const c = this.slim || {}
      return !!(c?.psicologica_user?.celular || c?.legal_user?.celular || c?.trabajo_social_user?.celular)
    },
  },
  created () {
    this.fetchSlim()
  },
  methods: {
    normalizePhone (raw) {
      if (!raw) return ''
      let digits = String(raw).replace(/\D+/g, '')
      if (/^(\d{11,13})$/.test(digits)) return digits
      if (digits.length === 8) return this.defaultCountryCallingCode + digits
      if (digits.length === 9 && !digits.startsWith(this.defaultCountryCallingCode)) {
        return this.defaultCountryCallingCode + digits
      }
      return digits
    },
    waUrl (phone, text) {
      const p = this.normalizePhone(phone)
      const msg = encodeURIComponent(text || '')
      return `https://wa.me/${p}${msg ? `?text=${msg}` : ''}`
    },
    waMessage (roleKey) {
      const c = this.slim || {}
      const num = c.caso_numero ? c.caso_numero.replace(/\\\//g, '/') : `#${this.caseId}`
      const link = (this.$axios?.defaults?.baseURL || '') + `/slims/${this.caseId}`
      const rolNombre = roleKey === 'psico'
        ? 'Psicología'
        : roleKey === 'legal'
          ? 'Legal'
          : 'Trabajo Social'
      return [
        `*SLIM - Notificación de Caso*`,
        `Nro: ${num}`,
        `Área: ${rolNombre}`,
        `Denunciante: ${c.denunciante_nombre_completo || '—'}`,
        c.caso_tipologia ? `Tipología: ${c.caso_tipologia}` : null,
        c.caso_modalidad ? `Modalidad: ${c.caso_modalidad}` : null,
        c.caso_fecha_hecho ? `Fecha del hecho: ${c.caso_fecha_hecho}` : null,
        '',
        `Ver SLIM: ${link}`
      ].filter(Boolean).join('\n')
    },
    sendWhatsApp (roleKey) {
      const c = this.slim || {}
      const user =
        roleKey === 'psico'   ? c.psicologica_user :
          roleKey === 'legal'   ? c.legal_user :
            roleKey === 'social'  ? c.trabajo_social_user : null

      if (!user?.celular) {
        this.$q.notify({ type:'warning', message: 'No hay número de celular configurado para ese responsable' })
        return
      }
      const url = this.waUrl(user.celular, this.waMessage(roleKey))
      window.open(url, '_blank')
    },

    async fetchSlim () {
      this.loading = true
      try {
        const res = await this.$axios.get(`/slims/${this.caseId}`)
        this.slim = res.data
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo cargar el SLIM')
      } finally {
        this.loading = false
      }
    },

    printPdf () {
      const url = this.$axios.defaults.baseURL + `/slims/${this.caseId}/pdf`
      window.open(url, '_blank')
    },
    printPdfHojaRuta (tipo = 'denunciante') {
      const url = this.$axios.defaults.baseURL + `/slims/${this.caseId}/pdf/hoja-ruta?tipo=${tipo}`
      window.open(url, '_blank')
    },
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
</style>
