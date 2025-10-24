<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Header -->
    <div class="row items-center q-mb-md">
      <div class="col">
        <div class="text-h6 text-weight-bold">Juventudes — Actividades / Talleres</div>
        <div class="text-caption text-grey-7">Gestión en calendario</div>
      </div>
      <div class="col-auto">
        <q-btn color="primary" icon="add_circle" label="Nuevo" @click="openCreateForNow" no-caps />
        <q-btn flat icon="refresh" class="q-ml-sm" @click="refetch" />
      </div>
    </div>

    <!-- Calendario -->
    <q-card flat bordered class="section-card">
      <q-card-section>
        <FullCalendar ref="calendar" :options="calendarOptions" />
      </q-card-section>
    </q-card>

    <!-- Diálogo Crear/Editar -->
    <q-dialog v-model="dialog.open" persistent>
      <q-card style="min-width: 640px; max-width: 900px;">
        <q-card-section class="row items-center q-gutter-sm">
          <q-icon name="event_note" />
          <div class="text-subtitle1 text-weight-medium">
            {{ dialog.isEdit ? 'Editar actividad/taller' : 'Nueva actividad/taller' }}
          </div>
          <q-space />
        </q-card-section>
        <q-separator />

        <q-card-section class="q-gutter-md">
          <div class="row q-col-gutter-sm">
            <div class="col-12 col-md-8">
              <q-input v-model="form.actividad" label="Actividad *" dense outlined :rules="[req]" />
            </div>
            <div class="col-12 col-md-4">
              <q-select
                v-model="form.area"
                :options="areaOpts"
                label="Área"
                dense
                outlined
                clearable
                use-input
                fill-input
              />
            </div>
          </div>

          <div class="row q-col-gutter-sm">
            <div class="col-12 col-md-6">
              <q-input v-model="form.poblacion" label="Población" dense outlined />
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model="form.lugar" label="Lugar" dense outlined />
            </div>
          </div>

          <div class="row q-col-gutter-sm">
            <div class="col-12 col-md-6">
              <q-input v-model="form.responsables" label="Responsables" dense outlined />
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model="form.colaboracion" label="Colaboración" dense outlined />
            </div>
          </div>

          <q-input v-model="form.descripcion" type="textarea" autogrow label="Descripción" dense outlined />
          <q-input v-model="form.observaciones" type="textarea" autogrow label="Observaciones" dense outlined />

          <div class="row q-col-gutter-sm">
            <div class="col-12">
              <q-checkbox
                v-model="form.all_day"
                label="Todo el día"
                @update:model-value="onToggleAllDay"
              />
            </div>
          </div>

          <div class="row q-col-gutter-sm">
            <div class="col-12 col-md-6">
              <q-input
                v-model="form.fecha_inicio"
                type="datetime-local"
                label="Fecha inicio *"
                dense
                outlined
                :rules="[req]"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model="form.fecha_final" type="datetime-local" label="Fecha final" dense outlined />
            </div>
          </div>

          <!-- Color -->
          <div class="row items-center q-col-gutter-sm">
            <div class="col-auto">
              <div class="text-caption text-grey-7">Color</div>
            </div>
            <div class="col">
              <div class="row q-col-gutter-xs items-center">
                <div v-for="c in colorOptions" :key="c" class="col-auto">
                  <q-chip
                    clickable
                    square
                    :style="{
                      background: c,
                      color: textColor(c),
                      width: '28px',
                      height: '28px',
                      padding: 0,
                      border: form.color === c ? '2px solid rgba(0,0,0,0.35)' : '1px solid rgba(0,0,0,0.12)',
                      boxShadow: form.color === c ? '0 0 0 2px rgba(0,0,0,0.08)' : 'none'
                    }"
                    @click="form.color = c"
                  />
                </div>
                <div class="col-auto">
                  <q-input v-model="form.color" dense outlined style="width: 140px" label="#HEX" />
                </div>
              </div>
            </div>
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" v-close-popup />
          <q-btn color="primary" :label="dialog.isEdit ? 'Actualizar' : 'Crear'" :loading="saving" @click="saveEvent" />
          <q-btn
            v-if="dialog.isEdit"
            flat
            color="negative"
            label="Eliminar"
            :loading="deleting"
            @click="deleteEvent"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import esLocale from '@fullcalendar/core/locales/es'

export default {
  name: 'JuventudTalleresCalendar',
  components: { FullCalendar },
  data () {
    return {
      dialog: { open: false, isEdit: false, currentId: null },
      areaOpts: [
        'Formación', 'Medio ambiente', 'Desarrollo', 'Salud', 'Conmemoración',
        'Emprendimiento', 'Cultura', 'Deporte'
      ],
      colorOptions: ['#1976D2', '#2E7D32', '#FB8C00', '#C62828', '#6A1B9A', '#0097A7', '#C2185B', '#7B1FA2', '#455A64', '#8E24AA'],
      form: {
        actividad: '',
        area: '',
        descripcion: '',
        poblacion: '',
        lugar: '',
        responsables: '',
        colaboracion: '',
        observaciones: '',
        fecha_inicio: '',
        fecha_final: '',
        color: '#1976D2',
        all_day: false
      },
      saving: false,
      deleting: false,
      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        locale: esLocale,
        timeZone: 'local',
        initialView: 'timeGridWeek',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00',
        selectable: true,
        editable: true,
        eventOverlap: false,
        eventDisplay: 'block',
        nowIndicator: true,
        height: 'auto',
        events: (info, success, failure) => this.fetchEvents(info, success, failure),
        select: (selInfo) => this.handleSelect(selInfo),
        eventClick: (info) => this.openEdit(info.event),
        eventDrop: (info) => this.handleEventMove(info, 'drop'),
        eventResize: (info) => this.handleEventMove(info, 'resize')
      }
    }
  },
  methods: {
    // -------- UX --------
    textColor (bg) {
      if (!bg) return 'white'
      const hex = bg.replace('#', '')
      const r = parseInt(hex.slice(0, 2), 16)
      const g = parseInt(hex.slice(2, 4), 16)
      const b = parseInt(hex.slice(4, 6), 16)
      const lum = (0.299 * r + 0.587 * g + 0.114 * b) / 255
      return lum > 0.58 ? 'black' : 'white'
    },
    colorByArea (area) {
      const map = {
        'Formación': '#1976D2',
        'Medio ambiente': '#2E7D32',
        'Desarrollo': '#FB8C00',
        'Salud': '#C62828',
        'Conmemoración': '#6A1B9A',
        'Emprendimiento': '#0097A7',
        'Cultura': '#C2185B',
        'Deporte': '#455A64'
      }
      return map[area] || '#1976D2'
    },

    // -------- Validación --------
    req (v) { return !!v || 'Requerido' },

    // -------- Fechas --------
    onToggleAllDay (val) {
      if (val) {
        if (this.form.fecha_inicio?.length >= 10) {
          const d = this.form.fecha_inicio.slice(0, 10)
          this.form.fecha_inicio = `${d}T00:00`
        }
        if (this.form.fecha_final?.length >= 10) {
          const d = this.form.fecha_final.slice(0, 10)
          this.form.fecha_final = `${d}T23:59`
        }
      }
    },
    toApiLocal (val, endOfDay = false) {
      if (!val) return null
      // Acepta "YYYY-MM-DD" o "YYYY-MM-DDTHH:mm"
      const date = val.includes('T') ? val : `${val}T${endOfDay ? '23:59' : '00:00'}`
      const withSeconds = date.length === 16 ? `${date}:00` : date
      return withSeconds.replace('T', ' ')
    },
    toLocalInputValue (iso) { return (iso || '').slice(0, 16) },
    dateToLocalString (d) {
      const dt = (d instanceof Date) ? d : new Date(d)
      const pad = n => String(n).padStart(2, '0')
      return `${dt.getFullYear()}-${pad(dt.getMonth() + 1)}-${pad(dt.getDate())} ${pad(dt.getHours())}:${pad(dt.getMinutes())}:${pad(dt.getSeconds())}`
    },

    // -------- Calendario handlers --------
    handleSelect ({ startStr, endStr }) {
      this.openCreate(startStr, endStr)
    },
    async handleEventMove (info, kind) {
      try {
        const payload = {
          fecha_inicio: this.dateToLocalString(info.event.start),
          fecha_final: info.event.end ? this.dateToLocalString(info.event.end) : null
        }
        await this.$axios.put(`/juventud-talleres/${info.event.id}`, payload)
        this.$q.notify({ type: 'positive', message: kind === 'drop' ? 'Actividad reprogramada' : 'Duración actualizada' })
        await this.refetch()
      } catch (e) {
        info.revert()
        this.$q.notify({ type: 'negative', message: 'No se pudo actualizar' })
      }
    },

    // -------- API listar --------
    async fetchEvents (_info, success, failure) {
      try {
        const { data } = await this.$axios.get('/juventud-talleres')
        const mapped = data.map(x => {
          const bg = x.color || this.colorByArea(x.area)
          const title = x.actividad || 'Actividad'
          const subtitle = x.lugar ? ` — ${x.lugar}` : ''
          const isAllDay =
            (x.fecha_inicio?.includes(' 00:00:00') || x.fecha_inicio?.includes('T00:00')) &&
            (!!x.fecha_final ? (x.fecha_final.includes(' 23:59:59') || x.fecha_final.includes('T23:59')) : true)

          return {
            id: x.id,
            title: `${title}${subtitle}`,
            start: x.fecha_inicio,
            end: x.fecha_final,
            allDay: isAllDay,
            backgroundColor: bg,
            borderColor: bg,
            textColor: this.textColor(bg),
            extendedProps: { ...x }
          }
        })
        success(mapped)
      } catch (e) {
        this.$q.notify({ type: 'negative', message: 'No se pudo cargar' })
        failure(e)
      }
    },

    // -------- Form helpers --------
    clearForm () {
      this.form = {
        actividad: '',
        area: '',
        descripcion: '',
        poblacion: '',
        lugar: '',
        responsables: '',
        colaboracion: '',
        observaciones: '',
        fecha_inicio: '',
        fecha_final: '',
        color: '#1976D2',
        all_day: false
      }
    },
    openCreate (startStr, endStr) {
      this.clearForm()
      this.dialog.isEdit = false
      this.dialog.currentId = null
      this.form.fecha_inicio = this.toLocalInputValue(startStr)
      this.form.fecha_final = endStr ? this.toLocalInputValue(endStr) : ''
      this.dialog.open = true
    },
    openCreateForNow () {
      const now = new Date()
      const pad = n => String(n).padStart(2, '0')
      const d = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`
      const t = `${pad(now.getHours())}:${pad(now.getMinutes())}`
      const start = this.form.all_day ? `${d}T00:00` : `${d}T${t}`
      this.openCreate(start, '')
    },
    openEdit (ev) {
      this.clearForm()
      this.dialog.isEdit = true
      this.dialog.currentId = ev.id
      const p = ev.extendedProps || {}

      // campos
      this.form.actividad = p.actividad || ev.title || ''
      this.form.area = p.area || ''
      this.form.descripcion = p.descripcion || ''
      this.form.poblacion = p.poblacion || ''
      this.form.lugar = p.lugar || ''
      this.form.responsables = p.responsables || ''
      this.form.colaboracion = p.colaboracion || ''
      this.form.observaciones = p.observaciones || ''
      this.form.color = p.color || ev.backgroundColor || '#1976D2'

      // fechas y all_day
      const startStr = ev.startStr
      const endStr = ev.endStr || ''
      const startIs00 = /T00:00/.test(startStr)
      const endIs2359 = /T23:59/.test(endStr)
      this.form.all_day = !!(ev.allDay || (startIs00 && (endStr ? endIs2359 : true)))
      this.form.fecha_inicio = this.toLocalInputValue(startStr)
      this.form.fecha_final = endStr ? this.toLocalInputValue(endStr) : ''

      this.dialog.open = true
    },

    // -------- Guardar / Eliminar --------
    async saveEvent () {
      if (!this.form.actividad || !this.form.fecha_inicio) {
        this.$q.notify({ type: 'warning', message: 'Completa Actividad y Fecha inicio' })
        return
      }
      this.saving = true
      try {
        const payload = { ...this.form }
        if (this.form.all_day) {
          payload.fecha_inicio = this.toApiLocal(this.form.fecha_inicio, false)
          payload.fecha_final = this.form.fecha_final ? this.toApiLocal(this.form.fecha_final, true) : null
        } else {
          payload.fecha_inicio = this.toApiLocal(this.form.fecha_inicio)
          payload.fecha_final = this.form.fecha_final ? this.toApiLocal(this.form.fecha_final) : null
        }
        delete payload.all_day

        if (this.dialog.isEdit && this.dialog.currentId) {
          await this.$axios.put(`/juventud-talleres/${this.dialog.currentId}`, payload)
          this.$q.notify({ type: 'positive', message: 'Actividad actualizada' })
        } else {
          await this.$axios.post('/juventud-talleres', payload)
          this.$q.notify({ type: 'positive', message: 'Actividad creada' })
        }
        this.dialog.open = false
        await this.refetch()
      } catch (e) {
        this.$q.notify({ type: 'negative', message: e?.response?.data?.message || 'Error al guardar' })
      } finally {
        this.saving = false
      }
    },

    async deleteEvent () {
      if (!this.dialog.currentId) return
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Eliminar esta actividad?',
        cancel: true,
        persistent: true
      }).onOk(async () => {
        this.deleting = true
        try {
          await this.$axios.delete(`/juventud-talleres/${this.dialog.currentId}`)
          this.$q.notify({ type: 'warning', message: 'Actividad eliminada' })
          this.dialog.open = false
          await this.refetch()
        } catch (e) {
          this.$q.notify({ type: 'negative', message: 'No se pudo eliminar' })
        } finally {
          this.deleting = false
        }
      })
    },

    async refetch () {
      const api = this.$refs.calendar?.getApi?.()
      if (api) api.refetchEvents()
    }
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
.section-card { border-radius: 14px; overflow: hidden; background: #fff; }

:deep(.fc) {
  --fc-page-bg-color: #fff;
  --fc-neutral-bg-color: #fafafa;
  --fc-border-color: #e0e0e0;
  --fc-today-bg-color: rgba(25, 118, 210, 0.10);
}
:deep(.fc-event) {
  border-radius: 6px;
  font-size: 0.86em;
  padding: 3px 6px;
  font-weight: 600; /* mejor legibilidad sobre colores */
}
:deep(.fc-event .fc-event-title) {
  white-space: normal;
}
</style>
