<template>
  <q-page class="q-pa-md">
    <!-- Filtros -->
    <div class="row items-center q-col-gutter-md q-mb-md">
      <div class="col-12 col-md">
        <q-input
          v-model="search"
          outlined dense debounce="400"
          placeholder="Buscar por N° de caso, denunciante, denunciado, tipología, zona…"
          @update:model-value="onSearch"
        >
          <template #prepend><q-icon name="search" /></template>
          <template #append>
            <q-btn flat dense round icon="close" @click="clearSearch" v-if="search" />
          </template>
        </q-input>
      </div>

      <div class="col-auto">
        <q-select
          v-model="perPage"
          :options="[10, 20, 50]"
          dense outlined style="width: 120px"
          label="Por página"
          @update:model-value="goFirstPage"
        />
      </div>

      <div class="col-auto">
        <q-btn color="primary" icon="refresh" label="Actualizar" no-caps :loading="loading" @click="fetchRows" />
      </div>
    </div>

    <!-- Tabla -->
    <q-markup-table flat bordered dense class="table--wrap">
      <thead>
      <tr>
        <th style="width:60px">#</th>
        <th style="min-width:110px">Nº Caso</th>
        <th style="min-width:110px">Denunciante</th>
        <th style="min-width:130px">Base (creación)</th>

        <th style="min-width:220px">Primera problemática</th>
        <th style="min-width:220px">Primera sesión psicológica</th>
        <th style="min-width:220px">Primer informe</th>

        <th style="min-width:120px">Tipología</th>
        <th style="min-width:120px">Zona</th>
      </tr>
      </thead>

      <tbody v-if="!loading && rows.data.length">
      <tr v-for="(r, idx) in rows.data" :key="r.id" @click="$router.push('/casos/' + r.id)" class="cursor-pointer">
        <td>{{ rowIndex(idx) }}</td>
        <td class="text-no-wrap">{{ r.caso_numero || '—' }}</td>
        <td>
<!--          <pre>{{r}}</pre>-->
          <div class="text-weight-medium">{{ r.denunciante_nombre || '—' }}</div>
          <div class="text-caption text-grey-7" v-if="r.denunciante_nro">CI: {{ r.denunciante_nro }}</div>
        </td>
        <td class="text-no-wrap">{{ r.base || '—' }}</td>

        <td>
          <div v-if="r.first_problematica?.exists">
            <b>{{ r.first_problematica.diff_human }}</b>
            <div class="text-caption text-grey-7">({{ r.first_problematica.date_human }})</div>
          </div>
          <div v-else class="text-grey">—</div>
        </td>

        <td>
          <div v-if="r.first_sesion?.exists">
            <b>{{ r.first_sesion.diff_human }}</b>
            <div class="text-caption text-grey-7">({{ r.first_sesion.date_human }})</div>
          </div>
          <div v-else class="text-grey">—</div>
        </td>

        <td>
          <div v-if="r.first_informe?.exists">
            <b>{{ r.first_informe.diff_human }}</b>
            <div class="text-caption text-grey-7">({{ r.first_informe.date_human }})</div>
          </div>
          <div v-else class="text-grey">—</div>
        </td>

        <td class="text-no-wrap">{{ r.caso_tipologia || '—' }}</td>
        <td class="text-no-wrap">{{ r.caso_zona || '—' }}</td>
      </tr>
      </tbody>

      <tbody v-else-if="!loading && !rows.data.length">
      <tr><td colspan="8" class="text-center text-grey">Sin resultados</td></tr>
      </tbody>

      <tbody v-else>
      <tr><td colspan="8" class="text-center"><q-spinner-dots size="24px" class="q-mr-sm" /> Cargando…</td></tr>
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
        @update:model-value="fetchRows"
      />
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'LineasTiempo',
  data () {
    return {
      loading: false,
      search: '',
      page: 1,
      perPage: 10,
      rows: { data: [], total: 0, last_page: 1, from: 0, to: 0 }
    }
  },
  mounted () { this.fetchRows() },
  methods: {
    async fetchRows () {
      this.loading = true
      try {
        const res = await this.$axios.get('/casos-linea-tiempo', {
          params: { q: this.search || '', page: this.page, per_page: this.perPage }
        })
        this.rows = res.data || { data: [], total: 0, last_page: 1, from: 0, to: 0 }
        // corrige la página si el backend la ajustó
        if (this.rows.current_page) this.page = this.rows.current_page
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
</style>
