<template>
  <q-page class="q-pa-md">

    <!-- Toolbar -->
    <div class="row items-center q-gutter-sm q-mb-md">
      <q-input dense outlined v-model="filter" label="Buscar" style="min-width:220px">
        <template #append><q-icon name="search"/></template>
      </q-input>

      <q-input dense outlined type="date" v-model="start" label="Inicio" style="width: 150px"/>
      <q-input dense outlined type="date" v-model="end"   label="Fin"    style="width: 150px"/>

      <q-btn dense outline color="primary" label="Esta semana" @click="setThisWeek"/>
      <q-btn dense outline color="indigo"  label="Fin de semana" @click="setWeekend"/>
      <q-btn color="primary" icon="refresh" label="Actualizar" no-caps :loading="loading" @click="fetch"/>
      <q-btn color="positive" icon="add_circle_outline" label="Nuevo" no-caps :loading="loading" @click="openNew"/>

      <!-- IMPRIMIR TODO -->
      <q-btn
        color="grey-8"
        icon="print"
        label="Imprimir todo"
        no-caps
        :loading="loading"
        @click="printAll"
      />
    </div>

    <!-- Tabla simple -->
    <q-markup-table flat bordered dense wrap-cells>
      <thead>
      <tr>
        <th class="text-center">Acciones</th>
        <th class="text-left">Fecha</th>
        <th class="text-left">Nombre</th>
        <th class="text-left">CI</th>
        <th class="text-left">Estado civil</th>
        <th class="text-left">Ocupación</th>
        <th class="text-left">Archivo</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="r in filteredRows" :key="r.id">
        <td class="text-center">
          <q-btn-dropdown label="Acciones" dense no-caps size="10px" color="primary">
            <q-list>
              <q-item clickable v-ripple @click="openEdit(r)">
                <q-item-section avatar><q-icon name="edit"/></q-item-section>
                <q-item-section>Editar</q-item-section>
              </q-item>

              <!-- IMPRESIONES (uno) -->
              <q-item clickable v-ripple @click="printOne(r)">
                <q-item-section avatar><q-icon name="print"/></q-item-section>
                <q-item-section>Impresiones</q-item-section>
              </q-item>

              <q-item clickable v-ripple @click="onDelete(r.id)">
                <q-item-section avatar><q-icon name="delete"/></q-item-section>
                <q-item-section>Eliminar</q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </td>

        <td>{{ fmtFechaSolo(r.fecha) }}</td>
        <td>{{ r.nombre_completo }}</td>
        <td>{{ r.ci }}</td>
        <td>{{ r.estado_civil }}</td>
        <td>{{ r.ocupacion }}</td>
        <td>
          <q-btn
            v-if="r.url"
            dense outline icon="attach_file" label="Archivo"
            :href="`${$url}/../storage/${r.url}`"
            target="_blank"
          />
          <q-badge v-else color="grey-5" outline>—</q-badge>
        </td>
      </tr>

      <tr v-if="!filteredRows.length">
        <td colspan="7" class="text-center text-grey">Sin registros</td>
      </tr>
      </tbody>
    </q-markup-table>

    <!-- Diálogo Crear/Editar (usa q-file) -->
    <q-dialog v-model="dialog" persistent>
      <q-card style="width: 720px; max-width: 95vw">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">{{ form.id ? 'Editar' : 'Nuevo' }} SLAM Notarial</div>
          <q-space/><q-btn flat round dense icon="close" @click="dialog=false"/>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form @submit="save">
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-8">
                <q-input dense outlined v-model="form.nombre_completo" label="Nombre completo"/>
              </div>

              <div class="col-6 col-md-3">
                <q-input
                  dense outlined type="date"
                  v-model="form.fecha_nacimiento"
                  label="F. nacimiento"
                  @update:model-value="edadCalculate()"
                />
              </div>

              <div class="col-6 col-md-3">
                <q-input dense outlined v-model="form.edad" label="Edad"/>
              </div>

              <div class="col-12 col-md-6">
                <q-input dense outlined v-model="form.estado_civil" label="Estado civil"/>
              </div>

              <div class="col-6 col-md-3">
                <q-input dense outlined v-model="form.ci" label="CI"/>
              </div>

              <div class="col-6 col-md-3">
                <q-input dense outlined v-model="form.grado_instruccion" label="Grado de instr."/>
              </div>

              <div class="col-12 col-md-6">
                <q-input dense outlined v-model="form.ocupacion" label="Ocupación"/>
              </div>

              <div class="col-12">
                <q-input dense outlined v-model="form.direccion_domicilio" label="Dirección"/>
              </div>

              <div class="col-12">
                <div class="row items-center">
                  <div class="col">
                    <q-file v-model="file" dense outlined label="Archivo (url)"/>
                  </div>
                  <div class="col-auto" v-if="form.url">
                    <q-btn
                      dense outline icon="open_in_new" label="Ver actual"
                      :href="`${$url}/../storage/${form.url}`"
                      target="_blank"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div class="text-right q-mt-md">
              <q-btn color="negative" flat label="Cancelar" @click="dialog=false" :loading="loading"/>
              <q-btn color="primary" label="Guardar" type="submit" class="q-ml-sm" :loading="loading"/>
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
  name: 'SlamNotarialPage',
  data () {
    return {
      loading: false,
      filter: '',
      start: moment().startOf('week').add(1, 'day').format('YYYY-MM-DD'), // Lunes
      end:   moment().endOf('week').add(1, 'day').format('YYYY-MM-DD'),   // Domingo
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
      return this.rows.filter(r =>
        (r.nombre_completo || '').toLowerCase().includes(t) ||
        (r.ci || '').toLowerCase().includes(t) ||
        (r.estado_civil || '').toLowerCase().includes(t) ||
        (r.ocupacion || '').toLowerCase().includes(t) ||
        (String(r.fecha || '')).toLowerCase().includes(t)
      )
    }
  },
  mounted () {
    this.fetch()
  },
  methods: {
    // ==========
    // Helpers
    // ==========
    fmtFechaSolo (val) {
      if (!val) return '—'
      return moment(val).isValid() ? moment(val).format('YYYY-MM-DD') : String(val)
    },
    fmtFecha (val) {
      if (!val) return '—'
      // Soporta "2026-01-24 06:28:07"
      return moment(val).isValid() ? moment(val).format('YYYY-MM-DD HH:mm') : String(val)
    },
    emptyForm () {
      return {
        id: null,
        fecha: moment().format('YYYY-MM-DD'),
        nombre_completo: '',
        edad: '',
        fecha_nacimiento: '',
        estado_civil: '',
        ci: '',
        grado_instruccion: '',
        ocupacion: '',
        direccion_domicilio: '',
        url: null
      }
    },
    edadCalculate () {
      if (this.form.fecha_nacimiento) {
        const birthDate = moment(this.form.fecha_nacimiento, 'YYYY-MM-DD')
        const today = moment()
        this.form.edad = today.diff(birthDate, 'years')
      } else {
        this.form.edad = ''
      }
    },

    // ==========
    // Rango fechas
    // ==========
    setThisWeek () {
      this.start = moment().startOf('week').add(1, 'day').format('YYYY-MM-DD')
      this.end   = moment().endOf('week').add(1, 'day').format('YYYY-MM-DD')
      this.fetch()
    },
    setWeekend () {
      const sat = moment().startOf('week').add(6, 'day')
      const sun = moment().startOf('week').add(7, 'day')
      this.start = sat.format('YYYY-MM-DD')
      this.end   = sun.format('YYYY-MM-DD')
      this.fetch()
    },

    // ==========
    // CRUD
    // ==========
    async fetch () {
      this.loading = true
      try {
        const params = {}
        if (this.start && this.end) {
          params.start = this.start
          params.end = this.end
        }
        const { data } = await this.$axios.get('slam-notariales', { params })
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
      this.form = { ...this.emptyForm(), ...row }
    },

    async save () {
      this.loading = true
      try {
        const fd = new FormData()

        const payloadKeys = [
          'fecha', 'nombre_completo', 'edad', 'fecha_nacimiento', 'estado_civil',
          'ci', 'grado_instruccion', 'ocupacion', 'direccion_domicilio'
        ]
        payloadKeys.forEach(k => fd.append(k, this.form[k] ?? ''))

        if (this.file) fd.append('url', this.file)

        if (this.form.id) {
          // IMPORTANTE:
          // PUT + multipart a veces falla según backend.
          // Si tu Laravel no lo recibe bien, cambia a POST + _method=PUT
          // fd.append('_method','PUT')
          await this.$axios.put(`slam-notariales/${this.form.id}`, fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
        } else {
          await this.$axios.post('slam-notariales', fd, {
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
            await this.$axios.delete(`slam-notariales/${id}`)
            this.$alert?.success('Eliminado')
            this.fetch()
          } catch (e) {
            this.$alert?.error(e.response?.data?.message || 'No se pudo eliminar')
          } finally {
            this.loading = false
          }
        })
    },

    // ==========
    // PDF (jsPDF)
    // ==========
    storageUrl (path) {
      const clean = String(path || '').replace(/^\/+/, '')
      // Igual que usas en tu botón de "Archivo"
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

    async printOne (r) {
      const doc = new jsPDF({ unit: 'mm', format: 'a4' })

      // Encabezado
      doc.setFontSize(14)
      doc.text('SLAM NOTARIAL', 105, 14, { align: 'center' })
      doc.setFontSize(10)
      doc.text(`Fecha registro: ${this.fmtFecha(r.fecha)}`, 14, 22)

      // Tabla clave-valor
      autoTable(doc, {
        startY: 26,
        theme: 'grid',
        styles: { fontSize: 9, cellPadding: 2 },
        head: [['Campo', 'Valor']],
        body: [
          ['Nombre completo', r.nombre_completo || '—'],
          ['CI', r.ci || '—'],
          ['Estado civil', r.estado_civil || '—'],
          ['Fecha nacimiento', r.fecha_nacimiento || '—'],
          ['Edad', (r.edad ?? '') !== '' ? String(r.edad) : '—'],
          ['Grado de instrucción', r.grado_instruccion || '—'],
          ['Ocupación', r.ocupacion || '—'],
          ['Dirección', r.direccion_domicilio || '—']
        ]
      })

      // Si hay archivo, intenta poner mini preview (si es imagen)
      if (r.url) {
        const y = (doc.lastAutoTable?.finalY || 26) + 8
        doc.setFontSize(10)
        doc.text('Archivo adjunto:', 14, y)

        const dataUrl = await this.fetchImageAsDataUrl(r.url)
        if (dataUrl) {
          try {
            // JPG/PNG
            const isPng = String(dataUrl).startsWith('data:image/png')
            doc.addImage(dataUrl, isPng ? 'PNG' : 'JPEG', 14, y + 4, 60, 45)
            doc.setFontSize(8)
            doc.text(this.storageUrl(r.url), 14, y + 54)
          } catch (e) {
            doc.setFontSize(9)
            doc.text(`Ver: ${this.storageUrl(r.url)}`, 14, y + 6)
          }
        } else {
          doc.setFontSize(9)
          doc.text(`Ver: ${this.storageUrl(r.url)}`, 14, y + 6)
        }
      }

      // Footer
      doc.setFontSize(8)
      doc.text(`Generado: ${moment().format('YYYY-MM-DD HH:mm')}`, 14, 290)

      doc.save(`slam-notarial-${r.id}.pdf`)
    },

    async printAll () {
      // Imprime lo que estás viendo (filtrado)
      const list = this.filteredRows

      if (!list.length) {
        this.$alert?.error('No hay registros para imprimir')
        return
      }

      const doc = new jsPDF({ unit: 'mm', format: 'a4', orientation: 'portrait' })

      doc.setFontSize(14)
      doc.text('SLAM NOTARIAL — LISTADO', 105, 14, { align: 'center' })

      doc.setFontSize(10)
      doc.text(`Rango: ${this.start || '—'} a ${this.end || '—'}`, 14, 22)
      doc.text(`Total: ${list.length}`, 160, 22)

      autoTable(doc, {
        startY: 26,
        theme: 'grid',
        styles: { fontSize: 8, cellPadding: 2 },
        head: [[
          '#', 'Fecha', 'Nombre completo', 'CI', 'Estado civil', 'Ocupación', 'Archivo'
        ]],
        body: list.map((r, idx) => ([
          idx + 1,
          this.fmtFecha(r.fecha),
          r.nombre_completo || '',
          r.ci || '',
          r.estado_civil || '',
          r.ocupacion || '',
          r.url ? 'SI' : 'NO'
        ]))
      })

      doc.setFontSize(8)
      doc.text(`Generado: ${moment().format('YYYY-MM-DD HH:mm')}`, 14, 290)

      doc.save(`slam-notarial-listado-${moment().format('YYYYMMDD-HHmm')}.pdf`)
    }
  }
}
</script>
