<template>
  <div>
    <!-- Barra superior -->
    <div class="row items-center q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Hoja de ruta</div>
        <div class="text-caption text-grey-7">
          Completa las fechas clave del caso ({{ done }}/{{ total }} completadas)
        </div>
      </div>

      <div class="col-auto row items-center q-gutter-sm">
        <q-chip v-if="!canEdit" dense color="grey-3" text-color="grey-9" icon="visibility_off">
          Solo lectura
        </q-chip>

        <q-btn flat color="secondary" icon="refresh" :loading="loading" @click="fetch" />

        <template v-if="canEdit">
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
        </template>
      </div>
    </div>

    <q-card flat bordered class="section-card">
      <q-card-section>
        <div class="row q-col-gutter-md">
          <!-- Código y apertura: solo lectura -->
          <div class="col-12 col-md-3">
            <q-input v-model="form.caso_numero" label="Código de caso" dense outlined readonly />
          </div>
          <div class="col-12 col-md-3">
            <q-input
              v-model="form.fecha_apertura_caso"
              type="date"
              label="Fecha de apertura del caso"
              dense outlined readonly
            />
          </div>

          <!-- Editables -->
          <div class="col-12 col-md-3">
            <q-input
              v-model="form.fecha_derivacion_psicologica"
              type="date"
              :readonly="isReadOnly"
              dense outlined
              label="Fecha de derivación psicológica"
            />
          </div>

          <div class="col-12 col-md-3">
            <q-input
              v-model="form.fecha_informe_area_psicologica"
              type="date"
              :readonly="isReadOnly"
              dense outlined
              label="Fecha de entrega informe (Área psicológica)"
            />
          </div>

          <div class="col-12 col-md-3">
            <q-input
              v-model="form.fecha_informe_trabajo_social"
              type="date"
              :readonly="isReadOnly"
              dense outlined
              label="Fecha de entrega (Trabajo social)"
            />
          </div>

          <div class="col-12 col-md-3">
            <q-input
              v-model="form.fecha_derivacion_area_legal"
              type="date"
              :readonly="isReadOnly"
              dense outlined
              label="Fecha de derivación (Área legal)"
            />
          </div>
        </div>
      </q-card-section>

      <!-- Línea de tiempo -->
      <q-separator />
      <q-card-section>
        <q-timeline color="primary" layout="comfortable" side="right">
          <q-timeline-entry
            title="Apertura del caso"
            :subtitle="format(form.fecha_apertura_caso)"
            icon="flag"
          />
          <q-timeline-entry
            title="Derivación psicológica"
            :subtitle="format(form.fecha_derivacion_psicologica)"
            icon="psychology"
            :color="form.fecha_derivacion_psicologica ? 'primary' : 'grey-5'"
          />
          <q-timeline-entry
            title="Informe área psicológica"
            :subtitle="format(form.fecha_informe_area_psicologica)"
            icon="assignment_turned_in"
            :color="form.fecha_informe_area_psicologica ? 'primary' : 'grey-5'"
          />
          <q-timeline-entry
            title="Entrega trabajo social"
            :subtitle="format(form.fecha_informe_trabajo_social)"
            icon="diversity_3"
            :color="form.fecha_informe_trabajo_social ? 'primary' : 'grey-5'"
          />
          <q-timeline-entry
            title="Derivación área legal"
            :subtitle="format(form.fecha_derivacion_area_legal)"
            icon="gavel"
            :color="form.fecha_derivacion_area_legal ? 'primary' : 'grey-5'"
          />
        </q-timeline>
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
export default {
  name: 'HojaRuta',
  props: { caseId: { type: [String, Number], required: true } },
  data () {
    return {
      loading: false,
      editing: false,
      backup: null,
      form: {
        caso_numero: '',
        fecha_apertura_caso: '',
        fecha_derivacion_psicologica: '',
        fecha_informe_area_psicologica: '',
        fecha_informe_trabajo_social: '',
        fecha_derivacion_area_legal: ''
      }
    }
  },
  computed: {
    canEdit () {
      const r = this.$store.user?.role || ''
      return r === 'Administrador' || r === 'Asistente'
    },
    isReadOnly () { return !this.editing || !this.canEdit },
    total () { return 5 },
    done () {
      const keys = [
        'fecha_apertura_caso',
        'fecha_derivacion_psicologica',
        'fecha_informe_area_psicologica',
        'fecha_informe_trabajo_social',
        'fecha_derivacion_area_legal'
      ]
      return keys.filter(k => !!this.form[k]).length
    }
  },
  created () {
    this.fetch()
  },
  methods: {
    format (val) { return val || '—' },
    async fetch () {
      this.loading = true
      try {
        // ✅ migrado a SLIMs
        const { data } = await this.$axios.get(`/slims/${this.caseId}`)
        this.form = {
          caso_numero: data.caso_numero || '',
          fecha_apertura_caso: data.fecha_apertura_caso || '',
          fecha_derivacion_psicologica: data.fecha_derivacion_psicologica || '',
          fecha_informe_area_psicologica: data.fecha_informe_area_psicologica || '',
          fecha_informe_trabajo_social: data.fecha_informe_trabajo_social || '',
          fecha_derivacion_area_legal: data.fecha_derivacion_area_legal || ''
        }
        this.backup = { ...this.form }
      } catch (e) {
        this.$alert?.error(e?.response?.data?.message || 'No se pudo cargar la hoja de ruta')
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

      const payload = {
        fecha_derivacion_psicologica: this.form.fecha_derivacion_psicologica || null,
        fecha_informe_area_psicologica: this.form.fecha_informe_area_psicologica || null,
        fecha_informe_trabajo_social: this.form.fecha_informe_trabajo_social || null,
        fecha_derivacion_area_legal: this.form.fecha_derivacion_area_legal || null
      }

      this.loading = true
      try {
        // ✅ migrado a SLIMs
        const { data } = await this.$axios.put(`/slims/${this.caseId}`, payload)
        this.form = {
          ...this.form,
          fecha_derivacion_psicologica: data.data?.fecha_derivacion_psicologica ?? this.form.fecha_derivacion_psicologica,
          fecha_informe_area_psicologica: data.data?.fecha_informe_area_psicologica ?? this.form.fecha_informe_area_psicologica,
          fecha_informe_trabajo_social: data.data?.fecha_informe_trabajo_social ?? this.form.fecha_informe_trabajo_social,
          fecha_derivacion_area_legal: data.data?.fecha_derivacion_area_legal ?? this.form.fecha_derivacion_area_legal
        }
        this.backup = { ...this.form }
        this.editing = false
        this.$alert?.success('Hoja de ruta actualizada')
      } catch (e) {
        this.$alert?.error(e?.response?.data?.message || 'No se pudo actualizar')
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
  background: #fff;
}
</style>
