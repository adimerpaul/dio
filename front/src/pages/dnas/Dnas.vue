<template>
  <q-page class="q-pa-md">
    <!-- Barra superior -->
    <div class="row items-center q-col-gutter-md q-mb-md">
      <div class="col-12 col-md">
        <q-input
          v-model="search"
          outlined dense debounce="400"
          placeholder="Buscar por Código, denunciante, denunciado, tipo, zona…"
          @update:model-value="onSearch"
        >
          <template #prepend><q-icon name="search" /></template>
          <template #append>
            <q-btn flat dense round icon="close" @click="clearSearch" v-if="search"/>
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
        <q-btn color="primary" icon="refresh" label="Actualizar" no-caps @click="fetchDnas()" :loading="loading"/>
      </div>
    </div>

    <!-- Tabla -->
    <q-markup-table flat bordered dense>
      <thead>
      <tr>
        <th style="width: 60px">#</th>
        <th>Código</th>
        <th>Fecha</th>
        <th>Denunciante</th>
        <th>Denunciado</th>
        <th>Tipo proceso</th>
        <th>Zona</th>
        <th>Abogado(a)</th>
        <th>Men/Fam</th>
      </tr>
      </thead>

      <tbody v-if="!loading && dnas.length">
      <tr
        v-for="(c, idx) in dnas"
        :key="c.id"
        @click="$router.push('/dnas/' + c.id)"
        class="cursor-pointer"
      >
        <td>{{ rowIndex(idx) }}</td>
        <td class="text-no-wrap">{{ c.codigo || '—' }}</td>
        <td class="text-no-wrap">{{ $filters.date?.(c.fecha_atencion) || (c.fecha_atencion || '—') }}</td>

        <td>
          <div class="text-weight-medium">{{ c.denunciante_nombre || '—' }}</div>
          <div class="text-caption text-grey-7" v-if="c.denunciante_ci">CI: {{ c.denunciante_ci }}</div>
        </td>

        <td>
          <div class="text-weight-medium">{{ c.denunciado_nombre || '—' }}</div>
          <div class="text-caption text-grey-7" v-if="c.denunciado_ci">CI: {{ c.denunciado_ci }}</div>
        </td>

        <td>
          <q-badge color="blue-2" text-color="blue-10" :label="tipoLabel(c.tipo_proceso)" />
        </td>

        <td>{{ c.zona || '—' }}</td>

        <td>
          <span v-if="c.abogado?.name">{{ c.abogado.name }}</span>
          <span v-else class="text-grey-6">Sin asignar</span>
        </td>

        <td>
          <q-badge color="grey-3" text-color="black" rounded dense class="q-mr-xs">
            {{ c.menores_count ?? 0 }} M
          </q-badge>
          <q-badge color="grey-3" text-color="black" rounded dense>
            {{ c.familiares_count ?? 0 }} F
          </q-badge>
        </td>
      </tr>
      </tbody>

      <tbody v-else-if="!loading && !dnas.length">
      <tr>
        <td colspan="9" class="text-center text-grey">
          No hay resultados para tu búsqueda.
        </td>
      </tr>
      </tbody>

      <tbody v-else>
      <tr>
        <td colspan="9" class="text-center">
          <q-spinner-dots size="24px" class="q-mr-sm" /> Cargando…
        </td>
      </tr>
      </tbody>
    </q-markup-table>

    <!-- Paginación -->
    <div class="row items-center justify-between q-mt-md">
      <div class="text-caption text-grey-7">
        Mostrando
        <b>{{ meta.from || 0 }}</b> – <b>{{ meta.to || 0 }}</b>
        de <b>{{ meta.total || 0 }}</b> registros
      </div>

      <q-pagination
        v-model="page"
        :max="meta.last_page || 1"
        max-pages="10"
        boundary-numbers
        direction-links
        @update:model-value="fetchDnas"
      />
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'DnasPage',
  data () {
    return {
      dnas: [],
      loading: false,
      // server meta
      page: 1,
      perPage: 10,
      meta: { current_page: 1, last_page: 1, total: 0, from: 0, to: 0 },
      // search
      search: ''
    }
  },
  mounted () {
    this.fetchDnas()
  },
  methods: {
    tipoLabel (v) {
      const map = {
        PROCESO_PENAL: 'Procesos Penales',
        PROCESO_FAMILIAR: 'Procesos Familiares',
        PROCESO_NNA: 'Procesos Niñez y Adolescencia',
        APOYO_INTEGRAL: 'Apoyos Integrales'
      }
      return map[(v || '').toUpperCase()] || v || '—'
    },
    fetchDnas () {
      this.loading = true
      this.$axios.get('/dnas', {
        params: {
          q: this.search || '',
          page: this.page,
          per_page: this.perPage
        }
      })
        .then(res => {
          const r = res.data || {}
          this.dnas = r.data || []
          this.meta  = {
            current_page: r.current_page || 1,
            last_page:    r.last_page || 1,
            total:        r.total || 0,
            from:         r.from || 0,
            to:           r.to || 0
          }
          this.page = this.meta.current_page
        })
        .catch(e => this.$alert?.error?.(e.response?.data?.message || 'Error cargando DNAs'))
        .finally(() => { this.loading = false })
    },
    onSearch () {
      this.page = 1
      this.fetchDnas()
    },
    clearSearch () {
      this.search = ''
      this.onSearch()
    },
    goFirstPage () {
      this.page = 1
      this.fetchDnas()
    },
    rowIndex (idx) {
      return (this.meta.from || 0) + idx
    }
  }
}
</script>

<style scoped>
.cursor-pointer { cursor: pointer; }
</style>
