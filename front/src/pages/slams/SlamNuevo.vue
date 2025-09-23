<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Barra superior -->
    <div class="toolbar q-pa-sm bg-white row items-center q-gutter-sm shadow-1">
      <div class="col">
        <div class="text-h6 text-weight-bold">
          Registro S.L.A.M. (Adulto Mayor) — Caso
        </div>
        <div class="text-caption text-grey-7">
          Múltiples Adultos (denunciantes) y Familiares · Se guarda en /casos (tipo=SLAM)
        </div>
      </div>
      <div class="col-auto row q-gutter-sm" v-if="!editable">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
      <div class="col-auto row q-gutter-sm" v-else>
        <q-btn flat color="primary" icon="refresh" label="Recargar" @click="getCaso"/>
        <q-btn color="primary" icon="save" label="Actualizar" :loading="loading" @click="update"/>
      </div>
    </div>

    <q-form class="q-mt-lg" @submit.prevent="save">
      <!-- 1) ADULTOS (denunciantes) -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="elderly" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Adultos (denunciantes)</div>
          <q-space/>
          <q-btn dense color="primary" flat icon="add" label="Agregar adulto" @click="addAdulto"/>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div v-if="!f.denunciantes.length" class="text-grey-7 q-mb-md">
            No hay adultos agregados. Usa “Agregar adulto”.
          </div>

          <div v-for="(a, idx) in f.denunciantes" :key="'ad'+idx" class="q-mb-md">
            <div class="row q-col-gutter-md items-center">
              <div class="col-12 col-md-4">
                <q-input v-model="a.denunciante_nombres" dense outlined clearable :rules="[req]" label="Nombres *"/>
              </div>
              <div class="col-6 col-md-2"><q-input v-model="a.denunciante_paterno" dense outlined clearable label="Paterno"/></div>
              <div class="col-6 col-md-2"><q-input v-model="a.denunciante_materno" dense outlined clearable label="Materno"/></div>

              <div class="col-6 col-md-3">
                <q-select v-model="a.denunciante_documento" dense outlined emit-value map-options clearable
                          :options="documentos" label="Documento"/>
              </div>
              <div class="col-6 col-md-3"><q-input v-model="a.denunciante_nro" dense outlined clearable label="Doc. Nº"/></div>

              <div class="col-6 col-md-3">
                <q-input v-model="a.denunciante_fecha_nacimiento" type="date" dense outlined label="Fecha nac."
                         @update:model-value="a.denunciante_edad = calcEdad(a.denunciante_fecha_nacimiento)"/>
              </div>
              <div class="col-6 col-md-3"><q-input v-model="a.denunciante_lugar_nacimiento" dense outlined clearable label="Lugar nac."/></div>
              <div class="col-6 col-md-2"><q-input v-model="a.denunciante_edad" dense outlined clearable label="Edad"/></div>
              <div class="col-12 col-md-4"><q-input v-model="a.denunciante_residencia" dense outlined clearable label="Domicilio"/></div>

              <div class="col-6 col-md-3"><q-input v-model="a.denunciante_estado_civil" dense outlined clearable label="Estado civil"/></div>
              <div class="col-6 col-md-3"><q-input v-model="a.denunciante_ocupacion" dense outlined clearable label="Ocupación"/></div>
              <div class="col-12 col-md-3"><q-input v-model="a.denunciante_telefono" dense outlined clearable label="Teléfono"/></div>

              <!-- Idiomas (se consolidan en denunciante_idioma al guardar) -->
              <div class="col-12 text-caption text-grey-7 q-pt-xs">Idiomas</div>
              <div class="col-6 col-md-2"><q-toggle v-model="a.__idioma_castellano" label="Castellano"/></div>
              <div class="col-6 col-md-2"><q-toggle v-model="a.__idioma_quechua" label="Quechua"/></div>
              <div class="col-6 col-md-2"><q-toggle v-model="a.__idioma_aymara" label="Aymara"/></div>
              <div class="col-12 col-md-6"><q-input v-model="a.__idioma_otros" dense outlined clearable label="Otros idiomas"/></div>

              <div class="col-auto q-mt-sm">
                <q-btn dense round flat color="negative" icon="delete" @click="f.denunciantes.splice(idx,1)"/>
              </div>
            </div>
            <q-separator spaced/>
          </div>
        </q-card-section>
      </q-card>

      <!-- 2) DATOS DEL CASO (SLAM) -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="description" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">2) Datos del caso (S.L.A.M.)</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">

            <div class="col-12 col-md-3">
              <q-input v-model="f.numero_apoyo_integral" dense outlined clearable label="Nº Apoyo Integral"/>
            </div>

            <div class="col-6 col-md-2">
              <q-input v-model="f.caso_fecha_hecho" type="date" dense outlined label="Fecha de registro"/>
            </div>

            <div class="col-12 col-md-7">
              <q-input v-model="f.caso_direccion" dense outlined clearable label="Dirección para hoja de ruta (opcional)"/>
            </div>
            <div class="col-12 col-md-2">
              <q-btn outline label="Buscar" @click="$refs.mapRef?.geocodeAndFly(f.caso_direccion)"/>
            </div>

            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (usa la dirección del 1er adulto)</div>
              <MapPicker
                v-model="denunciantePos"
                :center="oruroCenter"
                :address="f.caso_direccion || (f.denunciantes[0]?.denunciante_residencia || '')"
                country="bo"
                ref="mapRef"
              />
            </div>

            <div class="col-12">
              <q-input
                v-model="f.caso_descripcion"
                type="textarea" autogrow outlined dense clearable
                label="Breve circunstancia del hecho / denuncia" maxlength="3000"
              />
            </div>

            <!-- Tipologías (se consolidan en caso_tipologia y banderas de violencia_*) -->
            <div class="col-6 col-md-3"><q-toggle v-model="tipos.violencia_fisica" label="Violencia física"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="tipos.violencia_psicologica" label="Violencia psicológica"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="tipos.abandono" label="Abandono"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="tipos.apoyo_integral" label="Apoyo integral (principal)"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 3) FAMILIARES (N) -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="diversity_3" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">3) Familiares (N)</div>
          <q-space/>
          <q-btn dense color="primary" flat icon="add" label="Agregar familiar" @click="addFamiliar"/>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div v-if="!f.familiares.length" class="text-grey-7 q-mb-md">
            No hay familiares agregados. Usa “Agregar familiar”.
          </div>

          <div v-for="(fm, idx) in f.familiares" :key="'fam'+idx" class="q-mb-md">
            <div class="row q-col-gutter-md items-center">
              <div class="col-12 col-md-4"><q-input v-model="fm.familiar_nombre_completo" dense outlined clearable label="Nombres y apellidos"/></div>
              <div class="col-6  col-md-2"><q-input v-model="fm.familiar_parentesco" dense outlined clearable label="Parentesco"/></div>
              <div class="col-3  col-md-2"><q-input v-model.number="fm.familiar_edad" type="number" dense outlined label="Edad"/></div>
              <div class="col-3  col-md-2">
                <q-select v-model="fm.familiar_sexo" dense outlined clearable :options="sexos" emit-value map-options label="Sexo"/>
              </div>
              <div class="col-12 col-md-3"><q-input v-model="fm.familiar_ocupacion" dense outlined clearable label="Ocupación"/></div>
              <div class="col-auto q-mt-sm">
                <q-btn dense round flat color="negative" icon="delete" @click="f.familiares.splice(idx,1)"/>
              </div>
            </div>
            <q-separator spaced/>
          </div>
        </q-card-section>
      </q-card>

      <!-- 4) DENUNCIADO/A -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="person_off" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">4) Datos del denunciado/a</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3"><q-input v-model="f.denunciados[0].denunciado_nombres" dense outlined clearable label="Nombres"/></div>
            <div class="col-6  col-md-2"><q-input v-model="f.denunciados[0].denunciado_paterno" dense outlined clearable label="Paterno"/></div>
            <div class="col-6  col-md-2"><q-input v-model="f.denunciados[0].denunciado_materno" dense outlined clearable label="Materno"/></div>
            <div class="col-6  col-md-2"><q-input v-model="f.denunciados[0].denunciado_edad" dense outlined clearable label="Edad"/></div>
            <div class="col-12 col-md-5"><q-input v-model="f.denunciados[0].denunciado_domicilio_actual" dense outlined clearable label="Domicilio"/></div>
            <div class="col-6  col-md-3"><q-input v-model="f.denunciados[0].denunciado_estado_civil" dense outlined clearable label="Estado civil"/></div>
            <div class="col-6  col-md-3"><q-input v-model="f.denunciados[0].denunciado_idioma" dense outlined clearable label="Idioma"/></div>
            <div class="col-6  col-md-3"><q-input v-model="f.denunciados[0].denunciado_grado" dense outlined clearable label="Grado de instrucción"/></div>
            <div class="col-6  col-md-3"><q-input v-model="f.denunciados[0].denunciado_ocupacion" dense outlined clearable label="Ocupación"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 5) RESPONSABLES -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">5) Seguimiento (responsables)</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-select v-model="f.psicologica_user_id" dense outlined emit-value map-options clearable
                        :options="psicologos.map(u => ({ label: u.name, value: u.id }))"
                        label="Área psicológica"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="f.trabajo_social_user_id" dense outlined emit-value map-options clearable
                        :options="sociales.map(u => ({ label: u.name, value: u.id }))"
                        label="Área social"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="f.legal_user_id" dense outlined emit-value map-options clearable
                        :options="abogados.map(u => ({ label: u.name, value: u.id }))"
                        label="Área legal"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 6) CHECK DOCUMENTOS -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="inventory_2" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">6) Check documentos adjuntos</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-checkbox v-model="f.documento_fotocopia_carnet_denunciante" true-value="1" false-value="0" label="Fotocopia CI denunciante"/>
            </div>
            <div class="col-12 col-md-3">
              <q-checkbox v-model="f.documento_fotocopia_carnet_denunciado" true-value="1" false-value="0" label="Fotocopia CI denunciado"/>
            </div>
            <div class="col-12 col-md-3">
              <q-checkbox v-model="f.documento_placas_fotograficas_domicilio_denunciante" true-value="1" false-value="0" label="Placas fotográficas denunciante"/>
            </div>
            <div class="col-12 col-md-3">
              <q-checkbox v-model="f.documento_placas_fotograficas_domicilio_denunciado" true-value="1" false-value="0" label="Placas fotográficas denunciado"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Acciones -->
      <div class="text-right q-mb-xl">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm" class="q-mr-sm"/>
        <q-btn v-if="!editable" color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
        <q-btn v-else color="primary" icon="save" label="Actualizar" :loading="loading" @click="update"/>
      </div>
    </q-form>
  </q-page>
</template>

<script>
import MapPicker from 'components/MapPicker.vue'

export default {
  name: 'CasoNuevoSLAM',
  components: { MapPicker },
  props: {
    casoId:   { type: [Number, String], default: null },
    editable: { type: Boolean, default: false },
    showNumeroApoyoIntegral: { type: Boolean, default: false },
  },
  data () {
    return {
      loading: false,
      oruroCenter: [-17.9667, -67.1167],
      sexos: [
        { label: 'Masculino', value: 'Masculino' },
        { label: 'Femenino',  value: 'Femenino'  },
        { label: 'Otro',      value: 'Otro'      },
      ],
      documentos: [
        { label: 'Carnet de identidad', value: 'Carnet de identidad' },
        { label: 'RUN/Complemento',     value: 'RUN' },
        { label: 'Pasaporte',           value: 'Pasaporte' },
        { label: 'Otro',                value: 'Otro' },
      ],
      abogados: [], psicologos: [], sociales: [],
      tipos: { violencia_fisica:false, violencia_psicologica:false, abandono:false, apoyo_integral:false },
      f: {
        // Cabecera Caso
        tipo: 'SLAM',
        principal: '',
        numero_apoyo_integral: '',
        caso_numero: '',
        caso_fecha_hecho: '',
        caso_lugar_hecho: '',
        caso_zona: '',
        caso_direccion: '',
        caso_descripcion: '',
        caso_tipologia: '',  // se llena al guardar con los toggles
        caso_modalidad: '',

        // Banderas violencia (las que tengas en DB)
        violencia_fisica: false,
        violencia_psicologica: false,
        violencia_sexual: false,
        violencia_economica: false,

        // Relaciones
        denunciantes: [],
        denunciados: [
          {
            denunciado_nombres: '',
            denunciado_paterno: '',
            denunciado_materno: '',
            denunciado_documento: 'Carnet de identidad',
            denunciado_nro: '',
            denunciado_sexo: '',
            denunciado_lugar_nacimiento: '',
            denunciado_fecha_nacimiento: '',
            denunciado_edad: '',
            denunciado_telefono: '',
            denunciado_residencia: '',
            denunciado_estado_civil: '',
            denunciado_relacion: '',
            denunciado_grado: '',
            denunciado_trabaja: 1,
            denunciado_prox: '',
            denunciado_ocupacion: '',
            denunciado_ocupacion_exacto: '',
            denunciado_idioma: '',
            denunciado_fijo: '',
            denunciado_movil: '',
            denunciado_domicilio_actual: '',
            denunciado_latitud: null,
            denunciado_longitud: null,
          }
        ],
        familiares: [],

        // Responsables
        psicologica_user_id: '',
        trabajo_social_user_id: '',
        legal_user_id: '',

        // Documentos
        documento_fotocopia_carnet_denunciante: "0",
        documento_fotocopia_carnet_denunciado:  "0",
        documento_placas_fotograficas_domicilio_denunciante: "0",
        documento_placas_fotograficas_domicilio_denunciado:  "0",
      },
    }
  },
  computed: {
    // v-model para el mapa (1er adulto)
    denunciantePos: {
      get () {
        const d = this.f.denunciantes[0] || {}
        return { latitud: d.latitud || null, longitud: d.longitud || null }
      },
      set (v) {
        if (!this.f.denunciantes.length) this.addAdulto()
        this.f.denunciantes[0].latitud  = v?.latitud ?? null
        this.f.denunciantes[0].longitud = v?.longitud ?? null
      }
    }
  },
  mounted () {
    this.$axios.get('/usuariosRole').then(res => {
      this.psicologos = res.data.psicologos || []
      this.abogados   = res.data.abogados   || []
      this.sociales   = res.data.sociales   || []
    }).catch(() => this.$alert?.error?.('No se pudo cargar los usuarios por rol'))

    if (this.editable && this.casoId) this.getCaso()
  },
  methods: {
    req (v) { return !!v || 'Requerido' },
    calcEdad (fecha) {
      if (!fecha) return ''
      const ms = Date.now() - new Date(fecha).getTime()
      return Math.floor(ms / (1000 * 60 * 60 * 24 * 365.25))
    },
    addAdulto () {
      this.f.denunciantes.push({
        denunciante_nombres: '', denunciante_paterno: '', denunciante_materno: '',
        denunciante_documento: 'Carnet de identidad', denunciante_nro: '',
        denunciante_sexo: '', denunciante_lugar_nacimiento: '', denunciante_fecha_nacimiento: '',
        denunciante_edad: '', denunciante_telefono: '', denunciante_residencia: '',
        denunciante_estado_civil: '', denunciante_relacion: '', denunciante_grado: '',
        denunciante_trabaja: false, denunciante_ocupacion: '',
        latitud: null, longitud: null,
        // Ephemerals para UI de idioma
        __idioma_castellano: false, __idioma_quechua: false, __idioma_aymara: false, __idioma_otros: ''
      })
    },
    addFamiliar () {
      this.f.familiares.push({
        familiar_nombre_completo: '',
        familiar_parentesco: '',
        familiar_edad: null,
        familiar_sexo: '',
        familiar_ocupacion: ''
      })
    },
    resetForm () {
      this.f = {
        ...this.f,
        principal: '',
        numero_apoyo_integral: '',
        caso_numero: '',
        caso_fecha_hecho: '',
        caso_lugar_hecho: '',
        caso_zona: '',
        caso_direccion: '',
        caso_descripcion: '',
        caso_tipologia: '',
        caso_modalidad: '',
        violencia_fisica: false,
        violencia_psicologica: false,
        violencia_sexual: false,
        violencia_economica: false,
        denunciantes: [],
        denunciados: this.f.denunciados, // mantiene estructura base
        familiares: [],
        psicologica_user_id: '',
        trabajo_social_user_id: '',
        legal_user_id: '',
        documento_fotocopia_carnet_denunciante: "0",
        documento_fotocopia_carnet_denunciado:  "0",
        documento_placas_fotograficas_domicilio_denunciante: "0",
        documento_placas_fotograficas_domicilio_denunciado:  "0",
      }
      this.tipos = { violencia_fisica:false, violencia_psicologica:false, abandono:false, apoyo_integral:false }
      this.$q.notify({ type: 'info', message: 'Formulario reiniciado' })
    },

    // ===== CRUD API =====
    async save () {
      if (!this.f.denunciantes.length || !this.f.denunciantes[0].denunciante_nombres) {
        this.$alert?.error?.('Debe registrar al menos un Adulto (denunciante) con nombres.')
        return
      }
      // Consolidar idiomas en cada denunciante
      this.f.denunciantes = this.f.denunciantes.map(d => {
        const idiomas = []
        if (d.__idioma_castellano) idiomas.push('Castellano')
        if (d.__idioma_quechua)   idiomas.push('Quechua')
        if (d.__idioma_aymara)    idiomas.push('Aymara')
        if ((d.__idioma_otros || '').trim()) idiomas.push(d.__idioma_otros.trim())
        return { ...d, denunciante_idioma: idiomas.join(', ') }
      })

      // Consolidar tipologías
      const t = []
      if (this.tipos.violencia_fisica)      { t.push('Violencia física');      this.f.violencia_fisica = true }
      if (this.tipos.violencia_psicologica) { t.push('Violencia psicológica'); this.f.violencia_psicologica = true }
      if (this.tipos.abandono)              { t.push('Abandono') }
      if (this.tipos.apoyo_integral)        { t.push('Apoyo integral'); this.f.principal = 'APOYO INTEGRAL' }
      this.f.caso_tipologia = t.join(' · ')

      this.loading = true
      try {
        const res = await this.$axios.post('/casos', { ...this.f, tipo: 'SLAM' })
        const id  = res?.data?.id || res?.data?.caso?.id
        this.$alert?.success?.('Caso SLAM creado')
        if (id) this.$router.push(`/casos/${id}`) ; else this.$router.push('/casos?tipo=SLAM')
      } catch (e) {
        this.$alert?.error?.(e?.response?.data?.message || 'No se pudo crear el Caso SLAM')
      } finally {
        this.loading = false
      }
    },

    async update () {
      if (!this.casoId) return
      if (!this.f.denunciantes.length || !this.f.denunciantes[0].denunciante_nombres) {
        this.$alert?.error?.('Debe registrar al menos un Adulto (denunciante) con nombres.')
        return
      }
      // idiomas y tipologías igual que en save()
      this.f.denunciantes = this.f.denunciantes.map(d => {
        const idiomas = []
        if (d.__idioma_castellano) idiomas.push('Castellano')
        if (d.__idioma_quechua)   idiomas.push('Quechua')
        if (d.__idioma_aymara)    idiomas.push('Aymara')
        if ((d.__idioma_otros || '').trim()) idiomas.push(d.__idioma_otros.trim())
        return { ...d, denunciante_idioma: idiomas.join(', ') }
      })
      const t = []
      this.f.violencia_fisica = this.f.violencia_psicologica = false
      if (this.tipos.violencia_fisica)      { t.push('Violencia física');      this.f.violencia_fisica = true }
      if (this.tipos.violencia_psicologica) { t.push('Violencia psicológica'); this.f.violencia_psicologica = true }
      if (this.tipos.abandono)              { t.push('Abandono') }
      if (this.tipos.apoyo_integral)        { t.push('Apoyo integral'); this.f.principal = 'APOYO INTEGRAL' }
      this.f.caso_tipologia = t.join(' · ')

      this.loading = true
      try {
        await this.$axios.put(`/casos/${this.casoId}`, this.f)
        this.$alert?.success?.('Caso SLAM actualizado')
        this.$router.push(`/casos/${this.casoId}`)
      } catch (e) {
        this.$alert?.error?.(e?.response?.data?.message || 'No se pudo actualizar el Caso SLAM')
      } finally {
        this.loading = false
      }
    },

    async getCaso () {
      if (!this.casoId) return
      this.loading = true
      try {
        const { data } = await this.$axios.get(`/casos/${this.casoId}`)
        // normaliza estructura mínima requerida por el form
        this.f = {
          ...this.f,
          ...data,
          denunciantes: (data.denunciantes && data.denunciantes.length) ? data.denunciantes.map(d => ({
            ...d,
            __idioma_castellano: (d.denunciante_idioma || '').includes('Castellano'),
            __idioma_quechua:    (d.denunciante_idioma || '').includes('Quechua'),
            __idioma_aymara:     (d.denunciante_idioma || '').includes('Aymara'),
            __idioma_otros:      ''
          })) : []
        }
        this.tipos.violencia_fisica      = !!data.violencia_fisica
        this.tipos.violencia_psicologica = !!data.violencia_psicologica
        this.tipos.abandono              = (data.caso_tipologia || '').toLowerCase().includes('abandono')
        this.tipos.apoyo_integral        = (data.principal || '').toLowerCase().includes('apoyo integral')
        if (!this.f.denunciados || !this.f.denunciados.length) this.f.denunciados = [this.f.denunciados?.[0] || this.f.denunciadosBase]
      } catch (_) {
        this.$alert?.error?.('No se pudo cargar el caso')
      } finally {
        this.loading = false
      }
    },
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
.toolbar   { position: sticky; top: 0; z-index: 5; border-radius: 12px; }
.section-card { border-radius: 14px; overflow: hidden; margin-bottom: 16px; background: #fff; }
</style>
