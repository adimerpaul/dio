<template>
  <div>
    <div class="row items-center q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Información General</div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn v-if="!editing" color="primary" icon="edit" label="Editar" @click="startEdit" :loading="loading"/>
        <template v-else>
          <q-btn flat color="negative" icon="close" label="Cancelar" @click="cancelEdit" :loading="loading"/>
          <q-btn color="primary" icon="save" label="Guardar" @click="save" :loading="loading"/>
        </template>
      </div>
    </div>

    <q-card flat bordered class="section-card q-mb-md">
      <q-card-section>
        <div class="row q-col-gutter-md">
          <div class="col-12 col-md-3">
            <q-input v-model="form.caso_numero" :readonly="true" dense outlined clearable label="Nro de caso"/>
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
          <div class="col-12 col-md-4">
            <q-input v-model="form.caso_modalidad" :readonly="!editing" dense outlined clearable label="Modalidad"/>
          </div>
          <div class="col-12">
            <q-input v-model="form.caso_descripcion" :readonly="!editing" type="textarea" autogrow outlined dense clearable
                     label="Descripción del hecho" counter maxlength="1000"/>
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
<!--          <div class="col-12 col-md-6">-->
<!--            <q-input v-model="form.denunciante_nombre_completo" :readonly="!editing" dense outlined clearable label="Nombre completo"/>-->
<!--          </div>-->
          <div class="col-12 col-md-2">
            <q-select v-model="form.area" dense outlined :readonly="!editing"
                      :options="$areas"
                      label="Área *" :rules="[req]"/>
          </div>
          <div class="col-12 col-md-2">
            <q-select v-model="form.zona" dense outlined :readonly="!editing"
                      :options="$zonas"
                      label="Zona *" :rules="[req]"/>
          </div>
          <div class="col-12 col-md-2">
            <q-input v-model="form.denunciante_nombres" dense outlined clearable :readonly="!editing"
                     label="Nombres *" :rules="[req]"/>
          </div>
          <div class="col-12 col-md-2">
            <q-input v-model="form.denunciante_paterno" dense outlined clearable :readonly="!editing"
                     label="Apellido paterno"/>
          </div>
          <div class="col-12 col-md-2">
            <q-input v-model="form.denunciante_materno" dense outlined clearable :readonly="!editing"
                     label="Apellido materno"/>
          </div>
          <div class="col-6 col-md-3">
            <q-input v-model="form.denunciante_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento" :readonly="!editing"/>
          </div>
          <div class="col-6 col-md-3">
            <q-input v-model="form.denunciante_fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento" @update:model-value="(v) => {
                if (v) {
                  const birthDate = new Date(v)
                  const ageDifMs = Date.now() - birthDate.getTime()
                  const ageDate = new Date(ageDifMs)
                  this.form.denunciante_edad = Math.abs(ageDate.getUTCFullYear() - 1970)
                } else {
                  this.form.denunciante_edad = ''
                }
              }"
                     :readonly="!editing"
            />
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
    <q-card flat bordered class="section-card q-mb-xl">
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
<!--            <q-input v-model="form.denunciado_documento" :readonly="!editing" dense outlined clearable label="Documento"/>-->
            <q-select v-model="form.denunciado_documento" dense outlined :readonly="!editing"
                      emit-value
                      map-options
                      :options="documentos"
                      label="Documento"/>
          </div>
          <div class="col-6 col-md-3">
            <q-input v-model="form.denunciado_nro" :readonly="!editing" dense outlined clearable label="Nro documento"/>
          </div>

          <div class="col-6 col-md-3"><q-input v-model="form.denunciado_sexo" :readonly="!editing" dense outlined clearable label="Sexo"/></div>
          <div class="col-6 col-md-3">
            <q-input v-model="form.denunciado_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/>
          </div>
          <div class="col-6 col-md-3">
            <q-input v-model="form.denunciado_fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento" @update:model-value="(v) => {
                if (v) {
                  const birthDate = new Date(v)
                  const ageDifMs = Date.now() - birthDate.getTime()
                  const ageDate = new Date(ageDifMs)
                  this.form.denunciado_edad = Math.abs(ageDate.getUTCFullYear() - 1970)
                } else {
                  this.form.denunciado_edad = ''
                }
              }"/>
          </div>
          <div class="col-6 col-md-3">
            <q-input v-model.number="form.denunciado_edad" dense outlined type="number" label="Edad"/>
          </div>
          <div class="col-6 col-md-3">
            <q-input v-model="form.denunciado_telefono" dense outlined clearable label="Teléfono/Celular"/>
          </div>
          <div class="col-6 col-md-3">
            <q-input v-model="form.denunciado_grado" dense outlined clearable label="Grado de instrucción"/>
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
          <q-card-section>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-2">
                <q-checkbox v-model="form.documento_fotocopia_carnet_denunciante" label="Fotocopia CI denunciante" :false-value="'0'" :true-value="'1'"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="form.documento_fotocopia_carnet_denunciado" label="Fotocopia CI denunciado" :false-value="'0'" :true-value="'1'"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="form.documento_placas_fotograficas_domicilio_denunciante" label="Placas fotográficas domicilio denunciante" :false-value="'0'" :true-value="'1'"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="form.documento_croquis_direccion_denunciado" label="Croquis dirección denunciado" :false-value="'0'" :true-value="'1'"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="form.documento_placas_fotograficas_domicilio_denunciado" label="Placas fotográficas domicilio denunciado" :false-value="'0'" :true-value="'1'"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="form.documento_ciudadania_digital" label="Ciudadanía digital" :false-value="'0'" :true-value="'1'"/>
              </div>
            </div>
          </q-card-section>
    </q-card>
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
      sexos: [
        { label: 'Masculino', value: 'Masculino' },
        { label: 'Femenino', value: 'Femenino' },
        { label: 'Otro', value: 'Otro' }
      ],
      estadosCiviles: [
        { label: 'Soltero/a', value: 'Soltero/a' },
        { label: 'Casado/a', value: 'Casado/a' },
        { label: 'Divorciado/a', value: 'Divorciado/a' },
        { label: 'Viudo/a', value: 'Viudo/a' },
        { label: 'Unión libre', value: 'Unión libre' }
      ],
      loading: false,
      editing: false,
      form: {},
      backup: null,
      oruroCenter: [-17.9667, -67.1167]
    }
  },
  computed: {
    denunciantePos: {
      get () { return { latitud: this.form.latitud, longitud: this.form.longitud } },
      set (v) { this.form.latitud = v.latitud; this.form.longitud = v.longitud }
    },
    denunciadoPos: {
      get () { return { latitud: this.form.denunciado_latitud, longitud: this.form.denunciado_longitud } },
      set (v) { this.form.denunciado_latitud = v.latitud; this.form.denunciado_longitud = v.longitud }
    }
  },
  created () {
    this.fetch()
  },
  methods: {
    req (v) { return !!v || 'Requerido' },
    async fetch () {
      this.loading = true
      try {
        const res = await this.$axios.get(`/casos/${this.caseId}`)
        this.form = { ...res.data }
        this.backup = { ...this.form }
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo cargar el caso')
      } finally {
        this.loading = false
      }
    },
    startEdit () {
      this.editing = true
      this.backup = { ...this.form }
    },
    cancelEdit () {
      this.form = { ...this.backup }
      this.editing = false
      this.$q.notify({ type: 'info', message: 'Cambios descartados' })
    },
    async save () {
      this.loading = true
      try {
        const res = await this.$axios.put(`/casos/${this.caseId}`, this.form)
        this.form = { ...res.data.data }
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
</style>
