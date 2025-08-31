<template>
  <q-page class="q-pa-md">
    <!-- Filtros -->
    <div class="row items-center q-col-gutter-md q-mb-md">
      <div class="col-12 col-md">
        <q-input v-model="search" outlined dense debounce="400"
                 placeholder="Buscar por usuario, evento, modelo, IP, URL, texto en cambios…"
                 @update:model-value="onSearch">
          <template #prepend><q-icon name="search" /></template>
          <template #append>
            <q-btn flat dense round icon="close" v-if="search" @click="clearSearch"/>
          </template>
        </q-input>
      </div>

      <div class="col-auto">
        <q-select v-model="type" :options="typeOptions" dense outlined style="width: 180px"
                  label="Entidad" @update:model-value="goFirst"/>
      </div>

      <div class="col-auto">
        <q-select v-model="event" :options="eventOptions" dense outlined style="width: 160px"
                  label="Evento" @update:model-value="goFirst"/>
      </div>

      <div class="col-auto">
        <q-input v-model="start" type="date" dense outlined label="Desde" style="width: 150px"/>
      </div>
      <div class="col-auto">
        <q-input v-model="end" type="date" dense outlined label="Hasta" style="width: 150px"/>
      </div>

      <div class="col-auto">
        <q-select v-model="perPage" :options="[10,20,50]" dense outlined style="width:120px"
                  label="Por página" @update:model-value="goFirst"/>
      </div>

      <div class="col-auto">
        <q-btn color="primary" icon="refresh" label="Actualizar" no-caps :loading="loading"
               @click="fetchRows"/>
      </div>
    </div>

    <!-- Tabla -->
    <q-markup-table flat bordered dense>
      <thead>
      <tr>
        <th style="width:70px">#</th>
        <th style="width:130px">Fecha</th>
        <th style="width:120px">Evento</th>
        <th style="width:190px">Usuario</th>
        <th>Entidad</th>
        <th>Cambios</th>
        <th style="width:130px">Acciones</th>
      </tr>
      </thead>
      <tbody v-if="!loading && rows.data.length">
      <tr v-for="(r, i) in rows.data" :key="r.id">
        <td class="text-grey-8">#{{ r.id }}</td>
        <td class="text-no-wrap">{{ $filters.dateDmYHis(r.created_at) }}</td>
        <td>
          <q-badge :color="badgeColor(r.event)" align="middle" class="q-px-sm">
            {{ r.event }}
          </q-badge>
        </td>
        <td>
          <div class="text-weight-medium">{{ r.user_label || '—' }}</div>
          <div class="text-caption text-grey-7" v-if="r.ip_address">IP {{ r.ip_address }}</div>
        </td>
        <td>
          <div>
            <b>{{ r.model_short }}</b> <span class="text-grey-7">#{{ r.auditable_id }}</span>
          </div>
          <div class="text-caption ellipsis">{{ r.auditable_type }}</div>
        </td>
        <td>
          <div v-if="r.changed_count">
            {{ r.changed_count }} campo(s)
            <span class="text-caption text-grey-7" v-if="r.summary_fields?.length">
              — {{ r.summary_fields.join(', ') }}
            </span>
          </div>
          <span v-else class="text-grey">—</span>
        </td>
        <td class="text-no-wrap">
          <q-btn dense flat icon="visibility" @click="openShow(r)" />
          <q-btn dense flat icon="open_in_new" v-if="r.entity_route" @click="goToEntity(r.entity_route)" />
        </td>
      </tr>
      </tbody>

      <tbody v-else-if="!loading">
      <tr><td colspan="7" class="text-center text-grey q-pa-lg">Sin resultados</td></tr>
      </tbody>

      <tbody v-else>
      <tr><td colspan="7" class="text-center">
        <q-spinner-dots size="24px" class="q-mr-sm"/> Cargando…
      </td></tr>
      </tbody>
    </q-markup-table>

    <!-- Footer / Paginación -->
    <div class="row items-center justify-between q-mt-md">
      <div class="text-caption text-grey-7">
        Mostrando <b>{{ rows.from || 0 }}</b>–<b>{{ rows.to || 0 }}</b> de <b>{{ rows.total || 0 }}</b>
      </div>
      <q-pagination v-model="page" :max="rows.last_page || 1"
                    boundary-numbers direction-links @update:model-value="fetchRows"/>
    </div>

    <!-- Dialog Detalle -->
    <q-dialog v-model="dlg" persistent>
      <q-card style="max-width: 900px; width: 95vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1">Detalle de auditoría #{{ showRow?.audit?.id }}</div>
          <q-space/><q-btn flat round dense icon="close" v-close-popup/>
        </q-card-section>

        <q-card-section class="q-gutter-sm">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <div class="text-caption text-grey-7">Evento</div>
              <div><q-badge :color="badgeColor(showRow?.audit?.event)">{{ showRow?.audit?.event }}</q-badge></div>
            </div>
            <div class="col-12 col-md-6">
              <div class="text-caption text-grey-7">Fecha</div>
              <div>{{ $filters.dateDmYHis(showRow?.audit?.created_at) }}</div>
            </div>

            <div class="col-12 col-md-6">
              <div class="text-caption text-grey-7">Usuario</div>
              <div>{{ showRow?.audit?.user_name || showRow?.audit?.user_username || '—' }}</div>
              <div class="text-caption text-grey-7" v-if="showRow?.audit?.user_email">{{ showRow.audit.user_email }}</div>
            </div>

            <div class="col-12 col-md-6">
              <div class="text-caption text-grey-7">Entidad</div>
              <div><b>{{ showRow?.audit?.model_short }}</b> #{{ showRow?.audit?.auditable_id }}</div>
              <div class="text-caption text-grey-7 ellipsis">{{ showRow?.audit?.auditable_type }}</div>
            </div>

            <div class="col-12">
              <q-separator/>
            </div>

            <div class="col-12">
              <div class="text-subtitle2 q-mb-xs">Cambios</div>

              <q-markup-table dense flat bordered v-if="showRow?.diff?.length">
                <thead><tr><th style="width:220px">Campo</th><th>Antes</th><th>Después</th></tr></thead>
                <tbody>
                <tr v-for="d in showRow.diff" :key="d.field">
                  <td class="text-no-wrap">{{ d.field }}</td>
                  <td><pre class="m-pre">{{ pretty(d.old) }}</pre></td>
                  <td><pre class="m-pre">{{ pretty(d.new) }}</pre></td>
                </tr>
                </tbody>
              </q-markup-table>

              <div v-else class="text-grey">No hay diferencias (o registro de creación/eliminación sin valores).</div>
            </div>

            <div class="col-12">
              <q-separator/>
            </div>

            <div class="col-12 col-md-6">
              <div class="text-caption text-grey-7">IP</div>
              <div>{{ showRow?.audit?.ip_address || '—' }}</div>
            </div>
            <div class="col-12 col-md-6">
              <div class="text-caption text-grey-7">URL</div>
              <div class="ellipsis" :title="showRow?.audit?.url">{{ showRow?.audit?.url || '—' }}</div>
            </div>
            <div class="col-12">
              <div class="text-caption text-grey-7">User Agent</div>
              <div class="ellipsis-3" :title="showRow?.audit?.user_agent">{{ showRow?.audit?.user_agent || '—' }}</div>
            </div>
          </div>
        </q-card-section>

        <q-separator/>
        <q-card-actions align="right">
          <q-btn flat label="Cerrar" v-close-popup/>
          <q-btn v-if="showRow?.audit?.entity_route" color="primary" label="Ir al registro"
                 @click="goToEntity(showRow.audit.entity_route)"/>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'Auditorias',
  data () {
    const today = new Date()
    const toISO = d => d.toISOString().slice(0, 10)
    return {
      loading: false,
      rows: { data: [], last_page: 1, total: 0, from: 0, to: 0 },
      page: 1,
      perPage: 10,
      search: '',
      event: '',
      type: '',
      start: toISO(new Date(today.getFullYear(), today.getMonth(), 1)),
      end: toISO(today),

      dlg: false,
      showRow: null,

      eventOptions: [
        { label: 'Todos', value: '' },
        { label: 'created', value: 'created' },
        { label: 'updated', value: 'updated' },
        { label: 'deleted', value: 'deleted' },
      ],
      typeOptions: [
        { label: 'Todas', value: '' },
        { label: 'Caso', value: 'Caso' },
        { label: 'Problematica', value: 'Problematica' },
        { label: 'SesionPsicologica', value: 'SesionPsicologica' },
        { label: 'Informe', value: 'Informe' },
        { label: 'Documento', value: 'Documento' },
        { label: 'Fotografia', value: 'Fotografia' },
        { label: 'User', value: 'User' },
      ]
    }
  },
  mounted () { this.fetchRows() },
  methods: {
    async fetchRows () {
      this.loading = true
      try {
        const res = await this.$axios.get('/audits', {
          params: {
            q: this.search || '',
            event: this.event || '',
            type: this.type || '',
            start: this.start || '',
            end: this.end || '',
            page: this.page,
            per_page: this.perPage
          }
        })
        this.rows = res.data || { data: [], last_page: 1, total: 0 }
      } catch (e) {
        this.$q.notify({ type: 'negative', message: e?.response?.data?.message || 'No se pudo cargar auditorías' })
      } finally {
        this.loading = false
      }
    },
    onSearch () { this.page = 1; this.fetchRows() },
    clearSearch () { this.search = ''; this.onSearch() },
    goFirst () { this.page = 1; this.fetchRows() },

    badgeColor (ev) {
      if (ev === 'created') return 'green'
      if (ev === 'updated') return 'orange'
      if (ev === 'deleted') return 'red'
      return 'grey'
    },
    async openShow (row) {
      try {
        const res = await this.$axios.get(`/audits/${row.id}`)
        this.showRow = res.data || null
        this.dlg = true
      } catch (e) {
        this.$q.notify({ type: 'negative', message: e?.response?.data?.message || 'No se pudo cargar detalle' })
      }
    },
    pretty (v) {
      if (typeof v === 'object') return JSON.stringify(v, null, 2)
      return String(v)
    },
    goToEntity (route) {
      if (!route) return
      // si tus rutas SPA coinciden, empuja al router;
      // si necesitas abrir en nueva pestaña con base pública:
      if (route.startsWith('/')) {
        this.$router.push(route)
      } else {
        window.open(route, '_blank')
      }
    }
  }
}
</script>

<style scoped>
.ellipsis { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.ellipsis-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.m-pre {
  margin: 0;
  white-space: pre-wrap;
  word-break: break-word;
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  font-size: 12px;
}
</style>
