<template>
  <q-page class="q-pa-md bg-grey-2">

    <div class="row items-center q-mb-md">
      <div class="col">
        <div class="text-h6 text-weight-bold">Talleres</div>
        <div class="text-caption text-grey-7">Registro simple</div>
      </div>
      <div class="col-auto">
        <q-btn color="primary" icon="add" label="Agregar" @click="openDialog" />
        <q-btn flat icon="refresh" class="q-ml-sm" @click="fetchRows" />
      </div>
    </div>

    <q-card flat bordered>
      <q-card-section>
        <q-markup-table dense flat bordered v-if="rows.length">
          <thead>
          <tr class="bg-primary text-white">
            <th style="width: 80px;">#</th>
            <th>Solicitado</th>
            <th>Fecha inicio</th>
            <th>Colegio</th>
            <th>Curso</th>
            <th>Asistentes</th>
            <th>Dirigido a</th>
            <th style="width:120px;">Acciones</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="r in rows" :key="r.id">
            <td>{{ r.id }}</td>
            <td>{{ r.solicitado }}</td>
            <td>{{ formatDate(r.fecha_inicio) }}</td>
            <td>{{ r.colegio }}</td>
            <td>{{ r.curso }}</td>
            <td>{{ r.numero_asistentes }}</td>
            <td>{{ r.dirigido_a }}</td>
            <td class="text-right">
              <q-btn flat dense color="negative" icon="delete" @click="onDelete(r)" />
            </td>
          </tr>
          </tbody>
        </q-markup-table>

        <div v-else class="text-grey-7">Sin registros.</div>
      </q-card-section>
    </q-card>

    <!-- Diálogo Crear -->
    <q-dialog v-model="dialog.open" persistent>
      <q-card style="min-width: 520px; max-width: 720px;">
        <q-card-section class="row items-center q-gutter-sm">
          <q-icon name="add" />
          <div class="text-subtitle1 text-weight-medium">Nuevo taller</div>
          <q-space />
        </q-card-section>
        <q-separator />

        <q-card-section class="q-gutter-md">
          <q-select
            v-model="form.solicitado"
            :options="solicitadoOpts"
            label="Solicitado *"
            dense outlined :rules="[req]"
          />

          <div class="row q-col-gutter-sm">
            <div class="col-6">
              <q-input v-model="form.fecha_inicio" type="datetime-local" label="Fecha inicio *" dense outlined :rules="[req]" />
            </div>
            <div class="col-6">
              <q-input v-model="form.fecha_final" type="datetime-local" label="Fecha final" dense outlined />
            </div>
          </div>

          <div class="row q-col-gutter-sm">
            <div class="col-6">
              <q-input v-model="form.colegio" label="Colegio" dense outlined />
            </div>
            <div class="col-6">
              <q-input v-model="form.curso" label="Curso" dense outlined />
            </div>
          </div>

          <div class="row q-col-gutter-sm">
            <div class="col-6">
              <q-input v-model.number="form.numero_asistentes" type="number" min="0" label="Nº asistentes *" dense outlined :rules="[reqNumber]" />
            </div>
            <div class="col-6">
              <q-input v-model="form.dirigido_a" label="Dirigido a" dense outlined />
            </div>
          </div>

          <q-input v-model="form.lugar" label="Lugar" dense outlined />
          <q-input v-model="form.dia" label="Día (texto)" dense outlined />

          <q-input v-model="form.tema" label="Tema" dense outlined />
          <q-input v-model="form.descripcion" type="textarea" autogrow label="Descripción" dense outlined />

          <div class="row q-col-gutter-sm">
            <div class="col-6">
              <q-input v-model="form.director_nombre" label="Director - Nombre" dense outlined />
            </div>
            <div class="col-6">
              <q-input v-model="form.director_telefono" label="Director - Teléfono" dense outlined />
            </div>
          </div>

          <div class="row q-col-gutter-sm">
            <div class="col-6">
              <q-input v-model="form.encargado_taller_nombre" label="Encargado Taller - Nombre" dense outlined />
            </div>
            <div class="col-6">
              <q-input v-model="form.encargado_taller_telefono" label="Encargado Taller - Teléfono" dense outlined />
            </div>
          </div>

          <q-input v-model="form.direccion_ubicacion_ue_colegio" label="Dirección/Ubicación UE" dense outlined />

          <q-input v-model="form.color" label="Color (#RRGGBB)" dense outlined />

        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" v-close-popup />
          <q-btn color="primary" label="Guardar" :loading="saving" @click="save" />
        </q-card-actions>
      </q-card>
    </q-dialog>

  </q-page>
</template>

<script>
export default {
  name: 'TalleresSimple',
  data () {
    return {
      rows: [],
      saving: false,
      dialog: { open: false },
      solicitadoOpts: ['DIRECTOR','SECRETARIA','PERSONAL UE'],
      form: this.emptyForm()
    }
  },
  mounted () {
    this.fetchRows()
  },
  methods: {
    emptyForm () {
      return {
        solicitado: null,
        dia: '',
        fecha_inicio: '',
        fecha_final: '',
        lugar: '',
        colegio: '',
        curso: '',
        numero_asistentes: 0,
        tema: '',
        descripcion: '',
        director_nombre: '',
        director_telefono: '',
        encargado_taller_nombre: '',
        encargado_taller_telefono: '',
        direccion_ubicacion_ue_colegio: '',
        dirigido_a: '',
        color: ''
      }
    },
    req (v) { return !!v || 'Requerido' },
    reqNumber (v) {
      if (v === 0 || v === '0') return true
      return (!!v && Number(v) >= 0) || 'Número válido'
    },
    formatDate (val) {
      if (!val) return ''
      const d = new Date(val)
      return isNaN(d) ? val : d.toLocaleString()
    },
    openDialog () {
      this.form = this.emptyForm()
      this.dialog.open = true
    },
    async fetchRows () {
      try {
        const { data } = await this.$axios.get('/talleres')
        this.rows = data
      } catch (e) {
        this.$q.notify({ type: 'negative', message: 'No se pudo cargar' })
      }
    },
    async save () {
      // Validaciones mínimas
      if (!this.form.solicitado || !this.form.fecha_inicio || this.form.numero_asistentes === null) {
        this.$q.notify({ type: 'warning', message: 'Completa los campos obligatorios' })
        return
      }

      this.saving = true
      try {
        const payload = { ...this.form }
        // Normalizar datetime-local => "YYYY-MM-DD HH:mm:ss"
        const toApi = (s) => s ? s.replace('T', ' ') + (s.length === 16 ? ':00' : '') : null
        payload.fecha_inicio = toApi(this.form.fecha_inicio)
        payload.fecha_final  = toApi(this.form.fecha_final)

        await this.$axios.post('/talleres', payload)
        this.$q.notify({ type: 'positive', message: 'Taller creado' })
        this.dialog.open = false
        await this.fetchRows()
      } catch (e) {
        this.$q.notify({ type: 'negative', message: e?.response?.data?.message || 'Error al guardar' })
      } finally {
        this.saving = false
      }
    },
    async onDelete (row) {
      this.$q.dialog({
        title: 'Confirmar',
        message: `¿Eliminar el taller #${row.id}?`,
        cancel: true,
        persistent: true
      }).onOk(async () => {
        try {
          await this.$axios.delete(`/talleres/${row.id}`)
          this.$q.notify({ type: 'warning', message: 'Eliminado' })
          await this.fetchRows()
        } catch (e) {
          this.$q.notify({ type: 'negative', message: 'No se pudo eliminar' })
        }
      })
    }
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
</style>
