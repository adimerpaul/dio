<!-- pages/slims/tabs/InfoGeneral.vue -->
<template>
  <div>
    <!-- ======= HEADER / ACCIONES ======= -->
    <div class="row items-center q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Información General</div>
        <div class="text-caption text-grey-7">SLAM #{{ orDash(form.numero_caso) }}</div>
      </div>
      <div class="col-auto row q-gutter-sm" v-if="canEdit">
        <q-btn
          v-if="!editing"
          color="primary"
          icon="edit"
          label="Editar"
          @click="startEdit"
          :loading="loading"
        />
        <template v-else>
          <q-btn flat color="negative" icon="close" label="Cancelar" @click="cancelEdit" :loading="loading"/>
          <q-btn color="primary" icon="save" label="Guardar" @click="save" :loading="loading"/>
        </template>
      </div>
    </div>

    <!-- Debug opcional -->
    <!-- <pre>{{ form }}</pre> -->

    <!-- ========= MODO LECTURA ========= -->
    <template v-if="!editing">
      <!-- 1) Datos del caso (S.L.A.M.) -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="description" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Datos del caso (S.L.A.M.)</div>
        </q-card-section>
        <q-separator/>
        <q-card-section class="row q-col-gutter-md">
          <div class="col-6 col-md-3"><b>Nº Apoyo Integral:</b> {{ orDash(form.numero_apoyo_integral) }}</div>
          <div class="col-6 col-md-3"><b>Nº de caso:</b> {{ orDash(form.numero_caso) }}</div>
          <div class="col-6 col-md-3"><b>Fecha registro:</b> {{ orDash(form.fecha_registro) }}</div>

          <div class="col-12 text-subtitle2 q-mt-sm">Idiomas (AM)</div>
          <div class="col-6 col-md-2">Castellano: {{ yesNo(form.am_idioma_castellano) }}</div>
          <div class="col-6 col-md-2">Quechua: {{ yesNo(form.am_idioma_quechua) }}</div>
          <div class="col-6 col-md-2">Aymara: {{ yesNo(form.am_idioma_aymara) }}</div>
          <div class="col-12 col-md-6">Otros: {{ orDash(form.am_idioma_otros) }}</div>

          <div class="col-12 text-subtitle2 q-mt-sm">Teléfonos de referencia</div>
          <div class="col-12 col-md-4"><b>Tel. fijo:</b> {{ orDash(form.ref_tel_fijo) }}</div>
          <div class="col-12 col-md-4"><b>Tel. móvil:</b> {{ orDash(form.ref_tel_movil) }}</div>
          <div class="col-12 col-md-4"><b>Tel. móvil (alt):</b> {{ orDash(form.ref_tel_movil_alt) }}</div>

          <div class="col-12 text-subtitle2 q-mt-sm">Ubicación</div>
          <div class="col-6 col-md-3"><b>Latitud:</b> {{ orDash(form.am_latitud) }}</div>
          <div class="col-6 col-md-3"><b>Longitud:</b> {{ orDash(form.am_longitud) }}</div>

          <div class="col-12 text-subtitle2 q-mt-sm">Hecho y Tipología</div>
          <div class="col-12"><b>Descripción:</b> <span class="ellipsis-3">{{ orDash(form.hecho_descripcion) }}</span></div>
          <div class="col-6 col-md-3">Violencia física: {{ yesNo(form.tip_violencia_fisica) }}</div>
          <div class="col-6 col-md-3">Violencia psicológica: {{ yesNo(form.tip_violencia_psicologica) }}</div>
          <div class="col-6 col-md-3">Abandono: {{ yesNo(form.tip_abandono) }}</div>
          <div class="col-6 col-md-3">Apoyo integral: {{ yesNo(form.tip_apoyo_integral) }}</div>
        </q-card-section>
      </q-card>

      <!-- 4) Datos del denunciado/a -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="person_off" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">4) Datos del denunciado/a</div>
        </q-card-section>
        <q-separator/>
        <q-card-section class="row q-col-gutter-md">
          <div class="col-12 col-md-6"><b>Nombre:</b> {{ nombreDenunciado }}</div>
          <div class="col-6 col-md-2"><b>Edad:</b> {{ orDash(form.den_edad) }}</div>
          <div class="col-12 col-md-4"><b>Domicilio:</b> {{ orDash(form.den_domicilio) }}</div>
          <div class="col-6 col-md-3"><b>Estado civil:</b> {{ orDash(form.den_estado_civil) }}</div>
          <div class="col-6 col-md-3"><b>Idioma:</b> {{ orDash(form.den_idioma) }}</div>
          <div class="col-6 col-md-3"><b>Grado de instrucción:</b> {{ orDash(form.den_grado_instruccion) }}</div>
          <div class="col-6 col-md-3"><b>Ocupación:</b> {{ orDash(form.den_ocupacion) }}</div>
        </q-card-section>
      </q-card>

      <!-- 5) Seguimiento -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">5) Seguimiento</div>
        </q-card-section>
        <q-separator/>
        <q-card-section class="row q-col-gutter-md">
          <div class="col-12 col-md-4"><b>Psicología:</b> {{ nombreUsuario(form.psicologica_user, form.psicologica_user_id, psicologos) }}</div>
          <div class="col-12 col-md-4"><b>Trabajo social:</b> {{ nombreUsuario(form.trabajo_social_user, form.trabajo_social_user_id, sociales) }}</div>
          <div class="col-12 col-md-4"><b>Legal:</b> {{ nombreUsuario(form.legal_user, form.legal_user_id, abogados) }}</div>
        </q-card-section>
      </q-card>

      <!-- 6) Check documentos adjuntos -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="inventory_2" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">6) Check documentos adjuntos</div>
        </q-card-section>
        <q-separator/>
        <q-card-section class="row q-col-gutter-md">
          <div class="col-6 col-md-3">Fotocopia CI denunciante: {{ yesNo(form.doc_ci) }}</div>
          <div class="col-6 col-md-3">Foto frontal denunciado: {{ yesNo(form.doc_frontal_denunciado) }}</div>
          <div class="col-6 col-md-3">Foto frontal denunciante: {{ yesNo(form.doc_frontal_denunciante) }}</div>
          <div class="col-6 col-md-3">Croquis del hecho: {{ yesNo(form.doc_croquis) }}</div>
        </q-card-section>
      </q-card>
    </template>

    <!-- ========= MODO EDICIÓN ========= -->
    <template v-else>
      <!-- 1) Datos del caso (S.L.A.M.) -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="description" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Datos del caso (S.L.A.M.)</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-input v-model="form.numero_apoyo_integral" dense outlined clearable label="Nº Apoyo Integral"/>
            </div>
            <div class="col-12 col-md-3">
              <q-input v-model="form.numero_caso" dense outlined clearable label="Nº de caso"/>
            </div>
            <div class="col-12 col-md-3">
              <q-input v-model="form.fecha_registro" type="date" dense outlined label="Fecha registro"/>
            </div>

            <div class="col-12 text-subtitle2 q-mt-md">Idiomas (AM - marcadores generales)</div>
            <div class="col-6 col-md-2"><q-toggle v-model="form.am_idioma_castellano" label="Castellano"/></div>
            <div class="col-6 col-md-2"><q-toggle v-model="form.am_idioma_quechua" label="Quechua"/></div>
            <div class="col-6 col-md-2"><q-toggle v-model="form.am_idioma_aymara" label="Aymara"/></div>
            <div class="col-12 col-md-6"><q-input v-model="form.am_idioma_otros" dense outlined clearable label="Otros idiomas"/></div>

            <div class="col-12 text-subtitle2 q-mt-sm">Teléfonos de referencia</div>
            <div class="col-12 col-md-4"><q-input v-model="form.ref_tel_fijo" dense outlined label="Tel. fijo"/></div>
            <div class="col-12 col-md-4"><q-input v-model="form.ref_tel_movil" dense outlined label="Tel. móvil"/></div>
            <div class="col-12 col-md-4"><q-input v-model="form.ref_tel_movil_alt" dense outlined label="Tel. móvil (alt)"/></div>

            <div class="col-12 text-subtitle2 q-mt-sm">Ubicación (lat/lng)</div>
            <div class="col-12">
              <MapPicker v-model="mapModel" :center="oruroCenter" :zoom-init="13"/>
            </div>

            <div class="col-12 text-subtitle2 q-mt-sm">Hecho y Tipología</div>
            <div class="col-12">
              <q-input
                v-model="form.hecho_descripcion"
                type="textarea"
                autogrow
                outlined dense
                clearable
                label="Breve circunstancia del hecho / denuncia"
                maxlength="3000"
              />
            </div>
            <div class="col-6 col-md-3"><q-toggle v-model="form.tip_violencia_fisica" label="Violencia física"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="form.tip_violencia_psicologica" label="Violencia psicológica"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="form.tip_abandono" label="Abandono"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="form.tip_apoyo_integral" label="Apoyo integral"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 4) Datos del denunciado/a -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="person_off" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">4) Datos del denunciado/a</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4"><q-input v-model="form.den_nombres" dense outlined clearable label="Nombres"/></div>
            <div class="col-6 col-md-2"><q-input v-model="form.den_paterno" dense outlined clearable label="Paterno"/></div>
            <div class="col-6 col-md-2"><q-input v-model="form.den_materno" dense outlined clearable label="Materno"/></div>
            <div class="col-6 col-md-2"><q-input v-model="form.den_edad" dense outlined clearable label="Edad"/></div>
            <div class="col-12 col-md-4"><q-input v-model="form.den_domicilio" dense outlined clearable label="Domicilio"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.den_estado_civil" dense outlined clearable label="Estado civil"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.den_idioma" dense outlined clearable label="Idioma"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.den_grado_instruccion" dense outlined clearable label="Grado de instrucción"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.den_ocupacion" dense outlined clearable label="Ocupación"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 5) Seguimiento (responsables) -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">5) Seguimiento</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-select v-model="form.psicologica_user_id" dense outlined emit-value map-options clearable
                        :options="optPsicologos" label="Área psicológica (responsable)"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="form.trabajo_social_user_id" dense outlined emit-value map-options clearable
                        :options="optSociales" label="Área social (responsable)"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="form.legal_user_id" dense outlined emit-value map-options clearable
                        :options="optAbogados" label="Área legal (responsable)"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 6) Check documentos adjuntos -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="inventory_2" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">6) Check documentos adjuntos</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-2"><q-checkbox v-model="form.doc_ci" label="Fotocopia CI denunciante"/></div>
            <div class="col-12 col-md-2"><q-checkbox v-model="form.doc_frontal_denunciado" label="Foto frontal denunciado"/></div>
            <div class="col-12 col-md-2"><q-checkbox v-model="form.doc_frontal_denunciante" label="Foto frontal denunciante"/></div>
            <div class="col-12 col-md-2"><q-checkbox v-model="form.doc_croquis" label="Croquis del hecho"/></div>
          </div>
        </q-card-section>
      </q-card>
    </template>
  </div>
</template>

<script>
import MapPicker from 'components/MapPicker.vue'

export default {
  name: 'InfoGeneral',
  components: { MapPicker },
  props: {
    caseId: { type: [String, Number], required: true },
    caso:   { type: Object, default: () => null }   // ← viene del padre
  },
  data () {
    return {
      loading: false,
      editing: false,
      backup: null,

      // geocentro
      oruroCenter: [-17.9667, -67.1167],

      // responsables
      psicologos: [],
      abogados: [],
      sociales: [],

      // form local (clon del slam)
      form: {}
    }
  },
  computed: {
    canEdit () {
      const r = this.$store.user?.role || ''
      return r === 'Administrador' || r === 'Asistente'
    },

    nombreDenunciado () {
      const f = this.form || {}
      const full = [f.den_nombres, f.den_paterno, f.den_materno].filter(Boolean).join(' ').trim()
      return full || '—'
    },

    // opciones selects de responsables
    optPsicologos () { return this.psicologos.map(u => ({ label: u.name, value: u.id })) },
    optAbogados  () { return this.abogados .map(u => ({ label: u.name, value: u.id })) },
    optSociales  () { return this.sociales .map(u => ({ label: u.name, value: u.id })) },

    // v-model MapPicker (usa am_latitud/am_longitud)
    mapModel: {
      get () { return { latitud: this.form.am_latitud, longitud: this.form.am_longitud } },
      set (v) {
        this.form.am_latitud  = v?.latitud ?? v?.lat ?? null
        this.form.am_longitud = v?.longitud ?? v?.lng ?? null
      }
    }
  },
  watch: {
    // cuando el padre actualiza 'caso', clonar a form (tomando caso.slam si viene anidado)
    caso: {
      immediate: true,
      deep: true,
      handler (val) {
        if (!val) return
        const data = val.slam ? val.slam : val
        // clonar y normalizar booleanos 0/1→true/false para toggles/checkboxes
        const norm = this.normalizeFromBackend(data)
        // set form
        this.form = { ...norm }

        // normalizar ids desde relaciones (si faltan)
        if (!this.form.psicologica_user_id && data.psicologica_user?.id) this.form.psicologica_user_id = data.psicologica_user.id
        if (!this.form.trabajo_social_user_id && data.trabajo_social_user?.id) this.form.trabajo_social_user_id = data.trabajo_social_user.id
        if (!this.form.legal_user_id && data.legal_user?.id)         this.form.legal_user_id = data.legal_user.id
      }
    }
  },
  created () {
    this.fetchRoles()
  },
  methods: {
    // ===== utilidades =====
    orDash (v) { return (v !== null && v !== undefined && v !== '') ? v : '—' },
    yesNo (v) { return (v === true || v === 1 || v === '1') ? 'Sí' : 'No' },

    nombreUsuario (relObj, id, arr) {
      if (relObj && relObj.name) return relObj.name
      const f = (arr || []).find(x => Number(x.id) === Number(id))
      return f ? (f.name || f.username || '—') : '—'
    },

    normalizeFromBackend (o = {}) {
      // convierte 0/1 a boolean para los campos check/toggle
      const boolKeys = [
        'am_idioma_castellano','am_idioma_quechua','am_idioma_aymara',
        'tip_violencia_fisica','tip_violencia_psicologica','tip_abandono','tip_apoyo_integral',
        'doc_ci','doc_frontal_denunciado','doc_frontal_denunciante','doc_croquis'
      ]
      const out = { ...o }
      boolKeys.forEach(k => { if (k in out) out[k] = (out[k] === 1 || out[k] === true || out[k] === '1') })
      return out
    },

    prepareForSave (o = {}) {
      // convierte boolean → 0/1 para enviar homogéneo
      const boolKeys = [
        'am_idioma_castellano','am_idioma_quechua','am_idioma_aymara',
        'tip_violencia_fisica','tip_violencia_psicologica','tip_abandono','tip_apoyo_integral',
        'doc_ci','doc_frontal_denunciado','doc_frontal_denunciante','doc_croquis'
      ]
      const out = { ...o }
      boolKeys.forEach(k => { if (k in out) out[k] = out[k] ? 1 : 0 })
      return out
    },

    async fetchRoles () {
      try {
        const { data } = await this.$axios.get('/usuariosRole')
        this.psicologos = data.psicologos || []
        this.abogados  = data.abogados  || []
        this.sociales  = data.sociales  || []
      } catch (e) {
        this.$alert?.error?.('No se pudo cargar los usuarios por rol') || this.$q.notify({ type:'negative', message:'No se pudo cargar los usuarios por rol' })
      }
    },

    startEdit () {
      if (!this.canEdit) return
      this.backup = JSON.parse(JSON.stringify(this.form))
      this.editing = true
    },

    cancelEdit () {
      this.form = JSON.parse(JSON.stringify(this.backup || {}))
      this.editing = false
      this.$q.notify({ type: 'info', message: 'Cambios descartados' })
    },

    async save () {
      if (!this.canEdit) return
      this.loading = true
      try {
        // prepara payload (solo slam en este tab)
        const payload = { slam: this.prepareForSave(this.form) }
        const { data } = await this.$axios.put(`/slams/${this.caseId}`, payload)
        const updated = data?.slam ? this.normalizeFromBackend(data.slam) : this.normalizeFromBackend(data)
        this.form = { ...updated }
        this.editing = false
        this.$alert?.success?.('Información general actualizada') || this.$q.notify({ type:'positive', message:'Información general actualizada' })
        this.$emit('update') // para que el padre haga fetchSlam()
      } catch (e) {
        this.$alert?.error?.(e?.response?.data?.message || 'No se pudo actualizar') || this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'No se pudo actualizar' })
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.section-card { border-radius: 14px; overflow: hidden; margin-bottom: 16px; background: #fff; }
.ellipsis-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
</style>
