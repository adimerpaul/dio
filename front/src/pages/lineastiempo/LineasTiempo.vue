<template>
  <q-page class="q-pa-md">

    <!-- Toolbar -->
    <q-card flat bordered class="q-pa-sm q-mb-md">
      <div class="row items-center q-col-gutter-md">
        <div class="col-12 col-md">
          <q-input
            v-model="search"
            outlined dense debounce="400"
            placeholder="Buscar por N° de caso, denunciante, zona…"
            @input="onSearch"
          >
            <template v-slot:prepend><q-icon name="search" /></template>
            <template v-slot:append>
              <q-btn v-if="search" flat dense round icon="close" @click="clearSearch" />
            </template>
          </q-input>
        </div>
        <div class="col-auto">
          <q-select
            v-model="perPage" :options="[10,20,50]"
            dense outlined style="width:120px" label="Por página"
            @input="goFirstPage"
          />
        </div>
        <div class="col-auto">
          <q-btn color="primary" icon="refresh" label="Actualizar" no-caps :loading="loading" @click="fetchRows"/>
        </div>
      </div>
    </q-card>

    <!-- Tabla -->
    <q-markup-table flat bordered dense class="table--wrap">
      <thead>
      <tr>
        <th style="width:56px">#</th>
        <th style="min-width:140px">Nº Caso</th>
        <th style="min-width:220px">Denunciante</th>
        <th style="min-width:240px">Psicológico</th>
        <th style="min-width:240px">Social</th>
        <th style="min-width:240px">Legal</th>
        <th style="min-width:110px">Zona</th>
      </tr>
      </thead>

      <tbody v-if="!loading && rows.data.length">
      <tr v-for="(r, idx) in rows.data" :key="r.id" class="hover-row">
        <td class="text-right">{{ rowIndex(idx) }}</td>

        <td class="text-no-wrap">
          <div class="row items-center q-gutter-xs">
            <q-chip dense square color="blue-1" text-color="blue-9">{{ r.tipo || '—' }}</q-chip>
            <div class="text-weight-medium">{{ r.caso_numero || '—' }}</div>
          </div>
          <div class="text-caption text-grey-7">Creado: {{ r.creado_dmY }}</div>
        </td>

        <td>
          <div class="text-weight-medium ellipsis">{{ r.denunciante_nombre || '—' }}</div>
          <div class="text-caption text-grey-7" v-if="r.denunciante_nro">CI: {{ r.denunciante_nro }}</div>
        </td>

        <!-- Área: componente inline reutilizable -->
        <td>
          <div class="row items-center q-gutter-sm">
            <q-chip
              dense
              :color="areaColor(r.psico)"
              :text-color="areaTextColor(r.psico)"
            >
              {{ r.psico.entregado ? 'Entregado' : (r.psico.vencido ? 'Vencido' : 'Pendiente') }}
            </q-chip>
            <div class="text-caption">
              <template v-if="r.psico.entregado">
                Entregado en: <b>{{ r.psico.dias_hasta_entrega }}</b> días
                <span class="text-grey-7"> ({{ r.psico.entrego_dmY }})</span>
              </template>
              <template v-else>
                  <span v-if="r.psico.vencido" class="text-negative">
                    Vencido: <b>{{ Math.abs(r.psico.dias_restantes) }}</b> días
                  </span>
                <span v-else class="text-grey-7">
                    Dias: <b>{{ r.psico.dias_restantes }}</b> días
                  </span>
              </template>
            </div>
          </div>
        </td>

        <td>
          <div class="row items-center q-gutter-sm">
            <q-chip dense :color="areaColor(r.social)" :text-color="areaTextColor(r.social)">
              {{ r.social.entregado ? 'Entregado' : (r.social.vencido ? 'Vencido' : 'Pendiente') }}
            </q-chip>
            <div class="text-caption">
              <template v-if="r.social.entregado">
                Entregado en: <b>{{ r.social.dias_hasta_entrega }}</b> días
                <span class="text-grey-7"> ({{ r.social.entrego_dmY }})</span>
              </template>
              <template v-else>
                  <span v-if="r.social.vencido" class="text-negative">
                    Vencido: <b>{{ Math.abs(r.social.dias_restantes) }}</b> días
                  </span>
                <span v-else class="text-grey-7">
                    Dias: <b>{{ r.social.dias_restantes }}</b> días
                  </span>
              </template>
            </div>
          </div>
        </td>

        <td>
          <div class="row items-center q-gutter-sm">
            <q-chip dense :color="areaColor(r.legal)" :text-color="areaTextColor(r.legal)">
              {{ r.legal.entregado ? 'Entregado' : (r.legal.vencido ? 'Vencido' : 'Pendiente') }}
            </q-chip>
            <div class="text-caption">
              <template v-if="r.legal.entregado">
                Entregado en: <b>{{ r.legal.dias_hasta_entrega }}</b> días
                <span class="text-grey-7"> ({{ r.legal.entrego_dmY }})</span>
              </template>
              <template v-else>
                  <span v-if="r.legal.vencido" class="text-negative">
                    Vencido: <b>{{ Math.abs(r.legal.dias_restantes) }}</b> días
                  </span>
                <span v-else class="text-grey-7">
                    Dias: <b>{{ r.legal.dias_restantes }}</b> días
                  </span>
              </template>
            </div>
          </div>
        </td>

        <td class="text-no-wrap">
          <q-chip v-if="r.caso_zona" dense color="teal-1" text-color="teal-10">{{ r.caso_zona }}</q-chip>
          <span v-else>—</span>
        </td>
      </tr>
      </tbody>

      <tbody v-else-if="!loading && !rows.data.length">
      <tr><td colspan="7" class="text-center text-grey">Sin resultados</td></tr>
      </tbody>

      <tbody v-else>
      <tr><td colspan="7" class="text-center"><q-spinner-dots size="24px" class="q-mr-sm" /> Cargando…</td></tr>
      </tbody>
    </q-markup-table>

    <!-- Paginación -->
    <div class="row items-center justify-between q-mt-md">
      <div class="text-caption text-grey-7">
        Mostrando <b>{{ rows.from || 0 }}</b> – <b>{{ rows.to || 0 }}</b> de <b>{{ rows.total || 0 }}</b>
      </div>
      <q-pagination
        v-model="page"
        :max="rows.last_page || 1"
        max-pages="10"
        boundary-numbers
        direction-links
        @input="fetchRows"
      />
    </div>

  </q-page>
</template>

<script>
export default {
  name: 'LineasTiempoFaltanEntregado',
  data () {
    return {
      loading: false,
      search: '',
      page: 1,
      perPage: 10,
      rows: { data: [], current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 }
    }
  },
  created () { this.fetchRows() },
  methods: {
    areaColor (st) {
      if (st.entregado) return 'positive'
      if (st.vencido)   return 'negative'
      return 'grey-4'
    },
    areaTextColor (st) {
      return (st.entregado || st.vencido) ? 'white' : 'black'
    },

    async fetchRows () {
      this.loading = true
      try {
        const res = await this.$axios.get('/casos-linea-tiempo', {
          // cambia deadline_days si quieres otro SLA (ej. 90 días)
          params: { q: this.search || '', page: this.page, per_page: this.perPage, deadline_days: 365 }
        })
        const p = res.data || {}
        this.rows = {
          data: p.data || [],
          current_page: p.current_page || 1,
          last_page: p.last_page || 1,
          per_page: p.per_page || this.perPage,
          total: p.total || 0,
          from: p.from || 0,
          to: p.to || 0
        }
        if (p.current_page) this.page = p.current_page
      } catch (e) {
        this.$q.notify({ type: 'negative', message: e?.response?.data?.message || 'Error cargando datos' })
      } finally {
        this.loading = false
      }
    },
    onSearch () { this.page = 1; this.fetchRows() },
    clearSearch () { this.search = ''; this.onSearch() },
    goFirstPage () { this.page = 1; this.fetchRows() },
    rowIndex (idx) { return (this.rows.from || 0) + idx }
  }
}
</script>

<style scoped>
.table--wrap td { vertical-align: top; }
.hover-row:hover { background: #fafafa; }
.ellipsis { max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>
