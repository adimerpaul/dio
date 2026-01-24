<template>
  <q-page class="q-pa-md">
    <!-- Toolbar -->
<!--    <pre>{{subtipo}}</pre>-->
<!--    <pre>{{$store.casoSelect}}</pre>-->
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
<!--        check fragancia-->
        <q-checkbox v-model="fragancia" label="Fragancia" @update:model-value="goFirstPage"/>
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
        <th style="width: 60px">#</th>
        <th>Nº</th>
        <th>Fecha</th>
<!--        <th>Victima</th>-->
        <th>Denunciante</th>
        <th>Denunciado</th>
        <th>Tipología</th>
        <th>Zona</th>

        <th class="col-extra">NUREJ</th>
        <th class="col-extra">CUD</th>
        <th class="col-extra">Juzgado</th>
        <th>Estado del caso</th>
        <th>Observaciones</th>
        <th>Alerta</th>
        <th>Dias</th>
      </tr>
      </thead>
      <tbody v-if="!loading && casos.length">
      <tr v-for="(c, idx) in casos" :key="c.id" @click="goToDetalle(c)" :class="'cursor-pointer ' + ($store.casoSelect.id === c.id ? 'bg-red-3' : '')">
        <td>{{ rowIndex(idx) }}</td>
        <td class="text-no-wrap">{{ tipo }} {{ c.caso_numero || '—' }}</td>
        <td class="text-no-wrap">{{ $filters.date?.(c.fecha_apertura_caso) ?? c.fecha_apertura_caso }}</td>

        <!-- Denunciante -->
<!--        <td>-->
<!--          <div class="text-weight-medium">{{ c.victimas?.[0].nombres_apellidos }}</div>-->
<!--          <div class="text-caption text-grey-7" v-if="c.victimas?.[0]?.ci">-->
<!--            CI: {{ c.victimas?.[0]?.ci }}-->
<!--          </div>-->
<!--        </td>-->
        <td>
          <div class="text-weight-medium">{{ nombrePersona(c.denunciantes?.[0], 'denunciante') }}</div>
          <div class="text-caption text-grey-7" v-if="c.denunciantes?.[0]?.denunciante_nro">
            CI: {{ c.denunciantes?.[0]?.denunciante_nro }}
          </div>
        </td>

        <!-- Denunciado -->
        <td>
          <div class="text-weight-medium">{{ nombrePersona(c.denunciados?.[0], 'denunciado') }}</div>
          <div class="text-caption text-grey-7" v-if="c.denunciados?.[0]?.denunciado_nro">
            CI: {{ c.denunciados?.[0]?.denunciado_nro }}
          </div>
        </td>

        <td>{{ c.caso_tipologia || '—' }}</td>
        <td>{{ c.zona || '—' }}</td>
        <td>
          {{ fmt(c.nurej) }}
        </td>
        <td>
          {{ fmt(c.cud) }}
        </td>
        <td>
          {{ fmt(c.numero_juzgado) }}
        </td>
        <td>{{ c.estado_caso || '—' }}</td>
        <td class="ellipsis-2-lines">{{ fmt(c.observaciones) }}</td>
        <td>
          {{AniosMesesdiasAgo(c.fecha_apertura_caso)}} Dias
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
  <q-dialog v-model="dialogFormat" persistent maximized >
    <q-card class="q-pa-md">
      <q-card-section class="row items-center">
        <div class="text-h6">Visualizar Casos/Estado</div>
        <q-space />
        <q-btn icon="close" flat round dense @click="dialogFormat = false" />
      </q-card-section>
      <q-markup-table flat bordered dense separator="cell">
        <thead>
        <tr class="bg-primary text-white">
          <th style="width: 60px">#</th>
          <th>Nº</th>
          <th>Fecha</th>
          <th>Denunciante</th>
          <th>Denunciado</th>
          <th>Tipología</th>
          <th>Zona</th>
          <th>Estado</th>
          <th>Alerta</th>

          <!-- Extra, siempre render (sin v-if) -->
          <th class="col-extra">—</th>
          <th class="col-extra">Legal</th>
          <th class="col-extra">Psicológica</th>
          <th class="col-extra">Trabajo Social</th>
          <th class="col-extra">CUD</th>
          <th class="col-extra">NUREJ</th>
          <th class="col-extra">Juzgado</th>
          <th class="col-extra">Fiscal</th>
          <th class="col-extra">Estado (Legal)</th>
          <th class="col-extra">Fecha Sentencia</th>
          <th class="col-extra">Observaciones</th>
          <th class="col-extra">Entrega/Derivación</th>
        </tr>
        </thead>
        <tbody>
          <tr v-for="(c, idx) in casos" :key="c.id" @click="goToDetalle(c)" class="cursor-pointer">
            <td>{{ rowIndex(idx) }}</td>
            <td class="text-no-wrap">{{ tipo }} {{ c.caso_numero || '—' }}</td>
            <td class="text-no-wrap">{{ $filters.date?.(c.fecha_apertura_caso) ?? c.fecha_apertura_caso }}</td>

            <!-- Denunciante -->
            <td>
              <div class="text-weight-medium">{{ nombrePersona(c.denunciantes?.[0], 'denunciante') }}</div>
              <div class="text-caption text-grey-7" v-if="c.denunciantes?.[0]?.denunciante_nro">
                CI: {{ c.denunciantes?.[0]?.denunciante_nro }}
              </div>
            </td>

            <!-- Denunciado -->
            <td>
              <div class="text-weight-medium">{{ nombrePersona(c.denunciados?.[0], 'denunciado') }}</div>
              <div class="text-caption text-grey-7" v-if="c.denunciados?.[0]?.denunciado_nro">
                CI: {{ c.denunciados?.[0]?.denunciado_nro }}
              </div>
            </td>
            <td>{{ c.caso_tipologia || '—' }}</td>
            <td>{{ c.zona || '—' }}</td>
            <td>{{ c.estado_caso || '—' }}</td>
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
            <!-- Extra, siempre render (sin v-if) -->
            <td class="col-extra">—</td>
            <td class="col-extra">{{ c.legal_user?.name ?? '—' }}</td>
            <td class="col-extra">{{ c.psicologica_user?.name ?? '—' }}</td>
            <td class="col-extra">{{ c.trabajo_social_user?.name ?? '—' }}</td>
            <td class="col-extra">{{ fmt(c.cud) }}</td>
            <td class="col-extra">{{ fmt(c.nurej) }}</td>
            <td class="col-extra">{{ fmt(c.numero_juzgado) }}</td>
            <td class="col-extra">{{ fmt(c.responsable_fiscalia) }}</td>
            <td class="col-extra">{{ fmt(c.estado_caso || c.estado_caso_otro) }}</td>
            <td class="col-extra">{{ fmtFecha(c.fecha_sentencia) }}</td>
            <td class="col-extra ellipsis-2-lines">{{ fmt(c.observaciones) }}</td>
            <td class="col-extra">
              {{ c.numero_apoyo_integral ? fmtFecha(c.fecha_entrega_juzgado || c.fecha_derivacion_area_legal) : '—' }}
            </td>
          </tr>
        </tbody>
      </q-markup-table>

      <q-card-actions align="center" class="q-pt-md">
        <q-btn  label="Cerrar" color="grey-7" v-close-popup dense no-caps icon="close"/>
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import moment from 'moment';
export default {
  name: 'CasoTable',
  props: {
    tipo: { type: String, required: true },
    subtipo: { type: String, default: '' },
    tipologia: { type: String, default: '' },
    leerOnlyPendientesDeRuta: { type: Boolean, default: true },
    placeholder: { type: String, default: '' },
    perPageInit: { type: Number, default: 10 },
  },
  data () {
    return {
      fragancia: false,
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
    AniosMesesdiasAgo(fecha) {
      // a;os meses dias con moments
      if (!fecha) return '—';
      const fechaApertura = moment(fecha);
      const hoy = moment();
      const anios = hoy.diff(fechaApertura, 'years');
      fechaApertura.add(anios, 'years');
      const meses = hoy.diff(fechaApertura, 'months');
      fechaApertura.add(meses, 'months');
      const dias = hoy.diff(fechaApertura, 'days');
      return (anios > 0 ? anios + ' a ' : '') + (meses > 0 ? meses + ' m ' : '') + dias + ' d';
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
          fragancia: this.fragancia ? 1 : 0,
          tipologia: this.tipologia || '',
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
      if (this.subtipo === 'AntecedentesDna') {
        this.$router.push('/casos/' + c.id+'/dna')
      }else{
        this.$router.push('/casos/' + c.id)
      }
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
