<template>
  <q-page class="q-pa-md">
    <!-- Filtros -->
    <div class="row items-center q-col-gutter-md q-mb-md">
      <div class="col-12 col-sm-5">
        <q-input v-model="start" type="date" dense outlined label="Desde"/>
      </div>
      <div class="col-12 col-sm-5">
        <q-input v-model="end" type="date" dense outlined label="Hasta"/>
      </div>
      <div class="col-12 col-sm-2">
        <q-btn color="primary" icon="refresh" label="Actualizar" :loading="loading" @click="fetchKpis" />
      </div>
    </div>

    <!-- Tarjetas de totales -->
    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-sm-3" v-for="c in cards" :key="c.key">
        <q-card flat bordered class="kpi-card">
          <q-card-section>
            <div class="text-caption text-grey-7">{{ c.label }}</div>
            <div class="text-h4">{{ c.value ?? '—' }}</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Radial charts (promedios horas) -->
    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-md-4">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1">Promedio a 1ª Problemática (h)</q-card-section>
          <apexchart type="radialBar" height="240" :options="gaugeOptions('Problemática')" :series="[toPerc(avgHours.to_first_problematica)]"/>
          <div class="text-center q-pb-md text-grey-7">
            <span v-if="avgHours.to_first_problematica != null">{{ avgHours.to_first_problematica }} h</span>
            <span v-else>—</span>
          </div>
        </q-card>
      </div>
      <div class="col-12 col-md-4">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1">Promedio a 1ª Sesión (h)</q-card-section>
          <apexchart type="radialBar" height="240" :options="gaugeOptions('Sesión')" :series="[toPerc(avgHours.to_first_sesion)]"/>
          <div class="text-center q-pb-md text-grey-7">
            <span v-if="avgHours.to_first_sesion != null">{{ avgHours.to_first_sesion }} h</span>
            <span v-else>—</span>
          </div>
        </q-card>
      </div>
      <div class="col-12 col-md-4">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1">Promedio a 1º Informe (h)</q-card-section>
          <apexchart type="radialBar" height="240" :options="gaugeOptions('Informe')" :series="[toPerc(avgHours.to_first_informe)]"/>
          <div class="text-center q-pb-md text-grey-7">
            <span v-if="avgHours.to_first_informe != null">{{ avgHours.to_first_informe }} h</span>
            <span v-else>—</span>
          </div>
        </q-card>
      </div>
    </div>

    <!-- Series mensuales -->
    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1">Series mensuales</q-card-section>
          <apexchart
            type="area"
            height="340"
            :options="areaOptions"
            :series="areaSeries"
          />
        </q-card>
      </div>
    </div>

    <!-- Barras: Top Tipologías y Zonas -->
    <div class="row q-col-gutter-md">
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1">Top Tipologías</q-card-section>
          <apexchart type="bar" height="360" :options="barOptions('Tipología')" :series="[{ name: 'Casos', data: tipologiaVals }]" />
        </q-card>
      </div>
      <div class="col-12 col-md-6">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1">Top Zonas</q-card-section>
          <apexchart type="bar" height="360" :options="barOptions('Zona')" :series="[{ name: 'Casos', data: zonaVals }]" />
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
import ApexCharts from 'vue3-apexcharts'

export default {
  name: 'Kpis',
  components: { apexchart: ApexCharts },
  data () {
    const today = new Date()
    const toISO = d => d.toISOString().slice(0,10)
    const startDefault = new Date(today.getTime() - 89*24*60*60*1000) // 90 días

    return {
      loading: false,
      start: toISO(startDefault),
      end: toISO(today),

      // datos del backend
      totals: { casos: 0, problematicas: 0, sesiones: 0, informes: 0 },
      avgHours: { to_first_problematica: null, to_first_sesion: null, to_first_informe: null },
      mensual: { labels: [], casos: [], prob: [], ses: [], inf: [] },
      tipologia: [], zona: [],

      // series/bar helpers
      tipologiaLabels: [],
      tipologiaVals: [],
      zonaLabels: [],
      zonaVals: [],
    }
  },
  computed: {
    cards () {
      return [
        { key: 'casos', label: 'Casos', value: this.totals.casos },
        { key: 'problematicas', label: 'Problemáticas', value: this.totals.problematicas },
        { key: 'sesiones', label: 'Sesiones Psicológicas', value: this.totals.sesiones },
        { key: 'informes', label: 'Informes', value: this.totals.informes },
      ]
    },
    areaOptions () {
      return {
        chart: { toolbar: { show: true }, foreColor: '#666' },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: { enabled: false },
        xaxis: { categories: this.mensual.labels },
        legend: { position: 'top' },
        grid: { borderColor: '#eee' },
      }
    },
    areaSeries () {
      return [
        { name: 'Casos', data: this.mensual.casos },
        { name: 'Problemáticas', data: this.mensual.prob },
        { name: 'Sesiones', data: this.mensual.ses },
        { name: 'Informes', data: this.mensual.inf },
      ]
    }
  },
  mounted () {
    this.fetchKpis()
  },
  methods: {
    async fetchKpis () {
      this.loading = true
      try {
        const res = await this.$axios.get('/kpis', { params: { start: this.start, end: this.end } })
        const r = res.data || {}

        this.totals = r.totals || this.totals
        this.avgHours = r.avg_hours || this.avgHours
        this.mensual = r.mensual || this.mensual

        // tipologías
        this.tipologia = r.por_tipologia || []
        this.tipologiaLabels = this.tipologia.map(i => i.name || '—')
        this.tipologiaVals   = this.tipologia.map(i => i.value || 0)

        // zonas
        this.zona = r.por_zona || []
        this.zonaLabels = this.zona.map(i => i.name || '—')
        this.zonaVals   = this.zona.map(i => i.value || 0)

      } catch (e) {
        this.$q.notify({ type: 'negative', message: e?.response?.data?.message || 'No se pudo cargar KPIs' })
      } finally {
        this.loading = false
      }
    },

    // Gauge “bonito”: mapea horas a 0–100 (define un máximo esperable)
    toPerc (hours) {
      if (hours == null) return 0
      const MAX = 168 // 1 semana como techo
      const p = Math.max(0, Math.min(100, (hours / MAX) * 100))
      return Math.round(p)
    },

    gaugeOptions (label) {
      return {
        chart: { sparkline: { enabled: true } },
        plotOptions: {
          radialBar: {
            hollow: { size: '60%' },
            dataLabels: {
              name: { show: false },
              value: { show: true, fontSize: '22px', formatter: v => `${v}%` }
            }
          }
        },
        labels: [label]
      }
    },

    barOptions (label) {
      return {
        chart: { toolbar: { show: false } },
        xaxis: { categories: label === 'Tipología' ? this.tipologiaLabels : this.zonaLabels },
        plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#eee' }
      }
    }
  }
}
</script>

<style scoped>
.kpi-card { min-height: 100px; }
</style>
