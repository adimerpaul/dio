<template>
  <q-page class="q-pa-md bg-grey-2">

    <!-- Header -->
    <div class="row items-center q-mb-md">
      <div class="col-16 col-md-6">
        <div class="text-h6 text-weight-bold">Agenda de Citas</div>
        <div class="text-caption text-grey-7">Gestión de citas psicológicas</div>
      </div>
      <div class="col-16 col-md-6 row q-gutter-sm">
        <q-select v-model="filters.status" dense outlined clearable label="Estado"
                  :options="statusOptions" style="width: 180px" @input="refetch" />
        <q-btn color="primary" icon="event" label="Nueva cita" @click="openCreateForNow" />
        <q-btn flat icon="refresh" @click="refetch" />
        <q-btn flat icon="picture_as_pdf" label="PDF" color="primary" @click="exportPdf" />
      </div>
    </div>

    <!-- Calendario -->
    <q-card flat bordered class="section-card">
      <q-card-section>
        <FullCalendar
          ref="calendar"
          :options="calendarOptions"
        />
      </q-card-section>
    </q-card>

    <!-- Diálogo: crear/editar -->
    <q-dialog v-model="dialog.open" persistent>
      <q-card style="min-width: 420px; max-width: 640px;">
        <q-card-section class="row items-center q-gutter-sm">
          <q-icon name="event" />
          <div class="text-subtitle1 text-weight-medium">{{ dialog.isEdit ? 'Editar cita' : 'Nueva cita' }}</div>
          <q-space />
        </q-card-section>
        <q-separator />

        <q-card-section class="q-gutter-md">
          <q-input v-model="form.title" dense outlined label="Título *" :rules="[req]"/>
          <div class="row q-col-gutter-sm">
            <div class="col-6">
              <q-input v-model="form.start" dense outlined type="datetime-local" label="Inicio *" :rules="[req]"/>
            </div>
            <div class="col-6">
              <q-input v-model="form.end" dense outlined type="datetime-local" label="Fin" />
            </div>
          </div>

          <q-checkbox v-model="form.all_day" label="Todo el día" />

          <!-- NUEVO: color -->
          <q-select
            v-model="form.color"
            :options="colorOptions"
            option-value="value"
            option-label="label"
            emit-value
            map-options
            dense
            outlined
            clearable
            label="Color de la cita"
          >
            <!-- opción con chip -->
            <template #option="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section avatar>
                  <q-chip square :style="{background: normalizeColor(scope.opt.value) || '#546E7A', color: textColor(normalizeColor(scope.opt.value) || '#546E7A')}" />
                </q-item-section>
                <q-item-section>{{ scope.opt.label }}</q-item-section>
              </q-item>
            </template>
            <!-- selected con chip -->
            <template #selected-item="scope">
              <q-chip
                square
                :style="{background: normalizeColor(scope.opt) || '#546E7A', color: textColor(normalizeColor(scope.opt) || '#546E7A')}"
                size="sm"
              >
                {{ (colorOptions.find(c => c.value === scope.opt) || {}).label || 'Color' }}
              </q-chip>
            </template>
          </q-select>

          <q-input v-model="form.caso_id" dense outlined type="number" label="Caso (ID)" />
          <q-input v-model="form.notes" type="textarea" autogrow dense outlined label="Notas" />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" v-close-popup />
          <q-btn color="primary" :label="dialog.isEdit ? 'Actualizar' : 'Crear'"
                 :loading="saving" @click="saveEvent"/>
          <q-btn v-if="dialog.isEdit" flat color="negative" label="Eliminar" :loading="deleting" @click="deleteEvent"/>
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
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'

export default {
  name: 'Agenda',
  components: { FullCalendar },
  data () {
    return {
      statusOptions: ['Programado','Reprogramado','Atendido','Cancelado'],
      filters: {
        status: null,
        user_id: null
      },
      dialog: {
        open: false,
        isEdit: false,
        currentId: null
      },
      colorOptions: [
        { label: 'Azul',     value: '#1976D2' },
        { label: 'Naranja',  value: '#FB8C00' },
        { label: 'Verde',    value: '#2E7D32' },
        { label: 'Rojo',     value: '#C62828' },
        { label: 'Gris',     value: '#546E7A' },
        { label: 'Morado',   value: '#6A1B9A' },
        { label: 'Cian',     value: '#0097A7' }
      ],
      form: {
        title: '',
        start: '',
        end: '',
        all_day: false,
        status: 'Programado',
        location: '',
        notes: '',
        caso_id: null,
        color: null
      },
      originalWhen: { start: null, end: null },
      saving: false,
      deleting: false,
      eventsCache: [],
      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        locale: esLocale,
        timeZone: 'local',
        initialView: 'timeGridWeek',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        selectable: true,
        editable: true,
        eventOverlap: true,
        events: (info, success, failure) => this.fetchEvents(info, success, failure),
        select: (selInfo) => this.handleSelect(selInfo),
        eventClick: (info) => this.handleEventClick(info),
        eventDrop: (info) => this.handleEventDrop(info),
        eventResize: (info) => this.handleEventResize(info),
        eventDisplay: 'block',
        nowIndicator: true,
        height: 'auto'
      }
    }
  },
  methods: {
    normalizeColor (c) {
      if (!c) return null
      if (typeof c !== 'string') c = String(c).trim()
      if (/^#[0-9A-Fa-f]{3}$/.test(c)) {
        const r = c[1], g = c[2], b = c[3]
        return `#${r}${r}${g}${g}${b}${b}`
      }
      if (/^#[0-9A-Fa-f]{6}$/.test(c)) return c
      return null
    },
    textColor (bg) {
      const c = this.normalizeColor(bg)
      if (!c) return 'white'
      const hex = c.slice(1)
      const r = parseInt(hex.slice(0, 2), 16)
      const g = parseInt(hex.slice(2, 4), 16)
      const b = parseInt(hex.slice(4, 6), 16)
      const lum = (0.299 * r + 0.587 * g + 0.114 * b)
      return lum > 150 ? 'black' : 'white'
    },
    toApiLocal (val) {
      if (!val) return null
      const withSeconds = val.length === 16 ? `${val}:00` : val
      return withSeconds.replace('T', ' ')
    },
    dateToLocalString (d) {
      const dt = (d instanceof Date) ? d : new Date(d)
      const pad = n => String(n).padStart(2, '0')
      const y = dt.getFullYear()
      const m = pad(dt.getMonth() + 1)
      const day = pad(dt.getDate())
      const hh = pad(dt.getHours())
      const mm = pad(dt.getMinutes())
      const ss = pad(dt.getSeconds())
      return `${y}-${m}-${day} ${hh}:${mm}:${ss}`
    },
    req (v) { return !!v || 'Requerido' },

    async fetchEvents (fetchInfo, success, failure) {
      try {
        const params = new URLSearchParams()
        params.set('start', fetchInfo.startStr.slice(0, 19))
        params.set('end',   fetchInfo.endStr.slice(0, 19))
        if (this.filters.status) params.set('status', this.filters.status)
        if (this.filters.user_id) params.set('user_id', this.filters.user_id)

        const { data } = await this.$axios.get(`/agendas?${params.toString()}`)
        const mapped = data.map(e => {
          const bg = this.normalizeColor(e.color) || this.colorFor(e)
          return {
            id: e.id,
            title: e.title,
            start: e.start,
            end: e.end,
            allDay: !!e.all_day,
            backgroundColor: bg,
            borderColor: bg,
            textColor: this.textColor(bg),
            extendedProps: {
              status: e.status,
              notes: e.notes,
              location: e.location,
              caso_id: e.caso_id,
              color: this.normalizeColor(e.color)
            }
          }
        })
        this.eventsCache = mapped
        success(mapped)
      } catch (err) {
        this.$q.notify({ type: 'negative', message: 'No se pudo cargar la agenda' })
        failure(err)
      }
    },

    colorFor (e) {
      if (e.color) return e.color
      switch (e.status) {
        case 'Programado':   return '#1976D2'
        case 'Reprogramado': return '#FB8C00'
        case 'Atendido':     return '#2E7D32'
        case 'Cancelado':    return '#C62828'
        default:             return '#546E7A'
      }
    },

    clearForm () {
      this.form = {
        title: '',
        start: '',
        end: '',
        all_day: false,
        status: 'Programado',
        location: '',
        notes: '',
        caso_id: null,
        color: null
      }
      this.originalWhen = { start: null, end: null }
    },

    openCreate (startStr, endStr, allDay) {
      this.clearForm()
      this.dialog.isEdit = false
      this.dialog.currentId = null
      this.form.start = this.toLocalInputValue(startStr)
      this.form.end   = endStr ? this.toLocalInputValue(endStr) : ''
      this.form.all_day = !!allDay
      this.originalWhen.start = this.form.start
      this.originalWhen.end   = this.form.end
      this.dialog.open = true
    },

    openEdit (ev) {
      this.clearForm()
      this.dialog.isEdit = true
      this.dialog.currentId = ev.id
      this.form.title     = ev.title
      this.form.start     = this.toLocalInputValue(ev.startStr)
      this.form.end       = ev.endStr ? this.toLocalInputValue(ev.endStr) : ''
      this.form.all_day   = ev.allDay
      this.form.status    = ev.extendedProps?.status || 'Programado'
      this.form.location  = ev.extendedProps?.location || ''
      this.form.notes     = ev.extendedProps?.notes || ''
      this.form.caso_id   = ev.extendedProps?.caso_id || null
      this.form.color     = ev.extendedProps?.color || null
      this.originalWhen.start = this.form.start
      this.originalWhen.end   = this.form.end
      this.dialog.open = true
    },

    toLocalInputValue (iso) {
      return (iso || '').slice(0, 16)
    },

    handleSelect (selInfo) {
      this.openCreate(selInfo.startStr, selInfo.endStr, selInfo.allDay)
    },
    handleEventClick (info) {
      this.openEdit(info.event)
    },

    async handleEventDrop (info) {
      try {
        await this.$axios.put(`/agendas/${info.event.id}`, {
          start: this.dateToLocalString(info.event.start),
          end: info.event.end ? this.dateToLocalString(info.event.end) : null
        })
        this.$q.notify({ type: 'positive', message: 'Cita reprogramada' })
        await this.refetch()
      } catch (e) {
        info.revert()
        this.$q.notify({ type: 'negative', message: 'No se pudo reprogramar' })
      }
    },

    async handleEventResize (info) {
      try {
        await this.$axios.put(`/agendas/${info.event.id}`, {
          end: info.event.end ? this.dateToLocalString(info.event.end) : null
        })
        this.$q.notify({ type: 'positive', message: 'Duración actualizada' })
        await this.refetch()
      } catch (e) {
        info.revert()
        this.$q.notify({ type: 'negative', message: 'No se pudo actualizar la duración' })
      }
    },

    async saveEvent () {
      this.saving = true
      try {
        if (!this.form.title || !this.form.start) {
          this.$q.notify({ type: 'negative', message: 'Completa título e inicio' })
          this.saving = false
          return
        }

        const isEdit = this.dialog.isEdit && this.dialog.currentId
        const payload = {
          title: this.form.title,
          all_day: this.form.all_day,
          status: this.form.status,
          location: this.form.location || null,
          notes: this.form.notes || null,
          caso_id: this.form.caso_id || null,
          color: this.form.color || null,
          start: this.toApiLocal(this.form.start),
          end: this.form.end ? this.toApiLocal(this.form.end) : null
        }

        if (isEdit) {
          await this.$axios.put(`/agendas/${this.dialog.currentId}`, payload)
          this.$q.notify({ type: 'positive', message: 'Cita actualizada' })
        } else {
          await this.$axios.post('/agendas', payload)
          this.$q.notify({ type: 'positive', message: 'Cita creada' })
        }
        this.dialog.open = false
        await this.refetch()
      } catch (e) {
        this.$q.notify({ type: 'negative', message: e?.response?.data?.message || 'No se pudo guardar la cita' })
      } finally {
        this.saving = false
      }
    },

    async deleteEvent () {
      if (!this.dialog.currentId) return
      this.deleting = true
      try {
        await this.$axios.delete(`/agendas/${this.dialog.currentId}`)
        this.$q.notify({ type: 'warning', message: 'Cita eliminada' })
        this.dialog.open = false
        await this.refetch()
      } catch (e) {
        this.$q.notify({ type: 'negative', message: 'No se pudo eliminar' })
      } finally {
        this.deleting = false
      }
    },

    async refetch () {
      const api = this.$refs.calendar?.getApi?.()
      if (api) api.refetchEvents()
    },

    openCreateForNow () {
      const now = new Date()
      const pad = n => String(n).padStart(2, '0')
      const y = now.getFullYear()
      const m = pad(now.getMonth() + 1)
      const d = pad(now.getDate())
      const hh = pad(now.getHours())
      const mm = pad(now.getMinutes())
      const startLocal = `${y}-${m}-${d}T${hh}:${mm}`
      const endObj = new Date(now.getTime() + 30*60000)
      const eh = pad(endObj.getHours())
      const em = pad(endObj.getMinutes())
      const endLocal = `${y}-${m}-${d}T${eh}:${em}`
      this.openCreate(startLocal, endLocal, false)
    },

    async exportPdf () {
      const events = this.eventsCache
      if (!events.length) {
        this.$q.notify({ type: 'warning', message: 'No hay citas para exportar' })
        return
      }
      const doc = new jsPDF()
      doc.setFontSize(16)
      doc.text('Agenda de Citas', 14, 20)
      doc.setFontSize(10)
      doc.text(`Fecha de exportación: ${new Date().toLocaleString()}`, 14, 28)
      const rows = events.map(e => [
        e.title,
        e.extendedProps?.status || '',
        e.start ? new Date(e.start).toLocaleString() : '',
        e.end ? new Date(e.end).toLocaleString() : '',
        e.extendedProps?.location || '',
        e.extendedProps?.caso_id || ''
      ])
      autoTable(doc, {
        head: [['Título', 'Estado', 'Inicio', 'Fin', 'Lugar', 'Caso']],
        body: rows,
        startY: 35,
        styles: { fontSize: 9 }
      })
      doc.save(`agenda-${new Date().toISOString().slice(0,10)}.pdf`)
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
  --fc-today-bg-color: rgba(25,118,210,0.1);
  --fc-event-bg-color: #1976D2;
  --fc-event-text-color: #fff;
  --fc-event-border-color: #1976D2;
}
</style>
