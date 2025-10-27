<template>
  <q-page class="q-pa-md">
    <!-- Toolbar -->
    <div class="row items-center q-col-gutter-md q-mb-md">
      <div class="col-12 col-md">
        <q-input
          v-model="search"
          outlined dense debounce="400"
          :placeholder="placeholderComputed"
          @update:model-value="onSearch"
        >
          <template #prepend><q-icon name="search" /></template>
          <template #append>
            <q-btn flat dense round icon="close" @click="clearSearch" v-if="search"/>
          </template>
        </q-input>
      </div>

      <div class="col-auto">
        <q-toggle
          v-model="onlyPendientes"
          color="primary"
          label="Solo mis pendientes"
          @update:model-value="goFirstPage"
        />
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

      <div class="col-auto row items-center">
        <!-- Botón refresh -->
        <q-btn color="primary" icon="refresh" label="Actualizar" no-caps
               @click="fetchCasos()" :loading="loading" class="q-mr-sm" size="10px"/>
        <!-- Solo para SLIM: alternar columnas extra -->
        <q-btn v-if="isSLIM" color="green" icon="visibility" no-caps size="10px"
               :label="showExtra ? 'Ocultar columnas extra' : 'Visualizar Casos'"
               @click="showExtra = !showExtra"/>
        <!-- Slot opcional para acciones extra -->
        <slot name="actions" />
      </div>
    </div>

    <!-- Tabla -->
    <q-markup-table flat bordered dense>
      <thead>
      <tr>
        <th style="width: 60px">#</th>
        <th>Nº</th>
        <th>Fecha</th>
        <th>Denunciante</th>
        <th>Denunciado</th>
        <th>Tipología</th>
        <th>Zona</th>
        <th>Estado</th>
        <th>Alerta</th>
      </tr>
      </thead>

      <tbody v-if="!loading && casos.length">
      <tr
        v-for="(c, idx) in casos"
        :key="c.id"
        @click="goToDetalle(c)"
        class="cursor-pointer"
      >
        <td>{{ rowIndex(idx) }}</td>
        <td class="text-no-wrap">{{ tipo }} {{ c.caso_numero || '—' }}</td>
        <td class="text-no-wrap">{{ $filters.date?.(c.fecha_apertura_caso) ?? c.fecha_apertura_caso }}</td>

        <!-- Denunciante -->
        <td>
          <div class="text-weight-medium">
            {{ nombrePersona(c.denunciantes?.[0], 'denunciante') }}
          </div>
          <div class="text-caption text-grey-7" v-if="c.denunciantes?.[0]?.denunciante_nro">
            CI: {{ c.denunciantes?.[0]?.denunciante_nro }}
          </div>
        </td>

        <!-- Denunciado -->
        <td>
          <div class="text-weight-medium">
            {{ nombrePersona(c.denunciados?.[0], 'denunciado') }}
          </div>
          <div class="text-caption text-grey-7" v-if="c.denunciados?.[0]?.denunciado_nro">
            CI: {{ c.denunciados?.[0]?.denunciado_nro }}
          </div>
        </td>

        <td>{{ c.caso_tipologia || '—' }}</td>
        <td>{{ c.zona || '—' }}</td>

        <!-- Tipo/Estado -->
        <td>
            {{ c.estado_caso || '—' }}
        </td>

        <!-- Alerta -->
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

        <!-- Columnas extra (solo SLIM) -->
        <template >
          <td>—</td>
          <td>{{ c.legal_user?.name ?? '—' }}</td>
          <td>{{ c.psicologica_user?.name ?? '—' }}</td>
          <td>{{ c.trabajo_social_user?.name ?? '—' }}</td>
          <td>{{ fmt(c.cud) }}</td>
          <td>{{ fmt(c.nurej) }}</td>
          <td>{{ fmt(c.numero_juzgado) }}</td>
          <td>{{ fmt(c.responsable_fiscalia) }}</td>
          <td>{{ fmt(c.estado_caso || c.estado_caso_otro) }}</td>
          <td>{{ fmtFecha(c.fecha_sentencia) }}</td>
          <td class="ellipsis-2-lines">{{ fmt(c.observaciones) }}</td>
          <td>{{ c.numero_apoyo_integral ? fmtFecha(c.fecha_entrega_juzgado || c.fecha_derivacion_area_legal) : '—' }}</td>
        </template>
      </tr>
      </tbody>

      <tbody v-else-if="!loading && !casos.length">
      <tr>
        <td :colspan="baseColspan" class="text-center text-grey">
          No hay resultados para tu búsqueda.
        </td>
      </tr>
      </tbody>

      <tbody v-else>
      <tr>
        <td :colspan="baseColspan" class="text-center">
          <q-spinner-dots size="24px" class="q-mr-sm" /> Cargando…
        </td>
      </tr>
      </tbody>
    </q-markup-table>

    <!-- Paginación -->
    <div class="row items-center justify-between q-mt-md">
      <div class="text-caption text-grey-7">
        Mostrando <b>{{ meta.from || 0 }}</b> – <b>{{ meta.to || 0 }}</b>
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
  name: 'CasoTable',
  props: {
    // SLIM | SLAM | DNA
    tipo: { type: String, required: true },
    // Si llega ?only_pendientes=1, lo respetamos (por defecto true)
    leerOnlyPendientesDeRuta: { type: Boolean, default: true },
    // Placeholder de búsqueda (si no envías, se genera con base al tipo)
    placeholder: { type: String, default: '' },
    // Per page inicial
    perPageInit: { type: Number, default: 10 },
  },
  data () {
    return {
      casos: [],
      loading: false,
      // server meta
      page: 1,
      perPage: this.perPageInit,
      meta: { current_page: 1, last_page: 1, total: 0, from: 0, to: 0 },
      // search
      search: '',
      // filtro pendientes
      onlyPendientes: false,
      // columnas extra (solo SLIM)
      showExtra: false,
    }
  },
  computed: {
    isSLIM () { return String(this.tipo).toUpperCase() === 'SLIM' },
    placeholderComputed () {
      if (this.placeholder) return this.placeholder
      const label = this.tipo?.toUpperCase() || 'CASO'
      return `Buscar por N° ${label}, denunciante, denunciado, tipología, zona…`
    },
    baseColspan () {
      // columnas base: #, Nº, Fecha, Denunciante, Denunciado, Tipología, Zona, Tipo/Estado, Alerta = 9
      // si SLIM + extra, suma 12 más = 21
      return (this.isSLIM && this.showExtra) ? 21 : 9
    }
  },
  mounted () {
    if (this.leerOnlyPendientesDeRuta) {
      this.onlyPendientes = String(this.$route?.query?.only_pendientes || '') === '1'
    }
    this.fetchCasos()
  },
  watch: {
    '$route.query.only_pendientes'(v) {
      if (this.leerOnlyPendientesDeRuta) {
        this.onlyPendientes = String(v || '') === '1'
        this.goFirstPage()
      }
    }
  },
  methods: {
    // público: puedes llamar this.$refs.tabla.refresh()
    refresh () { this.fetchCasos() },

    fetchCasos () {
      this.loading = true
      this.$axios.get('/casos', {
        params: {
          q: this.search || '',
          tipo: this.tipo,
          page: this.page,
          per_page: this.perPage,
          only_pendientes: this.onlyPendientes ? 1 : 0
        }
      })
        .then(res => {
          const r = res.data || {}
          this.casos = r.data || []
          this.meta  = {
            current_page: r.current_page || 1,
            last_page:    r.last_page || 1,
            total:        r.total || 0,
            from:         r.from || 0,
            to:           r.to || 0
          }
          this.page = this.meta.current_page
        })
        .catch(e => this.$alert?.error?.(e.response?.data?.message || 'Error cargando casos') || console.error(e))
        .finally(() => { this.loading = false })
    },
    onSearch () {
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
      return (this.meta.from || 0) + idx
    },
    goToDetalle (c) {
      this.$router.push('/casos/' + c.id)
    },
    nombrePersona (p, rol = 'denunciante') {
      if (!p) return '—'
      const n =
        (rol === 'denunciante')
          ? [p.denunciante_nombres, p.denunciante_paterno, p.denunciante_materno]
          : [p.denunciado_nombres, p.denunciado_paterno, p.denunciado_materno]
      const full = n.filter(Boolean).join(' ')
      return full || '—'
    },
    fmt (v) { return (v ?? '') !== '' ? v : '—' },
    fmtFecha (v) {
      if (!v) return '—'
      try {
        const d = new Date(String(v).replace(' ', 'T'))
        const dd = String(d.getDate()).padStart(2, '0')
        const mm = String(d.getMonth() + 1).padStart(2, '0')
        const yyyy = d.getFullYear()
        return `${dd}/${mm}/${yyyy}`
      } catch { return v }
    }
  }
}
</script>

<style scoped>
.ellipsis-2-lines {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
