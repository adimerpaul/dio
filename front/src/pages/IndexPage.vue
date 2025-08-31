<template>
  <q-page class="q-pa-md">
    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-sm-3" v-for="c in cards" :key="c.key">
        <q-card flat bordered class="q-pa-md text-center">
          <div class="text-caption text-grey-7">{{ c.label }}</div>
          <div class="text-h4 q-my-sm">{{ c.value ?? '—' }}</div>
          <div class="text-positive text-caption">+{{ c.mes }} este mes</div>
        </q-card>
      </div>
    </div>

    <!-- Series por mes -->
    <q-card flat bordered>
      <q-card-section>
        <div class="text-subtitle1">Evolución últimos 6 meses</div>
      </q-card-section>
      <apexchart
        type="area"
        height="350"
        :options="areaOptions"
        :series="areaSeries"
      />
    </q-card>
  </q-page>
</template>

<script>
import ApexCharts from 'vue3-apexcharts'

export default {
  name: 'IndexPage',
  components: { apexchart: ApexCharts },
  data () {
    return {
      loading: false,
      totales: {},
      esteMes: {},
      series: { labels: [], casos: [], prob: [], ses: [], inf: [] },
    }
  },
  computed: {
    cards () {
      return [
        { key: 'casos', label: 'Casos', value: this.totales.casos, mes: this.esteMes.casos },
        { key: 'prob', label: 'Problemáticas', value: this.totales.problematicas, mes: this.esteMes.problematicas },
        { key: 'ses', label: 'Sesiones', value: this.totales.sesiones, mes: this.esteMes.sesiones },
        { key: 'inf', label: 'Informes', value: this.totales.informes, mes: this.esteMes.informes },
      ]
    },
    areaOptions () {
      return {
        chart: { toolbar: { show: false } },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: { enabled: false },
        xaxis: { categories: this.series.labels },
        legend: { position: 'top' },
        grid: { borderColor: '#eee' },
      }
    },
    areaSeries () {
      return [
        { name: 'Casos', data: this.series.casos },
        { name: 'Problemáticas', data: this.series.prob },
        { name: 'Sesiones', data: this.series.ses },
        { name: 'Informes', data: this.series.inf },
      ]
    }
  },
  mounted () {
    this.fetchDashboard()
  },
  methods: {
    async fetchDashboard () {
      this.loading = true
      try {
        const res = await this.$axios.get('/dashboard')
        this.totales = res.data.totales || {}
        this.esteMes = res.data.esteMes || {}
        this.series  = res.data.series || this.series
      } catch (e) {
        this.$q.notify({ type: 'negative', message: 'Error cargando dashboard' })
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
