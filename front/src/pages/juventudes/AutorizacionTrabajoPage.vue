<template>
  <q-page class="q-pa-md">

    <!-- TOOLBAR -->
    <div class="row items-center q-gutter-sm q-mb-md">
      <q-input dense outlined v-model="filter" label="Buscar" style="min-width:220px">
        <template #append><q-icon name="search"/></template>
      </q-input>

      <q-input dense outlined type="date" v-model="start" label="Inicio" style="width: 150px"/>
      <q-input dense outlined type="date" v-model="end"   label="Fin"    style="width: 150px"/>

<!--      <q-btn dense outline color="primary" label="Esta semana" @click="setThisWeek"/>-->
<!--      <q-btn dense outline color="indigo"  label="Fin de semana" @click="setWeekend"/>-->

      <q-btn color="primary" icon="refresh" label="Actualizar" no-caps :loading="loading" @click="fetch"/>
      <q-btn color="positive" icon="add_circle_outline" label="Nuevo" no-caps :loading="loading" @click="openNew"/>

      <q-btn color="grey-8" icon="print" label="Imprimir todo" no-caps :loading="loading" @click="printAll"/>
    </div>

    <!-- TABLA -->
    <q-markup-table flat bordered dense wrap-cells>
      <thead>
      <tr>
        <th class="text-center" style="width:140px">Acciones</th>
        <th class="text-left">Fecha</th>
        <th class="text-left">Adolescente</th>
        <th class="text-left">CI</th>
        <th class="text-left">Edad</th>
        <th class="text-left">Ciudad</th>
        <th class="text-left">Empresa</th>
<!--        <th class="text-left">Archivo</th>-->
      </tr>
      </thead>
      <tbody>
      <tr v-for="r in filteredRows" :key="r.id">
        <td class="text-center">
          <q-btn-dropdown label="Acciones" dense no-caps size="10px" color="primary">
            <q-list>
              <q-item clickable v-ripple @click="openEdit(r)" v-close-popup>
                <q-item-section avatar><q-icon name="edit"/></q-item-section>
                <q-item-section>Editar</q-item-section>
              </q-item>

              <q-item clickable v-ripple @click="printOne(r)" v-close-popup>
                <q-item-section avatar><q-icon name="print"/></q-item-section>
                <q-item-section>Impresiones</q-item-section>
              </q-item>

              <q-item clickable v-ripple @click="onDelete(r.id)" v-close-popup>
                <q-item-section avatar><q-icon name="delete"/></q-item-section>
                <q-item-section>Eliminar</q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </td>

        <td>{{ fmtFechaSolo(r.fecha) }}</td>
        <td>{{ fullName(r) }}</td>
        <td>{{ r.ci }}</td>
        <td>{{ r.edad }}</td>
        <td>{{ r.ciudad || r.ciudad_empresa }}</td>
        <td>{{ r.razon_social }}</td>

<!--        <td>-->
<!--          <q-btn-->
<!--            v-if="r.url"-->
<!--            dense outline icon="attach_file" label="Archivo"-->
<!--            :href="`${$url}/../storage/${r.url}`"-->
<!--            target="_blank"-->
<!--          />-->
<!--          <q-badge v-else color="grey-5" outline>—</q-badge>-->
<!--        </td>-->
      </tr>

      <tr v-if="!filteredRows.length">
        <td colspan="8" class="text-center text-grey">Sin registros</td>
      </tr>
      </tbody>
    </q-markup-table>

    <!-- DIALOGO: CREAR/EDITAR -->
    <q-dialog v-model="dialog" persistent>
      <q-card style="width: 980px; max-width: 98vw">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">{{ form.id ? 'Editar' : 'Nuevo' }} — Autorización de Trabajo</div>
          <q-space />
          <q-btn flat round dense icon="close" @click="dialog=false" />
        </q-card-section>

        <q-separator />

        <q-card-section class="q-pt-sm">
          <q-form @submit="save">
            <!-- DATOS DEL ADOLESCENTE -->
            <div class="text-subtitle1 text-bold q-mb-sm">Datos del adolescente</div>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-3">
                <q-input dense outlined v-model="form.primer_apellido" label="Primer apellido" />
              </div>
              <div class="col-12 col-md-3">
                <q-input dense outlined v-model="form.segundo_apellido" label="Segundo apellido" />
              </div>
              <div class="col-12 col-md-3">
                <q-input dense outlined v-model="form.primer_nombre" label="Primer nombre" />
              </div>
              <div class="col-12 col-md-3">
                <q-input dense outlined v-model="form.segundo_nombre" label="Segundo nombre" />
              </div>

              <div class="col-12 col-md-3">
                <q-input dense outlined v-model="form.ci" label="C.I." />
              </div>
              <div class="col-6 col-md-2">
                <q-input dense outlined v-model="form.edad" label="Edad" />
              </div>
              <div class="col-6 col-md-3">
                <q-input dense outlined type="datetime-local" v-model="form.fecha" label="Fecha (registro)" />
              </div>

              <div class="col-12 col-md-4">
                <q-input dense outlined v-model="form.direccion" label="Dirección" />
              </div>
              <div class="col-6 col-md-3">
                <q-input dense outlined v-model="form.ciudad" label="Ciudad" />
              </div>
              <div class="col-6 col-md-3">
                <q-input dense outlined v-model="form.municipio" label="Municipio" />
              </div>

              <div class="col-6 col-md-3">
                <q-input dense outlined v-model="form.telefono_1" label="Teléfono 1" />
              </div>
              <div class="col-6 col-md-3">
                <q-input dense outlined v-model="form.telefono_2" label="Teléfono 2" />
              </div>

              <div class="col-12 col-md-4">
                <q-input dense outlined v-model="form.nombre_padre" label="Nombre del padre" />
              </div>
              <div class="col-12 col-md-4">
                <q-input dense outlined v-model="form.nombre_madre" label="Nombre de la madre" />
              </div>
              <div class="col-12 col-md-4">
                <q-input dense outlined v-model="form.nombre_tutor" label="Nombre del tutor" />
              </div>

              <div class="col-12 col-md-4">
                <q-input dense outlined v-model="form.ocupacion" label="Ocupación (adolescente)" />
              </div>
              <div class="col-12 col-md-4">
                <q-input dense outlined v-model="form.grado_parentesco" label="Grado de parentesco" />
              </div>
              <div class="col-12 col-md-4">
                <q-input dense outlined v-model="form.grado_instruccion" label="Grado de instrucción" />
              </div>
            </div>

            <q-separator class="q-my-md" />

            <!-- DATOS EMPRESA -->
            <div class="text-subtitle1 text-bold q-mb-sm">Datos de la empresa o lugar de trabajo</div>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <q-input dense outlined v-model="form.razon_social" label="Razón social" />
              </div>
              <div class="col-12 col-md-6">
                <q-input dense outlined v-model="form.representante_legal" label="Representante legal" />
              </div>

              <div class="col-12 col-md-3">
                <q-input dense outlined v-model="form.tipo" label="Tipo (empresa/persona/cooperativa)" />
              </div>
              <div class="col-12 col-md-5">
                <q-input dense outlined v-model="form.direccion_empresa" label="Dirección empresa" />
              </div>
              <div class="col-12 col-md-2">
                <q-input dense outlined v-model="form.ciudad_empresa" label="Ciudad empresa" />
              </div>
              <div class="col-6 col-md-2">
                <q-input dense outlined v-model="form.nit" label="NIT" />
              </div>
              <div class="col-6 col-md-3">
                <q-input dense outlined v-model="form.telefono_empresa" label="Teléfono empresa" />
              </div>
            </div>

            <q-separator class="q-my-md" />

            <!-- TIPO Y CONDICIONES -->
            <div class="text-subtitle1 text-bold q-mb-sm">Tipo y condiciones de trabajo</div>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-4">
                <q-input dense outlined v-model="form.tipo_actividad" label="Tipo de actividad" />
              </div>
              <div class="col-6 col-md-2">
                <q-input dense outlined v-model="form.remuneracion_bs" label="Remuneración (Bs.)" />
              </div>
              <div class="col-6 col-md-3">
                <q-input dense outlined v-model="form.forma_pago" label="Forma de pago (texto)" />
              </div>

              <div class="col-12 col-md-3">
                <div class="text-caption text-grey-7 q-mb-xs">Periodicidad de pago</div>
                <div class="row q-col-gutter-sm">
                  <div class="col-6">
                    <q-toggle v-model="form.pago_diario" label="Diario" />
                  </div>
                  <div class="col-6">
                    <q-toggle v-model="form.pago_semanal" label="Semanal" />
                  </div>
                  <div class="col-6">
                    <q-toggle v-model="form.pago_quincenal" label="Quincenal" />
                  </div>
                  <div class="col-6">
                    <q-toggle v-model="form.pago_anual" label="Anual" />
                  </div>
                </div>
              </div>

              <div class="col-12 col-md-4">
                <q-input dense outlined v-model="form.cargo_ocupar" label="Cargo a ocupar" />
              </div>
              <div class="col-12 col-md-3">
                <q-input dense outlined v-model="form.duracion_trabajo" label="Duración del trabajo" />
              </div>
              <div class="col-12 col-md-12">
                <q-input
                  dense outlined type="textarea" autogrow
                  v-model="form.descripcion_lugar_actividad"
                  label="Descripción del lugar de trabajo y actividad a realizar"
                />
              </div>
            </div>

            <q-separator class="q-my-md" />

            <!-- ARCHIVO -->
<!--            <div class="text-subtitle1 text-bold q-mb-sm">Archivo</div>-->
<!--            <div class="row items-center q-col-gutter-md">-->
<!--              <div class="col-12 col-md">-->
<!--                <q-file v-model="file" dense outlined label="Subir archivo (url)" />-->
<!--              </div>-->
<!--              <div class="col-auto" v-if="form.url">-->
<!--                <q-btn-->
<!--                  dense outline icon="open_in_new" label="Ver actual"-->
<!--                  :href="`${$url}/../storage/${form.url}`"-->
<!--                  target="_blank"-->
<!--                />-->
<!--              </div>-->
<!--            </div>-->

            <div class="text-right q-mt-md">
              <q-btn color="negative" flat label="Cancelar" @click="dialog=false" :loading="loading" />
              <q-btn color="primary" label="Guardar" type="submit" class="q-ml-sm" :loading="loading" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

  </q-page>
</template>

<script>
import moment from 'moment'

// PDF
import { jsPDF } from 'jspdf'
import autoTable from 'jspdf-autotable'

export default {
  name: 'AutorizacionTrabajoPage',
  data () {
    return {
      loading: false,
      filter: '',
      start: moment().startOf('week').add(1, 'day').format('YYYY-MM-DD'),
      end: moment().endOf('week').add(1, 'day').format('YYYY-MM-DD'),
      rows: [],
      dialog: false,
      file: null,
      form: this.emptyForm()
    }
  },
  computed: {
    filteredRows () {
      if (!this.filter) return this.rows
      const t = this.filter.toLowerCase()
      return this.rows.filter(r => {
        const txt = [
          this.fullName(r),
          r.ci,
          r.ciudad,
          r.municipio,
          r.razon_social,
          r.representante_legal,
          r.nit,
          r.telefono_empresa,
          r.tipo_actividad,
          r.cargo_ocupar,
          r.descripcion_lugar_actividad,
          String(r.fecha || '')
        ].join(' ').toLowerCase()
        return txt.includes(t)
      })
    }
  },
  mounted () {
    this.fetch()
  },
  methods: {
    emptyForm () {
      return {
        id: null,
        user_id: null,
        fecha: moment().format('YYYY-MM-DDTHH:mm'),

        primer_apellido: '',
        segundo_apellido: '',
        primer_nombre: '',
        segundo_nombre: '',
        ci: '',
        edad: '',
        direccion: '',
        ciudad: '',
        municipio: '',
        telefono_1: '',
        telefono_2: '',
        nombre_padre: '',
        nombre_madre: '',
        nombre_tutor: '',
        ocupacion: '',
        grado_parentesco: '',
        grado_instruccion: '',

        razon_social: '',
        representante_legal: '',
        tipo: '',
        direccion_empresa: '',
        ciudad_empresa: '',
        nit: '',
        telefono_empresa: '',

        tipo_actividad: '',
        remuneracion_bs: '',
        forma_pago: '',
        pago_diario: false,
        pago_semanal: false,
        pago_quincenal: false,
        pago_anual: false,

        cargo_ocupar: '',
        duracion_trabajo: '',
        descripcion_lugar_actividad: '',

        url: null
      }
    },

    fullName (r) {
      const parts = [
        r.primer_apellido, r.segundo_apellido,
        r.primer_nombre, r.segundo_nombre
      ].filter(Boolean)
      return parts.join(' ').trim()
    },

    fmtFechaSolo (val) {
      if (!val) return '—'
      return moment(val).isValid() ? moment(val).format('YYYY-MM-DD') : String(val)
    },
    fmtFecha (val) {
      if (!val) return '—'
      return moment(val).isValid() ? moment(val).format('YYYY-MM-DD HH:mm') : String(val)
    },

    setThisWeek () {
      this.start = moment().startOf('week').add(1, 'day').format('YYYY-MM-DD')
      this.end = moment().endOf('week').add(1, 'day').format('YYYY-MM-DD')
      this.fetch()
    },
    setWeekend () {
      const sat = moment().startOf('week').add(6, 'day')
      const sun = moment().startOf('week').add(7, 'day')
      this.start = sat.format('YYYY-MM-DD')
      this.end = sun.format('YYYY-MM-DD')
      this.fetch()
    },

    async fetch () {
      this.loading = true
      try {
        const params = {}
        if (this.start && this.end) {
          params.start = this.start
          params.end = this.end
        }
        const { data } = await this.$axios.get('autorizaciones-trabajo', { params })
        this.rows = data
      } catch (e) {
        this.$alert?.error(e.response?.data?.message || 'Error cargando registros')
      } finally {
        this.loading = false
      }
    },

    openNew () {
      this.dialog = true
      this.file = null
      this.form = this.emptyForm()
    },
    openEdit (row) {
      this.dialog = true
      this.file = null

      // fecha: si viene "2026-01-24 06:28:07" => datetime-local
      const f = { ...this.emptyForm(), ...row }
      if (f.fecha) {
        const m = moment(f.fecha)
        if (m.isValid()) f.fecha = m.format('YYYY-MM-DDTHH:mm')
      }
      this.form = f
    },

    async save () {
      this.loading = true
      try {
        const fd = new FormData()

        const keys = [
          'fecha',
          'primer_apellido','segundo_apellido','primer_nombre','segundo_nombre',
          'ci','edad','direccion','ciudad','municipio',
          'telefono_1','telefono_2',
          'nombre_padre','nombre_madre','nombre_tutor',
          'ocupacion','grado_parentesco','grado_instruccion',

          'razon_social','representante_legal','tipo',
          'direccion_empresa','ciudad_empresa','nit','telefono_empresa',

          'tipo_actividad','remuneracion_bs','forma_pago',
          'pago_diario','pago_semanal','pago_quincenal','pago_anual',
          'cargo_ocupar','duracion_trabajo','descripcion_lugar_actividad'
        ]

        // datetime-local => "YYYY-MM-DDTHH:mm" => lo mandamos así (Laravel Carbon lo parsea bien)
        keys.forEach(k => {
          const v = this.form[k]
          fd.append(k, v === null || v === undefined ? '' : String(v))
        })

        if (this.file) fd.append('url', this.file)

        if (this.form.id) {
          await this.$axios.post(`autorizaciones-trabajo/${this.form.id}?_method=PUT`, fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        } else {
          await this.$axios.post('autorizaciones-trabajo', fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        }

        this.dialog = false
        this.$alert?.success('Guardado')
        this.fetch()
      } catch (e) {
        this.$alert?.error(e.response?.data?.message || 'No se pudo guardar')
      } finally {
        this.loading = false
      }
    },

    onDelete (id) {
      this.$alert?.dialog('¿Eliminar registro?')
        .onOk(async () => {
          this.loading = true
          try {
            await this.$axios.delete(`autorizaciones-trabajo/${id}`)
            this.$alert?.success('Eliminado')
            this.fetch()
          } catch (e) {
            this.$alert?.error(e.response?.data?.message || 'No se pudo eliminar')
          } finally {
            this.loading = false
          }
        })
    },

    // ========= PDF =========
    storageUrl (path) {
      const clean = String(path || '').replace(/^\/+/, '')
      return `${this.$url}/../storage/${clean}`
    },

    async fetchImageAsDataUrl (path) {
      const url = this.storageUrl(path)
      try {
        const res = await this.$axios.get(url, { responseType: 'blob' })
        const blob = res.data
        return await new Promise((resolve, reject) => {
          const reader = new FileReader()
          reader.onload = () => resolve(reader.result)
          reader.onerror = reject
          reader.readAsDataURL(blob)
        })
      } catch (e) {
        return null
      }
    },

    pagoTexto (r) {
      const arr = []
      if (r.pago_diario) arr.push('Diario')
      if (r.pago_semanal) arr.push('Semanal')
      if (r.pago_quincenal) arr.push('Quincenal')
      if (r.pago_anual) arr.push('Anual')
      return arr.length ? arr.join(', ') : '—'
    },

    async printOne (r) {
      const doc = new jsPDF({ unit: 'mm', format: 'a4' })

      doc.setFontSize(13)
      doc.text('FORMULARIO DE AUTORIZACIÓN DE TRABAJO', 105, 12, { align: 'center' })
      doc.setFontSize(9)
      doc.text('Defensoría de la Niñez y Adolescencia', 105, 17, { align: 'center' })
      doc.setFontSize(9)
      doc.text(`Fecha registro: ${this.fmtFecha(r.fecha)}`, 14, 24)

      // Sección: Adolescente
      doc.setFontSize(10)
      doc.text('DATOS DEL ADOLESCENTE', 14, 31)

      autoTable(doc, {
        startY: 33,
        theme: 'grid',
        styles: { fontSize: 8, cellPadding: 2 },
        head: [['Campo', 'Valor']],
        body: [
          ['Apellidos', `${r.primer_apellido || ''} ${r.segundo_apellido || ''}`.trim() || '—'],
          ['Nombres', `${r.primer_nombre || ''} ${r.segundo_nombre || ''}`.trim() || '—'],
          ['CI', r.ci || '—'],
          ['Edad', (r.edad ?? '') !== '' ? String(r.edad) : '—'],
          ['Dirección', r.direccion || '—'],
          ['Ciudad', r.ciudad || '—'],
          ['Municipio', r.municipio || '—'],
          ['Teléfono 1', r.telefono_1 || '—'],
          ['Teléfono 2', r.telefono_2 || '—'],
          ['Nombre del padre', r.nombre_padre || '—'],
          ['Nombre de la madre', r.nombre_madre || '—'],
          ['Nombre del tutor', r.nombre_tutor || '—'],
          ['Ocupación', r.ocupacion || '—'],
          ['Grado de parentesco', r.grado_parentesco || '—'],
          ['Grado de instrucción', r.grado_instruccion || '—']
        ]
      })

      // Empresa
      let y = (doc.lastAutoTable?.finalY || 33) + 6
      doc.setFontSize(10)
      doc.text('DATOS DE LA EMPRESA O LUGAR DE TRABAJO', 14, y)
      y += 2

      autoTable(doc, {
        startY: y,
        theme: 'grid',
        styles: { fontSize: 8, cellPadding: 2 },
        head: [['Campo', 'Valor']],
        body: [
          ['Razón social', r.razon_social || '—'],
          ['Representante legal', r.representante_legal || '—'],
          ['Tipo', r.tipo || '—'],
          ['Dirección', r.direccion_empresa || '—'],
          ['Ciudad', r.ciudad_empresa || '—'],
          ['NIT', r.nit || '—'],
          ['Teléfono', r.telefono_empresa || '—']
        ]
      })

      // Condiciones
      y = (doc.lastAutoTable?.finalY || y) + 6
      doc.setFontSize(10)
      doc.text('TIPO Y CONDICIONES DE TRABAJO', 14, y)
      y += 2

      autoTable(doc, {
        startY: y,
        theme: 'grid',
        styles: { fontSize: 8, cellPadding: 2 },
        head: [['Campo', 'Valor']],
        body: [
          ['Tipo de actividad', r.tipo_actividad || '—'],
          ['Remuneración (Bs.)', r.remuneracion_bs || '—'],
          ['Forma de pago', r.forma_pago || '—'],
          ['Periodicidad', this.pagoTexto(r)],
          ['Cargo a ocupar', r.cargo_ocupar || '—'],
          ['Duración del trabajo', r.duracion_trabajo || '—'],
          ['Descripción / actividad', r.descripcion_lugar_actividad || '—']
        ]
      })

      // Archivo
      if (r.url) {
        y = (doc.lastAutoTable?.finalY || y) + 6
        doc.setFontSize(9)
        doc.text('Archivo adjunto:', 14, y)
        const dataUrl = await this.fetchImageAsDataUrl(r.url)
        if (dataUrl) {
          try {
            const isPng = String(dataUrl).startsWith('data:image/png')
            doc.addImage(dataUrl, isPng ? 'PNG' : 'JPEG', 14, y + 3, 70, 50)
            doc.setFontSize(7)
            doc.text(this.storageUrl(r.url), 14, y + 56)
          } catch (e) {
            doc.setFontSize(8)
            doc.text(`Ver: ${this.storageUrl(r.url)}`, 14, y + 5)
          }
        } else {
          doc.setFontSize(8)
          doc.text(`Ver: ${this.storageUrl(r.url)}`, 14, y + 5)
        }
      }

      doc.setFontSize(8)
      doc.text(`Generado: ${moment().format('YYYY-MM-DD HH:mm')}`, 14, 292)

      doc.save(`autorizacion-trabajo-${r.id}.pdf`)
    },

    async printAll () {
      const list = this.filteredRows
      if (!list.length) {
        this.$alert?.error('No hay registros para imprimir')
        return
      }

      const doc = new jsPDF({ unit: 'mm', format: 'a4' })
      doc.setFontSize(14)
      doc.text('AUTORIZACIONES DE TRABAJO — LISTADO', 105, 14, { align: 'center' })
      doc.setFontSize(10)
      doc.text(`Rango: ${this.start || '—'} a ${this.end || '—'}   |   Total: ${list.length}`, 14, 22)

      autoTable(doc, {
        startY: 26,
        theme: 'grid',
        styles: { fontSize: 8, cellPadding: 2 },
        head: [[ '#', 'Fecha', 'Adolescente', 'CI', 'Edad', 'Ciudad', 'Empresa', 'Pago' ]],
        body: list.map((r, idx) => ([
          idx + 1,
          this.fmtFecha(r.fecha),
          this.fullName(r),
          r.ci || '',
          r.edad || '',
          r.ciudad || r.ciudad_empresa || '',
          r.razon_social || '',
          this.pagoTexto(r)
        ]))
      })

      doc.setFontSize(8)
      doc.text(`Generado: ${moment().format('YYYY-MM-DD HH:mm')}`, 14, 292)
      doc.save(`autorizaciones-trabajo-${moment().format('YYYYMMDD-HHmm')}.pdf`)
    }
  }
}
</script>
