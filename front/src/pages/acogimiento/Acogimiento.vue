<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-col-gutter-md q-mb-md">
      <div class="col-12 col-md">
        <q-input
          v-model="search"
          outlined dense debounce="400"
          placeholder="Buscar por Nurej"
          @update:model-value="onSearch"
        >
          <template #prepend><q-icon name="search" /></template>
          <template #append>
            <q-btn flat dense round icon="close" @click="clearSearch" v-if="search"/>
          </template>
        </q-input>
      </div>

      <div class="col-auto">
      </div>

      <div class="col-auto">
        <q-btn color="primary" icon="refresh" label="Buscar" no-caps @click="fetchCasos()" :loading="loading"/>
      </div>
    </div>
<!--    <pre>{{loading}}</pre>-->
<!--    <pre>{{casos.length}}</pre>-->
    <q-markup-table flat bordered dense>
      <thead>
      <tr>
        <th style="width: 60px">#</th>
        <th>Nº Caso</th>
        <th>Fecha</th>
        <th>Denunciante</th>
        <th>Denunciado</th>
        <th>Tipología</th>
        <th>Zona</th>
        <th>Tipo</th>
        <th>Alerta</th>
      </tr>
      </thead>
      <tbody v-if="!loading && casos.length">
      <tr v-for="(c, idx) in casos" :key="c.id" @click="$router.push('/acogimiento/' + c.id)" class="cursor-pointer">
        <td>{{ c.id }}</td>
        <td class="text-no-wrap">{{ c.caso_numero || '—' }}</td>
        <td class="text-no-wrap">{{ $filters.date(c.fecha_apertura_caso) }}</td>
        <td>
          <div class="text-weight-medium">{{ c.denunciante_nombre_completo }}</div>
          <div class="text-caption text-grey-7" v-if="c.denunciante_nro">CI: {{ c.denunciante_nro }}</div>
        </td>
        <td>
          <div class="text-weight-medium">{{ c.denunciado_nombre_completo || '—' }}</div>
          <div class="text-caption text-grey-7" v-if="c.denunciado_nro">CI: {{ c.denunciado_nro }}</div>
        </td>
        <td>{{ c.caso_tipologia || '—' }}</td>
        <td>{{ c.zona || '—' }}</td>
        <td>
          <q-badge
            :color="c.numero_apoyo_integral ? 'green' : 'grey-5'"
            :text-color="c.numero_apoyo_integral ? 'white' : 'black'"
            :label="c.numero_apoyo_integral ? 'Apoyo Integral' : 'Regular'"
            rounded
            dense
          />
        </td>
        <td>
          <template v-if="c.mi_estado?.me_asignado">
            <template v-if="c.mi_estado.primer_informe_hecho">
              <q-badge color="green" text-color="white" class="q-mr-xs">
                {{ c.mi_estado.label_listo || 'Primer informe listo' }}
              </q-badge>
            </template>
            <template v-else>
              <q-badge
                :color="c.mi_estado.atrasado ? 'red' : 'orange'"
                :text-color="c.mi_estado.atrasado ? 'white' : 'black'"
                class="q-mr-xs"
              >
                <template v-if="c.mi_estado.atrasado">
                  Atrasado {{ Math.abs(c.mi_estado.dias_restantes) }} d
                </template>
                <template v-else>
                  Faltan {{ c.mi_estado.dias_restantes }} d
                </template>
              </q-badge>
              <q-tooltip transition-show="jump-down" transition-hide="jump-up">
                Debes cargar tu
                <b>{{ c.mi_estado.rol === 'Psicologo' ? 'primera sesión' : 'primer informe' }}</b>.<br>
                Fecha límite: <b>{{ c.mi_estado.deadline }}</b>.
              </q-tooltip>
            </template>
          </template>
          <template v-else>
            <span class="text-grey-6">—</span>
          </template>
        </td>
      </tr>
      </tbody>

      <tbody v-else-if="!loading && !casos.length">
      <tr>
        <td colspan="8" class="text-center text-grey">
          No hay resultados para tu búsqueda.
        </td>
      </tr>
      </tbody>

      <tbody v-else>
      <tr>
        <td colspan="8" class="text-center">
          <q-spinner-dots size="24px" class="q-mr-sm" /> Cargando…
        </td>
      </tr>
      </tbody>
    </q-markup-table>
<!--    <pre>-->
<!--      {{ casos }}-->
<!--    </pre>-->

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
        @update:model-value="fetchCasos"
      />
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'CasosPage',
  data () {
    return {
      casos: [],
      loading: false,
      // server meta
      page: 1,
      perPage: 10,
      meta: { current_page: 1, last_page: 1, total: 0, from: 0, to: 0 },
      // search
      search: '',
      _searchTimer: null,
    }
  },
  mounted () {
    // this.fetchCasos()
  },
  methods: {
    fetchCasos () {
      if (this.search === '') {
        return this.$alert.info('Ingresa un Nurej para buscar')
      }

      this.loading = true
      this.$axios.get('search-nurej', {
        params: {
          nurej: this.search || '',
        }
      })
        .then(res => {
          // const r = res.data || {}
          this.casos = res.data || []
          // this.meta  = {
          //   current_page: r.current_page || 1,
          //   last_page:    r.last_page || 1,
          //   total:        r.total || 0,
          //   from:         r.from || 0,
          //   to:           r.to || 0
          // }
          // // en caso el backend cambie página por overflow
          // this.page = this.meta.current_page
        })
        .catch(e => this.$alert.error(e.response?.data?.message || 'Error cargando casos'))
        .finally(() => { this.loading = false })
    },
    onSearch () {
      // con debounce de <q-input /> casi basta; esto fuerza reset de página
      this.page = 1
      this.fetchCasos()
    },
    clearSearch () {
      this.search = ''
      this.onSearch()
    },
    goFirstPage () {
      this.page = 1
      this.fetchCasos()
    },
    rowIndex (idx) {
      // índice corrido por página
      return (this.meta.from || 0) + idx
    }
  }
}
</script>

<style scoped>
.ellipsis-2-lines {
  display: -webkit-box;
  -webkit-line-clamp: 2; /* número de líneas */
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
