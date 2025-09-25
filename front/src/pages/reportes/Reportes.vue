<!-- src/pages/reportes/ReportesCasos.vue -->
<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Toolbar -->
    <div class="toolbar q-pa-sm bg-white row items-center q-gutter-sm shadow-1">
      <div class="col">
        <div class="text-h6 text-weight-bold">Reportes de Casos</div>
        <div class="text-caption text-grey-7">Tortas por Tipología, Proceso y Módulo · Filtro por fechas</div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="clearFilters"/>
        <q-btn color="primary" icon="refresh" label="Actualizar" :loading="loading" @click="fetchReporte"/>
      </div>
    </div>

    <!-- Filtros -->
    <div class="q-mt-md">
      <q-card flat bordered class="section-card">
        <q-card-section>
          <div class="row items-center q-col-gutter-md">
            <div class="col-12 col-sm-3">
              <q-input v-model="filters.start" type="date" dense outlined label="Desde"/>
            </div>
            <div class="col-12 col-sm-3">
              <q-input v-model="filters.end" type="date" dense outlined label="Hasta"/>
            </div>
            <div class="col-12 col-sm-3">
              <q-select
                v-model="filters.tipo"
                :options="modulos"
                dense outlined clearable
                emit-value map-options
                label="Módulo (SLIM, DNA, SLAM, …)"
              />
            </div>
            <div class="col-12 col-sm-3">
              <q-btn color="primary" icon="refresh" label="Aplicar filtros" :loading="loading" class="full-width" @click="fetchReporte"/>
            </div>
          </div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <q-banner class="bg-indigo-1 text-indigo-10" rounded>
            <div class="text-subtitle1"><b>Total de casos:</b> {{ total ?? '—' }}</div>
            <div class="text-caption">
              Rango: {{ show(filters.start) }} → {{ show(filters.end) }}
              <span v-if="filters.tipo"> · Módulo: <b>{{ filters.tipo }}</b></span>
            </div>
          </q-banner>
        </q-card-section>
      </q-card>
    </div>

    <!-- Gráficos -->
    <div class="row q-col-gutter-md q-mt-md">
      <!-- Tipología -->
      <div class="col-12 col-md-4">
        <q-card flat bordered class="section-card">
          <q-card-section class="text-subtitle2 text-weight-bold">Por Tipología</q-card-section>
          <apexchart
            v-if="chart.tipologia.series.length"
            type="donut" height="300"
            :options="chart.tipologia.options"
            :series="chart.tipologia.series"
          />
          <div v-else class="q-pa-md text-grey">Sin datos</div>
        </q-card>
      </div>

      <!-- Tipo de Proceso / Modalidad -->
      <div class="col-12 col-md-4">
        <q-card flat bordered class="section-card">
          <q-card-section class="text-subtitle2 text-weight-bold">Por Tipo de Proceso</q-card-section>
          <apexchart
            v-if="chart.modalidad.series.length"
            type="donut" height="300"
            :options="chart.modalidad.options"
            :series="chart.modalidad.series"
          />
          <div v-else class="q-pa-md text-grey">Sin datos</div>
        </q-card>
      </div>

      <!-- Módulo -->
      <div class="col-12 col-md-4">
        <q-card flat bordered class="section-card">
          <q-card-section class="text-subtitle2 text-weight-bold">Por Módulo</q-card-section>
          <apexchart
            v-if="chart.tipo.series.length"
            type="donut" height="300"
            :options="chart.tipo.options"
            :series="chart.tipo.series"
          />
          <div v-else class="q-pa-md text-grey">Sin datos</div>
        </q-card>
      </div>
    </div>

    <!-- Top 10 Zonas -->
    <div class="row q-col-gutter-md q-mt-md">
      <div class="col-12">
        <q-card flat bordered class="section-card">
          <q-card-section class="text-subtitle2 text-weight-bold">Top 10 Zonas</q-card-section>
          <apexchart
            v-if="chart.zona.series.length"
            type="bar" height="320"
            :options="chart.zona.options"
            :series="chart.zona.series"
          />
          <div v-else class="q-pa-md text-grey">Sin datos</div>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
import ApexChart from 'vue3-apexcharts'

export default {
  name: 'ReportesCasos',
  components: { apexchart: ApexChart },
  data () {
    return {
      loading: false,
      total: null,
      modulos: [
        { label: 'SLIM', value: 'SLIM' },
        { label: 'DNA', value: 'DNA' },
        { label: 'SLAM', value: 'SLAM' },
        { label: 'UMADIS', value: 'UMADIS' },
        { label: 'PROPREMI', value: 'PROPREMI' }, // usa el valor real de tu BD
      ],
      filters: {
        start: '', // yyyy-mm-dd
        end:   '',
        tipo:  null
      },
      chart: {
        tipologia: { options: { labels: [], legend: { position: 'bottom' }, dataLabels: { enabled: true }, tooltip: { y: { formatter: v => `${v} casos` } } }, series: [] },
        modalidad: { options: { labels: [], legend: { position: 'bottom' }, dataLabels: { enabled: true }, tooltip: { y: { formatter: v => `${v} casos` } } }, series: [] },
        tipo:      { options: { labels: [], legend: { position: 'bottom' }, dataLabels: { enabled: true }, tooltip: { y: { formatter: v => `${v} casos` } } }, series: [] },
        zona:      { options: { xaxis: { categories: [] }, plotOptions: { bar: { borderRadius: 6 } }, legend: { show: false }, dataLabels: { enabled: false }, tooltip: { y: { formatter: v => `${v} casos` } } }, series: [] },
      }
    }
  },
  mounted () {
    this.fetchReporte()
  },
  watch: {
    'filters.tipo' () { this.fetchReporte() }
  },
  methods: {
    show (v) { return (v === null || v === undefined || v === '') ? '—' : String(v) },
    clearFilters () {
      this.filters.start = ''
      this.filters.end   = ''
      this.filters.tipo  = null
      this.fetchReporte()
    },
    toDonut (payload) {
      const data = payload || { labels: [], series: [] }
      return {
        options: {
          labels: (data.labels || []).map(v => v || '—'),
          legend: { position: 'bottom' },
          dataLabels: { enabled: true },
          tooltip: { y: { formatter: v => `${v} casos` } }
        },
        series: data.series || []
      }
    },
    toBar (cats, series) {
      return {
        options: {
          xaxis: { categories: (cats || []).map(v => v || '—') },
          plotOptions: { bar: { borderRadius: 6 } },
          dataLabels: { enabled: false },
          legend: { show: false },
          tooltip: { y: { formatter: v => `${v} casos` } }
        },
        series: [{ name: 'Casos', data: series || [] }]
      }
    },
    async fetchReporte () {
      this.loading = true
      try {
        const params = {}
        if (this.filters.start) params.start = this.filters.start
        if (this.filters.end)   params.end   = this.filters.end
        if (this.filters.tipo)  params.tipo  = this.filters.tipo

        // Tu axios ya tiene base `/api`, por eso no antepongo /api aquí
        const { data } = await this.$axios.get('/reportes/casos-resumen', { params })

        this.total = data?.total ?? 0
        this.chart.tipologia = this.toDonut(data?.tipologia)
        this.chart.modalidad = this.toDonut(data?.modalidad)
        this.chart.tipo      = this.toDonut(data?.tipo)
        this.chart.zona      = this.toBar(data?.zona_top10?.labels, data?.zona_top10?.series)
      } catch (e) {
        this.$alert?.error?.('No se pudo cargar el reporte')
        // console.error(e)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
.toolbar   { position: sticky; top: 0; z-index: 5; border-radius: 12px; }
.section-card { border-radius: 14px; overflow: hidden; margin-bottom: 16px; background: #fff; }
</style>
