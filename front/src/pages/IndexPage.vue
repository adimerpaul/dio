<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- FILTROS -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row q-col-gutter-sm items-end">
        <div class="col-12 col-sm-3">
          <q-input v-model="filters.from" type="date" label="Desde" dense outlined />
        </div>
        <div class="col-12 col-sm-3">
          <q-input v-model="filters.to" type="date" label="Hasta" dense outlined />
        </div>
        <div class="col-12 col-sm-3">
          <q-select
            v-model="filters.area"
            :options="filterOptions.areas"
            label="Área"
            dense
            outlined
            clearable
            v-if="$store.user?.role == 'Administrador'"
          />
<!--          <pre>{{$store.user}}</pre>-->
        </div>
        <div class="col-12 col-sm-auto">
          <q-btn
            color="primary"
            icon="search"
            label="Filtrar"
            no-caps
            :loading="loading"
            @click="fetchDashboard"
          />
        </div>
      </q-card-section>
    </q-card>

    <!-- TARJETAS RESUMEN -->
    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-sm-4" v-for="c in cards" :key="c.key">
        <q-card flat bordered class="q-pa-md text-center">
          <div class="text-caption text-grey-7">{{ c.label }}</div>
          <div class="text-h4 q-my-sm">{{ c.value ?? '—' }}</div>
          <div class="text-caption text-positive">
            {{ rangoTexto }}
          </div>
        </q-card>
      </div>
    </div>

    <!-- GRÁFICOS DONUT (Tipo / Tipología) -->
    <div class="row q-col-gutter-md q-mb-md">
      <!-- Si quieres recuperar "por área", descomentas este bloque y ajustas opciones -->
      <div class="col-12 col-md-4">
        <q-card flat bordered class="q-pa-md">
          <div class="text-subtitle2 q-mb-sm">
            DNA / SLIM: Hechos de fragancia vs denuncia
          </div>
          <apexchart
            type="donut"
            height="260"
            :options="getDonutOptions(
              'DNA / SLIM: fragancia vs denuncia',
              porFragancia.labels,
              'fragancia'
            )"
            :series="porFragancia.data"
          />
        </q-card>
      </div>

      <div class="col-12 col-md-4">
        <q-card flat bordered class="q-pa-md">
          <div class="text-subtitle2 q-mb-sm">Casos por tipo (click para ver detalle)</div>
          <apexchart
            type="donut"
            height="260"
            :options="getDonutOptions('Casos por tipo', porTipo.labels, 'tipo')"
            :series="porTipo.data"
          />
        </q-card>
      </div>

      <div class="col-12 col-md-4">
        <q-card flat bordered class="q-pa-md">
          <div class="text-subtitle2 q-mb-sm">Casos por tipología (click para ver detalle)</div>
          <apexchart
            type="donut"
            height="260"
            :options="getDonutOptions('Casos por tipología', porTipologia.labels, 'tipologia')"
            :series="porTipologia.data"
          />
        </q-card>
      </div>
    </div>

    <!-- BARRAS EDAD + SERIE TIEMPO -->
    <div class="row q-col-gutter-md">
      <div class="col-12 col-md-6">
        <q-card flat bordered class="q-pa-md">
          <div class="text-subtitle2 q-mb-sm">Casos por rango de edad (denunciante)</div>
          <apexchart
            type="bar"
            height="300"
            :options="barEdadOptions"
            :series="barEdadSeries"
          />
        </q-card>
      </div>

      <div class="col-12 col-md-6">
        <q-card flat bordered class="q-pa-md">
          <div class="text-subtitle2 q-mb-sm">Casos por mes</div>
          <apexchart
            type="area"
            height="300"
            :options="areaOptions"
            :series="areaSeries"
          />
        </q-card>
      </div>
    </div>

    <!-- DETALLE DE CASOS SELECCIONADOS -->
    <q-card
      v-if="detalle.rows.length"
      flat
      bordered
      class="q-mt-md"
    >
      <q-card-section class="row items-center justify-between">
        <div>
          <div class="text-subtitle2">{{ detalle.titulo }}</div>
          <div class="text-caption text-grey-7">
            Total: {{ detalle.rows.length }} registro(s)
          </div>
        </div>
<!--        btn imprimir-->
        <q-btn
          dense
          flat
          icon="print"
          round
          @click="imprimirDetalle()"
        />
        <q-btn
          dense
          flat
          icon="close"
          round
          @click="detalle.rows = []"
        />
      </q-card-section>

      <q-separator />

      <q-card-section>
        <q-table
          :rows="detalle.rows"
          :columns="detalleColumns"
          row-key="id"
          dense
          flat
          bordered
          :loading="detalle.loading"
          no-data-label="Sin casos para mostrar"
          @rowClick="(row, index, event) => {
            // console.log('Row clicked:', row)
            // console.log('Index:', index)
            // console.log('Event:', event)
            this.$router.push('/casos/' + index.id)
          }"
        >
          <template #body-cell-caso_fecha_hecho="props">
            <q-td :props="props">
              {{ formatDate(props.row.caso_fecha_hecho) }}
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import ApexCharts from 'vue3-apexcharts'
import moment from 'moment'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'
export default {
  name: 'IndexPage',
  components: { apexchart: ApexCharts },
  data () {
    return {
      loading: false,
      filters: {
        from: moment().format('YYYY-MM-DD'),
        to: moment().format('YYYY-MM-DD'),
        area: null
      },
      rango: { from: null, to: null },
      totales: {
        casos: 0,
        denunciantes: 0,
        denunciados: 0
      },
      porArea: { labels: [], data: [] },
      porTipo: { labels: [], data: [] },
      porTipologia: { labels: [], data: [] },
      porEdad: { labels: [], data: [] },
      seriesTiempo: { labels: [], data: [] },
      porFragancia: { labels: [], data: [] },
      filterOptions: {
        areas: []
      },
      detalle: {
        titulo: '',
        rows: [],
        loading: false
      },
      detalleColumns: [
        { name: 'id', label: 'ID', field: 'id', sortable: true, align: 'left' },
        { name: 'area', label: 'Área', field: 'area', align: 'left' },
        { name: 'tipo', label: 'Tipo', field: 'tipo', align: 'left' },
        { name: 'caso_tipologia', label: 'Tipología', field: 'caso_tipologia', align: 'left' },
        { name: 'caso_numero', label: 'N° Caso', field: 'caso_numero', align: 'left' },
        { name: 'caso_fecha_hecho', label: 'Fecha hecho', field: 'caso_fecha_hecho', align: 'left' },
        { name: 'caso_lugar_hecho', label: 'Lugar', field: 'caso_lugar_hecho', align: 'left' },
        // { name: 'principal', label: 'Principal', field: 'principal', align: 'left' }
        // fecha_apertura_caso
        { name: 'fecha_apertura_caso', label: 'Fecha apertura', field: 'fecha_apertura_caso', align: 'left' },
        // user
        // zona
        { name: 'zona', label: 'Zona', field: 'zona', align: 'left' },

        { name: 'denunciantes', label: 'Denunciantes', field: (row) => row.denunciantes?.map(d => d.denunciante_nombres).join(', ') || '—', align: 'left' },
        { name: 'denunciados', label: 'Denunciados', field: (row) => row.denunciados?.map(d => d.denunciado_nombres).join(', ') || '—', align: 'left' },
        { name: 'user', label: 'Usuario', field: (row) => row.user?.name || '—', align: 'left' },
        // denunciados
      ]
    }
  },
  computed: {
    cards () {
      return [
        { key: 'casos', label: 'Casos', value: this.totales.casos },
        { key: 'denunciante', label: 'Denunciantes', value: this.totales.denunciantes },
        { key: 'denunciado', label: 'Denunciados', value: this.totales.denunciados },
        // cantidad_denunciantes_del_dia
        // { key: 'denunciantes_dia', label: 'Denunciantes del día', value: this.totales.cantidad_denunciantes_del_dia }
      ]
    },
    rangoTexto () {
      if (!this.rango.from || !this.rango.to) return ''
      return `Del ${this.rango.from} al ${this.rango.to}`
    },
    barEdadOptions () {
      return {
        chart: { toolbar: { show: false } },
        xaxis: { categories: this.porEdad.labels },
        dataLabels: { enabled: false },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '40%'
          }
        },
        grid: { borderColor: '#eee' }
      }
    },
    barEdadSeries () {
      return [
        { name: 'Casos', data: this.porEdad.data }
      ]
    },
    areaOptions () {
      return {
        chart: { toolbar: { show: false } },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: { enabled: false },
        xaxis: { categories: this.seriesTiempo.labels },
        grid: { borderColor: '#eee' },
        legend: { position: 'top' }
      }
    },
    areaSeries () {
      return [
        { name: 'Casos', data: this.seriesTiempo.data }
      ]
    }
  },
  mounted () {
    this.fetchDashboard()
  },
  methods: {
    imprimirDetalle () {
      if (!this.detalle?.rows?.length) {
        this.$q.notify({ type: 'warning', message: 'No hay detalle para imprimir' })
        return
      }

      const doc = new jsPDF('l', 'mm', 'a4')

      const fmt = (v) => v ? moment(v).format('DD/MM/YYYY') : '—'
      const safe = (v) => (v === null || v === undefined || v === '') ? '—' : String(v)

      // ===== Header =====
      const title = 'Reporte de Casos'
      const subtitle = safe(this.detalle.titulo || 'Detalle')
      const rango = `Rango: ${this.filters.from}  →  ${this.filters.to}`
      const area = this.filters.area ? `Área: ${this.filters.area}` : 'Área: Todas'
      const generado = `Generado: ${moment().format('DD/MM/YYYY HH:mm')}`

      doc.setFont('helvetica', 'bold')
      doc.setFontSize(14)
      doc.text(title, 10, 14)

      doc.setFont('helvetica', 'normal')
      doc.setFontSize(10)
      doc.text(subtitle, 10, 20)
      doc.text(rango, 10, 25)
      doc.text(area, 10, 30)
      doc.text(generado, 10, 35)

      const head = [[
        'ID', 'Área', 'Tipo', 'Tipología', 'N° Caso',
        'Fecha hecho', 'Fecha apertura', 'Usuario', 'Zona',
        'Denunciantes', 'Denunciados'
      ]]

      const body = this.detalle.rows.map(r => ([
        safe(r.id),
        safe(r.area),
        safe(r.tipo),
        safe(r.caso_tipologia),
        safe(r.caso_numero),
        fmt(r.caso_fecha_hecho),
        r.fecha_apertura_caso ? moment(r.fecha_apertura_caso).format('DD/MM/YYYY') : '—',
        safe(r.user?.name),
        safe(r.zona),
        (r.denunciantes?.map(d => d.denunciante_nombres).filter(Boolean).join(', ') || '—'),
        (r.denunciados?.map(d => d.denunciado_nombres).filter(Boolean).join(', ') || '—')
      ]))

      // ===== FULL WIDTH =====
      const pageWidth = doc.internal.pageSize.getWidth()
      const marginX = 8
      const usableWidth = pageWidth - (marginX * 2)

      autoTable(doc, {
        head,
        body,
        startY: 40,
        theme: 'grid',

        // clave para ocupar todo el ancho
        margin: { left: marginX, right: marginX },
        tableWidth: usableWidth,     // <--- FULL WIDTH
        styles: {
          font: 'helvetica',
          fontSize: 8,
          cellPadding: 2,
          overflow: 'linebreak',
          valign: 'top',
          cellWidth: 'auto'          // <--- deja que distribuya ancho
        },

        headStyles: {
          fontStyle: 'bold',
          fontSize: 8
        },

        // Si quieres que algunas columnas sean más anchas (sin fijar todo):
        // (no uses números en todas, solo en 1–2 claves)
        columnStyles: {
          9: { cellWidth: 55 },      // Denunciantes (más ancho)
          10:{ cellWidth: 55 }       // Denunciados (más ancho)
        },

        didDrawPage: () => {
          const pageCount = doc.getNumberOfPages()
          const pageHeight = doc.internal.pageSize.getHeight()

          doc.setFontSize(9)
          doc.text(
            `Página ${doc.internal.getCurrentPageInfo().pageNumber} de ${pageCount}`,
            marginX,
            pageHeight - 6
          )
        }
      })

      doc.save(`casos_${this.filters.from}_${this.filters.to}.pdf`)
    },
    formatDate (val) {
      if (!val) return ''
      // viene como 'YYYY-MM-DD' probablemente
      return val
    },
    getDonutOptions (title, labels, groupBy) {
      const self = this
      return {
        labels,
        legend: { position: 'bottom' },
        dataLabels: { enabled: true },
        chart: {
          toolbar: { show: false },
          events: {
            dataPointSelection (event, chartContext, config) {
              const index = config.dataPointIndex
              const label = labels[index]
              if (label) {
                self.onDonutClick(groupBy, label)
              }
            }
          }
        },
        title: { text: '', align: 'left' }
      }
    },
    async onDonutClick (groupBy, label) {
      this.detalle.titulo =
        groupBy === 'tipo'
          ? `Casos - Tipo: ${label}`
          : groupBy === 'tipologia'
            ? `Casos - Tipología: ${label}`
            : `Casos - ${label}`

      this.detalle.loading = true
      this.detalle.rows = []

      try {
        const res = await this.$axios.get('dashboard-casos-detalle', {
          params: {
            from: this.filters.from,
            to: this.filters.to,
            area: this.filters.area,
            group_by: groupBy,
            value: label
          }
        })
        this.detalle.rows = res.data || []
      } catch (e) {
        console.error(e)
        this.$q.notify({
          type: 'negative',
          message: 'Error cargando detalle de casos'
        })
      } finally {
        this.detalle.loading = false
      }
    },
    async fetchDashboard () {
      this.loading = true
      try {
        const res = await this.$axios.get('dashboard-casos', {
          params: this.filters
        })

        const data = res.data || {}

        this.totales       = data.totales || this.totales
        this.porArea       = data.por_area || this.porArea
        this.porTipo       = data.por_tipo || this.porTipo
        this.porTipologia  = data.por_tipologia || this.porTipologia
        this.porEdad       = data.por_edad || this.porEdad
        this.seriesTiempo  = data.series_tiempo || this.seriesTiempo

        // <<< NUEVO
        this.porFragancia  = data.por_fragancia || this.porFragancia

        this.filterOptions.areas = data.filtros?.areas || []
        this.rango = data.rango || this.rango
      } catch (e) {
        console.error(e)
        this.$q.notify({
          type: 'negative',
          message: 'Error cargando dashboard de casos'
        })
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
