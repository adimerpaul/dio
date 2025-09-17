<template>
  <div>
    <!-- ======= HEADER / ACCIONES ======= -->
    <div class="row items-center q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Información General</div>
        <div class="text-caption text-grey-7">UMADI #{{ orDash(form.numero_caso) }}</div>
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

    <!-- ========= MODO LECTURA ========= -->
    <template v-if="!editing">
      <!-- 1) Adulto mayor / denunciante -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="badge" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Adulto mayor / denunciante</div>
        </q-card-section>
        <q-separator/>
        <q-card-section class="row q-col-gutter-md">
          <div class="col-6 col-md-3"><b>Nº Apoyo Integral:</b> {{ orDash(form.numero_apoyo_integral) }}</div>
          <div class="col-6 col-md-3"><b>Nº de caso:</b> {{ orDash(form.numero_caso) }}</div>
          <div class="col-6 col-md-3"><b>Fecha registro:</b> {{ orDash(form.fecha_registro) }}</div>

          <div class="col-12 text-subtitle2 q-mt-sm">Identificación</div>
          <div class="col-12 col-md-6"><b>Nombre:</b> {{ nombreDenunciante }}</div>
          <div class="col-6 col-md-3"><b>Documento:</b> {{ orDash(form.tipo_documento) }}</div>
          <div class="col-6 col-md-3"><b>Nº documento:</b> {{ orDash(form.numero_documento) }}</div>
          <div class="col-6 col-md-3"><b>Sexo:</b> {{ orDash(form.sexo) }}</div>
          <div class="col-6 col-md-3"><b>Lugar nac.:</b> {{ orDash(form.lugar_nacimiento) }}</div>
          <div class="col-6 col-md-3"><b>Fecha nac.:</b> {{ orDash(form.fecha_nacimiento) }}</div>
          <div class="col-6 col-md-3"><b>Edad:</b> {{ orDash(form.edad) }}</div>

          <div class="col-12 text-subtitle2 q-mt-sm">Contacto y dirección</div>
          <div class="col-12 col-md-6"><b>Dirección (anterior):</b> {{ orDash(form.direccion) }}</div>
          <div class="col-12 col-md-6"><b>Dirección actual:</b> {{ orDash(form.direccion_actual) }}</div>
          <div class="col-6 col-md-3"><b>Estado civil:</b> {{ orDash(form.estado_civil) }}</div>
          <div class="col-6 col-md-3"><b>Relación con denunciado:</b> {{ orDash(form.relacion_denunciado) }}</div>

          <div class="col-6 col-md-3"><b>Grado de instrucción:</b> {{ orDash(form.grado_instruccion) }}</div>
          <div class="col-6 col-md-3"><b>Trabaja:</b> {{ yesNo(form.trabaja) }}</div>
          <div class="col-6 col-md-3"><b>Ocupación:</b> {{ orDash(form.ocupacion) }}</div>
          <div class="col-6 col-md-3"><b>Edad aprox.:</b> {{ orDash(form.edad_aprox) }}</div>
          <div class="col-6 col-md-3"><b>Edad exacta:</b> {{ orDash(form.edada_exacto) }}</div>
          <div class="col-6 col-md-3"><b>Idioma:</b> {{ orDash(form.idioma) }}</div>

          <div class="col-12 text-subtitle2 q-mt-sm">Teléfonos</div>
          <div class="col-6 col-md-3"><b>Celular 1:</b> {{ orDash(form.celular1) }}</div>
          <div class="col-6 col-md-3"><b>Celular 2:</b> {{ orDash(form.celular2) }}</div>
          <div class="col-6 col-md-3"><b>Tel. fijo 1:</b> {{ orDash(form.telefono_fijo1) }}</div>
          <div class="col-6 col-md-3"><b>Tel. fijo 2:</b> {{ orDash(form.telefono_fijo2) }}</div>

          <div class="col-12 text-subtitle2 q-mt-sm">Ubicación</div>
          <div class="col-6 col-md-3"><b>Latitud:</b> {{ orDash(form.latitud) }}</div>
          <div class="col-6 col-md-3"><b>Longitud:</b> {{ orDash(form.longitud) }}</div>
        </q-card-section>
      </q-card>

      <!-- 2) Denunciado -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="person_off" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">2) Denunciado</div>
        </q-card-section>
        <q-separator/>
        <q-card-section class="row q-col-gutter-md">
          <div class="col-12 col-md-6"><b>Nombre:</b> {{ nombreDenunciado }}</div>
          <div class="col-6 col-md-2"><b>Edad:</b> {{ orDash(form.denunciado_edad) }}</div>
          <div class="col-6 col-md-2"><b>CI:</b> {{ orDash(form.denunciado_ci) }}</div>
          <div class="col-6 col-md-2"><b>Sexo:</b> {{ orDash(form.denunciado_sexo) }}</div>
          <div class="col-6 col-md-3"><b>Lugar nac.:</b> {{ orDash(form.denunciado_lugar_nacimiento) }}</div>
          <div class="col-6 col-md-3"><b>Fecha nac.:</b> {{ orDash(form.denunciado_fecha_nacimiento) }}</div>
          <div class="col-12 col-md-6"><b>Dirección:</b> {{ orDash(form.denunciado_direccion) }}</div>
          <div class="col-6 col-md-3"><b>Estado civil:</b> {{ orDash(form.denunciado_estado_civil) }}</div>
          <div class="col-6 col-md-3"><b>Idioma:</b> {{ orDash(form.denunciado_idioma) }}</div>
          <div class="col-6 col-md-3"><b>Grado de instrucción:</b> {{ orDash(form.denunciado_grado_instruccion) }}</div>
          <div class="col-6 col-md-3"><b>Ocupación:</b> {{ orDash(form.denunciado_ocupacion) }}</div>
          <div class="col-6 col-md-3"><b>Celular 1:</b> {{ orDash(form.denunciado_celular1) }}</div>
          <div class="col-6 col-md-3"><b>Celular 2:</b> {{ orDash(form.denunciado_celular2) }}</div>
          <div class="col-6 col-md-3"><b>Tel. fijo 1:</b> {{ orDash(form.denunciado_telefono_fijo1) }}</div>
          <div class="col-6 col-md-3"><b>Tel. fijo 2:</b> {{ orDash(form.denunciado_telefono_fijo2) }}</div>
          <div class="col-12 col-md-6"><b>Dirección actual:</b> {{ orDash(form.denunciado_direccion_actual) }}</div>
        </q-card-section>
      </q-card>

      <!-- 3) Hecho / denuncia -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="description" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">3) Hecho / denuncia</div>
        </q-card-section>
        <q-separator/>
        <q-card-section class="row q-col-gutter-md">
          <div class="col-6 col-md-3"><b>Fecha del hecho:</b> {{ orDash(form.fecha_hecho) }}</div>
          <div class="col-6 col-md-4"><b>Relación con denunciante:</b> {{ orDash(form.relacion_denunciante) }}</div>
          <div class="col-12 col-md-5"><b>Dirección del hecho:</b> {{ orDash(form.direccion_hecho) }}</div>
          <div class="col-12"><b>Descripción:</b> <span class="ellipsis-3">{{ orDash(form.descripcion_hecho) }}</span></div>
        </q-card-section>
      </q-card>

      <!-- 4) Seguimiento -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">4) Seguimiento</div>
        </q-card-section>
        <q-separator/>
        <q-card-section class="row q-col-gutter-md">
          <div class="col-12 col-md-4"><b>Psicología:</b> {{ nombreUsuario(form.psicologica_user, form.psicologica_user_id, psicologos) }}</div>
          <div class="col-12 col-md-4"><b>Trabajo social:</b> {{ nombreUsuario(form.trabajo_social_user, form.trabajo_social_user_id, sociales) }}</div>
          <div class="col-12 col-md-4"><b>Legal:</b> {{ nombreUsuario(form.legal_user, form.legal_user_id, abogados) }}</div>
        </q-card-section>
      </q-card>

      <!-- 5) Check documentos -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="inventory_2" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">5) Check documentos adjuntos</div>
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
      <!-- 1) Adulto mayor / denunciante -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="badge" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Adulto mayor / denunciante</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3"><q-input v-model="form.numero_apoyo_integral" dense outlined clearable label="Nº Apoyo Integral"/></div>
            <div class="col-12 col-md-3"><q-input v-model="form.numero_caso" dense outlined clearable label="Nº de caso"/></div>
            <div class="col-12 col-md-3"><q-input v-model="form.fecha_registro" type="date" dense outlined label="Fecha registro"/></div>

            <div class="col-12 text-subtitle2 q-mt-sm">Identificación</div>
            <div class="col-12 col-md-3"><q-input v-model="form.nombres" dense outlined clearable label="Nombres"/></div>
            <div class="col-6 col-md-2"><q-input v-model="form.paterno" dense outlined clearable label="Paterno"/></div>
            <div class="col-6 col-md-2"><q-input v-model="form.materno" dense outlined clearable label="Materno"/></div>
            <div class="col-6 col-md-2"><q-input v-model="form.tipo_documento" dense outlined clearable label="Documento"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.numero_documento" dense outlined clearable label="Nº documento"/></div>

            <div class="col-6 col-md-2"><q-input v-model="form.sexo" dense outlined clearable label="Sexo"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento" @update:model-value="onBirthChange('denunciante')"/></div>
            <div class="col-6 col-md-2"><q-input v-model="form.edad" dense outlined clearable label="Edad"/></div>

            <div class="col-12 col-md-6"><q-input v-model="form.direccion" dense outlined clearable label="Dirección"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.estado_civil" dense outlined clearable label="Estado civil"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.relacion_denunciado" dense outlined clearable label="Relación con denunciado"/></div>

            <div class="col-6 col-md-3"><q-input v-model="form.grado_instruccion" dense outlined clearable label="Grado de instrucción"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="form.trabaja" label="Trabaja"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.ocupacion" dense outlined clearable label="Ocupación"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.edad_aprox" dense outlined clearable label="Edad aprox."/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.edada_exacto" dense outlined clearable label="Edad exacta"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.idioma" dense outlined clearable label="Idioma"/></div>

            <div class="col-12 text-subtitle2 q-mt-sm">Teléfonos</div>
            <div class="col-6 col-md-3"><q-input v-model="form.celular1" dense outlined clearable label="Celular 1"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.celular2" dense outlined clearable label="Celular 2"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.telefono_fijo1" dense outlined clearable label="Teléfono fijo 1"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.telefono_fijo2" dense outlined clearable label="Teléfono fijo 2"/></div>

            <div class="col-12 col-md-9"><q-input v-model="form.direccion_actual" dense outlined clearable label="Dirección actual"/></div>

            <div class="col-12 text-subtitle2 q-mt-sm">Ubicación (lat/lng)</div>
            <div class="col-12">
              <MapPicker v-model="mapModel" :center="oruroCenter" :zoom-init="13"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 2) Denunciado -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="person_off" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">2) Denunciado</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4"><q-input v-model="form.denunciado_nombres" dense outlined clearable label="Nombres"/></div>
            <div class="col-6 col-md-2"><q-input v-model="form.denunciado_paterno" dense outlined clearable label="Paterno"/></div>
            <div class="col-6 col-md-2"><q-input v-model="form.denunciado_materno" dense outlined clearable label="Materno"/></div>
            <div class="col-6 col-md-2"><q-input v-model="form.denunciado_edad" dense outlined clearable label="Edad"/></div>
            <div class="col-6 col-md-2"><q-input v-model="form.denunciado_ci" dense outlined clearable label="CI"/></div>

            <div class="col-6 col-md-2"><q-input v-model="form.denunciado_ciudad_nacimiento" dense outlined clearable label="Ciudad nac."/></div>
            <div class="col-6 col-md-2"><q-input v-model="form.denunciado_sexo" dense outlined clearable label="Sexo"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_lugar_nacimiento" dense outlined clearable label="Lugar nac."/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_fecha_nacimiento" type="date" dense outlined label="Fecha nac." @update:model-value="onBirthChange('denunciado')"/></div>

            <div class="col-12 col-md-6"><q-input v-model="form.denunciado_direccion" dense outlined clearable label="Dirección"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_estado_civil" dense outlined clearable label="Estado civil"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_idioma" dense outlined clearable label="Idioma"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_grado_instruccion" dense outlined clearable label="Grado de instrucción"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_ocupacion" dense outlined clearable label="Ocupación"/></div>

            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_celular1" dense outlined clearable label="Celular 1"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_celular2" dense outlined clearable label="Celular 2"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_telefono_fijo1" dense outlined clearable label="Teléfono fijo 1"/></div>
            <div class="col-6 col-md-3"><q-input v-model="form.denunciado_telefono_fijo2" dense outlined clearable label="Teléfono fijo 2"/></div>
            <div class="col-12 col-md-6"><q-input v-model="form.denunciado_direccion_actual" dense outlined clearable label="Dirección actual"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 3) Hecho / denuncia -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="description" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">3) Hecho / denuncia</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-6 col-md-3"><q-input v-model="form.fecha_hecho" type="date" dense outlined label="Fecha del hecho"/></div>
            <div class="col-6 col-md-4"><q-input v-model="form.relacion_denunciante" dense outlined clearable label="Relación con denunciante"/></div>
            <div class="col-12 col-md-5"><q-input v-model="form.direccion_hecho" dense outlined clearable label="Dirección del hecho"/></div>

            <div class="col-12">
              <q-input v-model="form.descripcion_hecho" type="textarea" autogrow outlined dense clearable
                       label="Breve circunstancia del hecho / denuncia" maxlength="4000"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 4) Seguimiento (responsables) -->
      <q-card flat bordered class="section-card q-mb-md">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">4) Seguimiento</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-select v-model="form.psicologica_user_id" dense outlined emit-value map-options clearable
                        :options="optPsicologos" label="Psicológica (responsable)"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="form.trabajo_social_user_id" dense outlined emit-value map-options clearable
                        :options="optSociales" label="Trabajo social (responsable)"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="form.legal_user_id" dense outlined emit-value map-options clearable
                        :options="optAbogados" label="Legal (responsable)"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 5) Check documentos -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="inventory_2" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">5) Check documentos adjuntos</div>
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
  name: 'UmadiInfoGeneral',
  components: { MapPicker },
  props: {
    caseId: { type: [String, Number], required: true },
    caso:   { type: Object, default: () => null }   // puede venir { umadi: {...}, familiares: [...] }
  },
  data () {
    return {
      loading: false,
      editing: false,
      backup: null,

      oruroCenter: [-17.9667, -67.1167],

      psicologos: [],
      abogados: [],
      sociales: [],

      form: {}
    }
  },
  computed: {
    canEdit () {
      const r = this.$store.user?.role || ''
      return r === 'Administrador' || r === 'Asistente'
    },

    nombreDenunciante () {
      const f = this.form || {}
      const full = [f.nombres, f.paterno, f.materno].filter(Boolean).join(' ').trim()
      return full || '—'
    },

    nombreDenunciado () {
      const f = this.form || {}
      const full = [f.denunciado_nombres, f.denunciado_paterno, f.denunciado_materno].filter(Boolean).join(' ').trim()
      return full || '—'
    },

    optPsicologos () { return this.psicologos.map(u => ({ label: u.name, value: u.id })) },
    optAbogados  () { return this.abogados .map(u => ({ label: u.name, value: u.id })) },
    optSociales  () { return this.sociales .map(u => ({ label: u.name, value: u.id })) },

    mapModel: {
      get () { return { latitud: this.form.latitud, longitud: this.form.longitud } },
      set (v) {
        this.form.latitud  = v?.latitud ?? v?.lat ?? null
        this.form.longitud = v?.longitud ?? v?.lng ?? null
      }
    }
  },
  watch: {
    caso: {
      immediate: true,
      deep: true,
      handler (val) {
        if (!val) return
        const data = val.umadi ? val.umadi : val
        const norm = this.normalizeFromBackend(data)
        this.form = { ...norm }

        if (!this.form.psicologica_user_id && data.psicologica_user?.id) this.form.psicologica_user_id = data.psicologica_user.id
        if (!this.form.trabajo_social_user_id && data.trabajo_social_user?.id) this.form.trabajo_social_user_id = data.trabajo_social_user.id
        if (!this.form.legal_user_id && data.legal_user?.id) this.form.legal_user_id = data.legal_user.id
      }
    }
  },
  created () {
    this.fetchRoles()
  },
  methods: {
    orDash (v) { return (v !== null && v !== undefined && v !== '') ? v : '—' },
    yesNo (v) { return (v === true || v === 1 || v === '1') ? 'Sí' : 'No' },

    onBirthChange (who) {
      const key = (who === 'denunciado') ? 'denunciado' : ''
      const field = key ? `${key}_fecha_nacimiento` : 'fecha_nacimiento'
      if (!this.form[field]) return
      const d = new Date(this.form[field])
      const diff = Date.now() - d.getTime()
      const age = Math.abs(new Date(diff).getUTCFullYear() - 1970)
      if (key) this.form['denunciado_edad'] = String(age)
      else this.form['edad'] = String(age)
    },

    nombreUsuario (relObj, id, arr) {
      if (relObj && relObj.name) return relObj.name
      const f = (arr || []).find(x => Number(x.id) === Number(id))
      return f ? (f.name || f.username || '—') : '—'
    },

    normalizeFromBackend (o = {}) {
      const boolKeys = [
        'trabaja','doc_ci','doc_frontal_denunciado','doc_frontal_denunciante','doc_croquis'
      ]
      const out = { ...o }
      boolKeys.forEach(k => { if (k in out) out[k] = (out[k] === 1 || out[k] === true || out[k] === '1') })
      return out
    },

    prepareForSave (o = {}) {
      const boolKeys = [
        'trabaja','doc_ci','doc_frontal_denunciado','doc_frontal_denunciante','doc_croquis'
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
        const payload = { umadi: this.prepareForSave(this.form) }
        const { data } = await this.$axios.put(`/umadis/${this.caseId}`, payload)
        const updated = data?.umadi ? this.normalizeFromBackend(data.umadi) : this.normalizeFromBackend(data)
        this.form = { ...updated }
        this.editing = false
        this.$alert?.success?.('Información general actualizada') || this.$q.notify({ type:'positive', message:'Información general actualizada' })
        this.$emit('update')
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
