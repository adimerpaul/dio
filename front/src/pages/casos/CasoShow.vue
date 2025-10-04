<template>
  <q-page class="q-pa-md bg-grey-2">

    <!-- Header -->
    <div class="row items-center q-mb-md">
<!--      <a href="myscan://open">Escanear ahora</a>-->
      <div class="col-12 col-md-4">
        <div class="text-h6 text-weight-bold">{{caso?.tipo}} {{ caso?.caso_numero || '...' }}</div>
<!--          tiempo desde que de abri el caso con momnet y fecha_apertura_caso -->
          <div class="text-subtitle2 text-black-7">
            <q-icon name="calendar_today" class="q-mr-sm"/>
            Abierto {{ caso?.fecha_apertura_caso ? (new Date(caso.fecha_apertura_caso).toLocaleDateString()) : '...' }}
            <span v-if="caso?.fecha_apertura_caso"> (hace {{ Math.floor((new Date() - new Date(caso.fecha_apertura_caso)) / (1000 * 60 * 60 * 24)) }} días)</span>
          </div>
<!--        <div class="text-caption text-grey-7">Detalle y gestión integral</div>-->
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
            <q-item v-if="caso?.psicologica_user?.celular" clickable @click="sendWhatsApp('psico')">
              <q-item-section avatar><q-icon name="psychology"/></q-item-section>
              <q-item-section>Psicología ({{ caso.psicologica_user.name }})</q-item-section>
              <q-item-section side class="text-caption text-grey">{{ caso.psicologica_user.celular }}</q-item-section>
            </q-item>

            <q-item v-if="caso?.legal_user?.celular" clickable @click="sendWhatsApp('legal')">
              <q-item-section avatar><q-icon name="gavel"/></q-item-section>
              <q-item-section>Legal ({{ caso.legal_user.name }})</q-item-section>
              <q-item-section side class="text-caption text-grey">{{ caso.legal_user.celular }}</q-item-section>
            </q-item>

            <q-item v-if="caso?.trabajo_social_user?.celular" clickable @click="sendWhatsApp('social')">
              <q-item-section avatar><q-icon name="people"/></q-item-section>
              <q-item-section>Trabajo social ({{ caso.trabajo_social_user.name }})</q-item-section>
              <q-item-section side class="text-caption text-grey">{{ caso.trabajo_social_user.celular }}</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
<!--        <q-btn flat color="secondary" icon="print" label="Imprimir PDF" @click="printPdf" />-->
        <q-btn-dropdown flat color="secondary" icon="print" label="Imprimir PDF">
          <q-list>
            <q-item clickable @click="printPdf" v-close-popup >
              <q-item-section>Ficha del Caso</q-item-section>
            </q-item>
            <q-separator/>
            <!-- NUEVO: hoja de ruta con dos variantes -->
            <q-item clickable @click="printPdfHojaRuta('denunciante')" v-close-popup >
              <q-item-section>Ubicacion (Denunciante)</q-item-section>
            </q-item>
            <q-item clickable @click="printPdfHojaRuta('denunciado')" v-close-popup >
              <q-item-section>Ubicacion (Denunciado)</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
        <q-btn flat color="primary" icon="refresh" @click="fetchCaso" :loading="loading"/>
<!--        btoton eliminar caso y se vava atras-->
        <q-btn flat color="negative" icon="delete" label="Eliminar Caso" @click="eliminarCaso()" v-if="role === 'Administrador'" no-caps/>
      </div>
    </div>

    <!-- Tabs -->
    <q-card flat bordered class="q-mb-md">
      <q-tabs v-model="tab" class="text-primary" dense align="left"
              active-color="primary" indicator-color="primary" outside-arrows mobile-arrows>
        <q-tab name="info"         label="1 Información General"   icon="dashboard"       no-caps/>
        <q-tab name="seguimiento"  label="2 Seguimiento"           icon="track_changes"   no-caps/>
        <q-tab name="hoja"         label="3 Hoja de Ruta"          icon="report_problem"  no-caps />
        <q-tab name="psico"        label="4 Área Psicológico"      icon="psychology"      no-caps v-if="role === 'Administrador' || role === 'Psicologo'"/>
        <q-tab name="legal"        label="5 Área Legal"            icon="gavel"           no-caps v-if="role === 'Administrador' || role === 'Abogado'"/>
        <q-tab name="social"       label="6 Área Social"           icon="people"          no-caps v-if="role === 'Administrador' || role === 'Social'"/>
<!--        <q-tab name="apoyo"        label="7 Apoyo Integral"        icon="diversity_1"     no-caps v-if="role === 'Administrador' || role === 'Social'"/>-->
        <q-tab name="docs"         label="7 Documentos General"    icon="folder"          no-caps/>
        <q-tab name="fotos"        label="8 Fotografías"           icon="photo_library"   no-caps/>
        <q-tab name="codigo"        label="9 Codigos"               icon="code"   no-caps/>
      </q-tabs>
    </q-card>
<!--    <pre>{{role}}</pre>-->

    <q-tab-panels v-model="tab" animated keep-alive>
      <!-- 1) Información General -->
      <q-tab-panel name="info">
        <SlimNuevo :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-if="caso?.tipo==='SLIM'"/>
        <CasoNuevoDNA :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-else-if="caso?.tipo==='DNA'"/>
        <CasoNuevoSLAM :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-else-if="caso?.tipo==='SLAM'"/>
        <CasoNuevoUMADIS :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-else-if="caso?.tipo==='UMADIS'"/>
        <CasoNuevoPROPREMI :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-else-if="caso?.tipo==='PROPREMI'"/>
      </q-tab-panel>
      <q-tab-panel name="seguimiento">
        <Seguimiento :caso="caso"/>
      </q-tab-panel>

      <!-- 2) Problemática -->
      <q-tab-panel name="problematica">
        <Problematica :case-id="caseId"/>
      </q-tab-panel>

      <q-tab-panel name="hoja">
        <HojaRuta :case-id="caseId"/>
      </q-tab-panel>

      <!-- 3) Sesiones Psicológico -->
      <q-tab-panel name="psico">
        <SesionesPsicologico :case-id="caseId" :caso="caso" @refresh="fetchCaso"/>
      </q-tab-panel>

      <!-- 4) Informes Legal -->
      <q-tab-panel name="legal">
        <InformesLegal :case-id="caseId" :caso="caso" @refresh="fetchCaso"/>
      </q-tab-panel>
<!--      <q-tab name="social"       label="6 Área Social"           icon="people"          no-caps v-if="role === 'Administrador' || role === 'Social'"/>-->
      <q-tab-panel name="social">
        <SocialInformes :caso="caso" @refresh="fetchCaso" :case-id="caseId"/>
      </q-tab-panel>

      <!-- 5) Apoyo Integral -->
      <q-tab-panel name="apoyo">
        <ApoyoIntegral :case-id="caseId"/>
      </q-tab-panel>

      <!-- 6) Documentos General -->
      <q-tab-panel name="docs">
        <DocumentosGeneral :case-id="caseId" :caso="caso" @refresh="fetchCaso"/>
      </q-tab-panel>

      <!-- 7) Fotografías -->
      <q-tab-panel name="fotos">
        <Fotografias :case-id="caseId" :caso="caso" @refresh="fetchCaso"/>
      </q-tab-panel>

      <!-- 8) Códigos -->
      <q-tab-panel name="codigo">
        <Codigo :case-id="caseId" :caso="caso" @refresh="fetchCaso"/>
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
import HojaRuta from "pages/casos/tabs/HojaRuta.vue";
import Seguimiento from "pages/casos/tabs/Seguimiento.vue";
import SlimNuevo from "pages/slims/SlimNuevo.vue";
import SocialInformes from "pages/casos/tabs/Social.vue";
import CasoNuevoDNA from "pages/dnas/DnaNuevo.vue";
import CasoNuevoSLAM from "pages/slams/SlamNuevo.vue";
import CasoNuevoUMADIS from "pages/umadis/UmadisNuevo.vue";
import CasoNuevoPROPREMI from "pages/propremis/PropremisNuevo.vue";
import Codigo from "pages/casos/tabs/Codigo.vue";

export default {
  name: 'CasoDetalle',
  components: {
    Codigo,
    CasoNuevoPROPREMI,
    CasoNuevoUMADIS,
    CasoNuevoSLAM,
    CasoNuevoDNA,
    SocialInformes,
    SlimNuevo,
    Seguimiento,
    HojaRuta,
    InfoGeneral, Problematica, SesionesPsicologico, InformesLegal, ApoyoIntegral, DocumentosGeneral, Fotografias
  },
  data () {
    return {
      loading: false,
      tab: 'info',
      caso: null,
      defaultCountryCallingCode: '591',
    }
  },
  computed: {
    caseId () { return this.$route.params.id },
    role () { return this.$store.user?.role || '' },
    hasAnyWa () {
      const c = this.caso || {}
      return !!(c?.psicologica_user?.celular || c?.legal_user?.celular || c?.trabajo_social_user?.celular)
    },
  },
  created () {
    this.fetchCaso()
  },
  methods: {
    eliminarCaso() {
      this.$q.dialog({
        title: 'Confirmar eliminación',
        message: '¿Estás seguro de que deseas eliminar este caso? Esta acción no se puede deshacer.',
        cancel: true,
        persistent: true
      }).onOk(async () => {
        try {
          await this.$axios.delete(`/casos/${this.caseId}`);
          this.$q.notify({ type: 'positive', message: 'Caso eliminado exitosamente.' });
          this.$router.back(); // Volver a la página anterior
        } catch (e) {
          this.$alert.error(e?.response?.data?.message || 'No se pudo eliminar el caso');
        }
      });
    },
    normalizePhone (raw) {
      if (!raw) return ''
      // Solo dígitos
      let digits = String(raw).replace(/\D+/g, '')
      // Si ya viene con 11-12 dígitos (ej. 5917xxxxxxx), lo respetamos
      if (/^(\d{11,13})$/.test(digits)) return digits
      // Si es un número local de 8 dígitos (BO), anteponer código país
      if (digits.length === 8) return this.defaultCountryCallingCode + digits
      // Si trae 9 dígitos (a veces 7 + prefijo), también anteponer
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
      // Mensaje base (edítalo a tu gusto)
      const c = this.caso || {}
      const num = c.caso_numero ? c.caso_numero.replace(/\\\//g, '/') : `#${this.caseId}`
      const link = (this.$axios?.defaults?.baseURL || '') + `/casos/${this.caseId}`

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
        `Ver caso: ${link}`
      ].filter(Boolean).join('\n')
    },

    sendWhatsApp (roleKey) {
      const c = this.caso || {}
      const user =
        roleKey === 'psico'  ? c.psicologica_user :
          roleKey === 'legal'  ? c.legal_user :
            roleKey === 'social' ? c.trabajo_social_user : null

      if (!user?.celular) {
        this.$q.notify({ type:'warning', message: 'No hay número de celular configurado para ese responsable' })
        return
      }

      const url = this.waUrl(user.celular, this.waMessage(roleKey))
      window.open(url, '_blank')
    },
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
      const url = this.$axios.defaults.baseURL + `/casos/${this.caseId}/pdf`
      window.open(url, '_blank')
    },
    printPdfHojaRuta (tipo = 'denunciante') {
      const url = this.$axios.defaults.baseURL + `/casos/${this.caseId}/pdf/hoja-ruta?tipo=${tipo}`
      window.open(url, '_blank')
    },
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
</style>
