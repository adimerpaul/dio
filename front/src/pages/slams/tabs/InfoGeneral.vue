<template>
  <div>
    <!-- ======= HEADER / ACCIONES ======= -->
    <div class="row items-center q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Información General</div>
      </div>
      <div class="col-auto row q-gutter-sm" v-if="canEdit">
        <q-btn v-if="!editing" color="primary" icon="edit" label="Editar" @click="startEdit" :loading="loading"/>
        <template v-else>
          <q-btn flat color="negative" icon="close" label="Cancelar" @click="cancelEdit" :loading="loading"/>
          <q-btn color="primary" icon="save" label="Guardar" @click="save" :loading="loading"/>
        </template>
      </div>
    </div>

    <!-- ========= MODO LECTURA (roles sin permiso) ========= -->
    <template v-if="!canEdit">
      <!-- Caso -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row q-col-gutter-md">
          <div class="col-6 col-md-3"><b>N° SLIM:</b> {{ orDash(form.caso_numero) }}</div>
          <div class="col-6 col-md-3"><b>Fecha del hecho:</b> {{ orDash(form.caso_fecha_hecho) }}</div>
          <div class="col-12 col-md-6"><b>Lugar del hecho:</b> {{ orDash(form.caso_lugar_hecho) }}</div>
          <div class="col-6 col-md-4"><b>Tipología:</b> {{ orDash(form.caso_tipologia) }}</div>
          <div class="col-6 col-md-4"><b>Modalidad:</b> {{ orDash(form.caso_modalidad) }}</div>
          <div class="col-12"><b>Descripción:</b> <span class="ellipsis-3">{{ orDash(form.caso_descripcion) }}</span></div>
        </q-card-section>
      </q-card>

      <!-- Denunciante -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="assignment_ind" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">Denunciante</div>
        </q-card-section>
        <q-separator/>
        <q-card-section class="row q-col-gutter-md">
          <div class="col-6 col-md-2"><b>Área:</b> {{ orDash(form.area) }}</div>
          <div class="col-6 col-md-2"><b>Zona:</b> {{ orDash(form.zona) }}</div>
          <div class="col-12 col-md-6"><b>Nombre:</b> {{ nombreDenunciante }}</div>
          <div class="col-6 col-md-3"><b>Documento:</b> {{ orDash(form.denunciante_documento) }}</div>
          <div class="col-6 col-md-3"><b>Nro:</b> {{ orDash(form.denunciante_nro) }}</div>
          <div class="col-6 col-md-3"><b>Sexo:</b> {{ orDash(form.denunciante_sexo) }}</div>
          <div class="col-6 col-md-3"><b>Estado civil:</b> {{ orDash(form.denunciante_estado_civil) }}</div>
          <div class="col-6 col-md-3"><b>Edad:</b> {{ orDash(form.denunciante_edad) }}</div>
          <div class="col-6 col-md-3"><b>Teléfono:</b> {{ orDash(form.denunciante_telefono) }}</div>
          <div class="col-6 col-md-3"><b>Idioma:</b> {{ orDash(form.denunciante_idioma) }}</div>
          <div class="col-6 col-md-3"><b>Ocupación:</b> {{ orDash(form.denunciante_ocupacion) }}</div>
          <div class="col-12"><b>Domicilio:</b> {{ orDash(form.denunciante_domicilio_actual) }}</div>
        </q-card-section>
      </q-card>

      <!-- Denunciado -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="person_pin_circle" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">Denunciado</div>
        </q-card-section>
        <q-separator/>
        <q-card-section class="row q-col-gutter-md">
          <div class="col-12 col-md-6"><b>Nombre:</b> {{ orDash(form.denunciado_nombre_completo) }}</div>
          <div class="col-6 col-md-3"><b>Documento:</b> {{ orDash(form.denunciado_documento) }}</div>
          <div class="col-6 col-md-3"><b>Nro:</b> {{ orDash(form.denunciado_nro) }}</div>
          <div class="col-6 col-md-3"><b>Sexo:</b> {{ orDash(form.denunciado_sexo) }}</div>
          <div class="col-6 col-md-3"><b>Edad:</b> {{ orDash(form.denunciado_edad) }}</div>
          <div class="col-6 col-md-3"><b>Teléfono:</b> {{ orDash(form.denunciado_telefono) }}</div>
          <div class="col-6 col-md-3"><b>Grado:</b> {{ orDash(form.denunciado_grado) }}</div>
          <div class="col-12"><b>Domicilio:</b> {{ orDash(form.denunciado_domicilio_actual) }}</div>
        </q-card-section>
      </q-card>

      <!-- Seguimiento (solo lectura) -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">Seguimiento</div>
        </q-card-section>
        <q-separator/>
        <q-card-section class="row q-col-gutter-md">
          <div class="col-12 col-md-4"><b>Psicología:</b> {{ nombreUsuario(form.psicologica_user, form.psicologica_user_id, psicologos) }}</div>
          <div class="col-12 col-md-4"><b>Trabajo social:</b> {{ nombreUsuario(form.trabajo_social_user, form.trabajo_social_user_id, sociales) }}</div>
          <div class="col-12 col-md-4"><b>Legal:</b> {{ nombreUsuario(form.legal_user, form.legal_user_id, abogados) }}</div>
        </q-card-section>
      </q-card>
    </template>

    <!-- ========= MODO FORMULARIO (Admin/Asistente) ========= -->
    <template v-else>
      <!-- Caso -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-input v-model="form.caso_numero" :readonly="true" dense outlined label="Nro de SLIM"/>
            </div>
            <div class="col-12 col-md-3">
              <q-input v-model="form.caso_fecha_hecho" :readonly="!editing" type="date" dense outlined label="Fecha del hecho"/>
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model="form.caso_lugar_hecho" :readonly="!editing" dense outlined clearable label="Lugar del hecho"/>
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model="form.caso_tipologia" :readonly="!editing" dense outlined clearable label="Tipología"/>
            </div>
<!--            <div class="col-12 col-md-4">-->
<!--              <q-input v-model="form.caso_modalidad" :readonly="!editing" dense outlined clearable label="Modalidad"/>-->
<!--            </div>-->
            <div class="col-12">
              <q-input v-model="form.caso_descripcion" :readonly="!editing" type="textarea" autogrow outlined dense clearable
                       label="Descripción del hecho" counter maxlength="3000"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Denunciante -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="assignment_ind" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">Denunciante</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-2">
              <q-select v-model="form.area" dense outlined :readonly="!editing" :options="$areas" label="Área *" :rules="[req]"/>
            </div>
            <div class="col-12 col-md-2">
              <q-select v-model="form.zona" dense outlined :readonly="!editing" :options="$zonas" label="Zona *" :rules="[req]"/>
            </div>
            <div class="col-12 col-md-2">
              <q-input v-model="form.denunciante_nombres" dense outlined clearable :readonly="!editing" label="Nombres *" :rules="[req]"/>
            </div>
            <div class="col-12 col-md-2">
              <q-input v-model="form.denunciante_paterno" dense outlined clearable :readonly="!editing" label="Apellido paterno"/>
            </div>
            <div class="col-12 col-md-2">
              <q-input v-model="form.denunciante_materno" dense outlined clearable :readonly="!editing" label="Apellido materno"/>
            </div>

            <div class="col-6 col-md-3">
              <q-input v-model="form.denunciante_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento" :readonly="!editing"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="form.denunciante_fecha_nacimiento" type="date" dense outlined
                       label="Fecha de nacimiento" :readonly="!editing"
                       @update:model-value="onBirthChange('denunciante')"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model.number="form.denunciante_edad" dense outlined type="number" label="Edad" :readonly="!editing"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="form.denunciante_telefono" dense outlined clearable label="Teléfono/Celular" :readonly="!editing"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="form.denunciante_grado" dense outlined clearable label="Grado de instrucción" :readonly="!editing"/>
            </div>

            <div class="col-6 col-md-3">
              <q-input v-model="form.denunciante_documento" :readonly="!editing" dense outlined clearable label="Documento"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="form.denunciante_nro" :readonly="!editing" dense outlined clearable label="Nro documento"/>
            </div>

            <div class="col-6 col-md-3"><q-input v-model="form.denunciante_sexo" :readonly="!editing" dense outlined clearable label="Sexo"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciante_estado_civil" :readonly="!editing" dense outlined clearable label="Estado civil"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciante_residencia" :readonly="!editing" dense outlined clearable label="Residencia"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciante_idioma" :readonly="!editing" dense outlined clearable label="Idioma"/></div>

            <div class="col-12 col-md-3">
              <q-toggle v-model="form.denunciante_trabaja" :disable="!editing" label="Trabaja"/>
            </div>
            <div class="col-12 col-md-3">
              <q-input v-model="form.denunciante_ocupacion" :readonly="!editing" dense outlined clearable label="Ocupación"/>
            </div>

            <div class="col-12">
              <q-input v-model="form.denunciante_domicilio_actual" :readonly="!editing" dense outlined clearable label="Domicilio actual"/>
            </div>

            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (denunciante)</div>
              <MapPicker v-model="denunciantePos" :center="oruroCenter" />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Denunciado -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="person_pin_circle" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">Denunciado</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input v-model="form.denunciado_nombre_completo" :readonly="!editing" dense outlined clearable label="Nombre completo"/>
            </div>
            <div class="col-6 col-md-3">
              <q-select v-model="form.denunciado_documento" dense outlined :readonly="!editing"
                        emit-value map-options :options="documentos" label="Documento"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="form.denunciado_nro" :readonly="!editing" dense outlined clearable label="Nro documento"/>
            </div>

            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_sexo" :readonly="!editing" dense outlined clearable label="Sexo"/></div>
            <div class="col-6 col-md-3">
              <q-input v-model="form.denunciado_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento" :readonly="!editing"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="form.denunciado_fecha_nacimiento" type="date" dense outlined
                       label="Fecha de nacimiento" :readonly="!editing"
                       @update:model-value="onBirthChange('denunciado')"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model.number="form.denunciado_edad" dense outlined type="number" label="Edad" :readonly="!editing"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="form.denunciado_telefono" dense outlined clearable label="Teléfono/Celular" :readonly="!editing"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="form.denunciado_grado" dense outlined clearable label="Grado de instrucción" :readonly="!editing"/>
            </div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_residencia" :readonly="!editing" dense outlined clearable label="Residencia"/></div>

            <div class="col-12">
              <q-input v-model="form.denunciado_domicilio_actual" :readonly="!editing" dense outlined clearable label="Domicilio actual"/>
            </div>

            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (denunciado)</div>
              <MapPicker v-model="denunciadoPos" :center="oruroCenter" />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Seguimiento (editable) -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">Seguimiento</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-select v-model="form.psicologica_user_id" dense outlined emit-value map-options
                        :options="optPsicologos" label="Área psicológica (responsable)" :readonly="!editing"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="form.trabajo_social_user_id" dense outlined emit-value map-options
                        :options="optSociales" label="Área social (responsable)" :readonly="!editing"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="form.legal_user_id" dense outlined emit-value map-options
                        :options="optAbogados" label="Área legal (responsable)" :readonly="!editing"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Check de documentos -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">Check documentos adjuntos</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-2">
              <q-checkbox v-model="form.documento_fotocopia_carnet_denunciante" label="Fotocopia CI denunciante"
                          :false-value="'0'" :true-value="'1'" :disable="!editing"/>
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="form.documento_fotocopia_carnet_denunciado" label="Fotocopia CI denunciado"
                          :false-value="'0'" :true-value="'1'" :disable="!editing"/>
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="form.documento_placas_fotograficas_domicilio_denunciante" label="Placas fotográficas dom. denunciante"
                          :false-value="'0'" :true-value="'1'" :disable="!editing"/>
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="form.documento_croquis_direccion_denunciado" label="Croquis dirección denunciado"
                          :false-value="'0'" :true-value="'1'" :disable="!editing"/>
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="form.documento_placas_fotograficas_domicilio_denunciado" label="Placas fotográficas dom. denunciado"
                          :false-value="'0'" :true-value="'1'" :disable="!editing"/>
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="form.documento_ciudadania_digital" label="Ciudadanía digital"
                          :false-value="'0'" :true-value="'1'" :disable="!editing"/>
            </div>
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
  props: { caseId: { type: [String, Number], required: true } },
  data () {
    return {
      documentos: [
        { label: 'Carnet de identidad', value: 'Carnet de identidad' },
        { label: 'Pasaporte', value: 'Pasaporte' },
        { label: 'Libreta de servicio militar', value: 'Libreta de servicio militar' },
        { label: 'Libreta de conducir', value: 'Libreta de conducir' }
      ],
      loading: false,
      editing: false,
      form: {},
      backup: null,
      oruroCenter: [-17.9667, -67.1167],
      // responsables
      psicologos: [],
      abogados: [],
      sociales: []
    }
  },
  computed: {
    canEdit () {
      const r = this.$store.user?.role || ''
      return r === 'Administrador' || r === 'Asistente'
    },
    denunciantePos: {
      get () { return { latitud: this.form.latitud, longitud: this.form.longitud } },
      set (v) { this.form.latitud = v.latitud; this.form.longitud = v.longitud }
    },
    denunciadoPos: {
      get () { return { latitud: this.form.denunciado_latitud, longitud: this.form.denunciado_longitud } },
      set (v) { this.form.denunciado_latitud = v.latitud; this.form.denunciado_longitud = v.longitud }
    },
    optPsicologos () { return this.psicologos.map(u => ({ label: u.name, value: u.id })) },
    optAbogados  () { return this.abogados .map(u => ({ label: u.name, value: u.id })) },
    optSociales  () { return this.sociales .map(u => ({ label: u.name, value: u.id })) },
    nombreDenunciante () {
      const f = this.form || {}
      const full = f.denunciante_nombre_completo && f.denunciante_nombre_completo.trim()
      if (full) return full
      return [f.denunciante_nombres, f.denunciante_paterno, f.denunciante_materno].filter(Boolean).join(' ') || '—'
    }
  },
  created () {
    this.fetch()
    this.fetchRoles()
  },
  methods: {
    req (v) { return !!v || 'Requerido' },
    orDash (v) { return v ? v : '—' },
    nombreUsuario (relObj, id, arr) {
      if (relObj && relObj.name) return relObj.name
      const f = (arr || []).find(x => Number(x.id) === Number(id))
      return f ? (f.name || f.name) : '—'
    },
    onBirthChange (who) {
      const key = who === 'denunciado' ? 'denunciado' : 'denunciante'
      const v = this.form[`${key}_fecha_nacimiento`]
      if (!v) { this.form[`${key}_edad`] = ''; return }
      const birthDate = new Date(v)
      const ageDifMs = Date.now() - birthDate.getTime()
      const ageDate = new Date(ageDifMs)
      this.form[`${key}_edad`] = Math.abs(ageDate.getUTCFullYear() - 1970)
    },
    async fetchRoles () {
      try {
        const { data } = await this.$axios.get('/usuariosRole')
        this.psicologos = data.psicologos || []
        this.abogados  = data.abogados  || []
        this.sociales  = data.sociales  || []
      } catch (e) {
        this.$alert.error('No se pudo cargar los usuarios por rol')
      }
    },
    async fetch () {
      this.loading = true
      try {
        // RUTA MIGRADA A /slims
        const res = await this.$axios.get(`/slims/${this.caseId}`)
        this.form = { ...res.data }

        // Inicializar IDs de responsables desde relaciones si faltan
        if (!this.form.psicologica_user_id && this.form.psicologica_user?.id) {
          this.form.psicologica_user_id = this.form.psicologica_user.id
        }
        if (!this.form.trabajo_social_user_id && this.form.trabajo_social_user?.id) {
          this.form.trabajo_social_user_id = this.form.trabajo_social_user.id
        }
        if (!this.form.legal_user_id && this.form.legal_user?.id) {
          this.form.legal_user_id = this.form.legal_user.id
        }

        this.backup = { ...this.form }
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo cargar el SLIM')
      } finally {
        this.loading = false
      }
    },
    startEdit () {
      if (!this.canEdit) return
      this.editing = true
      this.backup = { ...this.form }
    },
    cancelEdit () {
      this.form = { ...this.backup }
      this.editing = false
      this.$q.notify({ type: 'info', message: 'Cambios descartados' })
    },
    async save () {
      if (!this.canEdit) return
      this.loading = true
      try {
        // RUTA MIGRADA A /slims
        const res = await this.$axios.put(`/slims/${this.caseId}`, this.form)
        this.form = { ...res.data.data }
        // refrescar relaciones si el backend las retorna
        this.backup = { ...this.form }
        this.editing = false
        this.$alert.success('Información general actualizada')
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo actualizar')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.section-card {
  border-radius: 14px;
  overflow: hidden;
  margin-bottom: 16px;
  background: #fff;
}
.ellipsis-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
