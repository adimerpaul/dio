<template>
  <q-page class="q-pa-md">
    <!-- Toolbar -->
    <!--    <pre>{{subtipo}}</pre>-->
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
        <q-select
          v-model="perPage"
          :options="[10, 20, 50]"
          dense outlined style="width: 120px"
          label="Por página"
          @update:model-value="goFirstPage"
        />
      </div>
      <div class="col-auto row items-center">
        <q-btn color="primary" icon="refresh" label="Actualizar" no-caps
               @click="fetchCasos()" :loading="loading" class="q-mr-sm" size="10px"/>
        <q-btn v-if="isSLIM" color="green" icon="visibility" no-caps size="10px"
               :labelx="showExtra ? 'Ocultar columnas extra' : 'Visualizar Casos/Estado'"
               label="Visualizar Casos/Estado"
               @click="openDialogFormat"/>
        <slot name="actions" />
      </div>
    </div>

    <!-- Tabla -->
    <q-markup-table flat bordered dense>
      <thead>
      <tr class="bg-primary text-white">

<!--        #	Nº	Fecha	Denunciante	Denunciado	Nurej	NNA	Tipo de Acogida	Cuidador(a)	Juzgado-->
        <th style="width: 60px">#</th>
        <th>Nº</th>
<!--        <th>Fecha</th>-->
<!--        <th>Denunciante</th>-->
<!--        <th>Denunciado</th>-->
        <th>Nurej</th>
        <th>NNA</th>
        <th>Juzgado</th>
        <th>Tipo de Acogida</th>
        <th>Cuidador(a)</th>
      </tr>
      </thead>
      <tbody v-if="!loading && casos.length">
      <tr v-for="(c, idx) in casos" :key="c.id" @click="goToDetalle(c)" class="cursor-pointer">
        <td>{{ rowIndex(idx) }}</td>
<!--        <td class="text-no-wrap">{{ tipo }} {{ c.caso_numero || '—' }}</td>-->
<!--        <td class="text-no-wrap">{{ $filters.date?.(c.fecha_apertura_caso) ?? c.fecha_apertura_caso }}</td>-->

<!--        &lt;!&ndash; Denunciante &ndash;&gt;-->
<!--        <td>-->
<!--          <div class="text-weight-medium">{{ c.victimas?.[0].nombres_apellidos }}</div>-->
<!--          <div class="text-caption text-grey-7" v-if="c.victimas?.[0]?.ci">-->
<!--            CI: {{ c.victimas?.[0]?.ci }}-->
<!--          </div>-->
<!--        </td>-->
<!--        <td>-->
<!--          <div class="text-weight-medium">{{ nombrePersona(c.denunciantes?.[0], 'denunciante') }}</div>-->
<!--          <div class="text-caption text-grey-7" v-if="c.denunciantes?.[0]?.denunciante_nro">-->
<!--            CI: {{ c.denunciantes?.[0]?.denunciante_nro }}-->
<!--          </div>-->
<!--        </td>-->

<!--        &lt;!&ndash; Denunciado &ndash;&gt;-->
<!--        <td>-->
<!--          <div class="text-weight-medium">{{ nombrePersona(c.denunciados?.[0], 'denunciado') }}</div>-->
<!--          <div class="text-caption text-grey-7" v-if="c.denunciados?.[0]?.denunciado_nro">-->
<!--            CI: {{ c.denunciados?.[0]?.denunciado_nro }}-->
<!--          </div>-->
<!--        </td>-->

<!--        <td>{{ c.caso_tipologia || '—' }}</td>-->
<!--        <td>{{ c.zona || '—' }}</td>-->
<!--        <td>-->
<!--          {{ fmt(c.nurej) }}-->
<!--        </td>-->
<!--        <td>-->
<!--          {{ fmt(c.cud) }}-->
<!--        </td>-->
<!--        <td>-->
<!--          {{ fmt(c.numero_juzgado) }}-->
<!--        </td>-->
<!--        <td>{{ c.estado_caso || '—' }}</td>-->

<!--        &lt;!&ndash; Alerta &ndash;&gt;-->
<!--        <td>-->
<!--          <template v-if="c.mi_estado?.me_asignado">-->
<!--            <template v-if="c.mi_estado.primer_informe_hecho">-->
<!--              <q-badge color="green" text-color="white" class="q-mr-xs">-->
<!--                {{ c.mi_estado.label_listo || 'Primer informe listo' }}-->
<!--              </q-badge>-->
<!--            </template>-->
<!--            <template v-else>-->
<!--              <q-badge-->
<!--                :color="c.mi_estado.atrasado ? 'red' : 'orange'"-->
<!--                :text-color="c.mi_estado.atrasado ? 'white' : 'black'"-->
<!--                class="q-mr-xs"-->
<!--              >-->
<!--                <template v-if="c.mi_estado.atrasado">-->
<!--                  Atrasado {{ Math.abs(c.mi_estado.dias_restantes) }} d-->
<!--                </template>-->
<!--                <template v-else>-->
<!--                  Faltan {{ c.mi_estado.dias_restantes }} d-->
<!--                </template>-->
<!--              </q-badge>-->
<!--              <q-tooltip transition-show="jump-down" transition-hide="jump-up">-->
<!--                Debes cargar tu-->
<!--                <b>{{ c.mi_estado.rol === 'Psicologo' ? 'primera sesión' : 'primer informe' }}</b>.<br>-->
<!--                Fecha límite: <b>{{ c.mi_estado.deadline }}</b>.-->
<!--              </q-tooltip>-->
<!--            </template>-->
<!--          </template>-->
<!--          <template v-else>-->
<!--            <span class="text-grey-6">—</span>-->
<!--          </template>-->
<!--        </td>-->
        <td>{{ c.caso_numero || '—' }}</td>
<!--        <td class="text-no-wrap">{{ fmtFecha(c.fecha_apertura_caso) }}</td>-->
<!--        <td>-->
<!--          <div class="text-weight-medium">{{ nombrePersona(c.denunciantes?.[0], 'denunciante') }}</div>-->
<!--          <div class="text-caption text-grey-7" v-if="c.denunciantes?.[0]?.denunciante_nro">-->
<!--            CI: {{ c.denunciantes?.[0]?.denunciante_nro }}-->
<!--          </div>-->
<!--        </td>-->
<!--        <td>-->
<!--          <div class="text-weight-medium">{{ nombrePersona(c.denunciados?.[0], 'denunciado') }}</div>-->
<!--          <div class="text-caption text-grey-7" v-if="c.denunciados?.[0]?.denunciado_nro">-->
<!--            CI: {{ c.denunciados?.[0]?.denunciado_nro }}-->
<!--          </div>-->
<!--        </td>-->
        <td>{{ fmt(c.nurej) }}</td>
        <td>
<!--          {{ fmt(c.nna_nombre) }} es la victima-->
          <div class="text-weight-medium">{{ c.victimas?.[0]?.nombres_apellidos || '—' }}</div>
          <div class="text-caption text-grey-7" v-if="c.victimas?.[0]?.ci">
            CI: {{ c.victimas?.[0]?.ci }}
          </div>
        </td>
        <td>{{ fmt(c.numero_juzgado) }}</td>
        <td>
          <q-chip
            v-if="c.acogimientos.length"
            dense
            outlined
            :color="tipoAcogidaConfig(c.acogimientos[0]?.tipo_de_acogida).chipColor"
          >
            <q-icon
              name="check_circle"
              size="16px"
              class="q-mr-xs"
              :color="tipoAcogidaConfig(c.acogimientos[0]?.tipo_de_acogida).iconColor"
            />
            <span :class="tipoAcogidaConfig(c.acogimientos[0]?.tipo_de_acogida).textClass">
      {{ c.acogimientos[0]?.tipo_de_acogida || '—' }}
    </span>
          </q-chip>
        </td>
        <td>{{ c.acogimientos[0]?.tipo_de_acogida == 'ACOGIDA INSTITUCIONAL (AI)' ? c.acogimientos[0]?.centro_de_acogida : c.acogimientos[0]?.cuidadora_nombre_completo }}</td>
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
  name: 'LugaresAcogidaNnaTabla',
  props: {
    tipo: { type: String, required: false },
    subtipo: { type: String, default: '' },
    tipologia: { type: String, default: '' },
    leerOnlyPendientesDeRuta: { type: Boolean, default: true },
    placeholder: { type: String, default: '' },
    perPageInit: { type: Number, default: 10 },
  },
  data () {
    return {
      dialogFormat: false,
      casos: [],
      loading: false,
      page: 1,
      perPage: this.perPageInit,
      meta: { current_page: 1, last_page: 1, total: 0, from: 0, to: 0 },
      search: '',
      onlyPendientes: false,
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
    tipoAcogidaConfig (tipo) {
      const def = {
        chipColor: 'grey-3',
        iconColor: 'grey-8',
        textClass: 'text-grey-8'
      }

      switch (tipo) {
        case 'ACOGIDA INSTITUCIONAL (AI)':
          return {
            chipColor: 'blue-3',
            iconColor: 'blue-10',
            textClass: 'text-blue-10'
          }
        case 'ACOGIMIENTO CON FAMILIA AMPLIADA (AFA)':
          return {
            chipColor: 'green-3',
            iconColor: 'green-10',
            textClass: 'text-green-10'
          }
        case 'ACOGIMIENTO CON FAMILIA COMUNITARIA (AFC)':
          return {
            chipColor: 'pink-3',
            iconColor: 'pink-9',
            textClass: 'text-pink-9'
          }
        case 'RESTITUCIÓN DE FAMILIA ORIGEN (RFO)':
          return {
            chipColor: 'purple-3',
            iconColor: 'purple-10',
            textClass: 'text-purple-10'
          }
        default:
          return def
      }
    },
    openDialogFormat(){
      this.dialogFormat=true;
    },
    // público: puedes llamar this.$refs.tabla.refresh()
    refresh () { this.fetchCasos() },

    fetchCasos () {
      this.loading = true
      this.$axios.get('/casos', {
        params: {
          q: this.search || '',
          tipologia: 'Acogimiento circunstancial',
          tipo: 'DNA',
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
      this.$router.push({
        name: 'lugar-acogida-nna',
        params: { id: c.id } // id del caso
      })
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
