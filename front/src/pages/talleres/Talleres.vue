<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Header -->
    <div class="row items-center q-mb-md">
      <div class="col">
        <div class="text-h6 text-weight-bold">Talleres</div>
        <div class="text-caption text-grey-7">Gestión en calendario</div>
      </div>
      <div class="col-auto">
        <q-btn color="primary" icon="add" label="Nuevo Taller" @click="openCreateForNow" />
        <q-btn flat icon="refresh" class="q-ml-sm" @click="refetch" />
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

    <!-- Diálogo Crear/Editar -->
    <q-dialog v-model="dialog.open" persistent>
      <q-card style="min-width: 500px; max-width: 700px;">
        <q-card-section class="row items-center q-gutter-sm">
          <q-icon name="workshop" />
          <div class="text-subtitle1 text-weight-medium">
            {{ dialog.isEdit ? 'Editar Taller' : 'Nuevo Taller' }}
          </div>
          <q-space />
        </q-card-section>
        <q-separator />

        <q-card-section class="q-gutter-md">
          <q-select
            v-model="form.solicitado"
            :options="solicitadoOpts"
            label="Solicitado *"
            dense outlined
            :rules="[req]"
          />

          <div class="row q-col-gutter-sm">
            <div class="col-6">
              <q-input
                v-model="form.fecha_inicio"
                type="datetime-local"
                label="Fecha inicio *"
                dense outlined
                :rules="[req]"
              />
            </div>
            <div class="col-6">
              <q-input
                v-model="form.fecha_final"
                type="datetime-local"
                label="Fecha final"
                dense outlined
              />
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
              <q-input
                v-model.number="form.numero_asistentes"
                type="number"
                min="0"
                label="Nº asistentes *"
                dense outlined
                :rules="[reqNumber]"
              />
            </div>
            <div class="col-6">
<!--              <q-input v-model="form.dirigido_a" label="Dirigido a" dense outlined />-->
<!--              ESTUDIANTE PADRES DE FAMILIA PROFESORES OTRO-->
              <q-select v-model="form.dirigido_a"
                        :options="['ESTUDIANTE', 'PADRES DE FAMILIA', 'PROFESORES/PERSONAL UE']"
                        label="Dirigido a"
                        dense outlined
              />

            </div>
          </div>

<!--          <q-input v-model="form.tema" label="Tema" dense outlined />-->
<!--          -PREVENCION DE ALCOHOLIMO    -PREVENCION DE ALCOHOLIMO Y DROGADICCION    -PREVENCION DE VIOLENCIA (BULLING)  -PREVENCION DE EMBARAZON NNA   -PRIMERA INFANCIA-->
          <q-select v-model="form.tema"
                    :options="['PREVENCION DE ALCOHOLIMO', 'PREVENCION DE ALCOHOLIMO Y DROGADICCION', 'PREVENCION DE VIOLENCIA (BULLING)', 'PREVENCION DE EMBARAZON NNA', 'PRIMERA INFANCIA']"
                    label="Tema"
                    dense outlined
          />
          <q-input v-model="form.descripcion" type="textarea" autogrow label="Descripción" dense outlined />

          <!-- Selector de color -->
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
            label="Color del taller"
          >
            <template #option="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section avatar>
                  <q-chip square :style="{background: scope.opt.value, color: textColor(scope.opt.value)}" />
                </q-item-section>
                <q-item-section>{{ scope.opt.label }}</q-item-section>
              </q-item>
            </template>
            <template #selected-item="scope">
              <q-chip
                square
                :style="{background: scope.opt, color: textColor(scope.opt)}"
                size="sm"
              >
                {{ (colorOptions.find(c => c.value === scope.opt) || {}).label || 'Color' }}
              </q-chip>
            </template>
          </q-select>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" v-close-popup />
          <q-btn
            color="primary"
            :label="dialog.isEdit ? 'Actualizar' : 'Crear'"
            :loading="saving"
            @click="saveEvent"
          />
          <q-btn
            v-if="dialog.isEdit"
            flat color="negative"
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
  name: 'TalleresCalendar',
  components: { FullCalendar },
  data () {
    return {
      dialog: {
        open: false,
        isEdit: false,
        currentId: null
      },
      solicitadoOpts: ['DIRECTOR', 'SECRETARIA', 'PERSONAL UE'],
      colorOptions: [
        { label: 'Azul', value: '#1976D2' },
        { label: 'Verde', value: '#2E7D32' },
        { label: 'Naranja', value: '#FB8C00' },
        { label: 'Rojo', value: '#C62828' },
        { label: 'Morado', value: '#6A1B9A' },
        { label: 'Turquesa', value: '#0097A7' },
        { label: 'Rosa', value: '#C2185B' }
      ],
      form: {
        solicitado: null,
        fecha_inicio: '',
        fecha_final: '',
        colegio: '',
        curso: '',
        numero_asistentes: 0,
        tema: '',
        descripcion: '',
        dirigido_a: '',
        color: '#1976D2'
      },
      saving: false,
      deleting: false,
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
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00',
        selectable: true,
        editable: true,
        eventOverlap: false,
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
    // Utilidades de color
    textColor (bgColor) {
      // if (!bgColor) return 'white'
      // const hex = bgColor.replace('#', '')
      // const r = parseInt(hex.substr(0, 2), 16)
      // const g = parseInt(hex.substr(2, 2), 16)
      // const b = parseInt(hex.substr(4, 2), 16)
      // const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255
      // return luminance > 0.5 ? 'black' : 'white'
    },

    // Validaciones
    req (v) { return !!v || 'Requerido' },
    reqNumber (v) {
      if (v === 0 || v === '0') return true
      return (!!v && Number(v) >= 0) || 'Número válido'
    },

    // Conversión de fechas
    toApiLocal (val) {
      if (!val) return null
      const withSeconds = val.length === 16 ? `${val}:00` : val
      return withSeconds.replace('T', ' ')
    },

    toLocalInputValue (iso) {
      return (iso || '').slice(0, 16)
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

    // Cargar eventos del calendario
    async fetchEvents (fetchInfo, success, failure) {
      try {
        const params = new URLSearchParams()
        params.set('start', fetchInfo.startStr.slice(0, 19))
        params.set('end', fetchInfo.endStr.slice(0, 19))

        const { data } = await this.$axios.get(`/talleres?${params.toString()}`)

        const mapped = data.map(taller => {
          const bgColor = taller.color || '#1976D2'
          return {
            id: taller.id,
            title: `${taller.colegio || 'Taller'} - ${taller.tema || 'Sin tema'}`,
            start: taller.fecha_inicio,
            end: taller.fecha_final,
            backgroundColor: bgColor,
            borderColor: bgColor,
            textColor: this.textColor(bgColor),
            extendedProps: {
              solicitado: taller.solicitado,
              colegio: taller.colegio,
              curso: taller.curso,
              numero_asistentes: taller.numero_asistentes,
              tema: taller.tema,
              descripcion: taller.descripcion,
              dirigido_a: taller.dirigido_a,
              color: taller.color
            }
          }
        })

        success(mapped)
      } catch (err) {
        this.$q.notify({ type: 'negative', message: 'No se pudo cargar los talleres' })
        failure(err)
      }
    },

    // Manejo del formulario
    clearForm () {
      this.form = {
        solicitado: null,
        fecha_inicio: '',
        fecha_final: '',
        colegio: '',
        curso: '',
        numero_asistentes: 0,
        tema: '',
        descripcion: '',
        dirigido_a: '',
        color: '#1976D2'
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

    openEdit (ev) {
      this.clearForm()
      this.dialog.isEdit = true
      this.dialog.currentId = ev.id

      // Cargar datos del evento
      const props = ev.extendedProps
      this.form.solicitado = props.solicitado
      this.form.fecha_inicio = this.toLocalInputValue(ev.startStr)
      this.form.fecha_final = ev.endStr ? this.toLocalInputValue(ev.endStr) : ''
      this.form.colegio = props.colegio || ''
      this.form.curso = props.curso || ''
      this.form.numero_asistentes = props.numero_asistentes || 0
      this.form.tema = props.tema || ''
      this.form.descripcion = props.descripcion || ''
      this.form.dirigido_a = props.dirigido_a || ''
      this.form.color = props.color || '#1976D2'

      this.dialog.open = true
    },

    // Interacciones del calendario
    handleSelect (selInfo) {
      this.openCreate(selInfo.startStr, selInfo.endStr)
    },

    handleEventClick (info) {
      this.openEdit(info.event)
    },

    async handleEventDrop (info) {
      try {
        await this.$axios.put(`/talleres/${info.event.id}`, {
          fecha_inicio: this.dateToLocalString(info.event.start),
          fecha_final: info.event.end ? this.dateToLocalString(info.event.end) : null
        })
        this.$q.notify({ type: 'positive', message: 'Taller reprogramado' })
        await this.refetch()
      } catch (e) {
        info.revert()
        this.$q.notify({ type: 'negative', message: 'No se pudo reprogramar' })
      }
    },

    async handleEventResize (info) {
      try {
        await this.$axios.put(`/talleres/${info.event.id}`, {
          fecha_final: info.event.end ? this.dateToLocalString(info.event.end) : null
        })
        this.$q.notify({ type: 'positive', message: 'Duración actualizada' })
        await this.refetch()
      } catch (e) {
        info.revert()
        this.$q.notify({ type: 'negative', message: 'No se pudo actualizar la duración' })
      }
    },

    // Guardar y eliminar
    async saveEvent () {
      // Validaciones
      if (!this.form.solicitado || !this.form.fecha_inicio || this.form.numero_asistentes === null) {
        this.$q.notify({ type: 'warning', message: 'Completa los campos obligatorios' })
        return
      }

      this.saving = true
      try {
        const payload = { ...this.form }

        // Convertir fechas al formato de la API
        payload.fecha_inicio = this.toApiLocal(this.form.fecha_inicio)
        payload.fecha_final = this.form.fecha_final ? this.toApiLocal(this.form.fecha_final) : null

        if (this.dialog.isEdit && this.dialog.currentId) {
          await this.$axios.put(`/talleres/${this.dialog.currentId}`, payload)
          this.$q.notify({ type: 'positive', message: 'Taller actualizado' })
        } else {
          await this.$axios.post('/talleres', payload)
          this.$q.notify({ type: 'positive', message: 'Taller creado' })
        }

        this.dialog.open = false
        await this.refetch()
      } catch (e) {
        this.$q.notify({
          type: 'negative',
          message: e?.response?.data?.message || 'Error al guardar'
        })
      } finally {
        this.saving = false
      }
    },

    async deleteEvent () {
      if (!this.dialog.currentId) return

      this.$q.dialog({
        title: 'Confirmar',
        message: `¿Eliminar este taller?`,
        cancel: true,
        persistent: true
      }).onOk(async () => {
        this.deleting = true
        try {
          await this.$axios.delete(`/talleres/${this.dialog.currentId}`)
          this.$q.notify({ type: 'warning', message: 'Taller eliminado' })
          this.dialog.open = false
          await this.refetch()
        } catch (e) {
          this.$q.notify({ type: 'negative', message: 'No se pudo eliminar' })
        } finally {
          this.deleting = false
        }
      })
    },

    // Utilidades
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
      this.openCreate(startLocal, '')
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
}

:deep(.fc-event) {
  border-radius: 4px;
  font-size: 0.85em;
  padding: 2px 4px;
}
</style>
