<!-- src/pages/dna/CasoNuevoDNA.vue -->
<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Barra superior -->
    <div class="toolbar q-pa-sm bg-white row items-center q-gutter-sm shadow-1">
      <div class="col">
        <div class="text-h6 text-weight-bold">Registro de Atención y/o Denuncia (DNA)</div>
        <div class="text-caption text-grey-7">Nuevo caso · Formato SID</div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </div>

    <q-form class="q-mt-lg" @submit.prevent="save">
      <!-- 1) DATOS GENERALES -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="assignment" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Datos generales</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-input v-model="f.fecha_atencion" type="date" dense outlined label="Fecha de atención"/>
            </div>
            <div class="col-12 col-md-5">
              <q-input v-model="f.principal" dense outlined clearable label="PRINCIPAL (p.ej. ASISTENCIA FAMILIAR) *" :rules="[req]"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="f.tipologia" :options="tipologias" dense outlined emit-value map-options clearable
                        label="Tipología (catálogo DNA)"/>
            </div>

            <div class="col-12 col-md-8">
              <q-input v-model="f.domicilio" dense outlined clearable label="Domicilio"/>
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="f.zona" dense outlined clearable label="Zona"/>
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="f.telefono" dense outlined clearable label="Teléfono"/>
            </div>

            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (para hoja de ruta)</div>
              <MapPicker
                v-model="denPos"
                :center="oruroCenter"
                :address="f.domicilio"
                country="bo"
                ref="denMap"
              />
              <div class="row q-mt-xs">
                <div class="col-12 col-md-10">
                  <q-input v-model="f.domicilio" dense outlined clearable label="Buscar dirección en el mapa"/>
                </div>
                <div class="col-12 col-md-2">
                  <q-btn outline label="Buscar" @click="$refs.denMap?.geocodeAndFly(f.domicilio)" />
                </div>
              </div>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 2) DEL MENOR (tabla) -->
      <q-card flat bordered class="section-card">
        <q-expansion-item icon="child_care" dense-toggle expand-separator
                          label="2) Datos del/los menor(es)" header-class="text-subtitle1">
          <q-card>
            <q-card-section>
              <div class="q-mb-sm">
                <q-btn dense icon="add" color="primary" label="Agregar menor" @click="addMenor"/>
              </div>

              <q-markup-table dense flat bordered class="q-mb-sm">
                <thead>
                <tr class="bg-blue-1 text-blue-10">
                  <th>Nombre y apellidos</th>
                  <th style="width:90px">Gestante</th>
                  <th style="width:90px">Edad (años)</th>
                  <th style="width:90px">Edad (meses)</th>
                  <th style="width:110px">Sexo</th>
                  <th style="width:90px">C. Nac</th>
                  <th style="width:90px">Estudia</th>
                  <th>Último curso</th>
                  <th>Tipo de trabajo</th>
                  <th style="width:60px"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(m,i) in f.menores" :key="i">
                  <td><q-input v-model="m.nombre" dense outlined/></td>
                  <td class="text-center"><q-toggle v-model="m.gestante"/></td>
                  <td><q-input v-model.number="m.edad_anios" type="number" dense outlined/></td>
                  <td><q-input v-model.number="m.edad_meses" type="number" dense outlined/></td>
                  <td><q-select v-model="m.sexo" :options="sexos" emit-value map-options dense outlined/></td>
                  <td class="text-center"><q-toggle v-model="m.cert_nac"/></td>
                  <td class="text-center"><q-toggle v-model="m.estudia"/></td>
                  <td><q-input v-model="m.ultimo_curso" dense outlined/></td>
                  <td><q-input v-model="m.tipo_trabajo" dense outlined/></td>
                  <td class="text-center">
                    <q-btn flat dense round icon="delete" color="negative" @click="f.menores.splice(i,1)"/>
                  </td>
                </tr>
                </tbody>
              </q-markup-table>
              <div v-if="!f.menores.length" class="text-grey text-caption">Sin menores registrados</div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- 3) GRUPO FAMILIAR -->
      <q-card flat bordered class="section-card">
        <q-expansion-item icon="diversity_3" dense-toggle expand-separator
                          label="3) Datos del grupo familiar" header-class="text-subtitle1">
          <q-card>
            <q-card-section>
              <div class="q-mb-sm">
                <q-btn dense icon="add" color="primary" label="Agregar integrante" @click="addFam"/>
              </div>

              <q-markup-table dense flat bordered>
                <thead>
                <tr class="bg-blue-1 text-blue-10">
                  <th>Nombre y apellidos</th>
                  <th>Parentesco</th>
                  <th style="width:100px">Edad</th>
                  <th style="width:120px">Sexo</th>
                  <th>G. Instrucción</th>
                  <th>Ocupación</th>
                  <th style="width:60px"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(g,i) in f.grupo_familiar" :key="'g'+i">
                  <td><q-input v-model="g.nombre" dense outlined/></td>
                  <td><q-input v-model="g.parentesco" dense outlined/></td>
                  <td><q-input v-model.number="g.edad" type="number" dense outlined/></td>
                  <td><q-select v-model="g.sexo" :options="sexos" emit-value map-options dense outlined/></td>
                  <td><q-input v-model="g.instruccion" dense outlined/></td>
                  <td><q-input v-model="g.ocupacion" dense outlined/></td>
                  <td class="text-center">
                    <q-btn flat dense round icon="delete" color="negative" @click="f.grupo_familiar.splice(i,1)"/>
                  </td>
                </tr>
                </tbody>
              </q-markup-table>
              <div v-if="!f.grupo_familiar.length" class="text-grey text-caption">Sin integrantes registrados</div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- 4) DENUNCIADO -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="person_off" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">4) Datos del denunciado</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input v-model="f.denunciado_nombre" dense outlined clearable label="Nombres y apellidos"/>
            </div>
            <div class="col-6 col-md-2">
              <q-select v-model="f.denunciado_sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/>
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model.number="f.denunciado_edad" type="number" dense outlined label="Edad"/>
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="f.denunciado_relacion" dense outlined label="Parentesco / Tipo de relación"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.denunciado_ci" dense outlined clearable label="C.I."/>
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model="f.denunciado_domicilio" dense outlined clearable label="Domicilio"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.denunciado_telefono" dense outlined clearable label="Teléfono"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.denunciado_lugar_trabajo" dense outlined clearable label="Lugar de trabajo"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.denunciado_ocupacion" dense outlined clearable label="Ocupación"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 5) DENUNCIANTE -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="person" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">5) Datos del denunciante</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input v-model="f.denunciante_nombre" dense outlined clearable label="Nombres y apellidos *" :rules="[req]"/>
            </div>
            <div class="col-6 col-md-2">
              <q-select v-model="f.denunciante_sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/>
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model.number="f.denunciante_edad" type="number" dense outlined label="Edad"/>
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="f.denunciante_ci" dense outlined clearable label="C.I."/>
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model="f.denunciante_domicilio" dense outlined clearable label="Domicilio"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.denunciante_telefono" dense outlined clearable label="Teléfono"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.denunciante_lugar_trabajo" dense outlined clearable label="Lugar de trabajo"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.denunciante_ocupacion" dense outlined clearable label="Ocupación"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 6) DESCRIPCIÓN -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="description" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">6) Descripción de la denuncia</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <q-input
            v-model="f.descripcion"
            type="textarea" autogrow outlined dense clearable
            label="Descripción"
            maxlength="5000" counter
          />
        </q-card-section>
      </q-card>

      <!-- 7) ASIGNACIÓN -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="gavel" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">7) Asignación</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-select v-model="f.abogado_user_id" dense outlined emit-value map-options clearable
                        :options="abogados.map(u => ({ label: u.username || u.name, value: u.id }))"
                        label="Abogado/a asignado"/>
            </div>
            <div class="col-12 col-md-3">
              <q-chip outline color="grey-6" text-color="grey-10">Área: DNA</q-chip>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Acciones -->
      <div class="text-right q-mb-lg">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm" class="q-mr-sm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </q-form>
  </q-page>
</template>

<script>
import MapPicker from 'components/MapPicker.vue'

export default {
  name: 'CasoNuevoDNA',
  components: { MapPicker },
  data () {
    return {
      loading: false,
      oruroCenter: [-17.9667, -67.1167],
      abogados: [],
      sexos: [
        { label: 'Masculino', value: 'Masculino' },
        { label: 'Femenino',  value: 'Femenino'  },
        { label: 'Otro',      value: 'Otro'      },
      ],
      tipologias: [
        // puedes ajustar según tu catálogo DNA
        'ASISTENCIA FAMILIAR', 'VIOLENCIA FAMILIAR O DOMÉSTICA', 'OTROS'
      ],
      f: {
        // encabezado
        fecha_atencion: '',     // opcional (server ya guarda fecha_apertura_caso)
        principal: '',          // se mapea a caso_tipologia
        tipologia: '',          // opcional extra
        domicilio: '',
        zona: '',
        telefono: '',
        latitud: null,          // para hoja de ruta (denunciante)
        longitud: null,

        // menores
        menores: [{
          nombre: '', gestante: false, edad_anios: null, edad_meses: null,
          sexo: '', cert_nac: false, estudia: false, ultimo_curso: '', tipo_trabajo: ''
        }],

        // grupo familiar
        grupo_familiar: [],

        // denunciado
        denunciado_nombre: '', denunciado_sexo: '', denunciado_edad: null,
        denunciado_relacion: '', denunciado_ci: '', denunciado_domicilio: '',
        denunciado_telefono: '', denunciado_lugar_trabajo: '', denunciado_ocupacion: '',

        // denunciante
        denunciante_nombre: '', denunciante_sexo: '', denunciante_edad: null,
        denunciante_ci: '', denunciante_domicilio: '', denunciante_telefono: '',
        denunciante_lugar_trabajo: '', denunciante_ocupacion: '',

        // descripción
        descripcion: '',

        // asignación
        abogado_user_id: null,
      }
    }
  },
  computed: {
    denPos: {
      get () { return { latitud: this.f.latitud, longitud: this.f.longitud } },
      set (v) { this.f.latitud = v.latitud; this.f.longitud = v.longitud }
    }
  },
  mounted () {
    // carga abogados (ya tienes este endpoint)
    this.$axios.get('/usuariosRole')
      .then(r => { this.abogados = r.data.abogados || [] })
      .catch(() => { this.$alert?.error?.('No se pudo cargar abogados') })
  },
  methods: {
    req (v) { return !!v || 'Requerido' },
    addMenor () {
      this.f.menores.push({
        nombre: '', gestante: false, edad_anios: null, edad_meses: null,
        sexo: '', cert_nac: false, estudia: false, ultimo_curso: '', tipo_trabajo: ''
      })
    },
    addFam () {
      this.f.grupo_familiar.push({ nombre: '', parentesco: '', edad: null, sexo: '', instruccion: '', ocupacion: '' })
    },
    resetForm () {
      const keep = {}
      this.f = {
        ...keep,
        fecha_atencion: '', principal: '', tipologia: '',
        domicilio: '', zona: '', telefono: '', latitud: null, longitud: null,
        menores: [{ nombre: '', gestante: false, edad_anios: null, edad_meses: null, sexo: '', cert_nac: false, estudia: false, ultimo_curso: '', tipo_trabajo: '' }],
        grupo_familiar: [],
        denunciado_nombre: '', denunciado_sexo: '', denunciado_edad: null, denunciado_relacion: '', denunciado_ci: '', denunciado_domicilio: '', denunciado_telefono: '', denunciado_lugar_trabajo: '', denunciado_ocupacion: '',
        denunciante_nombre: '', denunciante_sexo: '', denunciante_edad: null, denunciante_ci: '', denunciante_domicilio: '', denunciante_telefono: '', denunciante_lugar_trabajo: '', denunciante_ocupacion: '',
        descripcion: '',
        abogado_user_id: null,
      }
      this.$q.notify({ type: 'info', message: 'Formulario reiniciado' })
    },

    // 🔁 Mapeo al backend existente (/casos)
    buildPayload () {
      // menores + grupo_familiar → familiares
      const familiaresFromMenores = (this.f.menores || []).map(m => ({
        familiar_nombre_completo : m.nombre || '',
        familiar_parentesco      : 'Del menor',
        familiar_edad            : [m.edad_anios ? `${m.edad_anios} años` : '', m.edad_meses ? `${m.edad_meses} meses` : ''].filter(Boolean).join(' '),
        familiar_sexo            : m.sexo || '',
        familiar_grado           : m.ultimo_curso || '',
        familiar_ocupacion       : m.tipo_trabajo || '',
        // Guardamos flags en prox como texto (si lo quieres fuerte, crea columnas)
        familiar_prox            : `Gestante:${m.gestante?'Sí':'No'}; C.Nac:${m.cert_nac?'Sí':'No'}; Estudia:${m.estudia?'Sí':'No'}`
      }))

      const familiaresFromGrupo = (this.f.grupo_familiar || []).map(g => ({
        familiar_nombre_completo : g.nombre || '',
        familiar_parentesco      : g.parentesco || '',
        familiar_edad            : g.edad != null ? String(g.edad) : '',
        familiar_sexo            : g.sexo || '',
        familiar_grado           : g.instruccion || '',
        familiar_ocupacion       : g.ocupacion || ''
      }))

      const familiares = [...familiaresFromMenores, ...familiaresFromGrupo]

      // denunciados
      const denunciados = [{
        denunciado_nombres          : this.f.denunciado_nombre || '',
        denunciado_documento        : 'Carnet de identidad',
        denunciado_nro              : this.f.denunciado_ci || '',
        denunciado_sexo             : this.f.denunciado_sexo || '',
        denunciado_edad             : this.f.denunciado_edad || '',
        denunciado_telefono         : this.f.denunciado_telefono || '',
        denunciado_residencia       : '', // opcional
        denunciado_relacion         : this.f.denunciado_relacion || '',
        denunciado_grado            : '',
        denunciado_ocupacion        : this.f.denunciado_ocupacion || '',
        denunciado_ocupacion_exacto : this.f.denunciado_lugar_trabajo || '', // guardamos el “lugar de trabajo”
        denunciado_domicilio_actual : this.f.denunciado_domicilio || '',
        denunciado_latitud          : null,
        denunciado_longitud         : null,
      }]

      // denunciantes
      const denunciantes = [{
        denunciante_nombres          : this.f.denunciante_nombre || '',
        denunciante_documento        : 'Carnet de identidad',
        denunciante_nro              : this.f.denunciante_ci || '',
        denunciante_sexo             : this.f.denunciante_sexo || '',
        denunciante_edad             : this.f.denunciante_edad || '',
        denunciante_telefono         : this.f.denunciante_telefono || '',
        denunciante_residencia       : '', // opcional
        denunciante_relacion         : '',
        denunciante_grado            : '',
        denunciante_ocupacion        : this.f.denunciante_ocupacion || '',
        denunciante_ocupacion_exacto : this.f.denunciante_lugar_trabajo || '',
        denunciante_domicilio_actual : this.f.denunciante_domicilio || '',
        latitud                      : this.f.latitud,
        longitud                     : this.f.longitud,
      }]

      return {
        // IMPORTANTE para que tu store numere: numeroCaso($request->tipo)
        tipo: 'DNA',

        // Cabecera/datos de caso
        caso_tipologia   : this.f.principal || this.f.tipologia || '',
        caso_zona        : this.f.zona || '',
        caso_direccion   : this.f.domicilio || '',
        caso_descripcion : this.f.descripcion || '',

        // Asignación
        legal_user_id: this.f.abogado_user_id || null,

        // Relaciones
        denunciantes,
        denunciados,
        familiares,

        // Si quieres mantener el tel del encabezado como respaldo:
        telefono_encabezado: this.f.telefono || '',

        // Si tu PDF hoja de ruta usa lat/lng del caso, puedes enviarlos aquí también:
        latitud : this.f.latitud,
        longitud: this.f.longitud,
      }
    },

    async save () {
      if (!this.f.principal) {
        this.$alert?.error?.('El campo PRINCIPAL es obligatorio')
        return
      }
      if (!this.f.denunciante_nombre) {
        this.$alert?.error?.('El nombre del denunciante es obligatorio')
        return
      }
      this.loading = true
      try {
        const payload = this.buildPayload()
        const res = await this.$axios.post('/casos', payload)
        const id = res?.data?.id || res?.data?.caso?.id
        this.$alert?.success?.('Caso DNA creado')
        if (id) this.$router.push(`/casos/${id}`)
        else     this.$router.push('/casos?tipo=DNA')
      } catch (e) {
        this.$alert?.error?.(e?.response?.data?.message || 'No se pudo crear el caso DNA')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
.toolbar   { position: sticky; top: 0; z-index: 5; border-radius: 12px; }
.section-card { border-radius: 14px; overflow: hidden; margin-bottom: 16px; background: #fff; }
</style>
