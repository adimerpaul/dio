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
<!--      <div class="col-auto row q-gutter-sm">-->
<!--        <q-btn flat color="secondary" icon="refresh" :loading="loading" @click="fetch" />-->
<!--        <q-btn-->
<!--          v-if="!editing"-->
<!--          color="primary"-->
<!--          icon="edit"-->
<!--          label="Editar"-->
<!--          @click="startEdit"-->
<!--          :loading="loading"-->
<!--        />-->
<!--        <template v-else>-->
<!--          <q-btn flat color="negative" icon="close" label="Cancelar" @click="cancelEdit" :loading="loading"/>-->
<!--          <q-btn color="primary" icon="save" label="Guardar" @click="save" :loading="loading"/>-->
<!--        </template>-->
<!--      </div>-->
    </div>

    <q-card flat bordered class="section-card">
<!--      <q-card-section>-->
<!--        <div class="row q-col-gutter-md">-->
<!--          &lt;!&ndash; Código y apertura: solo lectura &ndash;&gt;-->
<!--          <div class="col-12 col-md-3">-->
<!--            <q-input v-model="caso.caso_numero" label="Código de caso" dense outlined readonly />-->
<!--          </div>-->
<!--          <div class="col-12 col-md-3">-->
<!--            <q-input-->
<!--              v-model="caso.fecha_apertura_caso"-->
<!--              type="date"-->
<!--              label="Fecha de apertura del caso"-->
<!--              dense outlined readonly-->
<!--            />-->
<!--          </div>-->

<!--          &lt;!&ndash; Editables &ndash;&gt;-->
<!--          <div class="col-12 col-md-3">-->
<!--            <q-input-->
<!--              v-model="caso.fecha_derivacion_psicologica"-->
<!--              type="date"-->
<!--              :readonly="!editing"-->
<!--              dense outlined-->
<!--              label="Fecha de derivación psicológica"-->
<!--            />-->
<!--          </div>-->

<!--          <div class="col-12 col-md-3">-->
<!--            <q-input-->
<!--              v-model="caso.fecha_informe_area_psicologica"-->
<!--              type="date"-->
<!--              :readonly="!editing"-->
<!--              dense outlined-->
<!--              label="Fecha de entrega informe (Área psicológica)"-->
<!--            />-->
<!--          </div>-->

<!--          <div class="col-12 col-md-3">-->
<!--            <q-input-->
<!--              v-model="caso.fecha_informe_trabajo_social"-->
<!--              type="date"-->
<!--              :readonly="!editing"-->
<!--              dense outlined-->
<!--              label="Fecha de entrega (Trabajo social)"-->
<!--            />-->
<!--          </div>-->

<!--          <div class="col-12 col-md-3">-->
<!--            <q-input-->
<!--              v-model="caso.fecha_derivacion_area_legal"-->
<!--              type="date"-->
<!--              :readonly="!editing"-->
<!--              dense outlined-->
<!--              label="Fecha de derivación (Área legal)"-->
<!--            />-->
<!--          </div>-->
<!--        </div>-->
<!--      </q-card-section>-->

      <!-- Línea de tiempo opcional -->
      <q-separator />
      <q-card-section>
        <q-timeline color="primary" layout="comfortable" side="right">
          <q-timeline-entry
            title="Apertura del caso"
            :subtitle="format(caso.fecha_apertura_caso)"
            icon="flag"
          />
<!--          <q-timeline-entry-->
<!--            title="Derivación psicológica"-->
<!--            :subtitle="format(caso.fecha_derivacion_psicologica)"-->
<!--            icon="psychology"-->
<!--            :color="caso.fecha_derivacion_psicologica ? 'primary' : 'grey-5'"-->
<!--          />-->
<!--          <q-timeline-entry-->
<!--            title="Informe área psicológica"-->
<!--            :subtitle="format(caso.fecha_informe_area_psicologica)"-->
<!--            icon="assignment_turned_in"-->
<!--            :color="caso.fecha_informe_area_psicologica ? 'primary' : 'grey-5'"-->
<!--          />-->
<!--          <q-timeline-entry-->
<!--            title="Entrega trabajo social"-->
<!--            :subtitle="format(caso.fecha_informe_trabajo_social)"-->
<!--            icon="diversity_3"-->
<!--            :color="caso.fecha_informe_trabajo_social ? 'primary' : 'grey-5'"-->
<!--          />-->
<!--          <q-timeline-entry-->
<!--            title="Derivación área legal"-->
<!--            :subtitle="format(caso.fecha_derivacion_area_legal)"-->
<!--            icon="gavel"-->
<!--            :color="caso.fecha_derivacion_area_legal ? 'primary' : 'grey-5'"-->
<!--          />-->

          <q-timeline-entry
            title="Derivación área legal"
            :subtitle="format(caso.fecha_derivacion_area_legal)"
            icon="gavel"
            :color="caso.fecha_derivacion_area_legal ? 'primary' : 'grey-5'"
          />
          <q-timeline-entry
            title="Aceptación área legal"
            :subtitle="format(caso.fecha_derivacion_area_legal)"
            icon="gavel"
            :color="caso.fecha_derivacion_area_legal ? 'primary' : 'grey-5'"
          />
          <q-timeline-entry
            title="Devolución área legal"
            :subtitle="format(caso.fecha_derivacion_area_legal)"
            icon="gavel"
            :color="caso.fecha_derivacion_area_legal ? 'primary' : 'grey-5'"
          />

          <q-timeline-entry
            title="Derivación área Psicologica"
            :subtitle="format(caso.fecha_derivacion_area_legal)"
            icon="gavel"
            :color="caso.fecha_derivacion_area_legal ? 'primary' : 'grey-5'"
          />
          <q-timeline-entry
            title="Aceptación área Psicologica"
            :subtitle="format(caso.fecha_derivacion_area_legal)"
            icon="gavel"
            :color="caso.fecha_derivacion_area_legal ? 'primary' : 'grey-5'"
          />
          <q-timeline-entry
            title="Devolución área Psicologica"
            :subtitle="format(caso.fecha_derivacion_area_legal)"
            icon="gavel"
            :color="caso.fecha_derivacion_area_legal ? 'primary' : 'grey-5'"
          />

          <q-timeline-entry
            title="Derivación área Social"
            :subtitle="format(caso.fecha_derivacion_area_legal)"
            icon="gavel"
            :color="caso.fecha_derivacion_area_legal ? 'primary' : 'grey-5'"
          />
          <q-timeline-entry
            title="Aceptación área Social"
            :subtitle="format(caso.fecha_derivacion_area_legal)"
            icon="gavel"
            :color="caso.fecha_derivacion_area_legal ? 'primary' : 'grey-5'"
          />
          <q-timeline-entry
            title="Devolución área Social"
            :subtitle="format(caso.fecha_derivacion_area_legal)"
            icon="gavel"
            :color="caso.fecha_derivacion_area_legal ? 'primary' : 'grey-5'"
          />

        </q-timeline>
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
export default {
  name: 'HojaRuta',
  props: {
    caseId: { type: [String, Number], required: true },
    caso: { type: Object, required: true }
  },
  data () {
    return {
      loading: false,
      editing: false,
      backup: null,
      form: {
        // solo los campos que usamos en esta vista
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
    today () {
      // YYYY-MM-DD
      const d = new Date()
      const mm = String(d.getMonth() + 1).padStart(2, '0')
      const dd = String(d.getDate()).padStart(2, '0')
      return `${d.getFullYear()}-${mm}-${dd}`
    },
    total () { return 5 }, // solo contamos las editables + apertura? si quieres 5 editables, cambia
    done () {
      // cuenta las que están llenas (incluyendo apertura)
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
    // this.fetch()
  },
  methods: {
    format (val) {
      if (!val) return '—'
      // val ya viene como YYYY-MM-DD
      return val
    },
    // async fetch () {
    //   this.loading = true
    //   try {
    //     const { data } = await this.$axios.get(`/casos/${this.caseId}`)
    //     // Tomamos solo los campos que necesitamos
    //     this.form = {
    //       caso_numero: data.caso_numero || '',
    //       fecha_apertura_caso: data.fecha_apertura_caso || '',
    //       fecha_derivacion_psicologica: data.fecha_derivacion_psicologica || '',
    //       fecha_informe_area_psicologica: data.fecha_informe_area_psicologica || '',
    //       fecha_informe_trabajo_social: data.fecha_informe_trabajo_social || '',
    //       fecha_derivacion_area_legal: data.fecha_derivacion_area_legal || ''
    //     }
    //     this.backup = { ...this.form }
    //   } catch (e) {
    //     this.$alert?.error(e?.response?.data?.message || 'No se pudo cargar la hoja de ruta')
    //   } finally {
    //     this.loading = false
    //   }
    // },
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
      // payload mínimo: solo campos de hoja de ruta (evita sobreescribir otros)
      const payload = {
        fecha_derivacion_psicologica: this.caso.fecha_derivacion_psicologica || null,
        fecha_informe_area_psicologica: this.caso.fecha_informe_area_psicologica || null,
        fecha_informe_trabajo_social: this.caso.fecha_informe_trabajo_social || null,
        fecha_derivacion_area_legal: this.caso.fecha_derivacion_area_legal || null
      }

      this.loading = true
      try {
        const { data } = await this.$axios.put(`/casos/${this.caseId}`, payload)
        // refrescamos con lo que devuelve el backend
        this.form = {
          ...this.form,
          fecha_derivacion_psicologica: data.data?.fecha_derivacion_psicologica || this.caso.fecha_derivacion_psicologica,
          fecha_informe_area_psicologica: data.data?.fecha_informe_area_psicologica || this.caso.fecha_informe_area_psicologica,
          fecha_informe_trabajo_social: data.data?.fecha_informe_trabajo_social || this.caso.fecha_informe_trabajo_social,
          fecha_derivacion_area_legal: data.data?.fecha_derivacion_area_legal || this.caso.fecha_derivacion_area_legal
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
